<?php
/**
 * Homepage Section: Concierge Form
 * Contact form with Forminator shortcode placeholder.
 * Falls back to a styled form linking to /contact-us/.
 */
?>
<section class="drt-concierge drt-section" aria-label="Begin your recovery">
	<div class="drt-container drt-container--text">
		<div class="drt-concierge__header">
			<h2 class="drt-heading drt-heading--md">Begin your recovery</h2>
			<div class="drt-concierge__avatar">
				<img
					src="<?php echo esc_url( drt_homepage_img( 'team/sergio-pereira-small.png' ) ); ?>"
					alt="Sergio Pereira, Rehab Director"
					width="96"
					height="96"
					loading="lazy"
				>
			</div>
			<p class="drt-body">
				Speak with our rehab director Sergio to explore availability and personalized care options.
			</p>
		</div>

		<div class="drt-concierge__form-wrap">
			<form class="drt-concierge__form" action="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" method="get">
				<div class="drt-concierge__toggle">
					<button type="button" class="drt-concierge__toggle-btn is-active" data-for="myself">For myself</button>
					<button type="button" class="drt-concierge__toggle-btn" data-for="loved-one">For a loved one</button>
				</div>

				<div class="drt-concierge__grid">
					<div class="drt-concierge__field">
						<label class="drt-concierge__label">Full Name</label>
						<input type="text" name="name" class="drt-concierge__input" placeholder="Enter your full name" required>
					</div>
					<div class="drt-concierge__field">
						<label class="drt-concierge__label">Email Address</label>
						<input type="email" name="email" class="drt-concierge__input" placeholder="your@email.com" required>
					</div>
					<div class="drt-concierge__field">
						<label class="drt-concierge__label">Country</label>
						<select name="country" class="drt-concierge__input drt-concierge__select">
							<option value="">Select country</option>
							<option>Australia</option>
							<option>Canada</option>
							<option>China</option>
							<option>Germany</option>
							<option>Hong Kong</option>
							<option>India</option>
							<option>Indonesia</option>
							<option>Japan</option>
							<option>Malaysia</option>
							<option>New Zealand</option>
							<option>Philippines</option>
							<option>Singapore</option>
							<option>South Korea</option>
							<option>Thailand</option>
							<option>United Arab Emirates</option>
							<option>United Kingdom</option>
							<option>United States</option>
							<option>Other</option>
						</select>
					</div>
					<div class="drt-concierge__field">
						<label class="drt-concierge__label">Contact Number</label>
						<input type="tel" name="phone" class="drt-concierge__input" placeholder="+1 (555) 000-0000" required>
					</div>
					<div class="drt-concierge__field drt-concierge__field--full">
						<label class="drt-concierge__label">Preferred Contact Method</label>
						<select name="contact_method" class="drt-concierge__input drt-concierge__select">
							<option value="">Select preferred method</option>
							<option value="call">Call</option>
							<option value="whatsapp">WhatsApp</option>
							<option value="signal">Signal</option>
						</select>
					</div>
					<div class="drt-concierge__field drt-concierge__field--full">
						<label class="drt-concierge__label">Message</label>
						<textarea name="message" class="drt-concierge__input drt-concierge__textarea" rows="2" placeholder="Share any details about your situation..."></textarea>
					</div>
				</div>

				<div class="drt-concierge__submit">
					<button type="submit" class="drt-btn drt-btn--luxury">Contact Our Team 24/7</button>
					<p class="drt-concierge__helper">Free, confidential, and no-obligation.</p>
				</div>
			</form>
		</div>
	</div>
</section>
