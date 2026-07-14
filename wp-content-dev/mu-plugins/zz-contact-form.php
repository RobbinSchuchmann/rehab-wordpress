<?php
/**
 * Lightweight contact-form handler for rehab/final-cta.
 *
 * Registers:
 *  - `rehab_submission` CPT (private, admin-only) — every form post creates one
 *    so the customer's lead is preserved even if wp_mail() fails to deliver.
 *  - REST endpoint POST /wp-json/rehab/v1/contact — accepts the final-CTA form
 *    submission, validates, honeypot-checks, stores, emails the admin.
 *
 * Settings (theme mods, with sensible defaults):
 *   rehab_form_recipient  — admin email to notify (default: admin_email option)
 *   rehab_form_subject    — email subject template (default: "New enquiry — %name%")
 *
 * Per memory: form storage is intentionally a CPT (not a custom DB table) so it
 * shows up in standard WP admin, can be moderated, and survives backups.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the rehab_submission CPT (private — not in nav, not searchable).
 * Visible in admin so the team can audit leads. Permanent fallback for the
 * (rare) case where wp_mail() drops the message.
 */
add_action( 'init', function () {
	register_post_type( 'rehab_submission', [
		'labels' => [
			'name'          => __( 'Form submissions', 'rehab-parent' ),
			'singular_name' => __( 'Form submission', 'rehab-parent' ),
			'menu_name'     => __( 'Form submissions', 'rehab-parent' ),
			'all_items'     => __( 'All submissions', 'rehab-parent' ),
		],
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'menu_icon'           => 'dashicons-email-alt',
		'menu_position'       => 25,
		'capability_type'     => 'post',
		'capabilities'        => [
			'create_posts' => 'do_not_allow', // only via the REST endpoint
		],
		'map_meta_cap'        => true,
		'supports'            => [ 'title', 'editor', 'custom-fields' ],
	] );
} );

/**
 * Register POST /wp-json/rehab/v1/contact.
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'rehab/v1', '/contact', [
		'methods'             => 'POST',
		'callback'            => 'rehab_contact_form_submit',
		'permission_callback' => '__return_true', // public; protected by honeypot + rate limit
		'args'                => [
			'name'    => [ 'type' => 'string', 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
			'email'   => [ 'type' => 'string', 'required' => true,  'sanitize_callback' => 'sanitize_email' ],
			'phone'   => [ 'type' => 'string', 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
			'country' => [ 'type' => 'string', 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ],
			// Treatment-page assessment forms: "Who is this enquiry for?" select.
			'enquiry_for' => [ 'type' => 'string', 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ],
			'message' => [ 'type' => 'string', 'required' => false, 'sanitize_callback' => 'sanitize_textarea_field' ],
			'source'  => [ 'type' => 'string', 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ],
			// Honeypot: real users won't fill this; bots will.
			'_hp'     => [ 'type' => 'string', 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ],
		],
	] );
} );

function rehab_contact_form_submit( WP_REST_Request $req ) {
	// Honeypot trap.
	if ( ! empty( $req->get_param( '_hp' ) ) ) {
		return new WP_REST_Response( [ 'ok' => true ], 200 ); // pretend success; don't notify
	}

	$name    = $req->get_param( 'name' );
	$email   = $req->get_param( 'email' );
	$phone   = $req->get_param( 'phone' );
	$country = (string) $req->get_param( 'country' );
	$enquiry = (string) $req->get_param( 'enquiry_for' );
	$message = (string) $req->get_param( 'message' );
	$source  = (string) $req->get_param( 'source' );

	if ( ! is_email( $email ) ) {
		return new WP_REST_Response( [ 'ok' => false, 'error' => 'Invalid email address.' ], 400 );
	}
	if ( strlen( $name ) < 2 || strlen( $phone ) < 4 ) {
		return new WP_REST_Response( [ 'ok' => false, 'error' => 'Please complete all required fields.' ], 400 );
	}

	// Simple rate limit: max 5 submissions/min per IP via transient.
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? preg_replace( '/[^0-9a-f:.]/', '', $_SERVER['REMOTE_ADDR'] ) : 'unknown';
	$key = 'rehab_cf_' . md5( $ip );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		return new WP_REST_Response( [ 'ok' => false, 'error' => 'Too many submissions. Please try again in a minute.' ], 429 );
	}
	set_transient( $key, $count + 1, MINUTE_IN_SECONDS );

	// Store as a rehab_submission post.
	$post_id = wp_insert_post( [
		'post_type'    => 'rehab_submission',
		'post_status'  => 'publish',
		'post_title'   => $name . ' — ' . $email,
		'post_content' => $message,
		'meta_input'   => [
			'_email'       => $email,
			'_phone'       => $phone,
			'_country'     => $country,
			'_enquiry_for' => $enquiry,
			'_source'      => $source ?: ( wp_get_referer() ?: '' ),
			'_ip'          => $ip,
			'_ua'          => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
		],
	], true );

	if ( is_wp_error( $post_id ) ) {
		// Don't fail the user — still try email
		$post_id = 0;
	}

	// Email the recipient.
	$recipient = (string) get_theme_mod( 'rehab_form_recipient', get_option( 'admin_email' ) );
	$subject_tpl = (string) get_theme_mod( 'rehab_form_subject', 'New enquiry — %name%' );
	$subject = strtr( $subject_tpl, [ '%name%' => $name, '%email%' => $email ] );

	// Resolve the submitting page to a human-readable title so the team can tell
	// at a glance which page produced the lead, not just its raw URL.
	$source_url = $source ?: ( wp_get_referer() ?: '' );
	$page_title = '';
	if ( $source_url ) {
		$src_pid = url_to_postid( $source_url );
		if ( $src_pid ) {
			$page_title = get_the_title( $src_pid );
		}
	}

	$body  = "A new contact form submission:\n\n";
	$body .= "Name:    $name\n";
	$body .= "Email:   $email\n";
	$body .= "Phone:   $phone\n";
	if ( $country ) $body .= "Country: $country\n";
	if ( $enquiry ) $body .= "Enquiry for: $enquiry\n";
	if ( $message ) $body .= "\nMessage:\n$message\n";
	$body .= "\n----\n";
	if ( $page_title ) $body .= "Page:    $page_title\n";
	$body .= "Source:  " . $source_url . "\n";
	if ( $post_id ) $body .= "Admin:   " . admin_url( 'post.php?action=edit&post=' . $post_id ) . "\n";

	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	];
	$mail_ok = wp_mail( $recipient, $subject, $body, $headers );

	return new WP_REST_Response( [
		'ok'      => true,
		'stored'  => (bool) $post_id,
		'emailed' => (bool) $mail_ok,
	], 200 );
}

/**
 * Show submitted email, phone, country, enquiry and form type in the admin
 * list view (REH-103 — country/enquiry were stored but surfaced nowhere).
 */
add_filter( 'manage_rehab_submission_posts_columns', function ( $cols ) {
	$cols['email']   = __( 'Email', 'rehab-parent' );
	$cols['phone']   = __( 'Phone', 'rehab-parent' );
	$cols['country'] = __( 'Country', 'rehab-parent' );
	$cols['enquiry'] = __( 'Enquiry for', 'rehab-parent' );
	$cols['form']    = __( 'Form', 'rehab-parent' );
	return $cols;
} );
add_action( 'manage_rehab_submission_posts_custom_column', function ( $col, $post_id ) {
	if ( $col === 'email' )   echo esc_html( get_post_meta( $post_id, '_email', true ) );
	if ( $col === 'phone' )   echo esc_html( get_post_meta( $post_id, '_phone', true ) );
	if ( $col === 'country' ) echo esc_html( get_post_meta( $post_id, '_country', true ) );
	if ( $col === 'enquiry' ) echo esc_html( get_post_meta( $post_id, '_enquiry_for', true ) );
	if ( $col === 'form' )    echo esc_html( get_post_meta( $post_id, '_form', true ) ?: 'contact' );
}, 10, 2 );

/**
 * Read-only "Submission details" meta box on the edit screen — every stored
 * field in one place, so nobody needs the (hidden) custom-fields UI.
 */
add_action( 'add_meta_boxes_rehab_submission', function () {
	add_meta_box( 'rehab-submission-details', __( 'Submission details', 'rehab-parent' ), function ( $post ) {
		$rows = [
			'Email'       => get_post_meta( $post->ID, '_email', true ),
			'Phone'       => get_post_meta( $post->ID, '_phone', true ),
			'Country'     => get_post_meta( $post->ID, '_country', true ),
			'Enquiry for' => get_post_meta( $post->ID, '_enquiry_for', true ),
			'Form'        => get_post_meta( $post->ID, '_form', true ) ?: 'contact',
			'Source'      => get_post_meta( $post->ID, '_source', true ),
			'Signature'   => get_post_meta( $post->ID, '_signature', true ),
			'IP'          => get_post_meta( $post->ID, '_ip', true ),
			'Browser'     => get_post_meta( $post->ID, '_ua', true ),
		];
		echo '<table class="widefat striped" style="border:0">';
		foreach ( $rows as $label => $value ) {
			if ( '' === (string) $value ) continue;
			$out = ( 0 === strpos( (string) $value, 'http' ) )
				? '<a href="' . esc_url( $value ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>'
				: esc_html( $value );
			echo '<tr><td style="width:120px"><strong>' . esc_html( $label ) . '</strong></td><td>' . $out . '</td></tr>';
		}
		echo '</table>';
	}, 'rehab_submission', 'normal', 'high' );
} );

/**
 * Lock submissions against accidental edits (REH-103): the edit screen opens
 * read-only (title, message editor and Update button disabled) with an
 * "Unlock editing" button. The unlock lasts only for the current page load —
 * closing or reloading locks it again. Client-side guard against fat-finger
 * edits, not a permission boundary (the CPT is admin-only anyway).
 */
add_action( 'admin_footer-post.php', function () {
	if ( 'rehab_submission' !== get_post_type() ) return;
	?>
	<style>
		.rehab-sub-locked #title,
		.rehab-sub-locked #content { pointer-events: none; background: #f6f7f7; }
		.rehab-sub-locked #wp-content-editor-tools,
		.rehab-sub-locked .mce-toolbar-grp { opacity: 0.4; pointer-events: none; }
		#rehab-sub-lock-notice { display: flex; align-items: center; gap: 12px; }
	</style>
	<script>
	( function () {
		var wrap = document.getElementById( 'poststuff' );
		var publish = document.getElementById( 'publish' );
		if ( ! wrap ) return;

		function setLocked( locked ) {
			wrap.classList.toggle( 'rehab-sub-locked', locked );
			[ 'title', 'content' ].forEach( function ( id ) {
				var el = document.getElementById( id );
				if ( el ) el.readOnly = locked;
			} );
			if ( publish ) publish.disabled = locked;
			var btn = document.getElementById( 'rehab-sub-unlock' );
			if ( btn ) btn.textContent = locked ? 'Unlock editing' : 'Lock again';
			// TinyMCE 4 (WP bundled) exposes setMode(); TinyMCE 5+ mode.set().
			var mce = window.tinymce && tinymce.get( 'content' );
			if ( mce ) {
				try {
					var mode = locked ? 'readonly' : 'design';
					if ( mce.mode && mce.mode.set ) mce.mode.set( mode );
					else if ( mce.setMode ) mce.setMode( mode );
				} catch ( err ) { /* CSS pointer-events guard still applies */ }
			}
		}

		var notice = document.createElement( 'div' );
		notice.id = 'rehab-sub-lock-notice';
		notice.className = 'notice notice-info';
		notice.innerHTML = '<p style="margin:8px 0"><strong>Submission is read-only.</strong> Lead data is locked against accidental edits; unlocking lasts until this page is closed or reloaded.</p>' +
			'<button type="button" class="button" id="rehab-sub-unlock">Unlock editing</button>';
		var heading = document.querySelector( '.wrap h1' );
		if ( heading ) heading.insertAdjacentElement( 'afterend', notice );

		var locked = true;
		document.getElementById( 'rehab-sub-unlock' ).addEventListener( 'click', function () {
			locked = ! locked;
			setLocked( locked );
		} );

		// TinyMCE boots after the footer — wait for it before applying the lock.
		var tries = 0;
		( function apply() {
			if ( window.tinymce && tinymce.get( 'content' ) || tries++ > 40 ) {
				setLocked( locked );
			} else {
				setTimeout( apply, 250 );
			}
		} )();
	} )();
	</script>
	<?php
} );
