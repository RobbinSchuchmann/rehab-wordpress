<?php
/**
 * Homepage Section: Admissions Flowchart
 * 5-step admissions process. Desktop: horizontal with SVG path. Mobile: vertical timeline.
 */

$steps = array(
	array(
		'icon'    => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
		'heading' => 'Confidential consultation',
		'text'    => 'Speak immediately with our admissions manager, Sergio, to verify suitability for The Diamond Rehab Thailand and discuss urgent needs in absolute confidence.',
	),
	array(
		'icon'    => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',
		'heading' => 'Clinical & travel clearance',
		'text'    => 'Our medical team reviews your history to ensure safety. Once approved, we secure your private pool bungalow and assist with flight arrangements.',
	),
	array(
		'icon'    => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-2-3.2-2H7c-1.3 0-2.5.6-3.2 1.7L2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>',
		'heading' => 'VIP airport transfer',
		'text'    => 'You are greeted airside at Bangkok (BKK) by our private chauffeur. Enjoy a comfortable, discreet transfer directly to our sanctuary in Hua Hin.',
	),
	array(
		'icon'    => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572"/><path d="M12 6l-3.5 6h7z"/></svg>',
		'heading' => '24/7 Medical stabilization',
		'text'    => 'Upon arrival, receive a full check-up by our medical doctors. If needed, medically supervised detox begins immediately in the comfort of your bungalow with round-the-clock nursing.',
	),
	array(
		'icon'    => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/><path d="M12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M2 12h2"/><path d="M20 12h2"/></svg>',
		'heading' => 'Immersion into therapy',
		'text'    => 'By days 3-5, as physical clarity returns, you begin your tailored schedule of 1-on-1 counseling with your primary therapist and engage in holistic wellness sessions.',
	),
);
?>
<section class="drt-admissions drt-bg-white drt-section" aria-label="Admissions process">
	<div class="drt-container">
		<div class="drt-section-header">
			<h2 class="drt-heading drt-heading--lg">
				The Diamond Rehab Thailand admission process
			</h2>
			<p class="drt-body drt-text-balance">
				We remove the uncertainty. From your first call to settling into your bungalow at The Diamond Rehab Thailand, every step is guided, secure, and handled by our team.
			</p>
		</div>

		<!-- Desktop: Horizontal Flow -->
		<div class="drt-admissions__desktop">
			<!-- SVG S-Curve Path -->
			<svg class="drt-admissions__path" viewBox="0 0 1200 72" preserveAspectRatio="none" fill="none" aria-hidden="true">
				<path d="M 120 36 C 180 36, 220 12, 300 12 S 400 60, 480 60 S 560 12, 660 12 S 760 60, 840 36 S 920 12, 1020 12 C 1060 12, 1080 36, 1080 36" stroke="#BEB39E" stroke-width="1.5" stroke-opacity="0.4" stroke-linecap="round" stroke-dasharray="8 6"/>
			</svg>

			<div class="drt-admissions__steps">
				<?php foreach ( $steps as $i => $step ) : ?>
					<div class="drt-admissions__step">
						<div class="drt-admissions__icon">
							<?php echo $step['icon']; ?>
						</div>
						<span class="drt-eyebrow">Step <?php echo $i + 1; ?></span>
						<h3 class="drt-heading drt-heading--sm drt-admissions__heading"><?php echo esc_html( $step['heading'] ); ?></h3>
						<p class="drt-body drt-admissions__text"><?php echo esc_html( $step['text'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Mobile: Vertical Timeline -->
		<div class="drt-admissions__mobile">
			<?php foreach ( $steps as $i => $step ) : ?>
				<div class="drt-admissions__step-mobile">
					<?php if ( $i < count( $steps ) - 1 ) : ?>
						<div class="drt-admissions__line" aria-hidden="true"></div>
					<?php endif; ?>
					<div class="drt-admissions__icon">
						<?php echo $step['icon']; ?>
					</div>
					<div class="drt-admissions__step-content">
						<span class="drt-eyebrow">Step <?php echo $i + 1; ?></span>
						<h3 class="drt-heading drt-heading--sm"><?php echo esc_html( $step['heading'] ); ?></h3>
						<p class="drt-body"><?php echo esc_html( $step['text'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
