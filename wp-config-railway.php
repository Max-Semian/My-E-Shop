<?php
/**
 * WordPress configuration for Railway (production).
 *
 * Used ONLY in the Railway image — Dockerfile.railway copies this file over
 * wp-config.php during build. Local development keeps its own wp-config.php,
 * so editing this file never affects the local Docker setup.
 *
 * Contains NO secrets: all sensitive values come from the Railway service
 * Variables (DB_* reference the MySQL service; salts are set as variables).
 */

if ( ! function_exists( 'wp_env' ) ) {
	function wp_env( $key, $default = null ) {
		$val = getenv( $key );
		if ( false === $val && isset( $_ENV[ $key ] ) ) {
			$val = $_ENV[ $key ];
		}
		if ( false === $val && isset( $_SERVER[ $key ] ) ) {
			$val = $_SERVER[ $key ];
		}
		return ( false === $val || null === $val || '' === $val ) ? $default : $val;
	}
}

// ** Database (Railway MySQL) ** //
$db_host = wp_env( 'DB_HOST' );
$db_port = wp_env( 'DB_PORT' );
if ( $db_port && false === strpos( (string) $db_host, ':' ) ) {
	$db_host .= ':' . $db_port;
}

define( 'DB_NAME',     wp_env( 'DB_NAME' ) );
define( 'DB_USER',     wp_env( 'DB_USER' ) );
define( 'DB_PASSWORD', wp_env( 'DB_PASSWORD' ) );
define( 'DB_HOST',     $db_host );
define( 'DB_CHARSET',  wp_env( 'DB_CHARSET', 'utf8mb4' ) );
define( 'DB_COLLATE',  '' );

// ** Authentication Unique Keys and Salts (from Railway Variables) ** //
foreach ( array(
	'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY',
	'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT',
) as $wp_salt_key ) {
	if ( ! defined( $wp_salt_key ) ) {
		define( $wp_salt_key, (string) wp_env( $wp_salt_key, '' ) );
	}
}
unset( $wp_salt_key );

// Real production data uses the wp_ prefix (from u821689086_ldApu.sql).
$table_prefix = 'wp_';

// ** Site URL ** //
// Prefer explicit WP_HOME/WP_SITEURL variables; otherwise use Railway's public
// domain. (Currently set to https://cretho.com via the service Variables.)
$railway_domain = wp_env( 'RAILWAY_PUBLIC_DOMAIN' );
$default_url     = $railway_domain ? 'https://' . $railway_domain : null;
$home            = wp_env( 'WP_HOME', $default_url );
$siteurl         = wp_env( 'WP_SITEURL', $default_url );
if ( $home )    { define( 'WP_HOME', $home ); }
if ( $siteurl ) { define( 'WP_SITEURL', $siteurl ); }

// Railway terminates TLS in front of the app; trust the forwarded protocol so
// WordPress generates https:// URLs and admin/login redirects work.
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

// ** Debug (off by default in production; set WP_DEBUG=true to enable) ** //
define( 'WP_DEBUG',         filter_var( wp_env( 'WP_DEBUG', 'false' ), FILTER_VALIDATE_BOOLEAN ) );
define( 'WP_DEBUG_LOG',     true );
define( 'WP_DEBUG_DISPLAY', false );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
