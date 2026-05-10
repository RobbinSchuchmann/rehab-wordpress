<?php
/**
 * Homepage Section: Comparison Table
 * Desktop: full table. Mobile: key feature cards + accordion for the rest.
 */

$features = array(
	array(
		'feature'   => 'Typical cost (4 weeks)',
		'western'   => '$60,000 – $100,000+',
		'diamond'   => 'Considerably lower',
		'highlight' => true,
		'key'       => true,
		'has_phone' => true,
	),
	array(
		'feature'   => 'Clinical leadership',
		'western'   => 'Board-certified psychiatrist',
		'diamond'   => 'Board-certified psychiatrist',
		'highlight' => false,
		'key'       => false,
	),
	array(
		'feature'   => '1-on-1 therapy intensity',
		'western'   => 'Typically 1–2 sessions / week',
		'diamond'   => '4 specialized sessions / week*',
		'highlight' => true,
		'key'       => true,
	),
	array(
		'feature'   => 'Accommodation tier',
		'western'   => 'Private room (clinical setting)',
		'diamond'   => 'Private pool bungalow (resort setting)',
		'highlight' => true,
		'key'       => true,
	),
	array(
		'feature'   => 'Massages & wellness',
		'western'   => 'Often charged as "extras"',
		'diamond'   => 'Included weekly',
		'highlight' => true,
		'key'       => false,
	),
	array(
		'feature'   => 'Facility capacity',
		'western'   => 'Often 20–50+ clients',
		'diamond'   => 'Strictly capped at 12',
		'highlight' => true,
		'key'       => false,
	),
	array(
		'feature'   => 'Primary cost driver',
		'western'   => 'Insurance & administration',
		'diamond'   => 'Direct client care',
		'highlight' => true,
		'key'       => false,
	),
);

$key_features   = array_filter( $features, function( $f ) { return ! empty( $f['key'] ); } );
$other_features = array_filter( $features, function( $f ) { return empty( $f['key'] ); } );
?>
<section class="drt-comparison drt-bg-white drt-section--lg" aria-label="Comparison table">
	<div class="drt-container">
		<h2 class="drt-heading drt-heading--lg drt-comparison__title drt-text-balance">
			World-class luxury drug and alcohol rehab in Thailand
			<br class="drt-comparison__br-sm">
			<span>without Western administrative overheads</span>
		</h2>

		<!-- Mobile: Cards + Accordion -->
		<div class="drt-comparison__mobile">
			<?php foreach ( $key_features as $row ) : ?>
				<?php drt_comparison_card( $row ); ?>
			<?php endforeach; ?>

			<div class="drt-accordion" data-drt-accordion>
				<button class="drt-accordion__trigger drt-comparison__more-trigger" data-drt-accordion-trigger aria-expanded="false">
					<span>View <?php echo count( $other_features ); ?> more comparisons</span>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
				</button>
				<div class="drt-accordion__content" data-drt-accordion-content>
					<?php foreach ( $other_features as $row ) : ?>
						<?php drt_comparison_card( $row ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<!-- Desktop: Table -->
		<div class="drt-comparison__desktop">
			<table class="drt-comparison__table">
				<thead>
					<tr>
						<th class="drt-comparison__th">Feature</th>
						<th class="drt-comparison__th">Premier western luxury rehab</th>
						<th class="drt-comparison__th drt-comparison__th--diamond">The Diamond Rehab Thailand</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $features as $i => $row ) :
						$is_last   = $i === count( $features ) - 1;
						$hl_class  = ! empty( $row['highlight'] ) ? ' drt-comparison__td--highlight' : '';
						$last_class = $is_last ? ' drt-comparison__td--last' : '';
					?>
						<tr class="drt-comparison__row">
							<td class="drt-comparison__td"><?php echo esc_html( $row['feature'] ); ?></td>
							<td class="drt-comparison__td"><?php echo esc_html( $row['western'] ); ?></td>
							<td class="drt-comparison__td drt-comparison__td--diamond<?php echo $hl_class . $last_class; ?>">
								<?php if ( ! empty( $row['has_phone'] ) ) : ?>
									<span class="drt-comparison__price-row">
										<?php echo esc_html( $row['diamond'] ); ?>
										<span class="drt-comparison__sep" aria-hidden="true">|</span>
										<a href="tel:+61279082277" class="drt-comparison__call">Call for current pricing</a>
									</span>
								<?php else : ?>
									<?php echo esc_html( $row['diamond'] ); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<p class="drt-comparison__footer">
			*Our rates reflect Thailand&rsquo;s lower operational costs, allowing us to redirect resources toward superior staffing ratios and luxury amenities without compromising clinical&nbsp;excellence.
		</p>
	</div>
</section>

<?php
/**
 * Helper: Render a single comparison card (mobile).
 */
function drt_comparison_card( $row ) { ?>
	<div class="drt-comparison__card">
		<div class="drt-comparison__card-header">
			<span><?php echo esc_html( $row['feature'] ); ?></span>
		</div>
		<div class="drt-comparison__card-western">
			<span class="drt-comparison__card-label">Western Luxury Rehab</span>
			<p><?php echo esc_html( $row['western'] ); ?></p>
		</div>
		<div class="drt-comparison__card-diamond">
			<span class="drt-comparison__card-label">Diamond Rehab Thailand</span>
			<p class="<?php echo ! empty( $row['highlight'] ) ? 'drt-comparison__card-hl' : ''; ?>">
				<?php echo esc_html( $row['diamond'] ); ?>
				<?php if ( ! empty( $row['has_phone'] ) ) : ?>
					<br><a href="tel:+61279082277" class="drt-comparison__call">Call for current pricing</a>
				<?php endif; ?>
			</p>
		</div>
	</div>
<?php }
