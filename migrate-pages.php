<?php
/**
 * Migrate Diamond's static pages to use the new block system.
 * Run via: wp eval-file /var/www/html/migrate-pages.php
 */

$migrations = [
	834 => [
		'title'   => 'Cost',
		'content' => <<<'BLOCKS'
<!-- wp:rehab/cta {"variant":"compact","background":"sage-mist","heading":"Cost & insurance — what's included","buttonText":"Speak with admissions","helper":"Free, confidential, no-obligation."} /-->

<!-- wp:rehab/prose {"width":"text"} -->
<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner"><!-- wp:heading {"level":2} --><h2>What your investment includes</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Replace this with the detailed cost breakdown — assessments, sessions, accommodations, dining, activities, aftercare, etc.</p><!-- /wp:paragraph --></div></div></section>
<!-- /wp:rehab/prose -->

<!-- wp:rehab/programs-list /-->

<!-- wp:rehab/comparison /-->

<!-- wp:rehab/faq /-->

<!-- wp:rehab/cta /-->
BLOCKS,
	],
	722 => [
		'title'   => 'Team',
		'content' => <<<'BLOCKS'
<!-- wp:rehab/cta {"variant":"compact","background":"sage-mist","heading":"Meet the team behind your recovery","body":"Our therapists, counsellors, and staff bring decades of combined clinical experience.","buttonText":"Speak with us","helper":"Free, confidential, no-obligation."} /-->

<!-- wp:rehab/founder-bio /-->

<!-- wp:rehab/team /-->

<!-- wp:rehab/cta /-->
BLOCKS,
	],
	1189 => [
		'title'   => 'Contact us',
		'content' => <<<'BLOCKS'
<!-- wp:rehab/cta {"variant":"compact","background":"sage-mist","heading":"Get in touch — 24/7","body":"Free, confidential, no-obligation. We respond within an hour, day or night.","buttonText":"Call our team"} /-->

<!-- wp:rehab/contact-form /-->

<!-- wp:rehab/map /-->

<!-- wp:rehab/phone-cta {"background":"charcoal"} /-->
BLOCKS,
	],
	1197 => [
		'title'   => 'Frequently asked questions',
		'content' => <<<'BLOCKS'
<!-- wp:rehab/cta {"variant":"compact","background":"sage-mist","heading":"Frequently asked questions","body":"Common questions we hear from families and clients."} /-->

<!-- wp:rehab/faq {"heading":"Privacy & confidentiality"} /-->
<!-- wp:rehab/faq {"heading":"Treatment & program"} /-->
<!-- wp:rehab/faq {"heading":"Cost & insurance"} /-->

<!-- wp:rehab/cta /-->
BLOCKS,
	],
	825 => [
		'title'   => 'Why us',
		'content' => <<<'BLOCKS'
<!-- wp:rehab/cta {"variant":"compact","background":"sage-mist","heading":"Why The Diamond Rehab","body":"What sets us apart from traditional rehab.","buttonText":"Check availability"} /-->

<!-- wp:rehab/comparison /-->

<!-- wp:rehab/features-list {"heading":"Our standard","subheading":"Doctor-led, evidence-based, with absolute discretion.","columns":3} -->
<section class="wp-block-rehab-features-list rehab-features rehab-bg-white rehab-features--cols-3"><div class="rehab-container"><header class="rehab-features__header"><h2 class="rehab-heading rehab-heading--lg">Our standard</h2><p class="rehab-features__subheading">Doctor-led, evidence-based, with absolute discretion.</p></header><div class="rehab-features__grid"><!-- wp:rehab/feature {"icon":"⚕","title":"Medical excellence","body":"On-site psychiatrist + 24/7 nursing team."} /--><!-- wp:rehab/feature {"icon":"☘","title":"Personalised plans","body":"Treatment designed around your unique needs."} /--><!-- wp:rehab/feature {"icon":"♥","title":"Lifetime aftercare","body":"Ongoing support and relapse prevention."} /--></div></div></section>
<!-- /wp:rehab/features-list -->

<!-- wp:rehab/testimonials /-->

<!-- wp:rehab/cta /-->
BLOCKS,
	],
];

foreach ( $migrations as $id => $data ) {
	$result = wp_update_post( [
		'ID'           => $id,
		'post_content' => $data['content'],
		'post_title'   => $data['title'],
	], true );
	if ( is_wp_error( $result ) ) {
		echo "FAIL $id: " . $result->get_error_message() . PHP_EOL;
	} else {
		echo "OK $id ({$data['title']})" . PHP_EOL;
	}
}
