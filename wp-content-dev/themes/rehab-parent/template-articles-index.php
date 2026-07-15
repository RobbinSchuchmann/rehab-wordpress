<?php
/**
 * Template Name: Articles Index
 * Description: Listing template for the all-articles page. Pulls every page
 * that uses template-article.php and shows a card grid with pagination.
 *
 * @package RehabParent
 */

get_header();

$paged    = max( 1, get_query_var( 'paged' ) ?: ( get_query_var( 'page' ) ?: 1 ) );
$per_page = 24;

$articles_query = new WP_Query( [
	'post_type'      => 'page',
	'posts_per_page' => $per_page,
	'paged'          => $paged,
	'meta_key'       => '_wp_page_template',
	'meta_value'     => 'template-article.php',
	// Alphabetical by title — a scannable order, vs the previous 'modified DESC'
	// which read as random to visitors (REH-51).
	'orderby'        => 'title',
	'order'          => 'ASC',
] );

while ( have_posts() ) :
	the_post();
	rehab_render_breadcrumb();
	?>
	<section class="rehab-articles-index__hero rehab-bg-sage-mist">
		<div class="rehab-container rehab-container--narrow">
			<div class="rehab-cta__inner">
				<h1 class="rehab-heading rehab-heading--lg"><?php the_title(); ?></h1>
				<p class="rehab-cta__body">Evidence-led writing on addiction, recovery, mental health, and the science behind effective treatment. Reviewed by our clinical team.</p>
			</div>
		</div>
	</section>
	<?php
endwhile;
?>

<section class="rehab-articles-index rehab-bg-white">
	<div class="rehab-container">
		<?php if ( $articles_query->have_posts() ) : ?>
			<ul class="rehab-articles-index__grid">
				<?php
				while ( $articles_query->have_posts() ) :
					$articles_query->the_post();
					$thumb   = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
					$content = get_the_content();
					$excerpt = wp_trim_words( wp_strip_all_tags( $content ), 22, '…' );
					$reading = max( 1, (int) round( str_word_count( wp_strip_all_tags( $content ) ) / 220 ) );
					?>
					<li class="rehab-articles-index__card">
						<a href="<?php the_permalink(); ?>" class="rehab-articles-index__card-link">
							<?php if ( $thumb ) : ?>
								<div class="rehab-articles-index__thumb">
									<img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy" decoding="async">
								</div>
							<?php endif; ?>
							<div class="rehab-articles-index__body">
								<h2 class="rehab-articles-index__title"><?php the_title(); ?></h2>
								<p class="rehab-articles-index__meta">
									<span><?php echo esc_html( $reading ); ?> min read</span>
									<span aria-hidden="true">·</span>
									<span>Updated <?php echo esc_html( get_the_modified_date( 'M Y' ) ); ?></span>
								</p>
								<p class="rehab-articles-index__excerpt"><?php echo esc_html( $excerpt ); ?></p>
								<span class="rehab-articles-index__read-more">Read article →</span>
							</div>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php
			$total = $articles_query->max_num_pages;
			if ( $total > 1 ) :
				$big = 999999999;
				echo '<nav class="rehab-articles-index__pagination" aria-label="Article pagination">';
				echo paginate_links( [
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'    => '?paged=%#%',
					'current'   => $paged,
					'total'     => $total,
					'prev_text' => '← Newer',
					'next_text' => 'Older →',
					'mid_size'  => 1,
				] );
				echo '</nav>';
			endif;
			wp_reset_postdata();
			?>
		<?php else : ?>
			<p>No articles published yet.</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
