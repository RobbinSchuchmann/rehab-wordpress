<?php
/**
 * REH-105 — point mega-menu parent headers at their hub pages.
 *
 * 11 primary-menu parent items were disclosure toggles with an empty `_menu_item_url`
 * (they opened a submenu but never navigated). Each now links to its overview/hub
 * page. "About" is deliberately left as a toggle (no dedicated landing page).
 *
 * Pairs with the theme change (functions.php walker + header.css): a parent that has
 * a real URL renders as a link PLUS a separate caret button that opens the submenu,
 * so the dropdown stays reachable on touch. Data-only here — the menu lives in the DB.
 *
 * Matches by exact title within the `primary` menu, and only rewrites items that are
 * still empty/`#` (idempotent — re-running is a no-op once applied). Set DRY=1 to
 * preview.
 *
 *   dev    : docker exec -i rehab-wp php < scripts/reh105-link-menu-headers.php
 *   server : wp eval-file reh105-link-menu-headers.php
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	$rehab_wp_load = getenv( 'WP_LOAD' ) ?: '/var/www/html/wp-load.php';
	require $rehab_wp_load;
}

$rehab_dry = (bool) getenv( 'DRY' );

/** Header label => destination (root-relative, host-agnostic per REH-23). */
$rehab_map = array(
	'Treatment'                     => '/all-treatments/',
	'Information'                   => '/all-articles/',
	'Substance addiction treatment' => '/substance-abuse-treatment/',
	'Prescription drug rehab'       => '/prescribed-medication-rehab/',
	'Eating disorders'              => '/eating-disorders/',
	'Mental Health'                 => '/mental-health-retreat-thailand/',
	'Rehabilitation'                => '/what-is-rehabilitation/',
	'Addiction'                     => '/what-is-addiction/',
	'Behavioral addiction'          => '/what-is-behavioral-addiction/',
	'Impulse control disorder'      => '/what-is-impulse-control-disorder/',
	'Physical addiction'            => '/what-is-physical-addiction/',
);

$locations = get_nav_menu_locations();
$menu_id   = $locations['primary'] ?? 0;
if ( ! $menu_id ) {
	echo "ABORT: no menu assigned to the 'primary' location\n";
	return;
}

$items   = wp_get_nav_menu_items( $menu_id );
$changed = 0;
$skipped = 0;
$missing = array_fill_keys( array_keys( $rehab_map ), true );

foreach ( $items as $it ) {
	$title = trim( $it->title );
	if ( ! isset( $rehab_map[ $title ] ) ) {
		continue;
	}
	unset( $missing[ $title ] );
	$target  = $rehab_map[ $title ];
	$current = trim( (string) $it->url );

	if ( $current === $target ) {
		echo "  = ID {$it->ID}  {$title}  already {$target}\n";
		$skipped++;
		continue;
	}
	// Only convert the intended toggle headers (empty/#), never clobber a real URL.
	if ( $current !== '' && $current !== '#' ) {
		echo "  ! ID {$it->ID}  {$title}  has unexpected url '{$current}' — LEFT ALONE\n";
		$skipped++;
		continue;
	}

	// Sanity-check the destination resolves to a published post before linking.
	$dest_id = url_to_postid( home_url( $target ) );
	$note    = $dest_id ? "-> post {$dest_id}" : '-> WARNING: no post resolves';

	echo ( $rehab_dry ? '  [dry] ' : '  [set] ' ) . "ID {$it->ID}  {$title}  {$target}  {$note}\n";
	if ( ! $rehab_dry ) {
		update_post_meta( $it->ID, '_menu_item_url', $target );
	}
	$changed++;
}

foreach ( array_keys( $missing ) as $title ) {
	echo "  ? '{$title}' not found in the primary menu\n";
}

if ( ! $rehab_dry ) {
	wp_cache_flush();
}

echo ( $rehab_dry ? '[DRY-RUN] ' : '[APPLIED] ' ) . "REH-105 link menu headers: "
	. ( $rehab_dry ? 'would change' : 'changed' ) . " {$changed}, unchanged {$skipped}\n";
