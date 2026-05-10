<?php
/**
 * Anker Holding — child theme.
 *
 * @package AnkerChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue child theme stylesheet (token overrides) after parent CSS.
 */
function anker_child_enqueue(): void {
	wp_enqueue_style(
		'anker-child',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'rehab-tokens', 'rehab-typography', 'rehab-layout', 'rehab-buttons', 'rehab-utilities' ],
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'anker_child_enqueue', 20 );

/**
 * Render Anker logo (text wordmark for now — replace with SVG when assets land).
 */
function anker_child_custom_logo( string $html ): string {
	$svg_path = get_stylesheet_directory() . '/assets/img/logo.svg';
	if ( file_exists( $svg_path ) ) {
		$svg = file_get_contents( $svg_path );
		if ( $svg ) {
			$home = esc_url( home_url( '/' ) );
			$alt  = esc_attr( get_bloginfo( 'name' ) );
			return sprintf(
				'<a class="custom-logo-link" href="%s" rel="home" aria-label="%s">%s</a>',
				$home,
				$alt,
				$svg
			);
		}
	}
	$home = esc_url( home_url( '/' ) );
	return sprintf(
		'<a class="custom-logo-link custom-logo-link--text" href="%s" rel="home"><span class="custom-logo-wordmark">ANKER</span><span class="custom-logo-tagline">HOLDING</span></a>',
		$home
	);
}
add_filter( 'get_custom_logo', 'anker_child_custom_logo', 10, 1 );

function anker_child_has_custom_logo( $has_logo ): bool {
	return true;
}
add_filter( 'has_custom_logo', 'anker_child_has_custom_logo' );
