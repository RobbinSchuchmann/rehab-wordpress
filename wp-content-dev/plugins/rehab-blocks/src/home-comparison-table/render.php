<?php
/**
 * Server-side render for `rehab/home-comparison-table`.
 *
 * Emits the same drt- markup as the legacy section-comparison-table.php
 * template-part: desktop table + mobile feature cards & "view more" accordion.
 * Every editable field is a block attribute; rows is a repeatable array where
 * each row has feature / western / diamond plus highlight / key / hasPhone flags.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a    = $attributes;
$rows = is_array( $a['rows'] ?? null ) ? array_values( $a['rows'] ) : [];

$key_rows   = array_values( array_filter( $rows, function ( $r ) { return ! empty( $r['key'] ); } ) );
$other_rows = array_values( array_filter( $rows, function ( $r ) { return empty( $r['key'] ); } ) );

// Mobile comparison card — closure (safe against multiple block instances).
$render_card = function ( $row ) use ( $a ) {
	$feature  = isset( $row['feature'] ) ? $row['feature'] : '';
	$western  = isset( $row['western'] ) ? $row['western'] : '';
	$diamond  = isset( $row['diamond'] ) ? $row['diamond'] : '';
	$hl       = ! empty( $row['highlight'] ) ? 'drt-comparison__card-hl' : '';
	$has_phone = ! empty( $row['hasPhone'] );
	?>
	<div class="drt-comparison__card">
		<div class="drt-comparison__card-header">
			<span><?php echo wp_kses_post( $feature ); ?></span>
		</div>
		<div class="drt-comparison__card-western">
			<span class="drt-comparison__card-label"><?php echo wp_kses_post( $a['cardWesternLabel'] ); ?></span>
			<p><?php echo wp_kses_post( $western ); ?></p>
		</div>
		<div class="drt-comparison__card-diamond">
			<span class="drt-comparison__card-label"><?php echo wp_kses_post( $a['cardDiamondLabel'] ); ?></span>
			<p class="<?php echo esc_attr( $hl ); ?>">
				<?php echo wp_kses_post( $diamond ); ?>
				<?php if ( $has_phone ) : ?>
					<br><a href="<?php echo esc_url( $a['phoneHref'] ); ?>" class="drt-comparison__call"><?php echo esc_html( $a['callText'] ); ?></a>
				<?php endif; ?>
			</p>
		</div>
	</div>
	<?php
};

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-comparison drt-bg-white drt-section--lg',
	'aria-label' => 'Comparison table',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container">
		<?php if ( '' !== $a['eyebrow'] ) : ?>
			<span class="drt-eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
		<?php endif; ?>

		<h2 class="drt-heading drt-heading--lg drt-comparison__title drt-text-balance">
			<?php echo wp_kses_post( $a['heading'] ); ?>
			<br class="drt-comparison__br-sm">
			<span><?php echo wp_kses_post( $a['headingEmphasis'] ); ?></span>
		</h2>

		<?php if ( '' !== $a['intro'] ) : ?>
			<p class="drt-comparison__intro"><?php echo wp_kses_post( $a['intro'] ); ?></p>
		<?php endif; ?>

		<!-- Mobile: Cards + Accordion -->
		<div class="drt-comparison__mobile">
			<?php foreach ( $key_rows as $row ) : ?>
				<?php $render_card( $row ); ?>
			<?php endforeach; ?>

			<div class="drt-accordion" data-drt-accordion>
				<button class="drt-accordion__trigger drt-comparison__more-trigger" data-drt-accordion-trigger aria-expanded="false">
					<span>View <?php echo count( $other_rows ); ?> more comparisons</span>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
				</button>
				<div class="drt-accordion__content" data-drt-accordion-content>
					<?php foreach ( $other_rows as $row ) : ?>
						<?php $render_card( $row ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<!-- Desktop: Table -->
		<div class="drt-comparison__desktop">
			<table class="drt-comparison__table">
				<thead>
					<tr>
						<th class="drt-comparison__th"><?php echo wp_kses_post( $a['colFeature'] ); ?></th>
						<th class="drt-comparison__th"><?php echo wp_kses_post( $a['colWestern'] ); ?></th>
						<th class="drt-comparison__th drt-comparison__th--diamond"><?php echo wp_kses_post( $a['colDiamond'] ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $i => $row ) :
						$is_last    = $i === count( $rows ) - 1;
						$hl_class   = ! empty( $row['highlight'] ) ? ' drt-comparison__td--highlight' : '';
						$last_class = $is_last ? ' drt-comparison__td--last' : '';
						$has_phone  = ! empty( $row['hasPhone'] );
					?>
						<tr class="drt-comparison__row">
							<td class="drt-comparison__td"><?php echo wp_kses_post( isset( $row['feature'] ) ? $row['feature'] : '' ); ?></td>
							<td class="drt-comparison__td"><?php echo wp_kses_post( isset( $row['western'] ) ? $row['western'] : '' ); ?></td>
							<td class="drt-comparison__td drt-comparison__td--diamond<?php echo esc_attr( $hl_class . $last_class ); ?>">
								<?php if ( $has_phone ) : ?>
									<span class="drt-comparison__price-row">
										<?php echo wp_kses_post( isset( $row['diamond'] ) ? $row['diamond'] : '' ); ?>
										<span class="drt-comparison__sep" aria-hidden="true">|</span>
										<a href="<?php echo esc_url( $a['phoneHref'] ); ?>" class="drt-comparison__call"><?php echo esc_html( $a['callText'] ); ?></a>
									</span>
								<?php else : ?>
									<?php echo wp_kses_post( isset( $row['diamond'] ) ? $row['diamond'] : '' ); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php if ( '' !== $a['footnote'] ) : ?>
			<p class="drt-comparison__footer"><?php echo wp_kses_post( $a['footnote'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
