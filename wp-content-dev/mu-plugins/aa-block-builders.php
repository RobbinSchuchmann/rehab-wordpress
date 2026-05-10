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
	$attrs = rehab_block_attrs( $a );
	$out  = '<section class="wp-block-rehab-hero rehab-hero" aria-label="Hero">';
	$out .= '<div class="rehab-hero__container"><div class="rehab-hero__grid">';
	$out .= '<div class="rehab-hero__content">';
	if ( $a['eyebrow'] !== '' ) $out .= '<p class="rehab-hero__eyebrow">' . esc_html( $a['eyebrow'] ) . '</p>';
	$out .= '<h1 class="rehab-hero__h1">' . esc_html( $a['headline'] ) . '</h1>';
	if ( $a['body'] !== '' ) $out .= '<p class="rehab-hero__body">' . esc_html( $a['body'] ) . '</p>';
	$out .= '<div class="rehab-hero__cta"><a class="rehab-btn rehab-btn--luxury" href="' . esc_url( $a['buttonUrl'] ) . '">' . esc_html( $a['buttonText'] ) . '</a><p class="rehab-hero__cta-helper">' . esc_html( $a['buttonHelper'] ) . '</p></div>';
	$out .= '<div class="rehab-hero__trust">';
	foreach ( [ $a['trustItem1'], $a['trustItem2'], $a['trustItem3'] ] as $t ) {
		if ( $t === '' ) continue;
		$out .= '<div class="rehab-hero__trust-item"><span class="rehab-hero__diamond" aria-hidden="true">◆</span>' . esc_html( $t ) . '</div>';
	}
	$out .= '</div></div>';
	if ( $a['imageUrl'] !== '' ) {
		$out .= '<div class="rehab-hero__media"><div class="rehab-hero__image-wrap"><img decoding="async" src="' . esc_url( $a['imageUrl'] ) . '" alt="' . esc_attr( $a['imageAlt'] ) . '" class="rehab-hero__image" loading="eager" width="1080" height="720"><div class="rehab-hero__overlay" aria-hidden="true"></div></div>';
		if ( $a['showDeco'] ) $out .= '<div class="rehab-hero__deco" aria-hidden="true"></div>';
		$out .= '</div>';
	}
	$out .= '</div></div></section>';
	return "<!-- wp:rehab/hero " . $attrs . " -->\n" . $out . "\n<!-- /wp:rehab/hero -->\n\n";
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
	$inner_html = '<div class="rehab-cards-grid__header"><h2 class="rehab-cards-grid__heading">' . esc_html( $heading ) . '</h2>';
	if ( $subheading !== '' ) $inner_html .= '<p class="rehab-cards-grid__sub">' . esc_html( $subheading ) . '</p>';
	$inner_html .= '</div><div class="rehab-cards-grid__list rehab-cards-grid__list--' . $columns . '">';
	$gut_cards  = '';
	foreach ( $cards as $card ) {
		$ca = rehab_block_attrs( $card );
		$ch = '<article class="wp-block-rehab-card rehab-card">';
		if ( ! empty( $card['imageUrl'] ) ) $ch .= '<figure class="rehab-card__media"><img src="' . esc_url( $card['imageUrl'] ) . '" alt="' . esc_attr( $card['imageAlt'] ?? '' ) . '"/></figure>';
		$ch .= '<div class="rehab-card__body"><h3 class="rehab-card__title">';
		if ( ! empty( $card['url'] ) ) $ch .= '<a href="' . esc_url( $card['url'] ) . '">' . esc_html( $card['title'] ) . '</a>';
		else                          $ch .= esc_html( $card['title'] );
		$ch .= '</h3>';
		if ( ! empty( $card['description'] ) ) $ch .= '<p class="rehab-card__desc">' . esc_html( $card['description'] ) . '</p>';
		$ch .= '</div></article>';
		$gut_cards  .= "<!-- wp:rehab/card " . $ca . " -->\n" . $ch . "\n<!-- /wp:rehab/card -->\n";
		$inner_html .= $ch;
	}
	$inner_html .= '</div>';
	return "<!-- wp:rehab/cards-grid " . $grid_attrs . " -->\n" .
		'<section class="wp-block-rehab-cards-grid rehab-cards-grid rehab-bg-' . $bg . '"><div class="rehab-container">' . $inner_html . $gut_cards . '</div></section>' .
		"\n<!-- /wp:rehab/cards-grid -->\n\n";
}

/**
 * Build a `rehab/faq` block.
 * $items = array of [ 'question', 'answer' ].
 */
function rehab_block_faq( string $heading, array $items, string $bg = 'cream' ): string {
	$faq_attrs = rehab_block_attrs( [ 'background' => $bg, 'heading' => $heading ] );
	$inner = '<div class="rehab-container rehab-container--narrow"><h2 class="rehab-faq__heading">' . esc_html( $heading ) . '</h2><div class="rehab-faq__list">';
	$gut   = '';
	foreach ( $items as $item ) {
		$ia = rehab_block_attrs( $item );
		$h  = '<details class="rehab-faq-item"><summary class="rehab-faq-item__summary"><span>' . esc_html( $item['question'] ) . '</span><span class="rehab-faq-item__icon" aria-hidden="true"></span></summary><div class="rehab-faq-item__answer"><p>' . esc_html( $item['answer'] ) . '</p></div></details>';
		$gut   .= "<!-- wp:rehab/faq-item " . $ia . " -->\n" . $h . "\n<!-- /wp:rehab/faq-item -->\n";
		$inner .= $h;
	}
	$inner .= '</div></div>';
	return "<!-- wp:rehab/faq " . $faq_attrs . " -->\n" .
		'<section class="wp-block-rehab-faq rehab-faq rehab-bg-' . $bg . '">' . $inner . $gut . '</section>' .
		"\n<!-- /wp:rehab/faq -->\n\n";
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
		'stat3Num' => '14+', 'stat3Label' => 'Years treating cocaine addiction',
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
	$h  = '<section class="wp-block-rehab-authority-ribbon rehab-authority-ribbon"><div class="rehab-container">';
	$h .= '<p class="rehab-authority-ribbon__label">' . esc_html( $label ) . '</p>';
	$h .= '<div class="rehab-authority-ribbon__logos">';
	foreach ( $logos as $logo ) {
		$h .= '<img src="' . esc_url( $logo['url'] ) . '" alt="' . esc_attr( $logo['alt'] ?? '' ) . '"/>';
	}
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/authority-ribbon " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/authority-ribbon -->\n\n";
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
		$h .= '<h4 class="rehab-journey-step__title">' . esc_html( $item['title'] ) . '</h4>';
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
		$h .= '<div class="rehab-benefit__body"><h4>' . esc_html( $item['title'] ) . '</h4><p>' . esc_html( $item['body'] ) . '</p></div>';
		$h .= '</div>';
	}
	$h .= '</div>';
	return "<!-- wp:rehab/benefits-numbered " . $attrs . " -->\n" . $h . "\n<!-- /wp:rehab/benefits-numbered -->\n\n";
}
