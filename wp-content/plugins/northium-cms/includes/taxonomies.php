<?php
/**
 * Taxonomies for Northium CMS.
 *
 *   article_category   editorial categories on Article (Insights, News, Press)
 *   service_tag        cross-cutting tags on Service (Retirement, Tax, Group, etc.)
 *
 * Division relationships are NOT a taxonomy; Division is a CPT (see docs/03-content-model.md).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'northium_cms_register_taxonomies', 0 );

function northium_cms_register_taxonomies(): void {

	register_taxonomy(
		'article_category',
		array( 'article' ),
		array(
			'labels'            => array(
				'name'              => __( 'Article Categories', 'northium-cms' ),
				'singular_name'     => __( 'Article Category', 'northium-cms' ),
				'menu_name'         => __( 'Categories', 'northium-cms' ),
				'all_items'         => __( 'All Categories', 'northium-cms' ),
				'edit_item'         => __( 'Edit Category', 'northium-cms' ),
				'add_new_item'      => __( 'Add New Category', 'northium-cms' ),
				'new_item_name'     => __( 'New Category Name', 'northium-cms' ),
				'search_items'      => __( 'Search Categories', 'northium-cms' ),
				'parent_item'       => __( 'Parent Category', 'northium-cms' ),
				'parent_item_colon' => __( 'Parent Category:', 'northium-cms' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'rest_base'         => 'article_category',
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'       => 'insights/category',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'service_tag',
		array( 'service' ),
		array(
			'labels'            => array(
				'name'          => __( 'Service Tags', 'northium-cms' ),
				'singular_name' => __( 'Service Tag', 'northium-cms' ),
				'menu_name'     => __( 'Tags', 'northium-cms' ),
				'all_items'     => __( 'All Tags', 'northium-cms' ),
				'edit_item'     => __( 'Edit Tag', 'northium-cms' ),
				'add_new_item'  => __( 'Add New Tag', 'northium-cms' ),
				'new_item_name' => __( 'New Tag Name', 'northium-cms' ),
				'search_items'  => __( 'Search Tags', 'northium-cms' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_in_rest'      => true,
			'rest_base'         => 'service_tag',
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'       => 'services/tag',
				'with_front' => false,
			),
		)
	);
}
