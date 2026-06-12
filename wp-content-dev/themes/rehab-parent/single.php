<?php
/**
 * Single blog post template — uses the shared editorial article layout
 * (see template-parts/article-page.php for the markup).
 *
 * @package RehabParent
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/article-page' );
endwhile;

get_footer();
