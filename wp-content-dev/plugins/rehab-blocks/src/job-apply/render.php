<?php
/**
 * Server-side render for `rehab/job-apply` — the application box on job ad
 * pages (REH-157). Dynamic on purpose: job pages carry only the block comment,
 * so copy tweaks here never require a stored-content migration.
 *
 * @var array $attributes Block attributes.
 */

$a       = $attributes;
$email   = sanitize_email( $a['email'] ?? '' );
$subject = rawurlencode( 'Application: ' . wp_strip_all_tags( get_the_title() ) );
$wrapper = get_block_wrapper_attributes( [ 'class' => 'rehab-job-apply rehab-bg-' . sanitize_html_class( $a['background'] ?: 'cream' ) ] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-job-apply__card">
			<?php if ( '' !== ( $a['eyebrow'] ?? '' ) ) : ?>
				<span class="rehab-eyebrow"><?php echo esc_html( $a['eyebrow'] ); ?></span>
			<?php endif; ?>
			<h2 class="rehab-job-apply__heading"><?php echo esc_html( $a['heading'] ?? '' ); ?></h2>
			<?php if ( '' !== ( $a['body'] ?? '' ) ) : ?>
				<p class="rehab-job-apply__body"><?php echo esc_html( $a['body'] ); ?></p>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<a class="rehab-btn rehab-btn--luxury rehab-job-apply__btn" href="mailto:<?php echo esc_attr( $email ); ?>?subject=<?php echo esc_attr( $subject ); ?>">
					<?php echo esc_html( $a['buttonText'] ?: 'Apply by email' ); ?>
				</a>
				<p class="rehab-job-apply__email"><?php echo esc_html( $email ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== ( $a['helper'] ?? '' ) ) : ?>
				<p class="rehab-job-apply__helper"><?php echo esc_html( $a['helper'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
