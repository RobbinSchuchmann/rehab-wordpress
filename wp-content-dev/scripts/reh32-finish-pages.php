<?php
/**
 * REH-32 — finish two migrated block pages stuck on the default template.
 *
 * Run with WP-CLI (NOT the public oneshot):
 *
 *     REH32_DRY=1 wp eval-file wp-content/scripts/reh32-finish-pages.php  # preview
 *     wp eval-file wp-content/scripts/reh32-finish-pages.php             # apply
 *
 * These two pages were rebuilt with the rehab block library (treatment-hero,
 * article-row, …) but never assigned a template, so they rendered on the bare
 * default template. They aren't treatments, so:
 *   - assign template-treatment.php (for the block layout/chrome);
 *   - set `_rehab_landing_page` so the template shows a plain "Home / Title"
 *     breadcrumb and skips the "Other treatments" cross-links;
 *   - on the superannuation page, drop a leading empty intro-doctor-card block
 *     whose heading just duplicated the hero headline.
 *
 * Resolved by slug, so it's portable across dev/prod. Idempotent.
 *
 * @package RehabParent
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Refusing to run outside WP-CLI.\n" );
	return;
}

$dry   = (bool) getenv( 'REH32_DRY' );
$slugs = [ 'superannuation-mental-health-treatment', 'discover-hua-hin' ];

/** Normalize a heading for equality: drop tags/entities, fold "&" and "and". */
function reh32_norm( string $s ): string {
	$s = preg_replace( '/<[^>]+>/', '', html_entity_decode( $s, ENT_QUOTES ) );
	$s = str_replace( '&', ' and ', strtolower( $s ) );
	return preg_replace( '/[^a-z0-9]+/', '', $s );
}

WP_CLI::log( $dry ? '=== DRY RUN (no writes) ===' : '=== APPLYING ===' );

foreach ( $slugs as $slug ) {
	$pages = get_posts(
		[
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'name'           => $slug,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		]
	);
	if ( ! $pages ) {
		WP_CLI::warning( "not found: $slug" );
		continue;
	}
	$id  = (int) $pages[0];
	$tpl = get_post_meta( $id, '_wp_page_template', true );
	WP_CLI::log( sprintf( '  #%d %s  (template now: %s)', $id, $slug, $tpl ?: 'default' ) );

	// 1) Strip the intro-doctor-card block ONLY when its heading just duplicates
	//    the treatment-hero headline (the superannuation case). Other pages may
	//    use intro-doctor-card for a genuine section heading (e.g. hua-hin's
	//    "Wild Elephant Excursions") — those must be left untouched.
	$content = get_post_field( 'post_content', $id );
	$intro_heading = '';
	$hero_headline = '';
	if ( preg_match( '/wp:rehab\/intro-doctor-card\s*(\{[^}]*\})/', $content, $im ) ) {
		$intro_heading = (string) ( json_decode( $im[1], true )['heading'] ?? '' );
	}
	if ( preg_match( '/wp:rehab\/treatment-hero\s*(\{[^}]*\})/', $content, $hm ) ) {
		$hero_headline = (string) ( json_decode( $hm[1], true )['headline'] ?? '' );
	}
	$redundant = $intro_heading !== ''
		&& reh32_norm( $hero_headline ) !== ''
		&& reh32_norm( $intro_heading ) === reh32_norm( $hero_headline );

	if ( $intro_heading !== '' && ! $redundant ) {
		WP_CLI::log( sprintf( '      - keeping intro-doctor-card (heading "%s" is not a hero duplicate)', $intro_heading ) );
	}
	if ( $redundant ) {
		WP_CLI::log( sprintf( '      - removing duplicate intro-doctor-card (heading "%s" == hero)', $intro_heading ) );
		if ( ! $dry ) {
			$stripped = preg_replace(
				'/<!--\s*wp:rehab\/intro-doctor-card\b.*?<!--\s*\/wp:rehab\/intro-doctor-card\s*-->\s*/s',
				'',
				$content,
				1
			);
			wp_update_post( [ 'ID' => $id, 'post_content' => $stripped ] );
		}
	}

	// 2) Assign the treatment template + landing flag.
	WP_CLI::log( '      - set template-treatment.php + _rehab_landing_page=1' );
	if ( ! $dry ) {
		update_post_meta( $id, '_wp_page_template', 'template-treatment.php' );
		update_post_meta( $id, '_rehab_landing_page', '1' );
	}
}

WP_CLI::success( $dry ? 'Dry run complete — no changes written.' : 'Pages finished.' );
