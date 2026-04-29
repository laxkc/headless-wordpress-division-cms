<?php
/**
 * Outbound revalidation webhook.
 *
 * Fires on save_post / delete_post for any Northium CPT and POSTs a small
 * payload to the Next.js frontend so it can invalidate the affected paths.
 *
 * Configuration: set these constants in wp-config.php (or as docker env →
 * WORDPRESS_CONFIG_EXTRA), do not commit production values:
 *
 *   define('NORTHIUM_REVALIDATE_URL',    'http://host.docker.internal:3000/api/revalidate');
 *   define('NORTHIUM_REVALIDATE_SECRET', 'shared-secret-here');
 *
 * Without the URL constant the webhook is a no-op (silent skip), which is the
 * right default — local installs that do not run a frontend should not error.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NORTHIUM_CMS_CPTS = array( 'division', 'service', 'advisor', 'article', 'campaign' );

add_action( 'save_post', 'northium_cms_revalidate_on_save', 20, 3 );
add_action( 'before_delete_post', 'northium_cms_revalidate_on_delete', 20, 2 );

function northium_cms_revalidate_on_save( int $post_id, WP_Post $post, bool $update ): void {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	if ( ! in_array( $post->post_type, NORTHIUM_CMS_CPTS, true ) ) {
		return;
	}
	if ( $post->post_status !== 'publish' && $post->post_status !== 'trash' ) {
		return;
	}
	northium_cms_send_revalidate( $post->post_type, $post->post_name, 'save' );
}

function northium_cms_revalidate_on_delete( int $post_id, WP_Post $post ): void {
	if ( ! in_array( $post->post_type, NORTHIUM_CMS_CPTS, true ) ) {
		return;
	}
	northium_cms_send_revalidate( $post->post_type, $post->post_name, 'delete' );
}

function northium_cms_send_revalidate( string $type, string $slug, string $action ): void {
	if ( ! defined( 'NORTHIUM_REVALIDATE_URL' ) || ! NORTHIUM_REVALIDATE_URL ) {
		return;
	}

	$headers = array( 'Content-Type' => 'application/json' );
	if ( defined( 'NORTHIUM_REVALIDATE_SECRET' ) && NORTHIUM_REVALIDATE_SECRET ) {
		$headers['Authorization'] = 'Bearer ' . NORTHIUM_REVALIDATE_SECRET;
	}

	$response = wp_remote_post(
		NORTHIUM_REVALIDATE_URL,
		array(
			'headers'  => $headers,
			'body'     => wp_json_encode(
				array(
					'type'   => $type,
					'slug'   => $slug,
					'action' => $action,
				)
			),
			'timeout'  => 5,
			'blocking' => false,
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( '[northium-cms] revalidate failed: ' . $response->get_error_message() );
	}
}
