<?php
/**
 * Homepage Section: Hero
 * 55/45 split layout with content left, video thumbnail right.
 * Contains the single H1 for the page.
 */

$hero_img  = drt_homepage_img( 'hero/hero-pool-pavilion.avif' );
$video_id  = 'rlEKwU70eGY';

$trust_signals = array(
	'Exclusive 12-client intake',
	'Intl. multi-disciplinary team',
	'Relapse prevention guarantee',
);
?>
<section class="drt-hero" aria-label="Hero">
	<div class="drt-container">
		<div class="drt-hero__grid">

			<!-- Left Column: Content -->
			<div class="drt-hero__content drt-animate-fade-in-left" style="animation-delay: 0.2s;">
				<h1 class="drt-hero__h1">
					<span class="drt-hero__voted">Voted #1 by The Thaiger</span>
					<span class="drt-hero__headline">
						The leading luxury<br>
						drug and alcohol rehab<br class="drt-hero__br-sm"> in Thailand
					</span>
				</h1>

				<p class="drt-hero__body">
					Thai hospitality meets Western clinical excellence. Set within a private 5-star sanctuary, our 12-client cap ensures your recovery is handled with absolute discretion.
				</p>

				<div class="drt-hero__cta">
					<a href="/contact-us/" class="drt-btn drt-btn--luxury">
						Check Availability
					</a>
					<p class="drt-hero__cta-helper">Free, confidential, and no-obligation.</p>
				</div>

				<div class="drt-hero__trust">
					<?php foreach ( $trust_signals as $signal ) : ?>
						<div class="drt-hero__trust-item">
							<span class="drt-hero__diamond" aria-hidden="true">&#9670;</span>
							<span><?php echo esc_html( $signal ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Right Column: Video Thumbnail -->
			<div class="drt-hero__media drt-animate-fade-in-right" style="animation-delay: 0.4s;">
				<div class="drt-hero__image-wrap" data-drt-video="<?php echo esc_attr( $video_id ); ?>">
					<img
						src="<?php echo esc_url( $hero_img ); ?>"
						alt="Luxury Thai pool pavilion with traditional roof surrounded by tropical gardens"
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
