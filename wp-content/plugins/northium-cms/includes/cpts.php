<?php
/**
 * Custom Post Types for Northium CMS.
 *
 * Five CPTs map directly to the content model documented in docs/03-content-model.md:
 *   - Division   the 5 business units (Wealth, Insurance, Group Benefits, HCM, Creative)
 *   - Service    offerings inside a division
 *   - Advisor    team members
 *   - Article    insights / blog posts
 *   - Campaign   marketing landing pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'northium_cms_register_cpts', 0 );

function northium_cms_register_cpts(): void {

	register_post_type(
		'division',
		array(
			'labels'        => array(
				'name'               => __( 'Divisions', 'northium-cms' ),
				'singular_name'      => __( 'Division', 'northium-cms' ),
				'menu_name'          => __( 'Divisions', 'northium-cms' ),
				'add_new'            => __( 'Add New', 'northium-cms' ),
				'add_new_item'       => __( 'Add New Division', 'northium-cms' ),
				'edit_item'          => __( 'Edit Division', 'northium-cms' ),
				'new_item'           => __( 'New Division', 'northium-cms' ),
				'view_item'          => __( 'View Division', 'northium-cms' ),
				'search_items'       => __( 'Search Divisions', 'northium-cms' ),
				'not_found'          => __( 'No divisions found', 'northium-cms' ),
				'not_found_in_trash' => __( 'No divisions found in trash', 'northium-cms' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'rest_base'     => 'division',
			'has_archive'   => 'divisions',
			'rewrite'       => array(
				'slug'       => 'divisions',
				'with_front' => false,
			),
			'menu_position' => 20,
			'menu_icon'     => 'dashicons-networking',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'custom-fields' ),
		)
	);

	register_post_type(
		'service',
		array(
			'labels'        => array(
				'name'               => __( 'Services', 'northium-cms' ),
				'singular_name'      => __( 'Service', 'northium-cms' ),
				'menu_name'          => __( 'Services', 'northium-cms' ),
				'add_new_item'       => __( 'Add New Service', 'northium-cms' ),
				'edit_item'          => __( 'Edit Service', 'northium-cms' ),
				'new_item'           => __( 'New Service', 'northium-cms' ),
				'view_item'          => __( 'View Service', 'northium-cms' ),
				'search_items'       => __( 'Search Services', 'northium-cms' ),
				'not_found'          => __( 'No services found', 'northium-cms' ),
				'not_found_in_trash' => __( 'No services found in trash', 'northium-cms' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'rest_base'     => 'service',
			'has_archive'   => 'services',
			'rewrite'       => array(
				'slug'       => 'services',
				'with_front' => false,
			),
			'menu_position' => 21,
			'menu_icon'     => 'dashicons-portfolio',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		)
	);

	register_post_type(
		'advisor',
		array(
			'labels'        => array(
				'name'               => __( 'Advisors', 'northium-cms' ),
				'singular_name'      => __( 'Advisor', 'northium-cms' ),
				'menu_name'          => __( 'Advisors', 'northium-cms' ),
				'add_new_item'       => __( 'Add New Advisor', 'northium-cms' ),
				'edit_item'          => __( 'Edit Advisor', 'northium-cms' ),
				'new_item'           => __( 'New Advisor', 'northium-cms' ),
				'view_item'          => __( 'View Advisor', 'northium-cms' ),
				'search_items'       => __( 'Search Advisors', 'northium-cms' ),
				'not_found'          => __( 'No advisors found', 'northium-cms' ),
				'not_found_in_trash' => __( 'No advisors found in trash', 'northium-cms' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'rest_base'     => 'advisor',
			'has_archive'   => 'advisors',
			'rewrite'       => array(
				'slug'       => 'advisors',
				'with_front' => false,
			),
			'menu_position' => 22,
			'menu_icon'     => 'dashicons-businessperson',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		)
	);

	register_post_type(
		'article',
		array(
			'labels'        => array(
				'name'               => __( 'Articles', 'northium-cms' ),
				'singular_name'      => __( 'Article', 'northium-cms' ),
				'menu_name'          => __( 'Articles', 'northium-cms' ),
				'add_new_item'       => __( 'Add New Article', 'northium-cms' ),
				'edit_item'          => __( 'Edit Article', 'northium-cms' ),
				'new_item'           => __( 'New Article', 'northium-cms' ),
				'view_item'          => __( 'View Article', 'northium-cms' ),
				'search_items'       => __( 'Search Articles', 'northium-cms' ),
				'not_found'          => __( 'No articles found', 'northium-cms' ),
				'not_found_in_trash' => __( 'No articles found in trash', 'northium-cms' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'rest_base'     => 'article',
			'has_archive'   => 'insights',
			'rewrite'       => array(
				'slug'       => 'insights',
				'with_front' => false,
			),
			'menu_position' => 23,
			'menu_icon'     => 'dashicons-edit-page',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'custom-fields' ),
		)
	);

	register_post_type(
		'campaign',
		array(
			'labels'        => array(
				'name'               => __( 'Campaigns', 'northium-cms' ),
				'singular_name'      => __( 'Campaign', 'northium-cms' ),
				'menu_name'          => __( 'Campaigns', 'northium-cms' ),
				'add_new_item'       => __( 'Add New Campaign', 'northium-cms' ),
				'edit_item'          => __( 'Edit Campaign', 'northium-cms' ),
				'new_item'           => __( 'New Campaign', 'northium-cms' ),
				'view_item'          => __( 'View Campaign', 'northium-cms' ),
				'search_items'       => __( 'Search Campaigns', 'northium-cms' ),
				'not_found'          => __( 'No campaigns found', 'northium-cms' ),
				'not_found_in_trash' => __( 'No campaigns found in trash', 'northium-cms' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'rest_base'     => 'campaign',
			'has_archive'   => 'campaigns',
			'rewrite'       => array(
				'slug'       => 'campaigns',
				'with_front' => false,
			),
			'menu_position' => 24,
			'menu_icon'     => 'dashicons-megaphone',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		)
	);
}
