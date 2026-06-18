<?php
/**
 * REH-31 — collapse duplicate `_wp_page_template` postmeta to one row per post.
 *
 * Run with WP-CLI (NOT the public oneshot):
 *
 *     REH31_DRY=1 wp eval-file wp-content/scripts/reh31-dedupe-page-template.php  # preview
 *     wp eval-file wp-content/scripts/reh31-dedupe-page-template.php             # apply
 *
 * `_wp_page_template` is core WP meta that selects a page's template, so this
 * preserves the EFFECTIVE value — the one `get_post_meta(..., true)` returns
 * (lowest meta_id), i.e. what the page renders with right now — and drops only
 * the extra rows. Audited beforehand: every duplicate group on this site holds
 * a single distinct value, so no page changes template. The script still keeps
 * the effective value per post, so it stays correct even if that ever changes.
 *
 * Cache-safe (delete_post_meta + add_post_meta, not raw SQL). Idempotent.
 * Site-wide (all post types) — these dupes aren't limited to article pages.
 *
 * @package RehabParent
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Refusing to run outside WP-CLI.\n" );
	return;
}

global $wpdb;
$dry = (bool) getenv( 'REH31_DRY' );
$key = '_wp_page_template';

WP_CLI::log( $dry ? '=== DRY RUN (no writes) ===' : '=== APPLYING ===' );

// Safety audit: any post whose duplicate rows disagree on the value?
$conflicts = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT post_id, GROUP_CONCAT(DISTINCT meta_value) AS vals
		 FROM {$wpdb->postmeta} WHERE meta_key = %s
		 GROUP BY post_id HAVING COUNT(*) > 1 AND COUNT(DISTINCT meta_value) > 1",
		$key
	)
);
if ( $conflicts ) {
	WP_CLI::warning( sprintf( '%d post(s) have conflicting %s values — keeping the live (lowest meta_id) one for each:', count( $conflicts ), $key ) );
	foreach ( $conflicts as $c ) {
		WP_CLI::log( sprintf( '    post %d: %s  → keep %s', $c->post_id, $c->vals, get_post_meta( (int) $c->post_id, $key, true ) ) );
	}
}

// Posts with more than one row for the key.
$dupes = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT post_id, COUNT(*) AS n FROM {$wpdb->postmeta}
		 WHERE meta_key = %s GROUP BY post_id HAVING COUNT(*) > 1",
		$key
	)
);

$rows_removed = 0;
foreach ( $dupes as $d ) {
	$rows_removed += (int) $d->n - 1;
	if ( $dry ) {
		continue;
	}
	$value = get_post_meta( (int) $d->post_id, $key, true ); // effective (lowest meta_id)
	delete_post_meta( (int) $d->post_id, $key );             // clears all rows + cache
	add_post_meta( (int) $d->post_id, $key, $value, true );  // re-add exactly one
}

WP_CLI::log( '--- summary ---' );
WP_CLI::log( sprintf( '  posts with duplicates  %d', count( $dupes ) ) );
WP_CLI::log( sprintf( '  conflicting-value posts %d', count( $conflicts ) ) );
WP_CLI::success(
	sprintf(
		'%s — %d extra %s row(s) across %d post(s) %s.',
		$dry ? 'Dry run' : 'Done',
		$rows_removed,
		$key,
		count( $dupes ),
		$dry ? 'would be removed' : 'removed'
	)
);
