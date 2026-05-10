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

		case 'rebuild-whyus-testimonials':
			$page_id = 825;
			$post = get_post( $page_id );
			if ( ! $post ) { echo "no why-us page\n"; break; }
			$file = WP_CONTENT_DIR . '/diamond-reviews.json';
			$reviews = json_decode( file_get_contents( $file ), true );
			if ( ! is_array( $reviews ) ) { echo "bad reviews\n"; break; }
			// Hand-pick reviews that end on complete sentences (no mid-word truncation).
			$picks_idx = [ 1, 3, 4 ]; // Alexander Evans, Gavin Gleeson, Maaike
			$picks = array_map( fn( $i ) => $reviews[ $i ], $picks_idx );

			$star_svg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><polygon points="12,2 15.1,8.6 22,9.5 17,14.4 18.2,21.5 12,18 5.8,21.5 7,14.4 2,9.5 8.9,8.6"></polygon></svg>';
			$stars = str_repeat( $star_svg, 5 );

			$inner = '';
			foreach ( $picks as $r ) {
				$name  = wp_kses_post( $r['name'] );
				$quote = wp_kses_post( html_entity_decode( $r['text'], ENT_QUOTES ) );
				$attrs = wp_json_encode( [
					'rating' => 5,
					'quote'  => $quote,
					'name'   => $name,
					'role'   => 'Former client',
				] );
				$inner .= '<!-- wp:rehab/testimonial ' . $attrs . " -->\n";
				$inner .= '<div class="wp-block-rehab-testimonial rehab-testimonial"><div class="rehab-testimonial__stars" aria-label="5 out of 5 stars">' . $stars . '</div><p class="rehab-testimonial__quote">' . esc_html( $quote ) . '</p><div class="rehab-testimonial__author"><p class="rehab-testimonial__name">' . esc_html( $name ) . '</p><p class="rehab-testimonial__role">Former client</p></div></div>' . "\n";
				$inner .= "<!-- /wp:rehab/testimonial -->\n\n";
			}

			$wrap_attrs = wp_json_encode( [
				'background' => 'white',
				'columns'    => 3,
				'heading'    => 'Real results, real people',
				'subheading' => 'Hear directly from those who achieved full recovery.',
			] );
			$new_block = '<!-- wp:rehab/testimonials ' . $wrap_attrs . " -->\n" .
				'<section class="wp-block-rehab-testimonials rehab-testimonials rehab-bg-white rehab-testimonials--cols-3"><div class="rehab-container"><header class="rehab-testimonials__header"><h2 class="rehab-heading rehab-heading--lg">Real results, real people</h2><p class="rehab-testimonials__subheading">Hear directly from those who achieved full recovery.</p></header><div class="rehab-testimonials__grid">' . "\n" .
				$inner .
				'</div></div></section>' . "\n" .
				"<!-- /wp:rehab/testimonials -->";

			$updated = preg_replace(
				'/<!--\s*wp:rehab\/testimonials\b.*?<!--\s*\/wp:rehab\/testimonials\s*-->/is',
				$new_block,
				$post->post_content,
				1
			);
			if ( $updated === $post->post_content ) { echo "no change\n"; break; }
			$res = wp_update_post( [ 'ID' => $page_id, 'post_content' => wp_slash( $updated ) ], true );
			echo is_wp_error( $res ) ? 'ERR ' . $res->get_error_message() : "OK why-us testimonials\n";
			break;

		case 'rebuild-faq-page':
			$file = WP_CONTENT_DIR . '/diamond-faq.json';
			if ( ! file_exists( $file ) ) { echo "no faq json\n"; break; }
			$items = json_decode( file_get_contents( $file ), true );
			if ( ! is_array( $items ) ) { echo "bad json\n"; break; }
			// Group: privacy (0-3) / treatment & care (4-7) / logistics (8-11)
			$groups = [
				[
					'heading' => 'Privacy &amp; confidentiality',
					'bg'      => 'cream',
					'items'   => array_slice( $items, 0, 4 ),
				],
				[
					'heading' => 'Treatment &amp; care',
					'bg'      => 'white',
					'items'   => array_slice( $items, 4, 4 ),
				],
				[
					'heading' => 'Location, cost &amp; logistics',
					'bg'      => 'cream',
					'items'   => array_slice( $items, 8, 4 ),
				],
			];
			$out = '';
			foreach ( $groups as $g ) {
				$inner_blocks = '';
				$inner_html   = '';
				foreach ( $g['items'] as $it ) {
					$q = wp_kses_post( $it['q'] );
					$a = wp_kses_post( $it['a'] );
					$inner_blocks .= '<!-- wp:rehab/faq-item ' . wp_json_encode( [ 'question' => $q, 'answer' => $a ] ) . " -->\n";
					$inner_blocks .= '<details class="wp-block-rehab-faq-item rehab-faq__item"><summary class="rehab-faq__question"><span>' . $q . '</span><span class="rehab-faq__icon" aria-hidden="true"></span></summary><div class="rehab-faq__answer"><p>' . $a . '</p></div></details>' . "\n";
					$inner_blocks .= "<!-- /wp:rehab/faq-item -->\n";
					$inner_html   .= '<details class="wp-block-rehab-faq-item rehab-faq__item"><summary class="rehab-faq__question"><span>' . $q . '</span><span class="rehab-faq__icon" aria-hidden="true"></span></summary><div class="rehab-faq__answer"><p>' . $a . '</p></div></details>';
				}
				$attrs = wp_json_encode( [ 'background' => $g['bg'], 'heading' => $g['heading'] ] );
				$out .= '<!-- wp:rehab/faq ' . $attrs . " -->\n";
				$out .= '<section class="wp-block-rehab-faq rehab-faq rehab-bg-' . $g['bg'] . '" aria-label="Frequently Asked Questions"><div class="rehab-container rehab-container--narrow"><h2 class="rehab-heading rehab-heading--lg rehab-faq__heading">' . $g['heading'] . '</h2><div class="rehab-faq__list">' . $inner_html . '</div></div></section>' . "\n";
				$out .= "<!-- /wp:rehab/faq -->\n\n";
			}
			$res = wp_update_post( [ 'ID' => 1197, 'post_content' => wp_slash( $out ) ], true );
			echo is_wp_error( $res ) ? 'ERR ' . $res->get_error_message() : "OK rebuilt faq\n";
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

		case 'rebuild-faq-page-v2':
			$json_path = '/var/www/html/wp-content/diamond-faq.json';
			if ( ! file_exists( $json_path ) ) { echo "no diamond-faq.json\n"; break; }
			$flat = json_decode( file_get_contents( $json_path ), true );
			if ( ! is_array( $flat ) ) { echo "bad json\n"; break; }

			// Index by question lowercase for matching.
			$by_q = [];
			foreach ( $flat as $row ) {
				$by_q[ strtolower( trim( $row['q'] ?? '' ) ) ] = $row;
			}
			$find = function( $needle ) use ( $by_q ) {
				$needle = strtolower( $needle );
				foreach ( $by_q as $key => $row ) {
					if ( strpos( $key, $needle ) !== false ) return $row;
				}
				return null;
			};

			$groups = [
				[
					'title' => 'Privacy & confidentiality',
					'qs'    => [
						'completely confidential',
						'sign ndas',
						'high-profile client',
						'alias during my stay',
					],
				],
				[
					'title' => 'Treatment & care',
					'qs'    => [
						'kind of therapies',
						'is the diamond rehab thailand licensed',
						'family members visit',
					],
				],
				[
					'title' => 'Location, cost & logistics',
					'qs'    => [
						'where is the facility located',
						'how do i travel',
						'climate like',
						'kind of visa',
						'thailand a safe place',
					],
				],
			];

			$out = '';
			foreach ( $groups as $g ) {
				$attrs_faq = wp_json_encode( [ 'heading' => $g['title'] ] );
				$inner_items = '';
				foreach ( $g['qs'] as $needle ) {
					$row = $find( $needle );
					if ( ! $row ) continue;
					$q = trim( $row['q'] );
					$a = trim( $row['a'] );
					$item_attrs = wp_json_encode( [ 'question' => $q, 'answer' => $a ] );
					$inner_items .=
						'<!-- wp:rehab/faq-item ' . $item_attrs . " -->\n" .
						'<details class="wp-block-rehab-faq-item rehab-faq-item"><summary>' . esc_html( $q ) . '</summary><div class="rehab-faq-item__answer">' . wp_kses_post( $a ) . "</div></details>\n" .
						"<!-- /wp:rehab/faq-item -->\n\n";
				}
				if ( ! $inner_items ) continue;

				$out .=
					'<!-- wp:rehab/faq ' . $attrs_faq . " -->\n" .
					'<section class="wp-block-rehab-faq rehab-faq"><div class="rehab-container rehab-container--narrow"><h2 class="rehab-heading rehab-heading--lg">' . esc_html( $g['title'] ) . "</h2><div class=\"rehab-faq__list\">\n" .
					$inner_items .
					"</div></div></section>\n" .
					"<!-- /wp:rehab/faq -->\n\n";
			}

			if ( ! $out ) { echo "no groups produced\n"; break; }

			wp_update_post( [ 'ID' => 1197, 'post_content' => $out ] );
			echo "OK faq rebuilt — " . substr_count( $out, 'wp:rehab/faq-item' ) . " items across " . substr_count( $out, 'wp:rehab/faq ' ) . " groups\n";
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

		case 'rebuild-cocaine-page':
			$page_id = 853;
			$post = get_post( $page_id );
			if ( ! $post ) { echo "no post 853\n"; break; }

			$base = '/wp-content/uploads/';
			$blocks = '';

			// 1. HERO
			$blocks .= rehab_block_hero( [
				'eyebrow'    => 'Cocaine & stimulant addiction treatment',
				'headline'   => 'Effective cocaine addiction treatment at Thailand\'s leading luxury centre',
				'body'       => 'Doctor-led residential rehab for cocaine and stimulant addiction at our private 5-star sanctuary in Hua Hin, Thailand. Maximum 12 clients at a time, with absolute confidentiality.',
				'imageUrl'   => 'http://5.223.87.211:8081' . $base . '2024/05/1-1-session-room-1.jpg',
				'imageAlt'   => 'Diamond Rehab Hua Hin Thailand — 1:1 session room',
			] );

			// 2. Overcome your Cocaine addiction (white)
			$blocks .= rehab_block_prose(
				'Overcome your Cocaine addiction in Thailand',
				[
					'This is where life-changing transformations happen. Nestled in the peaceful mountains of tropical Hua Hin, our five-star cocaine rehab centre is the perfect place to start your cocaine addiction treatment and recovery journey. Fully equipped cocaine rehab facilities, five-star accommodation options and world-class addiction experts — we\'ve assembled everything you need to overcome your cocaine addiction. The only thing missing is you.',
					'Contact us today to learn more about our admission process or read on to find out how our cocaine rehab treatment program can help you kickstart your recovery.',
				],
				[],
				$base . '2024/05/1-1-session-room-1.jpg',
				'Diamond Rehab Hua Hin Thailand',
				'white',
				'split'
			);

			// 3. Cocaine addiction treatment (cream)
			$blocks .= rehab_block_prose(
				'Cocaine addiction treatment',
				[
					'Cocaine rehab statistics show that professional intervention is the most effective treatment for cocaine addiction. Here at The Diamond Rehab Thailand, we take a holistic approach to treatment that addresses both the symptoms of cocaine use and the underlying factors — previous experiences, relationships, mental health issues, past trauma, and more — that contribute to addiction.',
					'Our highly experienced clinicians use a combination of Eastern and Western therapeutic techniques to effectively rehab cocaine addiction. Available 24/7, our clinical team is fully committed to providing you with the support, expertise, and guidance required to achieve a positive outcome.',
				],
				[],
				$base . '2024/05/Closer-up-dining-2.jpg',
				'Drug treatment center in Hua Hin',
				'cream',
				'split-reverse'
			);

			// 4. Personalised program options intro (white)
			$blocks .= rehab_block_prose(
				'Personalised Cocaine addiction treatment program options',
				[
					'As one of the leading cocaine rehab centers in Thailand, we understand that there\'s no silver bullet when it comes to treating cocaine addiction and achieving long-term sobriety.',
					'That\'s why we provide fully customized cocaine rehab programs based on a clinical assessment of your condition.',
					'During your stay in our luxury cocaine rehab facilities, we\'ll continuously monitor your progress and adjust your recovery plan as you advance through your program to ensure your treatment is as effective as possible.',
				],
				[], '', '', 'white'
			);

			// 5. Three pillars cards-grid (sage-mist)
			$blocks .= rehab_block_cards_grid(
				'Three pillars of our cocaine rehab program',
				'Each program is fully customized — these are the modalities we draw from.',
				[
					[ 'title' => 'Medical detox', 'description' => 'Safe, medically supervised withdrawal in our private rehab facility. Symptoms managed by qualified clinicians; resort-style amenities to keep you comfortable.', 'imageUrl' => $base . '2024/05/Bungalow-evening.jpg', 'imageAlt' => 'Luxury detox center Thailand', 'url' => '' ],
					[ 'title' => 'Behavioral therapy', 'description' => 'Evidence-based 1:1 and group therapy. Identify the triggers behind cocaine use and build the recovery plan that prevents relapse long-term.', 'imageUrl' => $base . '2024/05/Closer-up-dining-2.jpg', 'imageAlt' => 'Behavioral therapy room', 'url' => '' ],
					[ 'title' => 'Holistic therapy', 'description' => 'Yoga, mindfulness, and personalized fitness — restorative practices that bring mind and body back into harmony alongside clinical treatment.', 'imageUrl' => $base . '2022/09/people-on-top-of-a-mountain-watching-the-sunrise.jpg', 'imageAlt' => 'People on top of a mountain watching the sunrise', 'url' => '' ],
				],
				3, 'sage-mist'
			);

			// 6. Detox deep section (white) + bullet list
			$blocks .= rehab_block_prose(
				'Detox: an important part of the cocaine rehab process',
				[
					'Depending on the severity of your addiction, our clinical team may recommend starting your cocaine rehab treatment program with a detox. We\'ll provide you with a safe environment and monitor your symptoms to make the process as comfortable as possible.',
					'During the detoxification process, you may experience a range of cocaine withdrawal symptoms, including:',
				],
				[ 'Strong cravings for cocaine', 'Depression', 'Suicidal thoughts', 'Restlessness', 'Lethargy', 'Nightmares' ],
				'', '', 'white'
			);
			$blocks .= rehab_block_prose(
				'',
				[
					'During withdrawal, the cravings for cocaine can be extremely intense. Entering inpatient care at a cocaine rehab center reduces the risk of relapse during this critical time.',
					'The detoxification process for cocaine is relatively quick compared to other drugs, but some symptoms may persist for weeks or even months after completing your cocaine rehab programme. We\'ll do everything we can to make the process as comfortable as possible, providing you with resort-style amenities and a full team of qualified medical professionals who have extensive experience with cocaine drug rehab.',
				],
				[], '', '', 'white'
			);

			// 7. Behavioral therapy deep section (cream)
			$blocks .= rehab_block_prose(
				'The role of behavioral therapy in rehab for cocaine addiction',
				[
					'Addiction is so much more than a physical dependence on drugs or alcohol. Many people with substance abuse and addiction disorders have deep-rooted psychological and emotional issues that must be addressed in order to achieve lasting wellness.',
					'Therapy provides a crucial support system for people recovering from substance use disorders and is an important component of our substance abuse rehabilitation programs. We offer an intimate, judgment-free space, where you can speak honestly about your past and ambitions for the future. During rehab for substance abuse, our fully qualified counselors will work with you to help you dissect the behavioral issues and psychosocial factors that contribute to addiction.',
					'You\'ll learn to identify the personal triggers — stressors, environmental cues, and social circles — that lead to relapse and, together with your therapist, develop a recovery plan to manage these triggers in the short- and long-term.',
					'Education and awareness give rise to positive change. You\'ll graduate from your substance abuse treatment with a stronger understanding of yourself and your triggers, which will help you reduce the risk of relapse in the years ahead.',
				],
				[], '', '', 'cream'
			);

			// 8. Holistic therapy deep section (white)
			$blocks .= rehab_block_prose(
				'Holistic therapy used in the treatment of cocaine addiction',
				[
					'Here at The Diamond Rehab Thailand, we believe that successful cocaine addiction rehab relies on addressing every aspect of your health and wellbeing.',
					'That\'s why we take a holistic approach to the cocaine rehab process, which aims to bring harmony to your mind and body while equipping you with all the tools necessary for achieving life-long sobriety.',
					'Our rehab for cocaine addiction includes a range of holistic therapeutic techniques that are effective in the treatment of cocaine addiction:',
				],
				[
					'Yoga: Strengthen the neglected bond between mind and body. A combination of physical exercise, breath control, and mindfulness, the ancient art of yoga is an excellent tool for improving strength and flexibility while healing emotional trauma.',
					'Mindfulness: Steeped in Buddhist culture, Thailand is the perfect place to practice mindfulness and reconnect with yourself during cocaine rehab. Through our mindfulness program, you\'ll learn to be more present, live with intent, and process negative thoughts.',
					'Fitness: Rebuilding strength and physical awareness are critical for healthy habits. Studies show exercise programs help reduce the risk of relapse during and after cocaine rehab — swim, run, box, cycle, or lift weights with our professional trainers.',
				],
				$base . '2024/05/Dining-area-2.jpg',
				'Holistic therapy and dining at Diamond Rehab Thailand',
				'white',
				'split'
			);

			// 9. Advantages of inpatient (cream)
			$blocks .= rehab_block_prose(
				'Advantages of inpatient treatment for cocaine addiction',
				[
					'The Diamond Rehab Thailand is an inpatient treatment center. Inpatient rehabilitation is widely regarded as the most effective form of cocaine addiction treatment as it allows you to break away from your daily routines and social triggers that contribute to substance use disorders.',
					'Isolating yourself from your usual lifestyle and social circles eliminates the risk of giving in to drug cravings. Located in a peaceful part of Hua Hin, our luxury cocaine addiction rehab facility is worlds away from the distractions of everyday life, allowing you to focus all your time and energy on your recovery journey.',
					'In our world-class cocaine addiction treatment center, our addiction experts will guide you through the crucial first weeks of your custom-made treatment program as you settle into your daily recovery routine. We\'ll provide you with all the support you need to not only overcome the physical symptoms of your addiction, but also the underlying psychological factors that fuel substance abuse.',
				],
				[], '', '', 'cream'
			);

			// 10. Is it time to consider rehab (white)
			$blocks .= rehab_block_prose(
				'Is it time to consider rehab for cocaine addiction?',
				[
					'Cocaine is a powerful stimulant and a highly addictive drug. Originally used as an active ingredient in a range of medicinal products, cocaine is now regarded as a widely available party drug that carries a high addiction potential.',
					'Cocaine is notoriously addictive due to the profound impact it has on the chemistry of your brain. The use of cocaine triggers the release of dopamine, a hormone that produces feelings of pleasure, happiness, and satisfaction. When the effects wear off, users often experience a severe crash characterized by feelings of anxiety, fatigue, and an intense craving to use the drug again.',
					'It\'s easy for occasional cocaine abuse to quickly snowball into a pattern of misuse. Cocaine cravings can be difficult to resist, which leads to repeated substance abuse. Over time, users build up a tolerance to cocaine and need to take more to achieve the desired effects.',
					'Checking into a cocaine drug rehab center is an effective way to break cocaine dependence. Below are some of the most common signs of cocaine addiction:',
				],
				[ 'Nervousness', 'Severe weight loss', 'Sexual dysfunction', 'Depression', 'Frequent nightmares', 'Decreased ability to focus', 'Increased and/or involuntary movements' ],
				$base . '2024/05/Close-up-chairs-3.jpg',
				'Luxury treatment lounge',
				'white',
				'split-reverse'
			);

			// 11. Taking the next step (cream)
			$blocks .= rehab_block_prose(
				'Taking the next step in your cocaine addiction rehab journey',
				[
					'Recovering from cocaine addiction is hard work — but our exceptional cocaine rehab success rate proves that it\'s possible. Sometimes, all you need is a helping hand.',
					'That\'s where we come in. If you\'re ready to seek treatment, The Diamond Rehab Thailand is here to help. Drawing on our extensive experience as addiction experts in cocaine rehab, we\'ll guide you through a fully tailored treatment plan that sets the foundation for a healthy, positive and fulfilling life.',
					'Want to learn more about our cocaine addiction treatment options? Give us a call today to find out more about our world-class cocaine addiction treatment center and take the next step in your recovery journey.',
				],
				[], '', '', 'cream'
			);

			// 12. FAQ
			$blocks .= rehab_block_faq(
				'Frequently asked questions',
				[
					[ 'question' => 'What is the process of rehabilitation?', 'answer' => 'The process may differ — programs are customised based on what the patient needs and the severity of the addiction or co-occurring mental illness. The goal is always to ensure the individual\'s well-being. Most treatment programs include evaluation, detox, psychological treatments, education sessions, and supportive services. When you transition to outpatient therapy, you may continue with one-on-one or group sessions.' ],
					[ 'question' => 'Are 28 days of rehab enough?', 'answer' => 'This depends on the individual case. After consulting with our psychiatrist, we will give you a recommendation on the number of days in treatment that is advised.' ],
					[ 'question' => 'Can clients leave the rehab?', 'answer' => 'Clients can only leave the property under the care of our therapeutic team.' ],
				]
			);

			// 13. CTA
			$blocks .= rehab_block_cta( [
				'heading'    => 'Are you ready to take the next step?',
				'body'       => 'Reach out for a confidential call from our client relations team. We\'re available 24/7.',
			] );

			$res = wp_update_post( [ 'ID' => $page_id, 'post_content' => wp_slash( $blocks ) ], true );
			echo is_wp_error( $res ) ? "ERR: " . $res->get_error_message() . "\n" : "OK rebuilt cocaine page (" . strlen( $blocks ) . " bytes)\n";
			break;

		case 'rebuild-ice-page':
			$page_id = 867;
			$post = get_post( $page_id );
			if ( ! $post ) { echo "no post 867\n"; break; }

			$base = '/wp-content/uploads/';
			$blocks = '';

			// 1. HERO
			$blocks .= rehab_block_hero( [
				'eyebrow'    => 'Meth & ice (crystal meth) addiction treatment',
				'headline'   => 'Meth and ice addiction treatment at Thailand\'s leading luxury rehab centre',
				'body'       => 'Intensive residential rehabilitation for methamphetamine addiction. 24/7 medical care, structured therapy, and full lifestyle reconstruction in a private, secure 5-star setting.',
				'imageUrl'   => 'http://5.223.87.211:8081' . $base . '2024/05/Bungalow-evening-2.jpg',
				'imageAlt'   => 'Luxury bungalow evening — Diamond Rehab Thailand',
			] );

			// 2. Luxury Crystal Meth addiction treatment (white)
			$blocks .= rehab_block_prose(
				'Luxury crystal meth addiction treatment in Thailand',
				[
					'Methamphetamine — known as ice or crystal meth — is one of the most addictive stimulants in the world. The journey out of meth addiction is rarely something anyone can do alone. At The Diamond Rehab Thailand, our experienced clinical team provides a structured, medically supervised environment to help you fully recover.',
					'Our private 5-star rehab in Hua Hin combines world-class clinical care with the seclusion and comfort needed to reset both mind and body. Whether you\'re seeking treatment for yourself or a loved one, contact us today to learn how our crystal meth rehab program can help.',
				],
				[],
				$base . '2024/05/Bungalow-evening-2.jpg',
				'Luxury bungalow at Diamond Rehab Thailand',
				'white',
				'split'
			);

			// 3. Our approach to rehab for Ice addiction (cream)
			$blocks .= rehab_block_prose(
				'Our approach to rehab for ice addiction',
				[
					'Successful methamphetamine addiction treatment requires more than physical detox. Crystal meth addiction rewires the brain\'s reward system — sustainable recovery means addressing the psychological dependence as well as the physical.',
					'Our holistic ice addiction treatment program combines evidence-based clinical care with structured therapy and lifestyle reconstruction. Available 24/7, our team gives you the medical safety, therapeutic depth, and personal support needed to break free from crystal meth.',
				],
				[],
				$base . '2024/05/Dining-area-2.jpg',
				'Dining area — Diamond Rehab Thailand',
				'cream',
				'split-reverse'
			);

			// 4. World-class rehab for Ice intro (white)
			$blocks .= rehab_block_prose(
				'World-class rehab for ice (meth) addiction',
				[
					'There is no one-size-fits-all approach to crystal meth addiction. We design every program around the individual — medical history, severity of addiction, co-occurring mental health conditions, and personal goals.',
					'Two pillars anchor every program: safe medical detox, and relapse prevention through evidence-based therapy. Both are delivered by our resident clinical team in our private 5-star facility.',
				],
				[], '', '', 'white'
			);

			// 5. Two pillars cards-grid (sage-mist)
			$blocks .= rehab_block_cards_grid(
				'The two pillars of our ice addiction program',
				'Every program is fully customized around your circumstances.',
				[
					[ 'title' => 'Safe medical detox', 'description' => 'Medically supervised withdrawal in a luxurious private setting. Our doctors monitor symptoms 24/7 and adjust care as your body recovers.', 'imageUrl' => $base . '2024/05/Bungalow-evening.jpg', 'imageAlt' => 'Luxury detox bungalow', 'url' => '' ],
					[ 'title' => 'Preventing relapse', 'description' => 'Behavioral therapy, group counseling, and skills-based recovery work that gives you the tools to stay sober long after you leave the facility.', 'imageUrl' => $base . '2024/05/Closer-up-dining-2.jpg', 'imageAlt' => 'Therapy room', 'url' => '' ],
				],
				2, 'sage-mist'
			);

			// 6. Safe medical detox deep section (white)
			$blocks .= rehab_block_prose(
				'Safe medical detox for ice drug rehab',
				[
					'Methamphetamine withdrawal can be physically and psychologically severe. The first 1-2 weeks are typically the hardest — our medical team monitors you continuously and provides the medications and support you need to get through them safely.',
					'Common withdrawal symptoms include:',
				],
				[ 'Intense fatigue and excessive sleeping', 'Increased appetite', 'Severe depression and irritability', 'Strong cravings for crystal meth', 'Anxiety and paranoia', 'Difficulty experiencing pleasure (anhedonia)' ],
				'', '', 'white'
			);

			// 7. Preventing relapse deep section (cream)
			$blocks .= rehab_block_prose(
				'Preventing relapse after ice addiction treatment',
				[
					'Recovery from crystal meth doesn\'t end with detox — it begins there. The risk of relapse is highest in the first 90 days, which is why our program focuses heavily on the structured therapy and skill-building that follows medical stabilization.',
					'You\'ll work 1:1 with your therapist to identify the personal triggers — stressors, environments, relationships — that fuelled use, and develop a personal recovery plan to manage them. Group sessions, family work where appropriate, and lifetime aftercare ensure you have ongoing support long after discharge.',
					'Our holistic modalities — yoga, mindfulness, fitness, nutrition — give your body and mind the tools to rebuild around sobriety, not just escape from addiction.',
				],
				[], '', '', 'cream'
			);

			// 8. Rehabilitation treatment for Ice addiction (white)
			$blocks .= rehab_block_prose(
				'Rehabilitation treatment for ice addiction',
				[
					'Our ice rehabilitation program is designed for inpatient residential treatment — you stay at our facility for the duration of your program. This separates you from the people, places, and routines that maintained your meth use, and gives you a structured day around recovery.',
					'A typical day combines clinical work (1:1 therapy, group sessions, medical reviews) with restorative activity (fitness, yoga, mindfulness, nutrition, leisure). You\'re never alone in the work — our staff is there 24/7.',
					'Programs run from 28 days minimum to 12 weeks or longer. The right length depends on your circumstances. After consulting with our psychiatrist, we\'ll give you a recommendation tailored to you.',
				],
				[], '', '', 'white'
			);

			// 9. Ice Addiction treatment & rehabilitation at The Diamond Rehab (cream)
			$blocks .= rehab_block_prose(
				'Ice addiction treatment & rehabilitation at The Diamond Rehab Thailand',
				[
					'Our facility was purpose-built for clients who need the highest level of clinical care alongside the discretion of a private 5-star resort. With a maximum 12-client cap, your treatment is genuinely personal — your therapist knows your story; your doctor knows your medical history; your aftercare plan is built around you.',
					'Diamond Rehab is operated by an international clinical team with deep expertise in stimulant addiction, dual diagnosis, and trauma-informed care. We take referrals from clients across Australia, the UK, the US, and Europe.',
				],
				[],
				$base . '2024/05/Close-up-chairs-3.jpg',
				'Treatment lounge',
				'cream',
				'split'
			);

			// 10. Methamphetamine rehabilitation for Ice (white)
			$blocks .= rehab_block_prose(
				'Methamphetamine rehabilitation for ice (crystal meth) addicts',
				[
					'Crystal meth addiction has high relapse rates without proper rehabilitation. The good news: with structured residential treatment, evidence-based therapy, and ongoing aftercare, recovery is absolutely achievable.',
					'If you or someone you love is using crystal meth, the next step is the most important one. Our admissions team will speak with you confidentially, answer your questions, and walk you through the practicalities of starting treatment.',
				],
				[], '', '', 'white'
			);

			// 11. FAQ
			$blocks .= rehab_block_faq(
				'Frequently asked questions',
				[
					[ 'question' => 'What is the process of rehabilitation?', 'answer' => 'The process may differ — programs are customised based on what the patient needs and the severity of the addiction or co-occurring mental illness. The goal is always to ensure the individual\'s well-being. Most treatment programs include evaluation, detox, psychological treatments, education sessions, and supportive services.' ],
					[ 'question' => 'Are 28 days of rehab enough?', 'answer' => 'This depends on the individual case. After consulting with our psychiatrist, we will give you a recommendation on the number of days in treatment that is advised.' ],
					[ 'question' => 'Can clients leave the rehab?', 'answer' => 'Clients can only leave the property under the care of our therapeutic team.' ],
				]
			);

			// 12. CTA
			$blocks .= rehab_block_cta( [
				'heading'    => 'Are you ready to take the next step?',
				'body'       => 'Reach out for a confidential call from our admissions team — we\'re available 24/7.',
			] );

			$res = wp_update_post( [ 'ID' => $page_id, 'post_content' => wp_slash( $blocks ) ], true );
			echo is_wp_error( $res ) ? "ERR: " . $res->get_error_message() . "\n" : "OK rebuilt ice page (" . strlen( $blocks ) . " bytes)\n";
			break;

		case 'rebuild-alltreats-page':
			$page_id = 1219;
			$post = get_post( $page_id );
			if ( ! $post ) { echo "no post 1219\n"; break; }

			$base = '/wp-content/uploads/';
			$blocks = '';

			// 1. HERO
			$blocks .= rehab_block_hero( [
				'eyebrow'    => 'Full-spectrum addiction & mental-health treatment',
				'headline'   => 'All treatments at The Diamond Rehab Thailand',
				'body'       => 'We treat the full spectrum of substance and behavioral addictions, prescription dependence, eating disorders, and mental-health conditions. Every program is built individually around your medical history, life circumstances, and recovery goals.',
				'imageUrl'   => 'http://5.223.87.211:8081' . $base . '2024/05/1-1-session-room-1.jpg',
				'imageAlt'   => '1:1 session room — Diamond Rehab Thailand',
			] );

			// 2. Intro prose (white)
			$blocks .= rehab_block_prose(
				'',
				[
					'Below is the full list of conditions we treat at our private 5-star rehab in Hua Hin. Each program is delivered by our resident multi-disciplinary clinical team — psychiatrists, doctors, therapists, holistic practitioners — with a maximum 12-client cap so every client gets genuinely personal care.',
					'Don\'t see your specific situation listed? Get in touch — we treat dual-diagnosis cases and complex co-occurring conditions, and we\'ll be honest with you about whether our program fits.',
				],
				[], '', '', 'white'
			);

			// 3. Substance addiction (cream)
			$blocks .= rehab_block_cards_grid(
				'Substance addiction treatment',
				'Residential rehab for drug and alcohol addiction.',
				[
					[ 'title' => 'Cocaine addiction treatment', 'description' => 'Detox and behavioural therapy for cocaine and stimulant dependence.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/cocaine-addiction-treatment-rehab-thailand/' ],
					[ 'title' => 'Meth & ice addiction treatment', 'description' => 'Structured rehab for methamphetamine and crystal meth addiction.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/ice-addiction-treatment-rehab-thailand/' ],
					[ 'title' => 'Alcohol addiction', 'description' => 'Medically supervised alcohol detox and long-term recovery program.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/alcohol-addiction/' ],
					[ 'title' => 'Heroin & opiate addiction', 'description' => 'Specialist opiate rehab covering heroin, prescription opioids, and synthetic opioids.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/heroin-addiction/' ],
					[ 'title' => 'Crack addiction & detox', 'description' => 'Inpatient detox and recovery for crack-cocaine dependence.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/crack-cocaine-addiction/' ],
					[ 'title' => 'Marijuana / cannabis addiction', 'description' => 'Behavioural treatment for chronic cannabis use and dependence.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/marijuana-addiction-symptoms-and-treatment/' ],
				],
				3, 'cream'
			);

			// 4. Prescription drug rehab (white)
			$blocks .= rehab_block_cards_grid(
				'Prescription drug rehab',
				'Specialist treatment for prescription medication dependence.',
				[
					[ 'title' => 'Xanax (alprazolam) addiction', 'description' => 'Benzodiazepine detox and recovery program.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/what-is-xanax-addiction/' ],
					[ 'title' => 'OxyContin (oxycodone) addiction', 'description' => 'Opioid dependence treatment with medical detox.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/what-is-oxycontin-addiction/' ],
					[ 'title' => 'Valium (diazepam) addiction', 'description' => 'Long-acting benzodiazepine taper and recovery.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/what-is-valium-addiction/' ],
					[ 'title' => 'Tramadol addiction & detox', 'description' => 'Detox and rehabilitation for tramadol dependence.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/what-is-tramadol-addiction/' ],
					[ 'title' => 'Ritalin (methylphenidate) addiction', 'description' => 'Stimulant medication dependence treatment.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/what-is-ritalin-addiction/' ],
					[ 'title' => 'Adderall addiction & treatment', 'description' => 'Recovery program for amphetamine-based ADHD medications.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/adderall-addiction-symptoms-signs-withdrawal-and-treatment/' ],
				],
				3, 'white'
			);

			// 5. Mental health rehab (cream)
			$blocks .= rehab_block_cards_grid(
				'Mental health rehab',
				'Inpatient programs for anxiety, mood, trauma, and behavioral conditions.',
				[
					[ 'title' => 'Anxiety treatment & rehab', 'description' => 'Treatment for generalized anxiety, panic, and stress-related conditions.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/anxiety-treatment/' ],
					[ 'title' => 'Depression treatment', 'description' => 'Inpatient treatment for major depression and persistent depressive disorder.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/depression-retreat-thailand/' ],
					[ 'title' => 'PTSD & trauma treatment', 'description' => 'Trauma-informed therapy for post-traumatic stress and complex trauma.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/post-traumatic-stress-disorder/' ],
					[ 'title' => 'Burnout treatment for executives', 'description' => 'Restorative inpatient program for chronic burnout and overwork.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/burnout-retreat/' ],
					[ 'title' => 'Sex addiction treatment', 'description' => 'Behavioral treatment for compulsive sexual behavior.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/sex-addiction/' ],
					[ 'title' => 'Insomnia & sleep disorder treatment', 'description' => 'Inpatient sleep restoration and circadian-rhythm reset.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/insomnia-causes-symptoms-and-treatment/' ],
					[ 'title' => 'Codependency treatment', 'description' => 'Therapy for relational dependence and codependent patterns.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/codependency-causes-and-treatment/' ],
					[ 'title' => 'Gambling addiction', 'description' => 'Treatment for compulsive gambling and process addiction.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/gambling-symptoms-and-treatment/' ],
				],
				4, 'cream'
			);

			// 6. Eating disorders (white)
			$blocks .= rehab_block_cards_grid(
				'Eating disorder rehab',
				'Inpatient treatment for disordered eating and food-related conditions.',
				[
					[ 'title' => 'Anorexia treatment', 'description' => 'Medically supervised treatment for anorexia nervosa.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/anorexia-rehab/' ],
					[ 'title' => 'Bulimia treatment', 'description' => 'Therapy and nutritional rehabilitation for bulimia nervosa.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/bulimia-rehab/' ],
					[ 'title' => 'Binge eating & overeating', 'description' => 'Behavioral therapy for binge-eating disorder and compulsive overeating.', 'imageUrl' => '', 'imageAlt' => '', 'url' => '/binge-eating-disorder/' ],
				],
				3, 'white'
			);

			// 7. CTA
			$blocks .= rehab_block_cta( [
				'heading'    => 'Don\'t see your specific situation listed?',
				'body'       => 'Get in touch — we treat dual-diagnosis and complex co-occurring conditions, and will be straight with you about whether our program is the right fit.',
			] );

			$res = wp_update_post( [ 'ID' => $page_id, 'post_content' => wp_slash( $blocks ) ], true );
			echo is_wp_error( $res ) ? "ERR: " . $res->get_error_message() . "\n" : "OK rebuilt all-treatments page (" . strlen( $blocks ) . " bytes)\n";
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
} );
