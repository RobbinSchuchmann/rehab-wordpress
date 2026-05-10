<?php
/**
 * Homepage Section: Location
 * Address + embedded Google Map.
 */
$maps_url = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3911.5!2d99.9497!3d12.5764!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30fdab2f7c4b9a85%3A0x8c5e2f09c8b4d4e2!2sThe%20Diamond%20Rehab%20Thailand!5e0!3m2!1sen!2sth!4v1703000000000!5m2!1sen!2sth';
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
