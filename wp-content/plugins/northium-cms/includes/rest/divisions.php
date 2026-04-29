<?php
/**
 * GET /northium/v1/divisions          compact index
 * GET /northium/v1/division/{slug}    full hub: division + services + advisors + articles + campaigns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'northium_rest_register_divisions' );

function northium_rest_register_divisions(): void {

	register_rest_route(
		NORTHIUM_REST_NS,
		'/divisions',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'northium_rest_divisions_index',
		)
	);

	register_rest_route(
		NORTHIUM_REST_NS,
		'/division/(?P<slug>[a-z0-9-]+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'northium_rest_division_hub',
			'args'                => array(
				'slug' => array(
					'required' => true,
					'type'     => 'string',
					'sanitize_callback' => 'sanitize_title',
				),
			),
		)
	);
}

function northium_rest_divisions_index( WP_REST_Request $request ) {
	$posts = get_posts(
		array(
			'post_type'      => 'division',
			'post_status'    => 'publish',
			'numberposts'    => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);

	$items = array_map(
		static fn( WP_Post $post ): array => northium_format_division( $post ),
		$posts
	);

	return northium_rest_response( array( 'items' => $items ) );
}

function northium_rest_division_hub( WP_REST_Request $request ) {
	$slug     = (string) $request->get_param( 'slug' );
	$division = northium_get_post_by_slug( 'division', $slug );

	if ( ! $division ) {
		return northium_rest_not_found( 'Division not found' );
	}

	$division_id = $division->ID;

	$services = get_posts(
		array(
			'post_type'      => 'service',
			'post_status'    => 'publish',
			'numberposts'    => -1,
			'meta_key'       => 'division_id',
			'meta_value'     => $division_id,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);

	$advisors = northium_advisors_for_division( $division_id );

	$articles = get_posts(
		array(
			'post_type'      => 'article',
			'post_status'    => 'publish',
			'numberposts'    => 6,
			'meta_key'       => 'division_id',
			'meta_value'     => $division_id,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		)
	);

	$campaigns = get_posts(
		array(
			'post_type'      => 'campaign',
			'post_status'    => 'publish',
			'numberposts'    => -1,
			'meta_key'       => 'target_division_id',
			'meta_value'     => $division_id,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		)
	);

	return northium_rest_response(
		array(
			'division'  => northium_format_division( $division, true ),
			'services'  => array_map( static fn( WP_Post $p ): array => northium_format_service( $p ), $services ),
			'advisors'  => array_map( static fn( WP_Post $p ): array => northium_format_advisor( $p ), $advisors ),
			'articles'  => array_map( static fn( WP_Post $p ): array => northium_format_article( $p ), $articles ),
			'campaigns' => array_map( static fn( WP_Post $p ): array => northium_format_campaign( $p ), $campaigns ),
		)
	);
}
