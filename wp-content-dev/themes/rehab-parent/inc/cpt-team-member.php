<?php
/**
 * Team Member CPT.
 *
 * The single source of truth for team members (REH-72). Public, served at
 * `/team/{slug}/` (rewrite base `team`, matching the legacy page URLs so those
 * URLs are preserved). Powers both the Team grid (queried dynamically) and the
 * author/reviewer byline boxes on blog posts. Fields: title = name,
 * featured image = portrait, editor content = bio, plus the meta below.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Discipline vocabulary — mirrors the Team grid filter chips. cat => label.
 */
function rehab_team_disciplines(): array {
	return [
		'lead'     => __( 'Leadership', 'rehab-parent' ),
		'clinical' => __( 'Clinical', 'rehab-parent' ),
		'therapy'  => __( 'Therapy', 'rehab-parent' ),
		'nursing'  => __( 'Nursing', 'rehab-parent' ),
		'wellness' => __( 'Wellness', 'rehab-parent' ),
		'support'  => __( 'Support', 'rehab-parent' ),
		'writers'  => __( 'Medical writers', 'rehab-parent' ),
	];
}

add_action(
	'init',
	static function (): void {
		register_post_type(
			'team_member',
			[
				'labels'              => [
					'name'                  => __( 'Team', 'rehab-parent' ),
					'singular_name'         => __( 'Team Member', 'rehab-parent' ),
					'add_new'               => __( 'Add New Member', 'rehab-parent' ),
					'add_new_item'          => __( 'Add New Member', 'rehab-parent' ),
					'edit_item'             => __( 'Edit Member', 'rehab-parent' ),
					'view_item'             => __( 'View Member', 'rehab-parent' ),
					'search_items'          => __( 'Search Members', 'rehab-parent' ),
					'all_items'             => __( 'All Members', 'rehab-parent' ),
					'not_found'             => __( 'No members found.', 'rehab-parent' ),
					'menu_name'             => __( 'Team', 'rehab-parent' ),
				],
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'has_archive'         => false,
				'rewrite'             => [ 'slug' => 'team', 'with_front' => false ],
				'capability_type'     => 'post',
				'menu_icon'           => 'dashicons-groups',
				'menu_position'       => 20,
				'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
				'rest_base'           => 'team-members',
			]
		);

		// Structured member fields, exposed to REST for the block editor.
		$string_meta = [ '_rehab_member_role', '_rehab_member_credentials', '_rehab_member_discipline', '_rehab_member_first', '_rehab_member_quote', '_rehab_member_quote_src' ];
		foreach ( $string_meta as $key ) {
			register_post_meta( 'team_member', $key, [
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => static fn() => current_user_can( 'edit_posts' ),
			] );
		}
		register_post_meta( 'team_member', '_rehab_member_order', [
			'type' => 'integer', 'single' => true, 'show_in_rest' => true,
			'auth_callback' => static fn() => current_user_can( 'edit_posts' ),
		] );
		register_post_meta( 'team_member', '_rehab_member_featured', [
			'type' => 'boolean', 'single' => true, 'show_in_rest' => true,
			'auth_callback' => static fn() => current_user_can( 'edit_posts' ),
		] );
		register_post_meta( 'team_member', '_rehab_member_byline_only', [
			'type' => 'boolean', 'single' => true, 'show_in_rest' => true,
			'auth_callback' => static fn() => current_user_can( 'edit_posts' ),
		] );

		// The /team/ page (grid landing) and its legacy child pages otherwise
		// swallow every /team/* request before the CPT rewrite is reached. This
		// top-priority rule routes /team/{slug}/ to the member CPT; /team/ itself
		// (no trailing segment) still resolves to the grid page.
		add_rewrite_rule( '^team/([^/]+)/?$', 'index.php?team_member=$matches[1]', 'top' );
	}
);

/**
 * Flush rewrite rules once after the CPT went public (REH-72), so `/team/{slug}/`
 * resolves without a manual Settings → Permalinks save. Runs on `wp_loaded` —
 * after every post type + rewrite rule (across mu-plugins and the theme) has
 * registered — so the flush actually captures the /team/ CPT rules.
 */
add_action(
	'wp_loaded',
	static function (): void {
		if ( get_option( 'rehab_team_cpt_rewrite_v' ) !== '5' ) {
			flush_rewrite_rules( false );
			update_option( 'rehab_team_cpt_rewrite_v', '5' );
		}
	}
);

/**
 * Meta box: role / credentials for each team member.
 */
add_action(
	'add_meta_boxes',
	static function (): void {
		add_meta_box(
			'rehab_member_details',
			__( 'Member details', 'rehab-parent' ),
			'rehab_parent_member_meta_box',
			'team_member',
			'side',
			'default'
		);
	}
);

function rehab_parent_member_meta_box( WP_Post $post ): void {
	$role        = get_post_meta( $post->ID, '_rehab_member_role', true );
	$credentials = get_post_meta( $post->ID, '_rehab_member_credentials', true );
	$order       = get_post_meta( $post->ID, '_rehab_member_order', true );
	$discipline  = get_post_meta( $post->ID, '_rehab_member_discipline', true );
	$featured    = get_post_meta( $post->ID, '_rehab_member_featured', true );
	$byline_only = get_post_meta( $post->ID, '_rehab_member_byline_only', true );
	$first       = get_post_meta( $post->ID, '_rehab_member_first', true );
	$quote       = get_post_meta( $post->ID, '_rehab_member_quote', true );
	$quote_src   = get_post_meta( $post->ID, '_rehab_member_quote_src', true );
	wp_nonce_field( 'rehab_member_meta', 'rehab_member_meta_nonce' );
	?>
	<p>
		<label><strong><?php esc_html_e( 'Feature on team page', 'rehab-parent' ); ?></strong></label><br>
		<label><input type="checkbox" name="rehab_member_featured" value="1" <?php checked( (bool) $featured ); ?>> <?php esc_html_e( 'Show this member in the /team/ grid', 'rehab-parent' ); ?></label>
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Byline only', 'rehab-parent' ); ?></strong></label><br>
		<label><input type="checkbox" name="rehab_member_byline_only" value="1" <?php checked( (bool) $byline_only ); ?>> <?php esc_html_e( 'No public /team/ profile — use for blog bylines only', 'rehab-parent' ); ?></label>
	</p>
	<p>
		<label for="rehab_member_discipline"><strong><?php esc_html_e( 'Discipline', 'rehab-parent' ); ?></strong></label>
		<select name="rehab_member_discipline" id="rehab_member_discipline" class="widefat">
			<option value=""><?php esc_html_e( '— Select —', 'rehab-parent' ); ?></option>
			<?php foreach ( rehab_team_disciplines() as $cat => $label ) : ?>
				<option value="<?php echo esc_attr( $cat ); ?>" <?php selected( $discipline, $cat ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="rehab_member_role"><strong><?php esc_html_e( 'Role / title', 'rehab-parent' ); ?></strong></label>
		<input type="text" name="rehab_member_role" id="rehab_member_role" value="<?php echo esc_attr( $role ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g. Founder, Psychiatrist', 'rehab-parent' ); ?>">
	</p>
	<p>
		<label for="rehab_member_credentials"><strong><?php esc_html_e( 'Credentials', 'rehab-parent' ); ?></strong></label>
		<input type="text" name="rehab_member_credentials" id="rehab_member_credentials" value="<?php echo esc_attr( $credentials ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g. MD, PsyD', 'rehab-parent' ); ?>">
	</p>
	<p>
		<label for="rehab_member_first"><strong><?php esc_html_e( 'First name', 'rehab-parent' ); ?></strong></label>
		<input type="text" name="rehab_member_first" id="rehab_member_first" value="<?php echo esc_attr( $first ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Used in the profile form (auto from name if blank)', 'rehab-parent' ); ?>">
	</p>
	<p>
		<label for="rehab_member_quote"><strong><?php esc_html_e( 'Pull quote (optional)', 'rehab-parent' ); ?></strong></label>
		<textarea name="rehab_member_quote" id="rehab_member_quote" class="widefat" rows="2"><?php echo esc_textarea( $quote ); ?></textarea>
	</p>
	<p>
		<label for="rehab_member_quote_src"><strong><?php esc_html_e( 'Quote attribution', 'rehab-parent' ); ?></strong></label>
		<input type="text" name="rehab_member_quote_src" id="rehab_member_quote_src" value="<?php echo esc_attr( $quote_src ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Defaults to the member name', 'rehab-parent' ); ?>">
	</p>
	<p>
		<label for="rehab_member_order"><strong><?php esc_html_e( 'Sort order', 'rehab-parent' ); ?></strong></label>
		<input type="number" name="rehab_member_order" id="rehab_member_order" value="<?php echo esc_attr( $order ); ?>" class="small-text" placeholder="0">
		<br><small><?php esc_html_e( 'Lower number sorts earlier in team grids.', 'rehab-parent' ); ?></small>
	</p>
	<?php
}

add_action(
	'save_post_team_member',
	static function ( int $post_id ): void {
		if ( ! isset( $_POST['rehab_member_meta_nonce'] ) || ! wp_verify_nonce( $_POST['rehab_member_meta_nonce'], 'rehab_member_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( [ 'rehab_member_role', 'rehab_member_credentials', 'rehab_member_discipline', 'rehab_member_first', 'rehab_member_quote_src', 'rehab_member_order' ] as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, '_' . $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
		update_post_meta( $post_id, '_rehab_member_quote', isset( $_POST['rehab_member_quote'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rehab_member_quote'] ) ) : '' );
		update_post_meta( $post_id, '_rehab_member_featured', isset( $_POST['rehab_member_featured'] ) ? 1 : 0 );
		update_post_meta( $post_id, '_rehab_member_byline_only', isset( $_POST['rehab_member_byline_only'] ) ? 1 : 0 );
	}
);

/**
 * Meta on regular posts: pick a team member as author / medical reviewer.
 */
add_action(
	'add_meta_boxes',
	static function ( string $post_type, $post = null ): void {
		$screens = [ 'post' ];
		// Diamond's articles are pages using template-article.php — surface the
		// byline picker there too, otherwise the migrated author/reviewer can't
		// be edited in wp-admin.
		if ( 'page' === $post_type && $post instanceof WP_Post
			&& 'template-article.php' === get_post_meta( $post->ID, '_wp_page_template', true ) ) {
			$screens[] = 'page';
		}
		add_meta_box(
			'rehab_post_authors',
			__( 'Article author / reviewer', 'rehab-parent' ),
			'rehab_parent_post_authors_meta_box',
			$screens,
			'side',
			'default'
		);
	},
	10,
	2
);

function rehab_parent_post_authors_meta_box( WP_Post $post ): void {
	$author_id   = (int) get_post_meta( $post->ID, '_rehab_author_member', true );
	$reviewer_id = (int) get_post_meta( $post->ID, '_rehab_reviewer_member', true );
	$members     = get_posts( [
		'post_type'      => 'team_member',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'post_status'    => 'publish',
	] );
	wp_nonce_field( 'rehab_post_authors', 'rehab_post_authors_nonce' );
	?>
	<p>
		<label for="rehab_author_member"><strong><?php esc_html_e( 'Author', 'rehab-parent' ); ?></strong></label>
		<select name="rehab_author_member" id="rehab_author_member" class="widefat">
			<option value="0">— <?php esc_html_e( 'None', 'rehab-parent' ); ?> —</option>
			<?php foreach ( $members as $m ) : ?>
				<option value="<?php echo esc_attr( $m->ID ); ?>" <?php selected( $author_id, $m->ID ); ?>>
					<?php echo esc_html( $m->post_title ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="rehab_reviewer_member"><strong><?php esc_html_e( 'Medical reviewer', 'rehab-parent' ); ?></strong></label>
		<select name="rehab_reviewer_member" id="rehab_reviewer_member" class="widefat">
			<option value="0">— <?php esc_html_e( 'None', 'rehab-parent' ); ?> —</option>
			<?php foreach ( $members as $m ) : ?>
				<option value="<?php echo esc_attr( $m->ID ); ?>" <?php selected( $reviewer_id, $m->ID ); ?>>
					<?php echo esc_html( $m->post_title ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

add_action(
	'save_post',
	static function ( int $post_id ): void {
		// Fires for every post type; the nonce gate below means we only ever
		// write when our byline meta box was actually rendered (post + article
		// page screens).
		if ( ! isset( $_POST['rehab_post_authors_nonce'] ) || ! wp_verify_nonce( $_POST['rehab_post_authors_nonce'], 'rehab_post_authors' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( [ 'rehab_author_member', 'rehab_reviewer_member' ] as $key ) {
			$val = isset( $_POST[ $key ] ) ? (int) $_POST[ $key ] : 0;
			update_post_meta( $post_id, '_' . $key, $val );
		}
	}
);
