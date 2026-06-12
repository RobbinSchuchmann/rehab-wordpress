<?php
/**
 * Editorial article layout — shared between single.php and template-article.php.
 *
 * The page is split into two columns: a long-form article column (eyebrow,
 * title, meta strip, author + reviewer credits, featured image, prose body,
 * inline "talk to a clinician" CTA) and a sticky sidebar (CTA card + related
 * list). Table of contents is handled inline by the Easy Table of Contents
 * plugin, which inserts itself into the_content — so we don't render one here.
 *
 * Assumes `the_post()` has already been called by the caller.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id         = get_the_ID();
$content         = get_the_content();
$reading_minutes = max( 1, (int) round( str_word_count( wp_strip_all_tags( $content ) ) / 220 ) );

// Legacy pages often duplicate the featured image as the first content block.
// When the post has a featured image, strip a leading <figure>/wp:image block
// that points at the same image so we don't render it twice.
$thumb_id = get_post_thumbnail_id( $post_id );
if ( $thumb_id ) {
	$content = rehab_parent_strip_leading_duplicate_image( $content, (int) $thumb_id );
}

$author_id   = (int) get_post_meta( $post_id, '_rehab_author_member', true );
$reviewer_id = (int) get_post_meta( $post_id, '_rehab_reviewer_member', true );
$hide_rev    = (bool) get_post_meta( $post_id, 'hide_medical_reviewer', true );
if ( $hide_rev ) {
	$reviewer_id = 0;
}

$reviewed_label = get_the_modified_date( 'M j, Y' );
$category_label = function_exists( 'rehab_breadcrumb_category' )
	? rehab_breadcrumb_category( $post_id )
	: '';
$related_posts  = rehab_parent_resolve_related( $post_id, 4 );
?>

<article <?php post_class( 'rehab-article' ); ?>>

	<?php if ( $category_label ) : ?>
		<nav class="rehab-article__crumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'rehab-parent' ); ?>">
			<div class="rehab-container rehab-container--narrow">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'rehab-parent' ); ?></a>
				<span class="rehab-article__crumb-sep" aria-hidden="true">/</span>
				<a href="<?php echo esc_url( home_url( '/all-articles/' ) ); ?>"><?php esc_html_e( 'Articles', 'rehab-parent' ); ?></a>
				<span class="rehab-article__crumb-sep" aria-hidden="true">/</span>
				<span class="rehab-article__crumb-current"><?php echo esc_html( $category_label ); ?></span>
			</div>
		</nav>
	<?php endif; ?>

	<div class="rehab-article__main">
		<div class="rehab-container rehab-container--narrow">
			<div class="rehab-article__layout">

				<div class="rehab-article__column">

					<?php if ( $category_label ) : ?>
						<p class="rehab-article__eyebrow"><?php echo esc_html( $category_label ); ?></p>
					<?php endif; ?>

					<h1 class="rehab-article__title"><?php the_title(); ?></h1>

					<div class="rehab-article__meta">
						<span class="rehab-article__badge">
							<svg class="rehab-article__badge-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M12 2 4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5l-8-3z" />
								<path d="m9 12 2 2 4-4" />
							</svg>
							<span><?php
								printf(
									/* translators: %s reviewed date */
									esc_html__( 'Clinically reviewed · %s', 'rehab-parent' ),
									esc_html( $reviewed_label )
								);
							?></span>
						</span>

						<span class="rehab-article__meta-item">
							<svg class="rehab-article__meta-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<circle cx="12" cy="12" r="9" />
								<polyline points="12 7 12 12 16 14" />
							</svg>
							<?php
							echo esc_html(
								sprintf(
									/* translators: %d minutes to read */
									_n( '%d min read', '%d min read', $reading_minutes, 'rehab-parent' ),
									$reading_minutes
								)
							);
							?>
						</span>

						<span class="rehab-article__meta-item rehab-article__meta-item--quiet">
							<?php
							/* translators: %s = last-updated date */
							printf( esc_html__( '· Updated %s', 'rehab-parent' ), esc_html( $reviewed_label ) );
							?>
						</span>
					</div>

					<?php if ( $author_id || $reviewer_id ) : ?>
						<div class="rehab-article__credit">
							<?php
							if ( $author_id ) {
								rehab_parent_render_credit_cell( $author_id, __( 'Author', 'rehab-parent' ) );
							}
							if ( $reviewer_id ) {
								rehab_parent_render_credit_cell( $reviewer_id, __( 'Medical reviewer', 'rehab-parent' ) );
							}
							?>
						</div>
					<?php endif; ?>

					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="rehab-article__featured">
							<div class="rehab-article__featured-image">
								<?php
								the_post_thumbnail(
									'large',
									[
										'class'   => 'rehab-article__featured-img',
										'loading' => 'eager',
										'fetchpriority' => 'high',
									]
								);
								?>
							</div>
						</figure>
					<?php endif; ?>

					<div class="rehab-article__body rehab-prose__inner">
						<?php
						$content_with_ids = rehab_parent_inject_heading_ids( apply_filters( 'the_content', $content ) );
						echo $content_with_ids; // phpcs:ignore WordPress.Security.EscapeOutput
						?>
					</div>

					<div class="rehab-article__talk" role="complementary">
						<div class="rehab-article__talk-icon" aria-hidden="true">
							<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 12a8 8 0 1 1-3.2-6.4L21 4l-1 4.4A7.9 7.9 0 0 1 21 12z" />
							</svg>
						</div>
						<div class="rehab-article__talk-text">
							<h2 class="rehab-article__talk-title"><?php esc_html_e( 'Wondering if this sounds like someone you love?', 'rehab-parent' ); ?></h2>
							<p class="rehab-article__talk-sub"><?php esc_html_e( 'A short, confidential call with our admissions team — no pressure, no obligation.', 'rehab-parent' ); ?></p>
						</div>
						<a class="rehab-btn rehab-btn--luxury rehab-article__talk-btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">
							<?php esc_html_e( 'Talk to a clinician', 'rehab-parent' ); ?>
						</a>
					</div>

				</div>

				<aside class="rehab-article__sidebar" aria-label="<?php esc_attr_e( 'Resources', 'rehab-parent' ); ?>">
					<div class="rehab-article__sidebar-inner">

						<div class="rehab-article__cta">
							<div class="rehab-article__cta-media">
								<span class="rehab-article__cta-overline"><?php esc_html_e( 'A Sanctuary of Serenity', 'rehab-parent' ); ?></span>
								<h2 class="rehab-article__cta-title"><?php
									printf(
										/* translators: %s = italicised "admissions team" */
										esc_html__( 'Speak with our %s', 'rehab-parent' ),
										'<em>' . esc_html__( 'admissions team', 'rehab-parent' ) . '</em>'
									);
								?></h2>
							</div>
							<div class="rehab-article__cta-body">
								<p class="rehab-article__cta-copy"><?php esc_html_e( 'Reach out for a confidential, no-obligation conversation with a clinician on our team. We\'re here to listen, not to sell.', 'rehab-parent' ); ?></p>
								<a class="rehab-btn rehab-btn--luxury rehab-article__cta-btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Ask a question', 'rehab-parent' ); ?></a>
								<p class="rehab-article__cta-helper"><?php esc_html_e( 'Free · confidential · no obligation', 'rehab-parent' ); ?></p>
							</div>
						</div>

						<?php if ( $related_posts ) : ?>
							<div class="rehab-article__related-side">
								<p class="rehab-article__related-eyebrow"><?php esc_html_e( 'Continue reading', 'rehab-parent' ); ?></p>
								<ol class="rehab-article__related-list">
									<?php foreach ( $related_posts as $i => $rp ) :
										$rp_words = str_word_count( wp_strip_all_tags( $rp->post_content ) );
										$rp_mins  = max( 1, (int) round( $rp_words / 220 ) );
										$rp_cat   = function_exists( 'rehab_breadcrumb_category' )
											? rehab_breadcrumb_category( $rp->ID )
											: '';
										?>
										<li class="rehab-article__related-item">
											<span class="rehab-article__related-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
											<a class="rehab-article__related-link" href="<?php echo esc_url( get_permalink( $rp ) ); ?>">
												<span class="rehab-article__related-title"><?php echo esc_html( get_the_title( $rp ) ); ?></span>
												<span class="rehab-article__related-meta">
													<?php
													/* translators: %d minutes */
													printf( esc_html__( '%d min', 'rehab-parent' ), $rp_mins );
													if ( $rp_cat ) {
														echo ' · ' . esc_html( $rp_cat );
													}
													?>
												</span>
											</a>
										</li>
									<?php endforeach; ?>
								</ol>
							</div>
						<?php endif; ?>

					</div>
				</aside>

			</div>
		</div>
	</div>

</article>
