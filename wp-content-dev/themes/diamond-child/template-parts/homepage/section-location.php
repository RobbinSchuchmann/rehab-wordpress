<?php
/**
 * Homepage Section: Location
 * Address + embedded Google Map.
 */
$maps_url = 'https://www.google.com/maps?q=12.5737202,99.9074386&z=16&output=embed';
?>
<section class="drt-location drt-bg-white drt-section" aria-label="Getting here">
	<div class="drt-container drt-container--narrow">
		<div class="drt-location__grid">
			<div class="drt-location__text">
				<h2 class="drt-heading drt-heading--lg">Getting here</h2>
				<div class="drt-location__details">
					<p class="drt-location__name">The Diamond Rehab Thailand</p>
					<address class="drt-body drt-location__address">
						8, Moo 14, Soi Mon Mai Hin Lek Fai<br>
						Hua Hin District<br>
						Chang Wat Prachuap Khiri Khan<br>
						Thailand, 77110
					</address>
				</div>
			</div>
			<div class="drt-location__map">
				<iframe
					src="<?php echo esc_url( $maps_url ); ?>"
					class="drt-location__iframe"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					title="The Diamond Rehab Thailand location"
					allowfullscreen
				></iframe>
			</div>
		</div>
	</div>
</section>
