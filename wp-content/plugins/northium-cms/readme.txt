=== Northium CMS ===
Contributors: laxkc
Tags: cpt, headless, rest-api, multi-division
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Multi-division content backbone for the Northium headless WordPress + Next.js platform.

== Description ==

Northium CMS turns WordPress into a headless content store for a multi-division business. It registers five Custom Post Types (Division, Service, Advisor, Article, Campaign), two taxonomies (article_category, service_tag), and a custom REST namespace `northium/v1` that the Next.js frontend consumes.

This plugin owns the *content backbone*. The frontend lives in a separate Next.js app and is not part of WordPress.

== Installation ==

1. Copy this plugin to `wp-content/plugins/northium-cms/`.
2. Activate via Plugins admin or `wp plugin activate northium-cms`.
3. Visit Settings → Permalinks and click Save (flushes rewrite rules).

== Changelog ==

= 0.1.0 =
* Initial scaffold: 5 CPTs, 2 taxonomies, REST API exposure.
