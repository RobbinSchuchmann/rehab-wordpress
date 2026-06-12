<?php
/**
 * Server-side render for `rehab/guarantee`.
 *
 * Narrative copy left, dark offer card right. `body` holds paragraphs
 * separated by blank lines.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a     = $attributes;
$paras = array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", (string) $a['body'] ) ) );
$terms = array_filter( (array) ( $a['terms'] ?? [] ) );
$check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';

$wrapper = get_block_wrapper_attributes( [
	'class' => 'rehab-guarantee rehab-bg-' . sanitize_html_class( $a['background'] ?: 'white' ),
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<div class="rehab-guarantee__grid">
			<div class="rehab-guarantee__copy">
				<?php if ( '' !== $a['eyebrow'] ) : ?>
					<span class="rehab-guarantee__eyebrow"><?php echo wp_kses_post( $a['eyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="rehab-guarantee__heading"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
				<?php foreach ( $paras as $p ) : ?>
					<p><?php echo wp_kses_post( $p ); ?></p>
				<?php endforeach; ?>
				<?php if ( '' !== $a['ghostText'] ) : ?>
					<a class="rehab-btn rehab-btn--outline" href="<?php echo esc_url( $a['ghostUrl'] ?: '#' ); ?>"><?php echo wp_kses_post( $a['ghostText'] ); ?></a>
				<?php endif; ?>
			</div>
			<aside class="rehab-guarantee__card">
				<?php if ( '' !== $a['cardEyebrow'] ) : ?>
					<span class="rehab-guarantee__card-eyebrow"><span aria-hidden="true">◆</span><?php echo wp_kses_post( $a['cardEyebrow'] ); ?></span>
				<?php endif; ?>
				<div class="rehab-guarantee__card-big"><?php echo wp_kses_post( $a['cardBig'] ); ?></div>
				<?php if ( '' !== $a['cardSub'] ) : ?>
					<p class="rehab-guarantee__card-sub"><?php echo wp_kses_post( $a['cardSub'] ); ?></p>
				<?php endif; ?>
				<?php if ( $terms ) : ?>
					<ul class="rehab-guarantee__terms">
						<?php foreach ( $terms as $t ) : ?>
							<li><?php echo $check; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $t ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( '' !== $a['cardBtnText'] ) : ?>
					<a class="rehab-btn rehab-btn--luxury rehab-btn--block" href="<?php echo esc_url( $a['cardBtnUrl'] ?: '#' ); ?>"><?php echo wp_kses_post( $a['cardBtnText'] ); ?></a>
				<?php endif; ?>
			</aside>
		</div>
	</div>
</section>
