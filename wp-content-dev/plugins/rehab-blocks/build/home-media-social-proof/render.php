<?php
/**
 * Server-side render for `rehab/home-media-social-proof`.
 *
 * Press/media partner logo grid with CSS-only tooltips. Emits the same
 * drt- markup as the legacy section-media-social-proof.php template-part.
 * Each logo div carries a `data-tooltip` attribute (styled via CSS).
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a     = $attributes;
$logos = is_array( $a['logos'] ?? null ) ? $a['logos'] : [];

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-media drt-bg-cream',
	'aria-label' => 'Featured in',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container">
		<div class="drt-media__grid">
			<?php
			foreach ( $logos as $logo ) :
				$logo = array_merge( [ 'src' => '', 'alt' => '', 'tip' => '' ], (array) $logo );
				$src  = '' !== $logo['src'] ? $logo['src'] : get_stylesheet_directory_uri();
				?>
				<div class="drt-media__item" data-tooltip="<?php echo esc_attr( $logo['tip'] ); ?>">
					<img
						src="<?php echo esc_url( $src ); ?>"
						alt="<?php echo esc_attr( $logo['alt'] ); ?>"
						class="drt-partner-logo"
						loading="lazy"
					>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
