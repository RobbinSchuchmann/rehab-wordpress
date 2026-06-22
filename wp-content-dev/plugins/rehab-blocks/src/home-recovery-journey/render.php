<?php
/**
 * Server-side render for `rehab/home-recovery-journey`.
 *
 * Recovery journey: eyebrow + heading + intro, then a horizontal set of
 * stage tabs, each panel holding accordion items. Emits the same drt- markup
 * (and JS data-* hooks) as the legacy section-recovery-journey.php template-part.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a     = $attributes;
$steps = is_array( $a['steps'] ?? null ) ? $a['steps'] : [];

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'drt-recovery drt-bg-white',
	'aria-label' => 'Recovery journey programs',
] );
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

		<div class="drt-recovery__wrap" data-drt-tabs="recovery">
			<!-- Tab Navigation -->
			<nav class="drt-tabs__nav drt-tabs__nav--horizontal drt-recovery__nav" role="tablist" aria-label="Recovery stages">
				<?php $first = true; foreach ( $steps as $stage ) :
					$stage = array_merge( [ 'key' => '', 'short' => '', 'label' => '', 'items' => [] ], (array) $stage );
					?>
					<button
						class="drt-tabs__trigger<?php echo $first ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
						aria-controls="recovery-panel-<?php echo esc_attr( $stage['key'] ); ?>"
						data-drt-tab-trigger="<?php echo esc_attr( $stage['key'] ); ?>"
					>
						<span class="drt-recovery__label-short"><?php echo wp_kses_post( $stage['short'] ); ?></span>
						<span class="drt-recovery__label-full"><?php echo wp_kses_post( $stage['label'] ); ?></span>
					</button>
				<?php $first = false; endforeach; ?>
			</nav>

			<!-- Panels -->
			<div class="drt-recovery__panels">
				<?php $first = true; foreach ( $steps as $stage ) :
					$stage = array_merge( [ 'key' => '', 'short' => '', 'label' => '', 'items' => [] ], (array) $stage );
					$items = is_array( $stage['items'] ) ? $stage['items'] : [];
					?>
					<div
						class="drt-tabs__panel<?php echo $first ? ' is-active' : ''; ?>"
						id="recovery-panel-<?php echo esc_attr( $stage['key'] ); ?>"
						role="tabpanel"
						data-drt-tab-panel="<?php echo esc_attr( $stage['key'] ); ?>"
					>
						<?php foreach ( $items as $item ) :
							$item = array_merge( [ 'id' => '', 'title' => '', 'content' => '' ], (array) $item );
							?>
							<div class="drt-accordion__item" data-drt-accordion>
								<button
									class="drt-accordion__trigger"
									data-drt-accordion-trigger
									aria-expanded="false"
									aria-controls="recovery-acc-<?php echo esc_attr( $item['id'] ); ?>"
								>
									<span class="drt-accordion__trigger-text"><?php echo wp_kses_post( $item['title'] ); ?></span>
									<span class="drt-accordion__trigger-icon" aria-hidden="true">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
									</span>
								</button>
								<div
									class="drt-accordion__content"
									id="recovery-acc-<?php echo esc_attr( $item['id'] ); ?>"
									data-drt-accordion-content
								>
									<p><?php echo wp_kses_post( $item['content'] ); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php $first = false; endforeach; ?>
			</div>
		</div>
	</div>
</section>
