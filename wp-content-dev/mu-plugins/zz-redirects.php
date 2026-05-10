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
	$path = trim( (string) $path, '/' );
	$mirrors = [
		'intake-form-halfway-house' => '/',
	];
	if ( isset( $mirrors[ $path ] ) ) {
		wp_safe_redirect( home_url( $mirrors[ $path ] ), 301 );
		exit;
	}
}, 1 );
