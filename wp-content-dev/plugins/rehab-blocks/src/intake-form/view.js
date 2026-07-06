/**
 * Frontend wizard for rehab/intake-form.
 *
 * - step navigation with per-step validation + progress bar
 * - conditional show/hide (data-cond, same semantics as the legacy Forminator
 *   rules: action=show, rule=all|any, operator=is/is_not); hidden fields are
 *   disabled so they neither validate nor submit
 * - repeatable groups (template clone, add/remove)
 * - canvas signature pad → PNG data-URL in a hidden input
 * - JSON submit to /wp-json/rehab/v1/intake (zz-intake-form.php)
 */
( () => {
	const init = ( form ) => {
		const steps = [ ...form.querySelectorAll( '.rehab-intake__step' ) ];
		const progressFill = form.querySelector( '.rehab-intake__progress-fill' );
		const progress = form.querySelector( '.rehab-intake__progress' );
		const progressPct = form.querySelector( '[data-progress-pct]' );
		const status = form.querySelector( '.rehab-intake__status' );
		let current = 0;

		const setStatus = ( msg, kind ) => {
			status.textContent = msg || '';
			status.dataset.state = kind || '';
		};

		// ---- conditions -------------------------------------------------
		const condEls = [ ...form.querySelectorAll( '[data-cond]' ) ];
		const fieldValue = ( name ) => {
			const el = form.querySelector( `[name="${ name }"]` );
			if ( ! el ) return '';
			if ( el.type === 'radio' ) {
				// unchecked radio groups read as empty — never the first option's value attr
				const checked = form.querySelector( `input[type="radio"][name="${ name }"]:checked` );
				return checked ? checked.value : '';
			}
			return el.value;
		};
		const evalCond = ( cond ) => {
			const results = cond.rules.map( ( r ) => {
				const v = fieldValue( r.field );
				return r.operator === 'is_not' ? v !== r.value : v === r.value;
			} );
			const met = cond.rule === 'any' ? results.some( Boolean ) : results.every( Boolean );
			return cond.action === 'hide' ? ! met : met;
		};
		const applyConditions = () => {
			condEls.forEach( ( el ) => {
				const show = evalCond( JSON.parse( el.dataset.cond ) );
				el.hidden = ! show;
				el.querySelectorAll( 'input, select, textarea, button' ).forEach( ( c ) => {
					c.disabled = ! show;
				} );
			} );
		};
		form.addEventListener( 'change', applyConditions );
		form.addEventListener( 'input', ( e ) => {
			if ( e.target.matches( 'input[type="radio"]' ) ) applyConditions();
		} );

		// ---- validation -------------------------------------------------
		const fieldWrap = ( el ) => el.closest( '.rehab-intake__field, .rehab-intake__signature, .rehab-intake__date' ) || el.parentElement;
		const setError = ( el, msg ) => {
			const wrap = el.closest( '.rehab-intake__field' ) || fieldWrap( el );
			const slot = wrap && wrap.querySelector( '.rehab-intake__error' );
			if ( slot ) slot.textContent = msg || '';
			wrap && wrap.classList.toggle( 'has-error', !! msg );
		};
		const validateStep = ( idx ) => {
			let firstBad = null;
			const seenRadios = new Set();
			steps[ idx ].querySelectorAll( '[data-req="1"]' ).forEach( ( el ) => {
				if ( el.disabled ) return;
				let ok = true;
				if ( el.type === 'radio' ) {
					if ( seenRadios.has( el.name ) ) return;
					seenRadios.add( el.name );
					ok = !! form.querySelector( `input[name="${ el.name }"]:checked` );
				} else if ( el.type === 'email' ) {
					ok = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test( el.value.trim() );
				} else {
					ok = el.value.trim() !== '';
				}
				setError( el, ok ? '' : 'This field is required.' );
				if ( ! ok && ! firstBad ) firstBad = el;
			} );
			// optional emails still need a valid format when filled
			steps[ idx ].querySelectorAll( 'input[type="email"]:not([data-req])' ).forEach( ( el ) => {
				if ( el.disabled || ! el.value.trim() ) return;
				const ok = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test( el.value.trim() );
				setError( el, ok ? '' : 'Please enter a valid email address.' );
				if ( ! ok && ! firstBad ) firstBad = el;
			} );
			if ( firstBad ) {
				fieldWrap( firstBad ).scrollIntoView( { behavior: 'smooth', block: 'center' } );
			}
			return ! firstBad;
		};

		// ---- navigation -------------------------------------------------
		const goTo = ( idx ) => {
			steps[ current ].hidden = true;
			current = idx;
			steps[ current ].hidden = false;
			const pct = ( ( current + 1 ) / steps.length ) * 100;
			progressFill.style.width = pct + '%';
			progress.setAttribute( 'aria-valuenow', String( current + 1 ) );
			if ( progressPct ) progressPct.textContent = Math.round( pct ) + '%';
			applyConditions();
			form.scrollIntoView( { behavior: 'smooth', block: 'start' } );
		};
		form.addEventListener( 'click', ( e ) => {
			if ( e.target.closest( '.rehab-intake__next' ) ) {
				if ( validateStep( current ) ) goTo( current + 1 );
			} else if ( e.target.closest( '.rehab-intake__prev' ) ) {
				goTo( current - 1 );
			}
		} );

		// ---- repeatable groups -------------------------------------------
		form.querySelectorAll( '[data-repeat-group]' ).forEach( ( group ) => {
			const items = group.querySelector( '.rehab-intake__group-items' );
			const tpl = group.querySelector( 'template' );
			const sync = () => {
				const all = items.querySelectorAll( '.rehab-intake__group-item' );
				all.forEach( ( item ) => {
					item.querySelector( '.rehab-intake__group-remove' ).hidden = all.length <= 1;
				} );
			};
			group.querySelector( '.rehab-intake__group-add' ).addEventListener( 'click', () => {
				items.appendChild( tpl.content.cloneNode( true ) );
				sync();
			} );
			items.addEventListener( 'click', ( e ) => {
				const btn = e.target.closest( '.rehab-intake__group-remove' );
				if ( btn ) {
					btn.closest( '.rehab-intake__group-item' ).remove();
					sync();
				}
			} );
		} );

		// ---- signature pad ------------------------------------------------
		form.querySelectorAll( '[data-signature]' ).forEach( ( wrap ) => {
			const canvas = wrap.querySelector( 'canvas' );
			const hidden = wrap.querySelector( 'input[type="hidden"]' );
			const ctx = canvas.getContext( '2d' );
			const thickness = parseInt( wrap.dataset.thickness, 10 ) || 2;
			let drawing = false;
			let dirty = false;

			const size = () => {
				const cssW = canvas.clientWidth || canvas.parentElement.clientWidth;
				const cssH = parseInt( canvas.getAttribute( 'height' ), 10 ) || 180;
				const dpr = window.devicePixelRatio || 1;
				const prev = dirty ? canvas.toDataURL() : null;
				canvas.width = cssW * dpr;
				canvas.height = cssH * dpr;
				canvas.style.height = cssH + 'px';
				ctx.scale( dpr, dpr );
				ctx.lineWidth = thickness;
				ctx.lineCap = 'round';
				ctx.lineJoin = 'round';
				ctx.strokeStyle = '#1f2a24';
				if ( prev ) {
					const img = new Image();
					img.onload = () => ctx.drawImage( img, 0, 0, cssW, cssH );
					img.src = prev;
				}
			};
			const pos = ( e ) => {
				const r = canvas.getBoundingClientRect();
				return [ e.clientX - r.left, e.clientY - r.top ];
			};
			canvas.addEventListener( 'pointerdown', ( e ) => {
				drawing = true;
				canvas.setPointerCapture( e.pointerId );
				ctx.beginPath();
				ctx.moveTo( ...pos( e ) );
				e.preventDefault();
			} );
			canvas.addEventListener( 'pointermove', ( e ) => {
				if ( ! drawing ) return;
				ctx.lineTo( ...pos( e ) );
				ctx.stroke();
				dirty = true;
				e.preventDefault();
			} );
			const stop = () => {
				if ( ! drawing ) return;
				drawing = false;
				if ( dirty ) {
					hidden.value = canvas.toDataURL( 'image/png' );
					setError( hidden, '' );
				}
			};
			canvas.addEventListener( 'pointerup', stop );
			canvas.addEventListener( 'pointercancel', stop );
			canvas.addEventListener( 'pointerleave', stop );
			wrap.querySelector( '.rehab-intake__sig-clear' ).addEventListener( 'click', () => {
				ctx.clearRect( 0, 0, canvas.width, canvas.height );
				hidden.value = '';
				dirty = false;
			} );
			new ResizeObserver( size ).observe( canvas.parentElement );
			size();
		} );

		// ---- date defaults ------------------------------------------------
		form.querySelectorAll( '[data-date-field][data-default="today"]' ).forEach( ( wrap ) => {
			const now = new Date();
			const set = ( part, val ) => {
				const sel = wrap.querySelector( `select[data-part="${ part }"]` );
				if ( sel ) sel.value = String( val );
			};
			set( 'day', now.getDate() );
			set( 'month', now.getMonth() + 1 );
			set( 'year', now.getFullYear() );
		} );

		// ---- submit --------------------------------------------------------
		form.addEventListener( 'submit', async ( e ) => {
			e.preventDefault();
			if ( ! validateStep( current ) ) {
				setStatus( form.dataset.invalid, 'error' );
				return;
			}
			setStatus( 'Sending…', 'pending' );
			const submitBtn = form.querySelector( '.rehab-intake__submit' );
			submitBtn.disabled = true;

			const data = {};
			new FormData( form ).forEach( ( value, key ) => {
				if ( key.endsWith( '[]' ) ) {
					const k = key.slice( 0, -2 );
					( data[ k ] = data[ k ] || [] ).push( value );
				} else {
					data[ key ] = value;
				}
			} );
			data.source = window.location.href;

			const root = ( typeof wpApiSettings !== 'undefined' && wpApiSettings.root )
				? wpApiSettings.root
				: ( window.location.origin + '/wp-json/' );

			try {
				const res = await fetch( root + 'rehab/v1/intake', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
					body: JSON.stringify( data ),
				} );
				const body = await res.json().catch( () => ( {} ) );
				if ( res.ok && body.ok ) {
					const done = document.createElement( 'div' );
					done.className = 'rehab-intake__thankyou';
					done.setAttribute( 'role', 'status' );
					done.textContent = form.dataset.thankyou;
					form.replaceWith( done );
					done.scrollIntoView( { behavior: 'smooth', block: 'center' } );
				} else {
					setStatus( body.error || body.message || 'Something went wrong. Please try again or contact us directly.', 'error' );
					submitBtn.disabled = false;
				}
			} catch ( err ) {
				setStatus( 'Network error. Please try again or contact us directly.', 'error' );
				submitBtn.disabled = false;
			}
		} );

		applyConditions();
	};

	const boot = () => document.querySelectorAll( 'form[data-rehab-intake]' ).forEach( init );
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
