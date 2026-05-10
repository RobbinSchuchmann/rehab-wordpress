<?php
/**
 * Diamond Rehab — child theme.
 *
 * @package DiamondRehabChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue child theme stylesheet (token overrides) after parent CSS.
 */
function diamond_child_enqueue(): void {
	wp_enqueue_style(
		'diamond-child',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'rehab-tokens', 'rehab-typography', 'rehab-layout', 'rehab-buttons', 'rehab-utilities' ],
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'diamond_child_enqueue', 20 );

/**
 * Render Diamond's logo SVG inline. Child themes can override the parent
 * theme's logo by overriding the get_custom_logo filter, which the parent
 * theme uses inside header.php via the_custom_logo().
 */
function diamond_child_custom_logo( string $html ): string {
	$svg_path = get_stylesheet_directory() . '/assets/img/logo.svg';
	if ( ! file_exists( $svg_path ) ) {
		return $html;
	}
	$svg = file_get_contents( $svg_path );
	if ( ! $svg ) {
		return $html;
	}
	$home = esc_url( home_url( '/' ) );
	$alt  = esc_attr( get_bloginfo( 'name' ) );
	return sprintf(
		'<a class="custom-logo-link" href="%s" rel="home" aria-label="%s">%s</a>',
		$home,
		$alt,
		$svg
	);
}
add_filter( 'get_custom_logo', 'diamond_child_custom_logo', 10, 1 );

/**
 * Tell the parent theme that we have a "custom logo" so it renders the_custom_logo()
 * even though no attachment is set in the customizer.
 */
function diamond_child_has_custom_logo( $has_logo ) {
	return true;
}
add_filter( 'has_custom_logo', 'diamond_child_has_custom_logo' );

/**
 * Helper: get URI for a homepage image asset.
 * Used inside template-parts/homepage/section-*.php partials.
 */
function drt_homepage_img( $path = '' ) {
	return get_stylesheet_directory_uri() . '/assets/images/homepage/' . ltrim( $path, '/' );
}

/**
 * Enqueue homepage CSS + JS only on pages using page-homepage.php template.
 * Swiper + Fancybox loaded from CDN since we don't bundle them.
 */
function drt_homepage_assets() {
	if ( ! is_page_template( 'page-homepage.php' ) ) {
		return;
	}

	$child_dir = get_stylesheet_directory();
	$child_uri = get_stylesheet_directory_uri();
	$css_path  = $child_dir . '/assets/css/homepage';
	$css_uri   = $child_uri . '/assets/css/homepage';

	// Vendor: Swiper (CDN).
	wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11' );
	wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11', true );

	// Vendor: Fancybox v5 (CDN).
	wp_enqueue_style( 'fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.css', [], '5' );
	wp_enqueue_script( 'fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.js', [], '5', true );

	// Homepage CSS — loaded in cascade order; each depends on the previous.
	$css_files = [
		'drt-homepage-base'       => 'homepage-base.css',
		'drt-homepage-layout'     => 'homepage-layout.css',
		'drt-homepage-components' => 'homepage-components.css',
		'drt-homepage-sections'   => 'homepage-sections.css',
		'drt-homepage-responsive' => 'homepage-responsive.css',
		'drt-homepage-footer'     => 'homepage-footer.css',
	];
	$prev_handle = 'swiper';
	foreach ( $css_files as $handle => $file ) {
		$full_path = $css_path . '/' . $file;
		if ( file_exists( $full_path ) ) {
			wp_enqueue_style( $handle, $css_uri . '/' . $file, [ $prev_handle ], filemtime( $full_path ) );
			$prev_handle = $handle;
		}
	}

	// Homepage JS.
	$js_file = $child_dir . '/assets/js/homepage.js';
	if ( file_exists( $js_file ) ) {
		wp_enqueue_script( 'drt-homepage', $child_uri . '/assets/js/homepage.js', [ 'swiper', 'fancybox' ], filemtime( $js_file ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'drt_homepage_assets', 20 );
