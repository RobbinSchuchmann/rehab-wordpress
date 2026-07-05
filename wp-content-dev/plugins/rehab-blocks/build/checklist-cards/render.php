<?php
/**
 * Server-side render for `rehab/checklist-cards`.
 *
 * Cards = [ [ 'kick' => ..., 'title' => ..., 'items' => [...] ], ... ]
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a     = $attributes;
$cards = is_array( $a['cards'] ?? null ) ? $a['cards'] : [];
$check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';

$wrapper = get_block_wrapper_attributes( [
	'class' => 'rehab-checklist-cards rehab-bg-' . sanitize_html_class( $a['background'] ?: 'cream' ),
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-checklist-cards__head">
			<?php if ( '' !== $a['eyebrow'] ) : ?>
				<span class="rehab-checklist-cards__eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
			<?php endif; ?>
			<h2 class="rehab-checklist-cards__heading"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
			<?php if ( '' !== $a['lede'] ) : ?>
				<p class="rehab-checklist-cards__lede"><?php echo wp_kses_post( $a['lede'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $cards ) : ?>
			<div class="rehab-checklist-cards__grid rehab-checklist-cards__grid--cols-<?php echo esc_attr( min( 4, max( 1, count( $cards ) ) ) ); ?>">
				<?php foreach ( $cards as $card ) :
					$card = array_merge( [ 'kick' => '', 'title' => '', 'items' => [] ], (array) $card );
					?>
					<div class="rehab-checklist-card">
						<div class="rehab-checklist-card__icon" aria-hidden="true">◆</div>
						<?php if ( '' !== $card['kick'] ) : ?>
							<div class="rehab-checklist-card__kick"><?php echo esc_html( $card['kick'] ); ?></div>
						<?php endif; ?>
						<h3 class="rehab-checklist-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<ul class="rehab-checklist-card__list">
							<?php foreach ( (array) $card['items'] as $item ) :
								if ( '' === trim( (string) $item ) ) {
									continue;
								}
								?>
								<li><?php echo $check; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $a['panelTitle'] ) : ?>
			<div class="rehab-checklist-cards__panel">
				<div class="rehab-checklist-cards__panel-copy">
					<?php if ( '' !== $a['panelEyebrow'] ) : ?>
						<span class="rehab-checklist-cards__eyebrow"><?php echo wp_kses_post( $a['panelEyebrow'] ); ?></span>
					<?php endif; ?>
					<h3><?php echo wp_kses_post( $a['panelTitle'] ); ?></h3>
					<?php if ( '' !== $a['panelBody'] ) : ?>
						<p><?php echo wp_kses_post( $a['panelBody'] ); ?></p>
					<?php endif; ?>
				</div>
				<ul class="rehab-checklist-cards__panel-list">
					<?php foreach ( (array) $a['panelItems'] as $item ) :
						if ( '' === trim( (string) $item ) ) {
							continue;
						}
						?>
						<li><?php echo $check; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $item ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</section>
