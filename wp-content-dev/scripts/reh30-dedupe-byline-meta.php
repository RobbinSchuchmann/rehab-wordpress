<?php
/**
 * REH-30 — collapse duplicate single-valued byline postmeta to one row each.
 *
 * Run with WP-CLI (NOT the public oneshot):
 *
 *     REH30_DRY=1 wp eval-file wp-content/scripts/reh30-dedupe-byline-meta.php  # preview
 *     wp eval-file wp-content/scripts/reh30-dedupe-byline-meta.php             # apply
 *
 * Some article pages carry duplicate rows for these single-valued keys
 * (pre-existing in the seed). This keeps the EFFECTIVE value — the one
 * `get_post_meta(..., true)` returns, i.e. the lowest meta_id — and drops the
 * rest, so nothing the front end shows changes. Uses delete_post_meta +
 * add_post_meta (not raw SQL) so the object cache is invalidated correctly.
 * Idempotent; a second run finds nothing to do.
 *
 * Deliberately excludes `_wp_page_template` (core WP meta controlling the
 * template — out of scope, see REH-30).
 *
 * @package RehabParent
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Refusing to run outside WP-CLI.\n" );
	return;
}

global $wpdb;
$dry  = (bool) getenv( 'REH30_DRY' );
$keys = [ '_rehab_author_member', '_rehab_reviewer_member', 'hide_medical_reviewer' ];

WP_CLI::log( $dry ? '=== DRY RUN (no writes) ===' : '=== APPLYING ===' );

$placeholders = implode( ',', array_fill( 0, count( $keys ), '%s' ) );
// (post_id, meta_key) groups that have more than one row.
$dupes = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT post_id, meta_key, COUNT(*) AS n
		 FROM {$wpdb->postmeta}
		 WHERE meta_key IN ($placeholders)
		 GROUP BY post_id, meta_key
		 HAVING COUNT(*) > 1",
		$keys
	)
);

$groups_total = count( $dupes );
$rows_removed = 0;
$by_key       = array_fill_keys( $keys, 0 );

foreach ( $dupes as $d ) {
	$extra = (int) $d->n - 1;            // rows that will be removed for this group
	$by_key[ $d->meta_key ] += $extra;
	$rows_removed           += $extra;

	if ( $dry ) {
		continue;
	}

	$value = get_post_meta( (int) $d->post_id, $d->meta_key, true ); // effective (lowest meta_id)
	delete_post_meta( (int) $d->post_id, $d->meta_key );             // clears all rows + cache
	add_post_meta( (int) $d->post_id, $d->meta_key, $value, true );  // re-add exactly one
}

WP_CLI::log( '--- summary ---' );
WP_CLI::log( sprintf( '  duplicate groups   %d', $groups_total ) );
foreach ( $by_key as $k => $n ) {
	WP_CLI::log( sprintf( '  %-22s %d extra row(s)', $k, $n ) );
}
WP_CLI::success(
	sprintf(
		'%s — %d duplicate row(s) across %d post/key group(s) %s.',
		$dry ? 'Dry run' : 'Done',
		$rows_removed,
		$groups_total,
		$dry ? 'would be removed' : 'removed'
	)
);
