<?php
/**
 * Homepage Section: Recovery Journey
 * 3-stage tabs with accordion items inside each stage.
 */

$stages = array(
	'detox' => array(
		'label'      => 'Stage 1: Detox & stabilization',
		'short'      => 'Stage 1: Detox',
		'items'      => array(
			array(
				'id'      => 'detox-1',
				'title'   => 'Clinical assessment & intake',
				'content' => 'Upon arrival, our medical team conducts a thorough evaluation to design your bespoke treatment protocol.',
			),
			array(
				'id'      => 'detox-2',
				'title'   => 'Medically-supervised detox',
				'content' => 'Safety is our priority. We provide 24/7 clinical monitoring to manage withdrawal symptoms with maximum comfort.',
			),
		),
	),
	'therapy' => array(
		'label'      => 'Stage 2: Primary therapeutic care',
		'short'      => 'Stage 2: Therapy',
		'items'      => array(
			array(
				'id'      => 'therapy-1',
				'title'   => 'Intensive individual & group therapy',
				'content' => 'Core healing through evidence-based modalities like CBT, DBT, and trauma-informed sessions led by expert clinicians.',
			),
			array(
				'id'      => 'therapy-2',
				'title'   => 'Holistic & wellness integration',
				'content' => 'Combining traditional therapy with yoga, meditation, and fitness to restore physical and emotional balance.',
			),
		),
	),
	'aftercare' => array(
		'label'      => 'Stage 3: Sustainability & aftercare',
		'short'      => 'Stage 3: Aftercare',
		'items'      => array(
			array(
				'id'      => 'aftercare-1',
				'title'   => 'Family support & reintegration',
				'content' => 'Healing the system, not just the individual. We provide guided sessions for loved ones to ensure long-term success.',
			),
			array(
				'id'      => 'aftercare-2',
				'title'   => 'Lifetime alumni support',
				'content' => "Your journey doesn't end here. Gain access to our global recovery network and ongoing clinical check-ins.",
			),
		),
	),
);

$stage_keys = array_keys( $stages );
?>
<section class="drt-recovery drt-bg-white" aria-label="Recovery journey programs">
	<div class="drt-container">
		<div class="drt-section-header">
			<h2 class="drt-heading drt-heading--lg drt-text-balance">
				Rehab Thailand programs
			</h2>
			<p class="drt-body">
				A holistic, evidence-based program tailored to your unique clinical and personal needs.
			</p>
		</div>

		<div class="drt-recovery__wrap" data-drt-tabs="recovery">
			<!-- Tab Navigation -->
			<nav class="drt-tabs__nav drt-tabs__nav--horizontal drt-recovery__nav" role="tablist" aria-label="Recovery stages">
				<?php $first = true; foreach ( $stages as $key => $stage ) : ?>
					<button
						class="drt-tabs__trigger<?php echo $first ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
						aria-controls="recovery-panel-<?php echo esc_attr( $key ); ?>"
						data-drt-tab-trigger="<?php echo esc_attr( $key ); ?>"
					>
						<span class="drt-recovery__label-short"><?php echo esc_html( $stage['short'] ); ?></span>
						<span class="drt-recovery__label-full"><?php echo esc_html( $stage['label'] ); ?></span>
					</button>
				<?php $first = false; endforeach; ?>
			</nav>

			<!-- Panels -->
			<div class="drt-recovery__panels">
				<?php $first = true; foreach ( $stages as $key => $stage ) : ?>
					<div
						class="drt-tabs__panel<?php echo $first ? ' is-active' : ''; ?>"
						id="recovery-panel-<?php echo esc_attr( $key ); ?>"
						role="tabpanel"
						data-drt-tab-panel="<?php echo esc_attr( $key ); ?>"
					>
						<?php foreach ( $stage['items'] as $i => $item ) : ?>
							<div class="drt-accordion__item" data-drt-accordion>
								<button
									class="drt-accordion__trigger"
									data-drt-accordion-trigger
									aria-expanded="false"
									aria-controls="recovery-acc-<?php echo esc_attr( $item['id'] ); ?>"
								>
									<span class="drt-accordion__trigger-text"><?php echo esc_html( $item['title'] ); ?></span>
									<span class="drt-accordion__trigger-icon" aria-hidden="true">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
									</span>
								</button>
								<div
									class="drt-accordion__content"
									id="recovery-acc-<?php echo esc_attr( $item['id'] ); ?>"
									data-drt-accordion-content
								>
									<p><?php echo esc_html( $item['content'] ); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php $first = false; endforeach; ?>
			</div>
		</div>
	</div>
</section>
