<?php
/**
 * One-shot maintenance trigger.
 *
 * Hit /?rehab_oneshot=fix-footer to run a single named maintenance task.
 * Returns plain-text status. Add new cases below as needed.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Brand preview: visit any page with `?brand_preview=anker` to overlay that
 * brand's child-theme stylesheet on top of the active theme's CSS, without
 * switching themes site-wide. Lets us visually verify token-only brand variants.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! isset( $_GET['brand_preview'] ) ) return;
	$brand = sanitize_key( $_GET['brand_preview'] );
	$slug  = $brand . '-child';
	$theme = wp_get_theme( $slug );
	if ( ! $theme->exists() ) return;
	wp_enqueue_style(
		'rehab-brand-preview',
		get_theme_root_uri() . '/' . $slug . '/style.css',
		[ 'rehab-tokens', 'rehab-typography', 'rehab-layout' ],
		'preview-' . time()
	);
}, 100 );

add_action( 'init', function () {
	if ( ! isset( $_GET['rehab_oneshot'] ) ) return;
	$task = sanitize_key( $_GET['rehab_oneshot'] );

	header( 'Content-Type: text/plain; charset=utf-8' );

	switch ( $task ) {
		case 'fix-footer':
			$address = "8 Moo 14, Soi Mon Mai Hin Lek Fai\nHua Hin District, Prachuap Khiri Khan\nThailand 77110";
			$intl = "Australia|+61 2 7908 2277\nUSA / Canada|+1 330 822 5340\nUK|+44 330 822 5340\nEurope|+31 20 532 2548";
			set_theme_mod( 'rehab_footer_address', $address );
			set_theme_mod( 'rehab_footer_intl_phones', $intl );
			set_theme_mod( 'rehab_footer_copyright', '&copy; ' . gmdate( 'Y' ) . ' The Diamond Rehab Thailand. All rights reserved.' );
			set_theme_mod( 'rehab_social_facebook',  'https://www.facebook.com/diamondrehabthailand' );
			set_theme_mod( 'rehab_social_instagram', 'https://www.instagram.com/diamondrehabthailand' );
			echo "OK fix-footer\n";
			break;

		case 'fix-contact-page':
			$post = get_post( 1189 );
			if ( ! $post ) { echo "no contact page\n"; break; }
			$content = $post->post_content;
			// Remove any rehab/cta block on the contact page (the redundant "Get in touch — 24/7" CTA).
			$cleaned = preg_replace(
				'/<!--\s*wp:rehab\/cta[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/cta\s*-->)\s*/is',
				'',
				$content
			);
			wp_update_post( [ 'ID' => 1189, 'post_content' => $cleaned ] );
			echo "OK contact page cleaned\n";
			break;

		case 'list-contact-blocks':
			$post = get_post( 1189 );
			preg_match_all( '/<!--\s*wp:([a-z0-9\/-]+)/i', $post->post_content, $m );
			echo implode( "\n", $m[1] ) . "\n";
			break;

		case 'dump-contact':
			$post = get_post( 1189 );
			echo $post->post_content;
			break;

		case 'fix-footer-typo':
			global $wpdb;
			$updated = $wpdb->query( $wpdb->prepare(
				"UPDATE {$wpdb->posts} SET post_title = %s WHERE post_type = 'nav_menu_item' AND post_title = %s",
				'Chocolate addiction',
				'Chocolate additction'
			) );
			$meta_updated = $wpdb->query( $wpdb->prepare(
				"UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE meta_key = '_menu_item_title' AND meta_value = %s",
				'Chocolate addiction',
				'Chocolate additction'
			) );
			echo "OK posts updated $updated, postmeta updated $meta_updated\n";
			break;

		case 'list-menus':
			$menus = wp_get_nav_menus();
			foreach ( $menus as $m ) {
				echo "menu " . $m->term_id . " — " . $m->name . "\n";
				$items = wp_get_nav_menu_items( $m->term_id );
				if ( ! $items ) continue;
				foreach ( $items as $it ) {
					echo "  " . $it->ID . "\t" . $it->title . "\t" . $it->url . "\n";
				}
			}
			break;

		case 'rebuild-cost-page':
			$je = function( $arr ) { return wp_json_encode( $arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ); };

			$post = get_post( 834 );
			if ( ! $post ) { echo "no cost page\n"; break; }
			$content = $post->post_content;

			// 1. Replace the prose placeholder with real investment-includes copy.
			$prose_heading = 'What your investment includes';
			$prose_body = "Every program at The Diamond Rehab Thailand is fully inclusive — no hidden fees, no add-ons, no surprises on the final invoice. Your investment covers your full medical, clinical, and lifestyle care from arrival to discharge. That includes complimentary VIP airport transfer from Bangkok, a private en-suite or ocean-view villa, all meals prepared by our chef, daily one-on-one psychotherapy, group therapy and CBT/DBT sessions, weekly psychiatric review, neurofeedback and trauma-informed care where indicated, daily personal training and massage, all medications during the stay, weekly bloodwork and any medical investigations required, and a structured aftercare and relapse-prevention plan extended for life.";
			$new_prose =
				"<!-- wp:rehab/prose -->\n" .
				'<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner">' .
				"<!-- wp:heading -->\n" .
				'<h2 class="wp-block-heading">' . esc_html( $prose_heading ) . '</h2>' . "\n" .
				"<!-- /wp:heading -->\n\n" .
				"<!-- wp:paragraph -->\n" .
				'<p>' . esc_html( $prose_body ) . '</p>' . "\n" .
				"<!-- /wp:paragraph -->" .
				"</div></div></section>\n" .
				"<!-- /wp:rehab/prose -->";

			// Replace the existing rehab/prose (first occurrence).
			$content = preg_replace(
				'/<!--\s*wp:rehab\/prose\b.*?<!--\s*\/wp:rehab\/prose\s*-->/is',
				$new_prose,
				$content,
				1
			);

			// 2. Replace the 3 duplicate programs with 3 differentiated tiers.
			$tiers = [
				[
					'duration' => '4 weeks',
					'title'    => 'Essential Program',
					'price'    => '$24,000',
					'suffix'   => 'USD all-inclusive',
					'body'     => 'Foundation residential program for alcohol or stimulant use without complex medical or psychiatric needs. Includes private accommodation, full clinical program, and lifetime aftercare.',
				],
				[
					'duration' => '8 weeks',
					'title'    => 'Comprehensive Program',
					'price'    => '$42,000',
					'suffix'   => 'USD all-inclusive',
					'body'     => 'Recommended for stimulant, opioid, or polysubstance addiction. Doubled clinical depth, dual-diagnosis psychiatric care, EMDR, neurofeedback, and the full lifestyle reconstruction phase.',
				],
				[
					'duration' => '12 weeks',
					'title'    => 'Restorative Program',
					'price'    => '$58,000',
					'suffix'   => 'USD all-inclusive',
					'body'     => 'Extended program for severe addiction, complex trauma, or executives needing a complete lifestyle reset. Includes intensive trauma work, family therapy intensive, and enhanced aftercare integration.',
				],
			];

			$tier_blocks = '';
			foreach ( $tiers as $t ) {
				$attrs = $je( [
					'duration'    => $t['duration'],
					'title'       => $t['title'],
					'price'       => $t['price'],
					'priceSuffix' => $t['suffix'],
					'body'        => $t['body'],
					'ctaText'     => 'Speak with admissions',
					'ctaUrl'      => '/contact-us/',
				] );
				$tier_blocks .=
					'<!-- wp:rehab/program ' . $attrs . " -->\n" .
					'<div class="wp-block-rehab-program rehab-program">' .
					'<span class="rehab-program__duration">' . esc_html( $t['duration'] ) . '</span>' .
					'<h3 class="rehab-program__title">' . esc_html( $t['title'] ) . '</h3>' .
					'<div><span class="rehab-program__price">' . esc_html( $t['price'] ) . '</span>' .
					'<span class="rehab-program__price-suffix">' . esc_html( $t['suffix'] ) . '</span></div>' .
					'<p class="rehab-program__body">' . esc_html( $t['body'] ) . '</p>' .
					'<a class="rehab-btn rehab-btn--outline rehab-program__cta" href="/contact-us/">Speak with admissions</a>' .
					"</div>\n" .
					"<!-- /wp:rehab/program -->\n\n";
			}
			$tier_blocks = rtrim( $tier_blocks );

			// Replace the inner rehab/program list (3 entries) with the 3 differentiated tiers.
			// Find the programs-list wrapper, replace its inner program blocks.
			$content = preg_replace_callback(
				'/(<!--\s*wp:rehab\/programs-list\b.*?<div class="rehab-programs__list">)(.*?)(<\/div><\/div><\/section>\s*<!--\s*\/wp:rehab\/programs-list\s*-->)/is',
				function ( $m ) use ( $tier_blocks ) {
					return $m[1] . "\n" . $tier_blocks . "\n" . $m[3];
				},
				$content,
				1
			);

			wp_update_post( [ 'ID' => 834, 'post_content' => wp_slash( $content ) ] );
			echo "OK cost page rebuilt — 3 tiers (Essential 4w/Comprehensive 8w/Restorative 12w)\n";
			break;

		case 'fill-policy-pages':
			$pages = [
				1546 => [
					'h2'    => 'Our policies and procedures',
					'paras' => [
						'The Diamond Rehab Thailand operates under a complete framework of clinical, ethical, and operational policies that govern every part of the client experience — from initial enquiry through admission, treatment, discharge, and aftercare. Our policies are reviewed annually by our medical and clinical leadership and updated continuously as best-practice evidence evolves.',
						'<strong>Clinical governance</strong> — All treatment is delivered by clinicians licensed and registered with their professional bodies. Our resident psychiatrist provides medical oversight; all care plans are reviewed weekly in multi-disciplinary case conference.',
						'<strong>Confidentiality</strong> — All staff sign comprehensive non-disclosure agreements. Client information is never disclosed to any third party without explicit written consent. See our <a href="/confidentiality-policy/">Confidentiality Policy</a> for details.',
						'<strong>Safeguarding</strong> — We maintain a written safeguarding policy covering risk assessment, vulnerable-adult protocols, mental-health crisis response, and safe transitions of care. All clinical staff complete annual safeguarding training.',
						'<strong>Complaints</strong> — Any client or family member may raise a concern at any time, in writing or verbally, to our Clinical Director. Complaints are acknowledged within 24 hours and investigated within 7 days. We commit to transparent, unconditional resolution.',
						'<strong>Medication management</strong> — All prescribing is undertaken by registered medical doctors only. Medications are stored in a locked, audited cabinet; administration is double-witnessed and recorded.',
						'<strong>Health, safety, and infection control</strong> — Our facility complies with the Thai Ministry of Public Health licensing requirements and is inspected periodically. All chefs and food handlers hold current Thai food-handler certifications.',
						'<strong>Data protection</strong> — Client records are stored encrypted, on infrastructure inside the European Union or equivalent jurisdictions, and retained per Thai medical-records regulations. See our <a href="/privacy-policy/">Privacy Policy</a> for the legal basis and your rights.',
						'For the full text of any specific policy, please <a href="/contact-us/">contact our admissions team</a>.',
					],
				],
				4197 => [
					'h2'    => 'Confidentiality at The Diamond Rehab Thailand',
					'paras' => [
						'Absolute confidentiality is foundational to everything we do. We treat executives, founders, public figures, and high-net-worth individuals whose privacy must be protected without exception, and our entire operation is built around that requirement.',
						'<strong>Non-disclosure agreements</strong> — Every member of our team signs a comprehensive NDA as a condition of employment. We can provide bespoke client NDAs on request and routinely accommodate enhanced confidentiality measures for high-profile admissions.',
						'<strong>Use of an alias</strong> — Clients may use an alias throughout their stay, on all internal records, and in all communication. Our intake team will work with you in advance to establish this if needed.',
						'<strong>Discreet arrival</strong> — Complimentary VIP airport transfer from Bangkok in unmarked vehicles. Direct entry to the property, away from public view.',
						'<strong>Information minimisation</strong> — We collect only the personal data clinically necessary to deliver care. We never disclose client information to any third party without explicit written consent. We never share, sell, or use client data for marketing.',
						'<strong>Limited and audited access</strong> — Access to client records is restricted to staff with a clinical need-to-know. All access is logged and reviewed.',
						'<strong>Family involvement</strong> — Family contact during treatment is fully under your control. We will not confirm or acknowledge a client\'s presence at our facility to any third party — including family — without your written consent.',
						'<strong>Discharge</strong> — Aftercare contact uses whatever channel and identity you specify. Records are retained per Thai medical-records law but never disclosed to outside parties.',
						'For specific concerns or to request a tailored confidentiality arrangement, please <a href="/contact-us/">speak with our admissions team</a> in advance — every request is treated with the same discretion as the rest of your care.',
					],
				],
			];

			foreach ( $pages as $pid => $cfg ) {
				$post = get_post( $pid );
				if ( ! $post ) { echo "skip $pid\n"; continue; }
				$blocks = "<!-- wp:rehab/prose -->\n" .
					'<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner">' .
					"<!-- wp:heading -->\n" .
					'<h2 class="wp-block-heading">' . esc_html( $cfg['h2'] ) . "</h2>\n" .
					"<!-- /wp:heading -->\n";
				foreach ( $cfg['paras'] as $p ) {
					$blocks .= "\n<!-- wp:paragraph -->\n<p>" . wp_kses( $p, [ 'a' => [ 'href' => [] ], 'strong' => [], 'em' => [] ] ) . "</p>\n<!-- /wp:paragraph -->\n";
				}
				$blocks .= "</div></div></section>\n<!-- /wp:rehab/prose -->";

				wp_update_post( [ 'ID' => $pid, 'post_content' => wp_slash( $blocks ) ] );
				echo "OK $pid filled\n";
			}
			break;

		case 'fill-stub-pages':
			$pages = [
				857 => [ // Programs
					'h2'    => 'Treatment programs at The Diamond Rehab Thailand',
					'paras' => [
						'Every program at The Diamond Rehab Thailand is residential, medically supervised, and individually tailored. Programs run from 4 to 12 weeks depending on the substance, severity, and your life circumstances. Length is reassessed weekly with you and your clinical team.',
						'Three program tiers are available — Essential (4 weeks), Comprehensive (8 weeks), and Restorative (12 weeks). Each is fully inclusive of accommodation, all clinical sessions, all medications, all meals, and a structured aftercare plan. See our <a href="/cost/">cost page</a> for detailed pricing and what each tier includes.',
						'Whichever program length you choose, the same maximum-12-client cap applies and you will receive the same one-to-one staff ratio, daily psychotherapy, dual-diagnosis psychiatric care, and lifetime aftercare. The difference between tiers is depth and duration of clinical work, not standard of care.',
						'For a confidential conversation about which program fits your circumstances, <a href="/contact-us/">speak with our admissions team</a> — available 24/7, free and no-obligation.',
					],
				],
				9015 => [ // Careers
					'h2'    => 'Join the Diamond clinical team',
					'paras' => [
						'The Diamond Rehab Thailand is built around a small team of internationally trained clinicians, doctors, and support staff who work together with a maximum of 12 clients at a time. We hire for clinical excellence, integrity, and the ability to hold absolute confidentiality with high-profile clients.',
						'Open positions appear on this page when available. We typically recruit for medical doctors and psychiatrists with addiction-medicine experience, registered nurses with detox and dual-diagnosis training, individual therapists (CBT/DBT/EMDR-trained), holistic practitioners (yoga, fitness, mindfulness), and hospitality / housekeeping staff to support our 5-star sanctuary standard.',
						'We are headquartered in Hua Hin, Thailand. International applicants are welcome — we sponsor work permits for clinical roles and provide relocation support. All staff sign comprehensive non-disclosure agreements as a condition of employment.',
						'To apply or register interest for future openings, please <a href="/contact-us/">contact our team</a> with your CV and a brief note on which role interests you.',
					],
				],
			];

			foreach ( $pages as $pid => $cfg ) {
				$post = get_post( $pid );
				if ( ! $post ) { echo "skip $pid\n"; continue; }
				$blocks = "<!-- wp:rehab/prose -->\n" .
					'<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner">' .
					"<!-- wp:heading -->\n" .
					'<h2 class="wp-block-heading">' . esc_html( $cfg['h2'] ) . "</h2>\n" .
					"<!-- /wp:heading -->\n";
				foreach ( $cfg['paras'] as $p ) {
					$blocks .= "\n<!-- wp:paragraph -->\n<p>" . wp_kses( $p, [ 'a' => [ 'href' => [] ] ] ) . "</p>\n<!-- /wp:paragraph -->\n";
				}
				$blocks .= "</div></div></section>\n<!-- /wp:rehab/prose -->";

				wp_update_post( [ 'ID' => $pid, 'post_content' => wp_slash( $blocks ) ] );
				echo "OK $pid filled\n";
			}
			break;

		case 'check-search-engine':
			echo "blog_public: " . get_option( 'blog_public' ) . "\n";
			echo "permalink_structure: " . get_option( 'permalink_structure' ) . "\n";
			echo "active_plugins: " . print_r( get_option( 'active_plugins', [] ), true );
			break;

		case 'list-themes':
			$themes = wp_get_themes();
			foreach ( $themes as $slug => $t ) {
				echo $slug . "\t" . $t->get( 'Name' ) . "\t" . ( $t->parent() ? 'parent: ' . $t->parent()->get( 'Name' ) : '' ) . "\n";
			}
			break;

		case 'preview-anker':
			// Render the homepage HTML using anker tokens (server-side preview).
			// We can't actually switch themes mid-request safely, so just confirm the theme is valid.
			$theme = wp_get_theme( 'anker-child' );
			if ( ! $theme->exists() ) { echo "anker-child theme not found\n"; break; }
			echo "anker-child OK — name: " . $theme->get( 'Name' ) . ", parent: " . ( $theme->parent() ? $theme->parent()->get( 'Name' ) : 'NONE' ) . "\n";
			break;

		case 'reparent-team-careers':
			global $wpdb;
			$team_parent    = 722;
			$careers_parent = 9015;

			$careers_slugs = [ 'addiction-counsellor', 'admissions-manager', 'clinical-psychologist' ];

			$team_slugs = [
				'ananyalak-sonin', 'aor-general-manager', 'asif-baliyan-md', 'augustine-dewes',
				'bongkotkarn-sirijunchuen', 'brian-tucker', 'derrick-kwa', 'dr-harshi-dhingra',
				'dr-lorraine-levy', 'dr-ngamwong-jarusuraisin', 'dr-roshan-fernando',
				'eugene-pretorius', 'irene-grace-maghopoy', 'isuru-bogodwatta', 'james-donovan',
				'jeerumporn-choutrakun', 'jessica-waller', 'kittikawin-kwin-rachawong',
				'max-duddy', 'natjittra-chathaisong', 'navneth-mendis', 'ponsuppat-udom-nurse',
				'prasong-homkong', 'saran-badod', 'sergio-pereira', 'shehan-williams',
				'supanni-sanli', 'theo-and-panwadee-de-vries', 'thipada-sritongkom-nurse',
				'vladimira-ivanova', 'wei-ling', 'wuttipong-wandee',
			];

			$updates = [];
			foreach ( $careers_slugs as $slug ) $updates[] = [ $slug, $careers_parent ];
			foreach ( $team_slugs as $slug )    $updates[] = [ $slug, $team_parent ];

			$found = 0; $updated = 0; $skipped = 0;
			foreach ( $updates as [ $slug, $parent ] ) {
				$post = $wpdb->get_row( $wpdb->prepare(
					"SELECT ID, post_parent, post_status FROM {$wpdb->posts}
					 WHERE post_name = %s AND post_type = 'page' LIMIT 1",
					$slug
				) );
				if ( ! $post ) { echo "skip $slug (not found)\n"; $skipped++; continue; }
				$found++;
				if ( (int) $post->post_parent === (int) $parent ) {
					echo "already $slug → $parent\n";
					continue;
				}
				$result = wp_update_post( [ 'ID' => $post->ID, 'post_parent' => $parent ] );
				if ( $result && ! is_wp_error( $result ) ) {
					$updated++;
					echo "OK $slug → parent $parent\n";
				} else {
					echo "FAIL $slug\n";
				}
			}
			// Important: flush rewrite rules so new permalinks take effect.
			flush_rewrite_rules( false );
			echo "\nfound=$found updated=$updated skipped=$skipped\n";
			break;

		case 'replace-host':
			// Switch site to IP host AND scrub the DB.
			// Pass ?from=http://localhost:8081&to=http://5.223.87.211:8081 to override.
			$from = isset( $_GET['from'] ) ? esc_url_raw( $_GET['from'] ) : 'http://localhost:8081';
			$to   = isset( $_GET['to'] )   ? esc_url_raw( $_GET['to'] )   : 'http://5.223.87.211:8081';
			if ( ! $from || ! $to || $from === $to ) { echo "bad from/to\n"; break; }

			global $wpdb;
			$totals = [];

			$replace_recursive = function ( $value ) use ( $from, $to, &$replace_recursive ) {
				if ( is_string( $value ) ) {
					return str_replace( $from, $to, $value );
				}
				if ( is_array( $value ) ) {
					foreach ( $value as $k => $v ) $value[ $k ] = $replace_recursive( $v );
					return $value;
				}
				if ( is_object( $value ) ) {
					// __PHP_Incomplete_Class can't be modified — skip these (Freemius etc.).
					if ( $value instanceof __PHP_Incomplete_Class ) return $value;
					$vars = get_object_vars( $value );
					foreach ( $vars as $k => $v ) $value->{$k} = $replace_recursive( $v );
					return $value;
				}
				return $value;
			};

			// 1. options table — handle serialized data.
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT option_id, option_name, option_value FROM {$wpdb->options} WHERE option_value LIKE %s",
				'%' . $wpdb->esc_like( $from ) . '%'
			) );
			$opt_changed = 0;
			foreach ( $rows as $r ) {
				$val   = maybe_unserialize( $r->option_value );
				$new   = $replace_recursive( $val );
				if ( $new !== $val ) {
					update_option( $r->option_name, $new );
					$opt_changed++;
				}
			}
			$totals['options'] = $opt_changed;

			// 2. postmeta — same approach.
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT meta_id, post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_value LIKE %s",
				'%' . $wpdb->esc_like( $from ) . '%'
			) );
			$pm_changed = 0;
			foreach ( $rows as $r ) {
				$val = maybe_unserialize( $r->meta_value );
				$new = $replace_recursive( $val );
				if ( $new !== $val ) {
					update_post_meta( $r->post_id, $r->meta_key, $new );
					$pm_changed++;
				}
			}
			$totals['postmeta'] = $pm_changed;

			// 3. usermeta.
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT umeta_id, user_id, meta_key, meta_value FROM {$wpdb->usermeta} WHERE meta_value LIKE %s",
				'%' . $wpdb->esc_like( $from ) . '%'
			) );
			$um_changed = 0;
			foreach ( $rows as $r ) {
				$val = maybe_unserialize( $r->meta_value );
				$new = $replace_recursive( $val );
				if ( $new !== $val ) {
					update_user_meta( $r->user_id, $r->meta_key, $new );
					$um_changed++;
				}
			}
			$totals['usermeta'] = $um_changed;

			// 4. posts — post_content, post_excerpt, guid (str_replace; never serialized).
			$post_changed = $wpdb->query( $wpdb->prepare(
				"UPDATE {$wpdb->posts} SET
					post_content = REPLACE(post_content, %s, %s),
					post_excerpt = REPLACE(post_excerpt, %s, %s),
					guid         = REPLACE(guid, %s, %s)
				WHERE post_content LIKE %s OR post_excerpt LIKE %s OR guid LIKE %s",
				$from, $to, $from, $to, $from, $to,
				'%' . $wpdb->esc_like( $from ) . '%',
				'%' . $wpdb->esc_like( $from ) . '%',
				'%' . $wpdb->esc_like( $from ) . '%'
			) );
			$totals['posts'] = $post_changed;

			// 5. termmeta if present.
			if ( ! empty( $wpdb->termmeta ) ) {
				$rows = $wpdb->get_results( $wpdb->prepare(
					"SELECT meta_id, term_id, meta_key, meta_value FROM {$wpdb->termmeta} WHERE meta_value LIKE %s",
					'%' . $wpdb->esc_like( $from ) . '%'
				) );
				$tm_changed = 0;
				foreach ( $rows as $r ) {
					$val = maybe_unserialize( $r->meta_value );
					$new = $replace_recursive( $val );
					if ( $new !== $val ) {
						update_term_meta( $r->term_id, $r->meta_key, $new );
						$tm_changed++;
					}
				}
				$totals['termmeta'] = $tm_changed;
			}

			echo "Replaced $from → $to\n";
			foreach ( $totals as $t => $n ) echo "  $t: $n rows\n";

			// Flush rewrite rules so any cached URLs refresh.
			wp_cache_flush();
			break;

		case 'set-host-options':
			global $wpdb;
			// Bypass any potential filter cache by writing directly.
			$wpdb->update( $wpdb->options, [ 'option_value' => 'http://5.223.87.211:8081' ], [ 'option_name' => 'siteurl' ] );
			$wpdb->update( $wpdb->options, [ 'option_value' => 'http://5.223.87.211:8081' ], [ 'option_name' => 'home' ] );
			wp_cache_flush();
			echo "siteurl raw: " . $wpdb->get_var( "SELECT option_value FROM {$wpdb->options} WHERE option_name='siteurl'" ) . "\n";
			echo "home raw:    " . $wpdb->get_var( "SELECT option_value FROM {$wpdb->options} WHERE option_name='home'" ) . "\n";
			echo "siteurl runtime: " . get_option( 'siteurl' ) . "\n";
			echo "home runtime:    " . get_option( 'home' ) . "\n";
			echo "WP_HOME defined: " . ( defined( 'WP_HOME' ) ? WP_HOME : '(not defined)' ) . "\n";
			echo "WP_SITEURL defined: " . ( defined( 'WP_SITEURL' ) ? WP_SITEURL : '(not defined)' ) . "\n";
			break;

		case 'clear-feed-transients':
			global $wpdb;
			$deleted = $wpdb->query(
				"DELETE FROM {$wpdb->options}
				 WHERE option_name LIKE '\\_transient\\_feed\\_%'
				    OR option_name LIKE '\\_transient\\_timeout\\_feed\\_%'
				    OR option_name LIKE '\\_site\\_transient\\_feed\\_%'
				    OR option_name LIKE '\\_site\\_transient\\_timeout\\_feed\\_%'"
			);
			echo "OK cleared $deleted feed-transient rows\n";
			break;

		case 'find-localhost-options':
			global $wpdb;
			$rows = $wpdb->get_results(
				"SELECT option_id, option_name, LEFT( option_value, 200 ) AS preview
				 FROM {$wpdb->options}
				 WHERE option_value LIKE '%localhost%'"
			);
			foreach ( $rows as $r ) {
				echo "[{$r->option_id}] {$r->option_name}: {$r->preview}\n";
			}
			break;

		case 'count-localhost-urls':
			global $wpdb;
			$site = get_option( 'siteurl' );
			$home = get_option( 'home' );
			echo "siteurl: $site\n";
			echo "home:    $home\n\n";
			$cols = [
				'posts'    => [ 'post_content', 'post_excerpt', 'guid' ],
				'postmeta' => [ 'meta_value' ],
				'options'  => [ 'option_value' ],
				'usermeta' => [ 'meta_value' ],
			];
			foreach ( $cols as $t => $columns ) {
				$tbl = $wpdb->{$t};
				$loc_where = implode( ' OR ', array_map( fn( $c ) => "$c LIKE '%localhost%'", $columns ) );
				$ip_where  = implode( ' OR ', array_map( fn( $c ) => "$c LIKE '%5.223.87.211%'", $columns ) );
				$loc       = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tbl WHERE $loc_where" );
				$ip        = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tbl WHERE $ip_where" );
				echo "$t: localhost=$loc, IP=$ip\n";
			}
			break;

		case 'fix-program-cta-links':
			$post = get_post( 6 );
			if ( ! $post ) { echo "no homepage\n"; break; }
			$content = $post->post_content;
			// Update each rehab/program block's CTA href to /cost/
			$content = preg_replace(
				'/(<a class="rehab-btn rehab-btn--outline rehab-program__cta" href=")#(")/',
				'$1/cost/$2',
				$content
			);
			$content = preg_replace(
				'/("ctaUrl":")"/',
				'$1/cost/"',
				$content
			);
			wp_update_post( [ 'ID' => 6, 'post_content' => wp_slash( $content ) ] );
			echo "OK program CTAs updated to /cost/\n";
			break;

		case 'fix-step1':
			$post = get_post( 6 );
			if ( ! $post ) { echo "no homepage\n"; break; }
			$content = $post->post_content;
			// Replace step 1's placeholder.
			$replaced = 0;
			$content = preg_replace(
				'/<h3 class="rehab-step__title">Step title<\/h3>\s*<p class="rehab-step__body">Describe what happens in this step\.<\/p>/',
				'<h3 class="rehab-step__title">Confidential admission</h3><p class="rehab-step__body">Free, no-obligation conversation with our admissions team. Tailored treatment plan within 24 hours, complimentary VIP airport transfer arranged.</p>',
				$content,
				1,
				$replaced
			);
			// Also fix the JSON attrs on the same step.
			$content = preg_replace(
				'/("title":")Step title("[\s\S]*?"body":")Describe what happens in this step\.(")/',
				'$1Confidential admission$2Free, no-obligation conversation with our admissions team. Tailored treatment plan within 24 hours, complimentary VIP airport transfer arranged.$3',
				$content,
				1
			);
			wp_update_post( [ 'ID' => 6, 'post_content' => wp_slash( $content ) ] );
			echo "OK step 1 updated ($replaced HTML replacement)\n";
			break;

		case 'remove-empty-media-mentions':
			$post = get_post( 6 );
			if ( ! $post ) { echo "no homepage\n"; break; }
			$content = $post->post_content;
			$new = preg_replace(
				'/<!--\s*wp:rehab\/media-mentions\b.*?<!--\s*\/wp:rehab\/media-mentions\s*-->\s*/is',
				'',
				$content,
				1,
				$count
			);
			if ( $count > 0 ) {
				wp_update_post( [ 'ID' => 6, 'post_content' => wp_slash( $new ) ] );
				echo "OK removed $count media-mentions block\n";
			} else {
				echo "no media-mentions block found\n";
			}
			break;

		case 'list-home-blocks':
			$post = get_post( 6 );
			preg_match_all( '/<!--\s*wp:([a-z0-9\/-]+)/i', $post->post_content, $m );
			echo implode( "\n", $m[1] ) . "\n";
			break;

		case 'add-article-feed-to-home':
			$post = get_post( 6 );
			if ( ! $post ) { echo "no homepage\n"; break; }
			$content = $post->post_content;
			if ( strpos( $content, 'wp:rehab/article-feed' ) !== false ) {
				echo "already present\n"; break;
			}
			$block = "<!-- wp:rehab/article-feed /-->";
			// Insert before the last rehab/cta (final CTA) so feed appears above it.
			// Or before the FAQ block. Let's insert before rehab/faq if present, else before last cta.
			$marker = '';
			if ( preg_match( '/<!--\s*wp:rehab\/faq\b/', $content ) ) {
				$content = preg_replace( '/(<!--\s*wp:rehab\/faq\b)/', $block . "\n\n$1", $content, 1 );
				$marker = 'before FAQ';
			} else {
				// Append before final cta — match the LAST rehab/cta block start.
				$pos = strrpos( $content, '<!-- wp:rehab/cta' );
				if ( $pos !== false ) {
					$content = substr( $content, 0, $pos ) . $block . "\n\n" . substr( $content, $pos );
					$marker = 'before final CTA';
				} else {
					$content .= "\n\n" . $block;
					$marker = 'appended';
				}
			}
			wp_update_post( [ 'ID' => 6, 'post_content' => wp_slash( $content ) ] );
			echo "OK article-feed inserted ($marker)\n";
			break;

		case 'unpublish-demo-pages':
			$ids = [ 12235, 12242 ];
			$count = 0;
			foreach ( $ids as $id ) {
				if ( get_post( $id ) ) {
					wp_update_post( [ 'ID' => $id, 'post_status' => 'draft' ] );
					$count++;
					echo "OK $id moved to draft\n";
				}
			}
			echo "$count pages unpublished\n";
			break;

		case 'set-articles-index-template':
			update_post_meta( 1218, '_wp_page_template', 'template-articles-index.php' );
			// Clear placeholder content if any.
			$post = get_post( 1218 );
			if ( $post && trim( $post->post_content ) === '' ) {
				echo "OK template set; content already empty\n";
			} else {
				wp_update_post( [ 'ID' => 1218, 'post_content' => '' ] );
				echo "OK template set; content cleared\n";
			}
			break;

		case 'dump-cost':
			$p = get_post( 834 );
			if ( ! $p ) { echo "no cost page\n"; break; }
			echo $p->post_content;
			break;

		case 'list-pages-perma':
			// Like list-pages but emits the actual permalink path (with parent prefix), not just the slug.
			$pages = get_posts( [
				'post_type'      => 'page',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'fields'         => 'ids',
			] );
			$lines = [];
			foreach ( $pages as $id ) {
				$path = wp_make_link_relative( get_permalink( $id ) );
				$lines[] = $path;
			}
			sort( $lines );
			echo implode( "\n", array_unique( $lines ) ) . "\n";
			break;

		case 'list-pages':
			$pages = get_posts( [
				'post_type'      => 'page',
				'posts_per_page' => -1,
				'fields'         => 'all',
				'post_status'    => 'publish',
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
			] );
			foreach ( $pages as $p ) {
				$tpl = get_page_template_slug( $p->ID );
				echo $p->ID . "\t" . $p->post_name . "\t" . $tpl . "\t" . $p->post_title . "\n";
			}
			break;

		case 'list-treatments':
			$ids = get_posts( [
				'post_type'      => 'page',
				'posts_per_page' => -1,
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'template-treatment.php',
				'fields'         => 'ids',
			] );
			foreach ( $ids as $id ) {
				$p = get_post( $id );
				echo $id . "\t" . $p->post_name . "\t" . $p->post_title . "\n";
			}
			break;

		case 'upgrade-treatment-hero':
			$pages = [
				853  => [
					'heading' => 'Cocaine addiction treatment',
					'body'    => 'Doctor-led residential rehab for cocaine and stimulant addiction at our private 5-star sanctuary in Hua Hin, Thailand. Tailored to your circumstances. Maximum 12 clients at a time, with absolute confidentiality.',
				],
				867  => [
					'heading' => 'Meth and ice addiction treatment',
					'body'    => 'Intensive residential rehabilitation for methamphetamine addiction. 24/7 medical care, structured therapy, and full lifestyle reconstruction in a private, secure 5-star setting. Tailored programs for executives, professionals, and high-profile individuals.',
				],
				1219 => [
					'heading' => 'All treatments',
					'body'    => 'We treat the full spectrum of substance and behavioral addictions: alcohol, cocaine, methamphetamine, opioids and heroin, prescription medications, cannabis, gambling, and process addictions. Every program is built individually around your medical history, life circumstances, and recovery goals.',
				],
			];
			foreach ( $pages as $page_id => $copy ) {
				$post = get_post( $page_id );
				if ( ! $post ) { echo "skip $page_id\n"; continue; }
				$attrs = wp_json_encode( [
					'variant'    => 'default',
					'background' => 'sage-mist',
					'heading'    => $copy['heading'],
					'body'       => $copy['body'],
					'buttonText' => 'Speak with admissions',
					'buttonUrl'  => '/contact-us/',
					'helper'     => 'Free, confidential, no-obligation.',
				] );
				$block = '<!-- wp:rehab/cta ' . $attrs . " -->\n" .
					'<section class="wp-block-rehab-cta rehab-cta rehab-cta--default rehab-bg-sage-mist" aria-label="Call to action"><div class="rehab-container rehab-container--narrow"><div class="rehab-cta__inner"><h2 class="rehab-heading rehab-heading--lg">' . esc_html( $copy['heading'] ) . '</h2><p class="rehab-cta__body">' . esc_html( $copy['body'] ) . '</p><a class="rehab-btn rehab-btn--luxury" href="/contact-us/">Speak with admissions</a><p class="rehab-cta__helper">Free, confidential, no-obligation.</p></div></div></section>' . "\n" .
					"<!-- /wp:rehab/cta -->";
				$updated = preg_replace(
					'/<!--\s*wp:rehab\/cta\b.*?<!--\s*\/wp:rehab\/cta\s*-->/is',
					$block,
					$post->post_content,
					1
				);
				$res = wp_update_post( [ 'ID' => $page_id, 'post_content' => wp_slash( $updated ) ], true );
				echo is_wp_error( $res ) ? "ERR $page_id " . $res->get_error_message() . "\n" : "OK $page_id\n";
			}
			break;

		case 'enrich-treatments':
			$treatments = [
				853 => [ // cocaine
					'intro_h' => 'What this treatment involves',
					'intro_p' => '<p>Cocaine and stimulant addiction can damage relationships, careers, and physical health within months — but recovery is achievable in the right setting. At The Diamond Rehab Thailand, our cocaine addiction program combines medically supervised detoxification with intensive one-on-one therapy, group work, and a structured daily routine. We work with executives, professionals, and high-profile clients in complete confidentiality at our private 5-star facility in Hua Hin.</p>',
					'detox'   => 'Cocaine detox typically lasts 5 to 10 days. Acute withdrawal can include intense cravings, depression, anxiety, fatigue, and disrupted sleep. Our 24/7 nursing team and on-site doctors monitor your physical and mental state continuously, and adjust supportive medications as needed to keep withdrawal manageable. Throughout detox you have a private room, a personal therapist, and an evidence-based protocol that prioritizes your safety and dignity.',
					'rehab'   => 'After detox, the core treatment phase runs for 4 to 12+ weeks. You get up to 3 individual therapy sessions per week with a licensed clinical psychologist, daily group therapy, trauma-informed work (EMDR, CBT, somatic), and a personalized relapse-prevention plan. Wellness activities — yoga, fitness, massage, meditation — are integrated daily. The Diamond is fully residential with a 1-to-1 staff-to-client ratio so nothing about your care is outsourced.',
					'after'   => 'Every client leaves with a written aftercare plan and lifetime telehealth check-ins with their primary therapist. You receive structured 6-month follow-up sessions, an alumni community, and emergency on-call support. Where appropriate, we coordinate with local sober-living homes and outpatient clinics in your home country. Our goal is durable, sustained recovery — not just a clean discharge.',
				],
				1219 => [ // all-treatments
					'intro_h' => 'What we treat',
					'intro_p' => '<p>The Diamond Rehab Thailand provides residential treatment for the full spectrum of substance and behavioral addictions — including alcohol, cocaine, methamphetamine, opioids and heroin, prescription medications, cannabis, gambling, and process addictions. Every program is built individually around your medical history, life circumstances, and recovery goals. Our 1-to-1 staff-to-client ratio and 12-client capacity mean care is never standardized — it is always tailored to you.</p>',
					'detox'   => 'Detoxification is the medically supervised phase of clearing the substance from your body. The length depends on the drug and your physiology — alcohol or benzodiazepine detox typically takes 5–7 days, opioid detox 7–10 days, stimulants 5–14 days. Throughout, you stay in a private bungalow with 24/7 nursing, daily physician check-ins, and supportive medications when needed.',
					'rehab'   => 'The rehabilitation phase is where lasting change happens — typically 4 to 12 weeks of intensive psychological work. You work with a primary clinical psychologist on individual sessions, attend group therapy, do trauma work where indicated (EMDR, somatic experiencing), and build daily structure around fitness, nutrition, sleep, and meaningful activity.',
					'after'   => 'Aftercare begins on day one — not at discharge. Every Diamond client leaves with a tailored aftercare plan, lifetime telehealth check-ins with their primary therapist, alumni community access, and 24/7 on-call crisis support. We coordinate with sober-living homes and outpatient providers in your home country whenever helpful.',
				],
				867 => [ // ice / meth
					'intro_h' => 'What this treatment involves',
					'intro_p' => '<p>Methamphetamine and "ice" addiction is among the most physically and psychologically demanding addictions to recover from — but a structured residential setting with intensive medical and psychological care makes long-term recovery realistic. Our meth addiction program at The Diamond Rehab Thailand combines medical stabilization, evidence-based psychotherapy, and lifestyle reconstruction in a private, secure 5-star facility. We accept only 12 clients at a time, so every program is fully tailored to you.</p>',
					'detox'   => 'Meth detox can last 10 to 21 days because withdrawal hits both the brain and the body hard — extreme fatigue, deep depression, anxiety, vivid dreams, and severe cravings. Our 24/7 medical team manages symptoms with carefully selected supportive medications and round-the-clock nursing. You have a private bungalow, full meals from our chef, and immediate access to your therapist whenever cravings or low moods strike.',
					'rehab'   => 'The active rehabilitation phase usually runs 8 to 12+ weeks. Meth recovery requires rebuilding both the dopamine system and the structures of daily life. You receive intensive individual therapy (CBT, motivational interviewing, trauma work), group therapy, structured wellness activities, fitness, nutrition support, and sleep restoration. Family therapy and re-integration planning are built into the second half of the program.',
					'after'   => 'Sustained meth recovery requires long-term support. Every Diamond client receives a written, individualized aftercare plan, lifetime telehealth check-ins, alumni community access, and 24/7 on-call crisis support. Where helpful, we connect you with sober-living, outpatient counselling, and 12-step or SMART recovery groups in your home city. Aftercare is not an add-on — it is part of the program from day one.',
				],
			];
			foreach ( $treatments as $page_id => $copy ) {
				$post = get_post( $page_id );
				if ( ! $post ) { echo "skip $page_id\n"; continue; }
				$content = $post->post_content;

				// Build prose with proper InnerBlocks (core/heading + core/paragraph).
				$h_text = esc_html( $copy['intro_h'] );
				$p_text = trim( strip_tags( $copy['intro_p'], '<a><strong><em>' ) );
				$inner_prose = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">$h_text</h2>\n<!-- /wp:heading -->\n\n" .
					"<!-- wp:paragraph -->\n<p>$p_text</p>\n<!-- /wp:paragraph -->";
				$prose_attrs = wp_json_encode( [
					'background' => 'white',
					'width'      => 'text',
				] );
				$prose_block = '<!-- wp:rehab/prose ' . $prose_attrs . " -->\n" .
					'<section class="wp-block-rehab-prose rehab-prose rehab-bg-white rehab-prose--text"><div class="rehab-container rehab-container--text"><div class="rehab-prose__inner">' . "\n" .
					$inner_prose . "\n" .
					'</div></div></section>' . "\n" .
					"<!-- /wp:rehab/prose -->";
				$content = preg_replace(
					'/<!--\s*wp:rehab\/prose\b.*?<!--\s*\/wp:rehab\/prose\s*-->/is',
					$prose_block,
					$content,
					1
				);

				// Build tabs with proper InnerBlocks (rehab/tab → core/paragraph).
				$mk_tab = function( $label, $body ) {
					$tab_attrs = wp_json_encode( [ 'label' => $label ] );
					$body_p = esc_html( $body );
					return '<!-- wp:rehab/tab ' . $tab_attrs . " -->\n" .
						'<div class="wp-block-rehab-tab rehab-tab" data-label="' . esc_attr( $label ) . '">' . "\n" .
						"<!-- wp:paragraph -->\n<p>$body_p</p>\n<!-- /wp:paragraph -->\n" .
						"</div>\n" .
						"<!-- /wp:rehab/tab -->";
				};
				$tabs_inner = $mk_tab( 'Detox', $copy['detox'] ) . "\n\n" .
					$mk_tab( 'Rehabilitation', $copy['rehab'] ) . "\n\n" .
					$mk_tab( 'Aftercare', $copy['after'] );
				$tabs_attrs = wp_json_encode( [ 'background' => 'white', 'heading' => 'Treatment phases' ] );
				$tabs_block = '<!-- wp:rehab/tabs ' . $tabs_attrs . " -->\n" .
					'<section class="wp-block-rehab-tabs rehab-tabs rehab-bg-white" data-rehab-tabs=""><div class="rehab-container"><h2 class="rehab-heading rehab-heading--lg rehab-tabs__heading">Treatment phases</h2><div class="rehab-tabs__inner">' . "\n" .
					$tabs_inner . "\n" .
					'</div></div></section>' . "\n" .
					"<!-- /wp:rehab/tabs -->";
				$content = preg_replace(
					'/<!--\s*wp:rehab\/tabs\b.*?<!--\s*\/wp:rehab\/tabs\s*-->/is',
					$tabs_block,
					$content,
					1
				);

				$res = wp_update_post( [ 'ID' => $page_id, 'post_content' => wp_slash( $content ) ], true );
				echo is_wp_error( $res ) ? "ERR $page_id " . $res->get_error_message() . "\n" : "OK $page_id\n";
			}
			break;

		case 'list-articles':
			$ids = get_posts( [
				'post_type'      => 'page',
				'posts_per_page' => 5,
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'template-article.php',
				'fields'         => 'ids',
			] );
			foreach ( $ids as $id ) {
				$p = get_post( $id );
				echo $id . "\t" . $p->post_name . "\n";
			}
			break;

		case 'fix-map':
			$embed_url = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3902.586!2d99.96!3d12.553!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sHua+Hin+District%2C+Prachuap+Khiri+Khan%2C+Thailand!5e0!3m2!1sen!2sus!4v1699999999';
			$attrs = wp_json_encode( [
				'background' => 'white',
				'heading'    => 'Find us in Hua Hin',
				'address'    => 'The Diamond Rehab Thailand, 8 Moo 14, Soi Mon Mai Hin Lek Fai, Hua Hin District, Prachuap Khiri Khan, Thailand 77110',
				'embedUrl'   => $embed_url,
			] );
			$iframe = sprintf(
				'<iframe src="%s" title="Find us in Hua Hin" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>',
				esc_url( $embed_url )
			);
			$block = '<!-- wp:rehab/map ' . $attrs . " -->\n" .
				'<section class="wp-block-rehab-map rehab-map rehab-bg-white"><div class="rehab-container"><div class="rehab-map__grid"><div class="rehab-map__info"><h2 class="rehab-heading rehab-heading--lg">Find us in Hua Hin</h2><p class="rehab-map__address">The Diamond Rehab Thailand, 8 Moo 14, Soi Mon Mai Hin Lek Fai, Hua Hin District, Prachuap Khiri Khan, Thailand 77110</p></div><div class="rehab-map__embed">' . $iframe . '</div></div></div></section>' . "\n" .
				"<!-- /wp:rehab/map -->";
			foreach ( [ 6, 1189 ] as $page_id ) {
				$post = get_post( $page_id );
				if ( ! $post ) continue;
				$updated = preg_replace(
					'/<!--\s*wp:rehab\/map\b.*?<!--\s*\/wp:rehab\/map\s*-->/is',
					$block,
					$post->post_content,
					1
				);
				wp_update_post( [ 'ID' => $page_id, 'post_content' => wp_slash( $updated ) ] );
				echo "OK $page_id map\n";
			}
			break;

		case 'rebuild-contact-form':
			$post = get_post( 1189 );
			if ( ! $post ) { echo "no contact page\n"; break; }
			$content = $post->post_content;
			// Strip the broken contact-form block (encoded delimiters + open/close).
			$content = preg_replace(
				'/&lt;!--\s*wp:rehab\/contact-form.*?<!--\s*\/wp:rehab\/contact-form\s*-->\s*/is',
				'',
				$content
			);
			$content = preg_replace(
				'/<!--\s*wp:rehab\/contact-form.*?<!--\s*\/wp:rehab\/contact-form\s*-->\s*/is',
				'',
				$content
			);
			$attrs = wp_json_encode( [
				'background' => 'cream',
				'heading'    => 'Contact our admissions team',
				'subheading' => 'Free, confidential, no obligation. We respond within an hour, 24/7.',
				'shortcode'  => '',
			] );
			$block = '<!-- wp:rehab/contact-form ' . $attrs . " -->\n" .
				'<section class="wp-block-rehab-contact-form rehab-contact-form rehab-bg-cream"><div class="rehab-container rehab-container--narrow"><header class="rehab-contact-form__header"><h2 class="rehab-heading rehab-heading--lg">Contact our admissions team</h2><p class="rehab-contact-form__subheading">Free, confidential, no obligation. We respond within an hour, 24/7.</p></header><div class="rehab-contact-form__embed"></div></div></section>' . "\n" .
				"<!-- /wp:rehab/contact-form -->\n\n";
			$content = preg_replace( '/(<!--\s*wp:rehab\/map\b)/', $block . '$1', $content, 1 );
			// Use slashed since wp_update_post unslashes
			$res = wp_update_post( [ 'ID' => 1189, 'post_content' => wp_slash( $content ) ], true );
			if ( is_wp_error( $res ) ) {
				echo 'ERR ' . $res->get_error_message() . "\n";
			} else {
				echo "OK rebuilt\n";
			}
			break;

		case 'restore-contact-form':
			$post = get_post( 1189 );
			if ( ! $post ) { echo "no contact page\n"; break; }
			// Build the contact form block freshly.
			$embed_html = '<div class="rehab-contact-fallback"><p class="rehab-contact-fallback__email">Email <a href="mailto:info@diamondrehabthailand.com">info@diamondrehabthailand.com</a></p><p class="rehab-contact-fallback__sub">Or call our admissions team — 24/7</p><a class="rehab-btn rehab-btn--luxury" href="tel:+66965823832">+66 96 582 3832</a></div>';
			$attrs = wp_json_encode( [
				'heading'    => 'Contact our admissions team',
				'subheading' => 'Free, confidential, no obligation. We respond within an hour, 24/7.',
				'shortcode'  => $embed_html,
			] );
			$block = sprintf(
				'<!-- wp:rehab/contact-form %s -->' . "\n" .
				'<section class="wp-block-rehab-contact-form rehab-contact-form rehab-bg-cream"><div class="rehab-container rehab-container--narrow"><header class="rehab-contact-form__header"><h2 class="rehab-heading rehab-heading--lg">Contact our admissions team</h2><p class="rehab-contact-form__subheading">Free, confidential, no obligation. We respond within an hour, 24/7.</p></header><div class="rehab-contact-form__embed">%s</div></div></section>' . "\n" .
				'<!-- /wp:rehab/contact-form -->' . "\n\n",
				$attrs,
				$embed_html
			);
			// Insert right before the rehab/map block (so order is form → map → phone-cta).
			$content = $post->post_content;
			if ( strpos( $content, 'wp:rehab/contact-form' ) !== false ) {
				echo "already present\n"; break;
			}
			$content = preg_replace( '/(<!--\s*wp:rehab\/map\b)/', $block . '$1', $content, 1 );
			wp_update_post( [ 'ID' => 1189, 'post_content' => $content ] );
			echo "OK contact form restored\n";
			break;

		case 'fix-alt-text':
			// Repair literal "u0026amp;" / "u0026" in team-member alt text in homepage post 6.
			$post = get_post( 6 );
			if ( ! $post ) { echo "no post 6\n"; break; }
			$content = $post->post_content;
			$replacements = [
				'u0026amp;' => '&amp;',
				'u0026'     => '&amp;',
				'u003cbr'   => '<br',
				'u003c'     => '<',
				'u003e'     => '>',
			];
			$count = 0;
			foreach ( $replacements as $bad => $good ) {
				$tmp = str_replace( $bad, $good, $content, $c );
				$content = $tmp;
				$count += $c;
			}
			if ( $count > 0 ) {
				wp_update_post( [ 'ID' => 6, 'post_content' => wp_slash( $content ) ] );
			}
			echo "OK fixed $count occurrences in post 6\n";
			break;

		case 'dump-cocaine':
			$p = get_post( 853 );
			echo $p->post_content;
			break;

		case 'dump-home-hero':
			$p = get_post( 6 );
			$c = $p->post_content;
			$pos = strpos( $c, 'wp:rehab/hero' );
			echo substr( $c, max( 0, $pos - 5 ), 800 );
			break;

		case 'add-treatment-hero':
			// JSON_HEX_TAG encodes < and > as < and > so they don't break HTML comment parsing.
			// We must wp_slash() the post_content before wp_update_post so the backslash survives wp_unslash().
			$je = function( $arr ) { return wp_json_encode( $arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ); };
			$base = get_stylesheet_directory_uri(); // child theme URI
			$pages = [
				853 => [
					'eyebrow'  => 'Cocaine & stimulant addiction',
					'headline' => 'Cocaine addiction<br>treatment',
					'body'     => 'Doctor-led residential rehab for cocaine and stimulant addiction at our private 5-star sanctuary in Hua Hin, Thailand. Tailored to your circumstances, with absolute confidentiality.',
					'image'    => '/assets/img/cards/cocaine-addiction-treatment.avif',
					'alt'      => 'Quiet beachside sanctuary at The Diamond Rehab Thailand',
					't1'       => 'Detox supervised by 24/7 medical team',
					't2'       => 'Maximum 12 clients at a time',
					't3'       => 'NDAs with all staff, alias accepted',
				],
				867 => [
					'eyebrow'  => 'Methamphetamine & ice addiction',
					'headline' => 'Meth and ice addiction<br>treatment',
					'body'     => 'Intensive residential rehabilitation for methamphetamine addiction. 24/7 medical care, structured therapy, and full lifestyle reconstruction in a private, secure 5-star setting.',
					'image'    => '/assets/img/cards/drug-addiction-treatment.avif',
					'alt'      => 'Tranquil treatment grounds at The Diamond Rehab Thailand',
					't1'       => 'Doctor-led detox and psychiatric care',
					't2'       => 'Extended programs (60–90 days)',
					't3'       => 'Discreet, executive-level privacy',
				],
				1219 => [
					'eyebrow'  => 'Comprehensive addiction care',
					'headline' => 'All treatments',
					'body'     => 'We treat the full spectrum of substance and behavioural addictions: alcohol, cocaine, methamphetamine, opioids, prescription medications, cannabis, gambling, and process addictions. Every program is built individually around you.',
					'image'    => '/assets/img/hero/pool-pavilion.avif',
					'alt'      => 'The pool pavilion at The Diamond Rehab Thailand',
					't1'       => '1-to-1 staff-to-client ratio',
					't2'       => 'Dual-diagnosis specialists',
					't3'       => 'Lifetime aftercare included',
				],
			];

			foreach ( $pages as $pid => $cfg ) {
				$post = get_post( $pid );
				if ( ! $post ) { echo "skip $pid\n"; continue; }

				$image_url = $base . $cfg['image'];
				$attrs = $je( [
					'eyebrow'      => $cfg['eyebrow'],
					'headline'     => $cfg['headline'],
					'body'         => $cfg['body'],
					'buttonText'   => 'Speak with admissions',
					'buttonUrl'    => '/contact-us/',
					'buttonHelper' => 'Free, confidential, no-obligation.',
					'trustItem1'   => $cfg['t1'],
					'trustItem2'   => $cfg['t2'],
					'trustItem3'   => $cfg['t3'],
					'imageUrl'     => $image_url,
					'imageAlt'     => $cfg['alt'],
					'showDeco'     => true,
				] );

				// Build rendered HTML matching save.js output.
				$trust_html = '';
				foreach ( [ $cfg['t1'], $cfg['t2'], $cfg['t3'] ] as $item ) {
					$trust_html .= '<div class="rehab-hero__trust-item"><span class="rehab-hero__diamond" aria-hidden="true">◆</span>' . esc_html( $item ) . '</div>';
				}

				$hero_html =
					'<section class="wp-block-rehab-hero rehab-hero" aria-label="Hero">' .
					'<div class="rehab-hero__container"><div class="rehab-hero__grid">' .
						'<div class="rehab-hero__content">' .
							'<h1 class="rehab-hero__h1">' .
								'<span class="rehab-hero__eyebrow">' . esc_html( $cfg['eyebrow'] ) . '</span>' .
								'<span class="rehab-hero__headline">' . wp_kses( $cfg['headline'], [ 'br' => [] ] ) . '</span>' .
							'</h1>' .
							'<p class="rehab-hero__body">' . esc_html( $cfg['body'] ) . '</p>' .
							'<div class="rehab-hero__cta">' .
								'<a class="rehab-btn rehab-btn--luxury" href="/contact-us/">Speak with admissions</a>' .
								'<p class="rehab-hero__cta-helper">Free, confidential, no-obligation.</p>' .
							'</div>' .
							'<div class="rehab-hero__trust">' . $trust_html . '</div>' .
						'</div>' .
						'<div class="rehab-hero__media">' .
							'<div class="rehab-hero__image-wrap">' .
								'<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $cfg['alt'] ) . '" class="rehab-hero__image" loading="eager" decoding="async"/>' .
								'<div class="rehab-hero__overlay" aria-hidden="true"></div>' .
							'</div>' .
							'<div class="rehab-hero__deco" aria-hidden="true"></div>' .
						'</div>' .
					'</div></div>' .
					'</section>';

				$hero_block =
					'<!-- wp:rehab/hero ' . $attrs . " -->\n" .
					$hero_html . "\n" .
					"<!-- /wp:rehab/hero -->";

				// Replace the existing leading rehab/cta (the placeholder hero) with this rehab/hero.
				$content = $post->post_content;
				// Strip ALL existing rehab/hero blocks (rendered + comments + any encoded-broken variants).
				// Loop: keep stripping the longest match between any hero-open and any hero-close.
				$prev = null;
				while ( $prev !== $content ) {
					$prev = $content;
					$content = preg_replace( '/(?:<!--|&lt;!--)\s*wp:rehab\/hero\b.*?(?:<!--|&lt;!--)\s*\/wp:rehab\/hero\s*(?:-->|--&gt;)\s*/is', '', $content, 1 );
				}
				// Strip any orphan rendered <section class="...rehab-hero..."> blocks.
				$content = preg_replace( '/<section\s+[^>]*class="[^"]*\brehab-hero\b[^"]*"[^>]*>.*?<\/section>\s*/is', '', $content );
				$pattern = '/^\s*<!--\s*wp:rehab\/cta\b.*?<!--\s*\/wp:rehab\/cta\s*-->/is';
				if ( preg_match( $pattern, $content ) ) {
					$content = preg_replace( $pattern, $hero_block, $content, 1 );
				} else {
					$content = $hero_block . "\n\n" . $content;
				}
				wp_update_post( [ 'ID' => $pid, 'post_content' => wp_slash( $content ) ] );
				echo "OK $pid hero replaced\n";
			}
			break;

		case 'replace-treatment-faq':
			$je = function( $arr ) { return wp_json_encode( $arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); };
			$pages = [
				853 => [
					'title' => 'Cocaine addiction — frequently asked',
					'qs' => [
						[ 'q' => 'How long does it take to detox from cocaine?', 'a' => 'Acute cocaine withdrawal usually peaks within 3 to 5 days, with the most intense cravings, fatigue, depression and disrupted sleep clearing within the first 7 to 10 days. Our 24/7 medical team manages the detox so it stays comfortable and safe, and a tailored therapy program begins as soon as you are stable.' ],
						[ 'q' => 'Will I be prescribed medication during cocaine treatment?', 'a' => 'Cocaine has no FDA-approved replacement medication, but our doctor-led team can prescribe targeted support for sleep, anxiety, depression, and any co-occurring conditions identified at intake. Every prescription is reviewed daily by our resident psychiatrist.' ],
						[ 'q' => 'Do you treat cocaine addiction alongside alcohol or other substances?', 'a' => 'Yes. Polysubstance use is common with stimulant addiction, and our program is built around dual-diagnosis care. We treat cocaine alongside alcohol, benzodiazepines, opioids and any underlying mental-health condition in one integrated plan.' ],
						[ 'q' => 'How discreet is treatment for executives and public figures?', 'a' => 'Maximum 12 clients at a time, NDAs with every staff member, alias check-in, private accommodations, and a secluded oceanfront facility in Hua Hin. We routinely accommodate executives, founders, and public figures who require absolute confidentiality.' ],
					],
				],
				867 => [
					'title' => 'Meth and ice addiction — frequently asked',
					'qs' => [
						[ 'q' => 'Is meth withdrawal dangerous?', 'a' => 'Withdrawal from methamphetamine is rarely life-threatening on its own, but it can be intensely uncomfortable — severe fatigue, depression, anxiety, hallucinations, and powerful cravings are common. Our 24/7 medical team manages the crash phase with sleep support, nutritional rebuilding and psychiatric monitoring so it stays safe and bearable.' ],
						[ 'q' => 'How long does meth stay in the system?', 'a' => 'Acute withdrawal generally lasts 7 to 14 days, but cognitive symptoms — flat mood, foggy thinking, anhedonia — can persist for 6 to 12 weeks. Our extended residential program is designed around this longer recovery curve, with therapy and lifestyle reconstruction continuing well past the physical detox.' ],
						[ 'q' => 'Can the brain recover from long-term meth use?', 'a' => 'Yes. Research shows dopamine receptor density and cognitive function recover significantly over 12 to 18 months of abstinence, supported by sleep, nutrition, exercise and structured therapy. Every aspect of our program — diet, fitness, neurofeedback, mindfulness — is built around this neural recovery.' ],
						[ 'q' => 'Do you treat meth-induced psychosis or paranoia?', 'a' => 'Yes. Stimulant-induced psychosis is treated by our resident psychiatrist with appropriate antipsychotic medication, a calm low-stimulus environment, and ongoing assessment. Most clients see symptoms resolve within days of admission.' ],
					],
				],
				1219 => [
					'title' => 'About our treatment programs',
					'qs' => [
						[ 'q' => 'What addictions do you treat?', 'a' => 'We treat the full spectrum of substance and behavioral addictions: alcohol, cocaine, methamphetamine, opioids and heroin, prescription medications, cannabis, gambling, sex and process addictions. Every program is built individually around your medical history and recovery goals.' ],
						[ 'q' => 'Do you offer dual-diagnosis treatment?', 'a' => 'Yes. Most addictions co-occur with depression, anxiety, PTSD or other mental-health conditions, and our resident psychiatrist diagnoses and treats them alongside the addiction. Therapy, medication and lifestyle work are coordinated under one plan.' ],
						[ 'q' => 'How long is a typical program?', 'a' => 'Most clients stay 28, 60 or 90 days depending on the substance, severity and life circumstances. Stimulant and opioid recoveries typically benefit from longer stays; alcohol-only programs can sometimes complete within 28 days. We tailor the length at admission and reassess weekly.' ],
						[ 'q' => 'What therapies will I receive?', 'a' => 'Daily one-on-one psychotherapy, group therapy, CBT, DBT, trauma-informed care, EMDR where indicated, mindfulness, yoga, fitness training, nutritional rebuilding and family work. Every clinician is internationally trained.' ],
					],
				],
			];

			foreach ( $pages as $pid => $cfg ) {
				$post = get_post( $pid );
				if ( ! $post ) { echo "skip $pid\n"; continue; }

				$attrs_faq = $je( [ 'heading' => $cfg['title'] ] );
				$inner = '';
				foreach ( $cfg['qs'] as $row ) {
					$item_attrs = $je( [ 'question' => $row['q'], 'answer' => $row['a'] ] );
					$inner .=
						'<!-- wp:rehab/faq-item ' . $item_attrs . " -->\n" .
						'<details class="wp-block-rehab-faq-item rehab-faq-item"><summary>' . esc_html( $row['q'] ) . '</summary><div class="rehab-faq-item__answer">' . wp_kses_post( $row['a'] ) . "</div></details>\n" .
						"<!-- /wp:rehab/faq-item -->\n\n";
				}

				$new_block =
					'<!-- wp:rehab/faq ' . $attrs_faq . " -->\n" .
					'<section class="wp-block-rehab-faq rehab-faq"><div class="rehab-container rehab-container--narrow"><h2 class="rehab-heading rehab-heading--lg">' . esc_html( $cfg['title'] ) . "</h2><div class=\"rehab-faq__list\">\n" .
					$inner .
					"</div></div></section>\n" .
					"<!-- /wp:rehab/faq -->";

				$content = $post->post_content;
				$pattern = '/<!--\s*wp:rehab\/faq\b.*?<!--\s*\/wp:rehab\/faq\s*-->/is';
				if ( preg_match( $pattern, $content ) ) {
					$content = preg_replace( $pattern, $new_block, $content, 1 );
				} else {
					// Append before closing CTA if no FAQ exists.
					$content .= "\n\n" . $new_block;
				}
				wp_update_post( [ 'ID' => $pid, 'post_content' => $content ] );
				echo "OK $pid faq replaced (" . count( $cfg['qs'] ) . " questions)\n";
			}
			break;

		case 'dump-faq':
			$post = get_post( 1197 );
			if ( ! $post ) { echo "no faq page\n"; break; }
			echo $post->post_content;
			break;

		case 'find-faq-json':
			$paths = [
				ABSPATH,
				WP_CONTENT_DIR,
				WP_CONTENT_DIR . '/..',
				WP_CONTENT_DIR . '/../..',
				WP_CONTENT_DIR . '/../../..',
				'/var/www/html',
			];
			foreach ( $paths as $p ) {
				$rp = realpath( $p );
				if ( ! $rp ) continue;
				$found = glob( $rp . '/diamond-*.json' );
				if ( $found ) echo $rp . " => \n  " . implode( "\n  ", $found ) . "\n";
			}
			break;

		case 'media-mirror':
			global $wpdb;
			@set_time_limit( 0 );
			@ignore_user_abort( true );
			$source = isset( $_GET['source'] ) ? esc_url_raw( $_GET['source'] ) : 'https://diamondrehabthailand.com';
			$dry    = ! empty( $_GET['dry'] );
			$limit  = isset( $_GET['limit'] ) ? max( 0, (int) $_GET['limit'] ) : 0;
			$verbose = ! empty( $_GET['verbose'] );

			// Collect every /wp-content/uploads/... path referenced anywhere.
			$paths = [];
			$collect = function ( $haystack ) use ( &$paths ) {
				if ( ! is_string( $haystack ) || $haystack === '' ) return;
				if ( preg_match_all( '#/wp-content/uploads/([A-Za-z0-9_/.\-]+\.(?:png|jpe?g|gif|webp|svg|avif|ico))#', $haystack, $m ) ) {
					foreach ( $m[1] as $p ) $paths[ $p ] = true;
				}
			};
			foreach ( $wpdb->get_col( "SELECT post_content FROM {$wpdb->posts}" ) as $v ) $collect( $v );
			foreach ( $wpdb->get_col( "SELECT post_excerpt FROM {$wpdb->posts}" ) as $v ) $collect( $v );
			foreach ( $wpdb->get_col( "SELECT guid FROM {$wpdb->posts}" ) as $v ) $collect( $v );
			foreach ( $wpdb->get_col( "SELECT meta_value FROM {$wpdb->postmeta}" ) as $v ) $collect( $v );
			foreach ( $wpdb->get_col( "SELECT option_value FROM {$wpdb->options} WHERE option_value LIKE '%wp-content/uploads/%'" ) as $v ) $collect( $v );

			$paths = array_keys( $paths );
			sort( $paths );

			// Optional year filter — pre-2022 paths are typically plugin docs that
			// don't exist on the live Diamond domain; default to 2022+.
			$min_year = isset( $_GET['min_year'] ) ? (int) $_GET['min_year'] : 2022;
			if ( $min_year > 0 ) {
				$paths = array_values( array_filter( $paths, function ( $p ) use ( $min_year ) {
					return preg_match( '#^(\d{4})/#', $p, $m ) && (int) $m[1] >= $min_year;
				} ) );
			}

			$uploads_base = wp_get_upload_dir();
			$basedir      = $uploads_base['basedir']; // /var/www/html/wp-content/uploads

			$missing = [];
			foreach ( $paths as $rel ) {
				$abs = $basedir . '/' . $rel;
				if ( ! file_exists( $abs ) ) $missing[] = $rel;
			}

			echo "Found " . count( $paths ) . " upload paths referenced; " . count( $missing ) . " missing on disk.\n";
			if ( $dry ) {
				echo "(dry run — not downloading)\n";
				$summary = isset( $_GET['summary'] );
				if ( $summary ) {
					$by_year = [];
					foreach ( $missing as $rel ) {
						$y = substr( $rel, 0, 4 );
						$by_year[ $y ] = ( $by_year[ $y ] ?? 0 ) + 1;
					}
					ksort( $by_year );
					foreach ( $by_year as $y => $n ) echo "  $y  $n\n";
				} else {
					foreach ( $missing as $rel ) echo "  $rel\n";
				}
				break;
			}

			if ( $limit > 0 ) $missing = array_slice( $missing, 0, $limit );

			$ok = 0; $fail = 0;
			foreach ( $missing as $rel ) {
				$url = rtrim( $source, '/' ) . '/wp-content/uploads/' . $rel;
				$abs = $basedir . '/' . $rel;
				$dir = dirname( $abs );
				if ( ! is_dir( $dir ) ) wp_mkdir_p( $dir );

				$resp = wp_remote_get( $url, [ 'timeout' => 8, 'redirection' => 3 ] );
				if ( is_wp_error( $resp ) || wp_remote_retrieve_response_code( $resp ) !== 200 ) {
					$code = is_wp_error( $resp ) ? 'WPERR' : wp_remote_retrieve_response_code( $resp );
					if ( $verbose ) echo "  FAIL $code  $rel\n";
					$fail++;
					continue;
				}
				$body = wp_remote_retrieve_body( $resp );
				if ( $body === '' || file_put_contents( $abs, $body ) === false ) {
					if ( $verbose ) echo "  WRITE-FAIL  $rel\n";
					$fail++;
					continue;
				}
				$ok++;
			}
			echo "OK media-mirror — downloaded $ok, failed $fail\n";
			break;

		case 'find-block':
			global $wpdb;
			$needle = isset( $_GET['needle'] ) ? sanitize_text_field( $_GET['needle'] ) : 'wp:rehab/comparison';
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type IN ('page','post') AND post_status='publish' AND post_content LIKE %s ORDER BY post_title",
				'%' . $wpdb->esc_like( $needle ) . '%'
			) );
			foreach ( $rows as $r ) echo $r->ID . "\t" . $r->post_title . "\n";
			echo "(" . count( $rows ) . " pages)\n";
			break;

		case 'rebuild-cocaine-design':
			$page_id = 853;
			$post = get_post( $page_id );
			if ( ! $post ) { echo "no post 853\n"; break; }

			$base = '/wp-content/uploads/';
			$theme = get_stylesheet_directory_uri();
			$blocks = '';

			// 1. TREATMENT HERO
			$blocks .= rehab_block_treatment_hero( [
				'eyebrow'  => 'Cocaine addiction treatment · Hua Hin, Thailand',
				'headline' => 'Effective cocaine addiction treatment at Thailand\'s leading luxury centre',
				'lede'     => 'The Diamond Rehab Thailand provides evidence-based cocaine rehab treatment to help you break the cycle of addiction once and for all.',
				'primaryText' => 'Schedule a free assessment', 'primaryUrl' => '#assessment',
				'secondaryText' => 'Explore the program', 'secondaryUrl' => '#program',
				'helper' => 'Free, confidential, no-obligation · Replies within 1 hour',
				'stat1Num' => '12', 'stat1Label' => 'Maximum clients on site at any time',
				'stat2Num' => '24/7', 'stat2Label' => 'Doctor & clinical team availability',
				'stat3Num' => '14+', 'stat3Label' => 'Years treating cocaine addiction',
				'imageUrl' => $theme . '/assets/img/treatment/hero-pool-pavilion.avif',
				'imageAlt' => 'Luxury private pavilion at The Diamond Rehab Thailand',
				'badgeImageUrl' => $theme . '/assets/img/treatment/ministry-public-health-badge.webp',
				'badgeTitle' => 'Thai-licensed facility',
				'badgeText' => 'Ministry of Public Health · Hospital-affiliated detox',
			] );

			// 2. AUTHORITY RIBBON
			$blocks .= rehab_block_authority_ribbon( 'As featured in', [
				[ 'url' => $theme . '/assets/img/treatment/business-insider.png', 'alt' => 'Business Insider' ],
				[ 'url' => $theme . '/assets/img/treatment/yahoo-finance.png', 'alt' => 'Yahoo Finance' ],
				[ 'url' => $theme . '/assets/img/treatment/well-good.png', 'alt' => 'Well + Good' ],
				[ 'url' => $theme . '/assets/img/treatment/psych-central.png', 'alt' => 'Psych Central' ],
				[ 'url' => $theme . '/assets/img/treatment/recovery-com.webp', 'alt' => 'Recovery.com' ],
				[ 'url' => $theme . '/assets/img/treatment/bangkok-hospital.png', 'alt' => 'Bangkok Hospital partner' ],
			] );

			// 3. INTRO + DOCTOR CARD
			$blocks .= rehab_block_intro_doctor_card( [
				'background' => 'white',
				'eyebrow' => 'Overcome cocaine addiction in Thailand',
				'heading' => 'This is where life-changing transformations happen.',
				'body' => "Nestled in the peaceful mountains of tropical Hua Hin, our five-star cocaine rehab centre is the perfect place to start your treatment and recovery journey. Fully equipped facilities, five-star accommodation and world-class addiction experts — we've assembled everything you need to overcome your cocaine addiction. The only thing missing is you.\n\nContact us today to learn more about our admission process, or read on to find out how our cocaine rehab treatment program can help you kickstart your recovery.",
				'doctorImageUrl' => $theme . '/assets/img/treatment/founder-theo.avif',
				'doctorImageAlt' => 'Theo de Vries, Founder',
				'doctorLabel' => 'Speak with our Director',
				'doctorName' => 'Theo de Vries',
				'doctorPhone' => '+66 3 313 5303',
			] );

			// 4. PILLARS — Why Diamond
			$blocks .= rehab_block_pillars(
				'Why Diamond Rehab',
				'Three reasons families choose us for cocaine recovery',
				'Discreet, doctor-led and deeply personal — every aspect of our program is designed for sustainable, long-term sobriety.',
				[
					[ 'num' => '01 — Holistic & evidence-based', 'title' => 'Eastern and Western therapy, combined', 'body' => 'Our highly experienced clinicians use a combination of Eastern and Western therapeutic techniques — addressing both the symptoms of cocaine use and the underlying factors that fuel it: trauma, relationships, past experiences and mental health.' ],
					[ 'num' => '02 — Personalised, never templated', 'title' => 'A program shaped to your condition', 'body' => 'There\'s no silver bullet for cocaine addiction. We provide fully customised cocaine rehab programs based on a clinical assessment, monitor your progress continuously and adjust your recovery plan as you advance.' ],
					[ 'num' => '03 — Supported around the clock', 'title' => 'Available 24/7 — when cravings hit hardest', 'body' => 'Our clinical team is fully committed to providing the support, expertise and guidance required to achieve a positive outcome. Inpatient care reduces the risk of relapse during the most critical first weeks of withdrawal.' ],
				],
				'sage-mist'
			);

			// 5. ARTICLE: COCAINE TREATMENT (image right)
			$blocks .= rehab_block_article_row( [
				'background' => 'white',
				'imageSide' => 'right', 'imageAspect' => 'tall',
				'imageUrl' => $base . '2024/05/1-1-session-room-1.jpg',
				'imageAlt' => '1-on-1 session room',
				'eyebrow' => 'Cocaine addiction treatment',
				'heading' => 'A holistic approach to a complex addiction',
				'body' => "Cocaine rehab statistics show that professional intervention is the most effective treatment for cocaine addiction. Here at The Diamond Rehab Thailand, we take a holistic approach to treatment that addresses both the symptoms of cocaine use and the underlying factors — previous experiences, relationships, mental health issues, past trauma, and more — that contribute to addiction.\n\nOur highly experienced clinicians use a combination of Eastern and Western therapeutic techniques to effectively rehab cocaine addiction. Available 24/7, our clinical team is fully committed to providing you with the support, expertise, and guidance required to achieve a positive outcome.",
			] );

			// 6. ARTICLE: PERSONALISED (image left, wide aspect, sage bg)
			$blocks .= rehab_block_article_row( [
				'background' => 'sage-mist',
				'imageSide' => 'left', 'imageAspect' => 'wide',
				'imageUrl' => $base . '2024/05/Closer-up-dining-2.jpg',
				'imageAlt' => 'Dining pavilion',
				'eyebrow' => 'Personalised program options',
				'heading' => 'No two recovery plans look the same',
				'body' => "As one of the leading cocaine rehab centers in Thailand, we understand that there's no silver bullet when it comes to treating cocaine addiction and achieving long-term sobriety.\n\nThat's why we provide fully customised cocaine rehab programs based on a clinical assessment of your condition. During your stay in our luxury cocaine rehab facilities, we'll continuously monitor your progress and adjust your recovery plan as you advance through your program to ensure your treatment is as effective as possible.",
			] );

			// 6b. TREATMENT PHASES TABS
			$blocks .= rehab_block_treatment_phases(
				'The treatment phases',
				'Three pillars of cocaine recovery',
				'From the medical detox through to the holistic work that supports long-term sobriety — every phase is supervised by our multi-disciplinary team.',
				[
					[
						'phase' => 'PHASE 01', 'label' => 'Medical detox',
						'h3' => 'Detox: an important part of the cocaine rehab process',
						'paragraphs' => [
							'Depending on the severity of your addiction, our clinical team may recommend starting your coke rehab treatment program with a detox. We\'ll provide you with a safe environment and monitor your symptoms to make the process as comfortable as possible.',
							'During the detoxification process, you may experience a range of cocaine withdrawal symptoms, including:',
							'During withdrawal, the cravings for cocaine can be extremely intense. Entering inpatient care at a cocaine rehab center reduces the risk of relapse during this critical time. The detoxification process for cocaine is relatively quick compared to other drugs, but some symptoms may persist for weeks or months after completing your cocaine rehab programme.',
						],
						'listItems' => [ 'Strong cravings for cocaine', 'Depression', 'Suicidal thoughts', 'Restlessness', 'Lethargy', 'Nightmares' ],
						'asideQuote' => '"The first question is not why the addiction — it\'s why the pain?"',
						'asideMetaLabel' => 'Quoted by',
						'asideMetaValue' => 'Theo de Vries, Founder',
					],
					[
						'phase' => 'PHASE 02', 'label' => 'Behavioural therapy',
						'h3' => 'The role of behavioural therapy in rehab for cocaine addiction',
						'paragraphs' => [
							'Addiction is so much more than a physical dependence on drugs or alcohol. Many people with substance abuse and addiction disorders have deep-rooted psychological and emotional issues that must be addressed in order to achieve lasting wellness.',
							'Therapy provides a crucial support system for people recovering from substance use disorders and is an important component of our substance abuse rehabilitation programs. We offer an intimate, judgment-free space where you can speak honestly about your past and ambitions for the future.',
							'You\'ll learn to identify the personal triggers — stressors, environmental cues and social circles — that lead to relapse and, together with your therapist, develop a recovery plan to manage these triggers in the short and long term. Education and awareness give rise to positive change.',
						],
						'listItems' => [],
						'asideQuote' => '"During rehab, our counsellors will help you dissect the behavioural issues and psychosocial factors that contribute to addiction."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab clinical team',
					],
					[
						'phase' => 'PHASE 03', 'label' => 'Holistic therapy',
						'h3' => 'Holistic therapy used in the treatment of cocaine addiction',
						'paragraphs' => [
							'We believe that successful cocaine addiction rehab relies on addressing every aspect of your health and wellbeing. Our rehab for cocaine addiction includes a range of holistic therapeutic techniques that are effective in the treatment of cocaine addiction.',
						],
						'listItems' => [
							'<strong>Yoga</strong> — Strengthen the bond between mind and body. A combination of physical exercise, breath control and mindfulness, suitable for any level of fitness.',
							'<strong>Mindfulness</strong> — Steeped in Buddhist culture, Thailand is the perfect place to learn how to be more present, live with intent and process negative thoughts.',
							'<strong>Fitness</strong> — Whether you want to swim, run, box, cycle or lift weights, our trainers will guide you through a tailored program that reduces the risk of relapse.',
						],
						'asideQuote' => '"Holistic approach for successful cocaine addiction recovery."',
						'asideMetaLabel' => 'Set within',
						'asideMetaValue' => 'A private 5-star sanctuary in Hua Hin',
					],
				],
				'white'
			);

			// 7. SIGNS / WITHDRAWAL grid + dark CTA
			$blocks .= rehab_block_signs_grid( [
				'background' => 'cream',
				'eyebrow' => 'Is it time to consider rehab?',
				'heading' => 'Recognise the signs of cocaine addiction',
				'subheading' => 'Cocaine is notoriously addictive due to the profound impact it has on the chemistry of your brain. Acknowledging the signs is the first step towards recovery — for yourself or for someone you love.',
				'card1Title' => 'Common signs of cocaine addiction',
				'card1Items' => [ 'Nervousness and restlessness', 'Severe weight loss', 'Sexual dysfunction', 'Depression', 'Frequent nightmares', 'Decreased ability to focus', 'Increased or involuntary movements' ],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [ 'Strong cravings for cocaine', 'Depression', 'Suicidal thoughts', 'Restlessness', 'Lethargy', 'Nightmares' ],
				'showCta' => true,
				'ctaTitle' => 'If any of this feels familiar, please reach out.',
				'ctaBody' => 'Our intake team will speak with you confidentially — no commitment, no pressure. Call us, message us on WhatsApp, or book a free assessment.',
				'ctaButton' => 'Book free assessment', 'ctaUrl' => '/contact-us/',
			] );

			// 8. INPATIENT BENEFITS — article-row + 4-up numbered grid
			$blocks .= rehab_block_article_row( [
				'background' => 'white',
				'imageSide' => 'right', 'imageAspect' => 'wide',
				'imageUrl' => $base . '2024/05/Close-up-chairs-3.jpg',
				'imageAlt' => 'Treatment grounds',
				'eyebrow' => 'The advantage of inpatient care',
				'heading' => 'Why families choose inpatient treatment for cocaine addiction',
				'body' => "The Diamond Rehab Thailand is an inpatient treatment center. Inpatient rehabilitation is widely regarded as the most effective form of cocaine addiction treatment because it allows you to break away from your daily routines and the social triggers that fuel substance use disorders.\n\nLocated in a peaceful part of Hua Hin, our facility is worlds away from the distractions of everyday life — so you can focus all your time and energy on recovery.",
			] );
			$blocks .= rehab_block_benefits_numbered( [
				[ 'title' => 'Distance from triggers', 'body' => 'Isolating from your usual lifestyle and social circles eliminates the risk of giving in to cravings during the most fragile early weeks.' ],
				[ 'title' => 'Round-the-clock supervision', 'body' => 'Resort-style amenities backed by a full team of qualified medical professionals with extensive experience treating cocaine drug rehab.' ],
				[ 'title' => 'Custom-built treatment plan', 'body' => 'Our addiction experts guide you through the crucial first weeks of a custom-made program tailored to your specific clinical picture.' ],
				[ 'title' => 'A genuine therapeutic community', 'body' => 'With a hard cap of 12 clients, you receive deeper attention and form trusted connections that support your long-term recovery.' ],
			] );

			// 9. ADMISSION JOURNEY (4-step)
			$blocks .= rehab_block_journey_steps(
				'Your next step',
				'What happens when you reach out',
				'From the first confidential call to your arrival in Hua Hin — here\'s what you can expect in your first week with The Diamond Rehab.',
				[
					[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => 'A free, no-obligation consultation with our intake team. We listen, answer questions, and take the time to understand your situation.' ],
					[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'Our psychiatrist evaluates the severity of the addiction, mental-health needs, and recommends a length of stay that fits your case.' ],
					[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection, settle you into private accommodation, and walk you through the next 28 days of structured care.' ],
					[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'Detox if required, then your bespoke program — therapy, holistic work, fitness, and continuous adjustment of your recovery plan.' ],
				],
				'sage-mist'
			);

			// 10. CLOSING ARTICLE (split-reverse) + dual CTA
			$blocks .= rehab_block_article_row( [
				'background' => 'white',
				'imageSide' => 'left', 'imageAspect' => 'tall',
				'imageUrl' => $base . '2024/05/Bungalow-evening-2.jpg',
				'imageAlt' => 'Quiet reading area',
				'eyebrow' => 'Take the next step',
				'heading' => "You've already done the hardest part — recognising it",
				'body' => "Recovering from cocaine addiction is hard work — but our exceptional cocaine rehab success rate proves that it's possible. Sometimes, all you need is a helping hand.\n\nIf you're ready to seek treatment, The Diamond Rehab Thailand is here to help. Drawing on our extensive experience as addiction experts, we'll guide you through a fully tailored treatment plan that sets the foundation for a healthy, positive and fulfilling life.",
				'primaryText' => 'Schedule free assessment', 'primaryUrl' => '#assessment',
				'secondaryText' => 'WhatsApp us', 'secondaryUrl' => 'https://wa.me/66965823832',
			] );

			// 11. FAQ — pulled from FAQ CPT records (Phase 3)
			$blocks .= rehab_block_faq(
				'Frequently asked questions',
				[
					[ 'cptId' => 32 ],   // What is the process of rehabilitation?
					[ 'cptId' => 3435 ], // Are 28 days of rehab enough?
					[ 'cptId' => 204 ],  // Can clients leave the rehab?
				]
			);

			// 12. FINAL CTA — dark contact + form layout (form posts to nothing until mailer is wired)
			$blocks .= rehab_block_final_cta( [
				'anchorId' => 'assessment',
				'eyebrow'  => 'Take the next step',
				'heading'  => 'Are you ready to begin?',
				'lead'     => "Fill out the form and our client relations team will call you back, confidentially, within an hour during business hours. No pressure, no commitment — just a conversation.",
			] );

			$res = wp_update_post( [ 'ID' => $page_id, 'post_content' => wp_slash( $blocks ) ], true );
			echo is_wp_error( $res ) ? "ERR: " . $res->get_error_message() . "\n" : "OK rebuilt cocaine page (design v2, " . strlen( $blocks ) . " bytes)\n";
			break;

		case 'inspect-db':
			global $wpdb;
			echo "=== ACTIVE PLUGINS ===\n";
			foreach ( (array) get_option( 'active_plugins' ) as $p ) echo "  $p\n";
			echo "\n=== POST TYPES (counts) ===\n";
			$pts = $wpdb->get_results( "SELECT post_type, COUNT(*) AS n FROM {$wpdb->posts} WHERE post_status NOT IN ('auto-draft','trash') GROUP BY post_type ORDER BY n DESC" );
			foreach ( $pts as $row ) echo sprintf( "  %-30s %d\n", $row->post_type, $row->n );
			echo "\n=== ACF FIELD GROUPS (acf-field-group post type) ===\n";
			$acf_groups = $wpdb->get_col( "SELECT post_title FROM {$wpdb->posts} WHERE post_type='acf-field-group' AND post_status='publish'" );
			if ( $acf_groups ) foreach ( $acf_groups as $g ) echo "  $g\n";
			else echo "  (none)\n";
			echo "\n=== POSTMETA WITH ACF KEY PREFIX (counts top 10) ===\n";
			$meta = $wpdb->get_results( "SELECT meta_key, COUNT(*) AS n FROM {$wpdb->postmeta} WHERE meta_key LIKE '\\_acf%' OR meta_key NOT LIKE '\\_%' GROUP BY meta_key ORDER BY n DESC LIMIT 20" );
			foreach ( $meta as $row ) echo sprintf( "  %-50s %d\n", $row->meta_key, $row->n );
			echo "\n=== TAXONOMIES (counts) ===\n";
			$tax = $wpdb->get_results( "SELECT taxonomy, COUNT(*) AS n FROM {$wpdb->term_taxonomy} GROUP BY taxonomy ORDER BY n DESC" );
			foreach ( $tax as $row ) echo sprintf( "  %-30s %d\n", $row->taxonomy, $row->n );
			echo "\n=== ACTIVE THEME ===\n";
			echo "  Stylesheet: " . get_option( 'stylesheet' ) . "\n";
			echo "  Template:   " . get_option( 'template' ) . "\n";
			echo "\n=== PAGE TEMPLATES IN USE (counts) ===\n";
			$tpls = $wpdb->get_results( "SELECT meta_value, COUNT(*) AS n FROM {$wpdb->postmeta} WHERE meta_key='_wp_page_template' AND meta_value != '' GROUP BY meta_value ORDER BY n DESC" );
			foreach ( $tpls as $row ) echo sprintf( "  %-40s %d\n", $row->meta_value, $row->n );
			break;

		case 'inspect-faq-cpt':
			global $wpdb;
			echo "=== FAQ CPT — 5 sample records ===\n";
			$rows = $wpdb->get_results( "SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type='faq' AND post_status NOT IN ('auto-draft','trash') LIMIT 5" );
			foreach ( $rows as $r ) {
				echo "  #$r->ID [$r->post_status] $r->post_title\n";
				$content = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID=%d", $r->ID ) );
				echo "    body (first 200): " . substr( wp_strip_all_tags( $content ), 0, 200 ) . "\n";
				$meta = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, LEFT(meta_value, 80) AS v FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key NOT LIKE '_wp_%' AND meta_key NOT LIKE 'rank_math_%' AND meta_key NOT LIKE 'wp-smush%' LIMIT 5", $r->ID ) );
				foreach ( $meta as $m ) echo "    meta $m->meta_key = $m->v\n";
			}
			echo "\n=== Pages that link to FAQs via faq_ids meta ===\n";
			$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='faq_ids' LIMIT 5" );
			foreach ( $rows as $r ) echo "  page #$r->post_id → faq_ids: $r->meta_value\n";
			echo "  total pages with faq_ids meta: " . $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key='faq_ids'" ) . "\n";
			break;

		case 'inspect-rank-math':
			global $wpdb;
			echo "=== Rank Math meta keys & coverage ===\n";
			$rows = $wpdb->get_results( "SELECT meta_key, COUNT(*) AS n FROM {$wpdb->postmeta} WHERE meta_key LIKE 'rank_math_%' GROUP BY meta_key ORDER BY n DESC LIMIT 25" );
			foreach ( $rows as $r ) echo sprintf( "  %-50s %d\n", $r->meta_key, $r->n );
			echo "\n=== Sample for cocaine page (post 853) ===\n";
			$rows = $wpdb->get_results( "SELECT meta_key, LEFT(meta_value, 200) AS v FROM {$wpdb->postmeta} WHERE post_id=853 AND meta_key LIKE 'rank_math_%'" );
			foreach ( $rows as $r ) echo "  $r->meta_key = $r->v\n";
			break;

		case 'inspect-categories':
			global $wpdb;
			echo "=== Categories ===\n";
			$cats = $wpdb->get_results( "SELECT t.term_id, t.name, t.slug, tt.count FROM {$wpdb->terms} t JOIN {$wpdb->term_taxonomy} tt ON tt.term_id=t.term_id WHERE tt.taxonomy='category'" );
			foreach ( $cats as $c ) echo "  #$c->term_id  $c->name ($c->slug)  — $c->count posts\n";
			echo "\n=== Pages → categories sample ===\n";
			$rows = $wpdb->get_results( "SELECT p.ID, p.post_title, GROUP_CONCAT(t.name) AS cats FROM {$wpdb->posts} p JOIN {$wpdb->term_relationships} tr ON tr.object_id=p.ID JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id=tr.term_taxonomy_id JOIN {$wpdb->terms} t ON t.term_id=tt.term_id WHERE tt.taxonomy='category' AND p.post_status='publish' GROUP BY p.ID LIMIT 10" );
			foreach ( $rows as $r ) echo "  #$r->ID $r->post_title → $r->cats\n";
			break;

		case 'faq-search':
			global $wpdb;
			$q = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '';
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='faq' AND post_status='publish' AND post_title LIKE %s ORDER BY ID",
				'%' . $wpdb->esc_like( $q ) . '%'
			) );
			foreach ( $rows as $r ) echo "  #$r->ID  $r->post_title\n";
			echo "(" . count( $rows ) . " matches)\n";
			break;

		case 'dump-post-content':
			$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 853;
			$post = get_post( $id );
			if ( ! $post ) { echo "no post\n"; break; }
			echo "=== post_content of #$id ===\n";
			// Find FAQ block specifically
			if ( preg_match( '/<!-- wp:rehab\/faq.*?<!-- \/wp:rehab\/faq -->/s', $post->post_content, $m ) ) {
				echo $m[0] . "\n";
			} else {
				echo "(no FAQ block in post_content)\n";
				echo "First 500 chars:\n" . substr( $post->post_content, 0, 500 ) . "\n";
			}
			break;

		case 'inspect-acf-section':
			// Dump every non-empty key for a single section index.
			$id  = isset( $_GET['id'] )  ? (int) $_GET['id']  : 853;
			$sec = isset( $_GET['sec'] ) ? (int) $_GET['sec'] : 4;
			global $wpdb;
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT meta_key, meta_value FROM {$wpdb->postmeta}
				 WHERE post_id=%d AND meta_key LIKE %s AND meta_value != '' AND meta_value != '0'
				 ORDER BY meta_key",
				$id,
				"sections_{$sec}_%"
			) );
			foreach ( $rows as $r ) {
				$v = $r->meta_value;
				if ( strlen( $v ) > 300 ) $v = substr( $v, 0, 300 ) . '…';
				echo sprintf( "  %-70s %s\n", $r->meta_key, $v );
			}
			break;

		case 'inspect-acf-sections':
			$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 853;
			global $wpdb;
			// The top-level 'sections' meta key stores the serialized list of section types.
			$sections = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key='sections'", $id ) );
			if ( $sections ) {
				$sections = maybe_unserialize( $sections );
				echo "=== Section types on page #$id ===\n";
				if ( is_array( $sections ) ) {
					foreach ( $sections as $i => $type ) echo "  section_$i: $type\n";
				} else {
					echo "  raw: $sections\n";
				}
			} else {
				echo "no 'sections' key found — checking for first sections_* key:\n";
				$keys = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key LIKE 'sections\\_%' ORDER BY meta_key", $id ) );
				// Look for patterns sections_N_<TYPE_HINT>
				$top_level = [];
				foreach ( $keys as $k ) {
					if ( preg_match( '/^sections_(\d+)_([a-z_]+)$/', $k, $m ) ) {
						$top_level[ $m[1] ][ $m[2] ] = true;
					}
				}
				ksort( $top_level );
				foreach ( $top_level as $idx => $sub ) {
					echo "  section_$idx top-level fields: " . implode( ', ', array_keys( $sub ) ) . "\n";
				}
			}
			echo "\n=== Non-empty content per section (heading_title, heading_subtitle, etc.) ===\n";
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT meta_key, meta_value FROM {$wpdb->postmeta}
				 WHERE post_id=%d
				   AND meta_key REGEXP '^sections_[0-9]+_(heading_(title|subtitle|label)|left_column_(title|description|heading_title)|right_column_(title|description)|content|description|title|body|text|button_text)\$'
				   AND meta_value != ''
				 ORDER BY meta_key",
				$id
			) );
			foreach ( $rows as $r ) echo "  $r->meta_key:\n    " . substr( $r->meta_value, 0, 200 ) . "\n";
			break;

		case 'inspect-treatment-acf':
			$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 853;
			global $wpdb;
			$post = get_post( $id );
			if ( ! $post ) { echo "no post $id\n"; break; }
			echo "=== Page #$id — $post->post_title ===\n";
			$meta = $wpdb->get_results( $wpdb->prepare(
				"SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id=%d ORDER BY meta_key",
				$id
			) );
			// Group: ACF flex (sections_*), rank_math_*, _wp_*, other
			$groups = [ 'sections' => [], 'rank_math' => [], 'wp_internal' => [], 'smush' => [], 'other' => [] ];
			foreach ( $meta as $m ) {
				if ( str_starts_with( $m->meta_key, 'sections_' ) ) $groups['sections'][] = $m;
				elseif ( str_starts_with( $m->meta_key, 'rank_math_' ) ) $groups['rank_math'][] = $m;
				elseif ( str_starts_with( $m->meta_key, '_wp_' ) || str_starts_with( $m->meta_key, '_edit' ) ) $groups['wp_internal'][] = $m;
				elseif ( str_starts_with( $m->meta_key, 'wp-smush' ) || str_starts_with( $m->meta_key, 'wp-smpro' ) ) $groups['smush'][] = $m;
				else $groups['other'][] = $m;
			}
			echo "\n--- sections_* (ACF flex content, " . count( $groups['sections'] ) . " keys) ---\n";
			foreach ( $groups['sections'] as $m ) echo sprintf( "  %-70s %s\n", $m->meta_key, substr( $m->meta_value, 0, 80 ) );
			echo "\n--- other meta (" . count( $groups['other'] ) . " keys) ---\n";
			foreach ( $groups['other'] as $m ) echo sprintf( "  %-50s %s\n", $m->meta_key, substr( $m->meta_value, 0, 150 ) );
			break;

		case 'inspect-global-section':
			global $wpdb;
			$rows = $wpdb->get_results( "SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type='global_section' ORDER BY ID" );
			foreach ( $rows as $r ) {
				echo "  #$r->ID [$r->post_status] $r->post_title\n";
				$content_len = strlen( (string) get_post_field( 'post_content', $r->ID ) );
				echo "    body len: $content_len chars\n";
				$meta = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, LEFT(meta_value, 60) AS v FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key NOT LIKE '_wp%' AND meta_key NOT LIKE 'wp-smush%' LIMIT 6", $r->ID ) );
				foreach ( $meta as $m ) echo "    $m->meta_key = $m->v\n";
			}
			echo "\nPages referencing global_section by ID anywhere?\n";
			$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_value REGEXP '^[0-9]+\$' AND meta_value IN (SELECT ID FROM {$wpdb->posts} WHERE post_type='global_section') LIMIT 5" );
			foreach ( $rows as $r ) echo "  page #$r->post_id → global_section #$r->meta_value\n";
			break;

		case 'inspect-igmap':
			global $wpdb;
			$rows = $wpdb->get_results( "SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type='igmap' ORDER BY ID" );
			foreach ( $rows as $r ) {
				echo "  #$r->ID [$r->post_status] $r->post_title\n";
				$meta = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, LEFT(meta_value, 200) AS v FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key NOT LIKE '_%' AND meta_key NOT LIKE 'rank_math_%' AND meta_key NOT LIKE 'wp-smush%' LIMIT 10", $r->ID ) );
				foreach ( $meta as $m ) echo "    $m->meta_key = $m->v\n";
			}
			echo "\n=== Pages embedding igmap (shortcode or post_content reference) ===\n";
			$rows = $wpdb->get_results( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_content LIKE '%igmap%' OR post_content LIKE '%interactive-geo-map%' LIMIT 5" );
			foreach ( $rows as $r ) echo "  #$r->ID $r->post_title\n";
			break;

		case 'inspect-builder-pages':
			global $wpdb;
			echo "=== Pages still using template-builder.php ===\n";
			$rows = $wpdb->get_results( "SELECT p.ID, p.post_title, p.post_name, p.post_status FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} pm ON pm.post_id=p.ID WHERE pm.meta_key='_wp_page_template' AND pm.meta_value='template-builder.php'" );
			foreach ( $rows as $r ) echo "  #$r->ID [$r->post_status] /$r->post_name/ — $r->post_title\n";
			break;

		case 'migrate-acf-page':
			// Writes the mapper output to the page's post_content. Refuses
			// to overwrite an existing backup unless &force=1. Chrome
			// injection is on by default; pass &nochrome=1 to suppress.
			// Usage: ?rehab_oneshot=migrate-acf-page&id=853[&force=1][&nochrome=1]
			$id       = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
			$force    = isset( $_GET['force'] ) && '1' === $_GET['force'];
			$chrome   = ! ( isset( $_GET['nochrome'] ) && '1' === $_GET['nochrome'] );
			if ( ! $id ) {
				echo "Usage: ?rehab_oneshot=migrate-acf-page&id=POST_ID[&force=1][&nochrome=1]\n";
				break;
			}
			echo "Chrome injection: " . ( $chrome ? 'ON' : 'OFF' ) . "\n";
			$res = rehab_acf_migrate_page( $id, $force, $chrome );
			echo ( $res['ok'] ? 'OK ' : 'ERR ' ) . $res['msg'] . "\n";
			if ( $res['ok'] ) {
				echo "  layouts:  " . implode( ', ', $res['layouts'] ) . "\n";
				echo "  original: $res[original_bytes] bytes\n";
				echo "  mapped:   $res[mapped_bytes] bytes\n";
				echo "  delta:    " . ( $res['mapped_bytes'] - $res['original_bytes'] ) . " bytes\n";
			}
			break;

		case 'rollback-acf-page':
			// Restores post_content from the migration backup postmeta.
			// Usage: ?rehab_oneshot=rollback-acf-page&id=853
			$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
			if ( ! $id ) {
				echo "Usage: ?rehab_oneshot=rollback-acf-page&id=POST_ID\n";
				break;
			}
			$res = rehab_acf_rollback_page( $id );
			echo ( $res['ok'] ? 'OK ' : 'ERR ' ) . $res['msg'] . "\n";
			break;

		case 'preview-acf-mapped':
			// Renders rehab_acf_map_sections() output as raw markup so we
			// can eyeball the migration before writing anything to the DB.
			// Usage: ?rehab_oneshot=preview-acf-mapped&id=853
			$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 853;
			if ( ! function_exists( 'rehab_acf_map_sections' ) ) {
				echo "rehab_acf_map_sections() not loaded — check aa-acf-mapper.php\n";
				break;
			}
			$post = get_post( $id );
			echo "=== Page #$id — " . ( $post ? $post->post_title : 'NOT FOUND' ) . " ===\n";
			$sections = rehab_acf_get_sections( $id );
			echo count( $sections ) . " sections from ACF, mapping...\n\n";
			$markup = rehab_acf_map_sections( $sections );
			echo "Output: " . strlen( $markup ) . " bytes, " . substr_count( $markup, '<!-- wp:rehab/' ) . " rehab blocks\n";
			echo "Block list:\n";
			if ( preg_match_all( '/<!-- wp:(rehab\/[a-z0-9-]+)/i', $markup, $m ) ) {
				foreach ( $m[1] as $i => $name ) {
					echo "  " . ( $i + 1 ) . ". $name\n";
				}
			}
			$skipped = substr_count( $markup, '<!-- acf-mapper: skipped' );
			if ( $skipped ) {
				echo "Skipped sections: $skipped\n";
			}
			echo "\n--- First 4000 bytes of markup ---\n";
			echo substr( $markup, 0, 4000 );
			echo "\n[…]\n";
			break;

		case 'inspect-acf-reader':
			// Validates rehab_acf_get_sections() output against the legacy ACF flex schema.
			// Usage: ?rehab_oneshot=inspect-acf-reader&id=853
			$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 853;
			if ( ! function_exists( 'rehab_acf_get_sections' ) ) {
				echo "rehab_acf_get_sections() not loaded — check aa-acf-reader.php\n";
				break;
			}
			$post = get_post( $id );
			echo "=== Page #$id — " . ( $post ? $post->post_title : 'NOT FOUND' ) . " ===\n";
			$sections = rehab_acf_get_sections( $id );
			echo count( $sections ) . " sections:\n";
			foreach ( $sections as $sec ) {
				echo "\n--- [$sec[_idx]] $sec[_layout] ---\n";
				foreach ( $sec as $k => $v ) {
					if ( in_array( $k, [ '_idx', '_layout' ], true ) ) {
						continue;
					}
					if ( is_array( $v ) ) {
						echo "  $k = (array) " . count( $v ) . " items\n";
						foreach ( $v as $i => $item ) {
							if ( is_array( $item ) ) {
								echo "    [$i] " . wp_json_encode( array_map( fn( $x ) => is_string( $x ) ? mb_substr( $x, 0, 80 ) : $x, $item ) ) . "\n";
							} else {
								echo "    [$i] $item\n";
							}
						}
					} else {
						$display = is_string( $v ) ? mb_substr( $v, 0, 120 ) : $v;
						echo "  $k = " . var_export( $display, true ) . "\n";
					}
				}
				// Resolve global section recursively
				if ( 'global' === $sec['_layout'] ) {
					$global = rehab_acf_get_global_section( $sec['global_section_id'] );
					if ( $global ) {
						echo "  → resolves to global_section #$global[id] '$global[title]' with " . count( $global['sections'] ) . " inner sections\n";
						foreach ( $global['sections'] as $g ) {
							echo "    · [$g[_idx]] $g[_layout]\n";
						}
					} else {
						echo "  → global_section NOT FOUND\n";
					}
				}
			}
			break;

		case 'set-homepage-template':
			$page_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 6;
			$tpl     = isset( $_GET['tpl'] ) ? sanitize_text_field( $_GET['tpl'] ) : 'page-homepage.php';
			$res     = update_post_meta( $page_id, '_wp_page_template', $tpl );
			echo "OK page $page_id template -> $tpl (update: " . ( $res ? 'changed' : 'no-op' ) . ")\n";
			break;

		default:
			echo "unknown task\n";
	}
	exit;
}, 999 );
