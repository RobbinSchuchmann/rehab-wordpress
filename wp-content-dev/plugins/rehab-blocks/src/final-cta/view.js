/**
 * Frontend contact-form handler for rehab/final-cta.
 *
 * Submits via fetch to /wp-json/rehab/v1/contact (registered by
 * mu-plugins/zz-contact-form.php), displays inline status, prevents the
 * usual page-reload-on-submit so the user stays on the page.
 *
 * The endpoint is `public` (permission_callback __return_true) but
 * protected by a honeypot + per-IP transient rate-limit on the server.
 */
( () => {
	const init = ( form ) => {
		const status = form.querySelector( '.rehab-final-cta__form-status' );
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
					headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
					body: JSON.stringify( data ),
				} );
				const body = await res.json().catch( () => ( {} ) );
				if ( res.ok && body.ok ) {
					setStatus( 'Thank you — our team will be in touch within an hour during business hours.', 'success' );
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

	const boot = () => document.querySelectorAll( 'form[data-rehab-contact-form]' ).forEach( init );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
