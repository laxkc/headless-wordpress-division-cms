<?php
/**
 * GET /northium/v1/search?q=...    cross-CPT search
 *
 * Returns up to 10 hits per content type (divisions, services, advisors, articles).
 * For our scale this is a straight WP_Query per CPT with the `s` parameter.
 * If hit volume grows, swap this for a real search backend (Algolia, Meilisearch).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'northium_rest_register_search' );

function northium_rest_register_search(): void {
	register_rest_route(
		NORTHIUM_REST_NS,
		'/search',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'northium_rest_search',
			'args'                => array(
				'q' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}

function northium_rest_search( WP_REST_Request $request ) {
	$query = trim( (string) $request->get_param( 'q' ) );

	if ( $query === '' ) {
		return new WP_Error( 'northium_empty_query', 'Search query is empty.', array( 'status' => 400 ) );
	}

	$run = static fn( string $cpt ): array => get_posts(
		array(
			'post_type'      => $cpt,
			'post_status'    => 'publish',
			's'              => $query,
			'numberposts'    => 10,
			'no_found_rows'  => true,
		)
	);

	return northium_rest_response(
		array(
			'query'   => $query,
			'results' => array(
				'divisions' => array_map( static fn( WP_Post $p ): array => northium_format_division( $p ), $run( 'division' ) ),
				'services'  => array_map( static fn( WP_Post $p ): array => northium_format_service( $p ),  $run( 'service' ) ),
				'advisors'  => array_map( static fn( WP_Post $p ): array => northium_format_advisor( $p ),  $run( 'advisor' ) ),
				'articles'  => array_map( static fn( WP_Post $p ): array => northium_format_article( $p ),  $run( 'article' ) ),
			),
		)
	);
}
