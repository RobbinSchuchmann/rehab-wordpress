<?php
/**
 * Server-side render for `rehab/home-final-cta`.
 *
 * Homepage closing call-to-action: heading + body + button + helper. Emits the
 * same drt- markup as the legacy section-final-cta.php template-part.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a   = $attributes;
$bg  = sanitize_html_class( $a['background'] ?: 'sage-mist' );
$cls = 'drt-final-cta drt-bg-' . $bg;

$wrapper = get_block_wrapper_attributes( [
	'class'      => $cls,
	'aria-label' => 'Final call to action',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container drt-container--narrow">
		<div class="drt-final-cta__inner">
			<?php if ( '' !== $a['heading'] ) : ?>
				<h2 class="drt-heading drt-heading--lg drt-text-balance"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $a['body'] ) : ?>
				<p class="drt-body"><?php echo wp_kses_post( $a['body'] ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $a['buttonText'] ) : ?>
				<a href="<?php echo esc_url( $a['buttonUrl'] ?: '#' ); ?>" class="drt-btn drt-btn--luxury"><?php echo wp_kses_post( $a['buttonText'] ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $a['helper'] ) : ?>
				<p class="drt-final-cta__helper"><?php echo wp_kses_post( $a['helper'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
