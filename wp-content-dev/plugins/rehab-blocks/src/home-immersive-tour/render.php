<?php
/**
 * Server-side render for `rehab/home-immersive-tour`.
 *
 * 360° tour CTA with room shortcuts + a Swiper gallery with Fancybox lightbox.
 * Emits the same drt- markup as the legacy section-immersive-tour.php
 * template-part so the existing drt CSS/JS (Swiper + Fancybox) apply unchanged.
 *
 * CRITICAL JS hooks preserved byte-identically:
 *   - data-drt-swiper="gallery"  (Swiper init in homepage.js)
 *   - data-fancybox="gallery"    (Fancybox.bind in homepage.js)
 *   - .swiper / .swiper-wrapper / .swiper-slide / .swiper-button-prev / -next
 *   - .drt-swiper-dots / .drt-tour__dots / .drt-tour__gallery
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a = $attributes;

// Image asset base — mirrors theme drt_homepage_img() so empty fields fall back
// to the bundled homepage assets.
$img_base = get_stylesheet_directory_uri() . '/assets/images/homepage/';

// Tour CTA destination + hero preview image (bundled default when unset).
$tour_url  = $a['tourUrl'] ?: 'https://tour.diamondrehabthailand.com/';
$hero_img  = ( '' !== $a['heroImage'] )
	? $a['heroImage']
	: $img_base . 'gallery/360-tour-diamond-rehab-thailand.avif';
$hero_alt  = $a['heroAlt'] ?: 'The Diamond Rehab Thailand virtual tour preview';
$cta_text  = $a['ctaText'] ?: 'START 360° TOUR';

$rooms   = is_array( $a['rooms'] ?? null ) ? $a['rooms'] : array();
$gallery = is_array( $a['gallery'] ?? null ) ? $a['gallery'] : array();

$wrapper = get_block_wrapper_attributes( array(
	'class'      => 'drt-tour drt-bg-white',
	'aria-label' => 'Virtual tour and photography gallery',
) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container">
		<h2 class="drt-heading drt-heading--lg drt-tour__title drt-text-balance">
			<?php echo wp_kses_post( $a['heading'] ); ?>
		</h2>

		<!-- 360° Tour CTA -->
		<div class="drt-tour__hero">
			<div class="drt-tour__hero-wrap">
				<a href="<?php echo esc_url( $tour_url ); ?>" target="_blank" rel="noreferrer" class="drt-tour__hero-link">
					<img
						src="<?php echo esc_url( $hero_img ); ?>"
						alt="<?php echo esc_attr( $hero_alt ); ?>"
						class="drt-tour__hero-image"
						loading="lazy"
					>
					<div class="drt-tour__hero-overlay" aria-hidden="true"></div>
				</a>

				<div class="drt-tour__hero-center">
					<a href="<?php echo esc_url( $tour_url ); ?>" target="_blank" rel="noreferrer" class="drt-btn drt-btn--luxury drt-tour__hero-btn">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
						<?php echo wp_kses_post( $cta_text ); ?>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.7;" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
					</a>

					<!-- Room shortcuts: desktop only -->
					<div class="drt-tour__shortcuts drt-tour__shortcuts--desktop">
						<?php foreach ( $rooms as $room ) :
							$room = array_merge( array( 'label' => '', 'url' => '' ), (array) $room );
							?>
							<a href="<?php echo esc_url( $room['url'] ); ?>" target="_blank" rel="noreferrer" class="drt-btn drt-btn--ghost">
								<?php echo esc_html( $room['label'] ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<!-- Room shortcuts: mobile, below image -->
			<div class="drt-tour__shortcuts drt-tour__shortcuts--mobile">
				<?php foreach ( $rooms as $room ) :
					$room = array_merge( array( 'label' => '', 'url' => '' ), (array) $room );
					?>
					<a href="<?php echo esc_url( $room['url'] ); ?>" target="_blank" rel="noreferrer" class="drt-btn drt-btn--outline">
						<?php echo esc_html( $room['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Gallery Carousel -->
		<div class="drt-tour__gallery">
			<div class="swiper drt-tour__swiper" data-drt-swiper="gallery">
				<div class="swiper-wrapper">
					<?php foreach ( $gallery as $img ) :
						$img  = array_merge( array( 'thumb' => '', 'full' => '', 'alt' => '' ), (array) $img );
						$full = $img['full'] ?: $img_base . 'gallery/';
						$thmb = $img['thumb'] ?: $img_base . 'gallery/';
						?>
						<div class="swiper-slide">
							<a
								href="<?php echo esc_url( $full ); ?>"
								data-fancybox="gallery"
								data-caption="<?php echo esc_attr( $img['alt'] ); ?>"
								class="drt-tour__slide"
							>
								<img
									src="<?php echo esc_url( $thmb ); ?>"
									alt="<?php echo esc_attr( $img['alt'] ); ?>"
									loading="lazy"
								>
							</a>
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
			<div class="drt-swiper-dots drt-tour__dots"></div>
		</div>
	</div>
</section>
