<?php
/**
 * Server-side render for `rehab/cta-band`.
 *
 * Centered conversion band with three variants:
 *  - background "sage": mid-page conversion moment, dark button pops on sage.
 *  - background "dark": closing concierge band, sage + light buttons.
 *  - compact: action row only (no eyebrow/heading/lede), e.g. under steps.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a       = $attributes;
$bg      = in_array( $a['background'], [ 'sage', 'dark', 'none' ], true ) ? $a['background'] : 'sage';
$compact = ! empty( $a['compact'] );
$card    = ! empty( $a['cardStyle'] );

$phone_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';

$primary_class = 'dark' === $bg ? 'rehab-btn rehab-btn--luxury' : 'rehab-btn rehab-btn--dark';

$classes = 'rehab-cta-band rehab-cta-band--' . $bg . ( $compact ? ' rehab-cta-band--compact' : '' ) . ( $card ? ' rehab-cta-band--card' : '' );
$wrapper_args = [
	'class'      => $classes,
	'aria-label' => 'Call to action',
];
// Core's anchor support doesn't persist on save→null blocks, so we carry an
// explicit anchorId attribute instead.
if ( ! empty( $a['anchorId'] ) ) {
	$wrapper_args['id'] = sanitize_html_class( $a['anchorId'] );
}
$wrapper = get_block_wrapper_attributes( $wrapper_args );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container rehab-container--narrow">
	<?php if ( $card ) : ?><div class="rehab-cta-band__card"><span class="rehab-cta-band__gem" aria-hidden="true">◆</span><?php endif; ?>
		<?php if ( ! $compact ) : ?>
			<?php if ( '' !== $a['eyebrow'] ) : ?>
				<p class="rehab-cta-band__eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $a['heading'] ) : ?>
				<h2 class="rehab-cta-band__heading"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $a['lede'] ) : ?>
				<p class="rehab-cta-band__lede"><?php echo wp_kses_post( $a['lede'] ); ?></p>
			<?php endif; ?>
		<?php endif; ?>
		<div class="rehab-cta-band__actions">
			<?php if ( '' !== $a['primaryText'] ) : ?>
				<a class="<?php echo esc_attr( $primary_class ); ?>" href="<?php echo esc_url( $a['primaryUrl'] ?: '#assessment' ); ?>"><?php echo wp_kses_post( $a['primaryText'] ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $a['secondaryText'] ) : ?>
				<a class="rehab-btn rehab-btn--light" href="<?php echo esc_url( $a['secondaryUrl'] ?: '#' ); ?>"><?php echo wp_kses_post( $a['secondaryText'] ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $a['phoneText'] ) : ?>
				<a class="rehab-phone-link" href="<?php echo esc_url( $a['phoneHref'] ); ?>"><?php echo $phone_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?><u><?php echo esc_html( $a['phoneText'] ); ?></u></a>
			<?php endif; ?>
		</div>
		<?php if ( ! $compact && '' !== $a['helper'] ) : ?>
			<p class="rehab-cta-band__helper"><?php echo wp_kses_post( $a['helper'] ); ?></p>
		<?php endif; ?>
	<?php if ( $card ) : ?></div><?php endif; ?>
	</div>
</section>
