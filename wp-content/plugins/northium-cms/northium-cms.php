<?php
/**
 * Plugin Name:       Northium CMS
 * Plugin URI:        https://github.com/laxkc/headless-wordpress-division-cms
 * Description:       Multi-division content backbone for the Northium headless WordPress + Next.js platform. Registers Custom Post Types (Division, Service, Advisor, Article, Campaign), taxonomies, and REST endpoints consumed by the Next.js frontend.
 * Version:           0.1.0
 * Author:            Laxman KC
 * Author URI:        https://github.com/laxkc
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Text Domain:       northium-cms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NORTHIUM_CMS_VERSION', '0.1.0' );
define( 'NORTHIUM_CMS_FILE', __FILE__ );
define( 'NORTHIUM_CMS_DIR', plugin_dir_path( __FILE__ ) );
define( 'NORTHIUM_CMS_URL', plugin_dir_url( __FILE__ ) );

require_once NORTHIUM_CMS_DIR . 'includes/cpts.php';
require_once NORTHIUM_CMS_DIR . 'includes/taxonomies.php';
require_once NORTHIUM_CMS_DIR . 'includes/meta.php';

require_once NORTHIUM_CMS_DIR . 'includes/rest/helpers.php';
require_once NORTHIUM_CMS_DIR . 'includes/rest/divisions.php';
require_once NORTHIUM_CMS_DIR . 'includes/rest/service.php';
require_once NORTHIUM_CMS_DIR . 'includes/rest/advisors.php';
require_once NORTHIUM_CMS_DIR . 'includes/rest/insights.php';
require_once NORTHIUM_CMS_DIR . 'includes/rest/campaign.php';
require_once NORTHIUM_CMS_DIR . 'includes/rest/search.php';
require_once NORTHIUM_CMS_DIR . 'includes/webhook.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once NORTHIUM_CMS_DIR . 'includes/seed.php';
}

register_activation_hook( __FILE__, 'northium_cms_activate' );
function northium_cms_activate(): void {
	northium_cms_register_cpts();
	northium_cms_register_taxonomies();
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'northium_cms_deactivate' );
function northium_cms_deactivate(): void {
	flush_rewrite_rules();
}
