<?php
/**
 * Rehab Parent theme bootstrap.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme setup: register theme support flags.
 */
function rehab_parent_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'custom-logo', [
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	] );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );

	add_editor_style( [
		'assets/css/tokens.css',
		'assets/css/typography.css',
		'assets/css/buttons.css',
		'assets/css/layout.css',
		'assets/css/utilities.css',
	] );

	register_nav_menus(
		[
			'primary'      => __( 'Primary Menu (mega menu)', 'rehab-parent' ),
			'footer_col_1' => __( 'Footer Column 1', 'rehab-parent' ),
			'footer_col_2' => __( 'Footer Column 2', 'rehab-parent' ),
			'footer_col_3' => __( 'Footer Column 3', 'rehab-parent' ),
			'footer_legal' => __( 'Footer Legal (sub-footer)', 'rehab-parent' ),
		]
	);
}
add_action( 'after_setup_theme', 'rehab_parent_setup' );

/**
 * Enqueue parent theme stylesheets in dependency order.
 */
function rehab_parent_enqueue(): void {
	$theme_version = wp_get_theme()->get( 'Version' );
	$base_uri      = get_template_directory_uri() . '/assets';

	$stylesheets = [
		'rehab-tokens'     => [ "$base_uri/css/tokens.css", [] ],
		'rehab-typography' => [ "$base_uri/css/typography.css", [ 'rehab-tokens' ] ],
		'rehab-layout'     => [ "$base_uri/css/layout.css", [ 'rehab-tokens' ] ],
		'rehab-buttons'    => [ "$base_uri/css/buttons.css", [ 'rehab-tokens' ] ],
		'rehab-utilities'  => [ "$base_uri/css/utilities.css", [ 'rehab-tokens' ] ],
		'rehab-header'     => [ "$base_uri/css/header.css", [ 'rehab-tokens' ] ],
		'rehab-footer'     => [ "$base_uri/css/footer.css", [ 'rehab-tokens' ] ],
		'rehab-article'    => [ "$base_uri/css/article.css", [ 'rehab-tokens', 'rehab-typography' ] ],
		'rehab-articles-index' => [ "$base_uri/css/articles-index.css", [ 'rehab-tokens' ] ],
		'rehab-treatment'  => [ "$base_uri/css/treatment.css", [ 'rehab-tokens' ] ],
		'rehab-util-pages' => [ "$base_uri/css/util-pages.css", [ 'rehab-tokens' ] ],
		'rehab-a11y'       => [ "$base_uri/css/a11y.css", [ 'rehab-tokens' ] ],
	];

	foreach ( $stylesheets as $handle => [ $src, $deps ] ) {
		wp_enqueue_style( $handle, $src, $deps, $theme_version );
	}

	wp_enqueue_script(
		'rehab-header',
		"$base_uri/js/header.js",
		[],
		$theme_version,
		true
	);
	wp_enqueue_script(
		'rehab-image-fallback',
		"$base_uri/js/image-fallback.js",
		[],
		$theme_version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'rehab_parent_enqueue' );

/**
 * Theme customizer settings: phone number, etc.
 */
function rehab_parent_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section( 'rehab_contact', [
		'title'    => __( 'Contact', 'rehab-parent' ),
		'priority' => 30,
	] );

	$wp_customize->add_setting( 'rehab_phone_display', [
		'default'           => '+66 96 582 3832',
		'sanitize_callback' => 'sanitize_text_field',
	] );
	$wp_customize->add_control( 'rehab_phone_display', [
		'label'   => __( 'Phone — display text', 'rehab-parent' ),
		'section' => 'rehab_contact',
		'type'    => 'text',
	] );

	$wp_customize->add_setting( 'rehab_phone_number', [
		'default'           => '+66965823832',
		'sanitize_callback' => 'sanitize_text_field',
	] );
	$wp_customize->add_control( 'rehab_phone_number', [
		'label'       => __( 'Phone — tel: number', 'rehab-parent' ),
		'description' => __( 'Digits + country code, e.g. +66965823832', 'rehab-parent' ),
		'section'     => 'rehab_contact',
		'type'        => 'text',
	] );

	$wp_customize->add_setting( 'rehab_menu_pitch_title', [
		'default'           => __( 'In-patient luxury rehab in Thailand', 'rehab-parent' ),
		'sanitize_callback' => 'sanitize_text_field',
	] );
	$wp_customize->add_control( 'rehab_menu_pitch_title', [
		'label'   => __( 'Mega menu — pitch title', 'rehab-parent' ),
		'section' => 'rehab_contact',
		'type'    => 'text',
	] );

	$wp_customize->add_setting( 'rehab_menu_pitch_body', [
		'default'           => __( 'Doctor-led, evidence-based recovery in a private 5-star sanctuary.', 'rehab-parent' ),
		'sanitize_callback' => 'sanitize_textarea_field',
	] );
	$wp_customize->add_control( 'rehab_menu_pitch_body', [
		'label'   => __( 'Mega menu — pitch body', 'rehab-parent' ),
		'section' => 'rehab_contact',
		'type'    => 'textarea',
	] );

	// Footer settings
	$wp_customize->add_section( 'rehab_footer', [
		'title'    => __( 'Footer', 'rehab-parent' ),
		'priority' => 35,
	] );

	$footer_fields = [
		'rehab_footer_address'     => [ 'label' => 'Address (multiline)', 'type' => 'textarea' ],
		'rehab_footer_intl_phones' => [ 'label' => "International phones (one per line: Label|+number)", 'type' => 'textarea' ],
		'rehab_footer_copyright'   => [ 'label' => 'Copyright text', 'type' => 'text' ],
		'rehab_social_facebook'    => [ 'label' => 'Facebook URL', 'type' => 'url' ],
		'rehab_social_instagram'   => [ 'label' => 'Instagram URL', 'type' => 'url' ],
		'rehab_social_linkedin'    => [ 'label' => 'LinkedIn URL', 'type' => 'url' ],
		'rehab_social_youtube'     => [ 'label' => 'YouTube URL', 'type' => 'url' ],
	];
	foreach ( $footer_fields as $key => $cfg ) {
		$sanitize = $cfg['type'] === 'url' ? 'esc_url_raw' : ( $cfg['type'] === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field' );
		$wp_customize->add_setting( $key, [ 'default' => '', 'sanitize_callback' => $sanitize ] );
		$wp_customize->add_control( $key, [
			'label'   => $cfg['label'],
			'section' => 'rehab_footer',
			'type'    => $cfg['type'],
		] );
	}
}
add_action( 'customize_register', 'rehab_parent_customize_register' );

require_once get_template_directory() . '/inc/patterns.php';
require_once get_template_directory() . '/inc/cpt-team-member.php';
require_once get_template_directory() . '/inc/article-helpers.php';

/**
 * Inject a fallback (email + phone CTA) into rehab/contact-form blocks when no
 * working form shortcode is present (e.g., Forminator not installed).
 */
function rehab_parent_contact_form_fallback( string $block_content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'rehab/contact-form' ) {
		return $block_content;
	}
	$shortcode = trim( $block['attrs']['shortcode'] ?? '' );
	$rendered  = $shortcode ? do_shortcode( $shortcode ) : '';
	$has_form  = $rendered && stripos( $rendered, '<form' ) !== false;
	if ( $has_form ) {
		return str_replace(
			'<div class="rehab-contact-form__embed"></div>',
			'<div class="rehab-contact-form__embed">' . $rendered . '</div>',
			$block_content
		);
	}
	$phone_text = get_theme_mod( 'rehab_phone_display', '+66 96 582 3832' );
	$phone_tel  = preg_replace( '/[^0-9+]/', '', get_theme_mod( 'rehab_phone_number', '+66965823832' ) );
	$fallback  = '<div class="rehab-contact-fallback">';
	$fallback .= '<p class="rehab-contact-fallback__email">Email <a href="mailto:info@diamondrehabthailand.com">info@diamondrehabthailand.com</a></p>';
	$fallback .= '<p class="rehab-contact-fallback__sub">Or call our admissions team — available 24/7</p>';
	$fallback .= '<a class="rehab-btn rehab-btn--luxury" href="tel:' . esc_attr( $phone_tel ) . '">' . esc_html( $phone_text ) . '</a>';
	$fallback .= '</div>';
	return str_replace(
		'<div class="rehab-contact-form__embed"></div>',
		'<div class="rehab-contact-form__embed">' . $fallback . '</div>',
		$block_content
	);
}
add_filter( 'render_block', 'rehab_parent_contact_form_fallback', 10, 2 );

/**
 * Inject the Google Maps iframe into rehab/map blocks at render time
 * (KSES strips iframes from saved post_content, so we re-add server-side).
 */
function rehab_parent_map_iframe( string $block_content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'rehab/map' ) {
		return $block_content;
	}
	$embed_url = trim( $block['attrs']['embedUrl'] ?? '' );
	if ( ! $embed_url ) {
		return $block_content;
	}
	$heading = $block['attrs']['heading'] ?? 'Find us';
	$iframe = sprintf(
		'<iframe src="%s" title="%s" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen style="border:0;width:100%%;height:100%%;"></iframe>',
		esc_url( $embed_url ),
		esc_attr( $heading )
	);
	return preg_replace(
		'/<div class="rehab-map__embed">\s*<\/div>/',
		'<div class="rehab-map__embed">' . $iframe . '</div>',
		$block_content,
		1
	);
}
add_filter( 'render_block', 'rehab_parent_map_iframe', 10, 2 );

/**
 * Inject star SVGs into rehab/testimonial blocks at render time
 * (KSES strips <svg>/<polygon> from saved post_content).
 */
function rehab_parent_testimonial_stars( string $block_content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'rehab/testimonial' ) {
		return $block_content;
	}
	$rating = (int) ( $block['attrs']['rating'] ?? 5 );
	$rating = max( 0, min( 5, $rating ) );
	$star = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><polygon points="12,2 15.1,8.6 22,9.5 17,14.4 18.2,21.5 12,18 5.8,21.5 7,14.4 2,9.5 8.9,8.6"></polygon></svg>';
	$stars = str_repeat( $star, $rating );
	// Replace empty (or any) stars container content with the rendered stars.
	return preg_replace(
		'/<div class="rehab-testimonial__stars"([^>]*)>.*?<\/div>/is',
		'<div class="rehab-testimonial__stars"$1>' . $stars . '</div>',
		$block_content,
		1
	);
}
add_filter( 'render_block', 'rehab_parent_testimonial_stars', 10, 2 );

/**
 * Clean up testimonial quotes truncated mid-word from the source data.
 * If the quote ends with a partial word (no terminal punctuation, last token has no
 * obvious sentence end), drop the partial word and append an ellipsis.
 */
function rehab_parent_testimonial_truncation( string $block_content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'rehab/testimonial' ) {
		return $block_content;
	}
	return preg_replace_callback(
		'/(<p class="rehab-testimonial__quote">)(.*?)(<\/p>)/s',
		function ( $m ) {
			$quote = trim( $m[2] );
			$last_char = mb_substr( $quote, -1 );
			$ends_clean = in_array( $last_char, [ '.', '!', '?', '"', '”', ')' ], true );
			if ( $ends_clean ) {
				return $m[1] . $quote . $m[3];
			}
			// Drop the trailing partial word, then append ellipsis.
			$cleaned = preg_replace( '/\s*\S+$/u', '', $quote );
			$cleaned = rtrim( $cleaned, " \t,;:" );
			return $m[1] . $cleaned . '…' . $m[3];
		},
		$block_content
	);
}
add_filter( 'render_block', 'rehab_parent_testimonial_truncation', 11, 2 );

/**
 * Register a server-rendered "Article feed" block. Hosting it in the parent
 * theme keeps it independent of the @wordpress/scripts plugin build pipeline.
 * Place the block on a page via `<!-- wp:rehab/article-feed -->` or
 * `<!-- wp:rehab/article-feed {"count":4,"heading":"From our journal"} -->`.
 */
/**
 * Emit SEO meta tags: description, Open Graph, Twitter card. Auto-extracts
 * description from the post's first paragraph (or excerpt). Falls back to a
 * brand default image when the page has no featured image.
 */
function rehab_parent_seo_meta(): void {
	if ( is_admin() || is_feed() || is_search() || is_404() ) return;

	$site_name = get_bloginfo( 'name' );
	$is_single = is_singular();
	$is_front  = is_front_page();
	$post      = $is_single ? get_post() : null;

	$title       = $is_single ? wp_get_document_title() : ( get_bloginfo( 'name' ) . ' — ' . get_bloginfo( 'description' ) );
	$description = '';
	$image       = get_stylesheet_directory_uri() . '/assets/img/hero/pool-pavilion.avif';
	if ( ! file_exists( get_stylesheet_directory() . '/assets/img/hero/pool-pavilion.avif' ) ) {
		$image = home_url( '/wp-content/themes/diamond-child/assets/img/hero/pool-pavilion.avif' );
	}
	$url         = $is_single && $post ? get_permalink( $post ) : home_url( '/' );
	$type        = ( $is_single && ! $is_front ) ? 'article' : 'website';

	$brand_default_description = 'Doctor-led, residential drug and alcohol rehab in Hua Hin, Thailand. Maximum 12 clients at a time, absolute confidentiality, lifetime aftercare. Voted #1 by The Thaiger.';

	if ( $post ) {
		// Prefer manual excerpt; else first <p> paragraph from content; else brand default.
		$excerpt = trim( $post->post_excerpt );
		if ( ! $excerpt ) {
			$content = $post->post_content;
			if ( preg_match( '/<p[^>]*>(.+?)<\/p>/is', $content, $pm ) ) {
				$first_para = trim( wp_strip_all_tags( $pm[1] ) );
				if ( strlen( $first_para ) > 30 ) {
					$excerpt = $first_para;
				}
			}
		}
		if ( ! $excerpt ) {
			$excerpt = $brand_default_description;
		}
		$description = $excerpt;

		$thumb_url = get_the_post_thumbnail_url( $post, 'large' );
		if ( $thumb_url ) {
			$image = $thumb_url;
		}
	} else {
		$description = $brand_default_description;
	}

	$description = wp_strip_all_tags( $description );
	$description = preg_replace( '/\s+/', ' ', $description );
	$description = trim( $description );
	if ( mb_strlen( $description ) > 200 ) {
		$description = mb_substr( $description, 0, 197 ) . '…';
	}

	$tags = [];
	if ( $description ) {
		$tags[] = '<meta name="description" content="' . esc_attr( $description ) . '">';
	}
	$tags[] = '<meta property="og:type" content="' . esc_attr( $type ) . '">';
	$tags[] = '<meta property="og:title" content="' . esc_attr( $title ) . '">';
	if ( $description ) {
		$tags[] = '<meta property="og:description" content="' . esc_attr( $description ) . '">';
	}
	$tags[] = '<meta property="og:url" content="' . esc_url( $url ) . '">';
	$tags[] = '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">';
	$tags[] = '<meta property="og:image" content="' . esc_url( $image ) . '">';
	$tags[] = '<meta property="og:image:alt" content="' . esc_attr( $title ) . '">';
	$tags[] = '<meta name="twitter:card" content="summary_large_image">';
	$tags[] = '<meta name="twitter:title" content="' . esc_attr( $title ) . '">';
	if ( $description ) {
		$tags[] = '<meta name="twitter:description" content="' . esc_attr( $description ) . '">';
	}
	$tags[] = '<meta name="twitter:image" content="' . esc_url( $image ) . '">';

	echo "\n\t" . implode( "\n\t", $tags ) . "\n";
}
add_action( 'wp_head', 'rehab_parent_seo_meta', 5 );

/**
 * Emit JSON-LD structured data: Organization (sitewide), WebSite with
 * SearchAction (sitewide), Article (on template-article pages),
 * BreadcrumbList (on template-treatment pages).
 */
function rehab_parent_jsonld(): void {
	if ( is_admin() || is_feed() || is_search() || is_404() ) return;

	$site_url   = home_url( '/' );
	$site_name  = get_bloginfo( 'name' );
	$site_desc  = get_bloginfo( 'description' );
	$logo_url   = get_stylesheet_directory_uri() . '/assets/img/hero/pool-pavilion.avif';
	if ( ! file_exists( get_stylesheet_directory() . '/assets/img/hero/pool-pavilion.avif' ) ) {
		// Parent theme is active without a child — fall back to content uploads.
		$logo_url = home_url( '/wp-content/themes/diamond-child/assets/img/hero/pool-pavilion.avif' );
	}

	$blocks = [];

	// Sitewide: MedicalBusiness organization.
	$blocks[] = [
		'@context'    => 'https://schema.org',
		'@type'       => 'MedicalBusiness',
		'@id'         => $site_url . '#org',
		'name'        => $site_name,
		'url'         => $site_url,
		'description' => $site_desc,
		'logo'        => $logo_url,
		'image'       => $logo_url,
		'telephone'   => '+66 96 582 3832',
		'address'     => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => '8 Moo 14, Soi Mon Mai Hin Lek Fai',
			'addressLocality' => 'Hua Hin',
			'addressRegion'   => 'Prachuap Khiri Khan',
			'postalCode'      => '77110',
			'addressCountry'  => 'TH',
		],
		'geo'         => [
			'@type'     => 'GeoCoordinates',
			'latitude'  => 12.5556,
			'longitude' => 99.9131,
		],
		'sameAs'      => [
			'https://www.facebook.com/diamondrehabthailand',
			'https://www.instagram.com/diamondrehabthailand',
		],
		'medicalSpecialty' => [ 'Addiction Medicine', 'Psychiatry' ],
		'priceRange'  => '$$$$',
	];

	// Sitewide: WebSite with SearchAction.
	$blocks[] = [
		'@context'        => 'https://schema.org',
		'@type'           => 'WebSite',
		'@id'             => $site_url . '#website',
		'url'             => $site_url,
		'name'            => $site_name,
		'description'     => $site_desc,
		'publisher'       => [ '@id' => $site_url . '#org' ],
		'potentialAction' => [
			'@type'       => 'SearchAction',
			'target'      => [
				'@type'       => 'EntryPoint',
				'urlTemplate' => $site_url . '?s={search_term_string}',
			],
			'query-input' => 'required name=search_term_string',
		],
	];

	// Per-page: Article (on template-article pages) or BreadcrumbList (on treatment pages).
	if ( is_singular() ) {
		$post = get_post();
		$tpl  = get_page_template_slug( $post );

		if ( $tpl === 'template-article.php' ) {
			$thumb_url = get_the_post_thumbnail_url( $post, 'large' );
			$content   = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
			$word_ct   = str_word_count( $content );
			$blocks[] = array_filter( [
				'@context'         => 'https://schema.org',
				'@type'            => 'Article',
				'@id'              => get_permalink( $post ) . '#article',
				'mainEntityOfPage' => get_permalink( $post ),
				'headline'         => get_the_title( $post ),
				'datePublished'    => get_the_date( 'c', $post ),
				'dateModified'     => get_the_modified_date( 'c', $post ),
				'image'            => $thumb_url ?: $logo_url,
				'author'           => [
					'@type' => 'Organization',
					'@id'   => $site_url . '#org',
					'name'  => $site_name,
				],
				'publisher'        => [ '@id' => $site_url . '#org' ],
				'wordCount'        => $word_ct ?: null,
			] );
		}

		if ( $tpl === 'template-treatment.php' ) {
			$blocks[] = [
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => [
					[
						'@type'    => 'ListItem',
						'position' => 1,
						'name'     => 'Home',
						'item'     => $site_url,
					],
					[
						'@type'    => 'ListItem',
						'position' => 2,
						'name'     => 'Treatments',
						'item'     => home_url( '/all-treatments/' ),
					],
					[
						'@type'    => 'ListItem',
						'position' => 3,
						'name'     => get_the_title( $post ),
					],
				],
			];
		}
	}

	echo "\n";
	foreach ( $blocks as $b ) {
		// JSON_UNESCAPED_SLASHES keeps URLs clean; UNESCAPED_UNICODE keeps non-ASCII readable.
		echo "<script type=\"application/ld+json\">"
			. wp_json_encode( $b, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
			. "</script>\n";
	}
}
add_action( 'wp_head', 'rehab_parent_jsonld', 6 );

/**
 * Replace WP's default site-icon `<link>` tags with a theme-bundled SVG favicon
 * that lives in the active child theme. Avoids 404s when the Customizer's
 * uploaded site icon files are missing on this environment.
 */
function rehab_parent_favicon(): void {
	$svg_path = get_stylesheet_directory() . '/assets/img/favicon.svg';
	if ( ! file_exists( $svg_path ) ) return;

	$svg_url  = get_stylesheet_directory_uri() . '/assets/img/favicon.svg';
	echo "\n\t<link rel=\"icon\" type=\"image/svg+xml\" href=\"" . esc_url( $svg_url ) . "\">\n";
}
add_action( 'wp_head', 'rehab_parent_favicon', 1 );

/**
 * Suppress the WP-managed `<link rel="icon">` tags that point at the missing
 * Customizer-uploaded site icon. We replace them with our theme-bundled SVG
 * via `rehab_parent_favicon()` above.
 */
remove_action( 'wp_head', 'wp_site_icon', 99 );

/**
 * Trim default WP <head> output: remove emoji/legacy/version-leaking tags.
 * - `wp_emoji_styles`/`print_emoji_detection_script`: we don't ship emoji UX.
 * - `wp_generator`: don't leak the WP version.
 * - `rsd_link`/`wlwmanifest_link`: legacy publishing endpoints (RSD, Windows Live Writer).
 * - `wp_oembed_add_discovery_links`: we don't expose oEmbed for our pages.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

/**
 * Strip `href="#"` from menu items that have children — those are disclosure
 * parents, not real links. Replacing with `role="button"` + `aria-haspopup="true"`
 * gives them proper semantics and prevents the page-jump behaviour when clicked.
 */
function rehab_parent_walker_menu_no_hash( string $item_html, $item, int $depth, $args ): string {
	if ( strpos( $item_html, 'href="#"' ) !== false ) {
		$item_html = str_replace(
			'href="#"',
			'role="button" tabindex="0" aria-haspopup="true"',
			$item_html
		);
	}
	return $item_html;
}
add_filter( 'walker_nav_menu_start_el', 'rehab_parent_walker_menu_no_hash', 10, 4 );

/**
 * Add a "Rehab quick actions" admin dashboard widget so writers see the most
 * common editing destinations as soon as they log in.
 */
function rehab_parent_dashboard_widget(): void {
	wp_add_dashboard_widget(
		'rehab_quick_actions',
		__( 'Rehab — quick actions', 'rehab-parent' ),
		'rehab_parent_dashboard_widget_render'
	);
}
add_action( 'wp_dashboard_setup', 'rehab_parent_dashboard_widget' );

function rehab_parent_dashboard_widget_render(): void {
	$home_id = (int) get_option( 'page_on_front', 0 );
	$links = [
		[ 'View live site →',           home_url( '/' ) ],
		[ 'Edit homepage',              $home_id ? get_edit_post_link( $home_id, 'admin' ) : admin_url( 'edit.php?post_type=page' ) ],
		[ 'Add a new article',          admin_url( 'post-new.php?post_type=page' ) ],
		[ 'Manage all articles',        admin_url( 'edit.php?post_type=page&meta_key=_wp_page_template&meta_value=template-article.php' ) ],
		[ 'Edit Why us',                admin_url( 'post.php?post=825&action=edit' ) ],
		[ 'Edit FAQ',                   admin_url( 'post.php?post=1197&action=edit' ) ],
		[ 'Edit Cost / programs',       admin_url( 'post.php?post=834&action=edit' ) ],
		[ 'Edit Contact us',            admin_url( 'post.php?post=1189&action=edit' ) ],
		[ 'Customize footer / phones',  admin_url( 'customize.php' ) ],
	];
	echo '<ul style="margin:0;padding:0;list-style:none;display:grid;gap:0.4rem;">';
	foreach ( $links as $row ) {
		[ $label, $url ] = $row;
		printf(
			'<li><a class="button button-secondary" style="display:block;text-align:left;" href="%s">%s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}
	echo '</ul>';
	echo '<p style="margin:1rem 0 0;font-size:0.85em;color:#666;">';
	echo esc_html__( 'Need help? Patterns are in Inserter → Patterns → Rehab. Contact admin to onboard a new brand.', 'rehab-parent' );
	echo '</p>';
}

function rehab_parent_register_article_feed(): void {
	register_block_type( 'rehab/article-feed', [
		'api_version'     => 3,
		'title'           => __( 'Article Feed', 'rehab-parent' ),
		'description'     => __( 'Server-rendered grid of the most recently updated article-template pages.', 'rehab-parent' ),
		'category'        => 'rehab',
		'icon'            => 'list-view',
		'keywords'        => [ 'articles', 'blog', 'feed', 'journal' ],
		'attributes'      => [
			'count'      => [ 'type' => 'number', 'default' => 4 ],
			'heading'    => [ 'type' => 'string', 'default' => 'From our journal' ],
			'subheading' => [ 'type' => 'string', 'default' => 'Evidence-led writing on addiction, recovery, and the science behind effective treatment.' ],
		],
		'render_callback' => 'rehab_parent_render_article_feed',
		'supports'        => [ 'html' => false ],
		'example'         => [ 'attributes' => [ 'heading' => 'From our journal', 'count' => 4 ] ],
	] );
}
add_action( 'init', 'rehab_parent_register_article_feed' );

function rehab_parent_render_article_feed( array $attributes ): string {
	$count   = max( 1, min( 12, (int) ( $attributes['count'] ?? 4 ) ) );
	$heading = (string) ( $attributes['heading'] ?? 'From our journal' );
	$sub     = (string) ( $attributes['subheading'] ?? '' );

	$articles = get_posts( [
		'post_type'      => 'page',
		'posts_per_page' => $count,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'template-article.php',
		'orderby'        => 'modified',
		'order'          => 'DESC',
	] );
	if ( ! $articles ) return '';

	ob_start();
	?>
	<section class="rehab-article-feed rehab-bg-cream" aria-label="Featured articles">
		<div class="rehab-container">
			<header class="rehab-article-feed__header">
				<h2 class="rehab-heading rehab-heading--lg"><?php echo esc_html( $heading ); ?></h2>
				<?php if ( $sub ) : ?>
					<p class="rehab-article-feed__sub"><?php echo esc_html( $sub ); ?></p>
				<?php endif; ?>
			</header>
			<ul class="rehab-articles-index__grid">
				<?php foreach ( $articles as $a ) :
					$thumb   = get_the_post_thumbnail_url( $a->ID, 'medium' );
					$plain   = wp_strip_all_tags( $a->post_content );
					$excerpt = wp_trim_words( $plain, 22, '…' );
					$reading = max( 1, (int) round( str_word_count( $plain ) / 220 ) );
					?>
					<li class="rehab-articles-index__card">
						<a href="<?php echo esc_url( get_permalink( $a ) ); ?>" class="rehab-articles-index__card-link">
							<?php if ( $thumb ) : ?>
								<div class="rehab-articles-index__thumb">
									<img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy" decoding="async">
								</div>
							<?php endif; ?>
							<div class="rehab-articles-index__body">
								<h3 class="rehab-articles-index__title"><?php echo esc_html( get_the_title( $a ) ); ?></h3>
								<p class="rehab-articles-index__meta">
									<span><?php echo esc_html( $reading ); ?> min read</span>
									<span aria-hidden="true">·</span>
									<span>Updated <?php echo esc_html( get_the_modified_date( 'M Y', $a ) ); ?></span>
								</p>
								<p class="rehab-articles-index__excerpt"><?php echo esc_html( $excerpt ); ?></p>
								<span class="rehab-articles-index__read-more">Read article →</span>
							</div>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="rehab-article-feed__cta">
				<a class="rehab-btn rehab-btn--outline" href="<?php echo esc_url( home_url( '/all-articles/' ) ); ?>">Browse all articles</a>
			</div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Inject explicit width/height into known asset <img> tags so the browser can
 * reserve space and avoid layout shift. Static manifest of files we ship in
 * the child theme; covers all hero/card/team/accommodation imagery.
 */
function rehab_parent_image_dimensions( string $content ): string {
	if ( strpos( $content, '<img' ) === false ) return $content;

	static $manifest = [
		'/wp-content/themes/diamond-child/assets/img/hero/pool-pavilion.avif'                  => [ 1080, 720 ],
		'/wp-content/themes/diamond-child/assets/img/accommodation/pool-villa.avif'            => [ 2560, 1707 ],
		'/wp-content/themes/diamond-child/assets/img/cards/alcohol-addiction-treatment.avif'   => [ 800, 533 ],
		'/wp-content/themes/diamond-child/assets/img/cards/drug-addiction-treatment.avif'      => [ 800, 533 ],
		'/wp-content/themes/diamond-child/assets/img/cards/cocaine-addiction-treatment.avif'   => [ 800, 533 ],
		'/wp-content/themes/diamond-child/assets/img/cards/anxiety-treatment.avif'             => [ 800, 533 ],
		'/wp-content/themes/diamond-child/assets/img/team/theo-panwadee-de-vries.avif'         => [ 500, 560 ],
		'/wp-content/themes/diamond-child/assets/img/team/sergio-pereira.avif'                 => [ 500, 560 ],
		'/wp-content/themes/diamond-child/assets/img/team/jiraporn-takonchai.avif'             => [ 500, 560 ],
		'/wp-content/themes/diamond-child/assets/img/team/roshan-fernando.avif'                => [ 500, 560 ],
		'/wp-content/themes/diamond-child/assets/img/team/wei-ling.avif'                       => [ 500, 560 ],
		'/wp-content/themes/diamond-child/assets/img/team/augustine-dewes.avif'                => [ 500, 560 ],
	];

	return preg_replace_callback(
		'/<img\b([^>]*?)\s*\/?>/i',
		function ( $m ) use ( $manifest ) {
			$attrs = rtrim( $m[1] );
			if ( preg_match( '/\bwidth=/i', $attrs ) || preg_match( '/\bheight=/i', $attrs ) ) {
				return $m[0]; // already has dimensions
			}
			if ( ! preg_match( '/\bsrc="([^"]+)"/i', $attrs, $sm ) ) return $m[0];
			$src = $sm[1];
			$path = parse_url( $src, PHP_URL_PATH );
			if ( ! $path || ! isset( $manifest[ $path ] ) ) return $m[0];
			[ $w, $h ] = $manifest[ $path ];
			return '<img' . $attrs . ' width="' . $w . '" height="' . $h . '">';
		},
		$content
	);
}
add_filter( 'the_content', 'rehab_parent_image_dimensions', 20 );
