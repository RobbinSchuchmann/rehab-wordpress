<?php
/**
 * Server-side render for `rehab/home-hero`.
 *
 * Homepage hero: 55/45 split with the page H1, CTA, trust signals and a
 * YouTube video thumbnail. Emits the same drt- markup as the legacy
 * section-hero.php template-part so the existing drt CSS/JS apply unchanged.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a = $attributes;

// Image: fall back to the bundled homepage hero asset when unset.
$hero_img = ( '' !== $a['imageUrl'] )
	? $a['imageUrl']
	: get_stylesheet_directory_uri() . '/assets/images/homepage/hero/hero-pool-pavilion.avif';

// Trust signals: fall back to the original three when none supplied.
$trust = array_filter( array_map( 'trim', (array) ( $a['trustItems'] ?? array() ) ), 'strlen' );
if ( empty( $trust ) ) {
	$trust = array(
		'Exclusive 12-client intake',
		'Intl. multi-disciplinary team',
		'Relapse prevention guarantee',
	);
}

$wrapper = get_block_wrapper_attributes( array(
	'class'      => 'drt-hero',
	'aria-label' => 'Hero',
) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container">
		<div class="drt-hero__grid">

			<!-- Left Column: Content -->
			<div class="drt-hero__content drt-animate-fade-in-left" style="animation-delay: 0.2s;">
				<h1 class="drt-hero__h1">
					<?php if ( '' !== $a['voted'] ) : ?>
						<span class="drt-hero__voted"><?php echo wp_kses_post( $a['voted'] ); ?></span>
					<?php endif; ?>
					<span class="drt-hero__headline">
						<?php echo wp_kses_post( $a['headline'] ); ?>
					</span>
				</h1>

				<?php if ( '' !== $a['body'] ) : ?>
					<p class="drt-hero__body">
						<?php echo wp_kses_post( $a['body'] ); ?>
					</p>
				<?php endif; ?>

				<div class="drt-hero__cta">
					<?php if ( '' !== $a['ctaText'] ) : ?>
						<a href="<?php echo esc_url( $a['ctaUrl'] ?: '#' ); ?>" class="drt-btn drt-btn--luxury">
							<?php echo wp_kses_post( $a['ctaText'] ); ?>
						</a>
					<?php endif; ?>
					<?php if ( '' !== $a['ctaHelper'] ) : ?>
						<p class="drt-hero__cta-helper"><?php echo wp_kses_post( $a['ctaHelper'] ); ?></p>
					<?php endif; ?>
				</div>

				<div class="drt-hero__trust">
					<?php foreach ( $trust as $signal ) : ?>
						<div class="drt-hero__trust-item">
							<span class="drt-hero__diamond" aria-hidden="true">&#9670;</span>
							<span><?php echo esc_html( $signal ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Right Column: Video Thumbnail -->
			<div class="drt-hero__media drt-animate-fade-in-right" style="animation-delay: 0.4s;">
				<div class="drt-hero__image-wrap" data-drt-video="<?php echo esc_attr( $a['videoId'] ); ?>">
					<img
						src="<?php echo esc_url( $hero_img ); ?>"
						alt="<?php echo esc_attr( $a['imageAlt'] ); ?>"
						class="drt-hero__image"
						fetchpriority="high"
						loading="eager"
						decoding="async"
					>
					<!-- Brand Wash Overlay -->
					<div class="drt-hero__overlay" aria-hidden="true"></div>
					<!-- Play Button -->
					<div class="drt-hero__play" aria-label="Play video tour">
						<svg width="28" height="28" viewBox="0 0 24 24" fill="white" aria-hidden="true"><polygon points="5,3 19,12 5,21"/></svg>
					</div>
				</div>
				<!-- Decorative corner -->
				<div class="drt-hero__deco" aria-hidden="true"></div>
			</div>

		</div>
	</div>
</section>
