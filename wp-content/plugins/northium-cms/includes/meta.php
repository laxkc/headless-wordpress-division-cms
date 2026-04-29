<?php
/**
 * Custom meta fields for Northium CMS, exposed via REST.
 *
 * Native WordPress fields cover: title (post_title), slug (post_name),
 * short text (post_excerpt), long text (post_content), images (thumbnail),
 * order (menu_order), date (post_date), author (post_author).
 *
 * Everything below covers what those native fields can't:
 *   - inter-CPT relationships (e.g. Service -> Division)
 *   - structured arrays (campaign sections, testimonials)
 *   - typed scalars (URLs, hex colors, emails)
 *
 * Field map traces directly to docs/03-content-model.md.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'northium_cms_register_meta', 5 );

function northium_cms_register_meta(): void {

	$auth = static fn(): bool => current_user_can( 'edit_posts' );

	// ---------- Division ----------

	register_post_meta(
		'division',
		'accent_color',
		array(
			'type'              => 'string',
			'description'       => 'Hex color used as the division accent on the frontend.',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_hex_color',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'division',
		'featured_services',
		array(
			'type'          => 'array',
			'description'   => 'Curated Service post IDs surfaced on the division page.',
			'single'        => true,
			'show_in_rest'  => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'integer' ),
				),
			),
			'auth_callback' => $auth,
		)
	);

	register_post_meta(
		'division',
		'featured_advisors',
		array(
			'type'          => 'array',
			'description'   => 'Curated Advisor post IDs surfaced on the division page.',
			'single'        => true,
			'show_in_rest'  => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'integer' ),
				),
			),
			'auth_callback' => $auth,
		)
	);

	// ---------- Service ----------

	register_post_meta(
		'service',
		'division_id',
		array(
			'type'              => 'integer',
			'description'       => 'Division this service belongs to.',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'service',
		'icon_id',
		array(
			'type'              => 'integer',
			'description'       => 'Attachment ID for the service card icon.',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'service',
		'cta_label',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'service',
		'cta_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => $auth,
		)
	);

	// ---------- Advisor ----------

	register_post_meta(
		'advisor',
		'role',
		array(
			'type'              => 'string',
			'description'       => 'Job title displayed under the advisor name.',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'advisor',
		'division_ids',
		array(
			'type'          => 'array',
			'description'   => 'Divisions this advisor serves (one or more).',
			'single'        => true,
			'show_in_rest'  => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'integer' ),
				),
			),
			'auth_callback' => $auth,
		)
	);

	register_post_meta(
		'advisor',
		'email',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_email',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'advisor',
		'linkedin_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => $auth,
		)
	);

	// ---------- Article ----------

	register_post_meta(
		'article',
		'division_id',
		array(
			'type'              => 'integer',
			'description'       => 'Optional division this article is about. 0 = firm-wide.',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'article',
		'author_advisor_id',
		array(
			'type'              => 'integer',
			'description'       => 'Optional Advisor post ID, if an advisor authored this.',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $auth,
		)
	);

	// ---------- Campaign ----------

	register_post_meta(
		'campaign',
		'target_division_id',
		array(
			'type'              => 'integer',
			'description'       => 'Division this campaign targets. Drives accent + back-link.',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'campaign',
		'hero_headline',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'campaign',
		'hero_subtext',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_textarea_field',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'campaign',
		'cta_text',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'campaign',
		'cta_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'campaign',
		'sections',
		array(
			'type'          => 'array',
			'description'   => 'Ordered content sections rendered on the campaign page.',
			'single'        => true,
			'show_in_rest'  => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'additionalProperties' => false,
						'properties'           => array(
							'heading'  => array( 'type' => 'string' ),
							'body'     => array( 'type' => 'string' ),
							'image_id' => array( 'type' => 'integer' ),
						),
					),
				),
			),
			'auth_callback' => $auth,
		)
	);

	register_post_meta(
		'campaign',
		'testimonials',
		array(
			'type'          => 'array',
			'description'   => 'Quote / attribution / role triples shown on the campaign page.',
			'single'        => true,
			'show_in_rest'  => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'additionalProperties' => false,
						'properties'           => array(
							'quote'       => array( 'type' => 'string' ),
							'attribution' => array( 'type' => 'string' ),
							'role'        => array( 'type' => 'string' ),
						),
					),
				),
			),
			'auth_callback' => $auth,
		)
	);
}
