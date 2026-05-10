<?php
/**
 * Replace comparison block on homepage with Diamond's real comparison data.
 */

$rows = [
	[ 'topic' => 'Typical cost (4 weeks)',  'left' => 'Considerably lower — call for pricing', 'right' => '$60,000 – $100,000+' ],
	[ 'topic' => 'Clinical leadership',     'left' => 'Board-certified psychiatrist',          'right' => 'Board-certified psychiatrist' ],
	[ 'topic' => '1-on-1 therapy intensity','left' => '3 specialized sessions / week',         'right' => '1–2 sessions / week' ],
	[ 'topic' => 'Accommodation tier',      'left' => 'Private pool bungalow (resort setting)', 'right' => 'Private room (clinical setting)' ],
	[ 'topic' => 'Massages & wellness',     'left' => 'Included weekly',                        'right' => 'Often charged as "extras"' ],
	[ 'topic' => 'Facility capacity',       'left' => 'Strictly capped at 12 clients',          'right' => 'Often 20–50+ clients' ],
	[ 'topic' => 'Primary cost driver',     'left' => 'Direct client care',                     'right' => 'Insurance & administration' ],
];

$attrs = wp_json_encode( [
	'background' => 'white',
	'heading'    => 'World-class luxury rehab — without Western overheads',
	'leftLabel'  => 'The Diamond Rehab Thailand',
	'rightLabel' => 'Premier Western Luxury Rehab',
	'rows'       => $rows,
] );

$rows_html = '';
foreach ( $rows as $row ) {
	$rows_html .= sprintf(
		'<div class="rehab-comparison__cell rehab-comparison__cell--topic"><span>%s</span></div>' .
		'<div class="rehab-comparison__cell rehab-comparison__cell--ours"><span>%s</span></div>' .
		'<div class="rehab-comparison__cell"><span>%s</span></div>',
		esc_html( $row['topic'] ),
		esc_html( $row['left'] ),
		esc_html( $row['right'] )
	);
}

$comparison_block = sprintf(
	'<!-- wp:rehab/comparison %s -->' . "\n" .
	'<section class="wp-block-rehab-comparison rehab-comparison rehab-bg-white"><div class="rehab-container rehab-container--narrow"><header class="rehab-comparison__header"><h2 class="rehab-heading rehab-heading--lg">World-class luxury rehab — without Western overheads</h2></header><div class="rehab-comparison__grid"><div class="rehab-comparison__col rehab-comparison__col--header"></div><div class="rehab-comparison__col rehab-comparison__col--header rehab-comparison__col--ours"><span>The Diamond Rehab Thailand</span></div><div class="rehab-comparison__col rehab-comparison__col--header"><span>Premier Western Luxury Rehab</span></div>%s</div></div></section>' . "\n" .
	'<!-- /wp:rehab/comparison -->',
	$attrs,
	$rows_html
);

$home = get_post( 6 );
$updated = preg_replace(
	'/<!--\s*wp:rehab\/comparison[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/comparison\s*-->)/is',
	$comparison_block,
	$home->post_content,
	1
);
wp_update_post( [ 'ID' => 6, 'post_content' => $updated ] );

// Same for Why us page
$why = get_post( 825 );
if ( $why ) {
	$updated2 = preg_replace(
		'/<!--\s*wp:rehab\/comparison[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/comparison\s*-->)/is',
		$comparison_block,
		$why->post_content,
		1
	);
	wp_update_post( [ 'ID' => 825, 'post_content' => $updated2 ] );
	echo "OK why-us comparison updated" . PHP_EOL;
}

echo "OK homepage comparison updated" . PHP_EOL;
