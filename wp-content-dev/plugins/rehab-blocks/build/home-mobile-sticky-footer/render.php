<?php
/**
 * Server-side render for `rehab/home-mobile-sticky-footer`.
 *
 * Mobile-only fixed bottom CTA bar. Emits the same drt- markup as the legacy
 * section-mobile-sticky-footer.php template-part. The id `drt-mobile-sticky`
 * and the `hidden` attribute are required by the homepage.js scroll handler
 * (initMobileStickyFooter) — do not change them.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a    = $attributes;
$aria = '' !== $a['ariaLabel'] ? $a['ariaLabel'] : 'Check availability';

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-mobile-sticky',
	'id'         => 'drt-mobile-sticky',
	'aria-label' => $aria,
	'hidden'     => '',
] );
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-mobile-sticky__inner">
		<a href="<?php echo esc_url( $a['ctaHref'] ); ?>" class="drt-btn drt-btn--luxury drt-mobile-sticky__btn">
			<?php echo wp_kses_post( $a['ctaLabel'] ); ?>
		</a>
	</div>
</div>
