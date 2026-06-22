<?php
/**
 * Server-side render for `rehab/home-location`.
 *
 * Homepage location section: heading + address + embedded map. Emits the same
 * drt- markup as the legacy section-location.php template-part.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a = $attributes;

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-location drt-bg-white drt-section',
	'aria-label' => 'Getting here',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container drt-container--narrow">
		<div class="drt-location__grid">
			<div class="drt-location__text">
				<?php if ( '' !== $a['heading'] ) : ?>
					<h2 class="drt-heading drt-heading--lg"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
				<?php endif; ?>
				<div class="drt-location__details">
					<?php if ( '' !== $a['placeName'] ) : ?>
						<p class="drt-location__name"><?php echo wp_kses_post( $a['placeName'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $a['address'] ) : ?>
						<address class="drt-body drt-location__address"><?php echo wp_kses_post( $a['address'] ); ?></address>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( '' !== $a['mapSrc'] ) : ?>
				<div class="drt-location__map">
					<iframe
						src="<?php echo esc_url( $a['mapSrc'] ); ?>"
						class="drt-location__iframe"
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
						title="<?php echo esc_attr( $a['mapTitle'] ); ?>"
						allowfullscreen
					></iframe>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
