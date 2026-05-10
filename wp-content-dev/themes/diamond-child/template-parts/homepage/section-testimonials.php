<?php
/**
 * Homepage Section: Testimonials
 * Video testimonials (3) + review Swiper carousel (12 reviews).
 * Includes a CTA break between videos and reviews.
 */

$videos = array(
	array( 'id' => 'eKWXt4hIrkA', 'caption' => 'Finding hope and renewed purpose.' ),
	array( 'id' => 'TyIX2GXQ5cc', 'caption' => 'A transformative journey to freedom.' ),
	array( 'id' => 'eQqAKwpQhjk', 'caption' => 'Reclaiming life in serenity.' ),
);

$reviews = array(
	array( 'name' => 'Maas Tjaarda', 'time' => 'a month ago', 'initial' => 'M', 'color' => '#9C27B0', 'source' => 'Google',
		'content' => "I have been at the Diamond for over 6 months and it completely changed my life. Not only did they help me get off Xanax, the people here helped me enjoy life again. The therapeutic team is better than anything I ever expected and worked with me through all my problems. The service is second to none, and the waitresses & housekeepers are some of the kindest people I have ever met. Nurse Ow is the most caring person I know.\n\nThank you to everyone who I've met here, I will forever be grateful for all of you!" ),
	array( 'name' => 'Alexander Evans', 'time' => '3 months ago', 'initial' => 'A', 'color' => '#2196F3', 'source' => 'Google',
		'content' => "The Diamond Rehab Thailand saved my life.\n\nI spent 15 weeks at Diamond, and I can honestly say it changed everything for me. I walked in at the lowest point of my life and walked out with hope, strength, and a second family." ),
	array( 'name' => 'Rich', 'time' => 'July 2024', 'initial' => 'R', 'color' => '#5B7FD3', 'source' => 'Recovery.com',
		'content' => "I'm Richard, 56 years old, and left The Diamond Rehab Thailand. The name is telling enough — facilities were great, beyond my expectations. Treatment was exhausting but so good and personalized. It's a holistic system. So you go fast! I recommend this facility to everybody." ),
	array( 'name' => 'Gavin Gleeson', 'time' => '4 months ago', 'initial' => 'G', 'color' => '#4CAF50', 'source' => 'Google',
		'content' => "I came into this rehabilitation center at the lowest point of my life. I honestly couldn't believe I was going to rehab — I felt hopeless, ashamed, and lost. But from the moment I arrived, I was welcomed with warmth, compassion, and understanding." ),
	array( 'name' => 'Maaike', 'time' => '4 months ago', 'initial' => 'M', 'color' => '#9C27B0', 'source' => 'Google',
		'content' => "I can't express how grateful I am that the diamond has been a part of my life! To the owners, Panwadee and Theo, you are perhaps the most beautiful people I've ever met. How you created this wonderful community and how you give hope back to the most hopeless people." ),
	array( 'name' => 'Romi Zuki', 'time' => '4 months ago', 'initial' => 'R', 'color' => '#E91E63', 'source' => 'Google',
		'content' => "I would like to express my heartfelt gratitude to the entire team at Diamond who supported me throughout this journey – the therapeutic staff, psychologists, nurses, support staff, kitchen staff, housekeepers, and security guards." ),
	array( 'name' => 'S.B.', 'time' => 'July 2024', 'initial' => 'S', 'color' => '#5B7FD3', 'source' => 'Recovery.com',
		'content' => "I spent 60 days at The Diamond Rehab Thailand, and it changed my life. Initially skeptical, I found the program's mix of intense therapy, group support, and holistic activities surprisingly effective." ),
	array( 'name' => 'Nikki Butler', 'time' => '5 months ago', 'initial' => 'N', 'color' => '#FF9800', 'source' => 'Google',
		'content' => "This has been a life changing experience\nI couldn't recommend this treatment facility any more! From the staff that met every single need I required with such amazing smiles... To the incredible therapists and team that have helped me through my darkest days." ),
	array( 'name' => 'Chris Nottage', 'time' => '6 months ago', 'initial' => 'C', 'color' => '#9C27B0', 'source' => 'Google',
		'content' => "My 7-week stay at The Diamond Luxury Rehab in Hua Hin for a gambling addiction, a condition I've learned is a serious mental disorder, has been nothing short of transformative." ),
	array( 'name' => 'Elias Namil', 'time' => '7 months ago', 'initial' => 'E', 'color' => '#FF9800', 'source' => 'Google',
		'content' => "I will be forever grateful for The Diamond Rehab. This place genuinely changed my life. I walked in broken, unsure of who I was or how to face the world, but I walked out with clarity, confidence, and the tools to handle life head-on." ),
	array( 'name' => 'Jon Clarke', 'time' => '8 months ago', 'initial' => 'J', 'color' => '#FF9800', 'source' => 'Google',
		'content' => "Ninety days ago, I walked into The Diamond Rehab in Thailand with my life in pieces. My relationships, my role as a parent, and my professional career were all hanging by a thread — severely compromised by addiction. Today, I walk out not just sober, but transformed." ),
	array( 'name' => 'tom stratingh', 'time' => '8 months ago', 'initial' => 'T', 'color' => '#4CAF50', 'source' => 'Google',
		'content' => "After 12 years of heavy addiction on several substances I decided, I need help, right now! Thanks to the diamond I arrived in Thailand within a few days. I felt very welcome and cared for. The team at the diamond does literally everything to make you feel comfortable." ),
);

// Google SVG icon
$google_svg = '<svg viewBox="0 0 24 24" class="drt-icon-google__svg" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>';
$star_svg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>';
?>
<section class="drt-testimonials drt-bg-white drt-section" aria-label="Testimonials and reviews">
	<div class="drt-container">

		<!-- Video Heading -->
		<div class="drt-section-header">
			<h2 class="drt-heading drt-heading--lg">Real results, real people</h2>
			<p class="drt-body">Hear directly from those who achieved full recovery at The Diamond Rehab Thailand.</p>
		</div>

		<!-- Desktop: 3 videos grid -->
		<div class="drt-testimonials__videos-grid">
			<?php foreach ( $videos as $video ) : ?>
				<div class="drt-testimonials__video-item">
					<div class="drt-testimonials__video-thumb" data-drt-video="<?php echo esc_attr( $video['id'] ); ?>">
						<img
							src="https://img.youtube.com/vi/<?php echo esc_attr( $video['id'] ); ?>/maxresdefault.jpg"
							alt="<?php echo esc_attr( $video['caption'] ); ?>"
							loading="lazy"
						>
						<div class="drt-testimonials__video-overlay" aria-hidden="true"></div>
						<div class="drt-testimonials__play">
							<svg width="28" height="28" viewBox="0 0 24 24" fill="white" aria-hidden="true"><polygon points="5,3 19,12 5,21"/></svg>
						</div>
					</div>
					<p class="drt-testimonials__video-caption"><?php echo esc_html( $video['caption'] ); ?></p>
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
								<div class="drt-testimonials__video-thumb" data-drt-video="<?php echo esc_attr( $video['id'] ); ?>">
									<img
										src="https://img.youtube.com/vi/<?php echo esc_attr( $video['id'] ); ?>/maxresdefault.jpg"
										alt="<?php echo esc_attr( $video['caption'] ); ?>"
										loading="lazy"
									>
									<div class="drt-testimonials__video-overlay" aria-hidden="true"></div>
									<div class="drt-testimonials__play">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="white" aria-hidden="true"><polygon points="5,3 19,12 5,21"/></svg>
									</div>
								</div>
								<p class="drt-testimonials__video-caption"><?php echo esc_html( $video['caption'] ); ?></p>
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
				<h2 class="drt-heading drt-heading--lg">The Diamond Rehab Thailand reviews</h2>
				<p class="drt-body">Read verified reviews from those who began their recovery journey with The Diamond Rehab Thailand.</p>
			</div>

			<div class="drt-testimonials__reviews-carousel">
				<div class="swiper" data-drt-swiper="reviews">
					<div class="swiper-wrapper">
						<?php foreach ( $reviews as $review ) : ?>
							<div class="swiper-slide">
								<div class="drt-card--review" data-review-card>
									<!-- Header -->
									<div class="drt-card--review__header">
										<div class="drt-card--review__author">
											<div class="drt-card--review__avatar" style="background-color: <?php echo esc_attr( $review['color'] ); ?>">
												<?php echo esc_html( $review['initial'] ); ?>
											</div>
											<div>
												<p class="drt-card--review__name"><?php echo esc_html( $review['name'] ); ?></p>
												<p class="drt-card--review__time"><?php echo esc_html( $review['time'] ); ?></p>
											</div>
										</div>
										<?php if ( $review['source'] === 'Google' ) : ?>
											<span class="drt-icon-google"><?php echo $google_svg; ?></span>
										<?php else : ?>
											<span class="drt-icon-recovery"><img src="<?php echo esc_url( drt_homepage_img( 'logos/recovery-com-icon.png' ) ); ?>" alt="Recovery.com" width="20" height="20" loading="lazy"></span>
										<?php endif; ?>
									</div>

									<!-- Stars + Verified -->
									<div class="drt-card--review__trust">
										<div class="drt-card--review__stars"><?php echo str_repeat( $star_svg, 5 ); ?></div>
										<span class="drt-card--review__verified">
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
											Verified
										</span>
									</div>

									<!-- Content -->
									<div class="drt-card--review__content">
										<p class="drt-card--review__text"><?php echo nl2br( esc_html( $review['content'] ) ); ?></p>
									</div>

									<?php if ( strlen( $review['content'] ) > 150 ) : ?>
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
