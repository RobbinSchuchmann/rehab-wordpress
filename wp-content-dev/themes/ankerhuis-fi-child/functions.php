<?php
/**
 * Ankerhuis FI — child theme.
 *
 * @package AnkerhuisFiChild
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function ankerhuis_fi_child_enqueue(): void {
	wp_enqueue_style(
		'ankerhuis-fi-child',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'rehab-tokens', 'rehab-typography', 'rehab-layout', 'rehab-buttons', 'rehab-utilities' ],
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'ankerhuis_fi_child_enqueue', 20 );

function ankerhuis_fi_child_custom_logo( string $html ): string {
	$svg_path = get_stylesheet_directory() . '/assets/img/logo.svg';
	if ( file_exists( $svg_path ) ) {
		$svg = file_get_contents( $svg_path );
		if ( $svg ) {
			return sprintf(
				'<a class="custom-logo-link" href="%s" rel="home" aria-label="%s">%s</a>',
				esc_url( home_url( '/' ) ),
				esc_attr( get_bloginfo( 'name' ) ),
				$svg
			);
		}
	}
	return sprintf(
		'<a class="custom-logo-link custom-logo-link--text" href="%s" rel="home"><span class="custom-logo-wordmark">ANKERHUIS</span><span class="custom-logo-tagline">SUOMI</span></a>',
		esc_url( home_url( '/' ) )
	);
}
add_filter( 'get_custom_logo', 'ankerhuis_fi_child_custom_logo', 10, 1 );
add_filter( 'has_custom_logo', '__return_true' );
