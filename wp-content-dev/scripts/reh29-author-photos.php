<?php
/**
 * REH-29 — attach author/reviewer headshots to the team_member profiles
 * created by REH-28.
 *
 * Run with WP-CLI (NOT the public oneshot):
 *
 *     wp eval-file wp-content/scripts/reh29-author-photos.php          # apply
 *     REH29_DRY=1 wp eval-file wp-content/scripts/reh29-author-photos.php   # preview
 *
 * The headshots are already in the media library on both dev and prod (the old
 * site's uploads + attachment posts were seeded from xscstqwwnp.sql). So this
 * resolves each photo by its `_wp_attached_file` path — NOT a hard-coded
 * attachment ID, which differs between environments — and sets it as the
 * team_member's featured image. The credit cell then renders the photo instead
 * of the initials fallback. Idempotent; safe to re-run.
 *
 * The old backup only kept the full-size originals — the intermediate sizes
 * (the credit cell asks for `thumbnail`) were never in it, so they'd 404. We
 * therefore regenerate the missing sizes from the original. This needs a
 * writable uploads dir; on the dev stack uploads are bind-mounted read-only,
 * so regeneration is skipped there (the thumbnail is still set) and the avatar
 * only renders once this runs on prod, where uploads are writable.
 *
 * @package RehabParent
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Refusing to run outside WP-CLI.\n" );
	return;
}

$dry = (bool) getenv( 'REH29_DRY' );

// old user id (the _rehab_old_uid marker on each team_member) => uploads path.
$photos = [
	'3'  => '2023/02/Dr.-Ahmed-Zayed.png',                // Ahmed Zayed, MD
	'4'  => '2023/01/Theo-photo-hq.png',                  // Theo de Vries
	'5'  => '2023/04/Psychologist-Vladimira-Ivanova.jpg', // Vladimira Ivanova
	'12' => '2023/11/Asif-Baliyan.png',                   // Asif Baliyan, MD
	'10' => '2023/02/Dr.-Harshi-Dhingra.png',             // Dr. Harshi Dhingra
];

WP_CLI::log( $dry ? '=== DRY RUN (no writes) ===' : '=== APPLYING ===' );
$set = 0;
$regen = 0;
$miss = [];

foreach ( $photos as $uid => $file ) {
	$member = get_posts(
		[
			'post_type'      => 'team_member',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => '_rehab_old_uid',
			'meta_value'     => (string) $uid,
			'fields'         => 'ids',
		]
	);
	if ( ! $member ) {
		$miss[] = "uid $uid: no team_member (run reh28-author-sync.php first)";
		continue;
	}
	$member_id = (int) $member[0];

	$att = get_posts(
		[
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_attached_file',
			'meta_value'     => $file,
			'fields'         => 'ids',
		]
	);
	if ( ! $att ) {
		$miss[] = "uid $uid: attachment not found for $file";
		continue;
	}
	$att_id = (int) $att[0];

	// Are the resized variants actually on disk? (Old backup only had originals.)
	$img  = wp_get_attachment_image_src( $att_id, 'thumbnail' );
	$path = $img ? str_replace( wp_get_upload_dir()['baseurl'] . '/', wp_get_upload_dir()['basedir'] . '/', $img[0] ) : '';
	$have_size = $path && file_exists( $path );

	WP_CLI::log( sprintf( '  member #%d ← attachment #%d  (%s)%s', $member_id, $att_id, $file, $have_size ? '' : '  [sizes missing → regenerate]' ) );
	if ( ! $dry ) {
		set_post_thumbnail( $member_id, $att_id );

		if ( ! $have_size ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$src = get_attached_file( $att_id );
			if ( $src && file_exists( $src ) ) {
				$meta = @wp_generate_attachment_metadata( $att_id, $src );
				if ( is_array( $meta ) && ! empty( $meta['sizes'] ) ) {
					wp_update_attachment_metadata( $att_id, $meta );
					++$regen;
				} else {
					$miss[] = "uid $uid: could not regenerate sizes for $file (uploads writable?)";
				}
			}
		}
	}
	++$set;
}

if ( $miss ) {
	WP_CLI::warning( "Unresolved:\n  " . implode( "\n  ", $miss ) );
}
WP_CLI::success( sprintf( '%s — %d/%d photos %s, %d regenerated.', $dry ? 'Dry run' : 'Done', $set, count( $photos ), $dry ? 'would be set' : 'set', $regen ) );
