<?php
/**
 * Brand From address for outgoing mail (deliverability / DKIM alignment).
 *
 * By default WordPress sends from `wordpress@<host>`, which on a Cloudways
 * server is the staging `*.cloudwaysapps.com` host — a domain with no aligned
 * SPF/DKIM, so notifications (e.g. the contact-form lead emails from
 * `zz-contact-form.php`) tend to spam-folder even once an SMTP relay is wired up.
 *
 * This sets the From header to the site's own verified brand-domain address so
 * it aligns with the sending provider's SPF/DKIM (e.g. the Cloudways Elastic
 * Email add-on configured for `diamondrehabthailand.com`).
 *
 * The codebase is shared across every rehab brand site, so the address is NOT
 * hardcoded — it reads per-site options, set on each server via WP-CLI:
 *
 *   wp option update rehab_mail_from 'info@diamondrehabthailand.com'
 *   wp option update rehab_mail_from_name 'The Diamond Rehab Thailand'   # optional
 *
 * Both filters are safe no-ops when the option is unset/invalid, so sites
 * without a configured brand address keep WordPress's default behavior.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Override the From email only when a valid brand address is configured.
 */
add_filter( 'wp_mail_from', function ( $from ) {
	$brand = get_option( 'rehab_mail_from' );
	return ( $brand && is_email( $brand ) ) ? $brand : $from;
} );

/**
 * Override the From name with the configured value, or the site title.
 * Leaves an already-customised name (set by another plugin) untouched.
 */
add_filter( 'wp_mail_from_name', function ( $name ) {
	$brand = get_option( 'rehab_mail_from_name' );
	if ( $brand ) {
		return $brand;
	}
	// Only replace WordPress's default placeholder, not a deliberate override.
	if ( '' === $name || 'WordPress' === $name ) {
		$blogname = get_option( 'blogname' );
		if ( $blogname ) {
			return wp_specialchars_decode( (string) $blogname, ENT_QUOTES );
		}
	}
	return $name;
} );
