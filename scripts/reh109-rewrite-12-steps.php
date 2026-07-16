<?php
/**
 * REH-109 — replace the spun 12-step recovery copy on /programme/ (857).
 *
 * The "Our 12 step recovery program" section is a `rehab/cards-grid --card-process`
 * with 12 dynamic `rehab/card` blocks. The title/description live ONLY in the
 * block-comment attribute (the block renders server-side via render.php; innerHTML
 * is empty), so a surgical string replacement of each card's attribute value is
 * safe — no baked HTML to sync, no block-validation risk.
 *
 * The new copy is Robbin-approved, US-spelled, pure-ASCII (no & / em-dash / curly
 * quotes so it needs no JSON escaping), and reordered into a logical recovery arc.
 * Mapped by POSITION: card 1 gets NEW[0], …, card 12 gets NEW[11].
 *
 * Idempotent (skips a card whose title already matches). Writes via $wpdb->update
 * (wp_update_post would wp_unslash the content). DRY=1 to preview.
 *
 *   dev    : docker exec -i rehab-wp php < scripts/reh109-rewrite-12-steps.php
 *   server : wp eval-file reh109-rewrite-12-steps.php
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	$rehab_wp_load = getenv( 'WP_LOAD' ) ?: '/var/www/html/wp-load.php';
	require $rehab_wp_load;
}

$rehab_dry = (bool) getenv( 'DRY' );

$new = array(
	array( 'Acknowledgment and acceptance', 'Recognizing how dependency has affected your life and accepting the need for change.' ),
	array( 'Understanding the root causes', 'Guided self-reflection to uncover the underlying reasons and triggers behind the addiction.' ),
	array( 'Honesty and accountability', 'Learning to reflect openly on your behavior and take responsibility, without shame or avoidance.' ),
	array( 'Rediscovering your strengths and values', 'Identifying the inner resources, values and motivations that anchor your recovery.' ),
	array( 'Setting recovery goals', 'Turning the intention to change into clear, achievable goals that give the journey direction.' ),
	array( 'Mindfulness and emotional regulation', 'Building mindfulness and emotional regulation skills to manage cravings, stress and difficult feelings.' ),
	array( 'Engaging in therapy', 'Taking part in a tailored mix of therapies, from behavioral therapy to mindfulness meditation and group work.' ),
	array( 'Building a support network', 'Developing a dependable network of peers, mentors and community around your recovery.' ),
	array( 'Repairing relationships', 'Rebuilding trust and connection with loved ones through open, compassionate communication.' ),
	array( 'Continued growth and learning', 'Committing to ongoing personal development through workshops, self-help and reflection.' ),
	array( 'Relapse prevention and aftercare', 'Equipping you with practical strategies and an aftercare plan to protect long-term sobriety.' ),
	array( 'A foundation for life', 'Carrying the principles of recovery forward so the progress you make lasts for life.' ),
);

$page = get_page_by_path( 'programme' );
if ( ! $page ) {
	echo "ABORT: no page with slug 'programme'\n";
	return;
}

$content = $page->post_content;
$blocks  = parse_blocks( $content );

// Locate the process cards-grid and its 12 card blocks (in document order).
$cards = array();
$find  = function ( $bs ) use ( &$find, &$cards ) {
	foreach ( $bs as $b ) {
		if ( ( $b['blockName'] ?? '' ) === 'rehab/cards-grid'
			&& false !== strpos( wp_json_encode( $b['attrs'] ?? array() ), 'process' ) ) {
			foreach ( $b['innerBlocks'] as $c ) {
				if ( ( $c['blockName'] ?? '' ) === 'rehab/card' ) {
					$cards[] = $c;
				}
			}
		}
		if ( ! empty( $b['innerBlocks'] ) ) {
			$find( $b['innerBlocks'] );
		}
	}
};
$find( $blocks );

if ( count( $cards ) !== count( $new ) ) {
	echo 'ABORT: found ' . count( $cards ) . " process cards, expected " . count( $new ) . "\n";
	return;
}

$changed = 0;
$updated = $content;
foreach ( $cards as $i => $card ) {
	$old_title = $card['attrs']['title'] ?? '';
	$old_desc  = $card['attrs']['description'] ?? '';
	list( $new_title, $new_desc ) = $new[ $i ];

	foreach ( array( array( $old_title, $new_title, 'title' ), array( $old_desc, $new_desc, 'desc' ) ) as $pair ) {
		list( $old, $newv, $label ) = $pair;
		if ( $old === $newv ) {
			continue; // already applied
		}
		$hits = substr_count( $updated, $old );
		if ( 1 !== $hits ) {
			echo "  ABORT: step " . ( $i + 1 ) . " {$label} old value occurs {$hits}x (expected 1) — not writing\n";
			return;
		}
		$updated = str_replace( $old, $newv, $updated );
		$changed++;
	}
	echo '  step ' . ( $i + 1 ) . ": {$new_title}\n";
}

echo ( $rehab_dry ? '[DRY-RUN] ' : '[APPLIED] ' ) . "REH-109: {$changed} field(s) " . ( $rehab_dry ? 'would change' : 'changed' ) . "\n";

if ( ! $rehab_dry && $updated !== $content ) {
	global $wpdb;
	$wpdb->update( $wpdb->posts, array( 'post_content' => $updated ), array( 'ID' => $page->ID ) );
	clean_post_cache( $page->ID );
}
