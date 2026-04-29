<?php
/**
 * GET /northium/v1/advisors           list all
 * GET /northium/v1/advisor/{slug}     advisor detail + their divisions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'northium_rest_register_advisors' );

function northium_rest_register_advisors(): void {

	register_rest_route(
		NORTHIUM_REST_NS,
		'/advisors',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'northium_rest_advisors_index',
		)
	);

	register_rest_route(
		NORTHIUM_REST_NS,
		'/advisor/(?P<slug>[a-z0-9-]+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'northium_rest_advisor_detail',
			'args'                => array(
				'slug' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_title',
				),
			),
		)
	);
}

function northium_rest_advisors_index( WP_REST_Request $request ) {
	$posts = get_posts(
		array(
			'post_type'      => 'advisor',
			'post_status'    => 'publish',
			'numberposts'    => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);

	$items = array_map(
		static fn( WP_Post $p ): array => northium_format_advisor( $p ),
		$posts
	);

	return northium_rest_response( array( 'items' => $items ) );
}

function northium_rest_advisor_detail( WP_REST_Request $request ) {
	$slug    = (string) $request->get_param( 'slug' );
	$advisor = northium_get_post_by_slug( 'advisor', $slug );

	if ( ! $advisor ) {
		return northium_rest_not_found( 'Advisor not found' );
	}

	$division_ids = array_map( 'intval', (array) get_post_meta( $advisor->ID, 'division_ids', true ) );
	$division_posts = array_values(
		array_filter(
			array_map( 'get_post', $division_ids ),
			static fn( $p ): bool => $p instanceof WP_Post && $p->post_status === 'publish'
		)
	);

	return northium_rest_response(
		array(
			'advisor'   => northium_format_advisor( $advisor, true ),
			'divisions' => array_map(
				static fn( WP_Post $p ): array => northium_format_division( $p ),
				$division_posts
			),
		)
	);
}
