<?php
/**
 * Template Name: Homepage Redesign
 *
 * Full-page homepage template for The Diamond Rehab Thailand.
 * Uses hardcoded content with custom CSS/JS — no ACF dependency.
 *
 * @package DiamondRehab
 */

get_header(); ?>

<!-- Load Playfair Display from Google Fonts (same fallback as Loveable) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">

<!-- Critical typography overrides (inline to guarantee highest specificity) -->
<style id="drt-critical-typography">
	/* Force headings and headline elements to use Ivymode Light (weight 300) */
	.drt-homepage h1,
	.drt-homepage h2,
	.drt-homepage h3,
	.drt-homepage h4,
	.drt-homepage h5,
	.drt-homepage h6,
	.drt-homepage .drt-heading,
	.drt-homepage .drt-hero__headline,
	.drt-homepage .drt-approach__title,
	.drt-homepage .drt-founder__quote,
	.drt-homepage .drt-clinical__value,
	.drt-homepage .drt-clinical__label,
	.drt-homepage .drt-card--treatment__title {
		font-family: Ivymode, Georgia, serif !important;
		font-weight: 300 !important;
	}

	/* Accordion trigger text uses Ivymode for Programs & FAQ (per Loveable) */
	.drt-homepage .drt-accordion__trigger-text {
		font-family: 'ivymode', Georgia, serif !important;
		font-weight: 300 !important;
	}

	.drt-homepage .drt-comparison__th {
		font-family: 'Inter', sans-serif !important;
		font-weight: 500 !important;
	}

	/* Force body text to Inter Light */
	.drt-homepage {
		font-family: 'Inter', system-ui, sans-serif !important;
		font-weight: 300 !important;
	}

	.drt-homepage p,
	.drt-homepage li,
	.drt-homepage a,
	.drt-homepage .drt-body {
		font-weight: 300 !important;
	}

	/* Eyebrows have medium weight */
	.drt-homepage .drt-eyebrow {
		font-weight: 500 !important;
	}

	/* Buttons use light weight */
	.drt-homepage .drt-btn {
		font-weight: 300 !important;
	}
</style>

<main class="drt-homepage" id="main-content">

	<?php get_template_part( 'template-parts/homepage/section', 'hero' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'authority-ribbon' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'diamond-approach' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'comparison-table' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'clinical-excellence' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'conversion-bridge' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'founder-vision' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'immersive-tour' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'separator' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'what-we-treat' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'conversion-bridge' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'testimonials' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'testimonials-cta' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'recovery-journey' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'team-carousel' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'team-cta-bridge' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'accommodation-amenities' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'separator' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'admissions-flowchart' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'conversion-bridge' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'media-social-proof' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'seo-faq' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'location' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'final-cta' ); ?>

	<?php get_template_part( 'template-parts/homepage/section', 'content-grid' ); ?>

</main>

<?php get_template_part( 'template-parts/homepage/section', 'mobile-sticky-footer' ); ?>

<?php get_footer(); ?>
