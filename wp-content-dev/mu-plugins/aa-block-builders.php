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
	$h  = '<section class="wp-block-rehab-faq rehab-faq rehab-bg-' . esc_attr( $bg ) . '">';
	$h .= '<div class="rehab-container rehab-container--narrow"><h2 class="rehab-faq__heading">' . esc_html( $heading ) . '</h2>';
	$h .= '<div class="rehab-faq__list">';
	foreach ( $items as $item ) {
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
		'phoneText' => '+66 3 313 5303', 'phoneHref' => 'tel:+6633135303',
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
	$h .= '<form class="rehab-final-cta__form" onsubmit="event.preventDefault(); alert(\'Thank you — our team will be in touch.\');">';
	$h .= '<p class="rehab-final-cta__form-title">' . esc_html( $a['formTitle'] ) . '</p>';
	$h .= '<p class="rehab-final-cta__form-sub">' . esc_html( $a['formSub'] ) . '</p>';
	$h .= '<input type="text" placeholder="Full name" required>';
	$h .= '<div class="rehab-final-cta__form-row"><input type="email" placeholder="E-mail" required><input type="tel" placeholder="Phone (with country code)" required></div>';
	$h .= '<input type="text" placeholder="Country">';
	$h .= '<textarea placeholder="Tell us briefly what\'s happening (optional)" maxlength="180"></textarea>';
	$h .= '<button type="submit" class="rehab-btn rehab-btn--luxury" style="width:100%">' . esc_html( $a['formSubmit'] ) . '</button>';
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
