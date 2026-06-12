<?php
/**
 * Server-side render for `rehab/faq-page`.
 *
 * $attributes['categories'] = [ [ 'id' => 'general', 'label' => 'General',
 *   'items' => [ [ 'q' => ..., 'a' => ... ], ... ] ], ... ]
 *
 * Accordions use <details>/<summary> (no JS needed); view.js adds the
 * scroll-spy highlight on the sticky category nav.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a    = $attributes;
$cats = is_array( $a['categories'] ?? null ) ? $a['categories'] : [];

$phone_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';

$wrapper = get_block_wrapper_attributes( [
	'class' => 'rehab-faq-page rehab-bg-' . sanitize_html_class( $a['background'] ?: 'cream' ),
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-faq-page__layout">
			<nav class="rehab-faq-page__nav" aria-label="FAQ categories">
				<p class="rehab-faq-page__nav-label"><?php echo esc_html( $a['navLabel'] ); ?></p>
				<ul>
					<?php foreach ( $cats as $i => $cat ) :
						$cat = array_merge( [ 'id' => 'cat-' . $i, 'label' => '' ], (array) $cat );
						?>
						<li><a<?php echo 0 === $i ? ' class="on"' : ''; ?> href="#<?php echo esc_attr( sanitize_html_class( $cat['id'] ) ); ?>"><?php echo esc_html( $cat['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="rehab-faq-page__main">
				<?php foreach ( $cats as $i => $cat ) :
					$cat   = array_merge( [ 'id' => 'cat-' . $i, 'label' => '', 'items' => [] ], (array) $cat );
					$items = is_array( $cat['items'] ) ? $cat['items'] : [];
					?>
					<div class="rehab-faq-page__cat" id="<?php echo esc_attr( sanitize_html_class( $cat['id'] ) ); ?>">
						<h2><?php echo esc_html( $cat['label'] ); ?></h2>
						<div class="rehab-faq-page__list">
							<?php foreach ( $items as $item ) :
								$item = array_merge( [ 'q' => '', 'a' => '' ], (array) $item );
								?>
								<details class="rehab-faq-page__item">
									<summary><span><?php echo esc_html( $item['q'] ); ?></span><span class="rehab-faq-page__pm" aria-hidden="true"></span></summary>
									<div class="rehab-faq-page__answer"><?php echo wp_kses_post( $item['a'] ); ?></div>
								</details>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>

				<?php if ( '' !== $a['promptTitle'] ) : ?>
					<div class="rehab-faq-page__prompt">
						<div class="rehab-faq-page__prompt-text">
							<h3><?php echo wp_kses_post( $a['promptTitle'] ); ?></h3>
							<p><?php echo wp_kses_post( $a['promptBody'] ); ?></p>
						</div>
						<div class="rehab-faq-page__prompt-actions">
							<a class="rehab-btn rehab-btn--luxury" href="<?php echo esc_url( $a['promptBtnUrl'] ); ?>"><?php echo wp_kses_post( $a['promptBtnText'] ); ?></a>
							<?php if ( '' !== $a['phoneText'] ) : ?>
								<a class="rehab-phone-link" href="<?php echo esc_url( $a['phoneHref'] ); ?>"><?php echo $phone_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?><u><?php echo esc_html( $a['phoneText'] ); ?></u></a>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
