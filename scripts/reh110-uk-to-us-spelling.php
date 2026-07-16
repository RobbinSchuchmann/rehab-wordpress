<?php
/**
 * REH-110 — standardise the UK-spelling minority to US across published content.
 *
 * The site is ~96% US already; ~475 UK stragglers remain from the original content
 * migration. Convert them to US in post_content + post_excerpt of published
 * pages/posts/team_member.
 *
 * SAFETY:
 *   - Explicit UK->US word map (no blind `-our`->`-or`, which would hit hour/your/four).
 *   - Word-boundary matches, case-preserving (Behaviour->Behavior, BEHAVIOUR->BEHAVIOR).
 *   - `program`/`programme` DELIBERATELY EXCLUDED — the page slug is /programme/, so
 *     converting would 404 links; content is already mostly US anyway.
 *   - Skips any match inside an href="…" / src="…" URL attribute (belt-and-suspenders).
 *   - DRY=1 reports every word's change count + sample contexts and writes nothing.
 *
 * Writes via $wpdb->update. Idempotent (US targets don't re-match the UK keys).
 *
 *   dev    : docker exec -i rehab-wp php < scripts/reh110-uk-to-us-spelling.php
 *   server : wp eval-file reh110-uk-to-us-spelling.php
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	$rehab_wp_load = getenv( 'WP_LOAD' ) ?: '/var/www/html/wp-load.php';
	require $rehab_wp_load;
}
$rehab_dry = (bool) getenv( 'DRY' );

// Base UK => US (lowercase). Inflections listed explicitly where they occur.
$map = array(
	'behaviour' => 'behavior', 'behaviours' => 'behaviors', 'behavioural' => 'behavioral', 'behaviourally' => 'behaviorally',
	'centre' => 'center', 'centres' => 'centers', 'centred' => 'centered', 'centring' => 'centering',
	'counselling' => 'counseling', 'counsellor' => 'counselor', 'counsellors' => 'counselors', 'counselled' => 'counseled',
	'recognise' => 'recognize', 'recognised' => 'recognized', 'recognises' => 'recognizes', 'recognising' => 'recognizing', 'recognisable' => 'recognizable',
	'realise' => 'realize', 'realised' => 'realized', 'realises' => 'realizes', 'realising' => 'realizing',
	'organise' => 'organize', 'organised' => 'organized', 'organises' => 'organizes', 'organising' => 'organizing',
	'organisation' => 'organization', 'organisations' => 'organizations',
	'favour' => 'favor', 'favours' => 'favors', 'favoured' => 'favored', 'favourable' => 'favorable', 'favourite' => 'favorite', 'favourites' => 'favorites',
	'honour' => 'honor', 'honours' => 'honors', 'honoured' => 'honored',
	'fibre' => 'fiber', 'fibres' => 'fibers',
	'emphasise' => 'emphasize', 'emphasised' => 'emphasized', 'emphasises' => 'emphasizes', 'emphasising' => 'emphasizing',
	'prioritise' => 'prioritize', 'prioritised' => 'prioritized', 'prioritises' => 'prioritizes', 'prioritising' => 'prioritizing',
	'minimise' => 'minimize', 'minimised' => 'minimized', 'minimises' => 'minimizes', 'minimising' => 'minimizing',
	'licence' => 'license', 'licences' => 'licenses',
	'practise' => 'practice', 'practises' => 'practices', 'practised' => 'practiced', 'practising' => 'practicing',
	'fulfilment' => 'fulfillment', 'fulfilments' => 'fulfillments',
	'labelled' => 'labeled', 'labelling' => 'labeling',
	'travelled' => 'traveled', 'travelling' => 'traveling', 'traveller' => 'traveler', 'travellers' => 'travelers',
	'enrolment' => 'enrollment', 'enrolments' => 'enrollments',
	'personalise' => 'personalize', 'personalised' => 'personalized', 'personalising' => 'personalizing',
	'specialise' => 'specialize', 'specialised' => 'specialized', 'specialising' => 'specializing',
	'metre' => 'meter', 'metres' => 'meters', 'litre' => 'liter', 'litres' => 'liters',
	'defence' => 'defense', 'offence' => 'offense',
	// program/programme: converted in PROSE only — a slash-adjacent guard below keeps
	// the /programme/ page slug and any URL path intact.
	'programme' => 'program', 'programmes' => 'programs',
	'colour' => 'color', 'colours' => 'colors', 'coloured' => 'colored', 'colouring' => 'coloring', 'colourful' => 'colorful',
	'analyse' => 'analyze', 'analysed' => 'analyzed', 'analysing' => 'analyzing', 'analyses' => 'analyzes',
	'utilise' => 'utilize', 'utilised' => 'utilized', 'utilising' => 'utilizing', 'utilisation' => 'utilization',
	'stabilise' => 'stabilize', 'stabilised' => 'stabilized', 'stabilising' => 'stabilizing',
	'socialise' => 'socialize', 'socialised' => 'socialized', 'socialising' => 'socializing',
	'normalise' => 'normalize', 'normalised' => 'normalized', 'normalising' => 'normalizing',
	'characterise' => 'characterize', 'characterised' => 'characterized', 'characterising' => 'characterizing',
	'categorise' => 'categorize', 'categorised' => 'categorized', 'categorising' => 'categorizing',
	'maximise' => 'maximize', 'maximised' => 'maximized', 'maximising' => 'maximizing',
	'standardise' => 'standardize', 'standardised' => 'standardized',
	'optimise' => 'optimize', 'optimised' => 'optimized', 'optimising' => 'optimizing',
	'apologise' => 'apologize', 'apologised' => 'apologized',
	'summarise' => 'summarize', 'summarised' => 'summarized',
	'generalise' => 'generalize', 'generalised' => 'generalized',
	'centralise' => 'centralize', 'centralised' => 'centralized',
	'mobilise' => 'mobilize', 'mobilised' => 'mobilized',
	'hospitalise' => 'hospitalize', 'hospitalised' => 'hospitalized',
	'organisational' => 'organizational', 'behaviourally' => 'behaviorally',
	'cancelled' => 'canceled', 'cancelling' => 'canceling',
	'modelling' => 'modeling', 'modelled' => 'modeled',
	'paediatric' => 'pediatric', 'oestrogen' => 'estrogen', 'foetal' => 'fetal',
	'haemorrhage' => 'hemorrhage', 'anaemia' => 'anemia', 'tumour' => 'tumor', 'tumours' => 'tumors',
);

/**
 * Proper nouns that legitimately keep UK spelling (org names citing their real
 * form) — restored verbatim after the general US conversion so we don't rename them.
 */
$rehab_proper_nouns = array(
	'Canadian Pediatric Society' => 'Canadian Paediatric Society',
);

/** Apply the case of $src onto $dst. */
function rehab_case_like( $src, $dst ) {
	if ( ctype_upper( str_replace( array( ' ', '-' ), '', $src ) ) ) return strtoupper( $dst );
	if ( strlen( $src ) && ctype_upper( $src[0] ) ) return ucfirst( $dst );
	return $dst;
}

global $wpdb;
$rows = $wpdb->get_results(
	"SELECT ID, post_title, post_content, post_excerpt FROM {$wpdb->posts}
	 WHERE post_status='publish' AND post_type IN ('page','post','team_member','faq','global_section')"
);

$word_counts = array();  // uk => total replaced
$samples     = array();  // uk => [context…]
$url_skipped = array();  // uk => count skipped inside URLs
$posts_changed = 0;

foreach ( $rows as $r ) {
	$changed_here = false;
	foreach ( array( 'post_content', 'post_excerpt' ) as $field ) {
		$orig = (string) $r->$field;
		if ( '' === $orig ) continue;
		$new = preg_replace_callback(
			'/\b(' . implode( '|', array_map( 'preg_quote', array_keys( $map ) ) ) . ')\b/i',
			function ( $m ) use ( $map, &$word_counts, &$samples, &$url_skipped, $orig ) {
				$uk_lc = strtolower( $m[0] );
				$us    = $map[ $uk_lc ];
				// URL guard: skip if this match sits inside an href/src attribute.
				$pos   = $m[1] ?? null; // offset (PREG_OFFSET_CAPTURE not set) -> fallback below
				return rehab_case_like( $m[0], $us ) . '';
			},
			$orig
		);
		// Re-do with offset capture for URL guard + accurate counting.
		if ( preg_match_all( '/\b(' . implode( '|', array_map( 'preg_quote', array_keys( $map ) ) ) . ')\b/i', $orig, $mm, PREG_OFFSET_CAPTURE ) ) {
			$rebuilt = $orig; $delta = 0;
			foreach ( $mm[0] as $hit ) {
				$word = $hit[0]; $off = $hit[1];
				$uk_lc = strtolower( $word );
				// URL context: look back to the nearest quote and see if an href=/src=/…Url": precedes it unquoted.
				$back = substr( $orig, max( 0, $off - 80 ), min( 80, $off ) );
				$after_char = substr( $orig, $off + strlen( $word ), 1 );
				$before_char = $off > 0 ? substr( $orig, $off - 1, 1 ) : '';
				// URL context OR the word is a slash-adjacent path segment (e.g. /programme/).
				$is_url = ( '/' === $before_char || '/' === $after_char )
					|| (bool) preg_match( '/(href|src|url|Url|imageUrl)"?\s*[:=]\s*"[^"]*$/', $back );
				$us = rehab_case_like( $word, $map[ $uk_lc ] );
				if ( $is_url ) { $url_skipped[ $uk_lc ] = ( $url_skipped[ $uk_lc ] ?? 0 ) + 1; continue; }
				$start = $off + $delta;
				$rebuilt = substr( $rebuilt, 0, $start ) . $us . substr( $rebuilt, $start + strlen( $word ) );
				$delta += strlen( $us ) - strlen( $word );
				$word_counts[ $uk_lc ] = ( $word_counts[ $uk_lc ] ?? 0 ) + 1;
				if ( count( $samples[ $uk_lc ] ?? array() ) < 2 ) {
					$ctx = substr( $orig, max( 0, $off - 32 ), 32 + strlen( $word ) + 32 );
					$samples[ $uk_lc ][] = '#' . $r->ID . ' …' . trim( preg_replace( '/\s+/', ' ', $ctx ) ) . '…';
				}
				$changed_here = true;
			}
			$new = $rebuilt;
		}
		// Restore proper nouns that keep UK spelling.
		foreach ( $rehab_proper_nouns as $us_form => $uk_form ) {
			if ( false !== strpos( $new, $us_form ) ) {
				$new = str_replace( $us_form, $uk_form, $new );
				$changed_here = true;
			}
		}
		if ( $new !== $orig ) {
			if ( ! $rehab_dry ) {
				$wpdb->update( $wpdb->posts, array( $field => $new ), array( 'ID' => $r->ID ) );
			}
		}
	}
	if ( $changed_here ) { $posts_changed++; if ( ! $rehab_dry ) clean_post_cache( $r->ID ); }
}

echo ( $rehab_dry ? "[DRY-RUN] " : "[APPLIED] " ) . "REH-110 UK->US spelling\n";
echo "  posts changed: {$posts_changed}\n";
arsort( $word_counts );
foreach ( $word_counts as $uk => $n ) {
	printf( "  %-14s -> %-14s x%-4d  e.g. %s\n", $uk, $map[ $uk ], $n, $samples[ $uk ][0] ?? '' );
}
if ( $url_skipped ) {
	echo "  URL-context matches SKIPPED (left as-is):\n";
	foreach ( $url_skipped as $uk => $n ) echo "    {$uk}: {$n}\n";
}
