<?php
/**
 * Homepage Section: Team Carousel
 * 19 team members in a Swiper carousel with hover overlays.
 */

$team = array(
	array( 'name' => 'Theo & Panwadee de Vries', 'title' => 'Founders', 'image' => 'theo-panwadee-de-vries.avif' ),
	array( 'name' => 'Sergio Pereira', 'title' => 'Rehab Director', 'image' => 'sergio-pereira.avif' ),
	array( 'name' => 'Jessica Waller', 'title' => 'Psychologist', 'image' => 'jessica-waller.avif' ),
	array( 'name' => 'Jiraporn Takonchai', 'title' => 'General Manager', 'image' => 'jiraporn-takonchai.avif' ),
	array( 'name' => 'Dr. Roshan Fernando', 'title' => 'Psychiatrist', 'image' => 'roshan-fernando.avif' ),
	array( 'name' => 'Wei Ling', 'title' => 'Psychotherapist / Counselling Psychologist', 'image' => 'wei-ling.avif' ),
	array( 'name' => "Augustine D'Ewes", 'title' => 'Clinical Supervisor / Psychologist', 'image' => 'augustine-dewes.avif' ),
	array( 'name' => 'Eugene Pretorius', 'title' => 'Addiction Counsellor', 'image' => 'eugene-pretorius.avif' ),
	array( 'name' => 'Brian Tucker', 'title' => 'Addiction Counsellor', 'image' => 'brian-tucker.avif' ),
	array( 'name' => 'James Donovan', 'title' => 'Addiction Counsellor', 'image' => 'james-donovan.avif' ),
	array( 'name' => 'Thipada Sritongkom', 'title' => 'Nurse', 'image' => 'thipada-sritongkom.avif' ),
	array( 'name' => 'Ponsuppat Udom', 'title' => 'Nurse', 'image' => 'ponsuppat-udom.avif' ),
	array( 'name' => 'Bongkotkarn Sirijunchuen', 'title' => 'Nurse', 'image' => 'bongkotkarn-sirijunchuen.avif' ),
	array( 'name' => "Kittikawin 'Kwin' Rachawong", 'title' => 'Head Chef', 'image' => 'kittikawin-rachawong.avif' ),
	array( 'name' => 'Irene Grace Maghopoy', 'title' => 'Support Worker / Admissions', 'image' => 'irene-maghopoy.avif' ),
	array( 'name' => 'Supanni Sanli', 'title' => 'Support Worker', 'image' => 'supanni-sanli.avif' ),
	array( 'name' => 'Wuttipong Wandee', 'title' => 'Support Worker', 'image' => 'wuttipong-wandee.avif' ),
	array( 'name' => 'Saran Badod', 'title' => 'Admin - Support Worker', 'image' => 'saran-badod.avif' ),
	array( 'name' => 'Ananyalak Sonin', 'title' => 'Yoga Teacher', 'image' => 'ananyalak-sonin.avif' ),
);
?>
<section class="drt-team drt-bg-white drt-section" aria-label="Our team">
	<div class="drt-container">
		<div class="drt-section-header">
			<h2 class="drt-heading drt-heading--lg drt-text-balance">
				The Diamond Rehab Thailand therapists, counsellors, and staff
			</h2>
		</div>

		<div class="drt-team__carousel">
			<div class="swiper" data-drt-swiper="team">
				<div class="swiper-wrapper">
					<?php foreach ( $team as $member ) : ?>
						<div class="swiper-slide">
							<div class="drt-team__member">
								<img
									src="<?php echo esc_url( drt_homepage_img( 'team/' . $member['image'] ) ); ?>"
									alt="<?php echo esc_attr( $member['name'] . ' - ' . $member['title'] ); ?>"
									class="drt-team__photo"
									loading="lazy"
								>
								<div class="drt-team__overlay">
									<h3 class="drt-team__name"><?php echo esc_html( $member['name'] ); ?></h3>
									<p class="drt-team__title"><?php echo esc_html( $member['title'] ); ?></p>
								</div>
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
			<div class="drt-swiper-dots drt-team__dots"></div>
		</div>
	</div>
</section>
