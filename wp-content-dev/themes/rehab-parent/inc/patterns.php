<?php
/**
 * Block patterns: pre-arranged compositions of blocks. Writers drop a pattern
 * and fill in content. One pattern per page-archetype.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	static function (): void {
		register_block_pattern_category( 'rehab', [ 'label' => __( 'Rehab', 'rehab-parent' ) ] );

		// Treatment Page pattern — typical addiction/treatment landing.
		register_block_pattern(
			'rehab/treatment-page',
			[
				'title'       => __( 'Treatment Page', 'rehab-parent' ),
				'description' => __( 'Hero + intro + features + treatment grid + FAQ + CTA. Use for individual addiction/treatment pages.', 'rehab-parent' ),
				'categories'  => [ 'rehab' ],
				'content'     => rehab_parent_pattern_treatment(),
			]
		);

		// Homepage pattern — flagship homepage layout.
		register_block_pattern(
			'rehab/homepage',
			[
				'title'       => __( 'Homepage', 'rehab-parent' ),
				'description' => __( 'Hero + marquee + founder + features + comparison + cards + cta + team + testimonials + faq + map + final cta. Flagship homepage layout.', 'rehab-parent' ),
				'categories'  => [ 'rehab' ],
				'content'     => rehab_parent_pattern_homepage(),
			]
		);

		// Blog Article pattern — minimal, since single.php handles most of it.
		// Just a starter with prose + CTA.
		register_block_pattern(
			'rehab/article-starter',
			[
				'title'       => __( 'Article Starter', 'rehab-parent' ),
				'description' => __( 'Prose body + final CTA. Use for new blog/article posts when you want pre-styled prose.', 'rehab-parent' ),
				'categories'  => [ 'rehab' ],
				'content'     => rehab_parent_pattern_article_starter(),
			]
		);
	}
);

function rehab_parent_pattern_treatment(): string {
	return <<<'BLOCKS'
<!-- wp:rehab/cta {"variant":"compact","background":"sage-mist","heading":"Confidential, doctor-led addiction treatment","buttonText":"Speak with admissions","helper":"Free, confidential, no-obligation."} /-->

<!-- wp:rehab/prose {"width":"text"} -->
<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner"><!-- wp:heading {"level":2} --><h2>What this treatment involves</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Replace this with a clear, plain-language overview of the addiction and how the program treats it.</p><!-- /wp:paragraph --></div></div></section>
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
}

function rehab_parent_pattern_homepage(): string {
	return <<<'BLOCKS'
<!-- wp:rehab/hero /-->
<!-- wp:rehab/marquee /-->
<!-- wp:rehab/features-list {"heading":"Our clinical approach","subheading":"Evidence-based treatment in a serene setting.","columns":3} -->
<section class="wp-block-rehab-features-list rehab-features rehab-bg-white rehab-features--cols-3"><div class="rehab-container"><header class="rehab-features__header"><h2 class="rehab-heading rehab-heading--lg">Our clinical approach</h2><p class="rehab-features__subheading">Evidence-based treatment in a serene setting.</p></header><div class="rehab-features__grid"><!-- wp:rehab/feature {"icon":"⚕","title":"Doctor-led care","body":"On-site psychiatrist, medical doctors, 24/7 nursing."} /--><!-- wp:rehab/feature {"icon":"☘","title":"Holistic therapies","body":"Yoga, mindfulness, breathwork, acupuncture."} /--><!-- wp:rehab/feature {"icon":"♥","title":"Lifetime aftercare","body":"Comprehensive aftercare and ongoing support."} /--></div></div></section>
<!-- /wp:rehab/features-list -->
<!-- wp:rehab/comparison /-->
<!-- wp:rehab/founder-bio /-->
<!-- wp:rehab/cards-grid /-->
<!-- wp:rehab/cta {"variant":"compact","background":"sage-mist","heading":"World-class addiction recovery in a 5-star private setting","buttonText":"Check availability","helper":"Free, confidential, and no-obligation."} /-->
<!-- wp:rehab/programs-list /-->
<!-- wp:rehab/accommodation /-->
<!-- wp:rehab/steps /-->
<!-- wp:rehab/team /-->
<!-- wp:rehab/testimonials /-->
<!-- wp:rehab/article-feed /-->
<!-- wp:rehab/faq /-->
<!-- wp:rehab/map /-->
<!-- wp:rehab/cta /-->
BLOCKS;
}

function rehab_parent_pattern_article_starter(): string {
	return <<<'BLOCKS'
<!-- wp:rehab/prose -->
<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner"><!-- wp:paragraph --><p>Start writing your article here. Use Heading 2 for sections so they appear in the auto-generated table of contents.</p><!-- /wp:paragraph --></div></div></section>
<!-- /wp:rehab/prose -->

<!-- wp:rehab/cta {"variant":"compact","heading":"Ready to take the next step?","buttonText":"Speak with admissions"} /-->
BLOCKS;
}
