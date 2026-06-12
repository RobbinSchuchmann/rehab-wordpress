<?php
/**
 * Server-side render for `rehab/assessment-hero`.
 *
 * Assessment-first treatment hero: copy + Google rating + trust signals on
 * the left, inline admissions form card on the right. The form posts to the
 * shared rehab/v1/contact endpoint via view.js.
 *
 * @var array    $attributes Block attributes (block.json defaults already merged).
 * @var string   $content    Unused (no inner blocks).
 * @var WP_Block $block      Block instance.
 */

$a         = $attributes;
$anchor_id = sanitize_html_class( $a['anchorId'] ?: 'assessment' );
$stats     = [];
for ( $i = 1; $i <= 3; $i++ ) {
	if ( '' !== ( $a[ "stat{$i}Num" ] ?? '' ) ) {
		$stats[] = [ 'num' => $a[ "stat{$i}Num" ], 'label' => $a[ "stat{$i}Label" ] ?? '' ];
	}
}

$phone_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
$star_svg  = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'rehab-assessment-hero',
	'aria-label' => 'Hero',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput — get_block_wrapper_attributes() returns escaped output. ?>>
	<div class="rehab-container">
		<div class="rehab-assessment-hero__grid">
			<div class="rehab-assessment-hero__copy">
				<?php if ( '' !== $a['eyebrow'] ) : ?>
					<p class="rehab-assessment-hero__eyebrow"><span class="diamond" aria-hidden="true">◆</span><?php echo wp_kses_post( $a['eyebrow'] ); ?></p>
				<?php endif; ?>
				<h1 class="rehab-assessment-hero__h1"><?php echo wp_kses_post( $a['headline'] ); ?></h1>
				<?php if ( '' !== $a['lede'] ) : ?>
					<p class="rehab-assessment-hero__lede"><?php echo wp_kses_post( $a['lede'] ); ?></p>
				<?php endif; ?>
				<div class="rehab-assessment-hero__cta-row">
					<?php if ( '' !== $a['primaryText'] ) : ?>
						<a class="rehab-btn rehab-btn--luxury" href="<?php echo esc_url( $a['primaryUrl'] ?: '#' . $anchor_id ); ?>"><?php echo wp_kses_post( $a['primaryText'] ); ?></a>
					<?php endif; ?>
					<?php if ( '' !== $a['phoneText'] ) : ?>
						<a class="rehab-phone-link" href="<?php echo esc_url( $a['phoneHref'] ); ?>"><?php echo $phone_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?><u><?php echo esc_html( $a['phoneText'] ); ?></u></a>
					<?php endif; ?>
				</div>
				<?php if ( $a['showRating'] && '' !== $a['ratingScore'] ) : ?>
					<div class="rehab-assessment-hero__rating">
						<span class="rehab-assessment-hero__stars" aria-hidden="true"><?php echo str_repeat( $star_svg, 5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span><strong><?php echo esc_html( $a['ratingScore'] ); ?></strong> <?php echo wp_kses_post( $a['ratingText'] ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $stats ) ) : ?>
					<div class="rehab-assessment-hero__signals">
						<?php foreach ( $stats as $stat ) : ?>
							<div class="rehab-assessment-hero__signal">
								<div class="num"><?php echo esc_html( $stat['num'] ); ?></div>
								<div class="lbl"><?php echo wp_kses_post( $stat['label'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<aside class="rehab-assessment-hero__form-card" id="<?php echo esc_attr( $anchor_id ); ?>">
				<?php if ( '' !== $a['formEyebrow'] ) : ?>
					<p class="rehab-assessment-hero__form-eyebrow"><?php echo wp_kses_post( $a['formEyebrow'] ); ?></p>
				<?php endif; ?>
				<h3 class="rehab-assessment-hero__form-title"><?php echo wp_kses_post( $a['formTitle'] ); ?></h3>
				<?php if ( '' !== $a['formSub'] ) : ?>
					<p class="rehab-assessment-hero__form-sub"><?php echo wp_kses_post( $a['formSub'] ); ?></p>
				<?php endif; ?>
				<form data-rehab-contact-form>
					<p class="rehab-assessment-hero__hp" aria-hidden="true">
						<label>Don't fill this in:<input type="text" name="_hp" tabindex="-1" autocomplete="off" /></label>
					</p>
					<div class="rehab-assessment-hero__form-row">
						<div class="rehab-assessment-hero__field">
							<label for="<?php echo esc_attr( $anchor_id ); ?>-name">Your name</label>
							<input id="<?php echo esc_attr( $anchor_id ); ?>-name" type="text" name="name" placeholder="First name" required autocomplete="name" />
						</div>
						<div class="rehab-assessment-hero__field">
							<label for="<?php echo esc_attr( $anchor_id ); ?>-phone">Phone</label>
							<input id="<?php echo esc_attr( $anchor_id ); ?>-phone" type="tel" name="phone" placeholder="+44…" required autocomplete="tel" />
						</div>
					</div>
					<div class="rehab-assessment-hero__field">
						<label for="<?php echo esc_attr( $anchor_id ); ?>-email">Email</label>
						<input id="<?php echo esc_attr( $anchor_id ); ?>-email" type="email" name="email" placeholder="you@email.com" required autocomplete="email" />
					</div>
					<div class="rehab-assessment-hero__field">
						<label for="<?php echo esc_attr( $anchor_id ); ?>-for">This enquiry is for</label>
						<select id="<?php echo esc_attr( $anchor_id ); ?>-for" name="enquiry_for">
							<option>Myself</option>
							<option>A partner or family member</option>
							<option>A friend or colleague</option>
						</select>
					</div>
					<button type="submit" class="rehab-btn rehab-btn--luxury rehab-btn--block"><?php echo wp_kses_post( $a['formSubmit'] ); ?></button>
					<?php if ( '' !== $a['formPhoneLabel'] ) : ?>
						<a class="rehab-phone-link rehab-assessment-hero__form-phone" href="<?php echo esc_url( $a['phoneHref'] ); ?>"><?php echo $phone_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?><u><?php echo esc_html( $a['formPhoneLabel'] ); ?></u></a>
					<?php endif; ?>
					<p class="rehab-assessment-hero__form-status" role="status" aria-live="polite" data-rehab-form-status></p>
					<?php if ( '' !== $a['formConsent'] ) : ?>
						<p class="rehab-assessment-hero__consent"><?php echo wp_kses_post( $a['formConsent'] ); ?></p>
					<?php endif; ?>
				</form>
			</aside>
		</div>
	</div>
</section>
