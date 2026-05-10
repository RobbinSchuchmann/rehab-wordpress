<?php
/**
 * Replace homepage cards-grid + founder-bio with real Diamond content.
 */

$child_uri = get_stylesheet_directory_uri() . '/assets/img';

$treatments = [
	[
		'title' => 'Alcohol addiction treatment',
		'desc'  => 'Medically-supervised detox and evidence-based rehabilitation programs designed to break the cycle of alcohol dependency in our serene Thailand sanctuary.',
		'url'   => '/alcohol-addiction/',
		'photo' => 'cards/alcohol-addiction-treatment.avif',
	],
	[
		'title' => 'Drug addiction treatment',
		'desc'  => 'Comprehensive detox and evidence-based recovery programs for substance use disorders, combining clinical intervention with holistic therapies.',
		'url'   => '/substance-abuse-treatment/',
		'photo' => 'cards/drug-addiction-treatment.avif',
	],
	[
		'title' => 'Cocaine addiction treatment',
		'desc'  => 'Specialized evidence-based protocols addressing cocaine addiction through intensive detox, therapy, and neurological recovery in Thailand.',
		'url'   => '/cocaine-addiction-treatment-rehab-thailand/',
		'photo' => 'cards/cocaine-addiction-treatment.avif',
	],
	[
		'title' => 'Anxiety treatment',
		'desc'  => 'Integrated treatment for anxiety disorders combining cognitive-behavioural therapy, mindfulness, and medication management.',
		'url'   => '/anxiety-rehab-thailand/',
		'photo' => 'cards/anxiety-treatment.avif',
	],
];

$cards = '';
foreach ( $treatments as $t ) {
	$photo_url = "$child_uri/{$t['photo']}";
	$cards .= sprintf(
		'<!-- wp:rehab/card {"imageUrl":"%s","imageAlt":"%s","title":"%s","description":"%s","url":"%s"} -->%s<!-- /wp:rehab/card -->' . "\n",
		esc_url( $photo_url ),
		esc_attr( $t['title'] ),
		$t['title'],
		$t['desc'],
		$t['url'],
		sprintf(
			'<a class="wp-block-rehab-card rehab-card" href="%s"><div class="rehab-card__image"><img src="%s" alt="%s" loading="lazy"/></div><div class="rehab-card__body"><h3 class="rehab-card__title">%s</h3><p class="rehab-card__description">%s</p></div></a>',
			esc_url( $t['url'] ),
			esc_url( $photo_url ),
			esc_attr( $t['title'] ),
			$t['title'],
			$t['desc']
		)
	);
}

$cards_block = sprintf(
	'<!-- wp:rehab/cards-grid {"heading":"What we treat","subheading":"World-class drug and alcohol addiction treatment, personalized for every individual.","columns":2,"cardLayout":"horizontal"} -->' . "\n" .
	'<section class="wp-block-rehab-cards-grid rehab-cards-grid rehab-bg-white rehab-cards-grid--cols-2 rehab-cards-grid--card-horizontal"><div class="rehab-container"><header class="rehab-cards-grid__header"><h2 class="rehab-heading rehab-heading--lg">What we treat</h2><p class="rehab-cards-grid__subheading">World-class drug and alcohol addiction treatment, personalized for every individual.</p></header><div class="rehab-cards-grid__grid">%s</div></div></section>' . "\n" .
	'<!-- /wp:rehab/cards-grid -->',
	$cards
);

// Founder bio
$founder_url = "$child_uri/team/theo-panwadee-de-vries.avif";
$founder_block = sprintf(
	'<!-- wp:rehab/founder-bio {"imageUrl":"%s","imageAlt":"Theo and Panwadee de Vries","quote":"We built this place because we know recovery is possible — we have lived it ourselves.","body":"After founding the rehab in 2015, our mission has remained simple: provide a private, doctor-led environment where every individual gets the time and care needed for lasting recovery.","name":"Theo & Panwadee de Vries","role":"Founders"} -->' . "\n" .
	'<section class="wp-block-rehab-founder-bio rehab-founder rehab-bg-white"><div class="rehab-container"><div class="rehab-founder__grid"><div class="rehab-founder__media"><img src="%s" alt="Theo and Panwadee de Vries" class="rehab-founder__photo" loading="lazy"/></div><div class="rehab-founder__content"><blockquote class="rehab-founder__quote">We built this place because we know recovery is possible — we have lived it ourselves.</blockquote><p class="rehab-founder__body">After founding the rehab in 2015, our mission has remained simple: provide a private, doctor-led environment where every individual gets the time and care needed for lasting recovery.</p><div class="rehab-founder__signature"><p class="rehab-founder__name">Theo &amp; Panwadee de Vries</p><p class="rehab-founder__role">Founders</p></div></div></div></div></section>' . "\n" .
	'<!-- /wp:rehab/founder-bio -->',
	esc_url( $founder_url ),
	esc_url( $founder_url )
);

$post = get_post( 6 );
$content = $post->post_content;

// Replace cards-grid block
$content = preg_replace(
	'/<!--\s*wp:rehab\/cards-grid[^>]*?-->.*?<!--\s*\/wp:rehab\/cards-grid\s*-->/is',
	$cards_block,
	$content,
	1
);

// Replace founder-bio block
$content = preg_replace(
	'/<!--\s*wp:rehab\/founder-bio[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/founder-bio\s*-->)/is',
	$founder_block,
	$content,
	1
);

$result = wp_update_post( [
	'ID'           => 6,
	'post_content' => $content,
], true );
echo is_wp_error( $result ) ? 'FAIL: ' . $result->get_error_message() : 'OK homepage updated';
echo PHP_EOL;
