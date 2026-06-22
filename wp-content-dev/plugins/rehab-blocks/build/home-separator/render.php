<?php
/**
 * Server-side render for `rehab/home-separator`.
 *
 * Decorative homepage divider. Emits the same drt- markup as the legacy
 * section-separator.php template-part.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a = $attributes;

$wrapper = get_block_wrapper_attributes( [
	'class'       => 'drt-separator',
	'aria-hidden' => 'true',
] );
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="drt-separator__line"></div>
</div>
