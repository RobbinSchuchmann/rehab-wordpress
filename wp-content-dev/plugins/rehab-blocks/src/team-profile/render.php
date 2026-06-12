<?php
/**
 * Server-side render for `rehab/team-profile`.
 *
 * Two-column layout: role + name + portrait + optional pull-quote + bio on the
 * left, a sticky "[Name] is part of our team" enquiry form on the right. The
 * form posts to rehab/v1/contact via view.js.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a          = $attributes;
$anchor_id  = sanitize_html_class( $a['anchorId'] ?: 'enquire' );
$first      = '' !== $a['firstName'] ? $a['firstName'] : trim( strtok( (string) $a['name'], ' ' ) );
$quote_src  = '' !== $a['quoteSrc'] ? $a['quoteSrc'] : $a['name'];
$paras      = array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", (string) $a['bio'] ) ) );
$trust      = array_filter( (array) ( $a['trustItems'] ?? [] ) );
$bio_title  = '' !== $a['bioTitle'] ? $a['bioTitle'] : ( '' !== $first ? 'About ' . $first : 'About' );

$phone_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';

$wrapper = get_block_wrapper_attributes( [
	'class' => 'rehab-team-profile rehab-bg-' . sanitize_html_class( $a['background'] ?: 'white' ),
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<?php if ( '' !== $a['backText'] ) : ?>
			<a class="rehab-team-profile__back" href="<?php echo esc_url( $a['backUrl'] ?: '/team/' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
				<?php echo esc_html( $a['backText'] ); ?>
			</a>
		<?php endif; ?>

		<div class="rehab-team-profile__layout">
			<div class="rehab-team-profile__main">
				<?php if ( '' !== $a['role'] ) : ?>
					<span class="rehab-team-profile__role"><?php echo wp_kses_post( $a['role'] ); ?></span>
				<?php endif; ?>
				<h1 class="rehab-team-profile__name"><?php echo wp_kses_post( $a['name'] ); ?></h1>

				<div class="rehab-team-profile__portrait">
					<?php if ( '' !== $a['photoUrl'] ) : ?>
						<img src="<?php echo esc_url( $a['photoUrl'] ); ?>" alt="<?php echo esc_attr( $a['photoAlt'] ?: $a['name'] ); ?>" loading="eager" decoding="async" />
					<?php else : ?>
						<div class="rehab-team-profile__portrait-placeholder"><span><?php echo esc_html( $a['name'] ); ?></span></div>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $a['quote'] ) : ?>
					<blockquote class="rehab-team-profile__quote">
						<p><?php echo wp_kses_post( $a['quote'] ); ?></p>
						<?php if ( '' !== $quote_src ) : ?>
							<cite><?php echo esc_html( $quote_src ); ?></cite>
						<?php endif; ?>
					</blockquote>
				<?php endif; ?>

				<?php if ( $paras ) : ?>
					<div class="rehab-team-profile__bio">
						<h2><?php echo esc_html( $bio_title ); ?></h2>
						<?php foreach ( $paras as $p ) : ?>
							<p><?php echo wp_kses_post( $p ); ?></p>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<aside class="rehab-team-profile__rail" id="<?php echo esc_attr( $anchor_id ); ?>">
				<div class="rehab-team-profile__card">
					<?php if ( '' !== $a['formEyebrow'] ) : ?>
						<span class="rehab-team-profile__card-eyebrow"><?php echo wp_kses_post( $a['formEyebrow'] ); ?></span>
					<?php endif; ?>
					<p class="rehab-team-profile__card-name"><?php echo esc_html( $first ); ?> is <b>part of our team</b>, talk with admissions about your care</p>
					<?php if ( '' !== $a['formSub'] ) : ?>
						<p class="rehab-team-profile__card-sub"><?php echo wp_kses_post( $a['formSub'] ); ?></p>
					<?php endif; ?>
					<form data-rehab-contact-form>
						<p class="rehab-team-profile__hp" aria-hidden="true">
							<label>Don't fill this in:<input type="text" name="_hp" tabindex="-1" autocomplete="off" /></label>
						</p>
						<div class="rehab-team-profile__field">
							<label for="<?php echo esc_attr( $anchor_id ); ?>-name">Your name</label>
							<input id="<?php echo esc_attr( $anchor_id ); ?>-name" type="text" name="name" placeholder="First name" required autocomplete="name" />
						</div>
						<div class="rehab-team-profile__field">
							<label for="<?php echo esc_attr( $anchor_id ); ?>-phone">Phone</label>
							<input id="<?php echo esc_attr( $anchor_id ); ?>-phone" type="tel" name="phone" placeholder="+44…" required autocomplete="tel" />
						</div>
						<div class="rehab-team-profile__field">
							<label for="<?php echo esc_attr( $anchor_id ); ?>-email">Email</label>
							<input id="<?php echo esc_attr( $anchor_id ); ?>-email" type="email" name="email" placeholder="you@email.com" required autocomplete="email" />
						</div>
						<div class="rehab-team-profile__field">
							<label for="<?php echo esc_attr( $anchor_id ); ?>-for">This enquiry is for</label>
							<select id="<?php echo esc_attr( $anchor_id ); ?>-for" name="enquiry_for">
								<option>Myself</option>
								<option>A partner or family member</option>
								<option>A friend or colleague</option>
							</select>
						</div>
						<input type="hidden" name="enquiry_about" value="<?php echo esc_attr( $a['name'] ); ?>" />
						<button type="submit" class="rehab-btn rehab-btn--luxury rehab-btn--block"><?php echo wp_kses_post( $a['formSubmit'] ); ?></button>
						<a class="rehab-phone-link rehab-team-profile__card-phone" href="tel:+6633135303"><?php echo $phone_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?><u>Or call +66 3 313 5303</u></a>
						<p class="rehab-team-profile__form-status" role="status" aria-live="polite" data-rehab-form-status></p>
					</form>
					<?php if ( $trust ) : ?>
						<div class="rehab-team-profile__trust">
							<?php foreach ( $trust as $t ) : ?>
								<div><span class="gem" aria-hidden="true">◆</span><?php echo esc_html( $t ); ?></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</aside>
		</div>
	</div>
</section>
