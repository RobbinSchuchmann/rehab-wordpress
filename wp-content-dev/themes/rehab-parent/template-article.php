<?php
/**
 * Template Name: Article
 * Description: Editorial blog rendering for long-form educational pages.
 * Diamond stores their 600+ articles as pages (not posts), so this template
 * gives those pages the same chrome as single.php.
 *
 * @package RehabParent
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/article-page' );
endwhile;

get_footer();
