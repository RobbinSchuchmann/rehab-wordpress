<?php
/**
 * Replace homepage steps block with Diamond's real treatment phases.
 */

$steps = [
	[ 'title' => 'Clinical assessment & intake', 'body' => 'Upon arrival, our medical team conducts a thorough evaluation to design your bespoke treatment protocol.' ],
	[ 'title' => 'Medically-supervised detox', 'body' => 'Safety is our priority. We provide 24/7 clinical monitoring to manage withdrawal symptoms with maximum comfort.' ],
	[ 'title' => 'Intensive therapy', 'body' => 'Core healing through evidence-based modalities like CBT, DBT, and trauma-informed sessions led by expert clinicians.' ],
	[ 'title' => 'Holistic integration', 'body' => 'Combining traditional therapy with yoga, meditation, and fitness to restore physical and emotional balance.' ],
	[ 'title' => 'Family support', 'body' => 'Healing the system, not just the individual. We provide guided sessions for loved ones to ensure long-term success.' ],
	[ 'title' => 'Lifetime alumni support', 'body' => "Your journey doesn't end here. Gain access to our global recovery network and ongoing clinical check-ins." ],
];

$inner = '';
foreach ( $steps as $s ) {
	$attrs = wp_json_encode( $s );
	$inner .= sprintf(
		'<!-- wp:rehab/step %s -->' . "\n" .
		'<li class="wp-block-rehab-step rehab-step"><h3 class="rehab-step__title">%s</h3><p class="rehab-step__body">%s</p></li>' . "\n" .
		'<!-- /wp:rehab/step -->' . "\n",
		$attrs,
		esc_html( $s['title'] ),
		esc_html( $s['body'] )
	);
}

$attrs = wp_json_encode( [
	'background' => 'white',
	'layout'     => 'horizontal',
	'heading'    => 'Your journey to recovery',
	'subheading' => 'A six-phase, doctor-led path designed for sustainable, lasting recovery.',
] );

$steps_block = sprintf(
	'<!-- wp:rehab/steps %s -->' . "\n" .
	'<section class="wp-block-rehab-steps rehab-steps rehab-bg-white rehab-steps--horizontal"><div class="rehab-container"><header class="rehab-steps__header"><h2 class="rehab-heading rehab-heading--lg">Your journey to recovery</h2><p class="rehab-steps__subheading">A six-phase, doctor-led path designed for sustainable, lasting recovery.</p></header><ol class="rehab-steps__list">%s</ol></div></section>' . "\n" .
	'<!-- /wp:rehab/steps -->',
	$attrs,
	$inner
);

$home = get_post( 6 );
$updated = preg_replace(
	'/<!--\s*wp:rehab\/steps[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/steps\s*-->)/is',
	$steps_block,
	$home->post_content,
	1
);
wp_update_post( [ 'ID' => 6, 'post_content' => $updated ] );
echo "OK steps updated with Diamond's 6-phase treatment journey";
