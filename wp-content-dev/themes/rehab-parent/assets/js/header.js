/**
 * Header behavior: menu toggle + sticky-shrink on scroll.
 */
( () => {
	const init = () => {
		const navbar = document.querySelector( '[data-rehab-navbar]' );
		const toggle = document.querySelector( '[data-rehab-menu-toggle]' );
		const menu = document.querySelector( '[data-rehab-mega-menu]' );

		if ( ! navbar || ! toggle || ! menu ) return;

		const setOpen = ( open ) => {
			document.body.classList.toggle( 'rehab-menu-open', open );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			menu.setAttribute( 'aria-hidden', open ? 'false' : 'true' );
			document.body.style.overflow = open ? 'hidden' : '';
		};

		toggle.addEventListener( 'click', () => {
			const isOpen = document.body.classList.contains( 'rehab-menu-open' );
			setOpen( ! isOpen );
		} );

		document.addEventListener( 'keydown', ( e ) => {
			if ( e.key === 'Escape' && document.body.classList.contains( 'rehab-menu-open' ) ) {
				setOpen( false );
				toggle.focus();
			}
		} );

		// Sticky shrink + auto-hide on scroll-down behavior
		let lastScroll = 0;
		const onScroll = () => {
			const y = window.scrollY;
			navbar.classList.toggle( 'is-stuck', y > 80 );
			// Auto-hide when scrolling down past 200px, show on scroll-up
			if ( y > 200 ) {
				const scrollingDown = y > lastScroll + 6;
				const scrollingUp = y < lastScroll - 6;
				if ( scrollingDown ) navbar.classList.add( 'is-hidden' );
				else if ( scrollingUp ) navbar.classList.remove( 'is-hidden' );
			} else {
				navbar.classList.remove( 'is-hidden' );
			}
			lastScroll = y;
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	};

	const boot = () => {
		init();
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
