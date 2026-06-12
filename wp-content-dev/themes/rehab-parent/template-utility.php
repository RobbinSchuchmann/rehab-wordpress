<?php
/**
 * Template Name: Utility / Policy
 * Description: Light-touch readable rendering for legal and utility pages
 * (privacy / confidentiality / policies, intake-form intros). No bespoke
 * design, it just drops the page's existing content into the standard prose
 * column so long-form legal text reads in a comfortable measure instead of
 * spanning the full viewport. Content is untouched; only the wrapper changes.
 *
 * @package RehabParent
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="rehab-prose rehab-bg-white rehab-prose--text rehab-section">
		<div class="rehab-container rehab-container--text">
			<h1 class="wp-block-heading rehab-utility__title"><?php the_title(); ?></h1>
			<div class="rehab-prose__inner">
				<?php the_content(); ?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
