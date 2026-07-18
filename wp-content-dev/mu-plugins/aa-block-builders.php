<?php
/**
 * Helpers to build canonical Gutenberg block markup for the rehab block library.
 *
 * Used by the rebuild-* one-shot tasks. Each helper returns a string of
 * `<!-- wp:rehab/X attrs --> ... <!-- /wp:rehab/X -->` markup matching
 * what the block's save.js renders so the page is parser-clean.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * JSON-encode block attrs with the encoding gotchas baked in.
 * (See memory: rehab-platform-current-state §JSON in block attrs.)
 */
function rehab_block_attrs( array $a ): string {
	return wp_json_encode( $a, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP );
}

/**
 * Build a `rehab/hero` block.
 *
 * Eyebrow renders as a `<p>` above the H1 (not a span inside it) so the H1
 * remains the headline alone — matters for SEO and a11y.
 */
function rehab_block_hero( array $a ): string {
	$defaults = [
		'eyebrow' => '', 'headline' => '', 'body' => '',
		'buttonText' => 'Speak with admissions', 'buttonUrl' => '/contact-us/',
		'buttonHelper' => 'Free, confidential, and no-obligation.',
		'trustItem1' => '24/7 medical supervision',
		'trustItem2' => 'Dual-diagnosis specialists',
		'trustItem3' => 'Lifetime aftercare guarantee',
		'imageUrl' => '', 'imageAlt' => '', 'showDeco' => true,
	];
	$a = array_merge( $defaults, $a );
	// Dynamic block: render.php produces the markup from these attributes.
	return "<!-- wp:rehab/hero " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Build a `rehab/prose` block.
 * $bg     = 'white' | 'cream' | 'sage-mist'.
 * $layout = 'stacked' (default) | 'split' (image left at ≥1024px) | 'split-reverse' (image right).
 *           Only takes effect when $img_url is set. Stacks on mobile/tablet either way.
 */
function rehab_block_prose( string $heading, array $paragraphs, array $list_items = [], string $img_url = '', string $img_alt = '', string $bg = 'white', string $layout = 'stacked' ): string {
	$attrs = rehab_block_attrs( [ 'background' => $bg, 'width' => 'text' ] );
	$inner = '';
	$gut   = '';
	if ( $heading !== '' ) {
		$inner .= '<h2 class="wp-block-heading">' . esc_html( $heading ) . '</h2>';
		$gut   .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html( $heading ) . "</h2>\n<!-- /wp:heading -->\n\n";
	}
	if ( $img_url !== '' ) {
		$figure = '<figure class="wp-block-image size-large"><img src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $img_alt ) . '"/></figure>';
		$inner .= $figure;
		$gut   .= "<!-- wp:image {\"sizeSlug\":\"large\"} -->\n" . $figure . "\n<!-- /wp:image -->\n\n";
	}
	foreach ( $paragraphs as $p ) {
		$inner .= '<p>' . esc_html( $p ) . '</p>';
		$gut   .= "<!-- wp:paragraph -->\n<p>" . esc_html( $p ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
	if ( ! empty( $list_items ) ) {
		$inner .= '<ul class="wp-block-list">';
		$gut   .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
		foreach ( $list_items as $li ) {
			$inner .= '<li>' . esc_html( $li ) . '</li>';
			$gut   .= "<!-- wp:list-item -->\n<li>" . esc_html( $li ) . "</li>\n<!-- /wp:list-item -->\n";
		}
		$inner .= '</ul>';
		$gut   .= "</ul>\n<!-- /wp:list -->\n\n";
	}
	$class = 'wp-block-rehab-prose rehab-prose rehab-bg-' . $bg . ' rehab-prose--text';
	if ( $img_url !== '' && in_array( $layout, [ 'split', 'split-reverse' ], true ) ) {
		$class .= ' rehab-prose--' . $layout;
	}
	return "<!-- wp:rehab/prose " . $attrs . " -->\n" .
		'<section class="' . esc_attr( $class ) . '"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner">' . $gut . '</div></div></section>' .
		"\n<!-- /wp:rehab/prose -->\n\n";
}

/**
 * Build a `rehab/cards-grid` block with N cards.
 * $cards = array of [ 'title', 'description', 'imageUrl', 'imageAlt', 'url' ].
 */
function rehab_block_cards_grid( string $heading, string $subheading, array $cards, int $columns = 3, string $bg = 'cream' ): string {
	$grid_attrs = rehab_block_attrs( [
		'background' => $bg, 'columns' => $columns, 'cardLayout' => 'vertical',
		'heading' => $heading, 'subheading' => $subheading,
	] );
	// Dynamic blocks: parent + child render from attributes (cards-grid/render.php,
	// card/render.php). Emit only the comment delimiters — no HTML body.
	$out = "<!-- wp:rehab/cards-grid " . $grid_attrs . " -->\n";
	foreach ( $cards as $card ) {
		if ( ! isset( $card['description'] ) && isset( $card['body'] ) ) {
			$card['description'] = $card['body'];
		}
		unset( $card['body'] );
		$out .= "<!-- wp:rehab/card " . rehab_block_attrs( $card ) . " /-->\n";
	}
	$out .= "<!-- /wp:rehab/cards-grid -->\n\n";
	return $out;
}

/**
 * Build a `rehab/faq` block.
 *
 * Each $items element is either:
 *   - [ 'question' => '...', 'answer' => '...' ]   — inline content
 *   - [ 'cptId' => 32 ]                            — pull from FAQ CPT post by ID
 *   - 32 (raw int)                                 — same, shorthand
 *
 * cptId-based items resolve to the FAQ post's title (question) and content
 * (answer) at migration time, so the resulting block is fully static — the
 * editor remains a normal editing experience and the FAQ CPT records remain
 * the canonical source.
 */
function rehab_block_faq( string $heading, array $items, string $bg = 'cream' ): string {
	$resolved   = [];
	$source_ids = [];
	foreach ( $items as $item ) {
		// int → cptId shorthand
		if ( is_int( $item ) ) $item = [ 'cptId' => $item ];
		// resolve cptId → question/answer
		if ( ! empty( $item['cptId'] ) ) {
			$post = get_post( (int) $item['cptId'] );
			if ( ! $post || $post->post_type !== 'faq' || $post->post_status !== 'publish' ) continue;
			$source_ids[] = (int) $post->ID;
			$resolved[] = [
				'question' => $post->post_title,
				'answer'   => trim( wp_strip_all_tags( $post->post_content ) ),
			];
			continue;
		}
		if ( ! empty( $item['question'] ) ) $resolved[] = $item;
	}
	// Persist cptIds on the block attrs so render.php can re-resolve at
	// request time — keeps the CPT as the canonical source.
	$block_attrs = [ 'background' => $bg, 'heading' => $heading ];
	if ( ! empty( $source_ids ) ) $block_attrs['cptIds'] = $source_ids;
	$faq_attrs = rehab_block_attrs( $block_attrs );

	$h  = '<section class="wp-block-rehab-faq rehab-faq rehab-bg-' . esc_attr( $bg ) . '">';
	$h .= '<div class="rehab-container rehab-container--narrow"><h2 class="rehab-faq__heading">' . esc_html( $heading ) . '</h2>';
	$h .= '<div class="rehab-faq__list">';
	foreach ( $resolved as $item ) {
		$ia = rehab_block_attrs( $item );
		$item_html = '<details class="rehab-faq-item"><summary class="rehab-faq-item__summary"><span>' . esc_html( $item['question'] ) . '</span><span class="rehab-faq-item__icon" aria-hidden="true"></span></summary><div class="rehab-faq-item__answer"><p>' . esc_html( $item['answer'] ) . '</p></div></details>';
		$h .= "<!-- wp:rehab/faq-item " . $ia . " -->\n" . $item_html . "\n<!-- /wp:rehab/faq-item -->\n";
	}
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/faq " . $faq_attrs . " -->\n" . $h . "\n<!-- /wp:rehab/faq -->\n\n";
}

/**
 * Build a `rehab/cta` block.
 */
function rehab_block_cta( array $a ): string {
	$defaults = [
		'variant' => 'default', 'background' => 'sage-mist',
		'heading' => 'Ready to take the next step?',
		'body' => 'Reach out for a confidential call from our admissions team.',
		'buttonText' => 'Speak with admissions', 'buttonUrl' => '/contact-us/',
		'helper' => 'Free, confidential, no-obligation.',
	];
	$a = array_merge( $defaults, $a );
	$attrs = rehab_block_attrs( $a );
	$class = 'wp-block-rehab-cta rehab-cta rehab-cta--' . $a['variant'] . ' rehab-bg-' . $a['background'];
	$h = '<section class="' . esc_attr( $class ) . '" aria-label="Call to action"><div class="rehab-container rehab-container--narrow"><div class="rehab-cta__inner"><h2 class="rehab-heading rehab-heading--lg">' . esc_html( $a['heading'] ) . '</h2><p class="rehab-cta__body">' . esc_html( $a['body'] ) . '</p><a class="rehab-btn rehab-btn--luxury" href="' . esc_url( $a['buttonUrl'] ) . '">' . esc_html( $a['buttonText'] ) . '</a><p class="rehab-cta__helper">' . esc_html( $a['helper'] ) . '</p></div></div></section>';
	return "<!-- wp:rehab/cta " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/cta -->\n\n";
}


/**
 * Build a `rehab/treatment-hero` block.
 */
function rehab_block_treatment_hero( array $a ): string {
	$defaults = [
		'eyebrow' => '', 'headline' => '', 'lede' => '',
		'primaryText' => 'Schedule a free assessment', 'primaryUrl' => '#assessment',
		'secondaryText' => 'Explore the program', 'secondaryUrl' => '#program',
		'helper' => 'Free, confidential, no-obligation · Replies within 1 hour',
		'stat1Num' => '12', 'stat1Label' => 'Maximum clients on site at any time',
		'stat2Num' => '24/7', 'stat2Label' => 'Doctor & clinical team availability',
		'stat3Num' => '12+', 'stat3Label' => 'Years treating cocaine addiction',
		'imageUrl' => '', 'imageAlt' => '',
		'badgeImageUrl' => '', 'badgeTitle' => 'Thai-licensed facility',
		'badgeText' => 'Ministry of Public Health · Hospital-affiliated detox',
	];
	$a = array_merge( $defaults, $a );
	$attrs = rehab_block_attrs( $a );
	$h  = '<section class="wp-block-rehab-treatment-hero rehab-treatment-hero">';
	$h .= '<div class="rehab-container"><div class="rehab-treatment-hero__grid">';
	$h .= '<div>';
	if ( $a['eyebrow'] ) $h .= '<p class="rehab-treatment-hero__eyebrow"><span class="diamond" aria-hidden="true">◆</span>' . esc_html( $a['eyebrow'] ) . '</p>';
	$h .= '<h1 class="rehab-treatment-hero__h1">' . esc_html( $a['headline'] ) . '</h1>';
	if ( $a['lede'] ) $h .= '<p class="rehab-treatment-hero__lede">' . esc_html( $a['lede'] ) . '</p>';
	$h .= '<div class="rehab-treatment-hero__cta-row">';
	$h .= '<a href="' . esc_url( $a['primaryUrl'] ) . '" class="rehab-btn rehab-btn--luxury">' . esc_html( $a['primaryText'] ) . '</a>';
	$h .= '<a href="' . esc_url( $a['secondaryUrl'] ) . '" class="rehab-btn rehab-btn--outline">' . esc_html( $a['secondaryText'] ) . '</a>';
	$h .= '</div>';
	if ( $a['helper'] ) $h .= '<p class="rehab-treatment-hero__helper"><span class="dot" aria-hidden="true"></span>' . esc_html( $a['helper'] ) . '</p>';
	$h .= '<div class="rehab-treatment-hero__trust">';
	for ( $i = 1; $i <= 3; $i++ ) {
		$h .= '<div class="rehab-treatment-hero__trust-item"><div class="num">' . esc_html( $a[ "stat{$i}Num" ] ) . '</div><div class="lbl">' . esc_html( $a[ "stat{$i}Label" ] ) . '</div></div>';
	}
	$h .= '</div></div>';
	$h .= '<div class="rehab-treatment-hero__media"><div class="rehab-treatment-hero__image-wrap">';
	if ( $a['imageUrl'] ) $h .= '<img src="' . esc_url( $a['imageUrl'] ) . '" alt="' . esc_attr( $a['imageAlt'] ) . '"/>';
	$h .= '<div class="rehab-treatment-hero__image-overlay" aria-hidden="true"></div></div>';
	if ( $a['badgeTitle'] || $a['badgeImageUrl'] ) {
		$h .= '<div class="rehab-treatment-hero__badge">';
		if ( $a['badgeImageUrl'] ) $h .= '<img src="' . esc_url( $a['badgeImageUrl'] ) . '" alt=""/>';
		$h .= '<div class="rehab-treatment-hero__badge-text"><strong>' . esc_html( $a['badgeTitle'] ) . '</strong>' . esc_html( $a['badgeText'] ) . '</div>';
		$h .= '</div>';
	}
	$h .= '</div></div></div></section>';
	return "<!-- wp:rehab/treatment-hero " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/treatment-hero -->\n\n";
}

/**
 * Build a `rehab/authority-ribbon` block.
 * $logos = array of [ 'url', 'alt' ]
 */
function rehab_block_authority_ribbon( string $label, array $logos ): string {
	$attrs = rehab_block_attrs( [ 'label' => $label, 'logos' => $logos ] );
	// Class must byte-match the block's JS save() output (`rehab-authority-ribbon`,
	// no `wp-block-` prefix) or the static block fails editor validation (REH-85).
	$h  = '<section class="rehab-authority-ribbon"><div class="rehab-container">';
	$h .= '<p class="rehab-authority-ribbon__label">' . esc_html( $label ) . '</p>';
	$h .= '<div class="rehab-authority-ribbon__logos">';
	foreach ( $logos as $logo ) {
		$img = '<img src="' . esc_url( $logo['url'] ) . '" alt="' . esc_attr( $logo['alt'] ?? '' ) . '"/>';
		// Wrap in a data-tooltip host when a tip is set (CSS-only hover popover,
		// same pattern as the homepage press strip — REH-85).
		if ( ! empty( $logo['tip'] ) ) {
			$h .= '<span class="rehab-authority-ribbon__item" data-tooltip="' . esc_attr( $logo['tip'] ) . '">' . $img . '</span>';
		} else {
			$h .= $img;
		}
	}
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/authority-ribbon " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/authority-ribbon -->\n\n";
}

/**
 * Canonical "As featured in" press-logo set (with hover tooltips) for the
 * under-hero authority ribbon. Single source of truth shared by the
 * treatment-v3 builder and the cost rebuild (REH-93) — edit here, then
 * re-run the page rebuild oneshots to re-bake stored content.
 * URLs use the Media Library's stable /wp-content/uploads/brand/ path
 * (identical dev↔prod, editorially manageable — REH-66/REH-94).
 */
function rehab_press_ribbon_logos(): array {
	return [
		[ 'url' => '/wp-content/uploads/brand/business-insider.png', 'alt' => 'Business Insider', 'tip' => 'Business Insider featured insights from The Diamond Rehab Thailand experts on the vital connection between environment and long-term recovery success.' ],
		[ 'url' => '/wp-content/uploads/brand/yahoo-finance.png', 'alt' => 'Yahoo Finance', 'tip' => 'Yahoo Finance recognized The Diamond Rehab Thailand as a global leader for its unique fusion of luxury hospitality and rigorous Western clinical standards.' ],
		[ 'url' => '/wp-content/uploads/brand/well-good.png', 'alt' => 'Well + Good', 'tip' => 'Well+Good recognized The Diamond Rehab Thailand for its holistic, high-end approach to restoring physical, emotional, and mental balance in a tropical setting.' ],
		[ 'url' => '/wp-content/uploads/brand/psych-central.png', 'alt' => 'Psych Central', 'tip' => 'PsychCentral acknowledged The Diamond Rehab Thailand for its pioneering integration of evidence-based medical therapy and holistic mindfulness meditation.' ],
		[ 'url' => '/wp-content/uploads/brand/recovery-com.webp', 'alt' => 'Recovery.com', 'tip' => 'Recovery.com lists The Diamond Rehab Thailand among its recommended international centers for luxury residential addiction treatment.' ],
		[ 'url' => '/wp-content/uploads/brand/bangkok-hospital.png', 'alt' => 'Bangkok Hospital partner', 'tip' => 'The Diamond Rehab Thailand is partnered with Bangkok Hospital for comprehensive medical support and 24/7 emergency care for all residential clients.' ],
	];
}

/**
 * Build a `rehab/pillars` block.
 * $items = [ ['num','title','body'], ... ]
 */
function rehab_block_pillars( string $eyebrow, string $heading, string $subheading, array $items, string $bg = 'sage-mist' ): string {
	$attrs = rehab_block_attrs( [ 'background' => $bg, 'eyebrow' => $eyebrow, 'heading' => $heading, 'subheading' => $subheading, 'items' => $items ] );
	$h  = '<section class="wp-block-rehab-pillars rehab-pillars rehab-bg-' . esc_attr( $bg ) . '"><div class="rehab-container">';
	$h .= '<div class="rehab-pillars__head"><span class="rehab-pillars__eyebrow">' . esc_html( $eyebrow ) . '</span>';
	$h .= '<h2 class="rehab-pillars__heading">' . esc_html( $heading ) . '</h2>';
	if ( $subheading ) $h .= '<p class="rehab-pillars__sub">' . esc_html( $subheading ) . '</p>';
	$h .= '</div><div class="rehab-pillars__grid">';
	foreach ( $items as $item ) {
		$h .= '<div class="rehab-pillar">';
		$h .= '<div class="rehab-pillar__icon" aria-hidden="true"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>';
		$h .= '<span class="rehab-pillar__num">' . esc_html( $item['num'] ) . '</span>';
		$h .= '<h3 class="rehab-pillar__title">' . esc_html( $item['title'] ) . '</h3>';
		$h .= '<p class="rehab-pillar__body">' . esc_html( $item['body'] ) . '</p>';
		$h .= '</div>';
	}
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/pillars " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/pillars -->\n\n";
}

/**
 * Build a `rehab/signs-grid` block.
 */
function rehab_block_signs_grid( array $a ): string {
	$defaults = [
		'background' => 'cream', 'eyebrow' => '', 'heading' => '', 'subheading' => '',
		'card1Title' => '', 'card1Items' => [],
		'card2Title' => '', 'card2Items' => [],
		'showCta' => true, 'ctaTitle' => '', 'ctaBody' => '',
		'ctaButton' => 'Book free assessment', 'ctaUrl' => '/contact-us/',
	];
	$a = array_merge( $defaults, $a );
	$attrs = rehab_block_attrs( $a );
	$check = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>';
	$icon1 = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>';
	$icon2 = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12h4l3-9 4 18 3-9h4"/></svg>';
	$render_card = function ( $title, $icon, $items ) use ( $check ) {
		$html  = '<div class="rehab-signs-card">';
		$html .= '<div class="rehab-signs-card__head"><div class="rehab-signs-card__icon" aria-hidden="true">' . $icon . '</div>';
		$html .= '<h3 class="rehab-signs-card__title">' . esc_html( $title ) . '</h3></div>';
		$html .= '<ul class="rehab-signs-card__list">';
		foreach ( $items as $item ) $html .= '<li>' . $check . esc_html( $item ) . '</li>';
		$html .= '</ul></div>';
		return $html;
	};
	$h  = '<section class="wp-block-rehab-signs-grid rehab-signs-grid rehab-bg-' . esc_attr( $a['background'] ) . '"><div class="rehab-container">';
	$h .= '<div class="rehab-signs-grid__head">';
	if ( $a['eyebrow'] )    $h .= '<span class="rehab-signs-grid__eyebrow">' . esc_html( $a['eyebrow'] ) . '</span>';
	$h .= '<h2 class="rehab-signs-grid__heading">' . esc_html( $a['heading'] ) . '</h2>';
	if ( $a['subheading'] ) $h .= '<p class="rehab-signs-grid__sub">' . esc_html( $a['subheading'] ) . '</p>';
	$h .= '</div><div class="rehab-signs-grid__cards">';
	$h .= $render_card( $a['card1Title'], $icon1, $a['card1Items'] );
	$h .= $render_card( $a['card2Title'], $icon2, $a['card2Items'] );
	$h .= '</div>';
	if ( $a['showCta'] ) {
		$h .= '<div class="rehab-signs-grid__cta">';
		$h .= '<div class="rehab-signs-grid__cta-text"><h3>' . esc_html( $a['ctaTitle'] ) . '</h3><p>' . esc_html( $a['ctaBody'] ) . '</p></div>';
		$h .= '<a href="' . esc_url( $a['ctaUrl'] ) . '" class="rehab-btn rehab-btn--luxury">' . esc_html( $a['ctaButton'] ) . '</a>';
		$h .= '</div>';
	}
	$h .= '</div></section>';
	return "<!-- wp:rehab/signs-grid " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/signs-grid -->\n\n";
}

/**
 * Build a `rehab/journey-steps` block.
 * $items = [ ['label','title','body'], ... ]
 */
function rehab_block_journey_steps( string $eyebrow, string $heading, string $subheading, array $items, string $bg = 'sage-mist' ): string {
	$attrs = rehab_block_attrs( [ 'background' => $bg, 'eyebrow' => $eyebrow, 'heading' => $heading, 'subheading' => $subheading, 'items' => $items ] );
	$h  = '<section class="wp-block-rehab-journey-steps rehab-journey-steps rehab-bg-' . esc_attr( $bg ) . '"><div class="rehab-container">';
	$h .= '<div class="rehab-journey-steps__head">';
	if ( $eyebrow )    $h .= '<span class="rehab-journey-steps__eyebrow">' . esc_html( $eyebrow ) . '</span>';
	$h .= '<h2 class="rehab-journey-steps__heading">' . esc_html( $heading ) . '</h2>';
	if ( $subheading ) $h .= '<p class="rehab-journey-steps__sub">' . esc_html( $subheading ) . '</p>';
	$h .= '</div><div class="rehab-journey-steps__grid">';
	foreach ( $items as $i => $item ) {
		$label = ! empty( $item['label'] ) ? $item['label'] : ( 'STEP ' . str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) );
		$h .= '<div class="rehab-journey-step">';
		$h .= '<span class="rehab-journey-step__num">' . esc_html( $label ) . '</span>';
		$h .= '<h3 class="rehab-journey-step__title">' . esc_html( $item['title'] ) . '</h3>';
		$h .= '<p class="rehab-journey-step__body">' . esc_html( $item['body'] ) . '</p>';
		$h .= '</div>';
	}
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/journey-steps " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/journey-steps -->\n\n";
}

/**
 * Build a `rehab/benefits-numbered` block.
 * $items = [ ['title','body'], ... ]
 */
function rehab_block_benefits_numbered( array $items ): string {
	$attrs = rehab_block_attrs( [ 'items' => $items ] );
	$h  = '<div class="wp-block-rehab-benefits-numbered rehab-benefits-numbered">';
	foreach ( $items as $i => $item ) {
		$h .= '<div class="rehab-benefit">';
		$h .= '<div class="rehab-benefit__num">' . str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) . '</div>';
		$h .= '<div class="rehab-benefit__body"><h3>' . esc_html( $item['title'] ) . '</h3><p>' . esc_html( $item['body'] ) . '</p></div>';
		$h .= '</div>';
	}
	$h .= '</div>';
	return "<!-- wp:rehab/benefits-numbered " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/benefits-numbered -->\n\n";
}


/**
 * Build a `rehab/intro-doctor-card` block.
 */
function rehab_block_intro_doctor_card( array $a ): string {
	$defaults = [
		'background' => 'white', 'eyebrow' => '', 'heading' => '', 'body' => '',
		'doctorImageUrl' => '', 'doctorImageAlt' => '',
		'doctorLabel' => 'Speak with our Director', 'doctorName' => '',
		'doctorPhone' => '', 'doctorPhoneHref' => '',
	];
	// Convert plain \n\n-separated body to <p>-wrapped HTML so RichText
	// (multiline="p") in the editor can parse + edit it.
	$paragraphs = array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", trim( (string) $a['body'] ) ) ) );
	$body_html  = implode( '', array_map( fn( $p ) => '<p>' . esc_html( $p ) . '</p>', $paragraphs ) );
	$a['body']  = $body_html;
	$a = array_merge( $defaults, $a );
	$attrs = rehab_block_attrs( $a );
	$phone_svg = '<svg width="14" height="14" viewBox="0 0 15 16" aria-hidden="true"><path d="M14.8 13.1c-.8 2-2.8 2.4-3.5 2.4-.2 0-3.2.2-7.4-3.9C.5 8.3 0 4.8 0 4.1 0 3.4.2 1.8 2.4.6c.3-.2.8-.2 1-.1.1.1 1.9 3.2 2 3.3 0 .1.1.2.1.3 0 .1-.1.3-.3.5l-.7.7c-.3.1-.5.3-.7.5-.2.2-.3.4-.3.5 0 .3.3 1.5 2.3 3.3C7.9 11.5 8.9 12 9 12c.1 0 .2.1.2.1.1 0 .3-.1.5-.3.2-.2.9-1.1 1.1-1.3.2-.2.4-.3.5-.3.1 0 .2 0 .3.1.1 0 3.2 1.9 3.3 1.9.2.1.1.6-.1.9" fill="currentColor"/></svg>';
	$h  = '<section class="wp-block-rehab-intro-doctor-card rehab-intro-doctor-card rehab-bg-' . esc_attr( $a['background'] ) . '"><div class="rehab-container">';
	$h .= '<div class="rehab-intro-doctor-card__grid">';
	$h .= '<div>';
	if ( $a['eyebrow'] ) $h .= '<span class="rehab-intro-doctor-card__eyebrow">' . esc_html( $a['eyebrow'] ) . '</span>';
	$h .= '<h2 class="rehab-intro-doctor-card__heading">' . esc_html( $a['heading'] ) . '</h2>';
	if ( $a['doctorName'] || $a['doctorImageUrl'] ) {
		$h .= '<div class="rehab-doctor-card">';
		$h .= '<div class="rehab-doctor-card__avatar">';
		if ( $a['doctorImageUrl'] ) $h .= '<img src="' . esc_url( $a['doctorImageUrl'] ) . '" alt="' . esc_attr( $a['doctorImageAlt'] ) . '"/>';
		$h .= '</div><div>';
		if ( $a['doctorLabel'] ) $h .= '<div class="rehab-doctor-card__label">' . esc_html( $a['doctorLabel'] ) . '</div>';
		$h .= '<p class="rehab-doctor-card__name">' . esc_html( $a['doctorName'] ) . '</p>';
		if ( $a['doctorPhone'] ) {
			$href = $a['doctorPhoneHref'] !== '' ? $a['doctorPhoneHref'] : 'tel:' . preg_replace( '/[^+\d]/', '', $a['doctorPhone'] );
			$h .= '<a href="' . esc_url( $href ) . '" class="rehab-doctor-card__phone">' . $phone_svg . esc_html( $a['doctorPhone'] ) . '</a>';
		}
		$h .= '</div></div>';
	}
	$h .= '</div>';
	$h .= '<div class="rehab-intro-doctor-card__copy">' . $body_html . '</div>';
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/intro-doctor-card " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/intro-doctor-card -->\n\n";
}

/**
 * Build a `rehab/article-row` block.
 */
function rehab_block_article_row( array $a ): string {
	$defaults = [
		'background' => 'white', 'imageSide' => 'left', 'imageAspect' => 'tall',
		'imageUrl' => '', 'imageAlt' => '',
		'eyebrow' => '', 'heading' => '', 'body' => '',
		'listItems' => [],
		'primaryText' => '', 'primaryUrl' => '',
		'secondaryText' => '', 'secondaryUrl' => '',
	];
	// Convert plain \n\n-separated body to <p>-wrapped HTML so RichText
	// (multiline="p") in the editor can parse + edit it.
	$paragraphs = array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", trim( (string) $a['body'] ) ) ) );
	$body_html  = implode( '', array_map( fn( $p ) => '<p>' . esc_html( $p ) . '</p>', $paragraphs ) );
	$a['body']  = $body_html;
	$a = array_merge( $defaults, $a );
	$attrs = rehab_block_attrs( $a );
	$reverse_class = $a['imageSide'] === 'right' ? ' rehab-article-row--reverse' : '';
	$aspect_class  = $a['imageAspect'] === 'wide' ? ' rehab-article-row__media--wide' : '';
	$h  = '<section class="wp-block-rehab-article-row rehab-article-row-section rehab-bg-' . esc_attr( $a['background'] ) . '"><div class="rehab-container">';
	$h .= '<div class="rehab-article-row' . $reverse_class . '">';
	$h .= '<div class="rehab-article-row__media' . $aspect_class . '">';
	if ( $a['imageUrl'] ) {
		$h .= '<img src="' . esc_url( $a['imageUrl'] ) . '" alt="' . esc_attr( $a['imageAlt'] ) . '"/>';
	} else {
		$h .= '<div class="rehab-article-row__media-placeholder"><span>' . esc_html( $a['imageAlt'] ?: 'Image' ) . '</span></div>';
	}
	$h .= '</div>';
	$h .= '<div class="rehab-article-row__text">';
	if ( $a['eyebrow'] ) $h .= '<span class="rehab-article-row__eyebrow">' . esc_html( $a['eyebrow'] ) . '</span>';
	$h .= '<h2 class="rehab-article-row__heading">' . esc_html( $a['heading'] ) . '</h2>';
	$h .= $body_html;
	if ( ! empty( $a['listItems'] ) ) {
		$h .= '<ul class="rehab-article-row__list">';
		foreach ( $a['listItems'] as $li ) {
			$h .= '<li>' . wp_kses( $li, [ 'strong' => [], 'em' => [], 'a' => [ 'href' => [], 'title' => [], 'target' => [], 'rel' => [] ] ] ) . '</li>';
		}
		$h .= '</ul>';
	}
	if ( $a['primaryText'] || $a['secondaryText'] ) {
		$h .= '<div class="rehab-article-row__cta">';
		if ( $a['primaryText'] ) $h .= '<a href="' . esc_url( $a['primaryUrl'] ) . '" class="rehab-btn rehab-btn--luxury">' . esc_html( $a['primaryText'] ) . '</a>';
		if ( $a['secondaryText'] ) $h .= '<a href="' . esc_url( $a['secondaryUrl'] ) . '" class="rehab-btn rehab-btn--outline">' . esc_html( $a['secondaryText'] ) . '</a>';
		$h .= '</div>';
	}
	$h .= '</div></div></div></section>';
	return "<!-- wp:rehab/article-row " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/article-row -->\n\n";
}


/**
 * Build a `rehab/tabs` + `rehab/tab` block stack for the treatment-phases pattern.
 * Each tab gets a phase eyebrow, a label, a content panel (h3 + paragraphs + optional list),
 * and a sage-mist aside (quote + meta).
 *
 * $tabs = array of:
 *   [ 'phase' => 'PHASE 01', 'label' => 'Medical detox',
 *     'h3' => '...', 'paragraphs' => [...], 'listItems' => [...] (optional),
 *     'asideQuote' => '...', 'asideMetaLabel' => 'Quoted by', 'asideMetaValue' => 'Theo de Vries, Founder' ]
 */
function rehab_block_treatment_tabs( string $eyebrow, string $heading, string $subheading, array $tabs, string $bg = 'white' ): string {
	$tabs_attrs = rehab_block_attrs( [ 'background' => $bg, 'heading' => '' ] );
	// Outer head/wrapper renders eyebrow + heading + subheading above the nav.
	$h  = '<section class="wp-block-rehab-tabs rehab-tabs rehab-bg-' . esc_attr( $bg ) . '" data-rehab-tabs="">';
	$h .= '<div class="rehab-container">';
	$h .= '<div class="rehab-tabs__head">';
	if ( $eyebrow )    $h .= '<span class="rehab-tabs__eyebrow">' . esc_html( $eyebrow ) . '</span>';
	$h .= '<h2 class="rehab-tabs__heading">' . esc_html( $heading ) . '</h2>';
	if ( $subheading ) $h .= '<p class="rehab-tabs__sub">' . esc_html( $subheading ) . '</p>';
	$h .= '</div>';
	$h .= '<div class="rehab-tabs__inner">';

	$inner = '';
	foreach ( $tabs as $tab ) {
		$tab = array_merge( [
			'phase' => '', 'label' => '',
			'h3' => '', 'paragraphs' => [], 'listItems' => [],
			'asideQuote' => '', 'asideMetaLabel' => 'Quoted by', 'asideMetaValue' => '',
		], $tab );
		$tab_attrs = rehab_block_attrs( [ 'label' => $tab['label'], 'phaseNumber' => $tab['phase'] ] );

		$panel  = '<div class="rehab-tab" data-label="' . esc_attr( $tab['label'] ) . '" data-phase="' . esc_attr( $tab['phase'] ) . '">';
		$panel .= '<div class="rehab-tab__main">';
		$panel .= '<h3>' . esc_html( $tab['h3'] ) . '</h3>';
		foreach ( $tab['paragraphs'] as $i => $p ) {
			$panel .= '<p>' . esc_html( $p ) . '</p>';
			// Insert list right after the second paragraph if present (matches design pattern).
			if ( $i === 1 && ! empty( $tab['listItems'] ) ) {
				$panel .= '<ul>';
				foreach ( $tab['listItems'] as $li ) {
					$panel .= '<li>' . wp_kses( $li, [ 'strong' => [] ] ) . '</li>';
				}
				$panel .= '</ul>';
			}
		}
		$panel .= '</div>';
		if ( $tab['asideQuote'] ) {
			$panel .= '<aside class="rehab-tab__aside">';
			$panel .= '<p class="quote">' . esc_html( $tab['asideQuote'] ) . '</p>';
			$panel .= '<div class="meta"><strong>' . esc_html( $tab['asideMetaLabel'] ) . '</strong>' . esc_html( $tab['asideMetaValue'] ) . '</div>';
			$panel .= '</aside>';
		}
		$panel .= '</div>';

		$inner .= "<!-- wp:rehab/tab " . $tab_attrs . " -->\n" . $panel . "\n<!-- /wp:rehab/tab -->\n";
	}
	$h .= $inner;
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/tabs " . $tabs_attrs . " -->\n" . $h . "\n<!-- /wp:rehab/tabs -->\n\n";
}


/**
 * Build a `rehab/final-cta` block — dark contact + form section.
 */
function rehab_block_final_cta( array $a = [] ): string {
	$defaults = [
		'anchorId' => 'assessment',
		'eyebrow' => 'Take the next step',
		'heading' => 'Are you ready to begin?',
		'lead' => "Fill out the form and our client relations team will call you back, confidentially, within an hour during business hours. No pressure, no commitment — just a conversation.",
		'phoneText' => '+61 2 7908 2277', 'phoneHref' => 'tel:+61279082277',
		'whatsappText' => '+66 96 582 3832', 'whatsappHref' => 'https://wa.me/66965823832',
		'emailText' => 'info@diamondrehabthailand.com', 'emailHref' => 'mailto:info@diamondrehabthailand.com',
		'formTitle' => 'Free, confidential assessment',
		'formSub' => "We'll reply within one hour during business hours.",
		'formSubmit' => 'Schedule free assessment',
		'formLegal' => 'Your details are confidential and never shared. By submitting you agree to be contacted by The Diamond Rehab Thailand.',
	];
	$a = array_merge( $defaults, $a );
	$attrs = rehab_block_attrs( $a );
	$phone_svg    = '<svg width="20" height="20" viewBox="0 0 15 16" aria-hidden="true"><path d="M14.8 13.1c-.8 2-2.8 2.4-3.5 2.4-.2 0-3.2.2-7.4-3.9C.5 8.3 0 4.8 0 4.1 0 3.4.2 1.8 2.4.6c.3-.2.8-.2 1-.1.1.1 1.9 3.2 2 3.3 0 .1.1.2.1.3 0 .1-.1.3-.3.5l-.7.7c-.3.1-.5.3-.7.5-.2.2-.3.4-.3.5 0 .3.3 1.5 2.3 3.3C7.9 11.5 8.9 12 9 12c.1 0 .2.1.2.1.1 0 .3-.1.5-.3.2-.2.9-1.1 1.1-1.3.2-.2.4-.3.5-.3.1 0 .2 0 .3.1.1 0 3.2 1.9 3.3 1.9.2.1.1.6-.1.9" fill="currentColor"/></svg>';
	$whatsapp_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.6 6.3A8 8 0 0 0 4 12a7.8 7.8 0 0 0 1.1 4L4 20l4.1-1.1a8 8 0 0 0 9.5-12.6Zm-5.6 12.4a7.1 7.1 0 0 1-3.6-1l-.3-.1L5.7 18l.6-2.4-.2-.3a6.6 6.6 0 1 1 5.9 3.4Zm3.7-4.9c-.2 0-1.2-.6-1.4-.7s-.3 0-.5.2-.5.6-.6.7-.3.1-.5 0a5.4 5.4 0 0 1-2.7-2.4c-.2-.3 0-.5.1-.6l.4-.4.1-.3v-.3l-.7-1.7c-.2-.4-.3-.4-.5-.4h-.4a.8.8 0 0 0-.6.3 2.5 2.5 0 0 0-.7 1.8 4.3 4.3 0 0 0 .9 2.3 9.7 9.7 0 0 0 3.7 3.3 3.5 3.5 0 0 0 1.5.4 2.4 2.4 0 0 0 1.7-1 2 2 0 0 0 .1-1.2Z"/></svg>';
	$email_svg    = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3 7 9 6 9-6"/></svg>';
	$h  = '<section class="wp-block-rehab-final-cta rehab-final-cta"' . ( $a['anchorId'] ? ' id="' . esc_attr( $a['anchorId'] ) . '"' : '' ) . '>';
	$h .= '<div class="rehab-final-cta__inner">';
	$h .= '<div>';
	if ( $a['eyebrow'] ) $h .= '<span class="rehab-final-cta__eyebrow">' . esc_html( $a['eyebrow'] ) . '</span>';
	$h .= '<h2 class="rehab-final-cta__heading">' . esc_html( $a['heading'] ) . '</h2>';
	if ( $a['lead'] ) $h .= '<p class="rehab-final-cta__lead">' . esc_html( $a['lead'] ) . '</p>';
	$h .= '<div class="rehab-final-cta__contact">';
	if ( $a['phoneText'] )    $h .= '<a href="' . esc_url( $a['phoneHref'] ) . '">' . $phone_svg . 'Call <strong>' . esc_html( $a['phoneText'] ) . '</strong></a>';
	if ( $a['whatsappText'] ) $h .= '<a href="' . esc_url( $a['whatsappHref'] ) . '">' . $whatsapp_svg . 'WhatsApp <strong>' . esc_html( $a['whatsappText'] ) . '</strong></a>';
	if ( $a['emailText'] )    $h .= '<a href="' . esc_url( $a['emailHref'] ) . '">' . $email_svg . 'Email <strong>' . esc_html( $a['emailText'] ) . '</strong></a>';
	$h .= '</div></div>';
	$h .= '<form class="rehab-final-cta__form" data-rehab-contact-form>';
	$h .= '<p class="rehab-final-cta__form-title">' . esc_html( $a['formTitle'] ) . '</p>';
	$h .= '<p class="rehab-final-cta__form-sub">' . esc_html( $a['formSub'] ) . '</p>';
	$h .= '<div class="rehab-final-cta__honeypot" aria-hidden="true"><label>Don\'t fill this in:<input type="text" name="_hp" tabindex="-1" autocomplete="off"></label></div>';
	$h .= '<input type="text" name="name" placeholder="Full name" required autocomplete="name">';
	$h .= '<div class="rehab-final-cta__form-row"><input type="email" name="email" placeholder="E-mail" required autocomplete="email"><input type="tel" name="phone" placeholder="Phone (with country code)" required autocomplete="tel"></div>';
	$h .= '<input type="text" name="country" placeholder="Country" autocomplete="country-name">';
	$h .= '<textarea name="message" placeholder="Tell us briefly what\'s happening (optional)" maxlength="500"></textarea>';
	$h .= '<button type="submit" class="rehab-btn rehab-btn--luxury" style="width:100%">' . esc_html( $a['formSubmit'] ) . '</button>';
	$h .= '<p class="rehab-final-cta__form-status" role="status" aria-live="polite"></p>';
	$h .= '<p class="rehab-final-cta__form-legal">' . esc_html( $a['formLegal'] ) . '</p>';
	$h .= '</form>';
	$h .= '</div></section>';
	return "<!-- wp:rehab/final-cta " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/final-cta -->\n\n";
}


/**
 * Build a `rehab/treatment-phases` block — self-contained tabs widget.
 * All content is in attributes (no parent/child blocks) so the page is
 * canonicalize-safe.
 *
 * $phases = array of:
 *   [ 'phase' => 'PHASE 01', 'label' => 'Medical detox',
 *     'h3' => '...', 'paragraphs' => [...], 'listItems' => [...] (optional, inserted after para 2),
 *     'asideQuote' => '...', 'asideMetaLabel' => 'Quoted by', 'asideMetaValue' => 'Theo de Vries' ]
 */
function rehab_block_treatment_phases( string $eyebrow, string $heading, string $subheading, array $phases, string $bg = 'white' ): string {
	$attrs = rehab_block_attrs( [
		'background' => $bg, 'eyebrow' => $eyebrow, 'heading' => $heading,
		'subheading' => $subheading, 'phases' => $phases,
	] );
	$h  = '<section class="wp-block-rehab-treatment-phases rehab-treatment-phases rehab-bg-' . esc_attr( $bg ) . '">';
	$h .= '<div class="rehab-container">';
	if ( $eyebrow || $heading || $subheading ) {
		$h .= '<div class="rehab-treatment-phases__head">';
		if ( $eyebrow )    $h .= '<span class="rehab-treatment-phases__eyebrow">' . esc_html( $eyebrow ) . '</span>';
		if ( $heading )    $h .= '<h2 class="rehab-treatment-phases__heading">' . esc_html( $heading ) . '</h2>';
		if ( $subheading ) $h .= '<p class="rehab-treatment-phases__sub">' . esc_html( $subheading ) . '</p>';
		$h .= '</div>';
	}
	// Tab nav
	$h .= '<div class="rehab-treatment-phases__nav" role="tablist">';
	foreach ( $phases as $i => $phase ) {
		$active = $i === 0 ? ' is-active' : '';
		$selected = $i === 0 ? 'true' : 'false';
		$h .= '<button type="button" role="tab" class="rehab-treatment-phases__tab' . $active . '" data-tab="' . $i . '" aria-selected="' . $selected . '"><span>';
		if ( ! empty( $phase['phase'] ) ) $h .= '<span class="num">' . esc_html( $phase['phase'] ) . '</span>';
		$h .= '<span>' . esc_html( $phase['label'] ?? ( 'Phase ' . ( $i + 1 ) ) ) . '</span>';
		$h .= '</span></button>';
	}
	$h .= '</div>';
	// Panels
	$h .= '<div class="rehab-treatment-phases__panels">';
	foreach ( $phases as $i => $phase ) {
		$phase = array_merge( [ 'phase' => '', 'label' => '', 'h3' => '', 'paragraphs' => [], 'listItems' => [], 'asideQuote' => '', 'asideMetaLabel' => 'Quoted by', 'asideMetaValue' => '' ], $phase );
		$hidden = $i === 0 ? '' : ' hidden';
		$h .= '<div class="rehab-treatment-phases__panel" data-label="' . esc_attr( $phase['label'] ) . '" data-phase="' . esc_attr( $phase['phase'] ) . '"' . $hidden . '>';
		$h .= '<div class="rehab-treatment-phases__main">';
		if ( $phase['h3'] ) $h .= '<h3>' . esc_html( $phase['h3'] ) . '</h3>';
		foreach ( $phase['paragraphs'] as $j => $p ) {
			$h .= '<p>' . esc_html( $p ) . '</p>';
			if ( $j === 1 && ! empty( $phase['listItems'] ) ) {
				$h .= '<ul>';
				foreach ( $phase['listItems'] as $li ) {
					$h .= '<li>' . wp_kses( $li, [ 'strong' => [], 'em' => [] ] ) . '</li>';
				}
				$h .= '</ul>';
			}
		}
		$h .= '</div>';
		if ( $phase['asideQuote'] ) {
			$h .= '<aside class="rehab-treatment-phases__aside">';
			$h .= '<p class="quote">' . esc_html( $phase['asideQuote'] ) . '</p>';
			$h .= '<div class="meta"><strong>' . esc_html( $phase['asideMetaLabel'] ) . '</strong>' . esc_html( $phase['asideMetaValue'] ) . '</div>';
			$h .= '</aside>';
		}
		$h .= '</div>';
	}
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/treatment-phases " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/treatment-phases -->\n\n";
}


/**
 * Build a `rehab/assessment-hero` block (dynamic — self-closing comment).
 * Assessment-first treatment hero: copy + rating + signals left, admissions form right.
 */
function rehab_block_assessment_hero( array $a ): string {
	return "<!-- wp:rehab/assessment-hero " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Build a `rehab/video-reel` block (dynamic — self-closing comment).
 * $a['items'] = [ [ 'name', 'duration', 'tone' (1-4), 'quote', 'who', 'videoUrl', 'posterUrl' ], ... ]
 */
function rehab_block_video_reel( array $a ): string {
	return "<!-- wp:rehab/video-reel " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Build a `rehab/stat-band` block (dynamic — self-closing comment).
 */
function rehab_block_stat_band( array $a ): string {
	return "<!-- wp:rehab/stat-band " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Build a `rehab/cta-band` block (dynamic — self-closing comment).
 * background: 'sage' (mid-page) | 'dark' (closing concierge) | 'none'; compact: actions row only.
 */
function rehab_block_cta_band( array $a ): string {
	return "<!-- wp:rehab/cta-band " . rehab_block_attrs( $a ) . " /-->\n\n";
}


/**
 * Build the full approved treatment-page v3 sequence from a per-condition spec.
 *
 * Shared sections (press ribbon, pillars, sage band, video reel, benefits,
 * journey steps, compact CTA, stat band, related cards, concierge) come from
 * defaults; the spec supplies only the condition-specific copy. See
 * aa-treatment-v3-specs.php for the spec format and per-page entries.
 *
 * @param int   $page_id Page being rebuilt (used to exclude it from related cards).
 * @param array $spec    Condition-specific content.
 * @return string Block markup for post_content.
 */
function rehab_build_treatment_v3( int $page_id, array $spec ): string {
	$base   = '/wp-content/uploads/';
	$blocks = '';

	$hero = $spec['hero'];
	$blocks .= rehab_block_assessment_hero( array_merge( [
		'anchorId'    => 'assessment',
		'primaryText' => 'Talk with admissions', 'primaryUrl' => '#assessment',
		'ratingScore' => '4.9', 'ratingText' => 'from 48 Google reviews · families & alumni',
		'stat1Num' => '12',   'stat1Label' => 'Maximum clients on site at any time',
		'stat2Num' => '24/7', 'stat2Label' => 'Doctor & clinical team on call',
		'stat3Num' => '12+',
		'formEyebrow' => 'Free & confidential',
		'formTitle'   => 'Talk with our admissions team',
		'formSub'     => 'No pressure, no obligation. A clinician replies within the hour, not a call centre.',
		'formSubmit'  => 'Talk with admissions',
		'formPhoneLabel' => 'Or call +61 2 7908 2277',
		'formConsent' => 'By submitting you agree to a confidential call-back. We never share your details.',
	], $hero ) );

	$blocks .= rehab_block_authority_ribbon( 'As featured in', rehab_press_ribbon_logos() );

	$blocks .= rehab_block_signs_grid( array_merge( [
		'background' => 'cream',
		'eyebrow'    => 'Is this you, or someone you love?',
		'showCta'    => true,
		'ctaTitle'   => 'If any of this feels familiar, please reach out.',
		'ctaBody'    => "Call, message on WhatsApp, or speak with admissions. There's no pressure and nothing to commit to.",
		'ctaButton'  => 'Talk with admissions', 'ctaUrl' => '#assessment',
	], $spec['signs'] ) );

	// Pillars: shared substance-page copy by default; mental-health specs
	// override via $spec['pillars'] (the default mentions medical detox).
	$blocks .= rehab_block_pillars(
		'Why Diamond Rehab',
		'Three reasons families choose us',
		'',
		$spec['pillars'] ?? [
			[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Medical detox and proven therapies including CBT, trauma work and family therapy, alongside fitness, nutrition and mindfulness that rebuild the whole person.' ],
			[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your plan is built by a psychiatrist for your history, not slotted into a fixed curriculum.' ],
			[ 'num' => '03 · Support around the clock', 'title' => 'Care when cravings hit hardest', 'body' => 'A 2:1 staff-to-client ratio and 24/7 medical cover mean someone is always there, through the night and the hardest moments.' ],
		],
		'white'
	);

	$blocks .= rehab_block_article_row( array_merge( [
		'background' => 'cream',
		'imageSide' => 'right', 'imageAspect' => 'tall',
		'imageUrl' => $base . '2024/05/1-1-session-room-1.jpg',
		'imageAlt' => '1-on-1 therapy room',
	], $spec['holistic'] ) );

	$phases = $spec['phases'];
	$blocks .= rehab_block_treatment_phases(
		'The treatment phases',
		$phases['heading'],
		'',
		$phases['items'],
		'white'
	);

	$blocks .= rehab_block_cta_band( [
		'background' => 'sage',
		'eyebrow'    => 'Ready when you are',
		'heading'    => 'Recovery can begin this week',
		'lede'       => 'A confidential call is the first step. No pressure and no commitment to proceed.',
		'primaryText' => 'Talk with admissions', 'primaryUrl' => '#assessment',
		'helper'     => 'Free, confidential, and no-obligation.',
	] );

	$program_tag = $spec['programTag'] ?? 'program';
	$blocks .= rehab_block_video_reel( [
		'background' => 'cream',
		'eyebrow'    => 'Real stories',
		'heading'    => 'Recovery, in their own words',
		'ratingScore' => '4.9', 'ratingText' => '· 48 Google reviews',
		'items' => [
			[ 'name' => 'James · ' . $program_tag, 'duration' => '2:14', 'tone' => '1', 'quote' => '"They gave me my life back."', 'who' => 'Filmed with consent', 'videoUrl' => '', 'posterUrl' => '' ],
			[ 'name' => 'Anonymous · privacy-protected', 'duration' => '1:48', 'tone' => '4', 'quote' => '"I didn\'t think I could stop. I was wrong."', 'who' => 'Silhouette format', 'videoUrl' => '', 'posterUrl' => '' ],
			[ 'name' => 'A father, on his son', 'duration' => '3:02', 'tone' => '3', 'quote' => '"We got our son back."', 'who' => 'Family perspective', 'videoUrl' => '', 'posterUrl' => '' ],
			[ 'name' => 'Alumna · executive program', 'duration' => '2:31', 'tone' => '2', 'quote' => '"The twelve-client cap changed everything."', 'who' => 'Filmed with consent', 'videoUrl' => '', 'posterUrl' => '' ],
		],
	] );

	$blocks .= rehab_block_article_row( array_merge( [
		'background' => 'white',
		'imageSide' => 'left', 'imageAspect' => 'wide',
		'imageUrl' => $base . '2024/05/Close-up-chairs-3.jpg',
		'imageAlt' => 'The grounds at Hua Hin',
		'eyebrow' => 'The advantage of inpatient care',
		'heading' => 'Why distance makes recovery possible',
	], $spec['inpatient'] ?? [] ) );
	// Benefits: shared copy by default; specs override per condition via $spec['benefits'].
	$blocks .= rehab_block_benefits_numbered( $spec['benefits'] ?? [
		[ 'title' => 'Distance from triggers', 'body' => 'Away from the people, places and routines that keep the cycle turning.' ],
		[ 'title' => 'Round-the-clock supervision', 'body' => 'Resort-calm surroundings backed by qualified medical staff, day and night.' ],
		[ 'title' => 'A plan built for you', 'body' => 'A program tailored to your clinical picture, not a fixed curriculum.' ],
		[ 'title' => 'A real therapeutic community', 'body' => 'A hard cap of twelve clients means genuine attention and deeper connection.' ],
	] );

	$blocks .= rehab_block_prose(
		$spec['prose']['heading'],
		$spec['prose']['paragraphs'],
		[], '', '', 'cream'
	);

	// Journey steps: shared by default; mental-health specs override step 4
	// via $spec['steps'] (the default mentions medical detox).
	$blocks .= rehab_block_journey_steps(
		'Your next step',
		'What happens when you reach out',
		"There's no commitment in making contact. Here's exactly how the first few days unfold.",
		$spec['steps'] ?? [
			[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
			[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
			[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
			[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'Medical detox if needed, then your bespoke program of therapy and wellness.' ],
		],
		'white'
	);
	$blocks .= rehab_block_cta_band( [
		'background' => 'none', 'compact' => true,
		'primaryText' => 'Talk with admissions', 'primaryUrl' => '#assessment',
	] );

	$blocks .= rehab_block_stat_band( [
		'stat1Num' => '12',  'stat1Label' => 'Client cap, always',
		'stat2Num' => '2:1', 'stat2Label' => 'Staff-to-client ratio',
		'stat3Num' => '35', 'stat3Label' => 'Specialist staff',
		'stat4Num' => '28',  'stat4Label' => 'Day core program',
	] );

	$related_pages = get_posts( [
		'post_type'      => 'page',
		'posts_per_page' => 3,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'template-treatment.php',
		'post__not_in'   => [ $page_id ],
		'orderby'        => 'modified',
		'order'          => 'DESC',
	] );
	if ( $related_pages ) {
		$cards = [];
		foreach ( $related_pages as $rp ) {
			$thumb   = get_the_post_thumbnail_url( $rp, 'medium_large' );
			$cards[] = [
				'title'       => get_the_title( $rp ),
				'description' => get_the_excerpt( $rp ) ?: 'Discreet, doctor-led residential treatment in Hua Hin.',
				'imageUrl'    => $thumb ?: '',
				'imageAlt'    => get_the_title( $rp ),
				'url'         => get_permalink( $rp ),
			];
		}
		$blocks .= rehab_block_cards_grid( 'Other conditions we treat', '', $cards, 3, 'white' );
	}

	// FAQ: $spec['faqs'] is an ordered list mixing [ 'cptId' => N ] refs to
	// shared FAQ posts and [ 'question' => …, 'answer' => … ] page-local items.
	// Page-local posts are created with post_parent = page id and matched by
	// title *within that page*, so condition-specific answers to a question
	// shared across pages never collide (REH-64). Legacy faqCptIds + faqNew
	// keys still work and behave like the old cptIds-then-new ordering.
	$faq_defs = $spec['faqs'] ?? array_merge(
		array_map( static fn( $id ) => [ 'cptId' => (int) $id ], $spec['faqCptIds'] ?? [] ),
		$spec['faqNew'] ?? []
	);
	$faq_items = [];
	foreach ( $faq_defs as $df ) {
		if ( isset( $df['cptId'] ) ) { $faq_items[] = [ 'cptId' => (int) $df['cptId'] ]; continue; }
		$match  = get_posts( [ 'post_type' => 'faq', 'title' => $df['question'], 'post_parent' => $page_id, 'posts_per_page' => 1, 'fields' => 'ids' ] );
		$faq_id = $match ? (int) $match[0] : wp_insert_post( [
			'post_type'    => 'faq',
			'post_status'  => 'publish',
			'post_title'   => $df['question'],
			'post_content' => $df['answer'],
			'post_parent'  => $page_id,
		] );
		if ( $faq_id && ! is_wp_error( $faq_id ) ) {
			$faq_items[] = [ 'cptId' => $faq_id ];
			echo ( $match ? 'reused' : 'created' ) . " FAQ CPT {$faq_id}: {$df['question']}\n";
		}
	}
	$blocks .= rehab_block_faq( 'Frequently asked questions', $faq_items );

	$blocks .= rehab_block_cta_band( [
		'background' => 'dark',
		'eyebrow'    => 'Take the next step',
		'heading'    => "You've already done the hardest part: recognising it",
		'lede'       => "A short, confidential call with our admissions team. We listen, we answer your questions, and we never sell. Whenever you're ready.",
		'primaryText' => 'Talk with admissions', 'primaryUrl' => '#assessment',
		'secondaryText' => 'WhatsApp us', 'secondaryUrl' => 'https://wa.me/66965823832',
		'helper'     => '',
	] );

	return $blocks;
}


/**
 * Build a `rehab/checklist-cards` block (dynamic — self-closing comment).
 * $a['cards'] = [ [ 'kick', 'title', 'items' => [...] ], ... ] plus optional panel* keys.
 */
function rehab_block_checklist_cards( array $a ): string {
	return "<!-- wp:rehab/checklist-cards " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Build a `rehab/exclusions-list` block (dynamic — self-closing comment).
 */
function rehab_block_exclusions_list( array $a ): string {
	return "<!-- wp:rehab/exclusions-list " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Build a `rehab/guarantee` block (dynamic — self-closing comment).
 */
function rehab_block_guarantee( array $a ): string {
	return "<!-- wp:rehab/guarantee " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Build a `rehab/gallery` block (static — emits save-compatible markup).
 * $images = [ [ 'url', 'alt' ], ... ]
 */
function rehab_block_gallery( string $heading, array $images, string $variant = 'grid', int $columns = 3, string $bg = 'cream' ): string {
	$attrs = rehab_block_attrs( [ 'background' => $bg, 'variant' => $variant, 'columns' => $columns, 'heading' => $heading, 'images' => $images ] );
	$h  = '<section class="wp-block-rehab-gallery rehab-gallery rehab-bg-' . esc_attr( $bg ) . ' rehab-gallery--' . esc_attr( $variant ) . ' rehab-gallery--cols-' . esc_attr( (string) $columns ) . '"><div class="rehab-container">';
	if ( $heading !== '' ) {
		$h .= '<header class="rehab-gallery__header"><h2 class="rehab-heading rehab-heading--lg">' . esc_html( $heading ) . '</h2></header>';
	}
	$h .= '<div class="rehab-gallery__grid">';
	foreach ( $images as $img ) {
		$h .= '<figure class="rehab-gallery__item"><img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $img['alt'] ?? '' ) . '" loading="lazy"/></figure>';
	}
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/gallery " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/gallery -->\n\n";
}


/**
 * Build a `rehab/page-header` block (dynamic — self-closing comment).
 */
function rehab_block_page_header( array $a ): string {
	return "<!-- wp:rehab/page-header " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Build a `rehab/contact-methods` block (dynamic — self-closing comment).
 */
function rehab_block_contact_methods( array $a ): string {
	return "<!-- wp:rehab/contact-methods " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Re-serialize the first instance of a named block from an existing post's
 * content. Lets a rebuild carry over a working block (e.g. the contact page's
 * configured rehab/map) verbatim instead of reconstructing it.
 */
function rehab_block_copy_from_post( int $post_id, string $block_name ): string {
	$post = get_post( $post_id );
	if ( ! $post ) return '';
	foreach ( parse_blocks( $post->post_content ) as $b ) {
		if ( $b['blockName'] === $block_name ) {
			return serialize_block( $b ) . "\n\n";
		}
	}
	return '';
}


/**
 * Build a `rehab/feature-split` block (dynamic — self-closing comment).
 */
function rehab_block_feature_split( array $a ): string {
	return "<!-- wp:rehab/feature-split " . rehab_block_attrs( $a ) . " /-->\n\n";
}


/**
 * Build a `rehab/team-grid` block (dynamic — self-closing comment).
 */
function rehab_block_team_grid( array $a ): string {
	return "<!-- wp:rehab/team-grid " . rehab_block_attrs( $a ) . " /-->\n\n";
}


/**
 * Build a `rehab/faq-page` block (dynamic — self-closing comment).
 */
function rehab_block_faq_page( array $a ): string {
	return "<!-- wp:rehab/faq-page " . rehab_block_attrs( $a ) . " /-->\n\n";
}


/**
 * Build a `rehab/team-profile` block (dynamic — self-closing comment).
 */
function rehab_block_team_profile( array $a ): string {
	return "<!-- wp:rehab/team-profile " . rehab_block_attrs( $a ) . " /-->\n\n";
}

/**
 * Per-page role map for the team-profile rollout. Roles aren't stored on the
 * legacy member pages (intro-doctor-card has no role field), so they live here,
 * mirroring the approved Team grid.
 */
function rehab_team_member_roles(): array {
	return [
		4085  => 'Founders',
		9156  => 'Director',
		11930 => 'Clinical Supervisor / Psychologist',
		8124  => 'General Manager',
		8414  => 'Consultant Psychiatrist',
		11926 => 'Psychotherapist / Counselling Psychologist',
		11933 => 'Addiction Counsellor',
		11931 => 'Addiction Counsellor',
		11932 => 'Addiction Counsellor',
		8125  => 'Nurse',
		8126  => 'Nurse',
		8407  => 'Nurse',
		11935 => 'Head Chef',
		11934 => 'Support Worker / Admissions',
		9137  => 'Support Worker',
		9138  => 'Support Worker',
		9139  => 'Admin / Support Worker',
		8408  => 'Yoga Teacher',
		4088  => 'Medical Writer · Doctor of Medicine',
		4454  => 'Medical Writer · Psychologist',
		5340  => 'Medical Writer',
	];
}

/**
 * Extract { name, bio (\n\n paragraphs), photoUrl, photoAlt, quote } from a
 * member page's existing `rehab/intro-doctor-card` block. Used by the
 * team-profile rebuild so the real bio/photo carry over untouched.
 *
 * The pull-quote is taken from the first bio paragraph ONLY when it is short
 * and written in the first person — i.e. it reads as the person speaking. This
 * keeps the quote genuinely "pulled from the text", never invented.
 */
function rehab_extract_member_from_intro( string $content ): array {
	$out = [ 'name' => '', 'bio' => '', 'photoUrl' => '', 'photoAlt' => '', 'quote' => '' ];
	foreach ( parse_blocks( $content ) as $b ) {
		if ( $b['blockName'] !== 'rehab/intro-doctor-card' ) continue;
		$attrs = $b['attrs'];
		$out['name']     = $attrs['heading'] ?? '';
		$out['photoUrl'] = $attrs['doctorImageUrl'] ?? '';
		$out['photoAlt'] = $attrs['doctorImageAlt'] ?? '';
		$body = (string) ( $attrs['body'] ?? '' );
		// Tolerate backups whose JSON unicode escapes lost their backslash
		// (older update_post_meta unslashing): u003C -> < etc.
		if ( false !== strpos( $body, 'u003C' ) && false === strpos( $body, '<' ) ) {
			$body = str_replace( [ 'u003C', 'u003E', 'u002F', 'u0026' ], [ '<', '>', '/', '&' ], $body );
		}
		// Split <p>…</p> paragraphs into plain text. Normalise em/en dashes to a
		// comma per the site-wide no-em-dash rule (these are real bios).
		$norm = static function ( $t ) {
			$t = trim( html_entity_decode( wp_strip_all_tags( $t ), ENT_QUOTES ) );
			$t = preg_replace( '/\s*[—–]\s*/u', ', ', $t );
			return $t;
		};
		preg_match_all( '#<p[^>]*>(.*?)</p>#is', $body, $m );
		$paras = array_values( array_filter( array_map( $norm, $m[1] ) ) );
		if ( ! $paras && trim( wp_strip_all_tags( $body ) ) !== '' ) {
			$paras = array_values( array_filter( array_map( $norm, preg_split( "/\n\s*\n/", $body ) ) ) );
		}
		// Quote heuristic: short, first-person opener.
		if ( $paras ) {
			$first = $paras[0];
			$is_first_person = (bool) preg_match( '/^(I |I\'|My |As a |As someone )/', $first );
			if ( mb_strlen( $first ) <= 170 && $is_first_person ) {
				$out['quote'] = $first;
				$paras = array_slice( $paras, 1 );
			}
		}
		$out['bio'] = implode( "\n\n", $paras );
		break;
	}
	return $out;
}
