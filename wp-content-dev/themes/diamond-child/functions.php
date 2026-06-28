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
 * Does the current singular view render any of the homepage section blocks?
 * The homepage can now be built two ways: the legacy hardcoded
 * page-homepage.php template, or an editable page made of `rehab/home-*`
 * blocks. Both need the same drt- asset bundle.
 */
function drt_has_homepage_blocks(): bool {
	if ( ! is_singular() ) {
		return false;
	}
	$post = get_post();
	return $post instanceof WP_Post && false !== strpos( (string) $post->post_content, 'wp:rehab/home-' );
}

/**
 * Enqueue the homepage design-system bundle (drt- CSS + Swiper/Fancybox).
 *
 * @param bool $with_js Load the interactive JS (Swiper/Fancybox/homepage.js).
 *                      Skipped in the block editor, where carousels/lightbox
 *                      init would fight Gutenberg — the CSS alone gives the
 *                      canvas a faithful preview.
 */
function drt_homepage_enqueue_bundle( bool $with_js = true ): void {
	$child_dir = get_stylesheet_directory();
	$child_uri = get_stylesheet_directory_uri();
	// Structural homepage CSS now lives in the PARENT theme (shared across all
	// brand children, token-driven — REH-42); only brand JS/images stay here.
	$css_path  = get_template_directory() . '/assets/css/homepage';
	$css_uri   = get_template_directory_uri() . '/assets/css/homepage';

	// Body font: Inter 300/400/500 via Google Fonts. Replaces a dead self-hosted
	// @font-face that 404'd; mirrors how the Playfair heading font is loaded.
	wp_enqueue_style( 'inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap', [], null );

	// Vendor CSS: Swiper + Fancybox (CDN).
	wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11' );
	wp_enqueue_style( 'fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.css', [], '5' );

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

	if ( ! $with_js ) {
		return;
	}

	// Vendor JS + homepage behaviours (carousels, lightbox, sticky bits).
	wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11', true );
	wp_enqueue_script( 'fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.js', [], '5', true );

	$js_file = $child_dir . '/assets/js/homepage.js';
	if ( file_exists( $js_file ) ) {
		wp_enqueue_script( 'drt-homepage', $child_uri . '/assets/js/homepage.js', [ 'swiper', 'fancybox' ], filemtime( $js_file ), true );
	}
}

/**
 * Front-end: load the bundle on the legacy template, the new block-rendering
 * template, or any singular view whose content uses the homepage blocks.
 */
function drt_homepage_assets(): void {
	if (
		is_page_template( 'page-homepage.php' ) ||
		is_page_template( 'page-homepage-blocks.php' ) ||
		drt_has_homepage_blocks()
	) {
		drt_homepage_enqueue_bundle( true );
	}
}
add_action( 'wp_enqueue_scripts', 'drt_homepage_assets', 20 );

/**
 * Block editor: load the drt- CSS (no JS) so the homepage blocks preview
 * faithfully in the canvas.
 *
 * The editor renders blocks inside an iframe, so the stylesheets must come
 * through `enqueue_block_assets` (which WP injects into the iframe) — NOT
 * `enqueue_block_editor_assets`, which only reaches the editor's outer
 * document and leaves the canvas unstyled. Guarded to admin so the front end
 * keeps using the gated `drt_homepage_assets()` path and we never double-load.
 */
function drt_homepage_editor_assets(): void {
	if ( ! is_admin() ) {
		return;
	}
	drt_homepage_enqueue_bundle( false );

	// The heading font (Playfair Display) is pulled in via a <link> in the page
	// templates on the front end; the editor canvas has no such link, so load it
	// here too or canvas headings would fall back to Georgia.
	wp_enqueue_style( 'drt-playfair', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&display=swap', [], null );

	// Re-apply the front end's `.drt-homepage`-scoped heading typography to the
	// editor canvas (which has no `.drt-homepage` wrapper). Scoped to homepage
	// `.drt-*` classes, so it's inert in non-homepage editors.
	$editor_css = get_template_directory() . '/assets/css/homepage/homepage-editor.css';
	if ( file_exists( $editor_css ) ) {
		wp_enqueue_style(
			'drt-homepage-editor',
			get_template_directory_uri() . '/assets/css/homepage/homepage-editor.css',
			[ 'drt-homepage-base' ],
			filemtime( $editor_css )
		);
	}
}
add_action( 'enqueue_block_assets', 'drt_homepage_editor_assets' );

/**
 * Brand contact details + social profiles (REH-44).
 *
 * These override the parent theme's `rehab_*` theme-mod defaults via the
 * `theme_mod_{$name}` filter so the values ship with the code (the deploy is
 * git-based; theme-mods set only in the DB wouldn't travel to production). The
 * primary phone is the Australian admissions line; the Thai intl callback list
 * and WhatsApp number are left untouched. To change any of these later, edit
 * here rather than the Customizer (the filter always wins).
 */
function diamond_child_brand_contact( array $map ): void {
	foreach ( $map as $mod => $value ) {
		add_filter(
			"theme_mod_{$mod}",
			static function () use ( $value ) {
				return $value;
			}
		);
	}
}
diamond_child_brand_contact( [
	'rehab_phone_display'   => '+61 2 7908 2277',
	'rehab_phone_number'    => '+61279082277',
	'rehab_social_facebook' => 'https://www.facebook.com/profile.php?id=100062157021886',
	'rehab_social_instagram'=> 'https://www.instagram.com/diamondrehabthailand/',
	'rehab_social_x'        => 'https://twitter.com/rehab_thailand',
	'rehab_social_pinterest'=> 'https://www.pinterest.com/diamondrehabthailand/',
	'rehab_social_threads'  => 'https://www.threads.net/@diamondrehabthailand',

	// Brand identity that used to be hardcoded in the parent theme (REH-46).
	// Setting it here keeps Diamond pixel-identical while the parent stays
	// brand-agnostic for the other child themes.
	'rehab_menu_pitch_title'   => 'In-patient luxury rehab in Thailand',
	'rehab_footer_address'     => "8, Moo 14, Soi Mon Mai Hin Lek Fai\nHua Hin District, Prachuap Khiri Khan\nThailand 77110",
	'rehab_contact_email'      => 'info@diamondrehabthailand.com',
	'rehab_default_description'=> 'Doctor-led, residential drug and alcohol rehab in Hua Hin, Thailand. Maximum 12 clients at a time, absolute confidentiality, lifetime aftercare. Voted #1 by The Thaiger.',
	// LocalBusiness schema (fallback JSON-LD — RankMath emits the live schema).
	'rehab_addr_street'        => '8 Moo 14, Soi Mon Mai Hin Lek Fai',
	'rehab_addr_locality'      => 'Hua Hin',
	'rehab_addr_region'        => 'Prachuap Khiri Khan',
	'rehab_addr_postal'        => '77110',
	'rehab_addr_country'       => 'TH',
	'rehab_geo_lat'            => '12.5556',
	'rehab_geo_lng'            => '99.9131',
] );
