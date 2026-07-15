<?php
/**
 * REH-107 — repair corrupted block-attribute escapes on /programme/ (857).
 *
 * The block-comment attribute JSON lost the backslash on its `\uXXXX` escapes,
 * so `<` `>` `&` `"` became literal `u003c` … text. The baked
 * HTML is still correct, but `save()` regenerates the corrupted literal from the
 * broken attribute, so the editor flags the block invalid (treatment-hero,
 * intro-doctor-card, treatment-phases, journey-steps). Restoring the backslash
 * makes the attribute decode correctly → save() matches the baked HTML → valid,
 * with ZERO visible change. (Recovery is NOT used — it would bake the corruption.)
 *
 * Scope is strict: only the four known escapes, and only INSIDE `<!-- wp:… -->`
 * opening block comments (the baked HTML has 0 `uXXXX`, verified). Idempotent
 * (the `(?<!\\)` lookbehind skips already-correct `\uXXXX`) and DRY-able.
 *
 *   dev    : docker exec -i rehab-wp php < scripts/reh107-repair-programme-blocks.php
 *   server : wp eval-file reh107-repair-programme-blocks.php
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	$rehab_wp_load = getenv( 'WP_LOAD' ) ?: '/var/www/html/wp-load.php';
	require $rehab_wp_load;
}

$rehab_dry = (bool) getenv( 'DRY' );

$page = get_page_by_path( 'programme' );
if ( ! $page ) {
	echo "ABORT: no page with slug 'programme'\n";
	return;
}

$content  = $page->post_content;
$repaired = 0;

// Repair only within opening block comments; leave baked HTML untouched.
$new = preg_replace_callback(
	'/<!--\s+wp:.*?-->/s',
	function ( $m ) use ( &$repaired ) {
		return preg_replace_callback(
			'/(?<!\\\\)u(003c|003e|0026|0022)/',
			function ( $t ) use ( &$repaired ) {
				$repaired++;
				return '\\u' . $t[1];
			},
			$m[0]
		);
	},
	$content
);

if ( null === $new || $new === $content ) {
	echo "= programme ({$page->ID}): no corrupted escapes found (repaired {$repaired}) — no change\n";
	return;
}

// Guard on shape: content length must only grow by exactly one backslash per repair.
$delta = strlen( $new ) - strlen( $content );
echo ( $rehab_dry ? '[DRY-RUN] ' : '[APPLIED] ' )
	. "programme ({$page->ID}): repaired {$repaired} corrupted escapes (byte delta +{$delta}, expected +{$repaired})\n";

if ( $delta !== $repaired ) {
	echo "  ABORT: byte delta != repair count — unexpected; NOT writing.\n";
	return;
}

if ( ! $rehab_dry ) {
	// Write via $wpdb directly: wp_update_post() runs wp_unslash() on the content,
	// which would strip the very backslashes we're restoring (& -> u0026).
	global $wpdb;
	$wpdb->update( $wpdb->posts, [ 'post_content' => $new ], [ 'ID' => $page->ID ] );
	clean_post_cache( $page->ID );
}
