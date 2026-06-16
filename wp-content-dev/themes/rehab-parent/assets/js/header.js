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

		// Disclosure parents in the inline desktop nav. The walker renders these
		// as `<a role="button" aria-haspopup="true">` with no href (see
		// functions.php). Hover reveals the submenu via CSS, but touch devices
		// have no hover — so wire a click/keyboard toggle here. The mega menu
		// (burger) shows its submenus permanently, so it needs no toggle.
		const inlineNav = document.querySelector( '.rehab-navbar__menu' );
		if ( inlineNav ) {
			const parents = inlineNav.querySelectorAll( '[aria-haspopup="true"]' );

			const closeParent = ( el ) => {
				el.parentElement.classList.remove( 'is-open' );
				el.setAttribute( 'aria-expanded', 'false' );
			};

			parents.forEach( ( el ) => {
				const li = el.parentElement;

				const toggleParent = ( e ) => {
					e.preventDefault();
					const willOpen = ! li.classList.contains( 'is-open' );
					// Single open dropdown at a time.
					parents.forEach( ( other ) => {
						if ( other !== el ) closeParent( other );
					} );
					li.classList.toggle( 'is-open', willOpen );
					el.setAttribute( 'aria-expanded', willOpen ? 'true' : 'false' );
				};

				el.addEventListener( 'click', toggleParent );
				el.addEventListener( 'keydown', ( e ) => {
					if ( e.key === 'Enter' || e.key === ' ' ) toggleParent( e );
				} );
			} );

			// Close on outside click and on Escape.
			document.addEventListener( 'click', ( e ) => {
				if ( ! inlineNav.contains( e.target ) ) parents.forEach( closeParent );
			} );
			inlineNav.addEventListener( 'keydown', ( e ) => {
				if ( e.key === 'Escape' ) parents.forEach( closeParent );
			} );
		}

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
