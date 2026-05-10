<?php
/**
 * Update the homepage's team block with Diamond's actual team members.
 * Photos live in the Diamond child theme under assets/img/team/.
 */

$theme_uri = get_stylesheet_directory_uri() . '/assets/img/team';

$members = [
	[ 'name' => 'Theo &amp; Panwadee de Vries', 'role' => 'Founders', 'photo' => 'theo-panwadee-de-vries.avif' ],
	[ 'name' => 'Sergio Pereira', 'role' => 'Rehab Director', 'photo' => 'sergio-pereira.avif' ],
	[ 'name' => 'Jiraporn Takonchai', 'role' => 'General Manager', 'photo' => 'jiraporn-takonchai.avif' ],
	[ 'name' => 'Dr. Roshan Fernando', 'role' => 'Psychiatrist', 'photo' => 'roshan-fernando.avif' ],
	[ 'name' => 'Wei Ling', 'role' => 'Psychotherapist', 'photo' => 'wei-ling.avif' ],
	[ 'name' => 'Augustine D&rsquo;Ewes', 'role' => 'Clinical Supervisor', 'photo' => 'augustine-dewes.avif' ],
];

$member_blocks = '';
foreach ( $members as $m ) {
	$photo_url = "$theme_uri/{$m['photo']}";
	$attrs = wp_json_encode( [
		'imageUrl' => $photo_url,
		'imageAlt' => wp_strip_all_tags( $m['name'] ),
		'name'     => $m['name'],
		'role'     => $m['role'],
	] );
	$member_blocks .= sprintf(
		'<!-- wp:rehab/team-member %s -->' . "\n" .
		'<div class="wp-block-rehab-team-member rehab-team-member"><img class="rehab-team-member__photo" src="%s" alt="%s" loading="lazy"/><div class="rehab-team-member__overlay"><h3 class="rehab-team-member__name">%s</h3><p class="rehab-team-member__title">%s</p></div></div>' . "\n" .
		'<!-- /wp:rehab/team-member -->' . "\n",
		$attrs,
		esc_url( $photo_url ),
		esc_attr( wp_strip_all_tags( $m['name'] ) ),
		$m['name'],
		$m['role']
	);
}

$team_block = sprintf(
	'<!-- wp:rehab/team {"heading":"The Diamond Rehab team","columns":3} -->' . "\n" .
	'<section class="wp-block-rehab-team rehab-team rehab-bg-white rehab-team--cols-3"><div class="rehab-container"><header class="rehab-team__header"><h2 class="rehab-heading rehab-heading--lg">The Diamond Rehab team</h2></header><div class="rehab-team__grid">%s</div></div></section>' . "\n" .
	'<!-- /wp:rehab/team -->',
	$member_blocks
);

// Pages to update with the new team block in place of the placeholder
$page_ids = [ 6, 722 ]; // homepage and team page

foreach ( $page_ids as $page_id ) {
	$post = get_post( $page_id );
	if ( ! $post ) {
		echo "MISSING $page_id" . PHP_EOL;
		continue;
	}

	// Find existing rehab/team block in post_content and replace it
	$content = $post->post_content;
	$pattern = '/<!--\s*wp:rehab\/team(?:\s+\{[^}]*\})?\s*-->.*?<!--\s*\/wp:rehab\/team\s*-->/is';
	if ( preg_match( $pattern, $content ) ) {
		$content = preg_replace( $pattern, $team_block, $content, 1 );
	} else {
		// No existing team block, append before footer CTA
		$content .= "\n" . $team_block;
	}

	$result = wp_update_post( [
		'ID'           => $page_id,
		'post_content' => $content,
	], true );
	if ( is_wp_error( $result ) ) {
		echo "FAIL $page_id: " . $result->get_error_message() . PHP_EOL;
	} else {
		echo "OK $page_id (team block injected)" . PHP_EOL;
	}
}
