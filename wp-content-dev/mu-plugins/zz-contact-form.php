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
			'_email'   => $email,
			'_phone'   => $phone,
			'_country' => $country,
			'_source'  => $source ?: ( wp_get_referer() ?: '' ),
			'_ip'      => $ip,
			'_ua'      => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
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

	$body  = "A new contact form submission:\n\n";
	$body .= "Name:    $name\n";
	$body .= "Email:   $email\n";
	$body .= "Phone:   $phone\n";
	if ( $country ) $body .= "Country: $country\n";
	if ( $message ) $body .= "\nMessage:\n$message\n";
	$body .= "\n----\n";
	$body .= "Source:  " . ( $source ?: wp_get_referer() ) . "\n";
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
 * Show submitted email + phone in the admin list view.
 */
add_filter( 'manage_rehab_submission_posts_columns', function ( $cols ) {
	$cols['email'] = __( 'Email', 'rehab-parent' );
	$cols['phone'] = __( 'Phone', 'rehab-parent' );
	return $cols;
} );
add_action( 'manage_rehab_submission_posts_custom_column', function ( $col, $post_id ) {
	if ( $col === 'email' ) echo esc_html( get_post_meta( $post_id, '_email', true ) );
	if ( $col === 'phone' ) echo esc_html( get_post_meta( $post_id, '_phone', true ) );
}, 10, 2 );
