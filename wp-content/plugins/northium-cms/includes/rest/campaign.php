<?php
/**
 * GET /northium/v1/campaign/{slug}    campaign + target division
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'northium_rest_register_campaign' );

function northium_rest_register_campaign(): void {
	register_rest_route(
		NORTHIUM_REST_NS,
		'/campaign/(?P<slug>[a-z0-9-]+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'northium_rest_campaign_detail',
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

function northium_rest_campaign_detail( WP_REST_Request $request ) {
	$slug     = (string) $request->get_param( 'slug' );
	$campaign = northium_get_post_by_slug( 'campaign', $slug );

	if ( ! $campaign ) {
		return northium_rest_not_found( 'Campaign not found' );
	}

	$target_division_id = (int) get_post_meta( $campaign->ID, 'target_division_id', true );
	$division           = $target_division_id ? get_post( $target_division_id ) : null;

	return northium_rest_response(
		array(
			'campaign' => northium_format_campaign( $campaign, true ),
			'division' => $division instanceof WP_Post ? northium_format_division( $division ) : null,
		)
	);
}
