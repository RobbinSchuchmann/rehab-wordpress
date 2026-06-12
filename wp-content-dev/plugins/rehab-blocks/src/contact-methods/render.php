<?php
/**
 * Server-side render for `rehab/contact-methods`.
 *
 * Methods rail + reassurance card + socials on the left, enquiry form card on
 * the right. The form posts to rehab/v1/contact via view.js.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a         = $attributes;
$anchor_id = sanitize_html_class( $a['anchorId'] ?: 'get-in-touch' );
$methods   = is_array( $a['methods'] ?? null ) ? $a['methods'] : [];
$socials   = is_array( $a['socials'] ?? null ) ? $a['socials'] : [];

$icons = [
	'phone'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
	'whatsapp'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
	'email'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>',
	'facebook'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
	'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
	'twitter'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>',
	'pinterest' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="8" x2="12" y2="21"/><path d="M9 21c-1-4 0-7 1.5-9.5C12 9 16 8 16 11.5c0 2.5-2 4-4 3.5"/><circle cx="12" cy="6" r="3"/></svg>',
	'website'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
];
$check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';

$wrapper = get_block_wrapper_attributes( [
	'class' => 'rehab-contact-methods rehab-bg-' . sanitize_html_class( $a['background'] ?: 'cream' ),
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-contact-methods__grid">
			<div class="rehab-contact-methods__rail">
				<?php if ( '' !== $a['railEyebrow'] ) : ?>
					<span class="rehab-contact-methods__eyebrow"><?php echo wp_kses_post( $a['railEyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="rehab-contact-methods__heading"><?php echo wp_kses_post( $a['railHeading'] ); ?></h2>
				<div class="rehab-contact-methods__list">
					<?php foreach ( $methods as $m ) :
						$m = array_merge( [ 'icon' => 'phone', 'kick' => '', 'value' => '', 'href' => '#' ], (array) $m );
						?>
						<a class="rehab-contact-method" href="<?php echo esc_url( $m['href'] ); ?>">
							<span class="rehab-contact-method__icon"><?php echo $icons[ $m['icon'] ] ?? $icons['phone']; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="rehab-contact-method__body">
								<span class="rehab-contact-method__kick"><?php echo esc_html( $m['kick'] ); ?></span>
								<span class="rehab-contact-method__value"><?php echo esc_html( $m['value'] ); ?></span>
							</span>
							<span class="rehab-contact-method__go" aria-hidden="true">→</span>
						</a>
					<?php endforeach; ?>
				</div>

				<?php if ( '' !== $a['nextTitle'] && ! empty( $a['nextItems'] ) ) : ?>
					<div class="rehab-contact-methods__next">
						<h3><?php echo wp_kses_post( $a['nextTitle'] ); ?></h3>
						<ul>
							<?php foreach ( (array) $a['nextItems'] as $item ) : ?>
								<li><?php echo $check; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( $socials ) : ?>
					<div class="rehab-contact-methods__follow">
						<p><?php echo esc_html( $a['followLabel'] ); ?></p>
						<div class="rehab-contact-methods__follow-row">
							<?php foreach ( $socials as $s ) :
								$s = array_merge( [ 'network' => 'website', 'url' => '#' ], (array) $s );
								?>
								<a href="<?php echo esc_url( $s['url'] ); ?>" aria-label="<?php echo esc_attr( ucfirst( $s['network'] ) ); ?>" target="_blank" rel="noopener"><?php echo $icons[ $s['network'] ] ?? $icons['website']; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<aside class="rehab-contact-methods__form-card" id="<?php echo esc_attr( $anchor_id ); ?>">
				<?php if ( '' !== $a['formEyebrow'] ) : ?>
					<span class="rehab-contact-methods__form-eyebrow"><?php echo wp_kses_post( $a['formEyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="rehab-contact-methods__form-title"><?php echo wp_kses_post( $a['formTitle'] ); ?></h2>
				<?php if ( '' !== $a['formSub'] ) : ?>
					<p class="rehab-contact-methods__form-sub"><?php echo wp_kses_post( $a['formSub'] ); ?></p>
				<?php endif; ?>
				<form data-rehab-contact-form>
					<p class="rehab-contact-methods__hp" aria-hidden="true">
						<label>Don't fill this in:<input type="text" name="_hp" tabindex="-1" autocomplete="off" /></label>
					</p>
					<div class="rehab-contact-methods__field">
						<label for="<?php echo esc_attr( $anchor_id ); ?>-name">Name</label>
						<input id="<?php echo esc_attr( $anchor_id ); ?>-name" type="text" name="name" placeholder="Your name" required autocomplete="name" />
					</div>
					<div class="rehab-contact-methods__field-row">
						<div class="rehab-contact-methods__field">
							<label for="<?php echo esc_attr( $anchor_id ); ?>-email">Email</label>
							<input id="<?php echo esc_attr( $anchor_id ); ?>-email" type="email" name="email" placeholder="you@email.com" required autocomplete="email" />
						</div>
						<div class="rehab-contact-methods__field">
							<label for="<?php echo esc_attr( $anchor_id ); ?>-country">Country</label>
							<input id="<?php echo esc_attr( $anchor_id ); ?>-country" type="text" name="country" placeholder="e.g. United Kingdom" autocomplete="country-name" />
						</div>
					</div>
					<div class="rehab-contact-methods__field">
						<label for="<?php echo esc_attr( $anchor_id ); ?>-phone">Phone</label>
						<input id="<?php echo esc_attr( $anchor_id ); ?>-phone" type="tel" name="phone" placeholder="+44…" required autocomplete="tel" />
					</div>
					<div class="rehab-contact-methods__field">
						<label for="<?php echo esc_attr( $anchor_id ); ?>-message">Message <em>(optional)</em></label>
						<textarea id="<?php echo esc_attr( $anchor_id ); ?>-message" name="message" placeholder="Anything you'd like us to know"></textarea>
					</div>
					<button type="submit" class="rehab-btn rehab-btn--luxury rehab-btn--block"><?php echo wp_kses_post( $a['formSubmit'] ); ?></button>
					<p class="rehab-contact-methods__form-status" role="status" aria-live="polite" data-rehab-form-status></p>
					<?php if ( '' !== $a['formHelper'] ) : ?>
						<p class="rehab-contact-methods__helper"><?php echo wp_kses_post( $a['formHelper'] ); ?></p>
					<?php endif; ?>
				</form>
			</aside>
		</div>
	</div>
</section>
