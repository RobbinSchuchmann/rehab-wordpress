<?php
/**
 * REH-100 one-time prod migration — run via wp eval-file, then delete.
 *
 * Merges the three consecutive rehab/cards-grid blocks holding the 12 Diamond
 * Approach steps on /programme/ into one grid with cardLayout "process" (the
 * connected snaking-line variant added in the same PR). Mirrors the dev
 * `merge-programme-steps` oneshot in zz-oneshot.php (not deployed to prod).
 *
 * Usage (from the app's public_html):
 *   wp eval-file reh-100-merge-programme-steps.php            # dry run
 *   wp eval-file reh-100-merge-programme-steps.php -- apply   # write
 *   wp breeze purge --cache=all
 */

$pid  = 857;
$dry  = ! in_array( 'apply', $args ?? array(), true );
$post = get_post( $pid );
if ( ! $post ) { WP_CLI::error( "no post {$pid}" ); }

$blocks = parse_blocks( $post->post_content );
$out    = array();
$anchor = null;
$merged = 0;
foreach ( $blocks as $b ) {
	$name = $b['blockName'] ?? null;
	if ( 'rehab/cards-grid' === $name ) {
		$first_title = $b['innerBlocks'][0]['attrs']['title'] ?? '';
		if ( null === $anchor && 0 === strpos( $first_title, 'Self-awareness' ) ) {
			$anchor = count( $out );
			$out[]  = $b;
			continue;
		}
		if ( null !== $anchor && $merged < 2 ) {
			$out[ $anchor ]['innerBlocks'] = array_merge( $out[ $anchor ]['innerBlocks'], $b['innerBlocks'] );
			$merged++;
			continue;
		}
	}
	if ( null === $name && null !== $anchor && $merged < 2 && '' === trim( $b['innerHTML'] ) ) {
		continue;
	}
	$out[] = $b;
}

if ( null === $anchor ) { WP_CLI::error( "anchor grid (first card 'Self-awareness…') not found on {$pid}" ); }
$n = count( $out[ $anchor ]['innerBlocks'] );
$out[ $anchor ]['attrs']['cardLayout'] = 'process';
$out[ $anchor ]['innerHTML']    = '';
$out[ $anchor ]['innerContent'] = array_fill( 0, $n, null );

WP_CLI::log( ( $dry ? 'DRY RUN — ' : 'APPLYING — ' ) . "merged {$merged} extra grid(s); anchor now holds {$n} cards (cardLayout=process)" );
if ( 2 !== $merged || 12 !== $n ) {
	WP_CLI::error( 'expected to absorb 2 grids for 12 cards total — page structure differs, review by hand' );
}
if ( $dry ) {
	WP_CLI::success( 'dry run — nothing written. Re-run with: -- apply' );
} else {
	$res = wp_update_post( array( 'ID' => $pid, 'post_content' => wp_slash( serialize_blocks( $out ) ) ), true );
	if ( is_wp_error( $res ) ) { WP_CLI::error( $res->get_error_message() ); }
	WP_CLI::success( "page {$pid} updated." );
}
