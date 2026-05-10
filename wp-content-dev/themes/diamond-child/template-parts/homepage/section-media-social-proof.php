<?php
/**
 * Homepage Section: Media Social Proof
 * Grid of press/media partner logos with tooltips.
 */

$media_logos = array(
	array(
		'src' => 'logos/yahoo-finance.png',
		'alt' => 'Yahoo Finance',
		'tip' => 'Yahoo Finance recognized The Diamond Rehab Thailand as a global leader for its unique fusion of luxury hospitality and rigorous Western clinical standards.',
	),
	array(
		'src' => 'logos/business-insider.png',
		'alt' => 'Business Insider',
		'tip' => 'Business Insider featured insights from The Diamond Rehab Thailand experts on the vital connection between environment and long-term recovery success.',
	),
	array(
		'src' => 'logos/newcastle-herald.png',
		'alt' => 'Newcastle Herald',
		'tip' => 'Newcastle Herald officially ranked The Diamond Rehab Thailand as the #1 drug rehabilitation center for 2025, citing its world-class care and value.',
	),
	array(
		'src' => 'logos/canberra-times.png',
		'alt' => 'Canberra Times',
		'tip' => 'Canberra Times highlighted The Diamond Rehab Thailand as the premier international destination for Australians seeking private and discreet 5-star care.',
	),
	array(
		'src' => 'logos/digital-journal.png',
		'alt' => 'Digital Journal',
		'tip' => 'Digital Journal reported on The Diamond Rehab Thailand\'s industry-leading 12-client capacity, ensuring the highest level of personalized medical attention.',
	),
	array(
		'src' => 'logos/psych-central.png',
		'alt' => 'PsychCentral',
		'tip' => 'PsychCentral acknowledged The Diamond Rehab Thailand for its pioneering integration of evidence-based medical therapy and holistic mindfulness meditation.',
	),
	array(
		'src' => 'logos/fox56.png',
		'alt' => 'FOX 56',
		'tip' => 'FOX 56 featured The Diamond Rehab Thailand for its role as a secure and peaceful sanctuary that provides the ideal foundation for permanent transformation.',
	),
	array(
		'src' => 'logos/well-good.png',
		'alt' => 'Well+Good',
		'tip' => 'Well+Good recognized The Diamond Rehab Thailand for its holistic, high-end approach to restoring physical, emotional, and mental balance in a tropical setting.',
	),
	array(
		'src' => 'logos/bestlife.png',
		'alt' => 'Best Life',
		'tip' => 'Best Life commended The Diamond Rehab Thailand for creating a recovery experience that prioritizes absolute privacy, anonymity, and patient comfort.',
	),
	array(
		'src' => 'logos/coindesk.png',
		'alt' => 'CoinDesk',
		'tip' => 'CoinDesk noted the sophisticated private healthcare model at The Diamond Rehab Thailand, catering to global professionals who require total clinical discretion.',
	),
);
?>
<section class="drt-media drt-bg-cream" aria-label="Featured in">
	<div class="drt-container">
		<div class="drt-media__grid">
			<?php foreach ( $media_logos as $logo ) : ?>
				<div class="drt-media__item" data-tooltip="<?php echo esc_attr( $logo['tip'] ); ?>">
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
