<?php
/**
 * REH-111 — remove the duplicate breadcrumb baked into the /team/ hero eyebrow.
 *
 * The team page (slug `team`, 722) renders the standard `.rehab-breadcrumb`
 * (Home / Team) from page.php AND its `rehab/feature-split` hero has a SECOND
 * breadcrumb stuffed into the eyebrow attribute — `<a href="/">Home</a> / Team` —
 * rendered as an uppercase sage eyebrow, a different style. Replace that eyebrow
 * with a normal descriptive one ("Meet the team") so the page carries one
 * consistent breadcrumb like every other page.
 *
 * feature-split is a dynamic block (eyebrow lives in the attribute only), so this
 * is a surgical attribute edit. The eyebrow value is HTML escaped in the block
 * comment (" for the inner quotes), so a `[^"]*` regex safely spans it.
 * Writes via $wpdb->update. Idempotent. DRY=1 to preview.
 *
 *   dev    : docker exec -i rehab-wp php < scripts/reh111-team-eyebrow.php
 *   server : wp eval-file reh111-team-eyebrow.php
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	$rehab_wp_load = getenv( 'WP_LOAD' ) ?: '/var/www/html/wp-load.php';
	require $rehab_wp_load;
}
$rehab_dry   = (bool) getenv( 'DRY' );
$new_eyebrow = 'Meet the team';

$page = get_page_by_path( 'team' );
if ( ! $page ) {
	echo "ABORT: no page with slug 'team'\n";
	return;
}

$content = $page->post_content;

if ( false !== strpos( $content, '"eyebrow":"' . $new_eyebrow . '"' ) ) {
	echo "= team ({$page->ID}) eyebrow already \"{$new_eyebrow}\" — no change\n";
	return;
}

// Match the breadcrumb eyebrow (the only one containing Home + Team). Internal
// quotes are ", so [^"]* spans the whole attribute value.
$pattern = '/"eyebrow":"[^"]*Home[^"]*Team[^"]*"/';
$count   = 0;
$new     = preg_replace( $pattern, '"eyebrow":"' . $new_eyebrow . '"', $content, 1, $count );

if ( 0 === $count || null === $new ) {
	echo "ABORT: breadcrumb-eyebrow (Home … Team) not found on team ({$page->ID}) — not writing.\n";
	return;
}

echo ( $rehab_dry ? '[DRY-RUN] ' : '[APPLIED] ' ) . "team ({$page->ID}) hero eyebrow -> \"{$new_eyebrow}\" (duplicate breadcrumb removed)\n";

if ( ! $rehab_dry ) {
	global $wpdb;
	$wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $page->ID ) );
	clean_post_cache( $page->ID );
}
