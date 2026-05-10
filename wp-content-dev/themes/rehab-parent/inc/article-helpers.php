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
