<?php
/**
 * Replace the homepage marquee with real Diamond accolades.
 */

$items = [
	'Voted #1 by The Thaiger',
	'Featured in Forbes',
	'Discovery Channel feature',
	'Conde Nast Traveler',
	'12-client luxury cap',
	'Lifetime aftercare guarantee',
	'24/7 medical care',
	'NDA with all staff',
];

$attrs = wp_json_encode( [
	'background' => 'sage-mist',
	'speed'      => 35,
	'items'      => $items,
] );

$rows_html = '';
for ( $clone = 0; $clone < 2; $clone++ ) {
	$row = '';
	foreach ( $items as $idx => $it ) {
		$row .= sprintf(
			'<span class="rehab-marquee__item"><span class="rehab-marquee__diamond" aria-hidden="true">◆</span><span>%s</span></span>',
			esc_html( $it )
		);
	}
	$aria = $clone === 1 ? ' aria-hidden="true"' : '';
	$rows_html .= sprintf( '<div class="rehab-marquee__row"%s>%s</div>', $aria, $row );
}

$marquee_block = sprintf(
	'<!-- wp:rehab/marquee %s -->' . "\n" .
	'<section class="wp-block-rehab-marquee rehab-marquee rehab-bg-sage-mist" style="--rehab-marquee-duration:35s"><div class="rehab-marquee__track">%s</div></section>' . "\n" .
	'<!-- /wp:rehab/marquee -->',
	$attrs,
	$rows_html
);

$home = get_post( 6 );
$updated = preg_replace(
	'/<!--\s*wp:rehab\/marquee[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/marquee\s*-->)/is',
	$marquee_block,
	$home->post_content,
	1
);
wp_update_post( [ 'ID' => 6, 'post_content' => $updated ] );
echo "OK homepage marquee updated";
