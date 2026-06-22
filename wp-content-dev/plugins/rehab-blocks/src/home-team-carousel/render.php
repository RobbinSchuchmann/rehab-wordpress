<?php
/**
 * Server-side render for `rehab/home-team-carousel`.
 *
 * Emits the same drt- markup as the legacy section-team-carousel.php
 * template-part: a section header plus a `data-drt-swiper="team"` Swiper
 * carousel of team member slides with hover overlays. Every Swiper class,
 * the nav buttons and the dots container are kept byte-identical so the
 * homepage.js team Swiper init wires up unchanged.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a        = $attributes;
$members  = is_array( $a['members'] ?? null ) ? $a['members'] : [];
$img_base = get_stylesheet_directory_uri() . '/assets/images/homepage/team/';

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-team drt-bg-white drt-section',
	'aria-label' => 'Our team',
] );

/**
 * Resolve a member photo to a URL. MediaUpload stores a full URL; a bare
 * filename resolves against the bundled homepage team image directory; an
 * empty field falls back to a placeholder.
 */
$resolve_img = static function ( $image ) use ( $img_base ) {
	$image = (string) $image;
	if ( '' === $image ) {
		return $img_base . 'placeholder.avif';
	}
	if ( preg_match( '#^https?://#i', $image ) || str_starts_with( $image, '/' ) ) {
		return $image;
	}
	return $img_base . ltrim( $image, '/' );
};
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-container">
		<div class="drt-section-header">
			<?php if ( '' !== $a['eyebrow'] ) : ?>
				<span class="drt-eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $a['heading'] ) : ?>
				<h2 class="drt-heading drt-heading--lg drt-text-balance">
					<?php echo wp_kses_post( $a['heading'] ); ?>
				</h2>
			<?php endif; ?>
			<?php if ( '' !== $a['intro'] ) : ?>
				<p class="drt-body">
					<?php echo wp_kses_post( $a['intro'] ); ?>
				</p>
			<?php endif; ?>
		</div>

		<div class="drt-team__carousel">
			<div class="swiper" data-drt-swiper="team">
				<div class="swiper-wrapper">
					<?php foreach ( $members as $member ) :
						$name = (string) ( $member['name'] ?? '' );
						$role = (string) ( $member['role'] ?? '' );
						$alt  = (string) ( $member['alt'] ?? '' );
						if ( '' === $alt ) {
							$alt = trim( $name . ' - ' . $role, ' -' );
						}
						?>
						<div class="swiper-slide">
							<div class="drt-team__member">
								<img
									src="<?php echo esc_url( $resolve_img( $member['photo'] ?? '' ) ); ?>"
									alt="<?php echo esc_attr( $alt ); ?>"
									class="drt-team__photo"
									loading="lazy"
								>
								<div class="drt-team__overlay">
									<h3 class="drt-team__name"><?php echo esc_html( $name ); ?></h3>
									<p class="drt-team__title"><?php echo esc_html( $role ); ?></p>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<button class="swiper-button-prev drt-swiper-arrow" aria-label="Previous slide">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
			</button>
			<button class="swiper-button-next drt-swiper-arrow" aria-label="Next slide">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
			</button>
			<div class="drt-swiper-dots drt-team__dots"></div>
		</div>
	</div>
</section>
