<?php
/**
 * Server-side render for `rehab/cards-grid`. Mirrors src/cards-grid/save.js;
 * inner card blocks render themselves and arrive as $content.
 *
 * @var array    $attributes Block attributes (block.json defaults already merged).
 * @var string   $content    Rendered inner `rehab/card` blocks.
 * @var WP_Block $block      Block instance.
 */

$background  = $attributes['background'] ?? 'white';
$columns     = (int) ( $attributes['columns'] ?? 2 );
$card_layout = $attributes['cardLayout'] ?? 'horizontal';
$heading     = $attributes['heading']    ?? '';
$subheading  = $attributes['subheading'] ?? '';

$wrapper = get_block_wrapper_attributes( [
	'class' => sprintf(
		'rehab-cards-grid rehab-bg-%s rehab-cards-grid--cols-%d rehab-cards-grid--card-%s',
		$background,
		$columns,
		$card_layout
	),
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput — get_block_wrapper_attributes() returns escaped output. ?>>
	<div class="rehab-container">
		<?php if ( '' !== $heading || '' !== $subheading ) : ?>
			<header class="rehab-cards-grid__header">
				<?php if ( '' !== $heading ) : ?>
					<h2 class="rehab-heading rehab-heading--lg"><?php echo wp_kses_post( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $subheading ) : ?>
					<p class="rehab-cards-grid__subheading"><?php echo wp_kses_post( $subheading ); ?></p>
				<?php endif; ?>
			</header>
		<?php endif; ?>
		<div class="rehab-cards-grid__grid">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput — inner blocks are pre-rendered by core. ?>
		</div>
	</div>
</section>
