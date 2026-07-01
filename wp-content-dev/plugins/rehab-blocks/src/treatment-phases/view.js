/* Frontend tab switcher for rehab/treatment-phases. */
( () => {
	const init = ( root ) => {
		const tabs = Array.from( root.querySelectorAll( '.rehab-treatment-phases__tab' ) );
		const panels = Array.from( root.querySelectorAll( '.rehab-treatment-phases__panel' ) );
		if ( tabs.length === 0 || panels.length === 0 ) return;

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
