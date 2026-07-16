<?php
/**
 * Redirects that mirror upstream live-site behavior we want to preserve.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', function () {
	if ( is_admin() ) return;
	$path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
	// Lowercased for matching — keys below must be lowercase. Covers in-content
	// links with stray capitals (e.g. /what-is-Marijuana-addiction/).
	$path = strtolower( trim( (string) $path, '/' ) );
	$mirrors = [
		'intake-form-halfway-house' => '/',
		// REH-118: dead cross-link slugs inherited from live articles (404
		// there too) — point them at the real articles.
		'nicotine-addiction-symptoms-and-treatment' => '/what-is-nicotine-addiction/',
		'what-is-marijuana-addiction'               => '/marijuana-addiction-symptoms-and-treatment/',
	];
	if ( isset( $mirrors[ $path ] ) ) {
		wp_safe_redirect( home_url( $mirrors[ $path ] ), 301 );
		exit;
	}
}, 1 );
