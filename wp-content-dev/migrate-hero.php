<?php
/**
 * Inject Diamond's hero image into the homepage hero block.
 */

$post = get_post( 6 );
if ( ! $post ) {
	exit( 'no homepage' );
}

$hero_url = get_stylesheet_directory_uri() . '/assets/img/hero/pool-pavilion.avif';

$attrs = wp_json_encode( [
	'eyebrow'      => 'Voted #1 by The Thaiger',
	'headline'     => 'The leading luxury<br>drug and alcohol rehab<br>in Thailand',
	'body'         => 'Thai hospitality meets Western clinical excellence. Set within a private 5-star sanctuary, our 12-client cap ensures your recovery is handled with absolute discretion.',
	'buttonText'   => 'Check Availability',
	'buttonUrl'    => '/contact-us/',
	'buttonHelper' => 'Free, confidential, and no-obligation.',
	'trustItem1'   => 'Exclusive 12-client intake',
	'trustItem2'   => 'Intl. multi-disciplinary team',
	'trustItem3'   => 'Relapse prevention guarantee',
	'imageUrl'     => $hero_url,
	'imageAlt'     => 'Luxury Thai pool pavilion at Diamond Rehab Thailand',
	'showDeco'     => true,
] );

// Replace the existing hero block in the homepage with one that has the image set
$content = $post->post_content;
$pattern = '/<!--\s*wp:rehab\/hero[^>]*?\/-->/i';

// Build canonical hero block markup matching save() output
$hero_block = sprintf(
	'<!-- wp:rehab/hero %s -->%s<!-- /wp:rehab/hero -->',
	$attrs,
	build_hero_inner( $hero_url )
);

if ( preg_match( $pattern, $content ) ) {
	$content = preg_replace( $pattern, $hero_block, $content, 1 );
} else {
	// also try the open/close form
	$content = preg_replace( '/<!--\s*wp:rehab\/hero[^>]*?-->.*?<!--\s*\/wp:rehab\/hero\s*-->/is', $hero_block, $content, 1 );
}

$result = wp_update_post( [
	'ID'           => 6,
	'post_content' => $content,
], true );
echo is_wp_error( $result ) ? 'FAIL: ' . $result->get_error_message() : 'OK homepage hero updated';
echo PHP_EOL;

function build_hero_inner( string $hero_url ): string {
	$alt = 'Luxury Thai pool pavilion at Diamond Rehab Thailand';
	return <<<HTML
<section class="wp-block-rehab-hero rehab-hero" aria-label="Hero"><div class="rehab-hero__container"><div class="rehab-hero__grid"><div class="rehab-hero__content"><h1 class="rehab-hero__h1"><span class="rehab-hero__eyebrow">Voted #1 by The Thaiger</span><span class="rehab-hero__headline">The leading luxury<br>drug and alcohol rehab<br>in Thailand</span></h1><p class="rehab-hero__body">Thai hospitality meets Western clinical excellence. Set within a private 5-star sanctuary, our 12-client cap ensures your recovery is handled with absolute discretion.</p><div class="rehab-hero__cta"><a class="rehab-btn rehab-btn--luxury" href="/contact-us/">Check Availability</a><p class="rehab-hero__cta-helper">Free, confidential, and no-obligation.</p></div><div class="rehab-hero__trust"><div class="rehab-hero__trust-item"><span class="rehab-hero__diamond" aria-hidden="true">◆</span><span>Exclusive 12-client intake</span></div><div class="rehab-hero__trust-item"><span class="rehab-hero__diamond" aria-hidden="true">◆</span><span>Intl. multi-disciplinary team</span></div><div class="rehab-hero__trust-item"><span class="rehab-hero__diamond" aria-hidden="true">◆</span><span>Relapse prevention guarantee</span></div></div></div><div class="rehab-hero__media"><div class="rehab-hero__image-wrap"><img src="$hero_url" alt="$alt" class="rehab-hero__image" loading="eager" decoding="async"/><div class="rehab-hero__overlay" aria-hidden="true"></div></div><div class="rehab-hero__deco" aria-hidden="true"></div></div></div></div></section>
HTML;
}
