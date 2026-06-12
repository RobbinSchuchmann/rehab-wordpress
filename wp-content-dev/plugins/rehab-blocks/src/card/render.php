<?php
/**
 * Server-side render for `rehab/card`. Mirrors the former src/card save().
 *
 * @var array    $attributes Block attributes (block.json defaults already merged).
 * @var string   $content    Unused (no inner blocks).
 * @var WP_Block $block      Block instance.
 */

$image_url   = $attributes['imageUrl']    ?? '';
$image_alt   = $attributes['imageAlt']    ?? '';
$title       = $attributes['title']       ?? '';
$description = $attributes['description'] ?? '';
$url         = $attributes['url']         ?? '';

$tag       = '' !== $url ? 'a' : 'div';
$href_attr = '' !== $url ? ' href="' . esc_url( $url ) . '"' : '';
$wrapper   = get_block_wrapper_attributes( [ 'class' => 'rehab-card' ] );
?>
<<?php echo $tag; ?> <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput — get_block_wrapper_attributes() returns escaped output. ?><?php echo $href_attr; // phpcs:ignore WordPress.Security.EscapeOutput — esc_url() applied above. ?>>
	<?php if ( '' !== $image_url ) : ?>
		<div class="rehab-card__image">
			<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" />
		</div>
	<?php endif; ?>
	<div class="rehab-card__body">
		<h3 class="rehab-card__title"><?php echo wp_kses_post( $title ); ?></h3>
		<p class="rehab-card__description"><?php echo wp_kses_post( $description ); ?></p>
	</div>
</<?php echo $tag; ?>>
