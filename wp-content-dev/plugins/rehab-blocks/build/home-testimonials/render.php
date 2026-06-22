<?php
/**
 * Server-side render for `rehab/home-testimonials`.
 *
 * Emits the same drt- markup as the legacy section-testimonials.php
 * template-part: a video-testimonials header with a desktop grid + mobile
 * Swiper (data-drt-swiper="videos"), then a verified-review Swiper carousel
 * (data-drt-swiper="reviews"). Both Swipers, the data-drt-video lightbox
 * hooks and the data-review-card / data-review-toggle "read more" hooks are
 * preserved byte-identically so homepage.js still wires everything up.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a       = $attributes;
$videos  = is_array( $a['videos'] ?? null ) ? $a['videos'] : [];
$reviews = is_array( $a['reviews'] ?? null ) ? $a['reviews'] : [];

// Recovery.com icon — bundled homepage asset (fixed, not editable).
$recovery_icon = get_stylesheet_directory_uri() . '/assets/images/homepage/logos/recovery-com-icon.png';

// Google SVG icon
$google_svg = '<svg viewBox="0 0 24 24" class="drt-icon-google__svg" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>';
$star_svg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>';

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-testimonials drt-bg-white drt-section',
	'aria-label' => 'Testimonials and reviews',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container">

		<!-- Video Heading -->
		<div class="drt-section-header">
			<h2 class="drt-heading drt-heading--lg"><?php echo wp_kses_post( $a['videoHeading'] ); ?></h2>
			<p class="drt-body"><?php echo wp_kses_post( $a['videoIntro'] ); ?></p>
		</div>

		<!-- Desktop: 3 videos grid -->
		<div class="drt-testimonials__videos-grid">
			<?php foreach ( $videos as $video ) : ?>
				<div class="drt-testimonials__video-item">
					<div class="drt-testimonials__video-thumb" data-drt-video="<?php echo esc_attr( $video['id'] ?? '' ); ?>">
						<img
							src="https://img.youtube.com/vi/<?php echo esc_attr( $video['id'] ?? '' ); ?>/maxresdefault.jpg"
							alt="<?php echo esc_attr( $video['caption'] ?? '' ); ?>"
							loading="lazy"
						>
						<div class="drt-testimonials__video-overlay" aria-hidden="true"></div>
						<div class="drt-testimonials__play">
							<svg width="28" height="28" viewBox="0 0 24 24" fill="white" aria-hidden="true"><polygon points="5,3 19,12 5,21"/></svg>
						</div>
					</div>
					<p class="drt-testimonials__video-caption"><?php echo esc_html( $video['caption'] ?? '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Mobile: Video Swiper -->
		<div class="drt-testimonials__videos-mobile">
			<div class="swiper" data-drt-swiper="videos">
				<div class="swiper-wrapper">
					<?php foreach ( $videos as $video ) : ?>
						<div class="swiper-slide">
							<div class="drt-testimonials__video-item">
								<div class="drt-testimonials__video-thumb" data-drt-video="<?php echo esc_attr( $video['id'] ?? '' ); ?>">
									<img
										src="https://img.youtube.com/vi/<?php echo esc_attr( $video['id'] ?? '' ); ?>/maxresdefault.jpg"
										alt="<?php echo esc_attr( $video['caption'] ?? '' ); ?>"
										loading="lazy"
									>
									<div class="drt-testimonials__video-overlay" aria-hidden="true"></div>
									<div class="drt-testimonials__play">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="white" aria-hidden="true"><polygon points="5,3 19,12 5,21"/></svg>
									</div>
								</div>
								<p class="drt-testimonials__video-caption"><?php echo esc_html( $video['caption'] ?? '' ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="drt-swiper-dots"></div>
			</div>
		</div>

		<!-- Reviews Section -->
		<div class="drt-testimonials__reviews">
			<div class="drt-section-header">
				<h2 class="drt-heading drt-heading--lg"><?php echo wp_kses_post( $a['reviewsHeading'] ); ?></h2>
				<p class="drt-body"><?php echo wp_kses_post( $a['reviewsIntro'] ); ?></p>
			</div>

			<div class="drt-testimonials__reviews-carousel">
				<div class="swiper" data-drt-swiper="reviews">
					<div class="swiper-wrapper">
						<?php foreach ( $reviews as $review ) :
							$content = (string) ( $review['content'] ?? '' );
							?>
							<div class="swiper-slide">
								<div class="drt-card--review" data-review-card>
									<!-- Header -->
									<div class="drt-card--review__header">
										<div class="drt-card--review__author">
											<div class="drt-card--review__avatar" style="background-color: <?php echo esc_attr( $review['color'] ?? '' ); ?>">
												<?php echo esc_html( $review['initial'] ?? '' ); ?>
											</div>
											<div>
												<p class="drt-card--review__name"><?php echo esc_html( $review['name'] ?? '' ); ?></p>
												<p class="drt-card--review__time"><?php echo esc_html( $review['time'] ?? '' ); ?></p>
											</div>
										</div>
										<?php if ( ( $review['source'] ?? '' ) === 'Google' ) : ?>
											<span class="drt-icon-google"><?php echo $google_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										<?php else : ?>
											<span class="drt-icon-recovery"><img src="<?php echo esc_url( $recovery_icon ); ?>" alt="Recovery.com" width="20" height="20" loading="lazy"></span>
										<?php endif; ?>
									</div>

									<!-- Stars + Verified -->
									<div class="drt-card--review__trust">
										<div class="drt-card--review__stars"><?php echo str_repeat( $star_svg, 5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
										<span class="drt-card--review__verified">
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
											Verified
										</span>
									</div>

									<!-- Content -->
									<div class="drt-card--review__content">
										<p class="drt-card--review__text"><?php echo nl2br( esc_html( $content ) ); ?></p>
									</div>

									<?php if ( strlen( $content ) > 150 ) : ?>
										<button class="drt-card--review__toggle" data-review-toggle>Read more</button>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					</div>
				<button class="swiper-button-prev drt-swiper-arrow" aria-label="Previous slide">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
				</button>
				<button class="swiper-button-next drt-swiper-arrow" aria-label="Next slide">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
				</button>
				<div class="drt-swiper-dots drt-testimonials__dots"></div>
			</div>
		</div>
	</div>
</section>
