<?php
/**
 * Default template fallback. The <main id="main"> is opened by header.php
 * and closed by footer.php — templates only emit body content.
 *
 * @package RehabParent
 */

get_header();
?>
<div class="rehab-fallback rehab-container">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'rehab-article' ); ?>>
				<header class="rehab-article__header">
					<h1 class="rehab-article__title"><?php the_title(); ?></h1>
				</header>
				<div class="rehab-article__content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
	else :
		echo '<p>' . esc_html__( 'Nothing here.', 'rehab-parent' ) . '</p>';
	endif;
	?>
</div>
<?php
get_footer();
