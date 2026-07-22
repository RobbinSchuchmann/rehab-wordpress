<?php
/**
 * REH-177 — apply the REH-173 job-ad simplification to EVERY job ad page.
 *
 * REH-173 only rebuilt the Clinical Psychologist example; Addiction Counsellor
 * and Admissions Manager still carried the treatment-hero + treatment-phases
 * template (their single phase rendering as one lone tab in a 3-column grid).
 * This walks all children of /careers/ and rebuilds any that still have a
 * phases block into: H1 + the ad prose (lifted verbatim from the page's own
 * phases attrs) + the rehab/job-apply box. Pages already simplified are
 * skipped, so it is idempotent and safe to re-run for future job ads.
 *
 *   dev  : docker exec -i rehab-wp php < scripts/reh177-job-ads-all.php
 *   prod : cat … | ssh … "cat > /tmp/x.php && wp eval-file /tmp/x.php"
 *   DRY=1 previews.
 */

if ( ! defined( 'ABSPATH' ) ) {
	require getenv( 'WP_LOAD' ) ?: '/var/www/html/wp-load.php';
}
$rehab_dry = (bool) getenv( 'DRY' );

$attrs_fn = static function ( array $a ): string {
	return wp_json_encode( $a, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP );
};

$prose_h1_fn = static function ( string $h1, array $paragraphs ) use ( $attrs_fn ): string {
	$gut = "<!-- wp:heading {\"level\":1} -->\n<h1 class=\"wp-block-heading\">" . esc_html( $h1 ) . "</h1>\n<!-- /wp:heading -->\n\n";
	foreach ( $paragraphs as $p ) {
		$gut .= "<!-- wp:paragraph -->\n<p>" . esc_html( $p ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
	return "<!-- wp:rehab/prose " . $attrs_fn( [ 'background' => 'white', 'width' => 'text' ] ) . " -->\n" .
		'<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner">' . $gut . '</div></div></section>' .
		"\n<!-- /wp:rehab/prose -->\n\n";
};

$careers = get_page_by_path( 'careers', OBJECT, 'page' );
if ( ! $careers ) {
	echo "no /careers/ page\n";
	return;
}

$kids = get_posts( [
	'post_type'   => 'page',
	'post_parent' => $careers->ID,
	'post_status' => [ 'publish', 'draft' ],
	'numberposts' => -1,
] );

global $wpdb;
$done = 0;
foreach ( $kids as $kid ) {
	$c = $kid->post_content;
	if ( false === strpos( $c, 'wp:rehab/treatment-phases' ) ) {
		echo "skip {$kid->post_name} ({$kid->ID}): already simplified\n";
		continue;
	}
	if ( ! preg_match( '#<!-- wp:rehab/treatment-phases (\{.*?\}) -->#s', $c, $m ) ) {
		echo "skip {$kid->post_name}: phases attrs unreadable\n";
		continue;
	}
	$pa    = json_decode( $m[1], true ) ?: [];
	$paras = [];
	foreach ( (array) ( $pa['phases'] ?? [] ) as $phase ) {
		foreach ( (array) ( $phase['paragraphs'] ?? [] ) as $p ) {
			$p = trim( html_entity_decode( (string) $p, ENT_QUOTES ) );
			if ( '' !== $p ) {
				$paras[] = $p;
			}
		}
	}
	if ( count( $paras ) < 2 ) {
		echo "skip {$kid->post_name}: only " . count( $paras ) . " paragraphs found\n";
		continue;
	}
	$new = $prose_h1_fn( wp_strip_all_tags( $kid->post_title ), $paras );
	if ( false === strpos( $c, 'wp:rehab/job-apply' ) ) {
		$new .= "<!-- wp:rehab/job-apply /-->\n";
	}
	if ( $rehab_dry ) {
		echo "[DRY] {$kid->post_name} ({$kid->ID}): would rebuild as H1 + " . count( $paras ) . " paragraphs + apply box\n";
	} else {
		$wpdb->update( $wpdb->posts, [ 'post_content' => $new ], [ 'ID' => $kid->ID ] );
		clean_post_cache( $kid->ID );
		echo "{$kid->post_name} ({$kid->ID}): rebuilt as H1 + " . count( $paras ) . " paragraphs + apply box\n";
	}
	$done++;
}
echo ( $rehab_dry ? '[DRY] ' : '' ) . "{$done} job ad page(s) processed.\n";
