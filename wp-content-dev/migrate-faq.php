<?php
/**
 * Replace FAQ block on homepage and FAQ page with Diamond's real FAQ items.
 */

$faq_json = file_get_contents( __DIR__ . '/diamond-faq.json' );
$faq      = json_decode( $faq_json, true );

if ( ! $faq ) {
	exit( 'no faq' );
}

$build_items = static function ( array $items ): string {
	$html = '';
	foreach ( $items as $it ) {
		$q     = $it['q'];
		$a     = $it['a'];
		$attrs = wp_json_encode( [ 'question' => $q, 'answer' => $a ] );
		$html .= sprintf(
			'<!-- wp:rehab/faq-item %s -->' . "\n" .
			'<details class="wp-block-rehab-faq-item rehab-faq-item"><summary>%s</summary><div class="rehab-faq-item__answer">%s</div></details>' . "\n" .
			'<!-- /wp:rehab/faq-item -->' . "\n",
			$attrs,
			$q,
			$a
		);
	}
	return $html;
};

$build_faq_block = static function ( string $heading, array $items, string $bg = 'cream' ) use ( $build_items ): string {
	$inner = $build_items( $items );
	$attrs = wp_json_encode( [ 'background' => $bg, 'heading' => $heading ] );
	return sprintf(
		'<!-- wp:rehab/faq %s -->' . "\n" .
		'<section class="wp-block-rehab-faq rehab-faq rehab-bg-%s" aria-label="Frequently Asked Questions"><div class="rehab-container rehab-container--narrow"><h2 class="rehab-heading rehab-heading--lg rehab-faq__heading">%s</h2><div class="rehab-faq__list">%s</div></div></section>' . "\n" .
		'<!-- /wp:rehab/faq -->',
		$attrs,
		$bg,
		$heading,
		esc_html( $heading ),
		$inner
	);
};

// Homepage: 8-item FAQ
$homepage_faq = $build_faq_block( 'Frequently asked questions', array_slice( $faq, 0, 8 ), 'cream' );

// FAQ page: full 12 items split by category
$faq_page_content = $build_faq_block( 'Privacy & confidentiality', array_slice( $faq, 0, 4 ), 'cream' )
	. "\n\n" . $build_faq_block( 'Location & travel', array_slice( $faq, 4, 4 ), 'white' )
	. "\n\n" . $build_faq_block( 'Treatment programs', array_slice( $faq, 8 ), 'cream' );

// Update homepage's FAQ block
$home = get_post( 6 );
$home_content = preg_replace(
	'/<!--\s*wp:rehab\/faq[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/faq\s*-->)/is',
	$homepage_faq,
	$home->post_content,
	1
);
wp_update_post( [ 'ID' => 6, 'post_content' => $home_content ] );
echo "OK homepage FAQ updated" . PHP_EOL;

// Replace entire FAQ page content
$faq_intro_cta = '<!-- wp:rehab/cta {"variant":"compact","background":"sage-mist","heading":"Frequently asked questions","body":"Common questions we hear from families and clients. Reach out anytime if your question isn\'t covered."} /-->' . "\n";
$faq_outro_cta = "\n\n" . '<!-- wp:rehab/cta /-->';
wp_update_post( [
	'ID'           => 1197,
	'post_content' => $faq_intro_cta . $faq_page_content . $faq_outro_cta,
] );
echo "OK FAQ page updated" . PHP_EOL;
