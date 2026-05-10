<?php
/**
 * Template Name: Treatment Page
 * Description: Page template for individual treatment / addiction landing pages.
 * Adds breadcrumb + a "Related treatments" cross-link section. Uses the shared
 * block content body in between.
 *
 * @package RehabParent
 */

get_header();

while ( have_posts() ) :
	the_post();
	$current_id = get_the_ID();
	?>
	<div class="rehab-treatment">

		<nav class="rehab-breadcrumb" aria-label="Breadcrumb">
			<div class="rehab-container">
				<ol class="rehab-breadcrumb__list">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
					<li><a href="<?php echo esc_url( home_url( '/all-treatments/' ) ); ?>">Treatments</a></li>
					<li aria-current="page"><?php the_title(); ?></li>
				</ol>
			</div>
		</nav>

		<?php the_content(); ?>

		<?php
		$related = get_posts( [
			'post_type'      => 'page',
			'posts_per_page' => 4,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'template-treatment.php',
			'post__not_in'   => [ $current_id ],
			'orderby'        => 'modified',
			'order'          => 'DESC',
		] );
		if ( $related ) :
			?>
			<section class="rehab-related-treatments rehab-bg-cream">
				<div class="rehab-container">
					<h2 class="rehab-heading rehab-heading--md rehab-related-treatments__heading">Other treatments</h2>
					<ul class="rehab-related-treatments__list">
						<?php foreach ( $related as $rp ) : ?>
							<li class="rehab-related-treatments__item">
								<a href="<?php echo esc_url( get_permalink( $rp ) ); ?>">
									<span class="rehab-related-treatments__title"><?php echo esc_html( get_the_title( $rp ) ); ?></span>
									<span class="rehab-related-treatments__arrow" aria-hidden="true">→</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
			<?php
		endif;
		?>
	</div>
	<?php
endwhile;

get_footer();
