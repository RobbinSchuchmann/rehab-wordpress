<?php
/**
 * Inject Diamond's accommodation image and a gallery of facility photos.
 */

$child = get_stylesheet_directory_uri() . '/assets/img';

// Accommodation image — pool villa
$acc_url = "$child/accommodation/pool-villa.avif";
$attrs   = wp_json_encode( [
	'background'    => 'white',
	'imagePosition' => 'left',
	'imageUrl'      => $acc_url,
	'imageAlt'      => 'Private pool villa at The Diamond Rehab Thailand',
	'eyebrow'       => 'Accommodation',
	'heading'       => 'Private pool bungalows in a 5-star sanctuary',
	'body'          => 'Each suite is designed for restorative privacy. Ensuite bathrooms, premium linens, and a private pool overlooking the surrounding tropical gardens.',
	'features'      => [
		'Private bungalow with pool',
		'Ensuite bathroom with rainfall shower',
		'King-size bed with premium linens',
		'Daily housekeeping &amp; turndown service',
		'Complimentary high-speed Wi-Fi',
		'Gourmet dining — three meals daily',
	],
] );

$features_html = '';
foreach (
	[
		'Private bungalow with pool',
		'Ensuite bathroom with rainfall shower',
		'King-size bed with premium linens',
		'Daily housekeeping &amp; turndown service',
		'Complimentary high-speed Wi-Fi',
		'Gourmet dining — three meals daily',
	] as $f
) {
	$features_html .= '<li><span class="rehab-accommodation__diamond" aria-hidden="true">◆</span><span>' . $f . '</span></li>';
}

$accommodation_block = sprintf(
	'<!-- wp:rehab/accommodation %s -->' . "\n" .
	'<section class="wp-block-rehab-accommodation rehab-accommodation rehab-bg-white rehab-accommodation--image-left"><div class="rehab-container"><div class="rehab-accommodation__grid"><div class="rehab-accommodation__media"><img src="%s" alt="Private pool villa at The Diamond Rehab Thailand" loading="lazy"/></div><div class="rehab-accommodation__content"><span class="rehab-eyebrow">Accommodation</span><h2 class="rehab-heading rehab-heading--lg">Private pool bungalows in a 5-star sanctuary</h2><p class="rehab-accommodation__body">Each suite is designed for restorative privacy. Ensuite bathrooms, premium linens, and a private pool overlooking the surrounding tropical gardens.</p><ul class="rehab-accommodation__features">%s</ul></div></div></div></section>' . "\n" .
	'<!-- /wp:rehab/accommodation -->',
	$attrs,
	esc_url( $acc_url ),
	$features_html
);

$home = get_post( 6 );
$updated = preg_replace(
	'/<!--\s*wp:rehab\/accommodation[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/accommodation\s*-->)/is',
	$accommodation_block,
	$home->post_content,
	1
);
wp_update_post( [ 'ID' => 6, 'post_content' => $updated ] );
echo "OK accommodation updated" . PHP_EOL;
