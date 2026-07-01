/* Frontend behaviour for rehab/treatment-phases.
 *
 * Desktop (>720px): a tab switcher — pick a phase, its panel shows below.
 * Mobile (<=720px): CSS reveals every phase in full as a simple vertical list
 *   (see style.scss). This script injects a plain heading into each panel (from
 *   its data attributes) so each phase is labelled in that list; the heading is
 *   hidden on desktop via CSS. The saved markup is untouched (canonicalize-safe).
 */
( () => {
	const init = ( root ) => {
		const tabs = Array.from( root.querySelectorAll( '.rehab-treatment-phases__tab' ) );
		const panels = Array.from( root.querySelectorAll( '.rehab-treatment-phases__panel' ) );
		if ( panels.length === 0 ) return;

		// Label each panel for the mobile vertical list (hidden on desktop via CSS).
		panels.forEach( ( panel ) => {
			if ( panel.querySelector( ':scope > .rehab-treatment-phases__phase-heading' ) ) return;
			const num = panel.getAttribute( 'data-phase' ) || '';
			const label = panel.getAttribute( 'data-label' ) || '';
			if ( ! num && ! label ) return;
			const heading = document.createElement( 'div' );
			heading.className = 'rehab-treatment-phases__phase-heading';
			if ( num ) {
				const n = document.createElement( 'span' );
				n.className = 'num';
				n.textContent = num;
				heading.appendChild( n );
			}
			if ( label ) {
				const l = document.createElement( 'span' );
				l.className = 'label';
				l.textContent = label;
				heading.appendChild( l );
			}
			panel.insertBefore( heading, panel.firstChild );
		} );

		if ( tabs.length === 0 ) return;

		// Desktop tab switching.
		const activate = ( idx ) => {
			tabs.forEach( ( t, i ) => {
				const active = i === idx;
				t.classList.toggle( 'is-active', active );
				t.setAttribute( 'aria-selected', active ? 'true' : 'false' );
			} );
			panels.forEach( ( p, i ) => { p.hidden = i !== idx; } );
		};

		tabs.forEach( ( t, i ) => {
			t.addEventListener( 'click', () => activate( i ) );
			t.addEventListener( 'keydown', ( e ) => {
				if ( e.key === 'ArrowRight' ) {
					e.preventDefault();
					const next = ( i + 1 ) % tabs.length;
					tabs[ next ].focus();
					activate( next );
				} else if ( e.key === 'ArrowLeft' ) {
					e.preventDefault();
					const prev = ( i - 1 + tabs.length ) % tabs.length;
					tabs[ prev ].focus();
					activate( prev );
				}
			} );
		} );
	};

	const boot = () => document.querySelectorAll( '.rehab-treatment-phases' ).forEach( init );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
