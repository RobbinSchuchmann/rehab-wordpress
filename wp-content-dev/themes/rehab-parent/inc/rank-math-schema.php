<?php
/**
 * Feed custom BreadcrumbList + FAQPage schema into Rank Math's JSON-LD graph.
 *
 * The site renders custom breadcrumbs (template-treatment.php) and a custom
 * `rehab/faq` accordion block — neither of which Rank Math knows about — so out
 * of the box treatment pages ship no BreadcrumbList and their FAQ accordions
 * ship no FAQPage schema. The theme's own JSON-LD (rehab_parent_jsonld) bails
 * when Rank Math is active, deliberately letting Rank Math own schema output.
 *
 * Rather than hand-roll a second <script> (which would duplicate Rank Math's
 * WebPage graph), we hook `rank_math/json_ld` and add our entities to the single
 * graph Rank Math already owns:
 *
 *   - Setting $data['BreadcrumbList'] makes Rank Math's WebPage snippet reference
 *     it automatically via `breadcrumb: {@id}` (schema/snippets/class-webpage.php).
 *   - Adding a FAQPage entity lets Rank Math absorb it natively — its
 *     change_webpage_entity() types the WebPage as [WebPage, FAQPage], copies the
 *     mainEntity over and drops our temp entity (schema/class-frontend.php).
 *
 * Pure render-time code (no DB migration): live once the theme deploys and the
 * cache is purged. (REH-90)
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'rank_math/json_ld', 'rehab_rank_math_extra_schema', 20, 2 );

/**
 * Inject BreadcrumbList (treatment pages) + FAQPage (any rehab/faq block) into
 * Rank Math's JSON-LD graph.
 *
 * @param array $data   Rank Math schema graph (keyed entities).
 * @param mixed $jsonld Rank Math JsonLD instance (unused).
 * @return array
 */
function rehab_rank_math_extra_schema( $data, $jsonld ) {
	if ( ! is_array( $data ) || ! is_singular() ) {
		return $data;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return $data;
	}

	// BreadcrumbList — every singular page, mirroring the visible crumb where the
	// template renders one (REH-98). Guarded so an enabled Rank Math breadcrumb
	// (which also sets this key) wins and we never emit two. The front page is
	// skipped: a single-item Home crumb carries no information.
	if ( ! isset( $data['BreadcrumbList'] ) && ! is_front_page() ) {
		$items = rehab_breadcrumb_schema_items( $post );
		if ( $items ) {
			$data['BreadcrumbList'] = array(
				'@type'           => 'BreadcrumbList',
				'@id'             => get_permalink( $post ) . '#breadcrumb',
				'itemListElement' => $items,
			);
		}
	}

	// FAQPage — from every rehab/faq block on the page. Rank Math folds this into
	// the WebPage entity (see change_webpage_entity), so the key is throwaway.
	$questions = rehab_faq_schema_entities( $post );
	if ( $questions ) {
		$data['rehab_faqpage'] = array(
			'@type'      => 'FAQPage',
			'@id'        => get_permalink( $post ) . '#faq',
			'mainEntity' => $questions,
		);
	}

	return $data;
}

/**
 * Build BreadcrumbList itemListElement mirroring the visible breadcrumb the
 * page's template renders:
 *
 *   - template-treatment.php: Home / Treatments / [Category] / Title,
 *     collapsing to Home / Title on `_rehab_landing_page` pages
 *   - team_member CPT: Home / Team / Name (the team-profile block crumb)
 *   - template-article.php: Home / Articles / [Category] (the article crumb)
 *   - everything else (no visible crumb): Home / [ancestors…] / Title
 *
 * @param WP_Post $post Current post.
 * @return array<int,array>
 */
function rehab_breadcrumb_schema_items( WP_Post $post ) {
	$items = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => 'Home',
			'item'     => home_url( '/' ),
		),
	);
	$pos   = 2;

	$template = get_page_template_slug( $post );

	if ( 'template-treatment.php' === $template ) {
		if ( ! get_post_meta( $post->ID, '_rehab_landing_page', true ) ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => 'Treatments',
				'item'     => home_url( '/all-treatments/' ),
			);

			$cat = function_exists( 'rehab_breadcrumb_category' ) ? rehab_breadcrumb_category( $post->ID ) : '';
			if ( $cat ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos++,
					'name'     => rehab_schema_text( $cat ),
					'item'     => rehab_breadcrumb_category_url( $cat ),
				);
			}
		}
	} elseif ( 'team_member' === $post->post_type ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => 'Team',
			'item'     => home_url( '/team/' ),
		);
	} elseif ( 'template-article.php' === $template ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => 'Articles',
			'item'     => home_url( '/all-articles/' ),
		);

		$cat = function_exists( 'rehab_breadcrumb_category' ) ? rehab_breadcrumb_category( $post->ID ) : '';
		if ( $cat ) {
			// The visible article crumb ends at the category; keep the page
			// itself as the final schema item so the trail stays resolvable.
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => rehab_schema_text( $cat ),
				'item'     => home_url( '/all-articles/' ),
			);
		}
	} else {
		foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor_id ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => rehab_schema_text( get_the_title( $ancestor_id ) ),
				'item'     => get_permalink( $ancestor_id ),
			);
		}
	}

	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $pos,
		'name'     => rehab_schema_text( get_the_title( $post ) ),
		'item'     => get_permalink( $post ),
	);

	return $items;
}

/**
 * Extract FAQ Question entities from every rehab/faq and rehab/faq-page block in
 * the post, mirroring each block's render.php: rehab/faq is CPT-pull (`cptIds`)
 * first, else inline faq-item innerblocks; rehab/faq-page is inline attrs only.
 *
 * @param WP_Post $post Current post.
 * @return array<int,array>
 */
function rehab_faq_schema_entities( WP_Post $post ) {
	if ( ! has_block( 'rehab/faq', $post ) && ! has_block( 'rehab/faq-page', $post ) ) {
		return array();
	}

	$entities = array();
	rehab_collect_faq_blocks( parse_blocks( $post->post_content ), $entities );

	// rehab_faq_question_entity() returns null for empty Q/A pairs.
	return array_values( array_filter( $entities ) );
}

/**
 * Recurse the block tree, collecting Question entities from rehab/faq blocks.
 *
 * @param array $blocks   Parsed blocks.
 * @param array $entities Accumulator (by reference).
 * @return void
 */
function rehab_collect_faq_blocks( array $blocks, array &$entities ) {
	foreach ( $blocks as $block ) {
		if ( 'rehab/faq' === ( $block['blockName'] ?? '' ) ) {
			$cpt_ids = array_filter( array_map( 'intval', (array) ( $block['attrs']['cptIds'] ?? array() ) ) );

			if ( $cpt_ids ) {
				$faqs = get_posts(
					array(
						'post_type'      => 'faq',
						'post__in'       => $cpt_ids,
						'orderby'        => 'post__in',
						'posts_per_page' => -1,
						'no_found_rows'  => true,
					)
				);
				foreach ( $faqs as $faq ) {
					$entities[] = rehab_faq_question_entity( $faq->post_title, $faq->post_content );
				}
			} else {
				foreach ( (array) ( $block['innerBlocks'] ?? array() ) as $inner ) {
					if ( 'rehab/faq-item' !== ( $inner['blockName'] ?? '' ) ) {
						continue;
					}
					$entities[] = rehab_faq_question_entity(
						(string) ( $inner['attrs']['question'] ?? '' ),
						(string) ( $inner['attrs']['answer'] ?? '' )
					);
				}
			}

			// The faq block's children are handled above; no need to recurse in.
			continue;
		}

		// The /faq/ archive block (rehab/faq-page) keeps its Q/A pairs inline in
		// attrs: categories[].items[].q/a (mirrors its render.php).
		if ( 'rehab/faq-page' === ( $block['blockName'] ?? '' ) ) {
			foreach ( (array) ( $block['attrs']['categories'] ?? array() ) as $cat ) {
				foreach ( (array) ( $cat['items'] ?? array() ) as $item ) {
					$entities[] = rehab_faq_question_entity(
						(string) ( $item['q'] ?? '' ),
						(string) ( $item['a'] ?? '' )
					);
				}
			}
			continue;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			rehab_collect_faq_blocks( $block['innerBlocks'], $entities );
		}
	}
}

/**
 * Build one schema.org Question entity, or null when Q or A is empty.
 *
 * @param string $question Question text.
 * @param string $answer   Answer text (tags stripped, matching render.php).
 * @return array|null
 */
function rehab_faq_question_entity( $question, $answer ) {
	$question = rehab_schema_text( $question );
	$answer   = rehab_schema_text( $answer );

	if ( '' === $question || '' === $answer ) {
		return null;
	}

	return array(
		'@type'          => 'Question',
		'name'           => $question,
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $answer,
		),
	);
}

/**
 * Normalise a string for use as a JSON-LD value: decode HTML entities (titles
 * and imported copy often store `&#8217;` / `&amp;` literally) so the schema
 * carries real characters, then strip any tags and trim.
 *
 * @param string $text Raw text.
 * @return string
 */
function rehab_schema_text( $text ) {
	$text = html_entity_decode( (string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	return trim( wp_strip_all_tags( $text ) );
}
