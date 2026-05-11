<?php
/**
 * Treatment-page "chrome" — the 3 decoration blocks the ACF flex schema
 * never carried (logo strip, journey steps, generic benefits). Their copy
 * is drug-agnostic, so every treatment page can render them verbatim;
 * the editor can override per-page after the fact.
 *
 * The two drug-specific decoration blocks (pillars, signs-grid) are NOT
 * here — those need per-page copy and stay a hand-curated job inside the
 * block editor.
 *
 * Used by aa-acf-mapper.php's `_with_chrome` variant.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Logo strip slotted in directly after the treatment-hero.
 * URLs point at the theme's bundled press-mention assets so they survive
 * uploads-folder mutations.
 */
function rehab_chrome_authority_ribbon(): string {
	$base = get_template_directory_uri() === get_stylesheet_directory_uri()
		? get_template_directory_uri()
		: get_stylesheet_directory_uri();
	// Press/affiliate badges live under the diamond-child theme.
	$badge_base = home_url( '/wp-content/themes/diamond-child/assets/img/treatment' );
	return rehab_block_authority_ribbon(
		'As featured in',
		[
			[ 'url' => $badge_base . '/business-insider.png', 'alt' => 'Business Insider' ],
			[ 'url' => $badge_base . '/yahoo-finance.png',    'alt' => 'Yahoo Finance' ],
			[ 'url' => $badge_base . '/well-good.png',        'alt' => 'Well + Good' ],
			[ 'url' => $badge_base . '/psych-central.png',    'alt' => 'Psych Central' ],
			[ 'url' => $badge_base . '/recovery-com.webp',    'alt' => 'Recovery.com' ],
			[ 'url' => $badge_base . '/bangkok-hospital.png', 'alt' => 'Bangkok Hospital partner' ],
		]
	);
}

/**
 * Numbered list of generic inpatient-care benefits. Phrased without any
 * substance name so the same block fits ice / heroin / alcohol pages.
 */
function rehab_chrome_benefits_numbered(): string {
	return rehab_block_benefits_numbered( [
		[
			'title' => 'Distance from triggers',
			'body'  => 'Isolating from your usual lifestyle and social circles eliminates the risk of giving in to cravings during the most fragile early weeks.',
		],
		[
			'title' => 'Round-the-clock supervision',
			'body'  => 'Resort-style amenities backed by a full team of qualified medical professionals with extensive experience treating addiction.',
		],
		[
			'title' => 'Custom-built treatment plan',
			'body'  => 'Our addiction experts guide you through the crucial first weeks of a custom-made program tailored to your specific clinical picture.',
		],
		[
			'title' => 'A genuine therapeutic community',
			'body'  => 'With a hard cap of 12 clients, you receive deeper attention and form trusted connections that support your long-term recovery.',
		],
	] );
}

/**
 * First-week journey: confidential call → assessment → arrival → treatment.
 * Drug-agnostic copy.
 */
function rehab_chrome_journey_steps(): string {
	return rehab_block_journey_steps(
		'Your first week',
		'What to expect when you reach out',
		"From the first confidential call to your arrival in Hua Hin — here's what you can expect in your first week with The Diamond Rehab.",
		[
			[
				'label' => 'STEP 01',
				'title' => 'Confidential call',
				'body'  => 'A free, no-obligation consultation with our intake team. We listen, answer questions, and take the time to understand your situation.',
			],
			[
				'label' => 'STEP 02',
				'title' => 'Clinical assessment',
				'body'  => 'Our psychiatrist evaluates the severity of the addiction, mental-health needs, and recommends a length of stay that fits your case.',
			],
			[
				'label' => 'STEP 03',
				'title' => 'Arrival & onboarding',
				'body'  => 'We arrange airport collection, settle you into private accommodation, and walk you through the next 28 days of structured care.',
			],
			[
				'label' => 'STEP 04',
				'title' => 'Treatment begins',
				'body'  => 'Detox if required, then your bespoke program — therapy, holistic work, fitness, and continuous adjustment of your recovery plan.',
			],
		]
	);
}
