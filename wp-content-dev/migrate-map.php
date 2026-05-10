<?php
/**
 * Replace homepage / contact-page map block with Hua Hin coordinates.
 */

// Hua Hin, Thailand — real Diamond Rehab area
$embed_url = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3902.586!2d99.96!3d12.553!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sHua+Hin+District%2C+Prachuap+Khiri+Khan%2C+Thailand!5e0!3m2!1sen!2sus!4v1699999999';

$attrs = wp_json_encode( [
	'background' => 'white',
	'heading'    => 'Find us in Hua Hin',
	'address'    => 'The Diamond Rehab Thailand, 8 Moo 14, Soi Mon Mai Hin Lek Fai, Hua Hin District, Prachuap Khiri Khan, Thailand 77110',
	'embedUrl'   => $embed_url,
] );

$map_block = sprintf(
	'<!-- wp:rehab/map %s -->' . "\n" .
	'<section class="wp-block-rehab-map rehab-map rehab-bg-white"><div class="rehab-container"><div class="rehab-map__grid"><div class="rehab-map__info"><h2 class="rehab-heading rehab-heading--lg">Find us in Hua Hin</h2><p class="rehab-map__address">The Diamond Rehab Thailand, 8 Moo 14, Soi Mon Mai Hin Lek Fai, Hua Hin District, Prachuap Khiri Khan, Thailand 77110</p></div><div class="rehab-map__embed"><iframe src="%s" title="Find us in Hua Hin" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div></div></div></section>' . "\n" .
	'<!-- /wp:rehab/map -->',
	$attrs,
	esc_url( $embed_url )
);

foreach ( [ 6, 1189 ] as $page_id ) {
	$post = get_post( $page_id );
	if ( ! $post ) {
		continue;
	}
	$updated = preg_replace(
		'/<!--\s*wp:rehab\/map[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/map\s*-->)/is',
		$map_block,
		$post->post_content,
		1
	);
	wp_update_post( [ 'ID' => $page_id, 'post_content' => $updated ] );
	echo "OK $page_id map updated" . PHP_EOL;
}
