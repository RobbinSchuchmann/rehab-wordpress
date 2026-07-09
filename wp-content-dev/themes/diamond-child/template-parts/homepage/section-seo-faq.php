<?php
/**
 * Homepage Section: SEO FAQ
 * Tabbed FAQ section with 5 categories and accordion items.
 * Includes JSON-LD FAQPage schema markup for all questions.
 */

$faq_categories = array(
	array(
		'slug'  => 'privacy',
		'label' => 'Privacy',
		'items' => array(
			array(
				'question' => 'Is treatment at The Diamond Rehab Thailand completely confidential?',
				'answer'   => 'Yes, absolutely. We maintain the highest standards of privacy and confidentiality. Our secluded facility, private accommodations, and strict data protection protocols ensure your stay remains completely discreet. We never disclose client information to any third party.',
			),
			array(
				'question' => 'Do you sign NDAs with clients and staff?',
				'answer'   => 'Yes, all staff members sign comprehensive non-disclosure agreements. For clients requiring additional privacy measures, we offer personalized NDA arrangements and can accommodate specific confidentiality requirements upon request.',
			),
			array(
				'question' => 'How is my privacy protected as a high-profile client?',
				'answer'   => 'We specialize in serving executives, public figures, and high-net-worth individuals. Discrete check-in procedures, private accommodations, NDAs with all staff, and a secluded location ensure absolute confidentiality throughout your stay.',
			),
			array(
				'question' => 'Can I use an alias during my stay?',
				'answer'   => 'Yes, we fully support clients who wish to use an alias for additional privacy. Our team is experienced in accommodating high-profile individuals who require enhanced discretion, and we can tailor arrangements to your specific needs.',
			),
		),
	),
	array(
		'slug'  => 'location',
		'label' => 'Location',
		'items' => array(
			array(
				'question' => 'Where is the facility located and why choose Thailand for recovery?',
				'answer'   => 'The Diamond Rehab Thailand is located in the peaceful coastal town of Hua Hin, offering world-class treatment in a serene, tropical setting at a fraction of Western costs. Thailand\'s warm climate, renowned hospitality, and distance from daily triggers create an ideal healing environment for lasting recovery.',
			),
			array(
				'question' => 'How do I travel to The Diamond Rehab Thailand?',
				'answer'   => 'We provide complimentary VIP airport transfers from Bangkok\'s Suvarnabhumi International Airport. Our concierge team handles all logistics, ensuring a seamless and stress-free arrival experience from the moment you land.',
			),
			array(
				'question' => 'Can family members visit during my treatment?',
				'answer'   => 'Yes, family involvement is highly encouraged. We offer dedicated family therapy sessions, educational programs, and visitation opportunities. Healing family dynamics is often crucial to long-term recovery success.',
			),
			array(
				'question' => 'What is the climate like and what should I pack?',
				'answer'   => 'Thailand enjoys a warm tropical climate year-round. We recommend light, comfortable clothing. All amenities including toiletries, linens, and fitness attire are provided, so you can travel light and focus entirely on your recovery.',
			),
			array(
				'question' => 'What kind of visa do I need?',
				'answer'   => 'You will enter Thailand on a 30-day tourist visa. If you stay longer than 30 days, we bring you to the local immigration office and your visa will be renewed for another 30 days.',
			),
			array(
				'question' => 'Is Thailand a safe place for rehab?',
				'answer'   => 'Thailand is one of the safest countries in the World. Thailand has also one of the best medical care systems in the World with excellent private hospitals.',
			),
		),
	),
	array(
		'slug'  => 'treatment-programs',
		'label' => 'Treatment programs',
		'items' => array(
			array(
				'question' => 'What kind of therapies can I expect during my drug and alcohol rehab?',
				'answer'   => 'The Diamond Rehab Thailand offers a comprehensive program including individual psychotherapy, group therapy, CBT, DBT, trauma-informed care, EMDR, mindfulness-based interventions, and holistic therapies like yoga and meditation. All therapies are delivered by internationally trained clinicians.',
			),
			array(
				'question' => 'Is The Diamond Rehab Thailand licensed?',
				'answer'   => 'Yes, we are fully licensed by The Thai Ministry of Public Health.',
			),
			array(
				'question' => 'Does the treatment include medical detox?',
				'answer'   => 'Yes, we provide medically-supervised detoxification with 24/7 clinical monitoring. Our medical team manages withdrawal symptoms safely and comfortably, using evidence-based protocols tailored to each client\'s needs.',
			),
			array(
				'question' => 'What is the staff-to-client ratio at your facility?',
				'answer'   => 'We maintain an exceptional 2:1 staff-to-client ratio, ensuring personalized attention and care. With a maximum of 12 clients at any time, you receive truly individualized treatment from our multidisciplinary team.',
			),
			array(
				'question' => 'Do you treat co-occurring mental health disorders?',
				'answer'   => 'Yes, dual diagnosis treatment is a core specialty. Our clinical team is experienced in treating anxiety, depression, PTSD, trauma, and other mental health conditions alongside addiction for comprehensive, integrated care.',
			),
			array(
				'question' => 'What does a typical day at The Diamond rehab look like?',
				'answer'   => 'At The Diamond, we manage to create a well-balanced program. In the morning you can do exercise before breakfast. After breakfast, we will have two lectures. After lunch, we will have our third group lecture of the day. You will miss some group lectures if you have a 1 on 1 session scheduled for the day. In the afternoon we go for a walk or do meditation at the beach. In the evening there is a non-compulsory entertainment schedule.',
			),
			array(
				'question' => 'How long does treatment take?',
				'answer'   => 'This depends on the situation. E.g. does the client need a detox? Is addiction the main problem or are the underlying issues more concerning? It is important that the client finish what he/she signed up for. Overall, we advise that 6 -8 weeks in treatment is average and gives the client a good foundation to start a new life at home with the proper aftercare.',
			),
			array(
				'question' => 'Am I free to leave treatment at any time?',
				'answer'   => 'Yes, you are. However, we strongly suggest that you stay for the days you signed up for, if you finish treatment the chances of success in the future will grow hugely.',
			),
			array(
				'question' => 'Is there an aftercare program?',
				'answer'   => 'Aftercare is essential, and therefore we offer two ways to do aftercare. Option one would be that you have weekly zoom meetings with your focal counsellor, who also treated you while at our centre. Option two is that we bring you in contact with one of our aftercare partners around the world, depending on where you live.',
			),
		),
	),
	array(
		'slug'  => 'cost-insurance',
		'label' => 'Cost & insurance',
		'items' => array(
			array(
				'question' => 'What is the cost of luxury drug and alcohol rehab in Thailand?',
				'answer'   => 'Our all-inclusive programs start at a fraction of comparable Western facilities while delivering superior care. Contact our admissions team for a confidential consultation and personalized pricing based on your treatment needs.',
			),
			array(
				'question' => 'Does The Diamond Rehab Thailand accept international health insurance?',
				'answer'   => 'Yes, we work with most major international insurance providers. Our admissions team can assist with insurance verification and pre-authorization to maximize your coverage.',
			),
			array(
				'question' => 'What is included in the all-inclusive pricing?',
				'answer'   => 'Everything is included: private luxury accommodation, all meals and refreshments, medical care, therapy sessions, holistic activities, fitness programs, airport transfers, and aftercare planning. There are no hidden costs.',
			),
			array(
				'question' => 'Do I need travel insurance?',
				'answer'   => 'Yes, at The Diamond Rehab Thailand travel insurance is mandatory for unexpected things that may happen. At the admission, we will take a copy of your travel insurance.',
			),
			array(
				'question' => 'If I leave earlier, do I get a refund?',
				'answer'   => 'We have a refund policy: if you leave within 72 hours of arrival, we will refund you 50% of the payment. After 72 hours, there is no refund.',
			),
			array(
				'question' => 'Do I need to bring cash with me?',
				'answer'   => 'It would be good to have cash or a card available as during the weekends, we bring our clients to a lovely outing on the beautiful island of Hua Hin.',
			),
			array(
				'question' => 'Do I need to pay everything upfront?',
				'answer'   => 'No, we require just 50% to be transferred prior to your arrival. The remaining 50% will be charged upon arrival at The Diamond Rehab Thailand.',
			),
			array(
				'question' => 'Is aftercare included in the price?',
				'answer'   => 'Once per week, you can join our free zoom meeting with other ex-clients to discuss and check-in. Weekly aftercare one-to-one with your focal counsellor is not included in the treatment fees.',
			),
			array(
				'question' => 'Is the plane ticket included into the price?',
				'answer'   => 'No, the ticket needs to be booked separately and is not included in the treatment fees.',
			),
		),
	),
	array(
		'slug'  => 'amenities',
		'label' => 'Amenities',
		'items' => array(
			array(
				'question' => 'What are the accommodation standards at the facility?',
				'answer'   => 'Our private bungalows and suites feature premium bedding, en-suite bathrooms, air conditioning, and pool access. The resort-style environment supports healing while providing five-star comfort.',
			),
			array(
				'question' => 'Is there a limit on the number of clients at the facility?',
				'answer'   => 'Yes, we intentionally limit capacity to a maximum of 12 clients at any time. This ensures an intimate, uncrowded environment where you receive personalized attention and genuine privacy throughout your stay.',
			),
			array(
				'question' => 'What dining and wellness amenities are available?',
				'answer'   => 'Enjoy gourmet meals prepared by our private chef, a fully-equipped fitness center, swimming pool, spa treatments, yoga pavilion, meditation gardens, sauna, ice bath, and meditation room. Every amenity is designed to support your physical and mental restoration.',
			),
			array(
				'question' => 'Do you offer shared rooms or private rooms?',
				'answer'   => 'We only offer private rooms.',
			),
			array(
				'question' => 'Do I have access to my laptop, phone, etc.?',
				'answer'   => 'Yes, always you will have access to communicate with the outside world.',
			),
			array(
				'question' => 'Can I have visitors and contact with loved ones?',
				'answer'   => 'Yes, you can.',
			),
			array(
				'question' => 'What should I bring with me?',
				'answer'   => 'A checklist will be provided once the booking is confirmed.',
			),
		),
	),
);

// Build a flat array of all FAQs for the JSON-LD schema.
$all_faqs = array();
foreach ( $faq_categories as $category ) {
	foreach ( $category['items'] as $item ) {
		$all_faqs[] = $item;
	}
}
?>
<section class="drt-faq drt-bg-cream drt-section" aria-label="Frequently Asked Questions">
	<div class="drt-container drt-container--narrow">

		<h2 class="drt-heading drt-heading--lg">Frequently Asked Questions</h2>

		<div data-drt-tabs="faq">

			<!-- Tab Navigation -->
			<nav class="drt-tabs__nav drt-tabs__nav--horizontal" role="tablist" aria-label="FAQ categories">
				<?php foreach ( $faq_categories as $index => $category ) : ?>
					<button
						class="drt-tabs__trigger<?php echo 0 === $index ? ' is-active' : ''; ?>"
						data-drt-tab-trigger="<?php echo esc_attr( $category['slug'] ); ?>"
						role="tab"
						aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						aria-controls="faq-panel-<?php echo esc_attr( $category['slug'] ); ?>"
						id="faq-tab-<?php echo esc_attr( $category['slug'] ); ?>"
					>
						<?php echo esc_html( $category['label'] ); ?>
					</button>
				<?php endforeach; ?>
			</nav>

			<!-- Tab Panels -->
			<?php foreach ( $faq_categories as $index => $category ) : ?>
				<div
					class="drt-tabs__panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
					data-drt-tab-panel="<?php echo esc_attr( $category['slug'] ); ?>"
					role="tabpanel"
					aria-labelledby="faq-tab-<?php echo esc_attr( $category['slug'] ); ?>"
					id="faq-panel-<?php echo esc_attr( $category['slug'] ); ?>"
				>
					<div class="drt-faq__list">
						<?php foreach ( $category['items'] as $item_index => $item ) :
							$item_id = esc_attr( $category['slug'] . '-' . $item_index );
						?>
							<div class="drt-accordion__item" data-drt-accordion>
								<button
									class="drt-accordion__trigger"
									data-drt-accordion-trigger
									aria-expanded="false"
									aria-controls="faq-answer-<?php echo $item_id; ?>"
									id="faq-question-<?php echo $item_id; ?>"
								>
									<span class="drt-accordion__trigger-text"><?php echo esc_html( $item['question'] ); ?></span>
									<span class="drt-accordion__trigger-icon" aria-hidden="true"></span>
								</button>
								<div
									class="drt-accordion__content"
									data-drt-accordion-content
									role="region"
									aria-labelledby="faq-question-<?php echo $item_id; ?>"
									id="faq-answer-<?php echo $item_id; ?>"
								>
									<p><?php echo esc_html( $item['answer'] ); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>

		</div><!-- [data-drt-tabs="faq"] -->

	</div><!-- .drt-container -->
</section>

<?php
// JSON-LD FAQPage schema markup for all questions across all categories.
$schema_entities = array();
foreach ( $all_faqs as $faq ) {
	$schema_entities[] = array(
		'@type'          => 'Question',
		'name'           => $faq['question'],
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $faq['answer'],
		),
	);
}

$schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => $schema_entities,
);
?>
<script type="application/ld+json">
<?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</script>
