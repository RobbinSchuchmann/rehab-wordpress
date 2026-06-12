<?php
/**
 * Server-side render for `rehab/team-grid`.
 *
 * $attributes['members'] = [ [ 'cat', 'name', 'role', 'excerpt', 'photoUrl', 'photoAlt', 'url' ], ... ]
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a       = $attributes;
$filters = is_array( $a['filters'] ?? null ) ? $a['filters'] : [];
$members = is_array( $a['members'] ?? null ) ? $a['members'] : [];

$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>';

$wrapper = get_block_wrapper_attributes( [
	'class' => 'rehab-team-grid rehab-bg-' . sanitize_html_class( $a['background'] ?: 'cream' ),
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-team-grid__head">
			<?php if ( '' !== $a['eyebrow'] ) : ?>
				<span class="rehab-team-grid__eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
			<?php endif; ?>
			<h2 class="rehab-team-grid__heading"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
			<?php if ( '' !== $a['lede'] ) : ?>
				<p class="rehab-team-grid__lede"><?php echo wp_kses_post( $a['lede'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( count( $filters ) > 1 ) : ?>
			<div class="rehab-team-grid__filter" role="tablist">
				<?php foreach ( $filters as $i => $f ) :
					$f = array_merge( [ 'cat' => 'all', 'label' => '' ], (array) $f );
					?>
					<button type="button"<?php echo 0 === $i ? ' class="on"' : ''; ?> data-cat="<?php echo esc_attr( $f['cat'] ); ?>"><?php echo esc_html( $f['label'] ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="rehab-team-grid__grid">
			<?php foreach ( $members as $m ) :
				$m = array_merge( [ 'cat' => 'all', 'name' => '', 'role' => '', 'excerpt' => '', 'photoUrl' => '', 'photoAlt' => '', 'url' => '' ], (array) $m );
				$tag  = '' !== $m['url'] ? 'a' : 'div';
				$href = '' !== $m['url'] ? ' href="' . esc_url( $m['url'] ) . '"' : '';
				?>
				<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="rehab-team-card" data-cat="<?php echo esc_attr( $m['cat'] ); ?>"<?php echo $href; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<div class="rehab-team-card__photo">
						<?php if ( '' !== $m['photoUrl'] ) : ?>
							<img src="<?php echo esc_url( $m['photoUrl'] ); ?>" alt="<?php echo esc_attr( $m['photoAlt'] ?: $m['name'] ); ?>" loading="lazy" decoding="async" />
						<?php endif; ?>
					</div>
					<div class="rehab-team-card__head">
						<h3 class="rehab-team-card__name"><?php echo esc_html( $m['name'] ); ?></h3>
						<?php if ( '' !== $m['url'] ) : ?>
							<span class="rehab-team-card__arrow"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>
					</div>
					<p class="rehab-team-card__role"><?php echo esc_html( $m['role'] ); ?></p>
					<?php if ( '' !== $m['excerpt'] ) : ?>
						<p class="rehab-team-card__excerpt"><?php echo esc_html( $m['excerpt'] ); ?></p>
					<?php endif; ?>
				</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php endforeach; ?>
		</div>

		<p class="rehab-team-grid__empty"><?php echo esc_html( $a['emptyText'] ); ?></p>
	</div>
</section>
