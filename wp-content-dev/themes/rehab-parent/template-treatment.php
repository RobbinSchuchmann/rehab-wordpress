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

		<?php
		// Breadcrumb category between "Treatments" and the page title.
		// Resolution priority:
		//   1. Per-page override meta (_rehab_breadcrumb_category)
		//   2. Rank Math primary category (editor-chosen)
		//   3. First assigned `category` taxonomy term
		//   4. Slug-based inference (last-resort)
		$crumb_cat = rehab_breadcrumb_category( $current_id );

		// Some pages borrow this template purely for its block layout but aren't
		// actually treatments (e.g. a location or funding explainer). Flag them
		// with `_rehab_landing_page` to get a plain "Home / Title" breadcrumb and
		// skip the "Other treatments" cross-links below.
		$is_landing = (bool) get_post_meta( $current_id, '_rehab_landing_page', true );
		?>
		<nav class="rehab-breadcrumb" aria-label="Breadcrumb">
			<div class="rehab-container">
				<ol class="rehab-breadcrumb__list">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
					<?php if ( ! $is_landing ) : ?>
						<li><a href="<?php echo esc_url( home_url( '/all-treatments/' ) ); ?>">Treatments</a></li>
						<?php if ( $crumb_cat ) : ?>
							<li><a href="<?php echo esc_url( rehab_breadcrumb_category_url( $crumb_cat ) ); ?>"><?php echo esc_html( $crumb_cat ); ?></a></li>
						<?php endif; ?>
					<?php endif; ?>
					<li aria-current="page"><?php the_title(); ?></li>
				</ol>
			</div>
		</nav>

		<?php the_content(); ?>

		<?php
		// "Other treatments" cross-link section is suppressed when the page's
		// content already ends with a final-CTA / contact-form block or a dark
		// closing concierge band — those pages own their full bottom-of-page
		// experience (incl. in-content related-programs cards).
		$page_content = get_the_content();
		$has_own_final = (
			false !== strpos( $page_content, 'wp:rehab/final-cta' )
			|| ( false !== strpos( $page_content, 'wp:rehab/cta-band' ) && false !== strpos( $page_content, '"background":"dark"' ) )
		);
		$related = ( $has_own_final || $is_landing ) ? [] : get_posts( [
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
