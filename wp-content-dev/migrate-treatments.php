<?php
/**
 * Migrate sample treatment pages — apply the Treatment template + pattern.
 */

// Find IDs by slug for treatment pages
$treatment_slugs = [
	'cocaine-addiction-treatment-rehab-thailand',
	'alcohol-addiction-treatment-rehab-thailand',
	'ice-addiction-treatment-rehab-thailand',
	'cannabis-addiction-treatment',
	'addiction-treatment',
	'all-treatments',
];

$treatment_pattern = function ( string $title ) {
	$intro = sprintf( 'Replace this with a clear introduction to %s and how Diamond Rehab approaches it.', strtolower( $title ) );
	return <<<BLOCKS
<!-- wp:rehab/cta {"variant":"compact","background":"sage-mist","heading":"$title","buttonText":"Speak with admissions","helper":"Free, confidential, no-obligation."} /-->

<!-- wp:rehab/prose {"width":"text"} -->
<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner"><!-- wp:heading {"level":2} --><h2>What this treatment involves</h2><!-- /wp:heading --><!-- wp:paragraph --><p>$intro</p><!-- /wp:paragraph --></div></div></section>
<!-- /wp:rehab/prose -->

<!-- wp:rehab/features-list {"heading":"Our approach","subheading":"Evidence-based, integrated care.","columns":3} -->
<section class="wp-block-rehab-features-list rehab-features rehab-bg-cream rehab-features--cols-3"><div class="rehab-container"><header class="rehab-features__header"><h2 class="rehab-heading rehab-heading--lg">Our approach</h2><p class="rehab-features__subheading">Evidence-based, integrated care.</p></header><div class="rehab-features__grid"><!-- wp:rehab/feature {"icon":"⚕","title":"Medical detox","body":"Doctor-supervised detox to manage withdrawal safely."} /--><!-- wp:rehab/feature {"icon":"☘","title":"Therapy","body":"Daily one-on-one and group therapy with licensed clinicians."} /--><!-- wp:rehab/feature {"icon":"♥","title":"Aftercare","body":"Lifetime aftercare plan to prevent relapse."} /--></div></div></section>
<!-- /wp:rehab/features-list -->

<!-- wp:rehab/tabs {"heading":"Treatment phases"} -->
<section class="wp-block-rehab-tabs rehab-tabs rehab-bg-white" data-rehab-tabs=""><div class="rehab-container"><h2 class="rehab-heading rehab-heading--lg rehab-tabs__heading">Treatment phases</h2><div class="rehab-tabs__inner"><!-- wp:rehab/tab {"label":"Detox"} --><div class="wp-block-rehab-tab rehab-tab" data-label="Detox"><!-- wp:paragraph --><p>Describe the detox phase here.</p><!-- /wp:paragraph --></div><!-- /wp:rehab/tab --><!-- wp:rehab/tab {"label":"Rehabilitation"} --><div class="wp-block-rehab-tab rehab-tab" data-label="Rehabilitation"><!-- wp:paragraph --><p>Describe the rehabilitation phase here.</p><!-- /wp:paragraph --></div><!-- /wp:rehab/tab --><!-- wp:rehab/tab {"label":"Aftercare"} --><div class="wp-block-rehab-tab rehab-tab" data-label="Aftercare"><!-- wp:paragraph --><p>Describe the aftercare plan here.</p><!-- /wp:paragraph --></div><!-- /wp:rehab/tab --></div></div></section>
<!-- /wp:rehab/tabs -->

<!-- wp:rehab/faq /-->

<!-- wp:rehab/cta /-->
BLOCKS;
};

$migrated_ids = [];
foreach ( $treatment_slugs as $slug ) {
	$page = get_page_by_path( $slug );
	if ( ! $page ) {
		echo "MISSING: $slug" . PHP_EOL;
		continue;
	}
	$id = $page->ID;
	delete_post_meta( $id, '_wp_page_template' );
	$content = $treatment_pattern( $page->post_title );
	$result = wp_update_post( [
		'ID'           => $id,
		'post_content' => $content,
	], true );
	if ( is_wp_error( $result ) ) {
		echo "FAIL $id ($slug): " . $result->get_error_message() . PHP_EOL;
	} else {
		update_post_meta( $id, '_wp_page_template', 'template-treatment.php' );
		$migrated_ids[] = $id;
		echo "OK $id ($slug)" . PHP_EOL;
	}
}
echo 'IDS: ' . implode( ',', $migrated_ids ) . PHP_EOL;
