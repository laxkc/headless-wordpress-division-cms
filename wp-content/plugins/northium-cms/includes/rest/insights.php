<?php
/**
 * GET /northium/v1/insights              paginated, filterable list
 *   ?division={slug}    filter to one division
 *   ?category={slug}    filter to one editorial category
 *   ?page=1&per_page=12
 *
 * GET /northium/v1/insights/{slug}       article + author + related-by-division
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'northium_rest_register_insights' );

function northium_rest_register_insights(): void {

	register_rest_route(
		NORTHIUM_REST_NS,
		'/insights',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'northium_rest_insights_index',
			'args'                => array(
				'division' => array(
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_title',
				),
				'category' => array(
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_title',
				),
				'page' => array(
					'type'              => 'integer',
					'default'           => 1,
					'sanitize_callback' => 'absint',
				),
				'per_page' => array(
					'type'              => 'integer',
					'default'           => 12,
					'sanitize_callback' => 'absint',
				),
			),
		)
	);

	register_rest_route(
		NORTHIUM_REST_NS,
		'/insights/(?P<slug>[a-z0-9-]+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'northium_rest_insight_detail',
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

function northium_rest_insights_index( WP_REST_Request $request ) {
	$page     = max( 1, (int) $request->get_param( 'page' ) );
	$per_page = max( 1, min( 50, (int) $request->get_param( 'per_page' ) ?: 12 ) );
	$division = (string) $request->get_param( 'division' );
	$category = (string) $request->get_param( 'category' );

	$args = array(
		'post_type'      => 'article',
		'post_status'    => 'publish',
		'paged'          => $page,
		'posts_per_page' => $per_page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( $division ) {
		$division_post = northium_get_post_by_slug( 'division', $division );
		if ( ! $division_post ) {
			return new WP_Error( 'northium_bad_division', 'Unknown division slug.', array( 'status' => 400 ) );
		}
		$args['meta_key']   = 'division_id';
		$args['meta_value'] = $division_post->ID;
	}

	if ( $category ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'article_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$query = new WP_Query( $args );

	$items = array_map(
		static fn( WP_Post $p ): array => northium_format_article( $p ),
		$query->posts
	);

	return northium_rest_response(
		array(
			'items'       => $items,
			'page'        => $page,
			'per_page'    => $per_page,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
		)
	);
}

function northium_rest_insight_detail( WP_REST_Request $request ) {
	$slug    = (string) $request->get_param( 'slug' );
	$article = northium_get_post_by_slug( 'article', $slug );

	if ( ! $article ) {
		return northium_rest_not_found( 'Article not found' );
	}

	$division_id       = (int) get_post_meta( $article->ID, 'division_id', true );
	$author_advisor_id = (int) get_post_meta( $article->ID, 'author_advisor_id', true );

	$division = $division_id ? get_post( $division_id ) : null;
	$author   = $author_advisor_id ? get_post( $author_advisor_id ) : null;

	$related = array();
	if ( $division_id ) {
		$related = get_posts(
			array(
				'post_type'      => 'article',
				'post_status'    => 'publish',
				'numberposts'    => 3,
				'meta_key'       => 'division_id',
				'meta_value'     => $division_id,
				'post__not_in'   => array( $article->ID ),
				'orderby'        => 'date',
				'order'          => 'DESC',
				'no_found_rows'  => true,
			)
		);
	}

	return northium_rest_response(
		array(
			'article'        => northium_format_article( $article, true ),
			'division'       => $division instanceof WP_Post ? northium_format_division( $division ) : null,
			'author_advisor' => $author instanceof WP_Post ? northium_format_advisor( $author ) : null,
			'related'        => array_map( static fn( WP_Post $p ): array => northium_format_article( $p ), $related ),
		)
	);
}
