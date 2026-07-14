<?php
/**
 * REH-102 one-time prod migration — run via wp eval-file, then delete.
 *
 * Completes the truncated aside quote on the /programme/ treatment-phases
 * block ("Rehab Thailand, our comprehensive…" → full quoted sentence) and
 * switches the meta to the treatment-page convention (Statement / The Diamond
 * Rehab Team). Mirrors the dev `fix-programme-phases-quote` oneshot.
 *
 * Usage (from the app's public_html):
 *   wp eval-file reh-102-fix-programme-phases-quote.php            # dry run
 *   wp eval-file reh-102-fix-programme-phases-quote.php -- apply   # write
 *   wp breeze purge --cache=all
 */

$pid  = 857;
$dry  = ! in_array( 'apply', $args ?? array(), true );
$post = get_post( $pid );
if ( ! $post ) { WP_CLI::error( "no post {$pid}" ); }

$old_quote = 'Rehab Thailand, our comprehensive treatment plans for alcohol and drug addiction are created by a team of clinicians based on an evaluation of your condition, concerns, and personal preferences.';
$new_quote = '"At The Diamond Rehab Thailand, our comprehensive treatment plans for alcohol and drug addiction are created by a team of clinicians based on an evaluation of your condition, concerns, and personal preferences."';
$pairs = array(
	$old_quote              => $new_quote,
	'Quoted by'             => 'Statement',
	'Team of Diamond Rehab' => 'The Diamond Rehab Team',
);

$blocks = parse_blocks( $post->post_content );
$hits   = 0;
foreach ( $blocks as &$b ) {
	if ( 'rehab/treatment-phases' !== ( $b['blockName'] ?? '' ) ) continue;
	if ( false === strpos( $b['innerHTML'], $old_quote ) ) continue;

	foreach ( (array) ( $b['attrs']['phases'] ?? array() ) as $i => $phase ) {
		foreach ( array( 'asideQuote', 'asideMetaLabel', 'asideMetaValue' ) as $k ) {
			if ( isset( $phase[ $k ] ) ) {
				$b['attrs']['phases'][ $i ][ $k ] = strtr( $phase[ $k ], $pairs );
			}
		}
	}
	$b['innerHTML'] = strtr( $b['innerHTML'], $pairs );
	foreach ( $b['innerContent'] as &$chunk ) {
		if ( is_string( $chunk ) ) $chunk = strtr( $chunk, $pairs );
	}
	unset( $chunk );
	$hits++;
}
unset( $b );

if ( ! $hits ) { WP_CLI::error( "no treatment-phases block with the truncated quote on {$pid}" ); }
WP_CLI::log( ( $dry ? 'DRY RUN — ' : 'APPLYING — ' ) . "{$hits} block(s): quote completed, meta -> Statement / The Diamond Rehab Team" );
if ( $dry ) {
	WP_CLI::success( 'dry run — nothing written. Re-run with: -- apply' );
} else {
	$res = wp_update_post( array( 'ID' => $pid, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ), true );
	if ( is_wp_error( $res ) ) { WP_CLI::error( $res->get_error_message() ); }
	WP_CLI::success( "page {$pid} updated." );
}
