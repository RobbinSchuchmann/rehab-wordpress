<?php
/**
 * Homepage Section: What We Treat
 * 3 tabs (Addiction, Mental Health, Specialized) with treatment cards.
 * Desktop: 2-col grid. Mobile: horizontal Swiper carousel.
 */

$tabs = array(
	'addiction'     => 'Addiction',
	'mental-health' => 'Mental Health',
	'specialized'   => 'Specialized Care',
);

$treatments = array(
	'addiction' => array(
		array(
			'title' => 'Alcohol addiction treatment',
			'desc'  => 'Medically-supervised detox and evidence-based rehabilitation programs designed to break the cycle of alcohol dependency in our serene Thailand sanctuary.',
			'image' => 'treatments/alcohol-addiction-treatment-diamond-rehab.avif',
			'alt'   => 'Alcohol addiction treatment at The Diamond Rehab Thailand',
			'href'  => '/alcohol-addiction/',
		),
		array(
			'title' => 'Drug addiction treatment',
			'desc'  => 'Comprehensive detox and evidence-based recovery programs for substance use disorders, combining clinical intervention with holistic therapies at our luxury Thailand facility.',
			'image' => 'treatments/drug-addiction-treatment-diamond-rehab.avif',
			'alt'   => 'Drug addiction treatment at The Diamond Rehab Thailand',
			'href'  => '/substance-abuse-treatment/',
		),
		array(
			'title' => 'Cocaine addiction treatment',
			'desc'  => 'Specialized evidence-based protocols addressing cocaine addiction through intensive detox, therapy, and neurological recovery in Thailand.',
			'image' => 'treatments/cocaine-addiction-treatment-diamond-rehab.avif',
			'alt'   => 'Cocaine addiction treatment at The Diamond Rehab Thailand',
			'href'  => '/cocaine-addiction-treatment-rehab-thailand/',
		),
		array(
			'title' => 'Meth addiction treatment',
			'desc'  => 'Structured detox and evidence-based recovery programs designed to heal mind and body from methamphetamine dependency at our Thailand rehab.',
			'image' => 'treatments/meth-addiction-treatment-diamond-rehab.avif',
			'alt'   => 'Meth addiction treatment at The Diamond Rehab Thailand',
			'href'  => '/ice-addiction-treatment-rehab-thailand/',
		),
		array(
			'title' => 'Heroin addiction treatment',
			'desc'  => 'Safe, medically-supervised detox and evidence-based opioid recovery programs offering compassionate care in our private Thailand sanctuary.',
			'image' => 'treatments/heroin-addiction-treatment-diamond-rehab.avif',
			'alt'   => 'Heroin addiction treatment at The Diamond Rehab Thailand',
			'href'  => '/heroin-rehab-thailand/',
		),
		array(
			'title' => 'Prescription drug treatment',
			'desc'  => 'Expert detox and evidence-based rehabilitation for prescription medication dependency, delivered with discretion at our Thailand recovery center.',
			'image' => 'treatments/prescription-drug-addiction-treatment-diamond-rehab.avif',
			'alt'   => 'Prescription drug addiction treatment at The Diamond Rehab Thailand',
			'href'  => '/prescribed-medication-rehab/',
		),
	),
	'mental-health' => array(
		array(
			'title' => 'Depression treatment',
			'desc'  => 'Evidence-based therapeutic approaches combining clinical psychiatry and wellness practices to treat depression in our tranquil Thailand environment.',
			'image' => 'treatments/depression-treatment-diamond-rehab.avif',
			'alt'   => 'Depression treatment at The Diamond Rehab Thailand',
			'href'  => '/depression-retreat-thailand/',
		),
		array(
			'title' => 'Anxiety treatment',
			'desc'  => 'Personalized evidence-based treatment addressing root causes of anxiety disorders through cognitive therapy, mindfulness, and clinical support at our Thailand sanctuary.',
			'image' => 'treatments/anxiety-treatment-diamond-rehab.avif',
			'alt'   => 'Anxiety treatment at The Diamond Rehab Thailand',
			'href'  => '/anxiety-rehab-thailand/',
		),
		array(
			'title' => 'PTSD & Trauma treatment',
			'desc'  => 'Evidence-based trauma-informed care utilizing EMDR, somatic therapy, and clinical support for lasting recovery at our Thailand rehabilitation center.',
			'image' => 'treatments/ptst-treatment-diamond-rehab.avif',
			'alt'   => 'PTSD and Trauma treatment at The Diamond Rehab Thailand',
			'href'  => '/ptsd-trauma-retreat/',
		),
		array(
			'title' => 'Codependency treatment',
			'desc'  => 'Specialized therapeutic support designed to break codependent patterns and establish healthy boundaries, fostering emotional independence and sustainable relationship recovery.',
			'image' => 'treatments/codependency-treatment-diamond-rehab.avif',
			'alt'   => 'Codependency treatment at The Diamond Rehab Thailand',
			'href'  => '/codependency-treatment/',
		),
	),
	'specialized' => array(
		array(
			'title' => 'Dual diagnosis treatment',
			'desc'  => 'Expert integrated care for co-occurring disorders, addressing the root causes of dependency through synchronized clinical protocols.',
			'image' => 'treatments/dual-diagnosis-treatment-diamond-rehab.avif',
			'alt'   => 'Dual diagnosis treatment at The Diamond Rehab Thailand',
			'href'  => '/what-is-dual-diagnosis/',
		),
		array(
			'title' => 'Executive burnout treatment',
			'desc'  => 'Specialized recovery for high-performing professionals facing chronic stress and exhaustion, delivered within an ultra-discreet, luxury sanctuary.',
			'image' => 'treatments/executive-treatment-diamond-rehab.avif',
			'alt'   => 'Executive burnout treatment at The Diamond Rehab Thailand',
			'href'  => '/luxury-executive-burnout-thailand/',
		),
		array(
			'title' => 'Process addiction rehab',
			'desc'  => 'Focused therapy for behavioral addictions including gambling, gaming, and cryptocurrency addiction, utilizing targeted cognitive-behavioral interventions.',
			'image' => 'treatments/process-addiction-treatment-diamond-rehab.avif',
			'alt'   => 'Process addiction rehab at The Diamond Rehab Thailand',
			'href'  => '/process-addiction-treatment/',
		),
		array(
			'title' => 'Eating disorder rehab',
			'desc'  => 'Compassionate medical and psychological support for eating disorders including anorexia, bulimia, and binge eating, integrated with nutritional restoration.',
			'image' => 'treatments/eating-disorder-treatment-diamond-rehab.avif',
			'alt'   => 'Eating disorder rehab at The Diamond Rehab Thailand',
			'href'  => '/eating-disorders/',
		),
	),
);
?>
<section class="drt-treat drt-bg-white drt-section--lg" aria-label="What we treat">
	<div class="drt-container">
		<div class="drt-section-header">
			<h2 class="drt-heading drt-heading--lg drt-text-balance">
				What we treat at The Diamond Rehab Thailand
			</h2>
			<p class="drt-body">
				World-class drug and alcohol addiction treatment in Thailand, personalized for every individual.
			</p>
		</div>

		<!-- Tabs -->
		<div data-drt-tabs="treat">
			<nav class="drt-tabs__nav drt-tabs__nav--horizontal" role="tablist" aria-label="Treatment categories">
				<?php $first = true; foreach ( $tabs as $key => $label ) : ?>
					<button
						class="drt-tabs__trigger<?php echo $first ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
						aria-controls="treat-panel-<?php echo esc_attr( $key ); ?>"
						data-drt-tab-trigger="<?php echo esc_attr( $key ); ?>"
					><?php echo esc_html( $label ); ?></button>
				<?php $first = false; endforeach; ?>
			</nav>

			<?php $first = true; foreach ( $treatments as $key => $items ) : ?>
				<div
					class="drt-tabs__panel<?php echo $first ? ' is-active' : ''; ?>"
					id="treat-panel-<?php echo esc_attr( $key ); ?>"
					role="tabpanel"
					data-drt-tab-panel="<?php echo esc_attr( $key ); ?>"
				>
					<!-- Desktop grid -->
					<div class="drt-treat__grid">
						<?php foreach ( $items as $item ) : ?>
							<a href="<?php echo esc_url( $item['href'] ); ?>" class="drt-card--treatment">
								<div class="drt-card--treatment__image">
									<img
										src="<?php echo esc_url( drt_homepage_img( $item['image'] ) ); ?>"
										alt="<?php echo esc_attr( $item['alt'] ); ?>"
										loading="lazy"
									>
								</div>
								<div class="drt-card--treatment__body">
									<h3 class="drt-card--treatment__title"><?php echo esc_html( $item['title'] ); ?></h3>
									<p class="drt-card--treatment__desc"><?php echo esc_html( $item['desc'] ); ?></p>
								</div>
							</a>
						<?php endforeach; ?>
					</div>

					<!-- Mobile Swiper -->
					<div class="drt-treat__swiper swiper" data-drt-swiper="treatment">
						<div class="swiper-wrapper">
							<?php foreach ( $items as $item ) : ?>
								<div class="swiper-slide">
									<a href="<?php echo esc_url( $item['href'] ); ?>" class="drt-card--treatment">
										<div class="drt-card--treatment__image">
											<img
												src="<?php echo esc_url( drt_homepage_img( $item['image'] ) ); ?>"
												alt="<?php echo esc_attr( $item['alt'] ); ?>"
												loading="lazy"
											>
										</div>
										<div class="drt-card--treatment__body">
											<h3 class="drt-card--treatment__title"><?php echo esc_html( $item['title'] ); ?></h3>
											<p class="drt-card--treatment__desc"><?php echo esc_html( $item['desc'] ); ?></p>
										</div>
									</a>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="drt-swiper-dots"></div>
					</div>
				</div>
			<?php $first = false; endforeach; ?>
		</div>
	</div>
</section>
