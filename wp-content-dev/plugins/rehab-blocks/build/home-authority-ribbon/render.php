<?php
/**
 * Server-side render for `rehab/home-authority-ribbon`.
 *
 * Trust ribbon: three credential pillars (Ministry license, Google rating,
 * Recovery.com rating) followed by a repeatable row of partner logos. Emits
 * the exact drt- markup of the legacy section-authority-ribbon.php so the
 * existing homepage CSS renders it pixel-identically.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a = $attributes;

// Base URL for the theme's homepage image assets — mirrors drt_homepage_img().
$base = get_stylesheet_directory_uri() . '/assets/images/homepage/';

$star_svg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>';

// Original partner logos — used as a per-index fallback when an item leaves a
// field empty, so the section degrades to its hardcoded defaults.
$logo_fallbacks = array(
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

$logos = is_array( $a['logos'] ?? null ) ? $a['logos'] : array();

// Ministry badge: explicit URL, else the original theme asset.
$ministry_url = '' !== $a['ministryImageUrl']
	? $a['ministryImageUrl']
	: $base . 'logos/ministry-public-health-badge.webp';

$wrapper = get_block_wrapper_attributes( array(
	'class'      => 'drt-authority drt-bg-cream',
	'aria-label' => 'Accreditations and trust signals',
) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container">

		<!-- Row 1: Trust Pillars -->
		<div class="drt-authority__pillars">
			<!-- Ministry License -->
			<div class="drt-authority__pillar" data-tooltip="<?php echo esc_attr( $a['ministryTooltip'] ); ?>">
				<img
					src="<?php echo esc_url( $ministry_url ); ?>"
					alt="<?php echo esc_attr( $a['ministryAlt'] ); ?>"
					class="drt-authority__ministry-logo drt-animate-luxury-pulse"
					width="64"
					height="64"
					loading="lazy"
				>
				<span class="drt-authority__pillar-text"><?php echo wp_kses_post( $a['ministryText'] ); ?></span>
			</div>

			<div class="drt-authority__divider" aria-hidden="true"></div>

			<!-- Google Rating -->
			<div class="drt-authority__pillar" data-tooltip="<?php echo esc_attr( $a['googleTooltip'] ); ?>">
				<div class="drt-authority__stars drt-animate-luxury-pulse" aria-label="<?php echo esc_attr( $a['googleStarsLabel'] ); ?>">
					<?php echo str_repeat( $star_svg, 5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<span class="drt-authority__pillar-text"><?php echo wp_kses_post( $a['googleText'] ); ?></span>
			</div>

			<div class="drt-authority__divider" aria-hidden="true"></div>

			<!-- Recovery.com Rating -->
			<div class="drt-authority__pillar" data-tooltip="<?php echo esc_attr( $a['recoveryTooltip'] ); ?>">
				<div class="drt-authority__stars drt-animate-luxury-pulse" aria-label="<?php echo esc_attr( $a['recoveryStarsLabel'] ); ?>">
					<?php echo str_repeat( $star_svg, 5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<span class="drt-authority__pillar-text"><?php echo wp_kses_post( $a['recoveryText'] ); ?></span>
			</div>
		</div>

		<!-- Row 2: Partner Logos -->
		<div class="drt-authority__partners">
			<?php foreach ( $logos as $i => $logo ) :
				$logo = (array) $logo;
				$fb   = $logo_fallbacks[ $i ] ?? array( 'src' => '', 'alt' => '', 'tip' => '' );
				$url  = ! empty( $logo['imageUrl'] ) ? $logo['imageUrl'] : $base . $fb['src'];
				$alt  = ( isset( $logo['alt'] ) && '' !== $logo['alt'] ) ? $logo['alt'] : $fb['alt'];
				$tip  = ( isset( $logo['tip'] ) && '' !== $logo['tip'] ) ? $logo['tip'] : $fb['tip'];
				?>
				<div class="drt-authority__partner" data-tooltip="<?php echo esc_attr( $tip ); ?>">
					<img
						src="<?php echo esc_url( $url ); ?>"
						alt="<?php echo esc_attr( $alt ); ?>"
						class="drt-partner-logo"
						loading="lazy"
					>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
