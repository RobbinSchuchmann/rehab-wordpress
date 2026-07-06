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
			</div>
		</div>
	</div>
</section>
