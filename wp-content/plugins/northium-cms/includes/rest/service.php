<?php
/**
 * GET /northium/v1/service/{slug}    service detail + parent division + related articles
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'northium_rest_register_service' );

function northium_rest_register_service(): void {
	register_rest_route(
		NORTHIUM_REST_NS,
		'/service/(?P<slug>[a-z0-9-]+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'northium_rest_service_detail',
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

function northium_rest_service_detail( WP_REST_Request $request ) {
	$slug    = (string) $request->get_param( 'slug' );
	$service = northium_get_post_by_slug( 'service', $slug );

	if ( ! $service ) {
		return northium_rest_not_found( 'Service not found' );
	}

	$division_id = (int) get_post_meta( $service->ID, 'division_id', true );
	$division    = $division_id ? get_post( $division_id ) : null;

	$related_articles = array();
	if ( $division_id ) {
		$related_articles = get_posts(
			array(
				'post_type'      => 'article',
				'post_status'    => 'publish',
				'numberposts'    => 3,
				'meta_key'       => 'division_id',
				'meta_value'     => $division_id,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'no_found_rows'  => true,
			)
		);
	}

	return northium_rest_response(
		array(
			'service'          => northium_format_service( $service, true ),
			'division'         => $division instanceof WP_Post ? northium_format_division( $division ) : null,
			'related_articles' => array_map(
				static fn( WP_Post $p ): array => northium_format_article( $p ),
				$related_articles
			),
		)
	);
}
