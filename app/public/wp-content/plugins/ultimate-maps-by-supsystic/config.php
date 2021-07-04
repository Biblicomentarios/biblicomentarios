<?php
    global $wpdb;
    if (!defined('WPLANG') || WPLANG == '') {
        define('UMS_WPLANG', 'en_GB');
    } else {
        define('UMS_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('UMS_PLUG_NAME', basename(dirname(__FILE__)));
    define('UMS_DIR', WP_PLUGIN_DIR. DS. UMS_PLUG_NAME. DS);
    define('UMS_TPL_DIR', UMS_DIR. 'tpl'. DS);
    define('UMS_CLASSES_DIR', UMS_DIR. 'classes'. DS);
    define('UMS_TABLES_DIR', UMS_CLASSES_DIR. 'tables'. DS);
	define('UMS_HELPERS_DIR', UMS_CLASSES_DIR. 'helpers'. DS);
    define('UMS_LANG_DIR', UMS_DIR. 'languages'. DS);
    define('UMS_IMG_DIR', UMS_DIR. 'img'. DS);
    define('UMS_TEMPLATES_DIR', UMS_DIR. 'templates'. DS);
    define('UMS_MODULES_DIR', UMS_DIR. 'modules'. DS);
    define('UMS_FILES_DIR', UMS_DIR. 'files'. DS);
    define('UMS_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

	define('UMS_PLUGINS_URL', plugins_url());
    define('UMS_SITE_URL', get_bloginfo('wpurl'). '/');
    define('UMS_ASSETS_PATH', UMS_PLUGINS_URL. '/'. UMS_PLUG_NAME. '/assets/');
    define('UMS_JS_PATH', UMS_PLUGINS_URL. '/'. UMS_PLUG_NAME. '/js/');
    define('UMS_CSS_PATH', UMS_PLUGINS_URL. '/'. UMS_PLUG_NAME. '/css/');
    define('UMS_IMG_PATH', UMS_PLUGINS_URL. '/'. UMS_PLUG_NAME. '/img/');
    define('UMS_MODULES_PATH', UMS_PLUGINS_URL. '/'. UMS_PLUG_NAME. '/modules/');
    define('UMS_TEMPLATES_PATH', UMS_PLUGINS_URL. '/'. UMS_PLUG_NAME. '/templates/');
    define('UMS_JS_DIR', UMS_DIR. 'js/');

    define('UMS_URL', UMS_SITE_URL);

    define('UMS_LOADER_IMG', UMS_IMG_PATH. 'loading.gif');
	define('UMS_TIME_FORMAT', 'H:i:s');
    define('UMS_DATE_DL', '/');
    define('UMS_DATE_FORMAT', 'm/d/Y');
    define('UMS_DATE_FORMAT_HIS', 'm/d/Y ('. UMS_TIME_FORMAT. ')');
    define('UMS_DATE_FORMAT_JS', 'mm/dd/yy');
    define('UMS_DATE_FORMAT_CONVERT', '%m/%d/%Y');
    define('UMS_WPDB_PREF', $wpdb->prefix);
    define('UMS_DB_PREF', 'ums_');
    define('UMS_MAIN_FILE', 'ums.php');

    define('UMS_DEFAULT', 'default');
    define('UMS_CURRENT', 'current');

	define('UMS_EOL', "\n");

    define('UMS_PLUGIN_INSTALLED', true);
    define('UMS_VERSION_PLUGIN', '1.2.7');
    define('UMS_USER', 'user');

    define('UMS_CLASS_PREFIX', 'umsc');
    define('UMS_FREE_VERSION', false);
	define('UMS_TEST_MODE', true);

    define('UMS_SUCCESS', 'Success');
    define('UMS_FAILED', 'Failed');
	define('UMS_ERRORS', 'umsErrors');

	define('UMS_ADMIN',	'admin');
	define('UMS_LOGGED','logged');
	define('UMS_GUEST',	'guest');

	define('UMS_ALL',		'all');

	define('UMS_METHODS',		'methods');
	define('UMS_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('UMS_CODE', 'ums');

	define('UMS_LANG_CODE', 'ultimate-maps-by-supsystic');
	/**
	 * Plugin name
	 */
	define('UMS_WP_PLUGIN_NAME', 'Ultimate Maps by Supsystic');
	/**
	 * Plugin admin area slug
	 */
	define('UMS_ADMIN_AREA_SLUG', 'ultimate-maps-supsystic');
	/**
	 * Dash icon for WP admin area menu
	 */
	define('UMS_ADMIN_MENU_ICON', 'dashicons-admin-site');

	define('UMS_WP_NAME', 'ultimate-maps-by-supsystic');
	/**
	 * Custom defined for plugin
	 */
	define('UMS_COMMON', 'common');
	define('UMS_FB_LIKE', 'fb_like');
	define('UMS_VIDEO', 'video');

	define('UMS_SHORTCODE', 'ultimate_maps');
