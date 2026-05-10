<?php
/**
 * Homepage Section: Authority Ribbon
 * Trust signals: Ministry license, Google rating, Recovery.com rating + partner logos.
 */

$partner_logos = array(
	array(
		'src' => 'logos/bangkok-hospital.png',
		'alt' => 'Bangkok Hospital',
		'tip' => 'The Diamond Rehab Thailand is partnered with Bangkok Hospital for comprehensive medical support and 24/7 emergency care for all residential clients.',
	),
	array(
		'src' => 'logos/blue-cross-blue-shield.svg',
		'alt' => 'Blue Cross Blue Shield',
		'tip' => 'The Diamond Rehab Thailand works with BlueCross BlueShield to facilitate international insurance coverage for luxury residential rehab services.',
	),
	array(
		'src' => 'logos/cigna.webp',
		'alt' => 'Cigna Healthcare',
		'tip' => 'The Diamond Rehab Thailand is an eligible provider for Cigna policyholders seeking world-class addiction treatment and behavioral health services.',
	),
	array(
		'src' => 'logos/icrc.png',
		'alt' => 'IC&RC',
		'tip' => 'The Diamond Rehab Thailand adheres to the global professional standards of the IC&RC, the world\'s leader in certifying addiction treatment professionals.',
	),
	array(
		'src' => 'logos/rms.png',
		'alt' => 'Release My Super',
		'tip' => 'The Diamond Rehab Thailand assists Australian clients with Release my super (RMS) for early access to superannuation for essential medical treatment.',
	),
	array(
		'src' => 'logos/veteran-support.png',
		'alt' => 'Veteran Support',
		'tip' => 'The Diamond Rehab Thailand provides specialized trauma-informed programs and veteran support for former service members from the UK, US, and Australia.',
	),
);

$star_svg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>';
?>
<section class="drt-authority drt-bg-cream" aria-label="Accreditations and trust signals">
	<div class="drt-container">

		<!-- Row 1: Trust Pillars -->
		<div class="drt-authority__pillars">
			<!-- Ministry License -->
			<div class="drt-authority__pillar" data-tooltip="The Diamond Rehab Thailand is fully licensed by the Thai Ministry of Public Health, ensuring our facility meets the highest national standards for drug and alcohol treatment.">
				<img
					src="<?php echo esc_url( drt_homepage_img( 'logos/ministry-public-health-badge.webp' ) ); ?>"
					alt="Thai Ministry of Public Health"
					class="drt-authority__ministry-logo drt-animate-luxury-pulse"
					width="64"
					height="64"
					loading="lazy"
				>
				<span class="drt-authority__pillar-text">Fully licensed by the Thai Health Ministry</span>
			</div>

			<div class="drt-authority__divider" aria-hidden="true"></div>

			<!-- Google Rating -->
			<div class="drt-authority__pillar" data-tooltip="The Diamond Rehab Thailand maintains a 4.9/5 star reputation on Google, reflecting our commitment to high-quality patient care and successful recovery outcomes.">
				<div class="drt-authority__stars drt-animate-luxury-pulse" aria-label="4.9 out of 5 stars">
					<?php echo str_repeat( $star_svg, 5 ); ?>
				</div>
				<span class="drt-authority__pillar-text">Rated 4.9/5 stars on Google</span>
			</div>

			<div class="drt-authority__divider" aria-hidden="true"></div>

			<!-- Recovery.com Rating -->
			<div class="drt-authority__pillar" data-tooltip="The Diamond Rehab Thailand is recognized as a premier luxury provider on Recovery.com, verified for clinical excellence in treating addiction and mental health.">
				<div class="drt-authority__stars drt-animate-luxury-pulse" aria-label="5 out of 5 stars">
					<?php echo str_repeat( $star_svg, 5 ); ?>
				</div>
				<span class="drt-authority__pillar-text">Rated 5/5 stars on Recovery.com</span>
			</div>
		</div>

		<!-- Row 2: Partner Logos -->
		<div class="drt-authority__partners">
			<?php foreach ( $partner_logos as $logo ) : ?>
				<div class="drt-authority__partner" data-tooltip="<?php echo esc_attr( $logo['tip'] ); ?>">
					<img
						src="<?php echo esc_url( drt_homepage_img( $logo['src'] ) ); ?>"
						alt="<?php echo esc_attr( $logo['alt'] ); ?>"
						class="drt-partner-logo"
						loading="lazy"
					>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
