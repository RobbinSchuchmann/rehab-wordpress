<?php
/**
 * Single Team Member template (REH-72).
 *
 * Renders a member profile at /team/{slug}/ by feeding the CPT fields into the
 * existing `rehab/team-profile` block, so the design + enquiry form + styles are
 * reused verbatim. Fields: title = name, featured image = portrait, editor
 * content = bio, plus the `_rehab_member_*` meta.
 *
 * @package RehabParent
 */

// Byline-only members (e.g. external medical reviewers) have no public profile —
// send their /team/{slug}/ URL to the team page.
if ( have_posts() && get_post_meta( get_queried_object_id(), '_rehab_member_byline_only', true ) ) {
	wp_safe_redirect( home_url( '/team/' ), 301 );
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$id = get_the_ID();

	// No theme breadcrumb here — the team-profile block renders its own
	// Home / Team / {name} crumb, and the two stacked (REH-144). The
	// BreadcrumbList schema still ships via the Rank Math filter.

	// Bio: the member's editor content, flattened to plain paragraphs the
	// team-profile block expects (bios are plain prose, no rich formatting).
	$bio_html = apply_filters( 'the_content', get_the_content() );
	if ( preg_match_all( '/<p[^>]*>(.*?)<\/p>/s', $bio_html, $m ) && ! empty( $m[1] ) ) {
		$bio = implode( "\n\n", array_map( 'wp_strip_all_tags', $m[1] ) );
	} else {
		$bio = trim( wp_strip_all_tags( $bio_html ) );
	}

	$photo_id  = get_post_thumbnail_id( $id );
	$photo_url = $photo_id ? wp_get_attachment_image_url( $photo_id, 'full' ) : '';
	$photo_alt = $photo_id ? (string) get_post_meta( $photo_id, '_wp_attachment_image_alt', true ) : '';

	$attrs = [
		'background' => 'white',
		'backText'   => 'All of our team',
		'backUrl'    => '/team/',
		'role'       => (string) get_post_meta( $id, '_rehab_member_role', true ),
		'name'       => get_the_title(),
		'firstName'  => (string) get_post_meta( $id, '_rehab_member_first', true ),
		'photoUrl'   => (string) $photo_url,
		'photoAlt'   => $photo_alt ?: get_the_title(),
		'quote'      => (string) get_post_meta( $id, '_rehab_member_quote', true ),
		'quoteSrc'   => (string) get_post_meta( $id, '_rehab_member_quote_src', true ),
		'bio'        => $bio,
	];

	echo render_block( [
		'blockName'    => 'rehab/team-profile',
		'attrs'        => $attrs,
		'innerBlocks'  => [],
		'innerHTML'    => '',
		'innerContent' => [],
	] ); // phpcs:ignore WordPress.Security.EscapeOutput -- block render output is escaped internally.
endwhile;

get_footer();
