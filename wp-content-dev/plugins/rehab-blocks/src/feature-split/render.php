<?php
/**
 * Server-side render for `rehab/feature-split`.
 *
 * Image + copy split. `body` holds paragraphs separated by blank lines.
 * Optional extras render in order: chips → quote → stats → gem list →
 * footnote → CTA row.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a     = $attributes;
$paras = array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", (string) $a['body'] ) ) );
$chips = array_filter( (array) ( $a['chips'] ?? [] ) );
$stats = is_array( $a['stats'] ?? null ) ? $a['stats'] : [];
$gems  = array_filter( (array) ( $a['gemItems'] ?? [] ) );

$phone_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';

$classes = 'rehab-feature-split rehab-bg-' . sanitize_html_class( $a['background'] ?: 'cream' );
if ( 'right' === $a['imageSide'] ) {
	$classes .= ' rehab-feature-split--image-right';
}
$wrapper = get_block_wrapper_attributes( [ 'class' => $classes ] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-feature-split__grid">
			<div class="rehab-feature-split__media">
				<?php if ( '' !== $a['imageUrl'] ) : ?>
					<img src="<?php echo esc_url( $a['imageUrl'] ); ?>" alt="<?php echo esc_attr( $a['imageAlt'] ); ?>" loading="lazy" decoding="async" />
				<?php else : ?>
					<div class="rehab-feature-split__placeholder"><span><?php echo esc_html( $a['imageAlt'] ?: 'Image' ); ?></span></div>
				<?php endif; ?>
			</div>
			<div class="rehab-feature-split__copy">
				<?php if ( '' !== $a['eyebrow'] ) : ?>
					<span class="rehab-feature-split__eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
				<?php endif; ?>
				<?php $h_tag = 'h1' === ( $a['headingTag'] ?? 'h2' ) ? 'h1' : 'h2'; ?>
				<<?php echo $h_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="rehab-feature-split__heading"><?php echo wp_kses_post( $a['heading'] ); ?></<?php echo $h_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php foreach ( $paras as $p ) : ?>
					<p><?php echo wp_kses_post( $p ); ?></p>
				<?php endforeach; ?>

				<?php if ( $chips ) : ?>
					<div class="rehab-feature-split__chips">
						<?php foreach ( $chips as $chip ) : ?>
							<span><?php echo esc_html( $chip ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $a['quote'] ) : ?>
					<blockquote class="rehab-feature-split__quote">
						<p><?php echo wp_kses_post( $a['quote'] ); ?></p>
						<?php if ( '' !== $a['quoteSrc'] ) : ?>
							<cite><?php echo esc_html( $a['quoteSrc'] ); ?></cite>
						<?php endif; ?>
					</blockquote>
				<?php endif; ?>

				<?php if ( $stats ) : ?>
					<div class="rehab-feature-split__stats">
						<?php foreach ( $stats as $stat ) :
							$stat = array_merge( [ 'v' => '', 'k' => '' ], (array) $stat );
							?>
							<div class="rehab-feature-split__stat">
								<div class="v"><?php echo wp_kses( $stat['v'], [ 'em' => [] ] ); ?></div>
								<div class="k"><?php echo esc_html( $stat['k'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $gems ) : ?>
					<div class="rehab-feature-split__gems">
						<?php foreach ( $gems as $g ) : ?>
							<div class="rehab-feature-split__gem"><span aria-hidden="true">◆</span><?php echo esc_html( $g ); ?></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $a['footnote'] ) : ?>
					<p><?php echo wp_kses_post( $a['footnote'] ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $a['primaryText'] || '' !== $a['phoneText'] ) : ?>
					<div class="rehab-feature-split__cta">
						<?php if ( '' !== $a['primaryText'] ) : ?>
							<a class="rehab-btn rehab-btn--luxury" href="<?php echo esc_url( $a['primaryUrl'] ?: '#' ); ?>"><?php echo wp_kses_post( $a['primaryText'] ); ?></a>
						<?php endif; ?>
						<?php if ( '' !== $a['phoneText'] ) : ?>
							<a class="rehab-phone-link" href="<?php echo esc_url( $a['phoneHref'] ); ?>"><?php echo $phone_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?><u><?php echo esc_html( $a['phoneText'] ); ?></u></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
