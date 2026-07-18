<?php
/**
 * Helpers for blog/article rendering — used by single.php and template-article.php.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extract H2 headings from raw post content for the table of contents.
 *
 * @return array<array{id: string, text: string}>
 */
function rehab_parent_extract_toc( string $content ): array {
	$toc = [];
	if ( ! preg_match_all( '/<h2[^>]*>(.+?)<\/h2>/i', $content, $matches ) ) {
		return $toc;
	}
	foreach ( $matches[1] as $heading_html ) {
		$text = trim( wp_strip_all_tags( $heading_html ) );
		if ( ! $text ) {
			continue;
		}
		$toc[] = [ 'id' => sanitize_title( $text ), 'text' => $text ];
	}
	return $toc;
}

/**
 * Inject id="…" anchors on H2 headings so ToC links work.
 */
function rehab_parent_inject_heading_ids( string $html ): string {
	return (string) preg_replace_callback(
		'/<h2(\s[^>]*)?>(.+?)<\/h2>/i',
		static function ( $m ) {
			$attrs   = $m[1] ?? '';
			$content = $m[2];
			if ( $attrs && stripos( $attrs, 'id=' ) !== false ) {
				return $m[0];
			}
			$id = sanitize_title( wp_strip_all_tags( $content ) );
			return sprintf( '<h2 id="%s"%s>%s</h2>', esc_attr( $id ), $attrs, $content );
		},
		$html
	);
}

/**
 * Render an author / reviewer box from a team_member CPT post id.
 * Legacy two-line "card" variant. Kept for older templates that still call it.
 */
function rehab_parent_render_author_box( int $member_id, string $label ): void {
	$member = get_post( $member_id );
	if ( ! $member ) {
		return;
	}
	$photo = get_the_post_thumbnail( $member_id, 'thumbnail', [ 'class' => 'rehab-author__photo' ] );
	$role  = get_post_meta( $member_id, '_rehab_member_role', true );
	$name  = get_the_title( $member );
	$link  = get_permalink( $member_id ) ?: '#';
	?>
	<a class="rehab-author" href="<?php echo esc_url( $link ); ?>">
		<span class="rehab-author__label"><?php echo esc_html( $label ); ?></span>
		<div class="rehab-author__body">
			<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="rehab-author__text">
				<p class="rehab-author__name"><?php echo esc_html( $name ); ?></p>
				<?php if ( $role ) : ?>
					<p class="rehab-author__role"><?php echo esc_html( $role ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</a>
	<?php
}

/**
 * Render a credit cell (avatar + label + name + role) for the editorial article
 * header. Used in template-parts/article-page.php for both author and reviewer.
 * Falls back to initials when the team_member CPT has no thumbnail.
 */
function rehab_parent_render_credit_cell( int $member_id, string $label ): void {
	$member = get_post( $member_id );
	if ( ! $member ) {
		return;
	}
	$name     = get_the_title( $member );
	$role     = get_post_meta( $member_id, '_rehab_member_role', true );
	$photo    = get_the_post_thumbnail( $member_id, 'thumbnail', [ 'class' => 'rehab-credit__avatar-img', 'alt' => $name ] );
	$initials = rehab_parent_initials( $name );
	// Byline-only members (external reviewers, no public profile) render unlinked.
	$byline_only = (bool) get_post_meta( $member_id, '_rehab_member_byline_only', true );
	$link        = ( ! $byline_only && 'publish' === $member->post_status ) ? ( get_permalink( $member_id ) ?: '' ) : '';
	$tag         = $link ? 'a' : 'span';
	?>
	<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="rehab-credit__cell<?php echo $link ? '' : ' rehab-credit__cell--static'; ?>"<?php echo $link ? ' href="' . esc_url( $link ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<span class="rehab-credit__avatar">
			<?php if ( $photo ) : ?>
				<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php else : ?>
				<span class="rehab-credit__initials" aria-hidden="true"><?php echo esc_html( $initials ); ?></span>
			<?php endif; ?>
		</span>
		<span class="rehab-credit__text">
			<span class="rehab-credit__label"><?php echo esc_html( $label ); ?></span>
			<span class="rehab-credit__name"><?php echo esc_html( $name ); ?></span>
			<?php if ( $role ) : ?>
				<span class="rehab-credit__role"><?php echo esc_html( $role ); ?></span>
			<?php endif; ?>
		</span>
	</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php
}

/**
 * Build initials from a person's display name. Drops a leading honorific and a
 * trailing credential suffix so "Dr. Harshi Dhingra" → "HD" and
 * "Asif Baliyan, MD" → "AB" (not "AM" from the dangling "MD").
 */
function rehab_parent_initials( string $name ): string {
	$clean = preg_replace( '/^(Dr\.?|Mr\.?|Mrs\.?|Ms\.?|Prof\.?)\s+/i', '', trim( $name ) );
	$clean = preg_replace( '/\s*,.*$/', '', (string) $clean ); // drop ", MD" / ", PhD" etc.
	$parts = preg_split( '/\s+/', (string) $clean );
	if ( ! $parts ) {
		return '';
	}
	$first = mb_substr( $parts[0], 0, 1 );
	$last  = count( $parts ) > 1 ? mb_substr( end( $parts ), 0, 1 ) : '';
	return mb_strtoupper( $first . $last );
}

/**
 * Strip a leading wp:image / <figure> block from post content when it points
 * at the post's own featured image. Many legacy articles duplicate the
 * featured image as the first inline block; with the editorial template we
 * already render the featured image above the prose, so the inline one would
 * appear twice.
 *
 * Only removes the block if it's the very first content (we allow leading
 * whitespace / newlines). Image position is determined by either the
 * wp:image's `id` attribute or the wp-image-{id} class on the inner <img>.
 */
function rehab_parent_strip_leading_duplicate_image( string $content, int $thumb_id ): string {
	if ( $thumb_id < 1 ) {
		return $content;
	}
	$trimmed = ltrim( $content );

	// Block-editor variant: <!-- wp:image {...} --> … <!-- /wp:image -->.
	// Strip regardless of the block's `id`: the template already renders the
	// featured hero, and the migrated pages' leading blocks are usually the
	// same graphic as a DIFFERENT attachment (or carry no id at all), so an
	// id-equality check silently misses most duplicates (REH-167).
	if ( preg_match( '/^<!--\s*wp:image[\s{]/', $trimmed ) ) {
		$end = strpos( $trimmed, '<!-- /wp:image -->' );
		if ( $end !== false ) {
			return ltrim( substr( $trimmed, $end + strlen( '<!-- /wp:image -->' ) ) );
		}
	}

	// Classic / non-block variant: a leading <figure><img …></figure>.
	if ( preg_match( '/^<figure\b[^>]*>(?:(?!<\/figure>).)*<img\b(?:(?!<\/figure>).)*<\/figure>/s', $trimmed ) ) {
		return ltrim( preg_replace( '/^<figure\b[^>]*>.*?<\/figure>/s', '', $trimmed, 1 ) );
	}

	return $content;
}

/**
 * Resolve "related articles" for the sidebar. Honours the legacy ACF
 * `related_ids` postmeta (a serialized array of page IDs) when set; otherwise
 * falls back to the most recently modified article-template pages in the same
 * primary category. Returns at most $limit valid published WP_Post objects.
 *
 * @return WP_Post[]
 */
function rehab_parent_resolve_related( int $post_id, int $limit = 4 ): array {
	$ids = get_post_meta( $post_id, 'related_ids', true );
	if ( is_string( $ids ) ) {
		// Some ACF setups store it as a CSV string.
		$ids = array_filter( array_map( 'intval', preg_split( '/[,\s]+/', $ids ) ) );
	}
	if ( ! is_array( $ids ) ) {
		$ids = [];
	}
	$ids = array_values( array_unique( array_map( 'intval', $ids ) ) );

	$posts = [];
	foreach ( $ids as $id ) {
		if ( $id === $post_id ) {
			continue;
		}
		$p = get_post( $id );
		if ( $p && $p->post_status === 'publish' ) {
			$posts[] = $p;
		}
		if ( count( $posts ) >= $limit ) {
			break;
		}
	}

	if ( count( $posts ) >= $limit ) {
		return $posts;
	}

	// Fallback: same primary category, most-recently-modified, excluding ourselves
	// and anything already returned.
	$exclude = array_merge( [ $post_id ], wp_list_pluck( $posts, 'ID' ) );
	$cat_id  = (int) get_post_meta( $post_id, 'rank_math_primary_category', true );

	$args = [
		'post_type'      => 'page',
		'posts_per_page' => $limit - count( $posts ),
		'post__not_in'   => $exclude,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'template-article.php',
		'orderby'        => 'modified',
		'order'          => 'DESC',
	];
	if ( $cat_id > 0 ) {
		$args['category__in'] = [ $cat_id ];
	}

	$extra = get_posts( $args );
	return array_merge( $posts, $extra );
}
