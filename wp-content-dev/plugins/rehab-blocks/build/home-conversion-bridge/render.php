<?php
/**
 * Server-side render for `rehab/home-conversion-bridge`.
 *
 * Reusable homepage CTA band: heading + button + helper line. Emits the same
 * drt- markup as the legacy section-conversion-bridge.php template-part.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a   = $attributes;
$bg  = sanitize_html_class( $a['background'] ?: 'sage-mist' );
$cls = 'drt-cta-bridge drt-bg-' . $bg;

$wrapper = get_block_wrapper_attributes( [
	'class'      => $cls,
	'aria-label' => 'Call to action',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container drt-container--text">
		<div class="drt-cta-bridge__inner">
			<?php if ( '' !== $a['heading'] ) : ?>
				<h2 class="drt-heading drt-heading--md drt-text-balance"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $a['buttonText'] ) : ?>
				<a href="<?php echo esc_url( $a['buttonUrl'] ?: '#' ); ?>" class="drt-btn drt-btn--luxury"><?php echo wp_kses_post( $a['buttonText'] ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $a['helper'] ) : ?>
				<p class="drt-cta-bridge__helper"><?php echo wp_kses_post( $a['helper'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
