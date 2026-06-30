/* Frontend behaviour for rehab/treatment-phases.
 *
 * Desktop (>720px): a tab switcher — pick a phase box, its panel shows below.
 * Mobile (<=720px): an accordion — each phase's content expands directly under
 * its own header, so reading follows the natural downward scroll instead of
 * forcing the reader back up to the row of boxes.
 *
 * The saved markup is a plain tabs widget (a `__nav` of buttons + a `__panels`
 * list). To avoid changing that markup (and stay canonicalize-safe), the
 * accordion headers are injected here at runtime by cloning each tab, and a
 * matchMedia listener swaps between the two modes on resize. */
( () => {
	const MOBILE = '(max-width: 720px)';
	let uid = 0;

	const init = ( root ) => {
		const tabs = Array.from( root.querySelectorAll( '.rehab-treatment-phases__tab' ) );
		const panels = Array.from( root.querySelectorAll( '.rehab-treatment-phases__panel' ) );
		if ( tabs.length === 0 || panels.length === 0 ) return;

		const group = `rtp-${ ++uid }`;

		// Inject one accordion header per panel, mirroring its tab. Hidden on
		// desktop via CSS; it's the control surface for the mobile accordion.
		const headers = panels.map( ( panel, i ) => {
			if ( ! panel.id ) panel.id = `${ group }-panel-${ i }`;
			let header = panel.querySelector( ':scope > .rehab-treatment-phases__acc' );
			if ( ! header ) {
				header = document.createElement( 'button' );
				header.type = 'button';
				header.className = 'rehab-treatment-phases__acc';
				header.setAttribute( 'aria-expanded', 'false' );
				header.setAttribute( 'aria-controls', panel.id );
				header.innerHTML =
					( tabs[ i ] ? tabs[ i ].innerHTML : '' ) +
					'<span class="rehab-treatment-phases__acc-icon" aria-hidden="true"></span>';
				panel.insertBefore( header, panel.firstChild );
			}
			return header;
		} );

		// --- tabs mode (desktop) ---
		const activateTab = ( idx ) => {
			tabs.forEach( ( t, i ) => {
				const active = i === idx;
				t.classList.toggle( 'is-active', active );
				t.setAttribute( 'aria-selected', active ? 'true' : 'false' );
			} );
			panels.forEach( ( p, i ) => { p.hidden = i !== idx; } );
		};

		// --- accordion mode (mobile) ---
		const setAccordion = ( idx, open ) => {
			headers[ idx ].setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			panels[ idx ].classList.toggle( 'is-open', open );
		};

		// Bind both interaction models once; behaviour follows the active mode.
		tabs.forEach( ( t, i ) => {
			t.addEventListener( 'click', () => activateTab( i ) );
			t.addEventListener( 'keydown', ( e ) => {
				if ( e.key === 'ArrowRight' ) {
					e.preventDefault();
					const next = ( i + 1 ) % tabs.length;
					tabs[ next ].focus();
					activateTab( next );
				} else if ( e.key === 'ArrowLeft' ) {
					e.preventDefault();
					const prev = ( i - 1 + tabs.length ) % tabs.length;
					tabs[ prev ].focus();
					activateTab( prev );
				}
			} );
		} );
		headers.forEach( ( h, i ) => {
			h.addEventListener( 'click', () => {
				setAccordion( i, h.getAttribute( 'aria-expanded' ) !== 'true' );
			} );
		} );

		let mode = null;
		const applyMode = ( next ) => {
			if ( next === mode ) return;
			mode = next;
			if ( next === 'accordion' ) {
				// Reveal every panel (so its header shows); keep the first open,
				// the rest collapsed. The CSS hides the body until `.is-open`.
				panels.forEach( ( p, i ) => {
					p.hidden = false;
					setAccordion( i, i === 0 );
				} );
			} else {
				// Restore single-panel tab view; clear all accordion state.
				const active = Math.max(
					0,
					tabs.findIndex( ( t ) => t.classList.contains( 'is-active' ) )
				);
				panels.forEach( ( p, i ) => {
					p.classList.remove( 'is-open' );
					headers[ i ].setAttribute( 'aria-expanded', 'false' );
					p.hidden = i !== active;
				} );
			}
		};

		const mq = window.matchMedia( MOBILE );
		const sync = () => applyMode( mq.matches ? 'accordion' : 'tabs' );
		if ( mq.addEventListener ) {
			mq.addEventListener( 'change', sync );
		} else if ( mq.addListener ) {
			mq.addListener( sync );
		}
		sync();
	};

	const boot = () => document.querySelectorAll( '.rehab-treatment-phases' ).forEach( init );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
