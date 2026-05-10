<?php
/**
 * Single blog post / article template.
 *
 * Layout: title hero, reading time + authors, sticky ToC sidebar, prose body,
 * related posts, CTA. Article body uses the prose typography system.
 *
 * @package RehabParent
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id        = get_the_ID();
	$content        = get_the_content();
	$reading_minutes = max( 1, (int) round( str_word_count( wp_strip_all_tags( $content ) ) / 220 ) );

	// Meta refs to team_member CPT for author + reviewer (added in Phase A5).
	$author_id   = (int) get_post_meta( $post_id, '_rehab_author_member', true );
	$reviewer_id = (int) get_post_meta( $post_id, '_rehab_reviewer_member', true );
	$last_edited = get_the_modified_date( 'Y-m-d' );

	// Auto-extract ToC from H2 headings in the content.
	$toc = rehab_parent_extract_toc( $content );
	?>

<article <?php post_class( 'rehab-article' ); ?>>

	<header class="rehab-article__hero rehab-bg-cream">
		<div class="rehab-container rehab-container--narrow">
			<?php
			$cats = get_the_category();
			if ( $cats ) :
				?>
				<p class="rehab-article__eyebrow">
					<?php echo esc_html( $cats[0]->name ); ?>
				</p>
			<?php endif; ?>

			<h1 class="rehab-article__title"><?php the_title(); ?></h1>

			<div class="rehab-article__meta">
				<span class="rehab-article__reading-time">
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
				<?php if ( $last_edited ) : ?>
					<span class="rehab-article__updated">
						<?php
						printf(
							/* translators: %s last-edited date */
							esc_html__( 'Updated %s', 'rehab-parent' ),
							esc_html( $last_edited )
						);
						?>
					</span>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<?php if ( $author_id || $reviewer_id ) : ?>
		<div class="rehab-article__authors-wrap">
			<div class="rehab-container rehab-container--narrow">
				<div class="rehab-article__authors">
					<?php if ( $author_id ) :
						rehab_parent_render_author_box( $author_id, __( 'Author', 'rehab-parent' ) );
					endif; ?>
					<?php if ( $reviewer_id ) :
						rehab_parent_render_author_box( $reviewer_id, __( 'Medical reviewer', 'rehab-parent' ) );
					endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="rehab-article__body-wrap">
		<div class="rehab-container">
			<div class="rehab-article__layout">

				<?php if ( $toc ) : ?>
					<aside class="rehab-article__toc" aria-label="<?php esc_attr_e( 'Table of contents', 'rehab-parent' ); ?>">
						<div class="rehab-article__toc-inner">
							<p class="rehab-article__toc-title"><?php esc_html_e( 'Table of contents', 'rehab-parent' ); ?></p>
							<ol class="rehab-article__toc-list">
								<?php foreach ( $toc as $item ) : ?>
									<li><a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a></li>
								<?php endforeach; ?>
							</ol>
						</div>
					</aside>
				<?php endif; ?>

				<div class="rehab-article__body rehab-prose__inner">
					<?php
					$content_with_ids = rehab_parent_inject_heading_ids( apply_filters( 'the_content', $content ) );
					echo $content_with_ids;
					?>
				</div>

			</div>
		</div>
	</div>

	<?php
	$related = new WP_Query( [
		'post_type'      => 'post',
		'posts_per_page' => 3,
		'post__not_in'   => [ $post_id ],
		'category__in'   => wp_list_pluck( get_the_category(), 'term_id' ),
		'orderby'        => 'rand',
	] );
	if ( $related->have_posts() ) :
		?>
		<section class="rehab-article__related rehab-bg-cream">
			<div class="rehab-container">
				<header class="rehab-article__related-header">
					<h2 class="rehab-heading rehab-heading--lg"><?php esc_html_e( 'Continue reading', 'rehab-parent' ); ?></h2>
				</header>
				<div class="rehab-article__related-grid">
					<?php while ( $related->have_posts() ) : $related->the_post(); ?>
						<a class="rehab-article__related-card" href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="rehab-article__related-image">
									<?php the_post_thumbnail( 'medium' ); ?>
								</div>
							<?php endif; ?>
							<h3 class="rehab-article__related-title"><?php the_title(); ?></h3>
						</a>
					<?php endwhile; ?>
				</div>
			</div>
		</section>
		<?php
		wp_reset_postdata();
	endif;
	?>

</article>
<?php
endwhile;

get_footer();
// Helpers (rehab_parent_extract_toc, rehab_parent_inject_heading_ids,
// rehab_parent_render_author_box) live in inc/article-helpers.php.
