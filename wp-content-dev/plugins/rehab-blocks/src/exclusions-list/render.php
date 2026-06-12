<?php
/**
 * Server-side render for `rehab/exclusions-list`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a     = $attributes;
$items = array_filter( (array) ( $a['items'] ?? [] ) );

$wrapper = get_block_wrapper_attributes( [
	'class' => 'rehab-exclusions rehab-bg-' . sanitize_html_class( $a['background'] ?: 'cream' ),
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-exclusions__head">
			<?php if ( '' !== $a['eyebrow'] ) : ?>
				<span class="rehab-exclusions__eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
			<?php endif; ?>
			<h2 class="rehab-exclusions__heading"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
			<?php if ( '' !== $a['lede'] ) : ?>
				<p class="rehab-exclusions__lede"><?php echo wp_kses_post( $a['lede'] ); ?></p>
			<?php endif; ?>
		</div>
		<div class="rehab-exclusions__grid">
			<?php foreach ( $items as $item ) : ?>
				<div class="rehab-exclusions__item"><span class="rehab-exclusions__x" aria-hidden="true"></span><?php echo esc_html( $item ); ?></div>
			<?php endforeach; ?>
		</div>
		<?php if ( '' !== $a['note'] ) : ?>
			<p class="rehab-exclusions__note"><span class="gem" aria-hidden="true">◆</span><?php echo wp_kses_post( $a['note'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
