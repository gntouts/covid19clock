<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '94R0BC/Kc2U3NIAQy83ACaxo8FVAKmv+MVR8NauAl+0qfTHwzfxdn/DlGxZXqV7E/VWxsror8ifCkgma5oUzoA==');
define('SECURE_AUTH_KEY',  'ZJ0bj2IIX58T174lnmOmF6l5VHK6Sx1NdMcfAJJANTvM9oopeGlmjGPa/uKXceKEZYoi/74PEqAxG+DI1qPivA==');
define('LOGGED_IN_KEY',    'tUc6RMYvFn27AWw/vox8wydwpQhc9T6MZFygiHxQnvaJRQt95a8DVP7fFjRsgSfFdcVhD7ReOKf9fIOsu9knvw==');
define('NONCE_KEY',        'R6HCM9FaC/4jewrLJok0gABE0PacyPsb3dyUFWs6aHaqqKY/lnWcPGlxDAStNfj4PEGsNY3WRTM2y64i1CV/iQ==');
define('AUTH_SALT',        'Ux763fXIY6/vZH0XFTsdmkjecR8h+uo38PRR7Kzo+0qztKty+L8YYRVYAfMlv+4W/O7jo1AXGUa9zqAy6JYzRQ==');
define('SECURE_AUTH_SALT', 'mR4NA5QFqXCBbyejEsAP/DIY1cRI6uVxD1hL7rRevNOXyr/ogAWXpuubpVoLHr/n3jtn9DBJX3Jmfe6keMgfhQ==');
define('LOGGED_IN_SALT',   'arA2/Vemr2wmii3YTdSQA55+6g8gUHfcQC6kaPPP2/EML0Z61ar6+/3ZLfl0QoetVq9S/QPJPO+ezW5Wht/8Yw==');
define('NONCE_SALT',       'L8I0e86Z6E313RnkrrM6szCpfDQAj4yekMv56b540BAXx3tIitQlVDRp+/vTwY6YmQvzoZcsdXZxKo1RQJGQzg==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
