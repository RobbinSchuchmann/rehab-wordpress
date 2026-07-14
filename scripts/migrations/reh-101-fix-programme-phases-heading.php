<?php
/**
 * REH-101 one-time prod migration — run via wp eval-file, then delete.
 *
 * Replaces the unattributed decorative quote heading ("An ode to discovery…")
 * on the /programme/ treatment-phases block with the block's usual eyebrow +
 * descriptive heading. Mirrors the dev `fix-programme-phases-heading` oneshot
 * in zz-oneshot.php (not deployed to prod).
 *
 * Usage (from the app's public_html):
 *   wp eval-file reh-101-fix-programme-phases-heading.php            # dry run
 *   wp eval-file reh-101-fix-programme-phases-heading.php -- apply   # write
 *   wp breeze purge --cache=all
 */

$pid  = 857;
$dry  = ! in_array( 'apply', $args ?? array(), true );
$post = get_post( $pid );
if ( ! $post ) { WP_CLI::error( "no post {$pid}" ); }

$new_eyebrow = 'The program in depth';
$new_heading = 'Our 12 step recovery program';
$old_needle  = 'An ode to discovery';

$blocks = parse_blocks( $post->post_content );
$hit    = false;
foreach ( $blocks as &$b ) {
	if ( 'rehab/treatment-phases' !== ( $b['blockName'] ?? '' ) ) continue;
	if ( false === strpos( (string) ( $b['attrs']['heading'] ?? '' ), $old_needle ) ) continue;

	$b['attrs']['eyebrow'] = $new_eyebrow;
	$b['attrs']['heading'] = $new_heading;

	$new_head = '<div class="rehab-treatment-phases__head">'
		. '<span class="rehab-treatment-phases__eyebrow">' . esc_html( $new_eyebrow ) . '</span>'
		. '<h2 class="rehab-treatment-phases__heading">' . esc_html( $new_heading ) . '</h2>'
		. '</div>';
	$html = preg_replace( '#<div class="rehab-treatment-phases__head">.*?</div>#s', $new_head, $b['innerHTML'], 1 );
	if ( null === $html || $html === $b['innerHTML'] ) { WP_CLI::error( 'head div not found in block HTML' ); }
	$b['innerHTML'] = $html;
	foreach ( $b['innerContent'] as &$chunk ) {
		if ( is_string( $chunk ) && false !== strpos( $chunk, $old_needle ) ) {
			$chunk = preg_replace( '#<div class="rehab-treatment-phases__head">.*?</div>#s', $new_head, $chunk, 1 );
		}
	}
	unset( $chunk );
	$hit = true;
	break;
}
unset( $b );

if ( ! $hit ) { WP_CLI::error( "no treatment-phases block with the quote heading on {$pid}" ); }
WP_CLI::log( ( $dry ? 'DRY RUN — ' : 'APPLYING — ' ) . "replacing quote heading with '{$new_eyebrow} / {$new_heading}'" );
if ( $dry ) {
	WP_CLI::success( 'dry run — nothing written. Re-run with: -- apply' );
} else {
	$res = wp_update_post( array( 'ID' => $pid, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ), true );
	if ( is_wp_error( $res ) ) { WP_CLI::error( $res->get_error_message() ); }
	WP_CLI::success( "page {$pid} updated." );
}
