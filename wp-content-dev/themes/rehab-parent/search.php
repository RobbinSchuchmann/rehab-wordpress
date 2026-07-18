<?php
/**
 * Search results page.
 *
 * @package RehabParent
 */

get_header();
$query  = get_search_query();
$found  = (int) ( $GLOBALS['wp_query']->found_posts ?? 0 );
?>
<section class="rehab-search">
	<div class="rehab-container rehab-container--narrow">
		<h1 class="rehab-search__heading">
			<?php
			if ( $query ) {
				printf( esc_html__( '%1$s — %2$d results', 'rehab-parent' ), esc_html( $query ), $found );
			} else {
				esc_html_e( 'Search', 'rehab-parent' );
			}
			?>
		</h1>

		<form class="rehab-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" name="s" value="<?php echo esc_attr( $query ); ?>" placeholder="Search articles…" aria-label="Search">
			<button type="submit" class="rehab-btn rehab-btn--luxury">Search</button>
		</form>

		<?php if ( have_posts() ) : ?>
			<ol class="rehab-search__results">
				<?php while ( have_posts() ) : the_post(); ?>
					<li class="rehab-search__result">
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="rehab-search__result-url"><?php echo esc_html( str_replace( home_url(), '', get_permalink() ) ); ?></p>
						<p class="rehab-search__result-excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 28, '…' ) ); ?></p>
					</li>
				<?php endwhile; ?>
			</ol>
			<?php
			// REH-158: deeper pages were unreachable — no pagination existed.
			the_posts_pagination(
				[
					'mid_size'  => 2,
					'prev_text' => '‹ Previous',
					'next_text' => 'Next ›',
					'class'     => 'rehab-search__pagination',
				]
			);
			?>
		<?php else : ?>
			<p class="rehab-search__none">No results for "<?php echo esc_html( $query ); ?>". Try a different keyword, or browse <a href="<?php echo esc_url( home_url( '/all-articles/' ) ); ?>">all articles</a>.</p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
