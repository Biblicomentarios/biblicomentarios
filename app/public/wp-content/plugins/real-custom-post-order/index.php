<?php
/**
 * Main file for WordPress.
 *
 * @wordpress-plugin
 * Plugin Name: 	Real Custom Post Order
 * Plugin URI:		https://devowl.io
 * Description: 	Custom post order for posts, pages, WooCommerce products and custom post types using drag and drop. Simple and intuitive sorting of your content!
 * Author:          devowl.io
 * Author URI:		https://devowl.io
 * Version: 		1.2.22
 * Text Domain:		real-custom-post-order
 * Domain Path:		/languages
 */

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Plugin constants. This file is procedural coding style for initialization of
 * the plugin core and definition of plugin configuration.
 */
if (defined('RCPO_PATH')) {
    return;
}
define('RCPO_FILE', __FILE__);
define('RCPO_PATH', dirname(RCPO_FILE));
define('RCPO_ROOT_SLUG', 'devowl-wp');
define('RCPO_SLUG', basename(RCPO_PATH));
define('RCPO_INC', trailingslashit(path_join(RCPO_PATH, 'inc')));
define('RCPO_MIN_PHP', '7.0.0'); // Minimum of PHP 5.3 required for autoloading and namespacing
define('RCPO_MIN_WP', '5.2.0'); // Minimum of WordPress 5.0 required
define('RCPO_NS', 'DevOwl\\RealCustomPostOrder');
define('RCPO_DB_PREFIX', 'rcpo'); // The table name prefix wp_{prefix}
define('RCPO_OPT_PREFIX', 'rcpo'); // The option name prefix in wp_options
define('RCPO_SLUG_CAMELCASE', lcfirst(str_replace('-', '', ucwords(RCPO_SLUG, '-'))));
//define('RCPO_TD', ''); This constant is defined in the core class. Use this constant in all your __() methods
//define('RCPO_VERSION', ''); This constant is defined in the core class
//define('RCPO_DEBUG', true); This constant should be defined in wp-config.php to enable the Base#debug() method

// Check PHP Version and print notice if minimum not reached, otherwise start the plugin core
require_once RCPO_INC .
    'base/others/' .
    (version_compare(phpversion(), RCPO_MIN_PHP, '>=') ? 'start.php' : 'fallback-php-version.php');
