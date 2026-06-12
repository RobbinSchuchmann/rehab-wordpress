<?php
/**
 * Server-side render for `rehab/stat-band`.
 *
 * Light stat band: warm-cream background, large Ivymode numerals, sage
 * hairline separators. A trailing "+" or symbol in the number can be
 * accented by wrapping it in <em> (rendered as the sage gem accent).
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$stats = [];
for ( $i = 1; $i <= 4; $i++ ) {
	$num = $attributes[ "stat{$i}Num" ] ?? '';
	if ( '' !== $num ) {
		$stats[] = [ 'num' => $num, 'label' => $attributes[ "stat{$i}Label" ] ?? '' ];
	}
}

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'rehab-stat-band',
	'aria-label' => 'Key figures',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-stat-band__grid">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="rehab-stat-band__item">
					<div class="rehab-stat-band__value"><?php echo wp_kses( $stat['num'], [ 'em' => [], 'span' => [ 'class' => [] ] ] ); ?></div>
					<div class="rehab-stat-band__label"><?php echo wp_kses_post( $stat['label'] ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
