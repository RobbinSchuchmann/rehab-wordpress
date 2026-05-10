<?php
/**
 * Default page template. Block-composed pages render here. The <main> is
 * opened by header.php and closed by footer.php; we just emit the_content()
 * which contains the writer's blocks.
 *
 * @package RehabParent
 */

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
