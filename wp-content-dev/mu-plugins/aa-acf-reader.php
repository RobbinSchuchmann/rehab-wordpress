<?php
/**
 * ACF Flexible Content reader for the Diamond legacy schema.
 *
 * The previous theme stored every page's body as an ACF flex-content field
 * named `sections`. Each section has a `layout` (banner/columns/article/tabs/
 * faq/global/cta) and a fixed set of sub-fields stored as flat postmeta keys
 * like `sections_0_heading_title`, `sections_0_side_image`, etc.
 *
 * This file exposes pure read helpers that return normalised PHP arrays —
 * one entry per section, only the fields we actually need to render. The
 * section→block mapper (Phase C) consumes these arrays; this file does NOT
 * emit any markup.
 *
 * Loads before zz-oneshot.php (aa- prefix), so oneshot tasks can use it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the ordered list of sections on a page (or global_section CPT).
 *
 * @return array[] List of normalised section arrays. Each carries `_idx`
 *                 (original position) and `_layout` (banner/article/…) plus
 *                 layout-specific fields. Returns [] if no flex content.
 */
function rehab_acf_get_sections( int $post_id ): array {
	$layouts = get_post_meta( $post_id, 'sections', true );
	if ( ! is_array( $layouts ) ) {
		return [];
	}

	$out = [];
	foreach ( $layouts as $idx => $layout ) {
		$out[] = rehab_acf_read_section( $post_id, (int) $idx, (string) $layout );
	}
	return $out;
}

/**
 * Resolve a `global_section` CPT to its sections array (recursive — globals
 * use the same flex schema as pages).
 *
 * @return array|null { id, title, sections[] } or null if not found.
 */
function rehab_acf_get_global_section( int $id ): ?array {
	$post = get_post( $id );
	if ( ! $post || 'global_section' !== $post->post_type ) {
		return null;
	}
	return [
		'id'       => $id,
		'title'    => $post->post_title,
		'sections' => rehab_acf_get_sections( $id ),
	];
}

/**
 * Resolve an attachment ID to { id, url, alt }. Returns null if the ID is
 * 0 / the attachment is gone.
 */
function rehab_acf_image( int $attachment_id ): ?array {
	if ( $attachment_id <= 0 ) {
		return null;
	}
	$url = wp_get_attachment_image_url( $attachment_id, 'full' );
	if ( ! $url ) {
		return null;
	}
	return [
		'id'  => $attachment_id,
		'url' => $url,
		'alt' => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
	];
}

/* --------------------------------------------------------------------------
 * Internal: per-layout normalisers.
 * -------------------------------------------------------------------------- */

/**
 * Read a single section and return a normalised array shape.
 *
 * Unknown layouts fall through to a raw shape with `_unknown => true` so the
 * mapper can decide whether to skip or warn.
 */
function rehab_acf_read_section( int $post_id, int $idx, string $layout ): array {
	$get = static function ( string $field ) use ( $post_id, $idx ) {
		return get_post_meta( $post_id, "sections_{$idx}_{$field}", true );
	};

	$base = [ '_idx' => $idx, '_layout' => $layout ];

	switch ( $layout ) {
		case 'banner':
			return $base + [
				'title'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
				'image_id' => (int) $get( 'image' ),
			];

		case 'columns':
			return $base + [
				'top_title'      => (string) $get( 'heading_title' ),
				'left_title'     => (string) $get( 'left_column_heading_title' ),
				'left_subtitle'  => (string) $get( 'left_column_heading_subtitle' ),
				'left_image_id'  => (int) $get( 'left_column_media_image' ),
				'right_title'    => (string) $get( 'right_column_heading_title' ),
				'right_subtitle' => (string) $get( 'right_column_heading_subtitle' ),
				'right_image_id' => (int) $get( 'right_column_media_image' ),
				'insert_cta'     => '1' === $get( 'insert_cta' ),
				'reversed'       => '1' === $get( 'reversed' ),
			];

		case 'article':
			$section_classes = (string) $get( 'settings_section_classes' );
			return $base + [
				'title'         => (string) $get( 'heading_title' ),
				'subtitle'      => (string) $get( 'heading_subtitle' ),
				'content'       => (string) $get( 'content' ),
				'side_image_id' => (int) $get( 'side_image' ),
				'reverse'       => '1' === $get( 'reverse' ),
				'narrow_text'   => '1' === $get( 'narrow-text' ) || '1' === $get( '-narrow-text' ),
				'bg_class'      => $section_classes,
				'bg_color'      => (string) $get( 'settings_background_color' ),
			];

		case 'tabs':
			$count = (int) $get( 'tabs' );
			$tabs  = [];
			for ( $j = 0; $j < $count; $j++ ) {
				$tabs[] = [
					'title' => (string) $get( "tabs_{$j}_title" ),
					'text'  => (string) $get( "tabs_{$j}_text" ),
				];
			}
			return $base + [
				'title'      => (string) $get( 'title' ),
				'photo_id'   => (int) $get( 'photo' ),
				'pull_quote' => (string) $get( 'text' ),
				'cite'       => (string) $get( 'cite' ),
				'tabs'       => $tabs,
			];

		case 'faq':
			$faq_ids_raw = $get( 'faq_ids' );
			$faq_ids     = is_string( $faq_ids_raw ) ? maybe_unserialize( $faq_ids_raw ) : $faq_ids_raw;
			$faq_ids     = is_array( $faq_ids ) ? array_values( array_map( 'intval', $faq_ids ) ) : [];
			return $base + [
				'eyebrow' => (string) $get( 'heading_label' ),
				'title'   => (string) $get( 'heading_title' ),
				'faq_ids' => $faq_ids,
			];

		case 'global':
			return $base + [
				'global_section_id' => (int) $get( 'global_section' ),
			];

		case 'cta':
			return $base + [
				'title'          => (string) $get( 'heading_title' ),
				'subtitle'       => (string) $get( 'heading_subtitle' ),
				'form_shortcode' => (string) $get( 'form' ),
				'photo_id'       => (int) $get( 'photo' ),
			];

		case 'generic':
			// Free-form HTML block. `generic_content` carries the body;
			// `settings_custom_styles` is an extra style override we ignore.
			return $base + [
				'eyebrow'    => (string) $get( 'heading_label' ),
				'title'      => (string) $get( 'heading_title' ),
				'subtitle'   => (string) $get( 'heading_subtitle' ),
				'html'       => (string) $get( 'generic_content' ),
				'bg_color'   => (string) $get( 'settings_background_color' ),
				'text_color' => (string) $get( 'settings_text_color' ),
			];

		case 'hero':
			// Like `banner` but with a background image + overlay rather
			// than a separate image-side slot.
			return $base + [
				'eyebrow'    => (string) $get( 'heading_label' ),
				'title'      => (string) $get( 'heading_title' ),
				'subtitle'   => (string) $get( 'heading_subtitle' ),
				'bg_image'   => (int) $get( 'settings_background_image' ),
				'overlay'    => (int) $get( 'overlay' ),
				'text_color' => (string) $get( 'settings_text_color' ),
			];

		case 'team':
			// Repeater of team-member cards. `members` holds the count;
			// each member has photo / name / position / message fields.
			$count   = (int) $get( 'members' );
			$members = [];
			for ( $j = 0; $j < $count; $j++ ) {
				$members[] = [
					'photo_id' => (int) $get( "members_{$j}_photo" ),
					'name'     => (string) $get( "members_{$j}_member_name" ),
					'position' => (string) $get( "members_{$j}_position" ),
					'message'  => (string) $get( "members_{$j}_message" ),
				];
			}
			return $base + [
				'title'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
				'members'  => $members,
			];

		case 'moodboard':
			// Two-column "image on one side" layout. `image_first` is the
			// hero image; `reverse` flips the side.
			return $base + [
				'title'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
				'eyebrow'  => (string) $get( 'heading_label' ),
				'image_id' => (int) $get( 'image_first' ),
				'reverse'  => '1' === $get( 'reverse' ),
			];

		case 'features':
			// Repeater of icon + title + description. `features` holds count.
			$count    = (int) $get( 'features' );
			$features = [];
			for ( $j = 0; $j < $count; $j++ ) {
				$features[] = [
					'icon_id'     => (int) $get( "features_{$j}_icon" ),
					'title'       => (string) $get( "features_{$j}_title" ),
					'description' => (string) $get( "features_{$j}_description" ),
				];
			}
			return $base + [
				'eyebrow'  => (string) $get( 'heading_label' ),
				'title'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
				'features' => $features,
			];

		case 'logos':
			// Press / partner logos. `logos` holds count; each row is
			// logo (attachment ID) + link (URL).
			$count = (int) $get( 'logos' );
			$logos = [];
			for ( $j = 0; $j < $count; $j++ ) {
				$logos[] = [
					'logo_id' => (int) $get( "logos_{$j}_logo" ),
					'url'     => (string) $get( "logos_{$j}_link" ),
				];
			}
			return $base + [
				'title' => (string) $get( 'heading_title' ),
				'logos' => $logos,
			];

		case 'gallery':
			// Image grid. `gallery_ids` is the count of media entries;
			// each row's `_photo` is the attachment ID. `_url` is rarely
			// set (only when the media is an external video).
			$count = (int) $get( 'gallery_ids' );
			$items = [];
			for ( $j = 0; $j < $count; $j++ ) {
				$items[] = [
					'photo_id' => (int) $get( "gallery_ids_{$j}_photo" ),
					'url'      => (string) $get( "gallery_ids_{$j}_url" ),
				];
			}
			return $base + [
				'eyebrow'  => (string) $get( 'heading_label' ),
				'title'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
				'items'    => $items,
			];

		case 'cards':
			// Linked image cards. `cards_N_link` is a serialized
			// {title,url,target} array.
			$count = (int) $get( 'cards' );
			$cards = [];
			for ( $j = 0; $j < $count; $j++ ) {
				$link_raw = maybe_unserialize( $get( "cards_{$j}_link" ) );
				$cards[]  = [
					'title'    => (string) $get( "cards_{$j}_title" ),
					'image_id' => (int) $get( "cards_{$j}_image" ),
					'url'      => is_array( $link_raw ) ? (string) ( $link_raw['url'] ?? '' ) : '',
				];
			}
			return $base + [
				'eyebrow'  => (string) $get( 'heading_label' ),
				'title'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
				'cards'    => $cards,
			];

		case 'cards-columns':
			// Two-column "related posts" list. left_posts / right_posts
			// are serialized arrays of post IDs; left_link / right_link
			// are serialized {title,url,target} arrays for the "View
			// all" cta below each column.
			$left_posts_raw  = maybe_unserialize( $get( 'left_posts' ) );
			$right_posts_raw = maybe_unserialize( $get( 'right_posts' ) );
			$left_link_raw   = maybe_unserialize( $get( 'left_link' ) );
			$right_link_raw  = maybe_unserialize( $get( 'right_link' ) );
			return $base + [
				'title'           => (string) $get( 'heading_title' ),
				'left_title'      => (string) $get( 'left_title' ),
				'left_post_ids'   => is_array( $left_posts_raw ) ? array_map( 'intval', $left_posts_raw ) : [],
				'left_link_title' => is_array( $left_link_raw ) ? (string) ( $left_link_raw['title'] ?? '' ) : '',
				'left_link_url'   => is_array( $left_link_raw ) ? (string) ( $left_link_raw['url'] ?? '' ) : '',
				'right_title'     => (string) $get( 'right_title' ),
				'right_post_ids'  => is_array( $right_posts_raw ) ? array_map( 'intval', $right_posts_raw ) : [],
				'right_link_title'=> is_array( $right_link_raw ) ? (string) ( $right_link_raw['title'] ?? '' ) : '',
				'right_link_url'  => is_array( $right_link_raw ) ? (string) ( $right_link_raw['url'] ?? '' ) : '',
			];

		case 'message':
			// Pull-quote with attribution + portrait. The "quote" lives
			// in heading_title (despite the name).
			return $base + [
				'eyebrow'  => (string) $get( 'heading_label' ),
				'quote'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
				'photo_id' => (int) $get( 'photo' ),
				'cite'     => (string) $get( 'cite' ),
			];

		case 'map':
			// Embeds a map. `location` is a serialized ACF Google Map
			// field: { address, lat, lng, ... }.
			$loc = maybe_unserialize( $get( 'location' ) );
			$loc = is_array( $loc ) ? $loc : [];
			return $base + [
				'title'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
				'address'  => (string) ( $loc['address'] ?? '' ),
				'lat'      => isset( $loc['lat'] ) ? (float) $loc['lat'] : 0.0,
				'lng'      => isset( $loc['lng'] ) ? (float) $loc['lng'] : 0.0,
			];

		case 'steps':
			// Multi-row card grid (rows 1/2/3). Each row has its own
			// `_N_title` / `_N_text` items with the count in `row_N`.
			$rows = [];
			for ( $r = 1; $r <= 3; $r++ ) {
				$count = (int) $get( "row_{$r}" );
				if ( ! $count ) {
					continue;
				}
				$items = [];
				for ( $j = 0; $j < $count; $j++ ) {
					$items[] = [
						'title' => (string) $get( "row_{$r}_{$j}_title" ),
						'text'  => (string) $get( "row_{$r}_{$j}_text" ),
					];
				}
				$rows[] = $items;
			}
			return $base + [
				'eyebrow'  => (string) $get( 'heading_label' ),
				'title'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
				'rows'     => $rows,
			];

		case 'contacts':
			// Contact-form section. `form` is the legacy forminator
			// shortcode — we drop it in favour of the rehab/final-cta
			// REST-based form.
			return $base + [
				'title'    => (string) $get( 'heading_title' ),
				'subtitle' => (string) $get( 'heading_subtitle' ),
			];

		case 'pages':
			// Directory layout used by the all-treatments index. Each
			// chapter pairs a category link (serialized {title,url,target}
			// array) with an ordered list of child page IDs (also serialized).
			$chapter_count = (int) $get( 'chapters' );
			$chapters      = [];
			for ( $j = 0; $j < $chapter_count; $j++ ) {
				$title_raw    = maybe_unserialize( $get( "chapters_{$j}_title" ) );
				$page_ids_raw = maybe_unserialize( $get( "chapters_{$j}_page_ids" ) );
				$chapters[]   = [
					'title'    => is_array( $title_raw ) ? (string) ( $title_raw['title'] ?? '' ) : '',
					'url'      => is_array( $title_raw ) ? (string) ( $title_raw['url'] ?? '' ) : '',
					'page_ids' => is_array( $page_ids_raw ) ? array_values( array_map( 'intval', $page_ids_raw ) ) : [],
				];
			}
			return $base + [
				'title'    => (string) $get( 'heading_title' ),
				'chapters' => $chapters,
			];

		default:
			return $base + [ '_unknown' => true ];
	}
}
