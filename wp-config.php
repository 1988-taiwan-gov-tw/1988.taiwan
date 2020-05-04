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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', '1988dev' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', 'utf8_general_ci' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'FKWDluAfCzsAuT2l2eFi4KhAmrys4ymrQjiBOnk7Cw2bdbOVluHCwtmuM6GhsGI3');
define('SECURE_AUTH_KEY',  'h0aOP4NTexQKCzPSVTowjf7beCxIp6M5kO0Li30BKHVVwpdz8cr8C1bbL3npPeR6');
define('LOGGED_IN_KEY',    'pGy1LZb79PDJkDeuCpyocQYhRGwjpT62rg3JKUBavUai9nInBaJoP0yfmLpJETAv');
define('NONCE_KEY',        'zVDVfcI4MZagAyHM3YZiPIuKnT0rppUmAQ61NSJPmJLu5xrP22M4jmO0kSYUW3k8');
define('AUTH_SALT',        'eDJdd7EbXEnyQa5wHameIYn3xeM9t1zN7kPOXbXKe6FfFpl6KeZPP3CaSrFtRioF');
define('SECURE_AUTH_SALT', 'UUbZACvfYIIeNhhZVFpE3x4t8qAxeFZyGSWuDU9ohkZBrg0EzbYTdMXk9Fx7Bh5A');
define('LOGGED_IN_SALT',   'GmhyXVipxhtTcxcPDMU3d4SZjrf6Ps08K9oI8bV8s1jhM05yTOpNHz3DfGNvJabG');
define('NONCE_SALT',       'bI1mW0MaB2B7xpYI7axa1f88bFxGLlzalpyqWYSDiFI7WqwsV47pyc6k2LnUxBdY');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');
define('FS_CHMOD_DIR',0755);
define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed externally by Installatron.
 * If you remove this define() to re-enable WordPress's automatic background updating
 * then it's advised to disable auto-updating in Installatron.
 */
define('AUTOMATIC_UPDATER_DISABLED', false);


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __FILE__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';