<?php
/**
 * Template Name: Homepage (Blocks)
 *
 * Block-driven twin of page-homepage.php. The homepage is now authored as
 * editable `rehab/home-*` blocks in the editor; this template renders that
 * block content inside the same `.drt-homepage` wrapper and critical
 * typography the hardcoded template used, so the result is pixel-identical
 * while being fully editable. The drt- CSS/JS bundle is enqueued from
 * functions.php (drt_homepage_assets) for this template.
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
		font-family: "Playfair Display", Georgia, serif !important;
		font-weight: 300 !important;
	}

	/* Accordion trigger text uses Ivymode for Programs & FAQ (per Loveable) */
	.drt-homepage .drt-accordion__trigger-text {
		font-family: "Playfair Display", Georgia, serif !important;
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
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>

<?php get_footer(); ?>
