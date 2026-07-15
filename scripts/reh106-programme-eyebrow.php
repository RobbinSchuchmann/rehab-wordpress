<?php
/**
 * REH-106 — fill the blank eyebrow on the /programme/ hero.
 *
 * The programme page (slug `programme`) hero is a STATIC `wp:rehab/treatment-hero`
 * block: its markup is baked into post_content, so the eyebrow lives BOTH in the
 * block-comment attribute (editor hydration + validation) AND in the saved
 * `<p class="rehab-treatment-hero__eyebrow">` markup (what the frontend renders).
 * Both were empty, so no eyebrow kicker showed above "Bespoke treatment programs".
 * Set both to "A program built around you" — they MUST match or the editor shows
 * an "Attempt recovery" block-validation warning.
 *
 * Surgical string replacements (no JSON re-encode) so the editor's escaping of the
 * other attributes is preserved byte-for-byte. Idempotent and DRY-able.
 *
 *   dev    : docker exec -i rehab-wp php < scripts/reh106-programme-eyebrow.php
 *   server : wp eval-file reh106-programme-eyebrow.php
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	$rehab_wp_load = getenv( 'WP_LOAD' ) ?: '/var/www/html/wp-load.php';
	require $rehab_wp_load;
}

$rehab_dry   = (bool) getenv( 'DRY' );
$rehab_value = 'A program built around you';

$page = get_page_by_path( 'programme' );
if ( ! $page ) {
	echo "ABORT: no page with slug 'programme'\n";
	return;
}

$content  = $page->post_content;
$original = $content;

// 1. Block-comment attribute: {"eyebrow":"" -> {"eyebrow":"<value>"  (first key, empty only).
$content = preg_replace(
	'/(<!--\s*wp:rehab\/treatment-hero\s*\{)"eyebrow":""/',
	'$1"eyebrow":"' . $rehab_value . '"',
	$content,
	1,
	$attr_hits
);

// 2. Baked markup: the empty eyebrow paragraph -> the same text.
$content = str_replace(
	'<p class="rehab-treatment-hero__eyebrow"></p>',
	'<p class="rehab-treatment-hero__eyebrow">' . esc_html( $rehab_value ) . '</p>',
	$content,
	$markup_hits
);

if ( $content === $original ) {
	echo "= programme ({$page->ID}) hero eyebrow already set (attr+markup) — no change\n";
	return;
}

echo ( $rehab_dry ? '[DRY-RUN] ' : '[APPLIED] ' )
	. "programme ({$page->ID}) hero eyebrow -> \"{$rehab_value}\"  (attr:{$attr_hits} markup:{$markup_hits})\n";

if ( ! $markup_hits ) {
	echo "  NOTE: baked eyebrow <p> not empty — frontend may not change; inspect manually.\n";
}

if ( ! $rehab_dry ) {
	wp_update_post( [ 'ID' => $page->ID, 'post_content' => $content ] );
	clean_post_cache( $page->ID );
}
