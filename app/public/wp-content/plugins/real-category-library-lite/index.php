<?php
/**
 * Main file for WordPress.
 *
 * @wordpress-plugin
 * Plugin Name: 	Real Category Management (Free)
 * Plugin URI:		https://codecanyon.net/item/wordpress-real-category-management-custom-category-order-tree-view/13580393
 * Description: 	Manage posts, pages and custom post-types in an explorer-like tree view and create a custom order for your categories and other taxonomies.
 * Author:          devowl.io
 * Author URI:		https://devowl.io
 * Version: 		4.0.9
 * Text Domain:		real-category-library
 * Domain Path:		/languages
 */

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Plugin constants. This file is procedural coding style for initialization of
 * the plugin core and definition of plugin configuration.
 */
if (defined('RCL_PATH')) {
    require_once path_join(dirname(__FILE__), 'inc/base/others/fallback-already.php');
    return;
}
define('RCL_FILE', __FILE__);
define('RCL_PATH', dirname(RCL_FILE));
define('RCL_ROOT_SLUG', 'devowl-wp');
define('RCL_SLUG', basename(RCL_PATH));
define('RCL_INC', trailingslashit(path_join(RCL_PATH, 'inc')));
define('RCL_MIN_PHP', '7.0.0'); // Minimum of PHP 5.3 required for autoloading and namespacing
define('RCL_MIN_WP', '5.2.0'); // Minimum of WordPress 5.0 required
define('RCL_NS', 'DevOwl\\RealCategoryLibrary');
define('RCL_DB_PREFIX', 'rcl'); // The table name prefix wp_{prefix}
define('RCL_OPT_PREFIX', 'rcl'); // The option name prefix in wp_options
define('RCL_SLUG_CAMELCASE', lcfirst(str_replace('-', '', ucwords(RCL_SLUG, '-'))));
//define('RCL_TD', ''); This constant is defined in the core class. Use this constant in all your __() methods
//define('RCL_VERSION', ''); This constant is defined in the core class
//define('RCL_DEBUG', true); This constant should be defined in wp-config.php to enable the Base#debug() method

define('RCL_SLUG_LITE', 'real-category-library-lite');
define('RCL_SLUG_PRO', 'real-category-library');
define('RCL_PRO_VERSION', 'https://devowl.io/go/real-category-management?source=rcm-lite');

// Check PHP Version and print notice if minimum not reached, otherwise start the plugin core
require_once RCL_INC .
    'base/others/' .
    (version_compare(phpversion(), RCL_MIN_PHP, '>=') ? 'start.php' : 'fallback-php-version.php');
