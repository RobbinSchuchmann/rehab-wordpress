<?php
/**
 * Server-side render for `rehab/home-seo-faq`.
 *
 * Emits the exact same drt- markup as the legacy section-seo-faq.php
 * template-part: a `data-drt-tabs="faq"` group whose tab nav switches between
 * FAQ categories, each panel holding `data-drt-accordion` question/answer
 * items. This keeps homepage.js (the data-drt-tabs tab engine AND the
 * data-drt-accordion engine) and the existing drt CSS applying unchanged.
 * Also reproduces the FAQPage JSON-LD schema, built from every category's faqs.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a          = $attributes;
$heading    = (string) ( $a['heading'] ?? '' );
$categories = is_array( $a['categories'] ?? null ) ? $a['categories'] : array();

// Per-block id base keeps faq-tab-* / faq-panel-* / faq-question-* / faq-answer-*
// ids unique across multiple blocks on one page while preserving the template's
// id patterns. The data-drt-* hooks themselves are byte-identical to the template.
$uid = wp_unique_id( 'faq-' );

$wrapper = get_block_wrapper_attributes( array(
	'class'      => 'drt-faq drt-bg-cream drt-section',
	'aria-label' => 'Frequently Asked Questions',
) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container drt-container--narrow">

		<?php if ( '' !== $heading ) : ?>
			<h2 class="drt-heading drt-heading--lg"><?php echo wp_kses_post( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $categories ) : ?>
			<div data-drt-tabs="faq">

				<!-- Tab Navigation -->
				<nav class="drt-tabs__nav drt-tabs__nav--horizontal" role="tablist" aria-label="FAQ categories">
					<?php foreach ( $categories as $index => $category ) :
						$category = array_merge( array( 'key' => '', 'label' => '', 'faqs' => array() ), (array) $category );
						$key      = esc_attr( $uid . '-' . $category['key'] );
						?>
						<button
							class="drt-tabs__trigger<?php echo 0 === $index ? ' is-active' : ''; ?>"
							data-drt-tab-trigger="<?php echo $key; ?>"
							role="tab"
							aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
							aria-controls="faq-panel-<?php echo $key; ?>"
							id="faq-tab-<?php echo $key; ?>"
						>
							<?php echo wp_kses_post( $category['label'] ); ?>
						</button>
					<?php endforeach; ?>
				</nav>

				<!-- Tab Panels -->
				<?php foreach ( $categories as $index => $category ) :
					$category = array_merge( array( 'key' => '', 'label' => '', 'faqs' => array() ), (array) $category );
					$key      = esc_attr( $uid . '-' . $category['key'] );
					$faqs     = is_array( $category['faqs'] ) ? $category['faqs'] : array();
					?>
					<div
						class="drt-tabs__panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
						data-drt-tab-panel="<?php echo $key; ?>"
						role="tabpanel"
						aria-labelledby="faq-tab-<?php echo $key; ?>"
						id="faq-panel-<?php echo $key; ?>"
					>
						<div class="drt-faq__list">
							<?php foreach ( $faqs as $item_index => $item ) :
								$item    = array_merge( array( 'question' => '', 'answer' => '' ), (array) $item );
								$item_id = esc_attr( $key . '-' . $item_index );
							?>
								<div class="drt-accordion__item" data-drt-accordion>
									<button
										class="drt-accordion__trigger"
										data-drt-accordion-trigger
										aria-expanded="false"
										aria-controls="faq-answer-<?php echo $item_id; ?>"
										id="faq-question-<?php echo $item_id; ?>"
									>
										<span class="drt-accordion__trigger-text"><?php echo wp_kses_post( $item['question'] ); ?></span>
										<span class="drt-accordion__trigger-icon" aria-hidden="true"></span>
									</button>
									<div
										class="drt-accordion__content"
										data-drt-accordion-content
										role="region"
										aria-labelledby="faq-question-<?php echo $item_id; ?>"
										id="faq-answer-<?php echo $item_id; ?>"
									>
										<p><?php echo wp_kses_post( $item['answer'] ); ?></p>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>

			</div><!-- [data-drt-tabs="faq"] -->
		<?php endif; ?>

	</div><!-- .drt-container -->
</section>

<?php
// JSON-LD FAQPage schema markup for all questions across all categories.
$schema_entities = array();
foreach ( $categories as $category ) {
	$category = array_merge( array( 'key' => '', 'label' => '', 'faqs' => array() ), (array) $category );
	$faqs     = is_array( $category['faqs'] ) ? $category['faqs'] : array();
	foreach ( $faqs as $item ) {
		$item = array_merge( array( 'question' => '', 'answer' => '' ), (array) $item );
		if ( '' === $item['question'] ) {
			continue;
		}
		$schema_entities[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $item['question'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $item['answer'] ),
			),
		);
	}
}

if ( $schema_entities ) :
	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $schema_entities,
	);
	?>
	<script type="application/ld+json">
	<?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
	</script>
	<?php
endif;
