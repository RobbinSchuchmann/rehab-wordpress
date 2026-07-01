/* Mobile contact chooser (bottom sheet).
 *
 * Opened from the sticky "Talk with admissions" CTA. Presents Call / form /
 * WhatsApp so visitors choose how to reach us. The "Email / fill in a form"
 * option prefers an on-page assessment form (so treatment pages don't bounce
 * the visitor to a separate contact page) and only follows its href as a
 * fallback when the current page has no such form. */
( () => {
	const sheet = document.getElementById( 'rehab-contact-sheet' );
	if ( ! sheet ) return;

	const panel = sheet.querySelector( '.rehab-contact-sheet__panel' );
	const openers = document.querySelectorAll( '[data-rehab-sheet-open]' );
	const closers = sheet.querySelectorAll( '[data-rehab-sheet-close]' );
	let lastFocus = null;

	const focusable = () =>
		Array.from( panel.querySelectorAll( 'a[href], button:not([disabled])' ) )
			.filter( ( el ) => el.offsetParent !== null );

	const open = ( trigger ) => {
		lastFocus = trigger || document.activeElement;
		sheet.hidden = false;
		// Force a reflow before adding the class so the transform animates in.
		void panel.offsetHeight;
		sheet.classList.add( 'is-open' );
		document.body.classList.add( 'rehab-sheet-open' );
		openers.forEach( ( o ) => o.setAttribute( 'aria-expanded', 'true' ) );
		const f = focusable();
		if ( f.length ) f[ 0 ].focus();
	};

	const close = () => {
		if ( sheet.hidden ) return;
		sheet.classList.remove( 'is-open' );
		document.body.classList.remove( 'rehab-sheet-open' );
		openers.forEach( ( o ) => o.setAttribute( 'aria-expanded', 'false' ) );

		const finish = () => {
			panel.removeEventListener( 'transitionend', finish );
			if ( ! sheet.classList.contains( 'is-open' ) ) sheet.hidden = true;
		};
		panel.addEventListener( 'transitionend', finish );
		// Fallback in case transitionend doesn't fire (reduced motion, etc.).
		window.setTimeout( finish, 400 );

		if ( lastFocus && typeof lastFocus.focus === 'function' ) lastFocus.focus();
	};

	openers.forEach( ( o ) => o.addEventListener( 'click', () => open( o ) ) );
	closers.forEach( ( c ) => c.addEventListener( 'click', close ) );

	// "Email / fill in a form": jump to an on-page form when one exists.
	const formLink = sheet.querySelector( '[data-rehab-sheet-form]' );
	if ( formLink ) {
		formLink.addEventListener( 'click', ( e ) => {
			const target = document.querySelector( '#assessment, [data-rehab-contact-form]' );
			if ( ! target ) return; // no on-page form — follow the href to /contact-us/
			e.preventDefault();
			close();
			target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			const field = target.querySelector( 'input, select, textarea' );
			if ( field ) window.setTimeout( () => field.focus( { preventScroll: true } ), 500 );
		} );
	}

	document.addEventListener( 'keydown', ( e ) => {
		if ( sheet.hidden ) return;
		if ( e.key === 'Escape' ) {
			e.preventDefault();
			close();
			return;
		}
		if ( e.key === 'Tab' ) {
			const f = focusable();
			if ( ! f.length ) return;
			const first = f[ 0 ];
			const last = f[ f.length - 1 ];
			if ( e.shiftKey && document.activeElement === first ) {
				e.preventDefault();
				last.focus();
			} else if ( ! e.shiftKey && document.activeElement === last ) {
				e.preventDefault();
				first.focus();
			}
		}
	} );
} )();

/* Same-page CTAs that point at an on-page contact form (e.g. the treatment
 * hero's "Talk with admissions" → #assessment) should focus the form's first
 * field rather than just jumping to a form that may already be on screen —
 * on desktop the form sits beside the CTA, so a plain anchor jump looks like
 * nothing happened. Scroll only when the form isn't already in view.
 *
 * Scoped to links whose target IS or CONTAINS a [data-rehab-contact-form], so
 * other same-page anchors (hub/breadcrumb #cat-* sections, etc.) keep their
 * native behaviour. */
( () => {
	document.addEventListener( 'click', ( e ) => {
		const link = e.target.closest( 'a[href*="#"]' );
		if ( ! link ) return;

		let url;
		try {
			url = new URL( link.href, window.location.href );
		} catch ( err ) {
			return;
		}
		// Same page + a real fragment only.
		if ( url.pathname !== window.location.pathname || url.hash.length < 2 ) return;

		let target;
		try {
			target = document.querySelector( url.hash );
		} catch ( err ) {
			return; // not a valid selector
		}
		if ( ! target ) return;

		const form = target.matches( '[data-rehab-contact-form]' )
			? target
			: target.querySelector( '[data-rehab-contact-form]' );
		if ( ! form ) return; // not a contact-form anchor — leave native behaviour

		e.preventDefault();

		const rect = target.getBoundingClientRect();
		const inView = rect.top >= 0 && rect.bottom <= window.innerHeight;
		if ( ! inView ) target.scrollIntoView( { behavior: 'smooth', block: 'start' } );

		// Skip the honeypot (tabindex=-1) and land on the first real field.
		const field = form.querySelector(
			'input:not([type="hidden"]):not([tabindex="-1"]), select, textarea'
		);
		if ( field ) {
			window.setTimeout( () => field.focus( { preventScroll: true } ), inView ? 0 : 500 );
		}
	} );
} )();
