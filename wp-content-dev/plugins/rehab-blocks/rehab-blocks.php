<?php
/**
 * Plugin Name: Rehab Blocks
 * Description: Native Gutenberg block library for the rehab clinic platform.
 * Version: 0.1.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Rehab Platform
 * License: GPL-2.0+
 * Text Domain: rehab-blocks
 *
 * @package RehabBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REHAB_BLOCKS_VERSION', '0.1.0' );
define( 'REHAB_BLOCKS_PATH', plugin_dir_path( __FILE__ ) );
define( 'REHAB_BLOCKS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Register the block category and all blocks.
 */
function rehab_blocks_register(): void {
	$blocks_dir = REHAB_BLOCKS_PATH . 'build';
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}
	foreach ( glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $block_path ) {
		if ( file_exists( $block_path . '/block.json' ) ) {
			register_block_type( $block_path );
		}
	}
}
add_action( 'init', 'rehab_blocks_register' );

/**
 * Add a "Rehab" block category so all our blocks group together in the inserter.
 */
function rehab_blocks_category( array $categories ): array {
	return array_merge(
		[
			[
				'slug'  => 'rehab',
				'title' => __( 'Rehab Blocks', 'rehab-blocks' ),
				'icon'  => 'heart',
			],
		],
		$categories
	);
}
add_filter( 'block_categories_all', 'rehab_blocks_category' );

/**
 * Register the `faq` custom post type. The DB already has 85 FAQ records
 * (legacy from the previous theme); registering the type here keeps them
 * queryable, editable in admin, and accessible via REST so the FAQ block's
 * picker UI can list them.
 *
 * Uses post_type_exists() guard so this is a no-op if another plugin
 * (e.g. an FAQ plugin we install later) registers it first.
 */
function rehab_blocks_register_faq_cpt(): void {
	if ( post_type_exists( 'faq' ) ) {
		return;
	}
	register_post_type( 'faq', [
		'labels' => [
			'name'          => __( 'FAQs', 'rehab-blocks' ),
			'singular_name' => __( 'FAQ', 'rehab-blocks' ),
			'menu_name'     => __( 'FAQs', 'rehab-blocks' ),
			'add_new'       => __( 'Add FAQ', 'rehab-blocks' ),
			'add_new_item'  => __( 'Add New FAQ', 'rehab-blocks' ),
			'edit_item'     => __( 'Edit FAQ', 'rehab-blocks' ),
			'new_item'      => __( 'New FAQ', 'rehab-blocks' ),
			'view_item'     => __( 'View FAQ', 'rehab-blocks' ),
			'search_items'  => __( 'Search FAQs', 'rehab-blocks' ),
			'all_items'     => __( 'All FAQs', 'rehab-blocks' ),
		],
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true, // needed by the FAQ block picker
		'rest_base'           => 'faq',
		'has_archive'         => false,
		'rewrite'             => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'menu_icon'           => 'dashicons-editor-help',
		'menu_position'       => 24,
		'capability_type'     => 'post',
		'supports'            => [ 'title', 'editor', 'custom-fields', 'revisions' ],
	] );
}
add_action( 'init', 'rehab_blocks_register_faq_cpt' );
