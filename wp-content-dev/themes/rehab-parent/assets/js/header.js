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

		// Disclosure parents have no real href — the walker renders them as
		// `<a role="button" aria-haspopup="true">` (see functions.php). They toggle
		// their submenu open/closed so they work on touch, where there is no hover.
		// Wired for both the inline navbar (hover still opens it via CSS) and the
		// mega-menu accordion, at every depth.
		const closeDisclosure = ( el ) => {
			el.parentElement.classList.remove( 'is-open' );
			el.setAttribute( 'aria-expanded', 'false' );
		};

		const wireDisclosure = ( scope, exclusive ) => {
			const buttons = scope.querySelectorAll( '[aria-haspopup="true"]' );

			buttons.forEach( ( el ) => {
				const li = el.parentElement;

				const toggle = ( e ) => {
					e.preventDefault();
					const willOpen = ! li.classList.contains( 'is-open' );
					if ( exclusive ) {
						// Close any sibling disclosure at the same level.
						Array.from( li.parentElement.children ).forEach( ( sib ) => {
							if ( sib === li ) return;
							const sibBtn = sib.querySelector( ':scope > [aria-haspopup="true"]' );
							if ( sibBtn ) closeDisclosure( sibBtn );
						} );
					}
					li.classList.toggle( 'is-open', willOpen );
					el.setAttribute( 'aria-expanded', willOpen ? 'true' : 'false' );
				};

				el.addEventListener( 'click', toggle );
				el.addEventListener( 'keydown', ( e ) => {
					if ( e.key === 'Enter' || e.key === ' ' ) toggle( e );
				} );
			} );

			return buttons;
		};

		// Inline navbar: one dropdown open at a time; close on outside click / Esc.
		const inlineNav = document.querySelector( '.rehab-navbar__menu' );
		if ( inlineNav ) {
			const buttons = wireDisclosure( inlineNav, true );
			const closeOutside = ( e ) => {
				if ( ! inlineNav.contains( e.target ) ) buttons.forEach( closeDisclosure );
			};
			document.addEventListener( 'click', closeOutside );
			document.addEventListener( 'auxclick', closeOutside );
			// Middle-click opens a link in a background tab without navigating,
			// but Chromium focuses the link on middle mousedown — :focus-within
			// (header.css) then pins the dropdown open until the next left-click.
			// Drop the lingering focus once the aux click completes.
			inlineNav.addEventListener( 'auxclick', ( e ) => {
				const link = e.target.closest( 'a' );
				if ( link ) link.blur();
			} );
			inlineNav.addEventListener( 'keydown', ( e ) => {
				if ( e.key === 'Escape' ) buttons.forEach( closeDisclosure );
			} );
		}

		// Mega-menu (burger): independent accordion sections, expandable per depth.
		const megaList = document.querySelector( '.rehab-mega-menu__list' );
		if ( megaList ) {
			wireDisclosure( megaList, false );
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
