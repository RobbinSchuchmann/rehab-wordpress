<?php
/**
 * Homepage Section: Clinical Excellence
 * Four stat cards in a row with dividers.
 */

$stats = array(
	array( 'value' => '12+', 'label' => 'Years of clinical leadership' ),
	array( 'value' => '35', 'label' => 'Specialized staff members' ),
	array( 'value' => '2:1', 'label' => 'Staff-to-client ratio' ),
	array( 'value' => '30%', 'label' => 'More annual sunshine (Hua Hin)' ),
);
?>
<section class="drt-clinical drt-bg-white" aria-label="Clinical Excellence">
	<div class="drt-container drt-container--narrow">
		<div class="drt-clinical__grid">
			<?php foreach ( $stats as $index => $stat ) :
				$last_class = $index < count( $stats ) - 1 ? ' drt-clinical__stat--bordered' : '';
			?>
				<div class="drt-clinical__stat<?php echo $last_class; ?>">
					<span class="drt-clinical__value"><?php echo esc_html( $stat['value'] ); ?></span>
					<span class="drt-clinical__label"><?php echo esc_html( $stat['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
