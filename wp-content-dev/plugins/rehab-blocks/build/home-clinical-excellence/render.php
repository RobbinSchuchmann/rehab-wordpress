<?php
/**
 * Server-side render for `rehab/home-clinical-excellence`.
 *
 * Row of value/label stat cards with dividers. Emits the same drt- markup
 * as the legacy section-clinical-excellence.php template-part.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a     = $attributes;
$stats = is_array( $a['stats'] ?? null ) ? $a['stats'] : [];

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-clinical drt-bg-white',
	'aria-label' => 'Clinical Excellence',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container drt-container--narrow">
		<div class="drt-clinical__grid">
			<?php foreach ( $stats as $index => $stat ) :
				$stat       = array_merge( [ 'value' => '', 'label' => '' ], (array) $stat );
				$last_class = $index < count( $stats ) - 1 ? ' drt-clinical__stat--bordered' : '';
				?>
				<div class="drt-clinical__stat<?php echo esc_attr( $last_class ); ?>">
					<span class="drt-clinical__value"><?php echo wp_kses_post( $stat['value'] ); ?></span>
					<span class="drt-clinical__label"><?php echo wp_kses_post( $stat['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
