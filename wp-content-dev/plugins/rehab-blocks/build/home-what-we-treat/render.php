<?php
/**
 * Server-side render for `rehab/home-what-we-treat`.
 *
 * Emits the same drt- markup as the legacy section-what-we-treat.php
 * template-part: a section header plus a `data-drt-tabs` group of treatment
 * categories, each with a desktop grid + mobile Swiper of treatment cards.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a          = $attributes;
$categories = is_array( $a['categories'] ?? null ) ? $a['categories'] : [];
$img_base   = get_stylesheet_directory_uri() . '/assets/images/homepage/';

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-treat drt-bg-white drt-section--lg',
	'aria-label' => 'What we treat',
] );

/**
 * Resolve a card image to a URL. MediaUpload stores a full URL; an empty
 * field falls back to the bundled homepage image directory.
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

		<!-- Tabs -->
		<div data-drt-tabs="treat">
			<nav class="drt-tabs__nav drt-tabs__nav--horizontal" role="tablist" aria-label="Treatment categories">
				<?php $first = true; foreach ( $categories as $cat ) :
					$key   = sanitize_html_class( $cat['key'] ?? '' );
					$label = $cat['label'] ?? '';
					?>
					<button
						class="drt-tabs__trigger<?php echo $first ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
						aria-controls="treat-panel-<?php echo esc_attr( $key ); ?>"
						data-drt-tab-trigger="<?php echo esc_attr( $key ); ?>"
					><?php echo esc_html( $label ); ?></button>
				<?php $first = false; endforeach; ?>
			</nav>

			<?php $first = true; foreach ( $categories as $cat ) :
				$key   = sanitize_html_class( $cat['key'] ?? '' );
				$cards = is_array( $cat['cards'] ?? null ) ? $cat['cards'] : [];
				?>
				<div
					class="drt-tabs__panel<?php echo $first ? ' is-active' : ''; ?>"
					id="treat-panel-<?php echo esc_attr( $key ); ?>"
					role="tabpanel"
					data-drt-tab-panel="<?php echo esc_attr( $key ); ?>"
				>
					<!-- Desktop grid -->
					<div class="drt-treat__grid">
						<?php foreach ( $cards as $item ) : ?>
							<a href="<?php echo esc_url( $item['href'] ?? '' ); ?>" class="drt-card--treatment">
								<div class="drt-card--treatment__image">
									<img
										src="<?php echo esc_url( $resolve_img( $item['image'] ?? '' ) ); ?>"
										alt="<?php echo esc_attr( $item['alt'] ?? '' ); ?>"
										loading="lazy"
									>
								</div>
								<div class="drt-card--treatment__body">
									<h3 class="drt-card--treatment__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
									<p class="drt-card--treatment__desc"><?php echo esc_html( $item['desc'] ?? '' ); ?></p>
								</div>
							</a>
						<?php endforeach; ?>
					</div>

					<!-- Mobile Swiper -->
					<div class="drt-treat__swiper swiper" data-drt-swiper="treatment">
						<div class="swiper-wrapper">
							<?php foreach ( $cards as $item ) : ?>
								<div class="swiper-slide">
									<a href="<?php echo esc_url( $item['href'] ?? '' ); ?>" class="drt-card--treatment">
										<div class="drt-card--treatment__image">
											<img
												src="<?php echo esc_url( $resolve_img( $item['image'] ?? '' ) ); ?>"
												alt="<?php echo esc_attr( $item['alt'] ?? '' ); ?>"
												loading="lazy"
											>
										</div>
										<div class="drt-card--treatment__body">
											<h3 class="drt-card--treatment__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
											<p class="drt-card--treatment__desc"><?php echo esc_html( $item['desc'] ?? '' ); ?></p>
										</div>
									</a>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="drt-swiper-dots"></div>
					</div>
				</div>
			<?php $first = false; endforeach; ?>
		</div>
	</div>
</section>
