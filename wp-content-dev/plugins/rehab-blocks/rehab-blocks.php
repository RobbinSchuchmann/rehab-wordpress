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
