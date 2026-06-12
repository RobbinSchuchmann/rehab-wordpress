/**
 * Frontend form handler for rehab/team-profile.
 *
 * Posts the "[Name] is part of our team" enquiry form to rehab/v1/contact,
 * shows inline status, prevents page reload. Scoped to .rehab-team-profile.
 */
( () => {
	const init = ( form ) => {
		if ( form.dataset.rehabBound ) return;
		form.dataset.rehabBound = '1';
		const status = form.querySelector( '[data-rehab-form-status]' );
		const submit = form.querySelector( 'button[type="submit"]' );
		const setStatus = ( msg, kind ) => {
			if ( ! status ) return;
			status.textContent = msg || '';
			status.dataset.state = kind || '';
		};
		const root = ( typeof wpApiSettings !== 'undefined' && wpApiSettings.root )
			? wpApiSettings.root
			: ( window.location.origin + '/wp-json/' );
		const endpoint = root + 'rehab/v1/contact';

		form.addEventListener( 'submit', async ( e ) => {
			e.preventDefault();
			setStatus( 'Sending…', 'pending' );
			submit && ( submit.disabled = true );

			const data = Object.fromEntries( new FormData( form ).entries() );
			data.source = window.location.href;

			try {
				const res = await fetch( endpoint, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
					body: JSON.stringify( data ),
				} );
				const body = await res.json().catch( () => ( {} ) );
				if ( res.ok && body.ok ) {
					setStatus( 'Thank you — our team will be in touch within the hour during business hours.', 'success' );
					form.reset();
				} else {
					setStatus( body.error || body.message || 'Something went wrong. Please try again or call us directly.', 'error' );
					submit && ( submit.disabled = false );
				}
			} catch ( err ) {
				setStatus( 'Network error. Please try again or call us directly.', 'error' );
				submit && ( submit.disabled = false );
			}
		} );
	};

	const boot = () => document.querySelectorAll( '.rehab-team-profile form[data-rehab-contact-form]' ).forEach( init );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
