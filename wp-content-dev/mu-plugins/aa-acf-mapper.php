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
		case 'gallery':
			return rehab_acf_map_gallery( $section );
		case 'cards':
			return rehab_acf_map_cards( $section );
		case 'cards-columns':
			return rehab_acf_map_cards_columns( $section );
		case 'message':
			return rehab_acf_map_message( $section );
		case 'map':
			return rehab_acf_map_map( $section );
		case 'steps':
			return rehab_acf_map_steps( $section );
		case 'contacts':
			return rehab_acf_map_contacts( $section );
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
	// The legacy `columns` layout is overloaded across three shapes,
	// distinguished by which column carries the heading and whether the
	// right column carries a role/credential label:
	//
	// (a) Team-profile shape — page is *about* a single staff member.
	//     Left column = portrait, right column = name (right_title) + bio
	//     (right_subtitle) + role label (right_label) + page-primary
	//     heading tag (right_tag h1/h2). The role label discriminates
	//     against location cards (Hua Hin has the same geometry, empty
	//     label). The h1/h2 tag discriminates against a few "how we
	//     work"-style subsections on treatment pages (e.g. /cost/ sec#6)
	//     that carry a section label but render the title as h3 — those
	//     should be image cards, not bios. Renders as intro-doctor-card
	//     with the portrait wired in and default chrome suppressed.
	//
	// (b) Generic image card — same column geometry as (a) but no role
	//     label. Used for location cards (Pa La-U waterfalls etc.) and
	//     in-page info sections that pair a photo with a heading and
	//     prose. Renders as article-row, which is the right block for
	//     image + heading + body without pretending to be a doctor card.
	//
	// (c) Treatment-page intro shape — left column carries the section
	//     heading, no image. Renders as intro-doctor-card with the
	//     writer's heading + body (chrome supplies the doctor portrait
	//     and contact info).
	$left_title     = trim( (string) ( $s['left_title'] ?? '' ) );
	$right_title    = trim( (string) ( $s['right_title'] ?? '' ) );
	$right_subtitle = (string) ( $s['right_subtitle'] ?? '' );
	$right_label    = trim( (string) ( $s['right_label'] ?? '' ) );
	$right_tag      = strtolower( trim( (string) ( $s['right_tag'] ?? '' ) ) );
	$left_image     = rehab_acf_image( (int) ( $s['left_image_id'] ?? 0 ) );
	$reversed       = ! empty( $s['reversed'] );

	$has_image_card_shape = ( '' === $left_title ) && ( null !== $left_image ) && ( '' !== $right_title );
	$title_is_page_heading = in_array( $right_tag, [ 'h1', 'h2' ], true );

	if ( $has_image_card_shape && '' !== $right_label && $title_is_page_heading ) {
		// (a) team profile
		return rehab_block_intro_doctor_card( [
			'eyebrow'        => (string) ( $s['top_title'] ?? '' ),
			'heading'        => $right_title,
			'body'           => rehab_acf_html_to_text( $right_subtitle ),
			'doctorImageUrl' => $left_image['url'],
			'doctorImageAlt' => $left_image['alt'] ?: $right_title,
			'doctorLabel'    => '',  // suppress the default "Speak with our Director"
			'doctorName'     => '',  // name is already the heading; no duplicate
		] );
	}

	if ( $has_image_card_shape ) {
		// (b) generic image card — location / info section with a photo
		return rehab_block_article_row( [
			'background' => 'white',
			'imageSide'  => $reversed ? 'right' : 'left',
			'imageUrl'   => $left_image['url'],
			'imageAlt'   => $left_image['alt'] ?: $right_title,
			'heading'    => $right_title,
			'body'       => rehab_acf_html_to_text( $right_subtitle ),
		] );
	}

	// (c) treatment-page intro shape
	return rehab_block_intro_doctor_card( [
		'eyebrow' => (string) ( $s['top_title'] ?? '' ),
		'heading' => $left_title,
		'body'    => rehab_acf_html_to_text( $right_subtitle ),
	] );
}

function rehab_acf_map_article( array $s ): string {
	$image = rehab_acf_image( (int) ( $s['side_image_id'] ?? 0 ) );

	// Background mapping: legacy `bg-light-green` → our sage palette; an
	// explicit hex falls back to white (we don't translate arbitrary hex
	// values).
	$bg_class = (string) ( $s['bg_class'] ?? '' );
	$bg       = str_contains( $bg_class, 'bg-light-green' ) ? 'sage' : 'white';

	$content = (string) ( $s['content'] ?? '' );
	if ( '' === trim( $content ) ) {
		$content = (string) ( $s['subtitle'] ?? '' );
	}

	// Legacy article `content` fields often contain inline <h2>–<h4>
	// banners (e.g. "THE BENEFITS OF INPATIENT HEROIN REHAB", or the
	// <h4> sub-questions on /stages-of-change-addiction/) that the
	// live site renders as separate sections. Split the content on those
	// boundaries so we can emit them as standalone heading blocks rather
	// than flattening them into the article-row body. Everything up to
	// the first inline heading stays inside the article-row's text
	// column — including any <ul> bullets, which surface as the row's
	// `listItems` and render as a checklist after the body paragraphs.
	$chunks = rehab_acf_split_html_into_blocks( $content );

	$first_body = '';
	$first_list = [];
	while ( ! empty( $chunks ) && 'heading' !== $chunks[0][0] ) {
		$chunk = array_shift( $chunks );
		if ( 'paragraphs' === $chunk[0] ) {
			$first_body .= ( '' === $first_body ? '' : "\n\n" ) . $chunk[1];
		} elseif ( 'list' === $chunk[0] ) {
			$first_list = array_merge( $first_list, $chunk[1] );
		}
	}

	$out = rehab_block_article_row( [
		'background' => $bg,
		'imageSide'  => ( $s['reverse'] ?? false ) ? 'right' : 'left',
		'imageUrl'   => $image['url'] ?? '',
		'imageAlt'   => $image['alt'] ?? ( $s['title'] ?? '' ),
		'heading'    => $s['title'] ?? '',
		'body'       => $first_body,
		'listItems'  => $first_list,
	] );

	foreach ( $chunks as $chunk ) {
		if ( 'heading' === $chunk[0] ) {
			$level = (int) ( $chunk[2] ?? 2 );
			$attrs = 2 === $level ? '' : '{"level":' . $level . '} ';
			$out  .= "<!-- wp:heading {$attrs}-->\n";
			$out  .= '<h' . $level . ' class="wp-block-heading">' . esc_html( $chunk[1] ) . '</h' . $level . ">\n";
			$out  .= "<!-- /wp:heading -->\n\n";
		} elseif ( 'list' === $chunk[0] ) {
			$out .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">\n";
			foreach ( $chunk[1] as $item ) {
				$out .= "<!-- wp:list-item -->\n<li>" . wp_kses( $item, [ 'strong' => [], 'em' => [], 'a' => [ 'href' => [], 'title' => [], 'target' => [], 'rel' => [] ] ] ) . "</li>\n<!-- /wp:list-item -->\n";
			}
			$out .= "</ul>\n<!-- /wp:list -->\n\n";
		} else {
			foreach ( preg_split( "/\n\s*\n/", trim( $chunk[1] ) ) as $para ) {
				$para = trim( $para );
				if ( '' === $para ) {
					continue;
				}
				$out .= "<!-- wp:paragraph -->\n<p>" . esc_html( $para ) . "</p>\n<!-- /wp:paragraph -->\n\n";
			}
		}
	}

	return $out;
}

/**
 * Split a rich-text HTML blob on structural boundaries: inline <h2>–<h4>
 * banners and <ul> lists. Other prose flattens to plain-text paragraph
 * runs.
 *
 * Returns a sequence of typed tuples in source order:
 *   [ 'paragraphs', "text\n\nmore text" ]
 *   [ 'heading',    "THE BENEFITS",  2 ]      // level captured from the source tag
 *   [ 'list',       [ "item one", "<strong>two</strong>" ] ]
 *
 * List items preserve inline <strong>/<em>/<a> (the treatment-phases and
 * article-row builders render these via wp_kses with the same allowlist);
 * heading inner-text is flattened to plain text.
 */
function rehab_acf_split_html_into_blocks( string $html ): array {
	$html = (string) $html;
	$out  = [];
	$pos  = 0;
	$len  = strlen( $html );

	$pattern = '#<\s*(h[2-4])[^>]*>(.*?)<\s*/\s*\1\s*>|<\s*ul[^>]*>(.*?)<\s*/\s*ul\s*>#is';

	while ( $pos < $len ) {
		if ( ! preg_match( $pattern, $html, $m, PREG_OFFSET_CAPTURE, $pos ) ) {
			$tail = rehab_acf_html_to_text( substr( $html, $pos ) );
			if ( '' !== $tail ) {
				$out[] = [ 'paragraphs', $tail ];
			}
			break;
		}

		$match_start = $m[0][1];
		$match_end   = $match_start + strlen( $m[0][0] );

		if ( $match_start > $pos ) {
			$prose = rehab_acf_html_to_text( substr( $html, $pos, $match_start - $pos ) );
			if ( '' !== $prose ) {
				$out[] = [ 'paragraphs', $prose ];
			}
		}

		if ( ! empty( $m[1][0] ) ) {
			$level   = (int) substr( $m[1][0], 1 );
			$heading = trim( wp_strip_all_tags( (string) $m[2][0] ) );
			if ( '' !== $heading ) {
				$out[] = [ 'heading', $heading, $level ];
			}
		} else {
			$items = rehab_acf_parse_list_items( (string) $m[3][0] );
			if ( ! empty( $items ) ) {
				$out[] = [ 'list', $items ];
			}
		}

		$pos = $match_end;
	}

	return $out;
}

/**
 * Extract <li> inner content from a <ul>'s inner HTML, preserving inline
 * emphasis and links. Items that flatten to empty strings (e.g. `<li>&nbsp;</li>`)
 * are dropped.
 */
function rehab_acf_parse_list_items( string $inner ): array {
	if ( ! preg_match_all( '#<\s*li[^>]*>(.*?)<\s*/\s*li\s*>#is', $inner, $matches ) ) {
		return [];
	}
	$allowed = [
		'strong' => [],
		'em'     => [],
		'a'      => [ 'href' => [], 'title' => [], 'target' => [], 'rel' => [] ],
	];
	$out = [];
	foreach ( $matches[1] as $raw ) {
		// Drop shortcodes the same way rehab_acf_html_to_text() does.
		$raw  = preg_replace( '/\[\/?[a-z][a-z0-9_-]*\b[^\]]*\]/i', '', $raw );
		$item = trim( wp_kses( $raw, $allowed ) );
		// Collapse whitespace runs but keep single spaces.
		$item = preg_replace( '/\s+/u', ' ', $item );
		// Skip nbsp-only / whitespace-only items.
		$plain = trim( wp_strip_all_tags( $item ) );
		$plain = str_replace( "\xc2\xa0", '', $plain );
		if ( '' === $plain ) {
			continue;
		}
		$out[] = $item;
	}
	return $out;
}

function rehab_acf_map_tabs( array $s ): string {
	$phases = [];
	foreach ( ( $s['tabs'] ?? [] ) as $i => $tab ) {
		$chunks = rehab_acf_split_html_into_blocks( (string) ( $tab['text'] ?? '' ) );

		// Tab text usually opens with an UPPERCASE <h2> banner that repeats
		// the tab title. Sentence-case it and use as the panel h3. Any later
		// inline heading is folded into the surrounding paragraph stream so
		// the panel stays a single coherent block.
		$h3         = '';
		$paragraphs = [];
		$list_items = [];
		foreach ( $chunks as $chunk ) {
			if ( 'heading' === $chunk[0] ) {
				if ( '' === $h3 ) {
					$h3 = ucfirst( strtolower( (string) $chunk[1] ) );
				} else {
					$paragraphs[] = (string) $chunk[1];
				}
			} elseif ( 'list' === $chunk[0] ) {
				$list_items = array_merge( $list_items, $chunk[1] );
			} elseif ( 'paragraphs' === $chunk[0] ) {
				foreach ( preg_split( "/\n\s*\n/", trim( $chunk[1] ) ) as $para ) {
					$para = trim( str_replace( "\xc2\xa0", '', (string) $para ) );
					if ( '' === $para ) {
						continue;
					}
					$paragraphs[] = $para;
				}
			}
		}

		// Fallback for legacy text that wasn't wrapped in <h2> but uppercased the banner inline.
		if ( '' === $h3 && $paragraphs && mb_strlen( $paragraphs[0] ) < 80 && strtoupper( $paragraphs[0] ) === $paragraphs[0] ) {
			$h3 = ucfirst( strtolower( array_shift( $paragraphs ) ) );
		}

		$phases[] = [
			'phase'          => sprintf( 'PHASE %02d', $i + 1 ),
			'label'          => $tab['title'] ?? '',
			'h3'             => $h3,
			'paragraphs'     => array_values( $paragraphs ),
			'listItems'      => $list_items,
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
	//
	// When `faq_ids` is empty, the legacy layout was a "show every FAQ"
	// index (the /faq/ landing page is the canonical example). Falls back
	// to a query of every published FAQ post, capped at 100.
	$ids = $s['faq_ids'] ?? [];
	if ( empty( $ids ) ) {
		$ids = get_posts( [
			'post_type'      => 'faq',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'fields'         => 'ids',
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		] );
	}
	return rehab_block_faq(
		$s['title'] ?? 'Frequently Asked Questions',
		$ids,
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
	// `generic_content` is the canonical body, but a lot of legacy
	// generic-layout pages (privacy / confidentiality / careers copy)
	// instead stuffed their entire body into `heading_subtitle` — fall
	// back to it when the explicit content field is empty.
	$html = trim( (string) ( $s['html'] ?? '' ) );
	if ( '' === $html ) {
		$html = trim( (string) ( $s['subtitle'] ?? '' ) );
	}
	if ( '' !== $html ) {
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

function rehab_acf_map_gallery( array $s ): string {
	$ids = [];
	foreach ( ( $s['items'] ?? [] ) as $item ) {
		$pid = (int) ( $item['photo_id'] ?? 0 );
		if ( $pid > 0 ) {
			$ids[] = $pid;
		}
	}
	if ( empty( $ids ) ) {
		return '';
	}

	$out = '';
	if ( '' !== ( $s['title'] ?? '' ) ) {
		$out .= "<!-- wp:heading {\"level\":2} -->\n";
		$out .= '<h2 class="wp-block-heading">' . esc_html( $s['title'] ) . "</h2>\n";
		$out .= "<!-- /wp:heading -->\n\n";
	}

	// core/gallery is the path of least resistance — universal Gutenberg
	// support, no custom block needed. innerBlocks are core/image.
	$inner = '';
	foreach ( $ids as $pid ) {
		$url = wp_get_attachment_image_url( $pid, 'large' );
		if ( ! $url ) {
			continue;
		}
		$alt = (string) get_post_meta( $pid, '_wp_attachment_image_alt', true );
		$inner .= "<!-- wp:image {\"id\":{$pid},\"sizeSlug\":\"large\"} -->\n";
		$inner .= '<figure class="wp-block-image size-large"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="wp-image-' . $pid . '"/></figure>' . "\n";
		$inner .= "<!-- /wp:image -->\n";
	}

	$attrs = wp_json_encode( [ 'columns' => 3, 'linkTo' => 'none' ] );
	$out  .= "<!-- wp:gallery {$attrs} -->\n";
	$out  .= '<figure class="wp-block-gallery has-nested-images columns-3">' . "\n";
	$out  .= $inner;
	$out  .= "</figure>\n";
	$out  .= "<!-- /wp:gallery -->\n\n";
	return $out;
}

function rehab_acf_map_cards( array $s ): string {
	$cards = [];
	foreach ( ( $s['cards'] ?? [] ) as $c ) {
		$img = rehab_acf_image( (int) ( $c['image_id'] ?? 0 ) );
		$cards[] = [
			'title'    => (string) ( $c['title'] ?? '' ),
			'href'     => (string) ( $c['url'] ?? '' ),
			'imageUrl' => $img['url'] ?? '',
			'imageAlt' => $img['alt'] ?? ( $c['title'] ?? '' ),
		];
	}
	return rehab_block_cards_grid(
		(string) ( $s['title'] ?? '' ),
		rehab_acf_html_to_text( $s['subtitle'] ?? '' ),
		$cards,
		3,        // columns
		'cream'   // background
	);
}

function rehab_acf_map_cards_columns( array $s ): string {
	// Two side-by-side "related posts" columns. Renders as a core/columns
	// block: each column has a heading, a bullet list of linked post
	// titles, and an optional "View all" cta. core/columns handles the
	// responsive 2-up grid for us.
	$render_col = static function ( string $heading, array $post_ids, string $link_title, string $link_url ): string {
		$items_html = '';
		foreach ( $post_ids as $pid ) {
			$p = get_post( $pid );
			if ( ! $p || 'publish' !== $p->post_status ) {
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
		$inner  = "<!-- wp:heading {\"level\":3} -->\n";
		$inner .= '<h3 class="wp-block-heading">' . esc_html( $heading ) . "</h3>\n";
		$inner .= "<!-- /wp:heading -->\n\n";
		if ( '' !== $items_html ) {
			$inner .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">\n" . $items_html . "</ul>\n<!-- /wp:list -->\n\n";
		}
		if ( '' !== $link_url ) {
			$label  = '' !== $link_title ? $link_title : 'View all';
			$inner .= "<!-- wp:paragraph -->\n";
			$inner .= '<p><a href="' . esc_url( $link_url ) . '">' . esc_html( $label ) . " →</a></p>\n";
			$inner .= "<!-- /wp:paragraph -->\n\n";
		}
		return $inner;
	};

	$out = '';
	if ( '' !== ( $s['title'] ?? '' ) ) {
		$out .= "<!-- wp:heading {\"level\":2} -->\n";
		$out .= '<h2 class="wp-block-heading">' . esc_html( $s['title'] ) . "</h2>\n";
		$out .= "<!-- /wp:heading -->\n\n";
	}
	$out .= "<!-- wp:columns -->\n<div class=\"wp-block-columns\">\n";
	$out .= "<!-- wp:column -->\n<div class=\"wp-block-column\">\n";
	$out .= $render_col(
		(string) ( $s['left_title'] ?? '' ),
		$s['left_post_ids'] ?? [],
		(string) ( $s['left_link_title'] ?? '' ),
		(string) ( $s['left_link_url'] ?? '' )
	);
	$out .= "</div>\n<!-- /wp:column -->\n\n";
	$out .= "<!-- wp:column -->\n<div class=\"wp-block-column\">\n";
	$out .= $render_col(
		(string) ( $s['right_title'] ?? '' ),
		$s['right_post_ids'] ?? [],
		(string) ( $s['right_link_title'] ?? '' ),
		(string) ( $s['right_link_url'] ?? '' )
	);
	$out .= "</div>\n<!-- /wp:column -->\n";
	$out .= "</div>\n<!-- /wp:columns -->\n\n";
	return $out;
}

function rehab_acf_map_message( array $s ): string {
	// Pull-quote with attribution. core/quote is universal and round-
	// trips cleanly; the legacy `photo` field is dropped (the quote is
	// the focus, the portrait was decorative).
	$quote = trim( (string) ( $s['quote'] ?? '' ) );
	if ( '' === $quote ) {
		return '';
	}
	$cite = trim( (string) ( $s['cite'] ?? '' ) );
	$out  = "<!-- wp:quote -->\n";
	$out .= '<blockquote class="wp-block-quote">';
	$out .= "<!-- wp:paragraph -->\n<p>" . esc_html( $quote ) . "</p>\n<!-- /wp:paragraph -->";
	if ( '' !== $cite ) {
		// cite may contain inline HTML (<span>CEO</span>); strip to plain.
		$out .= '<cite>' . esc_html( wp_strip_all_tags( $cite ) ) . '</cite>';
	}
	$out .= "</blockquote>\n<!-- /wp:quote -->\n\n";
	return $out;
}

function rehab_acf_map_map( array $s ): string {
	// rehab/map is a registered block; render the canonical markup so the
	// editor recognises it. Falls back to a plain address paragraph if
	// coords are missing.
	$lat     = (float) ( $s['lat'] ?? 0 );
	$lng     = (float) ( $s['lng'] ?? 0 );
	$address = (string) ( $s['address'] ?? '' );
	$title   = (string) ( $s['title'] ?? '' );

	$out = '';
	if ( '' !== $title ) {
		$out .= "<!-- wp:heading {\"level\":2} -->\n";
		$out .= '<h2 class="wp-block-heading">' . esc_html( $title ) . "</h2>\n";
		$out .= "<!-- /wp:heading -->\n\n";
	}

	if ( 0.0 === $lat && 0.0 === $lng ) {
		// No usable coords — just emit the address.
		if ( '' !== $address ) {
			$out .= "<!-- wp:paragraph -->\n<p>" . esc_html( $address ) . "</p>\n<!-- /wp:paragraph -->\n\n";
		}
		return $out;
	}

	$attrs = wp_json_encode( [ 'lat' => $lat, 'lng' => $lng, 'address' => $address, 'zoom' => 15 ] );
	$out  .= "<!-- wp:rehab/map {$attrs} -->\n";
	$out  .= '<section class="wp-block-rehab-map rehab-map"><div class="rehab-container"><div class="rehab-map__embed" data-lat="' . esc_attr( (string) $lat ) . '" data-lng="' . esc_attr( (string) $lng ) . '"></div>';
	if ( '' !== $address ) {
		$out .= '<p class="rehab-map__address">' . esc_html( $address ) . '</p>';
	}
	$out .= '</div></section>' . "\n";
	$out .= "<!-- /wp:rehab/map -->\n\n";
	return $out;
}

function rehab_acf_map_steps( array $s ): string {
	// Renders the heading once, then one cards-grid per legacy row.
	// Items per row vary (typically 4); column count tracks that.
	$rows = $s['rows'] ?? [];
	if ( empty( $rows ) ) {
		return '';
	}
	$out = '';
	if ( '' !== ( $s['title'] ?? '' ) ) {
		$out .= "<!-- wp:heading {\"level\":2} -->\n";
		$out .= '<h2 class="wp-block-heading">' . esc_html( $s['title'] ) . "</h2>\n";
		$out .= "<!-- /wp:heading -->\n\n";
	}
	foreach ( $rows as $row ) {
		$cards = [];
		foreach ( $row as $item ) {
			$cards[] = [
				'title' => wp_strip_all_tags( (string) ( $item['title'] ?? '' ) ),
				'body'  => rehab_acf_html_to_text( $item['text'] ?? '' ),
			];
		}
		$columns = min( 4, max( 1, count( $cards ) ) );
		$out    .= rehab_block_cards_grid( '', '', $cards, $columns, 'white' );
	}
	return $out;
}

function rehab_acf_map_contacts( array $s ): string {
	// Drop the legacy forminator shortcode in favour of the rehab/
	// final-cta block (our REST-based contact form).
	return rehab_block_final_cta( [
		'heading' => (string) ( $s['title'] ?? 'Get in touch' ),
		'lead'    => rehab_acf_html_to_text( $s['subtitle'] ?? '' ),
	] );
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
