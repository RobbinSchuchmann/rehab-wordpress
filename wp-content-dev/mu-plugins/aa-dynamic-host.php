<?php
/**
 * Dev-stack helper: make the site host-agnostic.
 *
 * The stack is browsed both as http://localhost:8081 (on the server) and
 * http://<public-ip>:8081 (from outside). WordPress stores one absolute host
 * in home/siteurl AND absolute URLs get baked into post_content by the
 * migration builders. Both break when viewed from the other host.
 *
 * 1. home/siteurl follow the current request's Host header, so enqueued
 *    CSS/JS, links and canonical redirects always match the host in use.
 * 2. the_content rewrites any baked-in absolute URL for known dev hosts to
 *    a host-relative path, so theme images (press logos, photos) load from
 *    whichever host the visitor is on.
 *
 * Dev only — production uses the replace-host oneshot for real migrations.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Dynamic home/siteurl from the request host.
if ( ! empty( $_SERVER['HTTP_HOST'] ) && php_sapi_name() !== 'cli' ) {
	$rehab_dynamic_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
	add_filter( 'option_home', static fn() => $rehab_dynamic_url );
	add_filter( 'option_siteurl', static fn() => $rehab_dynamic_url );
}

// 2. Host-relative rewrite of absolute dev URLs baked into content
//    (theme assets AND page permalinks stored by the migration builders).
add_filter( 'the_content', static function ( $content ) {
	return preg_replace( '#https?://(localhost|127\.0\.0\.1|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:\d+)?/#', '/', $content );
}, 1 );
