<?php
/**
 * Server-side render for `rehab/hero`. Mirrors src/hero/save.js.
 *
 * @var array    $attributes Block attributes (block.json defaults already merged).
 * @var string   $content    Unused (no inner blocks).
 * @var WP_Block $block      Block instance.
 */

$eyebrow     = $attributes['eyebrow']      ?? '';
$headline    = $attributes['headline']     ?? '';
$body        = $attributes['body']         ?? '';
$button_text = $attributes['buttonText']   ?? '';
$button_url  = $attributes['buttonUrl']    ?? '';
$button_help = $attributes['buttonHelper'] ?? '';
$trust_items = array_values( array_filter( [
	$attributes['trustItem1'] ?? '',
	$attributes['trustItem2'] ?? '',
	$attributes['trustItem3'] ?? '',
], static fn( $t ) => '' !== $t ) );
$image_url = $attributes['imageUrl'] ?? '';
$image_alt = $attributes['imageAlt'] ?? '';
$video_id  = $attributes['videoId']  ?? '';
$show_deco = ! empty( $attributes['showDeco'] );

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'rehab-hero',
	'aria-label' => 'Hero',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput — get_block_wrapper_attributes() returns escaped output. ?>>
	<div class="rehab-hero__container">
		<div class="rehab-hero__grid">
			<div class="rehab-hero__content">
				<?php if ( '' !== $eyebrow ) : ?>
					<p class="rehab-hero__eyebrow"><?php echo wp_kses_post( $eyebrow ); ?></p>
				<?php endif; ?>
				<h1 class="rehab-hero__h1"><?php echo wp_kses_post( $headline ); ?></h1>
				<?php if ( '' !== $body ) : ?>
					<p class="rehab-hero__body"><?php echo wp_kses_post( $body ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $button_text ) : ?>
					<div class="rehab-hero__cta">
						<a class="rehab-btn rehab-btn--luxury" href="<?php echo esc_url( '' !== $button_url ? $button_url : '#' ); ?>"><?php echo wp_kses_post( $button_text ); ?></a>
						<?php if ( '' !== $button_help ) : ?>
							<p class="rehab-hero__cta-helper"><?php echo wp_kses_post( $button_help ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $trust_items ) ) : ?>
					<div class="rehab-hero__trust">
						<?php foreach ( $trust_items as $item ) : ?>
							<div class="rehab-hero__trust-item"><span class="rehab-hero__diamond" aria-hidden="true">◆</span><?php echo wp_kses_post( $item ); ?></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $image_url ) : ?>
				<div class="rehab-hero__media">
					<div class="rehab-hero__image-wrap"<?php echo '' !== $video_id ? ' data-video-id="' . esc_attr( $video_id ) . '"' : ''; ?>>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" class="rehab-hero__image" loading="eager" decoding="async" />
						<div class="rehab-hero__overlay" aria-hidden="true"></div>
						<?php if ( '' !== $video_id ) : ?>
							<div class="rehab-hero__play" aria-label="Play video tour">
								<svg width="28" height="28" viewBox="0 0 24 24" fill="white" aria-hidden="true"><polygon points="5,3 19,12 5,21" /></svg>
							</div>
						<?php endif; ?>
					</div>
					<?php if ( $show_deco ) : ?>
						<div class="rehab-hero__deco" aria-hidden="true"></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
