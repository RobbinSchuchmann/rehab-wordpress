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
		'assets/css/editor.css',
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
	$base_uri  = get_template_directory_uri() . '/assets';
	$base_path = get_template_directory() . '/assets';

	/**
	 * Version parent-theme assets by their own file mtime. These files are shared
	 * by every child theme, so keying the cache-bust off the active child theme's
	 * version (the old behaviour) meant editing an asset here didn't bust caches
	 * unless someone also remembered to bump a child version. mtime busts the
	 * cache automatically whenever the file actually changes.
	 */
	$asset_ver = static function ( string $rel ) use ( $base_path ): string {
		$path = "$base_path/$rel";
		$mtime = @filemtime( $path );
		return $mtime ? (string) $mtime : (string) wp_get_theme()->get( 'Version' );
	};

	$stylesheets = [
		'rehab-tokens'     => [ 'css/tokens.css', [] ],
		'rehab-base'       => [ 'css/base.css', [ 'rehab-tokens' ] ],
		'rehab-typography' => [ 'css/typography.css', [ 'rehab-tokens' ] ],
		'rehab-layout'     => [ 'css/layout.css', [ 'rehab-tokens' ] ],
		'rehab-buttons'    => [ 'css/buttons.css', [ 'rehab-tokens' ] ],
		'rehab-utilities'  => [ 'css/utilities.css', [ 'rehab-tokens' ] ],
		'rehab-header'     => [ 'css/header.css', [ 'rehab-tokens' ] ],
		'rehab-utility-bar' => [ 'css/utility-bar.css', [ 'rehab-tokens', 'rehab-buttons' ] ],
		'rehab-footer'     => [ 'css/footer.css', [ 'rehab-tokens' ] ],
		'rehab-article'    => [ 'css/article.css', [ 'rehab-tokens', 'rehab-typography' ] ],
		'rehab-articles-index' => [ 'css/articles-index.css', [ 'rehab-tokens' ] ],
		'rehab-treatment'  => [ 'css/treatment.css', [ 'rehab-tokens' ] ],
		'rehab-util-pages' => [ 'css/util-pages.css', [ 'rehab-tokens' ] ],
		'rehab-a11y'       => [ 'css/a11y.css', [ 'rehab-tokens' ] ],
	];

	foreach ( $stylesheets as $handle => [ $rel, $deps ] ) {
		wp_enqueue_style( $handle, "$base_uri/$rel", $deps, $asset_ver( $rel ) );
	}

	wp_enqueue_script(
		'rehab-header',
		"$base_uri/js/header.js",
		[],
		$asset_ver( 'js/header.js' ),
		true
	);
	wp_enqueue_script(
		'rehab-image-fallback',
		"$base_uri/js/image-fallback.js",
		[],
		$asset_ver( 'js/image-fallback.js' ),
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
require_once get_template_directory() . '/inc/page-categories.php';

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
/**
 * True when Rank Math plugin is active. When it is, we stand down — Rank
 * Math owns <title>, meta description, OG tags, schema markup. When it
 * isn't, our filter reads the same postmeta and emits everything itself.
 */
function rehab_parent_rank_math_active(): bool {
	return class_exists( 'RankMath\Helper' ) || function_exists( 'rank_math' );
}

function rehab_parent_seo_meta(): void {
	if ( is_admin() || is_feed() || is_search() || is_404() ) return;
	if ( rehab_parent_rank_math_active() ) return; // Let Rank Math own SEO output.

	$site_name = get_bloginfo( 'name' );
	$is_single = is_singular();
	$is_front  = is_front_page();
	$post      = $is_single ? get_post() : null;
	$post_id   = $post ? $post->ID : 0;

	// Phase 1: read Rank Math postmeta where set; fall back to our own logic.
	// 236 pages have rank_math_focus_keyword, 196 have descriptions, 43 have
	// custom titles — that's real human SEO work we want to preserve.
	$rm_title    = $post_id ? trim( (string) get_post_meta( $post_id, 'rank_math_title', true ) ) : '';
	$rm_desc     = $post_id ? trim( (string) get_post_meta( $post_id, 'rank_math_description', true ) ) : '';
	$rm_og_desc  = $post_id ? trim( (string) get_post_meta( $post_id, 'rank_math_facebook_description', true ) ) : '';
	$rm_og_image = $post_id ? trim( (string) get_post_meta( $post_id, 'rank_math_facebook_image', true ) ) : '';
	if ( ! $rm_og_image && $post_id ) {
		$rm_og_image = trim( (string) get_post_meta( $post_id, 'rank_math_og_content_image', true ) );
	}
	// Rank Math title supports tokens like %title%, %sep%, %sitename% — expand
	// them naively. (Full token expansion would re-implement the Rank Math
	// engine; this covers the common ones.)
	if ( $rm_title ) {
		$rm_title = strtr( $rm_title, [
			'%title%'    => $post ? get_the_title( $post ) : $site_name,
			'%sitename%' => $site_name,
			'%site_name%'=> $site_name,
			'%sep%'      => '|',
			'%page%'     => '',
		] );
		$rm_title = trim( preg_replace( '/\s+/', ' ', $rm_title ) );
	}

	$title       = $rm_title ?: ( $is_single ? wp_get_document_title() : ( get_bloginfo( 'name' ) . ' — ' . get_bloginfo( 'description' ) ) );
	$description = '';
	$image       = get_stylesheet_directory_uri() . '/assets/img/hero/pool-pavilion.avif';
	if ( ! file_exists( get_stylesheet_directory() . '/assets/img/hero/pool-pavilion.avif' ) ) {
		$image = home_url( '/wp-content/themes/diamond-child/assets/img/hero/pool-pavilion.avif' );
	}
	$url         = $is_single && $post ? get_permalink( $post ) : home_url( '/' );
	$type        = ( $is_single && ! $is_front ) ? 'article' : 'website';

	$brand_default_description = 'Doctor-led, residential drug and alcohol rehab in Hua Hin, Thailand. Maximum 12 clients at a time, absolute confidentiality, lifetime aftercare. Voted #1 by The Thaiger.';

	if ( $post ) {
		// Description priority: Rank Math → manual excerpt → first <p> → default.
		$excerpt = $rm_desc;
		if ( ! $excerpt ) $excerpt = trim( $post->post_excerpt );
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

		// Image priority: Rank Math FB image → featured thumbnail → brand default.
		if ( $rm_og_image ) {
			$image = $rm_og_image;
		} else {
			$thumb_url = get_the_post_thumbnail_url( $post, 'large' );
			if ( $thumb_url ) {
				$image = $thumb_url;
			}
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

	// OG description can be different from meta description.
	$og_description = $rm_og_desc ? trim( wp_strip_all_tags( $rm_og_desc ) ) : $description;

	// Robots: respect rank_math_robots (a serialized array) for noindex/nofollow.
	$robots_meta = '';
	if ( $post_id ) {
		$rm_robots = get_post_meta( $post_id, 'rank_math_robots', true );
		if ( is_array( $rm_robots ) && ! empty( $rm_robots ) ) {
			$robots_meta = implode( ', ', array_map( 'sanitize_text_field', $rm_robots ) );
		}
	}

	$tags = [];
	if ( $robots_meta ) {
		$tags[] = '<meta name="robots" content="' . esc_attr( $robots_meta ) . '">';
	}
	if ( $description ) {
		$tags[] = '<meta name="description" content="' . esc_attr( $description ) . '">';
	}
	$tags[] = '<meta property="og:type" content="' . esc_attr( $type ) . '">';
	$tags[] = '<meta property="og:title" content="' . esc_attr( $title ) . '">';
	if ( $og_description ) {
		$tags[] = '<meta property="og:description" content="' . esc_attr( $og_description ) . '">';
	}
	$tags[] = '<meta property="og:url" content="' . esc_url( $url ) . '">';
	$tags[] = '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">';
	$tags[] = '<meta property="og:image" content="' . esc_url( $image ) . '">';
	$tags[] = '<meta property="og:image:alt" content="' . esc_attr( $title ) . '">';
	$tags[] = '<meta name="twitter:card" content="summary_large_image">';
	$tags[] = '<meta name="twitter:title" content="' . esc_attr( $title ) . '">';
	if ( $og_description ) {
		$tags[] = '<meta name="twitter:description" content="' . esc_attr( $og_description ) . '">';
	}
	$tags[] = '<meta name="twitter:image" content="' . esc_url( $image ) . '">';

	echo "\n\t" . implode( "\n\t", $tags ) . "\n";
}
add_action( 'wp_head', 'rehab_parent_seo_meta', 5 );

/**
 * Override <title> tag to use Rank Math's custom title where set.
 * Hooks `pre_get_document_title` so wp_get_document_title() returns the
 * Rank Math title verbatim. Tokens like %title%, %sep%, %sitename% are
 * expanded; full Rank Math token expansion is intentionally out of scope.
 */
function rehab_parent_rank_math_title( $title ) {
	if ( is_admin() || is_feed() ) return $title;
	if ( rehab_parent_rank_math_active() ) return $title; // Rank Math will set it itself.
	if ( ! is_singular() ) return $title;
	$rm = trim( (string) get_post_meta( get_queried_object_id(), 'rank_math_title', true ) );
	if ( ! $rm ) return $title;
	return trim( preg_replace( '/\s+/', ' ', strtr( $rm, [
		'%title%'    => get_the_title(),
		'%sitename%' => get_bloginfo( 'name' ),
		'%site_name%'=> get_bloginfo( 'name' ),
		'%sep%'      => '|',
		'%page%'     => '',
	] ) ) );
}
add_filter( 'pre_get_document_title', 'rehab_parent_rank_math_title', 9 );

/**
 * Resolve a breadcrumb category label for a post. Used by template-treatment
 * and template-article. Priority:
 *
 *   1. Per-page meta override `_rehab_breadcrumb_category` (string)
 *   2. Rank Math primary category (term_id stored in `rank_math_primary_category`)
 *   3. First `category` taxonomy term assigned to the post
 *   4. Slug-based regex inference (substance / prescription / mental health)
 *   5. Empty string if nothing matches
 */
function rehab_breadcrumb_category( int $post_id ): string {
	// Categories considered too generic to use as a breadcrumb segment because
	// they duplicate the parent "Treatments" / "Articles" segment.
	$generic_slugs = [ 'uncategorized', 'page', 'treatment', 'information', 'article' ];

	$override = get_post_meta( $post_id, '_rehab_breadcrumb_category', true );
	if ( $override ) return (string) $override;

	$primary_id = (int) get_post_meta( $post_id, 'rank_math_primary_category', true );
	if ( $primary_id > 0 ) {
		$term = get_term( $primary_id, 'category' );
		if ( $term && ! is_wp_error( $term ) && ! in_array( strtolower( $term->slug ), $generic_slugs, true ) ) {
			return $term->name;
		}
	}

	$cats = get_the_category( $post_id );
	if ( $cats && ! is_wp_error( $cats ) ) {
		foreach ( $cats as $c ) {
			if ( in_array( strtolower( $c->slug ), $generic_slugs, true ) ) continue;
			return $c->name;
		}
	}

	$post = get_post( $post_id );
	if ( $post ) {
		$slug = $post->post_name;
		if ( preg_match( '/(cocaine|ice-addiction|meth|heroin|alcohol|crack|ecstasy|ghb|marijuana|cannabis)/i', $slug ) ) return 'Substance addiction';
		if ( preg_match( '/(xanax|valium|oxycontin|tramadol|ritalin|adderall|prescription)/i', $slug ) ) return 'Prescription drug';
		if ( preg_match( '/(anxiety|depression|ptsd|trauma|burnout|insomnia|gambling|sex-addiction|codependency)/i', $slug ) ) return 'Mental health';
	}

	return '';
}

/**
 * Emit JSON-LD structured data: Organization (sitewide), WebSite with
 * SearchAction (sitewide), Article (on template-article pages),
 * BreadcrumbList (on template-treatment pages).
 */
function rehab_parent_jsonld(): void {
	if ( is_admin() || is_feed() || is_search() || is_404() ) return;
	if ( rehab_parent_rank_math_active() ) return; // Let Rank Math emit schema.

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
 * Turn disclosure parents into button-like controls.
 *
 * Disclosure parents are menu items that have children but no real destination —
 * they exist only to group and reveal their submenu. They're authored either as a
 * "#" custom link or with an empty URL (which WordPress renders as a bare <a> with
 * no href at all). Either way the anchor is not a real link, so we strip the
 * placeholder href and mark it `role="button"` + `aria-haspopup` + `aria-expanded`.
 * `header.js` wires the click/keyboard toggle (so they work on touch, where there
 * is no hover) and `header.css` reveals the submenu on the `.is-open` parent.
 * Real links — including parents that DO point somewhere — pass through untouched.
 */
function rehab_parent_walker_menu_no_hash( string $item_html, $item, int $depth, $args ): string {
	$classes      = is_array( $item->classes ?? null ) ? $item->classes : [];
	$has_children = in_array( 'menu-item-has-children', $classes, true );
	$url          = isset( $item->url ) ? trim( $item->url ) : '';

	if ( ! $has_children || ( $url !== '' && $url !== '#' ) ) {
		return $item_html;
	}

	$attrs = 'role="button" tabindex="0" aria-haspopup="true" aria-expanded="false"';
	// Drop the placeholder href (`#` or empty) if present, then mark as a button.
	$item_html = preg_replace( '/\s*href="(?:#|)"/', '', $item_html, 1 );
	$item_html = preg_replace( '/<a\b/', '<a ' . $attrs, $item_html, 1 );

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

/**
 * Allow inline SVGs in post_content so block markup that includes icons
 * (rehab/pillars, rehab/signs-grid, doctor cards, final-cta contact icons)
 * survives wp_kses_post on save.
 *
 * Gutenberg post content is sanitized via kses_post when wp_update_post is
 * called. Default kses_allowed_html['post'] does not include <svg>, <path>,
 * <circle>, <polyline>, <rect> etc — so any inline icon gets stripped.
 *
 * Only adds SVG tags to the 'post' context, not 'data' or other contexts.
 */
function rehab_parent_allow_svg_in_post( $tags, $context ) {
	if ( $context !== 'post' ) return $tags;
	$svg_attrs = [
		'xmlns' => true, 'viewbox' => true, 'fill' => true, 'stroke' => true,
		'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true,
		'width' => true, 'height' => true, 'class' => true, 'aria-hidden' => true,
		'role' => true, 'focusable' => true, 'preserveaspectratio' => true,
		'transform' => true, 'opacity' => true, 'fill-rule' => true, 'clip-rule' => true,
	];
	$path_attrs = [
		'd' => true, 'fill' => true, 'stroke' => true, 'transform' => true,
		'opacity' => true, 'class' => true,
	];
	$tags['svg']      = $svg_attrs;
	$tags['path']     = $path_attrs;
	$tags['circle']   = [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true ];
	$tags['rect']     = [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true ];
	$tags['line']     = [ 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true ];
	$tags['polyline'] = [ 'points' => true, 'fill' => true, 'stroke' => true ];
	$tags['polygon']  = [ 'points' => true, 'fill' => true, 'stroke' => true ];
	$tags['g']        = [ 'fill' => true, 'stroke' => true, 'transform' => true ];
	$tags['defs']     = [];
	$tags['use']      = [ 'href' => true, 'xlink:href' => true ];
	$tags['title']    = [];
	$tags['desc']     = [];

	// Form elements — needed by rehab/final-cta which renders a real contact
	// form into post_content. WP's default 'post' context strips form/input/
	// textarea/button/label without this.
	$tags['form']     = [
		'class' => true, 'id' => true, 'action' => true, 'method' => true,
		'enctype' => true, 'novalidate' => true, 'autocomplete' => true,
		'data-rehab-contact-form' => true, 'aria-label' => true,
	];
	$tags['input']    = [
		'class' => true, 'id' => true, 'name' => true, 'type' => true,
		'value' => true, 'placeholder' => true, 'required' => true,
		'disabled' => true, 'readonly' => true, 'autocomplete' => true,
		'tabindex' => true, 'maxlength' => true, 'minlength' => true,
		'min' => true, 'max' => true, 'step' => true, 'pattern' => true,
		'inputmode' => true, 'aria-label' => true, 'aria-describedby' => true,
		'data-*' => true,
	];
	$tags['textarea'] = [
		'class' => true, 'id' => true, 'name' => true, 'placeholder' => true,
		'rows' => true, 'cols' => true, 'maxlength' => true, 'minlength' => true,
		'required' => true, 'disabled' => true, 'autocomplete' => true,
		'aria-label' => true, 'aria-describedby' => true,
	];
	$tags['button']   = [
		'class' => true, 'id' => true, 'type' => true, 'name' => true,
		'value' => true, 'disabled' => true, 'aria-label' => true,
		'aria-expanded' => true, 'aria-controls' => true, 'aria-selected' => true,
		'role' => true, 'data-*' => true, 'style' => true,
	];
	$tags['label']    = [ 'class' => true, 'for' => true, 'id' => true ];
	$tags['select']   = [ 'class' => true, 'id' => true, 'name' => true, 'required' => true, 'disabled' => true ];
	$tags['option']   = [ 'value' => true, 'selected' => true, 'disabled' => true ];
	$tags['fieldset'] = [ 'class' => true, 'disabled' => true ];
	$tags['legend']   = [ 'class' => true ];

	// Make sure div allows data-* attributes (used by tab panels etc).
	if ( isset( $tags['div'] ) && is_array( $tags['div'] ) ) {
		$tags['div']['data-*'] = true;
		$tags['div']['hidden'] = true;
		$tags['div']['role']   = true;
		$tags['div']['aria-label']  = true;
		$tags['div']['aria-hidden'] = true;
	}

	return $tags;
}
add_filter( 'wp_kses_allowed_html', 'rehab_parent_allow_svg_in_post', 10, 2 );
