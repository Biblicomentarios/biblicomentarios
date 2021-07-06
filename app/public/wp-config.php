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
define('AUTH_KEY',         '+w/1e7quW5Cf+n1Z2KxX+OL6O6rpPr767dGoin0vq7F16r6IpzPNFZMz6xVP81zJMDpIErbtk0FhtcWzvT8hFw==');
define('SECURE_AUTH_KEY',  'X0BLAOCx71jwDZy40BjF4BtAHfk6O+BZbYAU2h/TeoyofC4Ee268sRtv1ytbw4cwcSlUTmQHYEwvBZW4+4tmIA==');
define('LOGGED_IN_KEY',    'qHkc2IFIA20o+aHrJhmahVi/flwASiIls72nJFTaNaKpmZLNy2FyqAa1V/Aj31teR6Qesf8A0Ouu6WMh0k0RwQ==');
define('NONCE_KEY',        'SCNqOIwbDULU9WOOWCJnIZmwfF2dPdAy6Ck0DOhQGae7lZAS/bYK9ieVacb+QPfvSENJBrZem2+jUhPNRgUMtw==');
define('AUTH_SALT',        'XWW8tlGBjpuUAQP5Ptj4S+vD5zCN9sOILS+alLX+Bn47YW/Bf0cRxIpZjSRfbPf7qfpfLKmofauRXAOvA5omHw==');
define('SECURE_AUTH_SALT', 'POl25YWr5jhCFeZzPEUhkwQftjLkNGcUuONbrKS6aSW/3n/LQWZbUymuHmTQ42rIDzmxG9W0TliC+QIzYGs8Lg==');
define('LOGGED_IN_SALT',   'yx/vlsxAbtdjr/3djGTf0ZdUoscKy3eWu6N/n6wbeibcf/xIKONcKpxTTexblmQGEHO5RufhTaY77TNLSzMD6Q==');
define('NONCE_SALT',       'M2y1Xhnh6qiJ2vcWrUWWqJu7QqhFMA6MFp9XHP/KB4RdGWNYhMeEPwP+b9Xzm+OHM3S6YbcYA4DWJ3eVvYQG1A==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/* That's all, stop editing! Happy publishing. */

define('WP_MEMORY_LIMIT', '150M');

define('WPLANG','es');

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
// define('WP_ALLOW_REPAIR', true);

define( 'WP_DEBUG', true ); 
define( 'WP_DEBUG_LOG', true ); 
define( 'WP_DEBUG_DISPLAY', false );