<?php
/**
 * REH-23 — normalise baked absolute self-URLs in content to root-relative.
 *
 * A migration builder + the various host replacements baked absolute URLs into
 * page content: the dev IP, localhost, the Cloudways staging host, and (after
 * the Phase-8 learning-article re-sync) the live `diamondrehabthailand.com`
 * domain. `home`/`siteurl` are per-request, so these in-content absolutes break
 * whenever the site is viewed from a different host — and at cutover they'd
 * point at the wrong server. Converting them to **root-relative** (`/path`)
 * makes them host-agnostic: correct on dev, on staging, and on the production
 * domain, with no re-run.
 *
 * SCOPE (deliberately narrow):
 *   - wp_posts.post_content and .post_excerpt  (never serialized → plain str_replace)
 *   - wp_postmeta where meta_key = '_menu_item_url'  (nav menu links, plain strings)
 * We do NOT touch RankMath schema / OG / canonical meta, `home`/`siteurl`, or any
 * other option/meta — those must stay ABSOLUTE and get the real domain at cutover.
 * Both the unescaped (`https://host/`) and block-JSON escaped (`https:\/\/host\/`)
 * forms are handled.
 *
 * Runs in two ways:
 *   dev    : docker exec -i rehab-wp php < this file        (WP bootstrapped via fallback)
 *   server : wp eval-file reh23-make-links-relative.php     (WP already loaded)
 * Set DRY=1 in the environment for a preview (counts only, no writes).
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Standalone (dev) invocation — bootstrap WordPress.
	$rehab_wp_load = getenv( 'WP_LOAD' ) ?: '/var/www/html/wp-load.php';
	require $rehab_wp_load;
}

global $wpdb;

$rehab_dry = (bool) getenv( 'DRY' );

/**
 * Absolute self-hosts to relativise. Order is not significant (none is a prefix
 * of another); each is stripped in both a with-path and a bare form.
 */
$rehab_hosts = array(
	'https://wordpress-1636937-6489349.cloudwaysapps.com',
	'http://wordpress-1636937-6489349.cloudwaysapps.com',
	'https://diamondrehabthailand.com',
	'http://diamondrehabthailand.com',
	'https://www.diamondrehabthailand.com',
	'http://www.diamondrehabthailand.com',
	'http://5.223.87.211:8081',
	'https://5.223.87.211:8081',
	'http://localhost:8081',
	'https://localhost:8081',
);

/**
 * Convert every absolute self-URL in $s to root-relative, covering both the
 * plain (`https://host/x`) and block-JSON escaped (`https:\/\/host\/x`) forms.
 */
function rehab_relativise( $s, array $hosts ) {
	foreach ( $hosts as $h ) {
		// Plain form: strip "host/" (keeps the leading slash) then any bare "host".
		$s = str_replace( $h . '/', '/', $s );
		$s = str_replace( $h, '/', $s );
		// Escaped form found inside block-attribute JSON: https:\/\/host\/ .
		$he = str_replace( '/', '\\/', $h );
		$s  = str_replace( $he . '\\/', '\\/', $s );
		$s  = str_replace( $he, '\\/', $s );
	}
	return $s;
}

// Build a LIKE predicate that matches either the plain or the escaped host form.
$rehab_like = array();
foreach ( $rehab_hosts as $h ) {
	$rehab_like[] = $wpdb->prepare( 'post_content LIKE %s', '%' . $wpdb->esc_like( $h ) . '%' );
	$rehab_like[] = $wpdb->prepare( 'post_excerpt LIKE %s', '%' . $wpdb->esc_like( $h ) . '%' );
	$he           = str_replace( '/', '\\/', $h );
	$rehab_like[] = $wpdb->prepare( 'post_content LIKE %s', '%' . $wpdb->esc_like( $he ) . '%' );
	$rehab_like[] = $wpdb->prepare( 'post_excerpt LIKE %s', '%' . $wpdb->esc_like( $he ) . '%' );
}
$where = implode( ' OR ', $rehab_like );

// --- 1. posts: post_content + post_excerpt ---
$rows        = $wpdb->get_results( "SELECT ID, post_content, post_excerpt FROM {$wpdb->posts} WHERE {$where}" );
$post_hits   = count( $rows );
$post_writes = 0;
foreach ( $rows as $r ) {
	$new_content = rehab_relativise( $r->post_content, $rehab_hosts );
	$new_excerpt = rehab_relativise( $r->post_excerpt, $rehab_hosts );
	if ( $new_content !== $r->post_content || $new_excerpt !== $r->post_excerpt ) {
		if ( ! $rehab_dry ) {
			$wpdb->update(
				$wpdb->posts,
				array( 'post_content' => $new_content, 'post_excerpt' => $new_excerpt ),
				array( 'ID' => $r->ID )
			);
		}
		$post_writes++;
	}
}

// --- 2. nav menu item URLs (_menu_item_url) ---
$menu_like = array();
foreach ( $rehab_hosts as $h ) {
	$menu_like[] = $wpdb->prepare( 'meta_value LIKE %s', '%' . $wpdb->esc_like( $h ) . '%' );
}
$menu_where = implode( ' OR ', $menu_like );
$menu_rows  = $wpdb->get_results(
	"SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_menu_item_url' AND ( {$menu_where} )"
);
$menu_hits   = count( $menu_rows );
$menu_writes = 0;
foreach ( $menu_rows as $r ) {
	$new = rehab_relativise( $r->meta_value, $rehab_hosts );
	if ( $new !== $r->meta_value ) {
		if ( ! $rehab_dry ) {
			$wpdb->update( $wpdb->postmeta, array( 'meta_value' => $new ), array( 'meta_id' => $r->meta_id ) );
		}
		$menu_writes++;
	}
}

if ( ! $rehab_dry ) {
	wp_cache_flush();
}

echo ( $rehab_dry ? '[DRY-RUN] ' : '[APPLIED] ' ) . "REH-23 relativise\n";
echo "  posts matched: {$post_hits}, " . ( $rehab_dry ? 'would change' : 'changed' ) . ": {$post_writes}\n";
echo "  menu urls matched: {$menu_hits}, " . ( $rehab_dry ? 'would change' : 'changed' ) . ": {$menu_writes}\n";
