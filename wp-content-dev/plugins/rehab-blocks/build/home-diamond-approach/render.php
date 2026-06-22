<?php
/**
 * Server-side render for `rehab/home-diamond-approach`.
 *
 * Centered homepage intro: eyebrow + heading + paragraph. Emits the same
 * drt- markup as the legacy section-diamond-approach.php template-part.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a   = $attributes;
$bg  = sanitize_html_class( $a['background'] ?: 'sage-mist' );
$cls = 'drt-approach drt-bg-' . $bg;

$wrapper = get_block_wrapper_attributes( [
	'class'      => $cls,
	'aria-label' => 'The Diamond Approach',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container">
		<div class="drt-approach__inner">
			<?php if ( '' !== $a['eyebrow'] ) : ?>
				<span class="drt-eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $a['heading'] ) : ?>
				<h2 class="drt-heading drt-heading--lg drt-approach__title"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $a['body'] ) : ?>
				<p class="drt-body drt-approach__text"><?php echo wp_kses_post( $a['body'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
