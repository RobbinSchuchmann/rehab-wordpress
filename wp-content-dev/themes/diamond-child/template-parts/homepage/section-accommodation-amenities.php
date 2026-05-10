<?php
/**
 * Homepage Section: Accommodation & Amenities
 * Vertical tabs (left) + image/content (right).
 */

$accom_tabs = array(
	array(
		'id'      => 'bungalows',
		'label'   => 'Private pool bungalows',
		'image'   => 'gallery/private-bungalow.avif',
		'content' => 'Your own private haven at The Diamond Rehab Thailand. Each bungalow features a King-size bed, luxury ensuite bathroom, and direct pool access, ensuring the 12-client cap translates to absolute personal space.',
	),
	array(
		'id'      => 'grounds',
		'label'   => 'Tropical sanctuary grounds',
		'image'   => 'gallery/sanctuary-grounds.avif',
		'content' => 'Set within the peaceful outskirts of Hua Hin, the grounds at The Diamond Rehab Thailand offer a secluded escape from the noise of the world, fostering deep introspection and recovery.',
	),
	array(
		'id'      => 'wellness',
		'label'   => 'Wellness & fitness center',
		'image'   => 'gallery/wellness-fitness.avif',
		'content' => 'A holistic approach to physical health at The Diamond Rehab Thailand. Access our on-site gym, infinity pool, and yoga pavilion for daily sessions led by our expert instructors.',
	),
	array(
		'id'      => 'dining',
		'label'   => 'Executive lounge & dining',
		'image'   => 'gallery/executive-dining.avif',
		'content' => 'Gourmet nutrition tailored to your recovery at The Diamond Rehab Thailand. Enjoy world-class Thai and international cuisine prepared by our dedicated chef in a sophisticated, social setting.',
	),
	array(
		'id'      => 'clinical',
		'label'   => 'Clinical & medical suites',
		'image'   => 'gallery/clinical-suites.avif',
		'content' => 'Safety meets comfort at The Diamond Rehab Thailand. Our clinical areas provide a discrete and professional environment for assessments, 24/7 nursing care, and medically supervised detox.',
	),
);
?>
<section class="drt-accommodation drt-bg-white drt-section" aria-label="Accommodation and amenities">
	<div class="drt-container">
		<div class="drt-section-header">
			<h2 class="drt-heading drt-heading--lg">
				Drug and alcohol rehab Thailand accommodation and amenities
			</h2>
			<p class="drt-body drt-text-balance">
				Experience recovery in a private 5-star sanctuary where luxury living meets world-class clinical care. Our boutique facility in Hua Hin is strictly capped at 12 clients, ensuring absolute privacy and a tranquil environment designed for sustainable healing.
			</p>
		</div>

		<div class="drt-accommodation__layout" data-drt-tabs="accommodation">
			<!-- Left: Vertical Tabs -->
			<div class="drt-accommodation__tabs">
				<nav class="drt-tabs__nav" role="tablist" aria-label="Accommodation areas">
					<?php $first = true; foreach ( $accom_tabs as $tab ) : ?>
						<button
							class="drt-tabs__trigger<?php echo $first ? ' is-active' : ''; ?>"
							role="tab"
							aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
							aria-controls="accom-panel-<?php echo esc_attr( $tab['id'] ); ?>"
							data-drt-tab-trigger="<?php echo esc_attr( $tab['id'] ); ?>"
						><?php echo esc_html( $tab['label'] ); ?></button>
					<?php $first = false; endforeach; ?>
				</nav>
			</div>

			<!-- Right: Content -->
			<div class="drt-accommodation__content">
				<?php $first = true; foreach ( $accom_tabs as $tab ) : ?>
					<div
						class="drt-tabs__panel<?php echo $first ? ' is-active' : ''; ?>"
						id="accom-panel-<?php echo esc_attr( $tab['id'] ); ?>"
						role="tabpanel"
						data-drt-tab-panel="<?php echo esc_attr( $tab['id'] ); ?>"
					>
						<div class="drt-accommodation__image-wrap">
							<img
								src="<?php echo esc_url( drt_homepage_img( $tab['image'] ) ); ?>"
								alt="<?php echo esc_attr( $tab['label'] ); ?>"
								class="drt-accommodation__image"
								loading="lazy"
								decoding="async"
							>
						</div>
						<div class="drt-accommodation__text">
							<h3 class="drt-heading drt-heading--md"><?php echo esc_html( $tab['label'] ); ?></h3>
							<p class="drt-body"><?php echo esc_html( $tab['content'] ); ?></p>
						</div>
					</div>
				<?php $first = false; endforeach; ?>
			</div>
		</div>
	</div>
</section>
