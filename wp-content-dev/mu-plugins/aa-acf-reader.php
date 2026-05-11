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

		default:
			return $base + [ '_unknown' => true ];
	}
}
