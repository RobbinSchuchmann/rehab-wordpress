<?php
/**
 * Replace homepage testimonials block with real Diamond Google Reviews.
 */

$reviews = json_decode( file_get_contents( __DIR__ . '/diamond-reviews.json' ), true );
if ( ! $reviews ) {
	exit( 'no reviews' );
}

$build_card = static function ( array $r ): string {
	$attrs = wp_json_encode( [
		'quote'  => html_entity_decode( $r['text'], ENT_QUOTES ),
		'name'   => html_entity_decode( $r['name'], ENT_QUOTES ),
		'role'   => 'Former client',
		'rating' => 5,
	] );
	$stars_svg = str_repeat(
		'<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><polygon points="12,2 15.1,8.6 22,9.5 17,14.4 18.2,21.5 12,18 5.8,21.5 7,14.4 2,9.5 8.9,8.6"></polygon></svg>',
		5
	);
	$quote_html = esc_html( html_entity_decode( $r['text'], ENT_QUOTES ) );
	$name_html  = esc_html( html_entity_decode( $r['name'], ENT_QUOTES ) );
	return sprintf(
		'<!-- wp:rehab/testimonial %s -->' . "\n" .
		'<div class="wp-block-rehab-testimonial rehab-testimonial"><div class="rehab-testimonial__stars" aria-label="5 out of 5 stars">%s</div><p class="rehab-testimonial__quote">%s</p><div class="rehab-testimonial__author"><p class="rehab-testimonial__name">%s</p><p class="rehab-testimonial__role">Former client</p></div></div>' . "\n" .
		'<!-- /wp:rehab/testimonial -->',
		$attrs,
		$stars_svg,
		$quote_html,
		$name_html
	);
};

$cards = '';
foreach ( $reviews as $r ) {
	$cards .= $build_card( $r ) . "\n";
}

$attrs = wp_json_encode( [
	'background' => 'white',
	'columns'    => 3,
	'heading'    => 'Real results, real people',
	'subheading' => 'Hear directly from those who achieved full recovery at The Diamond Rehab Thailand.',
] );

$testimonials_block = sprintf(
	'<!-- wp:rehab/testimonials %s -->' . "\n" .
	'<section class="wp-block-rehab-testimonials rehab-testimonials rehab-bg-white rehab-testimonials--cols-3"><div class="rehab-container"><header class="rehab-testimonials__header"><h2 class="rehab-heading rehab-heading--lg">Real results, real people</h2><p class="rehab-testimonials__subheading">Hear directly from those who achieved full recovery at The Diamond Rehab Thailand.</p></header><div class="rehab-testimonials__grid">%s</div></div></section>' . "\n" .
	'<!-- /wp:rehab/testimonials -->',
	$attrs,
	$cards
);

$home = get_post( 6 );
$updated = preg_replace(
	'/<!--\s*wp:rehab\/testimonials[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/testimonials\s*-->)/is',
	$testimonials_block,
	$home->post_content,
	1
);
wp_update_post( [ 'ID' => 6, 'post_content' => $updated ] );
echo "OK homepage testimonials updated with " . count( $reviews ) . " reviews" . PHP_EOL;
