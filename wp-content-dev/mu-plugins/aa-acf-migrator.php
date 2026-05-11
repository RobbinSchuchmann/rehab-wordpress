<?php
/**
 * ACF flex-content → block migration runner.
 *
 * One write path with a full rollback: copies a page's existing
 * post_content into a sidecar postmeta key, then replaces it with the
 * mapper's output. Reversible via rehab_acf_rollback_page().
 *
 * Postmeta keys used:
 *   _rehab_acf_migration_backup   — the prior post_content verbatim
 *   _rehab_acf_migration_meta     — JSON status (when, by whom, byte sizes)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const REHAB_ACF_BACKUP_KEY = '_rehab_acf_migration_backup';
const REHAB_ACF_META_KEY   = '_rehab_acf_migration_meta';

/**
 * Migrate one page from its ACF flex content to native blocks.
 *
 * Refuses to overwrite an existing backup unless $force is true — that
 * way an accidental second run can't blow away the rollback target.
 * $chrome controls whether the three generic decoration blocks
 * (authority-ribbon, benefits-numbered, journey-steps) are injected.
 *
 * @return array { ok: bool, msg: string, original_bytes?: int, mapped_bytes?: int, layouts?: string[] }
 */
function rehab_acf_migrate_page( int $post_id, bool $force = false, bool $chrome = true ): array {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return [ 'ok' => false, 'msg' => "Post $post_id not found." ];
	}

	$sections = rehab_acf_get_sections( $post_id );
	if ( empty( $sections ) ) {
		return [ 'ok' => false, 'msg' => "Post $post_id has no ACF flex sections to migrate." ];
	}

	$existing_backup = get_post_meta( $post_id, REHAB_ACF_BACKUP_KEY, true );
	if ( $existing_backup && ! $force ) {
		return [
			'ok'  => false,
			'msg' => "Backup already exists for post $post_id (use force=1 to overwrite, or rollback first).",
		];
	}

	$mapped = $chrome
		? rehab_acf_map_sections_with_chrome( $sections )
		: rehab_acf_map_sections( $sections );
	if ( '' === trim( $mapped ) ) {
		return [ 'ok' => false, 'msg' => "Mapper returned empty output — refusing to wipe post_content." ];
	}

	$layouts = array_map( fn( $s ) => $s['_layout'] ?? '?', $sections );

	// Persist the old content so rollback is always possible. wp_slash so
	// quotes survive the round-trip through wpdb.
	update_post_meta( $post_id, REHAB_ACF_BACKUP_KEY, wp_slash( (string) $post->post_content ) );
	update_post_meta( $post_id, REHAB_ACF_META_KEY, wp_slash( wp_json_encode( [
		'migrated_at'    => current_time( 'mysql' ),
		'original_bytes' => strlen( (string) $post->post_content ),
		'mapped_bytes'   => strlen( $mapped ),
		'layouts'        => $layouts,
		'section_count'  => count( $sections ),
	] ) ) );

	$res = wp_update_post( [
		'ID'           => $post_id,
		'post_content' => wp_slash( $mapped ),
	], true );

	if ( is_wp_error( $res ) ) {
		return [ 'ok' => false, 'msg' => 'wp_update_post failed: ' . $res->get_error_message() ];
	}

	return [
		'ok'             => true,
		'msg'            => "Migrated post $post_id (" . count( $sections ) . " sections).",
		'original_bytes' => strlen( (string) $post->post_content ),
		'mapped_bytes'   => strlen( $mapped ),
		'layouts'        => $layouts,
	];
}

/**
 * Restore the pre-migration post_content from the sidecar backup.
 * Leaves the backup postmeta in place — a second rollback is a no-op.
 *
 * An empty-string backup is a valid rollback target (pages whose
 * post_content was empty before migration); we only refuse when no
 * backup key exists at all.
 */
function rehab_acf_rollback_page( int $post_id ): array {
	if ( ! metadata_exists( 'post', $post_id, REHAB_ACF_BACKUP_KEY ) ) {
		return [ 'ok' => false, 'msg' => "No backup found for post $post_id." ];
	}
	$backup = (string) get_post_meta( $post_id, REHAB_ACF_BACKUP_KEY, true );

	$res = wp_update_post( [
		'ID'           => $post_id,
		'post_content' => wp_slash( $backup ),
	], true );

	if ( is_wp_error( $res ) ) {
		return [ 'ok' => false, 'msg' => 'wp_update_post failed: ' . $res->get_error_message() ];
	}

	return [
		'ok'             => true,
		'msg'            => "Rolled back post $post_id from backup (" . strlen( $backup ) . " bytes).",
		'restored_bytes' => strlen( $backup ),
	];
}

/**
 * Whether a post has a stored migration backup.
 */
function rehab_acf_has_backup( int $post_id ): bool {
	return '' !== (string) get_post_meta( $post_id, REHAB_ACF_BACKUP_KEY, true );
}
