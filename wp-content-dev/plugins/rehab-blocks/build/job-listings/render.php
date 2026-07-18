<?php
/**
 * Server-side render for `rehab/job-listings`.
 *
 * Renders an optional section head (eyebrow / heading / lede) followed by a
 * repeatable list of jobs. Each job uses the same markup + CSS as
 * `rehab/article-row` (image left, eyebrow + title + body right), with
 * alternating white/cream backgrounds.
 *
 * jobs = [ [ 'imageUrl' => …, 'imageAlt' => …, 'eyebrow' => …, 'title' => …,
 *            'body' => …, 'applyText' => …, 'applyUrl' => … ], … ]
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a    = $attributes;
$jobs = is_array( $a['jobs'] ?? null ) ? $a['jobs'] : [];

$aspect_class = ( ( $a['imageAspect'] ?? 'wide' ) === 'wide' ) ? ' rehab-article-row__media--wide' : '';

// Emit the anchor id explicitly — dynamic blocks don't get it from the
// `supports.anchor` declaration alone, and the careers hero links to
// #OpenPositions (REH-156).
$wrapper_attrs = [ 'class' => 'rehab-job-listings' ];
if ( ! empty( $a['anchor'] ) ) {
	$wrapper_attrs['id'] = $a['anchor'];
}
$wrapper = get_block_wrapper_attributes( $wrapper_attrs );

$has_head = '' !== ( $a['eyebrow'] ?? '' ) || '' !== ( $a['heading'] ?? '' ) || '' !== ( $a['lede'] ?? '' );
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php if ( $has_head ) : ?>
		<section class="rehab-job-listings__head-section rehab-bg-<?php echo esc_attr( $a['background'] ?: 'cream' ); ?>">
			<div class="rehab-container">
				<div class="rehab-job-listings__head">
					<?php if ( '' !== ( $a['eyebrow'] ?? '' ) ) : ?>
						<span class="rehab-job-listings__eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== ( $a['heading'] ?? '' ) ) : ?>
						<h2 class="rehab-job-listings__heading"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== ( $a['lede'] ?? '' ) ) : ?>
						<p class="rehab-job-listings__lede"><?php echo wp_kses_post( $a['lede'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php foreach ( $jobs as $i => $job ) :
		$job = array_merge(
			[ 'imageUrl' => '', 'imageAlt' => '', 'eyebrow' => '', 'title' => '', 'body' => '', 'applyText' => '', 'applyUrl' => '' ],
			(array) $job
		);
		$bg         = ( 0 === $i % 2 ) ? 'white' : 'cream';
		$paragraphs = array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", trim( (string) $job['body'] ) ) ) );
		?>
		<section class="rehab-article-row-section rehab-bg-<?php echo esc_attr( $bg ); ?>">
			<div class="rehab-container">
				<div class="rehab-article-row">
					<div class="rehab-article-row__media<?php echo esc_attr( $aspect_class ); ?>">
						<?php if ( '' !== $job['imageUrl'] ) : ?>
							<img src="<?php echo esc_url( $job['imageUrl'] ); ?>" alt="<?php echo esc_attr( $job['imageAlt'] ); ?>"/>
						<?php else : ?>
							<div class="rehab-article-row__media-placeholder"><span><?php echo esc_html( $job['imageAlt'] ?: 'Image' ); ?></span></div>
						<?php endif; ?>
					</div>
					<div class="rehab-article-row__text">
						<?php if ( '' !== $job['eyebrow'] ) : ?>
							<span class="rehab-article-row__eyebrow"><?php echo esc_html( $job['eyebrow'] ); ?></span>
						<?php endif; ?>
						<h3 class="rehab-article-row__heading"><?php echo esc_html( $job['title'] ); ?></h3>
						<?php foreach ( $paragraphs as $p ) : ?>
							<p><?php echo esc_html( $p ); ?></p>
						<?php endforeach; ?>
						<?php if ( '' !== $job['applyText'] && '' !== $job['applyUrl'] ) : ?>
							<div class="rehab-article-row__cta">
								<a href="<?php echo esc_url( $job['applyUrl'] ); ?>" class="rehab-btn rehab-btn--luxury"><?php echo esc_html( $job['applyText'] ); ?></a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endforeach; ?>
</div>
