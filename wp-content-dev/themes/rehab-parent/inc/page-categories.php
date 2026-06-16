<?php
/**
 * Page categories.
 *
 * The legacy "diamond" theme associated the core `category` taxonomy with the
 * `page` post type, letting editors file pages under Article / Treatment /
 * Statistics / … . The v3 rebuild dropped that association, but the terms and
 * all ~447 page→category assignments survive in the DB — this re-surfaces them:
 * the Categories metabox on the page editor, the Category column on the Pages
 * list (core adds it via the taxonomy's show_admin_column), and a category
 * filter dropdown for managing the 448 pages.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Associate the built-in category taxonomy with the page post type.
 */
add_action(
	'init',
	static function (): void {
		register_taxonomy_for_object_type( 'category', 'page' );
	}
);

/**
 * Add a Category filter dropdown above the Pages list table.
 */
add_action(
	'restrict_manage_posts',
	static function ( string $post_type ): void {
		if ( 'page' !== $post_type ) {
			return;
		}

		$selected = isset( $_GET['category_name'] )
			? sanitize_text_field( wp_unslash( $_GET['category_name'] ) )
			: '';

		wp_dropdown_categories(
			[
				'show_option_all' => __( 'All categories', 'rehab-parent' ),
				'taxonomy'        => 'category',
				'name'            => 'category_name',
				'value_field'     => 'slug',
				'selected'        => $selected,
				'hierarchical'    => true,
				'show_count'      => true,
				'hide_empty'      => false,
				'orderby'         => 'name',
			]
		);
	}
);
