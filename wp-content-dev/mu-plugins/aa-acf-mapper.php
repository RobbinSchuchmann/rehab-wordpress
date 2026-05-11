<?php
/**
 * ACF section → Gutenberg block mapper.
 *
 * Consumes the normalised sections produced by aa-acf-reader.php and emits
 * canonical block-comment serialised markup using the helpers in
 * aa-block-builders.php. The result is ready to drop into
 * `wp_posts.post_content`.
 *
 * Mapping (legacy → block):
 *   banner   → rehab/treatment-hero
 *   columns  → rehab/intro-doctor-card
 *   article  → rehab/article-row
 *   tabs     → rehab/treatment-phases
 *   faq      → rehab/faq          (faq_ids → cptIds attr)
 *   global   → recurse into the referenced global_section CPT
 *   cta      → rehab/final-cta
 *   _unknown → block-level HTML comment so the markup remains valid
 *              and the writer can spot what was skipped
 *
 * HTML inside ACF rich-text fields (`article.content`, `tabs[].text`) is
 * flattened to plain-text paragraphs — the article-row / treatment-phases
 * blocks render single-line paragraph stacks, so preserving inline links
 * is out of scope for this first pass.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map a list of normalised sections to concatenated block markup.
 *
 * @param array[] $sections Output of rehab_acf_get_sections().
 * @return string Block-serialised markup ready for post_content.
 */
function rehab_acf_map_sections( array $sections ): string {
	$out = '';
	foreach ( $sections as $section ) {
		$out .= rehab_acf_map_section( $section );
	}
	return $out;
}

/**
 * Same as rehab_acf_map_sections(), but threads three generic decoration
 * blocks through the output so migrated pages look like the design
 * rebuild:
 *
 *   …treatment-hero
 *   authority-ribbon            ← injected after the first banner
 *   …intro-doctor-card
 *   …article-row × N
 *   …treatment-phases
 *   …article-row × N
 *   benefits-numbered           ← injected just before the FAQ
 *   journey-steps               ← injected just before the FAQ
 *   …faq
 *   …final-cta
 *
 * If there's no FAQ in the section list, the mid-page chrome falls back
 * to "before the global/cta close-out" instead. Caller can disable any
 * injection point with $opts.
 *
 * @param array[] $sections Output of rehab_acf_get_sections().
 * @param array   $opts     { authority?: bool, benefits?: bool, journey?: bool } — default all true.
 */
function rehab_acf_map_sections_with_chrome( array $sections, array $opts = [] ): string {
	$opts = array_merge( [ 'authority' => true, 'benefits' => true, 'journey' => true ], $opts );

	// Locate the injection target for mid-page chrome: prefer the FAQ
	// section; fall back to the close-out section (global/cta).
	$mid_target_idx = null;
	foreach ( $sections as $i => $s ) {
		if ( 'faq' === ( $s['_layout'] ?? '' ) ) {
			$mid_target_idx = $i;
			break;
		}
	}
	if ( null === $mid_target_idx ) {
		foreach ( $sections as $i => $s ) {
			if ( in_array( $s['_layout'] ?? '', [ 'global', 'cta' ], true ) ) {
				$mid_target_idx = $i;
				break;
			}
		}
	}

	$out               = '';
	$authority_emitted = false;
	$mid_emitted       = false;

	foreach ( $sections as $i => $section ) {
		// Before this section: mid-page chrome if we're at the injection point.
		if ( null !== $mid_target_idx && $i === $mid_target_idx && ! $mid_emitted ) {
			if ( $opts['benefits'] ) {
				$out .= rehab_chrome_benefits_numbered();
			}
			if ( $opts['journey'] ) {
				$out .= rehab_chrome_journey_steps();
			}
			$mid_emitted = true;
		}

		$out .= rehab_acf_map_section( $section );

		// After this section: authority-ribbon directly after the hero.
		if ( $opts['authority'] && ! $authority_emitted && 'banner' === ( $section['_layout'] ?? '' ) ) {
			$out .= rehab_chrome_authority_ribbon();
			$authority_emitted = true;
		}
	}

	// Fallback: page had no FAQ/global/cta — append chrome at the end.
	if ( ! $mid_emitted ) {
		if ( $opts['benefits'] ) {
			$out .= rehab_chrome_benefits_numbered();
		}
		if ( $opts['journey'] ) {
			$out .= rehab_chrome_journey_steps();
		}
	}

	return $out;
}

/**
 * Map one section. Returns block markup or a placeholder comment.
 */
function rehab_acf_map_section( array $section ): string {
	$layout = $section['_layout'] ?? '';

	switch ( $layout ) {
		case 'banner':
			return rehab_acf_map_banner( $section );
		case 'columns':
			return rehab_acf_map_columns( $section );
		case 'article':
			return rehab_acf_map_article( $section );
		case 'tabs':
			return rehab_acf_map_tabs( $section );
		case 'faq':
			return rehab_acf_map_faq( $section );
		case 'cta':
			return rehab_acf_map_cta( $section );
		case 'global':
			return rehab_acf_map_global( $section );
		case 'pages':
			return rehab_acf_map_pages_index( $section );
		default:
			return "<!-- acf-mapper: skipped unsupported layout '" . esc_html( $layout ) . "' (section " . (int) ( $section['_idx'] ?? -1 ) . ") -->\n\n";
	}
}

/* --------------------------------------------------------------------------
 * Per-layout mappers.
 * -------------------------------------------------------------------------- */

function rehab_acf_map_banner( array $s ): string {
	$image = rehab_acf_image( (int) ( $s['image_id'] ?? 0 ) );
	return rehab_block_treatment_hero( [
		'eyebrow'  => '',
		'headline' => $s['title'] ?? '',
		'lede'     => $s['subtitle'] ?? '',
		'imageUrl' => $image['url'] ?? '',
		'imageAlt' => $image['alt'] ?? ( $s['title'] ?? '' ),
	] );
}

function rehab_acf_map_columns( array $s ): string {
	// Legacy columns layout drives the intro-doctor-card on treatment pages:
	// left column = heading text, right column = body copy. The doctor card
	// chrome is supplied by template defaults (name/phone in the builder
	// itself), so we don't try to mine them from ACF here.
	return rehab_block_intro_doctor_card( [
		'eyebrow' => $s['top_title'] ?? '',
		'heading' => $s['left_title'] ?? '',
		'body'    => rehab_acf_html_to_text( $s['right_subtitle'] ?? '' ),
	] );
}

function rehab_acf_map_article( array $s ): string {
	$image = rehab_acf_image( (int) ( $s['side_image_id'] ?? 0 ) );

	// Background mapping: legacy `bg-light-green` → our sage palette; an
	// explicit hex falls back to white (we don't translate arbitrary hex
	// values).
	$bg_class = (string) ( $s['bg_class'] ?? '' );
	$bg       = str_contains( $bg_class, 'bg-light-green' ) ? 'sage' : 'white';

	// Body is the long-form copy. The article-row helper splits on blank
	// lines and esc_html()s each chunk — so we strip HTML first to avoid
	// rendering literal `<p>` text.
	$body = rehab_acf_html_to_text( $s['content'] ?? '' );

	// If `content` was empty, the subtitle carries the prose instead (see
	// section 5 on cocaine).
	if ( '' === trim( $body ) ) {
		$body = rehab_acf_html_to_text( $s['subtitle'] ?? '' );
	}

	return rehab_block_article_row( [
		'background' => $bg,
		'imageSide'  => ( $s['reverse'] ?? false ) ? 'right' : 'left',
		'imageUrl'   => $image['url'] ?? '',
		'imageAlt'   => $image['alt'] ?? ( $s['title'] ?? '' ),
		'heading'    => $s['title'] ?? '',
		'body'       => $body,
	] );
}

function rehab_acf_map_tabs( array $s ): string {
	$phases = [];
	foreach ( ( $s['tabs'] ?? [] ) as $i => $tab ) {
		$plain = rehab_acf_html_to_text( $tab['text'] ?? '' );
		$paragraphs = preg_split( "/\n\s*\n/", trim( $plain ) );
		$paragraphs = array_values( array_filter( array_map( 'trim', $paragraphs ?: [] ) ) );

		// First paragraph often opens with a UPPERCASE banner repeating the
		// section heading — keep it as h3 only if it's short, else demote
		// to a leading paragraph.
		$h3 = '';
		if ( $paragraphs && mb_strlen( $paragraphs[0] ) < 80 && strtoupper( $paragraphs[0] ) === $paragraphs[0] ) {
			$h3 = ucfirst( strtolower( array_shift( $paragraphs ) ) );
		}

		$phases[] = [
			'phase'          => sprintf( 'PHASE %02d', $i + 1 ),
			'label'          => $tab['title'] ?? '',
			'h3'             => $h3,
			'paragraphs'     => $paragraphs,
			'listItems'      => [],
			'asideQuote'     => $s['pull_quote'] ?? '',
			'asideMetaLabel' => 'Quoted by',
			'asideMetaValue' => $s['cite'] ?? '',
		];
	}
	return rehab_block_treatment_phases(
		'',                       // eyebrow
		$s['title'] ?? '',        // heading
		'',                       // subheading
		$phases,
		'white'
	);
}

function rehab_acf_map_faq( array $s ): string {
	// Pass int IDs straight through — the builder resolves each via the FAQ
	// CPT and persists `cptIds` on the block attrs so render.php can
	// re-query them at request time.
	return rehab_block_faq(
		$s['title'] ?? 'Frequently Asked Questions',
		$s['faq_ids'] ?? [],
		'cream'
	);
}

function rehab_acf_map_cta( array $s ): string {
	// Globals like #70 supply the headline + sub; the rest of the final-cta
	// (phone/whatsapp/email/form copy) lives in the builder's defaults so
	// every page renders the same contact panel.
	return rehab_block_final_cta( [
		'heading' => $s['title'] ?? 'Are you ready to begin?',
		'lead'    => rehab_acf_html_to_text( $s['subtitle'] ?? '' ),
	] );
}

/**
 * Map the legacy `pages` layout used by the all-treatments index page.
 * Renders each chapter as a heading (linked to the category landing
 * page) followed by a bullet list of child treatment pages. Stale child
 * IDs are silently dropped — we only keep pages that still exist and
 * are published.
 */
function rehab_acf_map_pages_index( array $s ): string {
	$out = '';
	foreach ( ( $s['chapters'] ?? [] ) as $chapter ) {
		$title = trim( (string) ( $chapter['title'] ?? '' ) );
		$url   = trim( (string) ( $chapter['url'] ?? '' ) );
		$ids   = array_filter( array_map( 'intval', $chapter['page_ids'] ?? [] ) );

		if ( '' === $title && empty( $ids ) ) {
			continue;
		}

		// Chapter heading — linked when the chapter has its own landing page.
		$heading_inner = $url
			? '<a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a>'
			: esc_html( $title );
		$out .= "<!-- wp:heading {\"level\":2} -->\n";
		$out .= '<h2 class="wp-block-heading">' . $heading_inner . "</h2>\n";
		$out .= "<!-- /wp:heading -->\n\n";

		// Bullet list of child pages — skipping anything unpublished or deleted.
		$items_html = '';
		foreach ( $ids as $pid ) {
			$child = get_post( $pid );
			if ( ! $child || 'publish' !== $child->post_status ) {
				continue;
			}
			$perm = get_permalink( $pid );
			if ( ! $perm ) {
				continue;
			}
			$items_html .= "<!-- wp:list-item -->\n";
			$items_html .= '<li><a href="' . esc_url( $perm ) . '">' . esc_html( get_the_title( $pid ) ) . "</a></li>\n";
			$items_html .= "<!-- /wp:list-item -->\n";
		}
		if ( '' !== $items_html ) {
			$out .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">\n" . $items_html . "</ul>\n<!-- /wp:list -->\n\n";
		}
	}
	return $out;
}

function rehab_acf_map_global( array $s ): string {
	$global = rehab_acf_get_global_section( (int) ( $s['global_section_id'] ?? 0 ) );
	if ( ! $global ) {
		return "<!-- acf-mapper: global_section #" . (int) ( $s['global_section_id'] ?? 0 ) . " not found -->\n\n";
	}
	return rehab_acf_map_sections( $global['sections'] );
}

/* --------------------------------------------------------------------------
 * Helpers.
 * -------------------------------------------------------------------------- */

/**
 * Flatten ACF rich-text HTML to clean plain-text paragraphs separated by
 * blank lines — the format the article-row / treatment-phases builders
 * expect. Drops shortcodes and stray Windows line endings, collapses
 * runs of whitespace, and keeps paragraph breaks intact.
 */
function rehab_acf_html_to_text( string $html ): string {
	// Drop any shortcode-shaped fragment — strip_shortcodes() only catches
	// shortcodes actively registered at runtime, which leaves legacy ones
	// like [forminator_form …] embedded in copy.
	$html = preg_replace( '/\[\/?[a-z][a-z0-9_-]*\b[^\]]*\]/i', '', (string) $html );
	// Block tags → paragraph breaks (preserves perceived structure).
	$html = preg_replace( '#<\s*/?\s*(p|div|h[1-6]|li|br)\s*/?\s*>#i', "\n\n", $html );
	$text = wp_strip_all_tags( $html );
	$text = str_replace( "\r", '', $text );
	$text = preg_replace( "/\n{3,}/", "\n\n", $text );
	// Collapse intra-line whitespace runs but keep newlines.
	$text = preg_replace( '/[ \t]+/', ' ', $text );
	return trim( $text );
}
