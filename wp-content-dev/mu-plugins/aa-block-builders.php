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
