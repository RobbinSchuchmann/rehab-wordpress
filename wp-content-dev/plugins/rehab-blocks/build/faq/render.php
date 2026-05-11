<?php
/**
 * Server-side render for `rehab/faq`.
 *
 * Dual-mode:
 *   - If `cptIds` attribute is non-empty → query FAQ CPT records by ID and
 *     render the accordion from their title + content. Single source of
 *     truth: editing a FAQ post propagates everywhere it's used.
 *   - Else → render the saved InnerBlocks content as static markup.
 *
 * Available variables (provided by WP block renderer):
 *   $attributes  — block attributes (array)
 *   $content     — InnerBlocks-rendered HTML (string)
 *   $block       — WP_Block instance
 *
 * @package RehabBlocks
 */

$bg          = $attributes['background'] ?? 'cream';
$heading     = $attributes['heading']    ?? 'Frequently Asked Questions';
$cpt_ids     = $attributes['cptIds']     ?? [];
$cpt_ids     = is_array( $cpt_ids ) ? array_filter( array_map( 'intval', $cpt_ids ) ) : [];
?>
<section class="wp-block-rehab-faq rehab-faq rehab-bg-<?php echo esc_attr( $bg ); ?>">
	<div class="rehab-container rehab-container--narrow">
		<h2 class="rehab-faq__heading"><?php echo esc_html( $heading ); ?></h2>
		<div class="rehab-faq__list">
			<?php
			if ( ! empty( $cpt_ids ) ) :
				$faqs = get_posts( [
					'post_type'      => 'faq',
					'post__in'       => $cpt_ids,
					'orderby'        => 'post__in',
					'posts_per_page' => -1,
					'no_found_rows'  => true,
				] );
				foreach ( $faqs as $faq ) :
					$answer = trim( wp_strip_all_tags( $faq->post_content ) );
					?>
					<details class="rehab-faq-item">
						<summary class="rehab-faq-item__summary">
							<span><?php echo esc_html( $faq->post_title ); ?></span>
							<span class="rehab-faq-item__icon" aria-hidden="true"></span>
						</summary>
						<div class="rehab-faq-item__answer">
							<p><?php echo esc_html( $answer ); ?></p>
						</div>
					</details>
					<?php
				endforeach;
			else :
				// Fallback: emit the InnerBlocks-rendered content (existing inline FAQ items).
				echo $content; // Already escaped by individual faq-item blocks.
			endif;
			?>
		</div>
	</div>
</section>
