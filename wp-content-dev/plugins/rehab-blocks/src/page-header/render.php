<?php
/**
 * Server-side render for `rehab/page-header`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a = $attributes;

$classes = 'rehab-page-header rehab-bg-' . sanitize_html_class( $a['background'] ?: 'white' );
if ( 'left' === ( $a['align'] ?? 'center' ) ) {
	$classes .= ' rehab-page-header--left';
}
$wrapper = get_block_wrapper_attributes( [ 'class' => $classes ] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-page-header__copy">
			<?php if ( '' !== $a['eyebrow'] ) : ?>
				<span class="rehab-page-header__eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
			<?php endif; ?>
			<h1 class="rehab-page-header__heading"><?php echo wp_kses_post( $a['heading'] ); ?></h1>
			<?php if ( '' !== $a['lede'] ) : ?>
				<p class="rehab-page-header__lede"><?php echo wp_kses_post( $a['lede'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( '' !== ( $a['imageUrl'] ?? '' ) ) : ?>
			<div class="rehab-page-header__media">
				<img src="<?php echo esc_url( $a['imageUrl'] ); ?>" alt="<?php echo esc_attr( $a['imageAlt'] ?? '' ); ?>" loading="eager" decoding="async" />
			</div>
		<?php endif; ?>
	</div>
</section>
