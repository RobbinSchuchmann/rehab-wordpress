<?php
/**
 * Template Name: Article
 * Description: Blog-style article rendering for pages with long-form content.
 * Reuses single.php's chrome — hero, ToC sidebar, related, CTA — but for the
 * `page` post type (Diamond stores their 600+ articles as pages, not posts).
 *
 * @package RehabParent
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id         = get_the_ID();
	$content         = get_the_content();
	$reading_minutes = max( 1, (int) round( str_word_count( wp_strip_all_tags( $content ) ) / 220 ) );
	$author_id       = (int) get_post_meta( $post_id, '_rehab_author_member', true );
	$reviewer_id     = (int) get_post_meta( $post_id, '_rehab_reviewer_member', true );
	$last_edited     = get_the_modified_date( 'Y-m-d' );
	$toc             = rehab_parent_extract_toc( $content );
	?>

<article <?php post_class( 'rehab-article' ); ?>>

	<header class="rehab-article__hero rehab-bg-cream">
		<div class="rehab-container rehab-container--narrow">
			<h1 class="rehab-article__title"><?php the_title(); ?></h1>

			<div class="rehab-article__meta">
				<span class="rehab-article__reading-time">
					<?php
					echo esc_html( sprintf( _n( '%d min read', '%d min read', $reading_minutes, 'rehab-parent' ), $reading_minutes ) );
					?>
				</span>
				<?php if ( $last_edited ) : ?>
					<span class="rehab-article__updated">
						<?php printf( esc_html__( 'Updated %s', 'rehab-parent' ), esc_html( $last_edited ) ); ?>
					</span>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<?php if ( $author_id || $reviewer_id ) : ?>
		<div class="rehab-article__authors-wrap">
			<div class="rehab-container rehab-container--narrow">
				<div class="rehab-article__authors">
					<?php
					if ( $author_id ) {
						rehab_parent_render_author_box( $author_id, __( 'Author', 'rehab-parent' ) );
					}
					if ( $reviewer_id ) {
						rehab_parent_render_author_box( $reviewer_id, __( 'Medical reviewer', 'rehab-parent' ) );
					}
					?>
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

	<section class="rehab-article__final-cta rehab-bg-sage-mist">
		<div class="rehab-container rehab-container--narrow">
			<div class="rehab-cta__inner">
				<h2 class="rehab-heading rehab-heading--md">Ready to start your recovery?</h2>
				<a class="rehab-btn rehab-btn--luxury" href="/contact-us/">Speak with admissions</a>
				<p class="rehab-cta__helper">Free, confidential, and no-obligation.</p>
			</div>
		</div>
	</section>

</article>
<?php
endwhile;

get_footer();
