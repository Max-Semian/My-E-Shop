<?php
/**
 * WordPress configuration file for local Docker development.
 */

// ** Database settings ** //
define( 'DB_NAME',     'u821689086_ldApu' );
define( 'DB_USER',     'wordpress' );
define( 'DB_PASSWORD', 'wordpress' );
define( 'DB_HOST',     'db' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

// ** Authentication Unique Keys and Salts ** //
define( 'AUTH_KEY',         'c3R@#Kx!mP2$vLqN8zYwJtRuAeDbFgHi' );
define( 'SECURE_AUTH_KEY',  'pQ7&nSjVoXlMbCdEfGhIkLmNoPqRsTuV' );
define( 'LOGGED_IN_KEY',    'wXyZ1a2B3c4D5e6F7g8H9i0JkLmNoPqR' );
define( 'NONCE_KEY',        'sT1uV2wX3yZ4aB5cD6eF7gH8iJ9kLmNo' );
define( 'AUTH_SALT',        'pQ0rS1tU2vW3xY4zA5bC6dE7fG8hI9jK' );
define( 'SECURE_AUTH_SALT', 'lM0nO1pQ2rS3tU4vW5xY6zA7bC8dE9fG' );
define( 'LOGGED_IN_SALT',   'hI0jK1lM2nO3pQ4rS5tU6vW7xY8zA9bC' );
define( 'NONCE_SALT',       'dE0fG1hI2jK3lM4nO5pQ6rS7tU8vW9xY' );

$table_prefix = 'wp_';

// ** Override site URL for local Docker development ** //
define( 'WP_HOME',    'http://localhost:8080' );
define( 'WP_SITEURL', 'http://localhost:8080' );

// ** Development settings ** //
define( 'WP_DEBUG',         true );
define( 'WP_DEBUG_LOG',     true );
define( 'WP_DEBUG_DISPLAY', false );

/* That's all, stop editing! Happy publishing. */

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
