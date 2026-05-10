/**
 * Frontend tabs behavior. Pure DOM, no framework.
 *
 * Markup contract:
 *   <section data-rehab-tabs>
 *     <div class="rehab-tabs__inner">
 *       <div class="rehab-tab" data-label="…">…panel content…</div>
 *       <div class="rehab-tab" data-label="…">…panel content…</div>
 *     </div>
 *   </section>
 *
 * On boot, generates a nav from tabs' data-label, hides all panels but first,
 * wires click handlers to switch.
 */
( () => {
	const init = ( root ) => {
		const inner = root.querySelector( '.rehab-tabs__inner' );
		if ( ! inner ) return;
		const panels = Array.from( inner.querySelectorAll( ':scope > .rehab-tab' ) );
		if ( panels.length === 0 ) return;

		const nav = document.createElement( 'nav' );
		nav.className = 'rehab-tabs__nav';
		nav.setAttribute( 'role', 'tablist' );

		panels.forEach( ( panel, idx ) => {
			const label = panel.getAttribute( 'data-label' ) || `Tab ${ idx + 1 }`;
			const id = `${ root.id || 'rehab-tabs' }-${ idx }`;
			panel.id = panel.id || `${ id }-panel`;
			panel.setAttribute( 'role', 'tabpanel' );
			panel.setAttribute( 'aria-labelledby', `${ panel.id }-tab` );
			if ( idx !== 0 ) panel.hidden = true;

			const btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'rehab-tabs__tab' + ( idx === 0 ? ' is-active' : '' );
			btn.id = `${ panel.id }-tab`;
			btn.textContent = label;
			btn.setAttribute( 'role', 'tab' );
			btn.setAttribute( 'aria-controls', panel.id );
			btn.setAttribute( 'aria-selected', idx === 0 ? 'true' : 'false' );
			btn.addEventListener( 'click', () => activate( idx ) );
			nav.appendChild( btn );
		} );

		const buttons = Array.from( nav.children );

		const activate = ( target ) => {
			panels.forEach( ( p, i ) => { p.hidden = i !== target; } );
			buttons.forEach( ( b, i ) => {
				const active = i === target;
				b.classList.toggle( 'is-active', active );
				b.setAttribute( 'aria-selected', active ? 'true' : 'false' );
			} );
		};

		inner.parentNode.insertBefore( nav, inner );
	};

	const boot = () =>
		document.querySelectorAll( '[data-rehab-tabs]' ).forEach( init );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
