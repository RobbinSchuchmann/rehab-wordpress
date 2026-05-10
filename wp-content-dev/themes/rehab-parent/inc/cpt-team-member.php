<?php
/**
 * Team Member CPT.
 *
 * Private (not publicly queryable as a URL — Diamond doesn't expose individual
 * team-member URLs). Used as a data store for the Team block + author/reviewer
 * boxes on blog posts.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'capability_type'     => 'post',
				'menu_icon'           => 'dashicons-groups',
				'menu_position'       => 20,
				'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
				'rest_base'           => 'team-members',
			]
		);
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
	wp_nonce_field( 'rehab_member_meta', 'rehab_member_meta_nonce' );
	?>
	<p>
		<label for="rehab_member_role"><strong><?php esc_html_e( 'Role / title', 'rehab-parent' ); ?></strong></label>
		<input type="text" name="rehab_member_role" id="rehab_member_role" value="<?php echo esc_attr( $role ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g. Founder, Psychiatrist', 'rehab-parent' ); ?>">
	</p>
	<p>
		<label for="rehab_member_credentials"><strong><?php esc_html_e( 'Credentials', 'rehab-parent' ); ?></strong></label>
		<input type="text" name="rehab_member_credentials" id="rehab_member_credentials" value="<?php echo esc_attr( $credentials ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g. MD, PsyD', 'rehab-parent' ); ?>">
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

		foreach ( [ 'rehab_member_role', 'rehab_member_credentials', 'rehab_member_order' ] as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, '_' . $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
	}
);

/**
 * Meta on regular posts: pick a team member as author / medical reviewer.
 */
add_action(
	'add_meta_boxes',
	static function (): void {
		add_meta_box(
			'rehab_post_authors',
			__( 'Article author / reviewer', 'rehab-parent' ),
			'rehab_parent_post_authors_meta_box',
			'post',
			'side',
			'default'
		);
	}
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
	'save_post_post',
	static function ( int $post_id ): void {
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
