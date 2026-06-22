<?php
/**
 * Server-side render for `rehab/home-accommodation-amenities`.
 *
 * Section header + vertical tabs (left) with image/content panels (right).
 * Emits the same drt- markup as the legacy
 * template-parts/homepage/section-accommodation-amenities.php partial, so the
 * generic `[data-drt-tabs]` handler in homepage.js binds unchanged.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a    = $attributes;
$tabs = is_array( $a['tabs'] ?? null ) ? $a['tabs'] : [];

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-accommodation drt-bg-white drt-section',
	'aria-label' => 'Accommodation and amenities',
] );
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
		</div>

		<?php if ( $tabs ) : ?>
		<div class="drt-accommodation__layout" data-drt-tabs="accommodation">
			<!-- Left: Vertical Tabs -->
			<div class="drt-accommodation__tabs">
				<nav class="drt-tabs__nav" role="tablist" aria-label="Accommodation areas">
					<?php foreach ( $tabs as $i => $tab ) :
						$tab     = array_merge( [ 'id' => '', 'label' => '', 'image' => '', 'imageAlt' => '', 'content' => '' ], (array) $tab );
						$tab_id  = '' !== $tab['id'] ? sanitize_title( $tab['id'] ) : 'tab-' . ( $i + 1 );
						$is_first = 0 === $i;
						?>
						<button
							class="drt-tabs__trigger<?php echo $is_first ? ' is-active' : ''; ?>"
							role="tab"
							aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>"
							aria-controls="accom-panel-<?php echo esc_attr( $tab_id ); ?>"
							data-drt-tab-trigger="<?php echo esc_attr( $tab_id ); ?>"
						><?php echo wp_kses_post( $tab['label'] ); ?></button>
					<?php endforeach; ?>
				</nav>
			</div>

			<!-- Right: Content -->
			<div class="drt-accommodation__content">
				<?php foreach ( $tabs as $i => $tab ) :
					$tab      = array_merge( [ 'id' => '', 'label' => '', 'image' => '', 'imageAlt' => '', 'content' => '' ], (array) $tab );
					$tab_id   = '' !== $tab['id'] ? sanitize_title( $tab['id'] ) : 'tab-' . ( $i + 1 );
					$is_first = 0 === $i;
					$img_url  = '' !== $tab['image']
						? $tab['image']
						: get_stylesheet_directory_uri() . '/assets/images/homepage/gallery/placeholder.avif';
					$img_alt  = '' !== $tab['imageAlt'] ? $tab['imageAlt'] : wp_strip_all_tags( $tab['label'] );
					?>
					<div
						class="drt-tabs__panel<?php echo $is_first ? ' is-active' : ''; ?>"
						id="accom-panel-<?php echo esc_attr( $tab_id ); ?>"
						role="tabpanel"
						data-drt-tab-panel="<?php echo esc_attr( $tab_id ); ?>"
					>
						<div class="drt-accommodation__image-wrap">
							<img
								src="<?php echo esc_url( $img_url ); ?>"
								alt="<?php echo esc_attr( $img_alt ); ?>"
								class="drt-accommodation__image"
								loading="lazy"
								decoding="async"
							>
						</div>
						<div class="drt-accommodation__text">
							<h3 class="drt-heading drt-heading--md"><?php echo wp_kses_post( $tab['label'] ); ?></h3>
							<p class="drt-body"><?php echo wp_kses_post( $tab['content'] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>
