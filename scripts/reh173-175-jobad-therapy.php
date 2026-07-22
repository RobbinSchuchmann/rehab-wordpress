<?php
/**
 * REH-173 + REH-175 — job-ad simplification + therapy-page section repairs.
 *
 * REH-175 (therapy pages): the rebuild wrote section titles into the tiny
 * `eyebrow` attr of rehab/intro-doctor-card and left `heading` empty, so
 * titles rendered as kickers with their body far below. Also on 1327 the
 * "How we treat…" heading carried the wrong body (the CBT-vs-DBT copy), the
 * real "How we treat…" body and the "The difference between…" heading were
 * dropped, and "Holistic inpatient DBT…" had no body at all. Fixes, with the
 * missing copy restored from the live site:
 *   - 1327: "How we treat…" gets its real body; a new "The difference
 *     between cognitive behavior therapy and dialectical behavior therapy"
 *     block carries the CBT-vs-DBT copy; "Holistic…" + "Learn more…" move
 *     eyebrow -> heading (Holistic also gains its live body).
 *   - 857: "The Diamond Program" moves eyebrow -> heading.
 *
 * REH-173 (job ads): page 9182 becomes H1 + the ad prose (pulled from its
 * own treatment-phases attrs, so the approved copy survives verbatim) + the
 * rehab/job-apply box. Hero and phases are dropped.
 *
 * Self-contained (embeds the builder shapes) so the same file runs on dev
 * and prod despite their byte-level content divergence. DRY=1 previews.
 *
 *   dev  : docker exec -i rehab-wp php < scripts/reh173-175-jobad-therapy.php
 *   prod : cat … | ssh … "cat > /tmp/x.php && wp eval-file /tmp/x.php"
 */

if ( ! defined( 'ABSPATH' ) ) {
	require getenv( 'WP_LOAD' ) ?: '/var/www/html/wp-load.php';
}
$rehab_dry = (bool) getenv( 'DRY' );

$attrs_fn = static function ( array $a ): string {
	return wp_json_encode( $a, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP );
};

// Mirror of rehab_block_intro_doctor_card() (aa-block-builders.php) for the
// card-less variant these pages use.
$idc_fn = static function ( string $heading, string $body_plain, string $bg = 'white' ) use ( $attrs_fn ): string {
	$paragraphs = array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", trim( $body_plain ) ) ) );
	$body_html  = implode( '', array_map( static fn( $p ) => '<p>' . esc_html( $p ) . '</p>', $paragraphs ) );
	$a = [
		'background' => $bg, 'eyebrow' => '', 'heading' => $heading, 'body' => $body_html,
		'doctorImageUrl' => '', 'doctorImageAlt' => '',
		'doctorLabel' => 'Speak with our Director', 'doctorName' => '',
		'doctorPhone' => '', 'doctorPhoneHref' => '',
	];
	// Shape mirrors the block's save(): NO wp-block- prefix on the section
	// class (the className attr is metadata only) and the eyebrow span is
	// ALWAYS present, empty included — byte-parity keeps the editor green.
	$h  = '<section class="rehab-intro-doctor-card rehab-bg-' . esc_attr( $bg ) . '"><div class="rehab-container">';
	$h .= '<div class="rehab-intro-doctor-card__grid">';
	$h .= '<div>';
	$h .= '<span class="rehab-intro-doctor-card__eyebrow"></span>';
	$h .= '<h2 class="rehab-intro-doctor-card__heading">' . esc_html( $heading ) . '</h2>';
	$h .= '</div>';
	$h .= '<div class="rehab-intro-doctor-card__copy">' . $body_html . '</div>';
	$h .= '</div></div></section>';
	return "<!-- wp:rehab/intro-doctor-card " . $attrs_fn( [ 'heading' => $heading, 'body' => $body_html, 'className' => 'wp-block-rehab-intro-doctor-card' ] ) . " -->\n" . $h . "\n<!-- /wp:rehab/intro-doctor-card -->\n\n";
};

// rehab/prose with an H1 heading (job-ad title) + plain paragraphs.
$prose_h1_fn = static function ( string $h1, array $paragraphs ) use ( $attrs_fn ): string {
	$gut = "<!-- wp:heading {\"level\":1} -->\n<h1 class=\"wp-block-heading\">" . esc_html( $h1 ) . "</h1>\n<!-- /wp:heading -->\n\n";
	foreach ( $paragraphs as $p ) {
		$gut .= "<!-- wp:paragraph -->\n<p>" . esc_html( $p ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
	return "<!-- wp:rehab/prose " . $attrs_fn( [ 'background' => 'white', 'width' => 'text' ] ) . " -->\n" .
		'<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner">' . $gut . '</div></div></section>' .
		"\n<!-- /wp:rehab/prose -->\n\n";
};

$plain_from_html = static function ( string $html ): string {
	$parts = preg_split( '/<\/p>/', $html );
	$out   = array_filter( array_map( static fn( $p ) => trim( html_entity_decode( wp_strip_all_tags( $p ), ENT_QUOTES ) ), $parts ) );
	return implode( "\n\n", $out );
};

$HOW_BODY = "When used in conjunction with other treatment methods, DBT programs can be very effective for treating drug addiction and substance abuse disorders. In contrast to detoxification and medication, which are used to address short-term physical symptoms, DBT inpatient treatment aims to treat the deeper psychological and emotional regulation issues that often lie at the heart of drug addiction.\n\nAt our DBT residential treatment center, we'll work with you to help you understand your personal strengths and identify certain areas that may need work. Drawing on extensive knowledge in the field of substance addiction, our fully qualified counselors will guide you through a tailor-made treatment program specifically designed to help you overcome the negative thoughts and behaviors that contribute to drug abuse.";

$DIFF_HEADING = 'The difference between cognitive behavior therapy and dialectical behavior therapy';
$DIFF_BODY = "Cognitive behavioral therapy is one of the most popular forms of psychotherapy practiced in the world today. It is a time-limited therapy technique that focuses on how a person's thoughts and attitudes influence their feelings and behavior. During CBT, people develop effective coping strategies to manage current problems.\n\nDialectical behavior therapy was built on the foundation of CBT and the two therapies share a number of similarities. The main point of difference is that DBT puts a special emphasis on the psychosocial aspect of treatment. Because DBT was originally developed to treat borderline personality disorder - a condition characterized by unstable moods and interpersonal relationships - it prioritizes the need for self-acceptance and the validation of emotional experiences. Whereas CBT is primarily focused on behavioral change, inpatient DBT treatment teaches you how to accept and manage your circumstances.\n\nDBT residential treatment for adults also tends to be more structured than CBT and more likely to include a group therapy element, which provides an important social context where newly developed skills can be applied.";

$HOLISTIC_BODY = "Our DBT inpatient treatment program incorporates a variety of complementary therapies that build on the skills obtained during DBT treatment. Throughout your stay at our DBT inpatient treatment center, you'll have the opportunity to engage in holistic wellness practices such as yoga, mindfulness meditation and exercise therapy, which can be very valuable for reducing stress and enhancing your overall wellbeing.\n\nCultivating healthy and sustainable habits is crucial for maintaining a healthy, balanced and happy lifestyle long after the completion of our residential DBT program.";

global $wpdb;
$idc_re = '#<!-- wp:rehab/intro-doctor-card (\{.*?\}) -->.*?<!-- /wp:rehab/intro-doctor-card -->\s*#s';

// ---- REH-175: page 1327 --------------------------------------------------
$c = get_post_field( 'post_content', 1327 );
if ( $c ) {
	$hits = 0;
	$new  = preg_replace_callback( $idc_re, static function ( $m ) use ( &$hits, $idc_fn, $plain_from_html, $HOW_BODY, $DIFF_HEADING, $DIFF_BODY, $HOLISTIC_BODY ) {
		$a       = json_decode( $m[1], true ) ?: [];
		$eyebrow = (string) ( $a['eyebrow'] ?? '' );
		$heading = (string) ( $a['heading'] ?? '' );
		$title = '' !== $heading ? $heading : $eyebrow;
		if ( str_starts_with( $title, 'How we treat substance abuse' ) ) {
			$hits++;
			return $idc_fn( $title, $HOW_BODY );
		}
		if ( $title === $DIFF_HEADING ) {
			// Already inserted on a previous run — re-emit in the correct shape.
			return $idc_fn( $DIFF_HEADING, $DIFF_BODY );
		}
		if ( str_starts_with( $title, 'Holistic inpatient DBT treatment' ) ) {
			$hits++;
			return $idc_fn( $title, $HOLISTIC_BODY );
		}
		if ( str_starts_with( $title, 'Learn more about our dialectical' ) ) {
			$hits++;
			return $idc_fn( $title, $plain_from_html( (string) ( $a['body'] ?? '' ) ) );
		}
		return $m[0];
	}, $c );
	if ( false === strpos( $new, $DIFF_HEADING ) ) {
		// First run: the difference section doesn't exist yet — insert it
		// directly after the corrected "How we treat…" block.
		$how_block_end = strpos( $new, '<!-- /wp:rehab/intro-doctor-card -->', strpos( $new, 'How we treat substance abuse' ) );
		if ( false !== $how_block_end ) {
			$insert_at = $how_block_end + strlen( "<!-- /wp:rehab/intro-doctor-card -->\n\n" );
			$new = substr( $new, 0, $insert_at ) . $idc_fn( $DIFF_HEADING, $DIFF_BODY ) . substr( $new, $insert_at );
		}
	}
	if ( 3 !== $hits ) {
		echo "1327: matched {$hits}/3 intro-doctor-card blocks — SKIPPED\n";
	} elseif ( $rehab_dry ) {
		echo "[DRY] 1327: would rewrite 3 blocks (+1 new), delta " . ( strlen( $new ) - strlen( $c ) ) . " chars\n";
	} else {
		$wpdb->update( $wpdb->posts, [ 'post_content' => $new ], [ 'ID' => 1327 ] );
		clean_post_cache( 1327 );
		echo "1327: rewrote 3 blocks, added 1 (difference section)\n";
	}
}

// ---- REH-175: page 857 ---------------------------------------------------
$c = get_post_field( 'post_content', 857 );
if ( $c ) {
	$hits = 0;
	$new  = preg_replace_callback( $idc_re, static function ( $m ) use ( &$hits, $idc_fn, $plain_from_html ) {
		$a = json_decode( $m[1], true ) ?: [];
		$title_857 = '' !== (string) ( $a['heading'] ?? '' ) ? (string) $a['heading'] : (string) ( $a['eyebrow'] ?? '' );
		if ( 'The Diamond Program' === $title_857 ) {
			$hits++;
			return $idc_fn( 'The Diamond Program', $plain_from_html( (string) ( $a['body'] ?? '' ) ) );
		}
		return $m[0];
	}, $c );
	if ( 1 !== $hits ) {
		echo "857: matched {$hits}/1 — SKIPPED\n";
	} elseif ( $rehab_dry ) {
		echo "[DRY] 857: would move eyebrow->heading\n";
	} else {
		$wpdb->update( $wpdb->posts, [ 'post_content' => $new ], [ 'ID' => 857 ] );
		clean_post_cache( 857 );
		echo "857: eyebrow -> heading\n";
	}
}

// ---- REH-173: page 9182 (job ad) ----------------------------------------
$c = get_post_field( 'post_content', 9182 );
if ( $c ) {
	$ok = preg_match( '#<!-- wp:rehab/treatment-phases (\{.*?\}) -->#s', $c, $pm );
	if ( ! $ok ) {
		echo "9182: no phases block — SKIPPED\n";
	} else {
		$pa   = json_decode( $pm[1], true ) ?: [];
		$paras = [];
		foreach ( (array) ( $pa['phases'] ?? [] ) as $phase ) {
			foreach ( (array) ( $phase['paragraphs'] ?? [] ) as $p ) {
				$p = trim( html_entity_decode( (string) $p, ENT_QUOTES ) );
				if ( '' !== $p ) {
					$paras[] = $p;
				}
			}
		}
		if ( count( $paras ) < 3 ) {
			echo '9182: only ' . count( $paras ) . " paragraphs extracted — SKIPPED\n";
		} else {
			$new = $prose_h1_fn( get_the_title( 9182 ), $paras ) . "<!-- wp:rehab/job-apply /-->\n";
			if ( $rehab_dry ) {
				echo '[DRY] 9182: would rebuild as H1 + ' . count( $paras ) . " paragraphs + apply box (from " . strlen( $c ) . " to " . strlen( $new ) . " chars)\n";
			} else {
				$wpdb->update( $wpdb->posts, [ 'post_content' => $new ], [ 'ID' => 9182 ] );
				clean_post_cache( 9182 );
				echo '9182: rebuilt as H1 + ' . count( $paras ) . " paragraphs + apply box\n";
			}
		}
	}
}
echo "done.\n";
