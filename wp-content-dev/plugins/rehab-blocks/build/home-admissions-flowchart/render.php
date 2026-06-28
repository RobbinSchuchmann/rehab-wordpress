<?php
/**
 * Server-side render for `rehab/home-admissions-flowchart`.
 *
 * Admissions process: heading + intro + repeatable steps. Emits the same
 * drt- markup as the legacy section-admissions-flowchart.php template-part:
 * a desktop horizontal SVG flow and a mobile vertical timeline that share the
 * same step list. Per-step "Step N" eyebrow keeps the template's index logic.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a     = $attributes;
$steps = is_array( $a['steps'] ?? null ) ? $a['steps'] : array();
$count = count( $steps );

// Resolve each step's eyebrow label, preserving the template's index logic.
$step_label = static function ( $step, $i ) {
	$number = isset( $step['number'] ) ? (string) $step['number'] : '';
	$label  = isset( $step['label'] ) ? (string) $step['label'] : '';
	if ( '' !== $label ) {
		return $label;
	}
	$number = '' !== $number ? $number : (string) ( $i + 1 );
	return 'Step ' . $number;
};

$wrapper = get_block_wrapper_attributes( array(
	'class'      => 'drt-admissions drt-bg-white drt-section',
	'aria-label' => 'Admissions process',
) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container">
		<div class="drt-section-header">
			<?php if ( '' !== $a['eyebrow'] ) : ?>
				<span class="drt-eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $a['heading'] ) : ?>
				<h2 class="drt-heading drt-heading--lg">
					<?php echo wp_kses_post( $a['heading'] ); ?>
				</h2>
			<?php endif; ?>
			<?php if ( '' !== $a['intro'] ) : ?>
				<p class="drt-body drt-text-balance">
					<?php echo wp_kses_post( $a['intro'] ); ?>
				</p>
			<?php endif; ?>
			<?php if ( '' !== $a['ctaText'] ) : ?>
				<a href="<?php echo esc_url( $a['ctaUrl'] ?: '#' ); ?>" class="drt-btn drt-btn--luxury">
					<?php echo wp_kses_post( $a['ctaText'] ); ?>
				</a>
			<?php endif; ?>
		</div>

		<!-- Desktop: Horizontal Flow -->
		<div class="drt-admissions__desktop">
			<!-- SVG S-Curve Path -->
			<svg class="drt-admissions__path" viewBox="0 0 1200 72" preserveAspectRatio="none" fill="none" aria-hidden="true">
				<path d="M 120 36 C 180 36, 220 12, 300 12 S 400 60, 480 60 S 560 12, 660 12 S 760 60, 840 36 S 920 12, 1020 12 C 1060 12, 1080 36, 1080 36" style="stroke: var(--rehab-tan, #BEB39E)" stroke-width="1.5" stroke-opacity="0.4" stroke-linecap="round" stroke-dasharray="8 6"/>
			</svg>

			<div class="drt-admissions__steps">
				<?php foreach ( $steps as $i => $step ) : ?>
					<div class="drt-admissions__step">
						<div class="drt-admissions__icon">
							<?php echo isset( $step['icon'] ) ? $step['icon'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
						<span class="drt-eyebrow"><?php echo wp_kses_post( $step_label( $step, $i ) ); ?></span>
						<h3 class="drt-heading drt-heading--sm drt-admissions__heading"><?php echo wp_kses_post( $step['title'] ?? '' ); ?></h3>
						<p class="drt-body drt-admissions__text"><?php echo wp_kses_post( $step['description'] ?? '' ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Mobile: Vertical Timeline -->
		<div class="drt-admissions__mobile">
			<?php foreach ( $steps as $i => $step ) : ?>
				<div class="drt-admissions__step-mobile">
					<?php if ( $i < $count - 1 ) : ?>
						<div class="drt-admissions__line" aria-hidden="true"></div>
					<?php endif; ?>
					<div class="drt-admissions__icon">
						<?php echo isset( $step['icon'] ) ? $step['icon'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
					<div class="drt-admissions__step-content">
						<span class="drt-eyebrow"><?php echo wp_kses_post( $step_label( $step, $i ) ); ?></span>
						<h3 class="drt-heading drt-heading--sm"><?php echo wp_kses_post( $step['title'] ?? '' ); ?></h3>
						<p class="drt-body"><?php echo wp_kses_post( $step['description'] ?? '' ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
