<?php
/**
 * Server-side render for `rehab/home-founder-vision`.
 *
 * Founder section: pull-quote, portrait, name, bio and an inspiration quote.
 * Emits the same drt- markup as the legacy
 * template-parts/homepage/section-founder-vision.php partial.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a = $attributes;

// Default portrait resolves to the same asset drt_homepage_img() returns.
$img_url = '' !== $a['imageUrl']
	? $a['imageUrl']
	: get_stylesheet_directory_uri() . '/assets/images/homepage/founder/theo-de-vries-founder-diamond-rehab.avif';

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-founder drt-bg-white drt-section',
	'aria-label' => 'Founder Vision',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container drt-container--narrow">
		<?php if ( '' !== $a['quote'] ) : ?>
			<blockquote class="drt-founder__quote">
				<p><?php echo wp_kses_post( $a['quote'] ); ?></p>
			</blockquote>
		<?php endif; ?>

		<div class="drt-founder__signature">
			<img
				src="<?php echo esc_url( $img_url ); ?>"
				alt="<?php echo esc_attr( $a['imageAlt'] ); ?>"
				class="drt-founder__portrait"
				width="220"
				height="293"
				loading="lazy"
			>
			<div class="drt-founder__bio">
				<?php if ( '' !== $a['heading'] ) : ?>
					<h2 class="drt-heading drt-heading--sm"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $a['bio'] ) : ?>
					<p class="drt-body"><?php echo wp_kses_post( $a['bio'] ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $a['inspirationQuote'] ) : ?>
					<p class="drt-founder__mate-quote"><?php echo wp_kses_post( $a['inspirationQuote'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
