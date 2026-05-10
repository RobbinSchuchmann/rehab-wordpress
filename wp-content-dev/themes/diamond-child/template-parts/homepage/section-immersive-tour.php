<?php
/**
 * Homepage Section: Immersive Tour
 * 360° tour CTA with room shortcuts + 22-image Swiper gallery with Fancybox lightbox.
 */

$tour_url = 'https://tour.diamondrehabthailand.com/';

$room_shortcuts = array(
	array( 'label' => 'Pool Area',       'url' => 'https://tour.diamondrehabthailand.com/pool-area-1' ),
	array( 'label' => 'Private Bungalow','url' => 'https://tour.diamondrehabthailand.com/private-bungalow-3/' ),
	array( 'label' => 'Therapy Room',    'url' => 'https://tour.diamondrehabthailand.com/meeting-room/' ),
	array( 'label' => 'Gym',             'url' => 'https://tour.diamondrehabthailand.com/gym-1' ),
	array( 'label' => 'Dining',          'url' => 'https://tour.diamondrehabthailand.com/dining-area-1' ),
);

$gallery = array(
	array( 'thumb' => '1_Bungalow_evening_pool_-_thumb.avif', 'full' => '1-Bungalow-evening-pool-full.avif', 'alt' => 'Evening pool view at private bungalow - The Diamond luxury rehab Thailand' ),
	array( 'thumb' => '2_Balcony_bungalow_-_thumb.avif', 'full' => '2-Balcony-bungalow-full.avif', 'alt' => 'Private bungalow balcony with tropical garden views' ),
	array( 'thumb' => '3_Bedroom_-_thumb.avif', 'full' => '3-Bedroom-full.avif', 'alt' => 'Luxury bedroom suite at The Diamond rehab Thailand' ),
	array( 'thumb' => '4_Bedroom_-_thumb.avif', 'full' => '4-Bedroom-full.avif', 'alt' => 'Premium accommodation bedroom with natural light' ),
	array( 'thumb' => '5_Bed_-_thumb.avif', 'full' => '5-Bed-full.avif', 'alt' => 'Comfortable king-size bed in private bungalow' ),
	array( 'thumb' => '6_Relaxing_outside_-_thumb.avif', 'full' => '6-Relaxing-outside-full.avif', 'alt' => 'Relaxation area in tropical garden setting' ),
	array( 'thumb' => '7_Meeting_Room_-_Session_Room_-_thumb.avif', 'full' => '7-Meeting-Room-Session-Room-full.avif', 'alt' => 'Meeting room and session space at The Diamond' ),
	array( 'thumb' => '8_Group_meeting_-_thumb.avif', 'full' => '8-Group-meeting-full.avif', 'alt' => 'Group therapy meeting room for recovery sessions' ),
	array( 'thumb' => '9_1-1_session_room_-_thumb.avif', 'full' => '9-1-1-session-room-full.avif', 'alt' => 'One-on-one therapy session room at The Diamond rehab' ),
	array( 'thumb' => '10_Dining_area_-_thumb.avif', 'full' => '10-Dining-area-full.avif', 'alt' => 'Fine dining area at The Diamond luxury rehab' ),
	array( 'thumb' => '11_Dining_area_4_-_thumb.avif', 'full' => '11-Dining-area-4-full.avif', 'alt' => 'Executive dining experience with chef-prepared meals' ),
	array( 'thumb' => '12_Massage_-_thumb.avif', 'full' => '12-Massage-full.avif', 'alt' => 'Wellness massage treatment at The Diamond Thailand' ),
	array( 'thumb' => '13_Meditation_yoga_-_thumb.avif', 'full' => '13-Meditation-yoga-full.avif', 'alt' => 'Meditation and yoga pavilion for mindfulness practice' ),
	array( 'thumb' => '14_Sauna_-_thumb.avif', 'full' => '14-Sauna-full.avif', 'alt' => 'Private sauna for relaxation and detox' ),
	array( 'thumb' => '15_Fitness_-_thumb.avif', 'full' => '15-Fitness-full.avif', 'alt' => 'State-of-the-art fitness center' ),
	array( 'thumb' => '16_Gym_-_thumb.avif', 'full' => '16-Gym-full.avif', 'alt' => 'Fully equipped gym at The Diamond rehab Thailand' ),
	array( 'thumb' => '17_Beach_Hua_Hin_-_thumb.avif', 'full' => '17-Beach-Hua-Hin-full.avif', 'alt' => 'Hua Hin beach excursion - luxury rehab Thailand' ),
	array( 'thumb' => '18_Shrine_-_thumb.avif', 'full' => '18-Shrine-full.avif', 'alt' => 'Traditional Thai shrine in sanctuary gardens' ),
	array( 'thumb' => '19_Front_bungalow_with_pool_-_thumb.avif', 'full' => '19-Front-bungalow-with-pool-full.avif', 'alt' => 'Front view of private pool bungalow' ),
	array( 'thumb' => '20_Close_up_pool_chairs_-_thumb.avif', 'full' => '20-Close-up-pool-chairs-full.avif', 'alt' => 'Poolside relaxation chairs at The Diamond' ),
	array( 'thumb' => '21_Bungalow_evening_-_thumb.avif', 'full' => '21-Bungalow-evening-full.avif', 'alt' => 'Private bungalow evening ambiance' ),
	array( 'thumb' => '22_Team_-_thumb.avif', 'full' => '22-Team-full.avif', 'alt' => 'The Diamond clinical and wellness team' ),
);
?>
<section class="drt-tour drt-bg-white" aria-label="Virtual tour and photography gallery">
	<div class="drt-container">
		<h2 class="drt-heading drt-heading--lg drt-tour__title drt-text-balance">
			Step inside our luxury rehab in Thailand: 360&deg; virtual tour &amp; photography gallery
		</h2>

		<!-- 360° Tour CTA -->
		<div class="drt-tour__hero">
			<div class="drt-tour__hero-wrap">
				<a href="<?php echo esc_url( $tour_url ); ?>" target="_blank" rel="noreferrer" class="drt-tour__hero-link">
					<img
						src="<?php echo esc_url( drt_homepage_img( 'gallery/360-tour-diamond-rehab-thailand.avif' ) ); ?>"
						alt="The Diamond Rehab Thailand virtual tour preview"
						class="drt-tour__hero-image"
						loading="lazy"
					>
					<div class="drt-tour__hero-overlay" aria-hidden="true"></div>
				</a>

				<div class="drt-tour__hero-center">
					<a href="<?php echo esc_url( $tour_url ); ?>" target="_blank" rel="noreferrer" class="drt-btn drt-btn--luxury drt-tour__hero-btn">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
						START 360&deg; TOUR
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.7;" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
					</a>

					<!-- Room shortcuts: desktop only -->
					<div class="drt-tour__shortcuts drt-tour__shortcuts--desktop">
						<?php foreach ( $room_shortcuts as $room ) : ?>
							<a href="<?php echo esc_url( $room['url'] ); ?>" target="_blank" rel="noreferrer" class="drt-btn drt-btn--ghost">
								<?php echo esc_html( $room['label'] ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<!-- Room shortcuts: mobile, below image -->
			<div class="drt-tour__shortcuts drt-tour__shortcuts--mobile">
				<?php foreach ( $room_shortcuts as $room ) : ?>
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
					<?php foreach ( $gallery as $img ) : ?>
						<div class="swiper-slide">
							<a
								href="<?php echo esc_url( drt_homepage_img( 'gallery/' . $img['full'] ) ); ?>"
								data-fancybox="gallery"
								data-caption="<?php echo esc_attr( $img['alt'] ); ?>"
								class="drt-tour__slide"
							>
								<img
									src="<?php echo esc_url( drt_homepage_img( 'gallery/' . $img['thumb'] ) ); ?>"
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
