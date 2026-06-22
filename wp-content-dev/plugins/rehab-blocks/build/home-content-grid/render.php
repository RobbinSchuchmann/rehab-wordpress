<?php
/**
 * Server-side render for `rehab/home-content-grid`.
 *
 * Homepage "Further reading" grid: a heading plus a repeatable set of
 * article cards (image + title + link). Emits the same drt- markup as the
 * legacy section-content-grid.php template-part.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a        = $attributes;
$articles = is_array( $a['articles'] ?? null ) ? $a['articles'] : [];

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-content-grid drt-bg-white drt-section--lg',
	'aria-label' => 'Further reading',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container drt-container--narrow">
		<div class="drt-section-header">
			<h2 class="drt-heading drt-heading--md">
				<?php echo wp_kses_post( $a['heading'] ); ?>
			</h2>
		</div>

		<div class="drt-content-grid__grid">
			<?php
			foreach ( $articles as $article ) :
				$article = array_merge( [ 'title' => '', 'imageUrl' => '', 'link' => '' ], (array) $article );
				$img     = '' !== $article['imageUrl']
					? $article['imageUrl']
					: get_stylesheet_directory_uri() . '/assets/images/homepage/content-grid/placeholder.webp';
				?>
				<a href="<?php echo esc_url( $article['link'] ?: '#' ); ?>" class="drt-card--article">
					<div class="drt-card--article__image">
						<img
							src="<?php echo esc_url( $img ); ?>"
							alt="<?php echo esc_attr( $article['title'] ); ?>"
							loading="lazy"
						>
					</div>
					<h3 class="drt-card--article__title"><?php echo esc_html( $article['title'] ); ?></h3>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
