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

	// Chrome is only appropriate for "treatment-page-shaped" content: a
	// banner section at the top and a faq/global/cta close-out. Single-
	// section bios, intake forms, and other one-off pages don't qualify
	// — appending generic benefits + journey copy there would be noise.
	$layouts            = array_map( fn( $s ) => $s['_layout'] ?? '', $sections );
	$has_banner         = in_array( 'banner', $layouts, true );
	$has_close_out      = (bool) array_intersect( $layouts, [ 'faq', 'global', 'cta' ] );
	$looks_like_treatment = $has_banner && $has_close_out;

	if ( ! $looks_like_treatment ) {
		return rehab_acf_map_sections( $sections );
	}

	// Locate the mid-page injection target: prefer FAQ, fall back to the
	// close-out section (global/cta).
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

		if ( $opts['authority'] && ! $authority_emitted && 'banner' === ( $section['_layout'] ?? '' ) ) {
			$out .= rehab_chrome_authority_ribbon();
			$authority_emitted = true;
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
		case 'generic':
			return rehab_acf_map_generic( $section );
		case 'hero':
			return rehab_acf_map_hero( $section );
		case 'team':
			return rehab_acf_map_team( $section );
		case 'moodboard':
			return rehab_acf_map_moodboard( $section );
		case 'features':
			return rehab_acf_map_features( $section );
		case 'logos':
			return rehab_acf_map_logos( $section );
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

function rehab_acf_map_generic( array $s ): string {
	$out = '';
	if ( '' !== ( $s['title'] ?? '' ) ) {
		$out .= "<!-- wp:heading {\"level\":2} -->\n";
		$out .= '<h2 class="wp-block-heading">' . esc_html( $s['title'] ) . "</h2>\n";
		$out .= "<!-- /wp:heading -->\n\n";
	}
	$html = trim( (string) ( $s['html'] ?? '' ) );
	if ( '' !== $html ) {
		// The legacy CMS stored sanitised HTML here, so we pass it through
		// to a wp:html block (which round-trips the raw markup verbatim).
		$out .= "<!-- wp:html -->\n" . $html . "\n<!-- /wp:html -->\n\n";
	}
	return $out;
}

function rehab_acf_map_hero( array $s ): string {
	$bg = rehab_acf_image( (int) ( $s['bg_image'] ?? 0 ) );
	// Reuse the treatment-hero block: the image goes on the side rather
	// than as a CSS background, which is the cleaner pattern in the new
	// design system.
	return rehab_block_treatment_hero( [
		'eyebrow'  => $s['eyebrow'] ?? '',
		'headline' => $s['title'] ?? '',
		'lede'     => $s['subtitle'] ?? '',
		'imageUrl' => $bg['url'] ?? '',
		'imageAlt' => $bg['alt'] ?? ( $s['title'] ?? '' ),
	] );
}

function rehab_acf_map_team( array $s ): string {
	$members = $s['members'] ?? [];
	if ( empty( $members ) ) {
		return '';
	}
	$heading    = trim( (string) ( $s['title'] ?? '' ) );
	$subheading = trim( (string) ( $s['subtitle'] ?? '' ) );

	$out  = "<!-- wp:rehab/team " . rehab_block_attrs( [ 'heading' => $heading, 'subheading' => $subheading ] ) . " -->\n";
	$out .= '<section class="wp-block-rehab-team rehab-team rehab-bg-white"><div class="rehab-container">';
	if ( '' !== $heading || '' !== $subheading ) {
		$out .= '<div class="rehab-team__head">';
		if ( '' !== $heading )    $out .= '<h2 class="rehab-team__heading">' . esc_html( $heading ) . '</h2>';
		if ( '' !== $subheading ) $out .= '<p class="rehab-team__sub">' . esc_html( $subheading ) . '</p>';
		$out .= '</div>';
	}
	$out .= '<div class="rehab-team__grid">';

	foreach ( $members as $m ) {
		$photo = rehab_acf_image( (int) ( $m['photo_id'] ?? 0 ) );
		$name  = (string) ( $m['name'] ?? '' );
		$role  = (string) ( $m['position'] ?? '' );
		$bio   = rehab_acf_html_to_text( $m['message'] ?? '' );

		$member_attrs = rehab_block_attrs( [
			'name'     => $name,
			'role'     => $role,
			'bio'      => $bio,
			'imageUrl' => $photo['url'] ?? '',
			'imageAlt' => $photo['alt'] ?? $name,
		] );
		$out .= "<!-- wp:rehab/team-member " . $member_attrs . " -->\n";
		$out .= '<article class="wp-block-rehab-team-member rehab-team-member">';
		if ( ! empty( $photo['url'] ) ) {
			$out .= '<div class="rehab-team-member__photo"><img src="' . esc_url( $photo['url'] ) . '" alt="' . esc_attr( $photo['alt'] ?? $name ) . '"/></div>';
		}
		$out .= '<div class="rehab-team-member__body">';
		if ( '' !== $name ) $out .= '<h3 class="rehab-team-member__name">' . esc_html( $name ) . '</h3>';
		if ( '' !== $role ) $out .= '<p class="rehab-team-member__role">' . esc_html( $role ) . '</p>';
		if ( '' !== $bio )  $out .= '<p class="rehab-team-member__bio">' . esc_html( $bio ) . '</p>';
		$out .= '</div></article>';
		$out .= "\n<!-- /wp:rehab/team-member -->\n";
	}

	$out .= '</div></div></section>';
	$out .= "\n<!-- /wp:rehab/team -->\n\n";
	return $out;
}

function rehab_acf_map_moodboard( array $s ): string {
	$image = rehab_acf_image( (int) ( $s['image_id'] ?? 0 ) );
	// Two-column with the image flipping side based on `reverse`.
	return rehab_block_article_row( [
		'background' => 'white',
		'imageSide'  => ( $s['reverse'] ?? false ) ? 'right' : 'left',
		'imageUrl'   => $image['url'] ?? '',
		'imageAlt'   => $image['alt'] ?? ( $s['title'] ?? '' ),
		'eyebrow'    => $s['eyebrow'] ?? '',
		'heading'    => $s['title'] ?? '',
		'body'       => rehab_acf_html_to_text( $s['subtitle'] ?? '' ),
	] );
}

function rehab_acf_map_features( array $s ): string {
	// Map the legacy 4-icon "features" section to the design system's
	// pillars block (also 3-4 numbered cards with bodies). Icon IDs are
	// dropped because the new block uses a single SVG shield icon.
	$items = [];
	foreach ( ( $s['features'] ?? [] ) as $i => $f ) {
		$items[] = [
			'num'   => sprintf( '%02d', $i + 1 ),
			'title' => (string) ( $f['title'] ?? '' ),
			'body'  => rehab_acf_html_to_text( $f['description'] ?? '' ),
		];
	}
	return rehab_block_pillars(
		(string) ( $s['eyebrow'] ?? '' ),
		(string) ( $s['title'] ?? '' ),
		rehab_acf_html_to_text( (string) ( $s['subtitle'] ?? '' ) ),
		$items,
		'sage-mist'
	);
}

function rehab_acf_map_logos( array $s ): string {
	$logos = [];
	foreach ( ( $s['logos'] ?? [] ) as $row ) {
		$img = rehab_acf_image( (int) ( $row['logo_id'] ?? 0 ) );
		if ( ! $img ) {
			continue;
		}
		$logos[] = [ 'url' => $img['url'], 'alt' => $img['alt'] ?: '' ];
	}
	if ( empty( $logos ) ) {
		return '';
	}
	return rehab_block_authority_ribbon(
		(string) ( $s['title'] ?? 'Featured on' ),
		$logos
	);
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
