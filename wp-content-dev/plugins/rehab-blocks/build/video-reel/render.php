<?php
/**
 * Server-side render for `rehab/video-reel`.
 *
 * Vertical (9:16) video-testimonial reel. Cards without a videoUrl render as
 * styled placeholders (toned poster) until real consented clips exist; cards
 * with a videoUrl render as links carrying data-rehab-video for a future
 * lightbox handler.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a     = $attributes;
$items = is_array( $a['items'] ?? null ) ? $a['items'] : [];

/**
 * YouTube id from a watch/shorts/youtu.be URL ('' when not YouTube) — used to
 * auto-derive the card poster and drive the in-card embed (REH-168).
 */
$rehab_reel_yt_id = static function ( string $url ): string {
	if ( preg_match( '#(?:youtube\.com/(?:shorts/|watch\?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,})#', $url, $m ) ) {
		return $m[1];
	}
	return '';
};

$play_svg = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>';
$star_svg = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

$wrapper = get_block_wrapper_attributes( [
	'class'      => 'rehab-video-reel rehab-bg-' . sanitize_html_class( $a['background'] ?: 'cream' ),
	'aria-label' => 'Video testimonials',
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-video-reel__head">
			<div>
				<?php if ( '' !== $a['eyebrow'] ) : ?>
					<span class="rehab-video-reel__eyebrow"><span class="diamond" aria-hidden="true">◆</span><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="rehab-video-reel__heading"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
			</div>
			<?php
			// Live Google-reviews badge when pinned (REH-169); static fallback.
			$rehab_reel_badge = function_exists( 'rehab_elfsight_embed' ) ? rehab_elfsight_embed( 'rehab_elfsight_reviews_badge' ) : '';
			?>
			<?php if ( $a['showRating'] && '' !== $rehab_reel_badge ) : ?>
				<div class="rehab-video-reel__rating rehab-video-reel__rating--live"><?php echo $rehab_reel_badge; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php elseif ( $a['showRating'] && '' !== $a['ratingScore'] ) : ?>
				<div class="rehab-video-reel__rating">
					<span class="rehab-video-reel__stars" aria-hidden="true"><?php echo str_repeat( $star_svg, 5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span><strong><?php echo esc_html( $a['ratingScore'] ); ?></strong> <?php echo wp_kses_post( $a['ratingText'] ); ?></span>
				</div>
			<?php endif; ?>
		</div>
		<div class="rehab-video-reel__grid">
			<?php foreach ( $items as $item ) :
				$item  = array_merge( [ 'name' => '', 'duration' => '', 'tone' => '1', 'quote' => '', 'who' => '', 'videoUrl' => '', 'posterUrl' => '' ], (array) $item );
				$tone  = in_array( (string) $item['tone'], [ '1', '2', '3', '4' ], true ) ? (string) $item['tone'] : '1';
				$tag   = '' !== $item['videoUrl'] ? 'a' : 'div';
				$yt_id = $rehab_reel_yt_id( (string) $item['videoUrl'] );
				// No hand-set poster? Use YouTube's own thumbnail. frame0 is the
				// full-res first frame — vertical for Shorts, so it fits the
				// 9:16 card (oardefault is NOT generated for these videos —
				// REH-168 follow-up); hqdefault always exists as the fallback.
				$poster   = $item['posterUrl'];
				$fallback = '';
				if ( '' === $poster && '' !== $yt_id ) {
					$poster   = 'https://i.ytimg.com/vi/' . rawurlencode( $yt_id ) . '/frame0.jpg';
					$fallback = 'https://i.ytimg.com/vi/' . rawurlencode( $yt_id ) . '/hqdefault.jpg';
				}
				?>
				<div class="rehab-video-card">
					<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="rehab-video-card__thumb rehab-video-card__thumb--tone-<?php echo esc_attr( $tone ); ?>"<?php echo '' !== $item['videoUrl'] ? ' href="' . esc_url( $item['videoUrl'] ) . '" data-rehab-video="' . esc_attr( $item['videoUrl'] ) . '"' . ( '' !== $yt_id ? ' data-rehab-video-id="' . esc_attr( $yt_id ) . '"' : '' ) : ''; ?>>
						<?php if ( '' !== $poster ) : ?>
							<img class="rehab-video-card__poster" src="<?php echo esc_url( $poster ); ?>" alt="" loading="lazy" decoding="async"<?php echo '' !== $fallback ? ' onerror="this.onerror=null;this.src=\'' . esc_url( $fallback ) . '\'"' : ''; ?> />
						<?php endif; ?>
						<?php if ( '' !== $item['duration'] ) : ?>
							<span class="rehab-video-card__duration"><?php echo $play_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $item['duration'] ); ?></span>
						<?php endif; ?>
						<span class="rehab-video-card__play" aria-hidden="true"><?php echo $play_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php if ( '' !== $item['name'] ) : ?>
							<span class="rehab-video-card__name"><?php echo esc_html( $item['name'] ); ?></span>
						<?php endif; ?>
					</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<div class="rehab-video-card__caption">
						<?php if ( '' !== $item['quote'] ) : ?>
							<div class="rehab-video-card__quote"><?php echo esc_html( $item['quote'] ); ?></div>
						<?php endif; ?>
						<?php if ( '' !== $item['who'] ) : ?>
							<div class="rehab-video-card__who"><?php echo esc_html( $item['who'] ); ?></div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
