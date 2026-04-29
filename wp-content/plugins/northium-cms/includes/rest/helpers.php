<?php
/**
 * Shared formatters for the northium/v1 REST namespace.
 *
 * Each formatter takes a WP_Post and returns a clean array shaped for the
 * frontend. Keep these helpers pure; do no I/O outside of WP API calls already
 * cached by WordPress (get_post_meta, get_the_title, attachment URL lookups).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NORTHIUM_REST_NS = 'northium/v1';

function northium_rest_image( int $attachment_id ): ?array {
	if ( ! $attachment_id ) {
		return null;
	}
	$url = wp_get_attachment_image_url( $attachment_id, 'full' );
	if ( ! $url ) {
		return null;
	}
	return array(
		'id'  => $attachment_id,
		'url' => $url,
		'alt' => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
	);
}

function northium_rest_thumbnail( int $post_id ): ?array {
	return northium_rest_image( (int) get_post_thumbnail_id( $post_id ) );
}

function northium_rest_response( $data, int $status = 200 ): WP_REST_Response {
	$response = new WP_REST_Response( $data, $status );
	$response->header( 'Cache-Control', 'public, max-age=300' );
	return $response;
}

function northium_rest_not_found( string $message = 'Not found' ): WP_Error {
	return new WP_Error( 'northium_not_found', $message, array( 'status' => 404 ) );
}

function northium_format_division( WP_Post $post, bool $full = false ): array {
	$out = array(
		'id'                => $post->ID,
		'slug'              => $post->post_name,
		'title'             => get_the_title( $post ),
		'short_description' => $post->post_excerpt,
		'hero_image'        => northium_rest_thumbnail( $post->ID ),
		'accent_color'      => get_post_meta( $post->ID, 'accent_color', true ) ?: null,
		'order'             => (int) $post->menu_order,
	);
	if ( $full ) {
		$out['full_description'] = apply_filters( 'the_content', $post->post_content );
	}
	return $out;
}

function northium_format_service( WP_Post $post, bool $full = false ): array {
	$division_id = (int) get_post_meta( $post->ID, 'division_id', true );
	$out = array(
		'id'             => $post->ID,
		'slug'           => $post->post_name,
		'title'          => get_the_title( $post ),
		'summary'        => $post->post_excerpt,
		'icon'           => northium_rest_image( (int) get_post_meta( $post->ID, 'icon_id', true ) ),
		'featured_image' => northium_rest_thumbnail( $post->ID ),
		'cta_label'      => (string) get_post_meta( $post->ID, 'cta_label', true ),
		'cta_url'        => (string) get_post_meta( $post->ID, 'cta_url', true ),
		'division_id'    => $division_id,
	);
	if ( $full ) {
		$out['body'] = apply_filters( 'the_content', $post->post_content );
	}
	return $out;
}

function northium_format_advisor( WP_Post $post, bool $full = false ): array {
	$division_ids = array_map( 'intval', (array) get_post_meta( $post->ID, 'division_ids', true ) );
	$out = array(
		'id'            => $post->ID,
		'slug'          => $post->post_name,
		'name'          => get_the_title( $post ),
		'role'          => (string) get_post_meta( $post->ID, 'role', true ),
		'profile_image' => northium_rest_thumbnail( $post->ID ),
		'division_ids'  => array_values( array_filter( $division_ids ) ),
		'email'         => (string) get_post_meta( $post->ID, 'email', true ),
		'linkedin_url'  => (string) get_post_meta( $post->ID, 'linkedin_url', true ),
	);
	if ( $full ) {
		$out['bio'] = apply_filters( 'the_content', $post->post_content );
	}
	return $out;
}

function northium_format_article( WP_Post $post, bool $full = false ): array {
	$division_id       = (int) get_post_meta( $post->ID, 'division_id', true );
	$author_advisor_id = (int) get_post_meta( $post->ID, 'author_advisor_id', true );
	$out = array(
		'id'                => $post->ID,
		'slug'              => $post->post_name,
		'title'             => get_the_title( $post ),
		'excerpt'           => $post->post_excerpt,
		'featured_image'    => northium_rest_thumbnail( $post->ID ),
		'publish_date'      => mysql2date( 'c', $post->post_date_gmt, false ),
		'division_id'       => $division_id,
		'author_advisor_id' => $author_advisor_id,
	);
	if ( $full ) {
		$out['content'] = apply_filters( 'the_content', $post->post_content );
	}
	return $out;
}

function northium_format_campaign( WP_Post $post, bool $full = false ): array {
	$out = array(
		'id'                 => $post->ID,
		'slug'               => $post->post_name,
		'title'              => get_the_title( $post ),
		'hero_image'         => northium_rest_thumbnail( $post->ID ),
		'hero_headline'      => (string) get_post_meta( $post->ID, 'hero_headline', true ),
		'target_division_id' => (int) get_post_meta( $post->ID, 'target_division_id', true ),
	);
	if ( $full ) {
		$out['hero_subtext'] = (string) get_post_meta( $post->ID, 'hero_subtext', true );
		$out['cta_text']     = (string) get_post_meta( $post->ID, 'cta_text', true );
		$out['cta_url']      = (string) get_post_meta( $post->ID, 'cta_url', true );
		$out['sections']     = (array) get_post_meta( $post->ID, 'sections', true );
		$out['testimonials'] = (array) get_post_meta( $post->ID, 'testimonials', true );
	}
	return $out;
}

function northium_get_post_by_slug( string $cpt, string $slug ): ?WP_Post {
	$posts = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => $cpt,
			'post_status'    => 'publish',
			'numberposts'    => 1,
			'no_found_rows'  => true,
			'suppress_filters' => false,
		)
	);
	return $posts ? $posts[0] : null;
}

/**
 * Find advisors whose division_ids array contains the given division.
 *
 * For our content scale (5 advisors) we filter in PHP. If this grows past a
 * few hundred advisors, register division_ids with single:false so each value
 * is a separate row and meta_query can index it.
 */
function northium_advisors_for_division( int $division_id ): array {
	if ( ! $division_id ) {
		return array();
	}
	$advisors = get_posts(
		array(
			'post_type'      => 'advisor',
			'post_status'    => 'publish',
			'numberposts'    => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
	return array_values(
		array_filter(
			$advisors,
			static function ( WP_Post $post ) use ( $division_id ): bool {
				$ids = array_map( 'intval', (array) get_post_meta( $post->ID, 'division_ids', true ) );
				return in_array( $division_id, $ids, true );
			}
		)
	);
}
