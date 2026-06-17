<?php
/**
 * REH-28 — re-sync article author + medical reviewer bylines.
 *
 * Run with WP-CLI (NOT the public oneshot):
 *
 *     wp eval-file wp-content/scripts/reh28-author-sync.php          # apply
 *     REH28_DRY=1 wp eval-file wp-content/scripts/reh28-author-sync.php   # preview
 *
 * What it does (idempotent — safe to re-run):
 *   1. Creates/updates 5 `team_member` CPT profiles (4 authors + Dr. Harshi
 *      Dhingra as reviewer), keyed by a `_rehab_old_uid` marker so re-runs
 *      update in place rather than duplicating.
 *   2. For every published article page (page using template-article.php):
 *        - sets `_rehab_author_member` from the old-site slug→author map;
 *        - sets `_rehab_reviewer_member` = Dhingra, unless the page has
 *          `hide_medical_reviewer` set.
 *
 * Data comes from the committed reh28-authors.json (generated from the old
 * dump by reh28-extract-old-authors.py) so prod needs neither the dump nor
 * Python. Photos are NOT migrated — the old DB stored no avatar attachment;
 * the credit cell falls back to initials. Roles can be edited in wp-admin.
 *
 * @package RehabParent
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Refusing to run outside WP-CLI.\n" );
	return;
}

$dry  = (bool) getenv( 'REH28_DRY' );
$data = json_decode( (string) file_get_contents( __DIR__ . '/reh28-authors.json' ), true );
if ( ! is_array( $data ) || empty( $data['members'] ) ) {
	WP_CLI::error( 'Could not read reh28-authors.json next to this script.' );
}

$members      = $data['members'];      // uid => { name, role, bio }
$slug_author  = $data['slug_author'];  // slug => author-uid
$reviewer_uid = (string) $data['reviewer_uid'];

WP_CLI::log( $dry ? '=== DRY RUN (no writes) ===' : '=== APPLYING ===' );

/* ---------------------------------------------------------------------------
 * 1) Ensure the 5 team_member profiles exist; build uid => member-post-id map.
 * ------------------------------------------------------------------------- */
$uid_to_member = [];
foreach ( $members as $uid => $m ) {
	$existing = get_posts(
		[
			'post_type'      => 'team_member',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => '_rehab_old_uid',
			'meta_value'     => (string) $uid,
			'fields'         => 'ids',
		]
	);
	$member_id = $existing ? (int) $existing[0] : 0;

	if ( $dry ) {
		WP_CLI::log( sprintf( '  member uid %s — %s [%s] → %s', $uid, $m['name'], $m['role'], $member_id ? "update #$member_id" : 'CREATE' ) );
		$uid_to_member[ $uid ] = $member_id; // may be 0 in dry run; fine for preview
		continue;
	}

	$postarr = [
		'post_type'    => 'team_member',
		'post_status'  => 'publish',
		'post_title'   => $m['name'],
		'post_content' => $m['bio'],
	];
	if ( $member_id ) {
		$postarr['ID'] = $member_id;
		wp_update_post( $postarr );
	} else {
		$member_id = (int) wp_insert_post( $postarr );
		update_post_meta( $member_id, '_rehab_old_uid', (string) $uid );
	}
	update_post_meta( $member_id, '_rehab_member_role', $m['role'] );
	$uid_to_member[ $uid ] = $member_id;
	WP_CLI::log( sprintf( '  member uid %s → #%d  %s', $uid, $member_id, $m['name'] ) );
}

$reviewer_member = $uid_to_member[ $reviewer_uid ] ?? 0;

/* ---------------------------------------------------------------------------
 * 2) Walk every article page and set author + reviewer meta.
 * ------------------------------------------------------------------------- */
$pages = get_posts(
	[
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'template-article.php',
		'fields'         => 'ids',
	]
);

$stats = [ 'articles' => count( $pages ), 'author_set' => 0, 'reviewer_set' => 0, 'reviewer_hidden' => 0, 'no_author_match' => 0 ];
$unmatched = [];

foreach ( $pages as $pid ) {
	$slug = get_post_field( 'post_name', $pid );

	// Author: look the slug up in the old-site map.
	if ( isset( $slug_author[ $slug ] ) && isset( $uid_to_member[ $slug_author[ $slug ] ] ) ) {
		$member_id = (int) $uid_to_member[ $slug_author[ $slug ] ];
		if ( ! $dry ) {
			update_post_meta( $pid, '_rehab_author_member', $member_id );
		}
		++$stats['author_set'];
	} else {
		++$stats['no_author_match'];
		$unmatched[] = $slug;
	}

	// Reviewer: Dhingra on all, unless explicitly hidden on the page.
	if ( get_post_meta( $pid, 'hide_medical_reviewer', true ) ) {
		++$stats['reviewer_hidden'];
	} elseif ( $reviewer_member ) {
		if ( ! $dry ) {
			update_post_meta( $pid, '_rehab_reviewer_member', $reviewer_member );
		}
		++$stats['reviewer_set'];
	}
}

WP_CLI::log( '--- summary ---' );
foreach ( $stats as $k => $v ) {
	WP_CLI::log( sprintf( '  %-16s %d', $k, $v ) );
}
if ( $unmatched ) {
	WP_CLI::log( sprintf( '  %d article pages had no author match. First 20 slugs:', count( $unmatched ) ) );
	WP_CLI::log( '    ' . implode( ', ', array_slice( $unmatched, 0, 20 ) ) );
}
WP_CLI::success( $dry ? 'Dry run complete — no changes written.' : 'Byline re-sync complete.' );
