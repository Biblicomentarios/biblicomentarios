<?php
/*
Plugin Name: 	Admin Columns Pro - Advanced Custom Fields (ACF)
Version: 		2.6.4
Description: 	Supercharges Admin Columns Pro with columns for Advanced Custom Fields (ACF)
Author:         AdminColumns.com
Author URI:     https://www.admincolumns.com
Plugin URI:     https://www.admincolumns.com
Text Domain: 	codepress-admin-columns
Requires PHP:   5.6.20
*/

use ACA\ACF\AdvancedCustomFields;
use ACA\ACF\Dependencies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() ) {
	return;
}

require_once __DIR__ . '/classes/Dependencies.php';

add_action( 'after_setup_theme', function () {
	$dependencies = new Dependencies( plugin_basename( __FILE__ ), '2.6.4' );
	$dependencies->requires_acp( '5.4' );
	$dependencies->requires_php( '5.6.20' );

	if ( ! class_exists( 'acf', false ) ) {
		$dependencies->add_missing_plugin( 'Advanced Custom Fields', 'https://www.advancedcustomfields.com/' );
	}

	if ( $dependencies->has_missing() ) {
		return;
	}

	$class_map = __DIR__ . '/config/autoload-classmap.php';

	if ( is_readable( $class_map ) ) {
		AC\Autoloader::instance()->register_class_map( require $class_map );
	} else {
		AC\Autoloader::instance()->register_prefix( 'ACA\ACF', __DIR__ . '/classes' );
	}

	AC\Autoloader\Underscore::instance()
	                        ->add_alias( 'ACA\ACF\Column', 'ACA_ACF_Column' )
	                        ->add_alias( 'ACA\ACF\Field', 'ACA_ACF_Field' );

	$addon = new AdvancedCustomFields( __FILE__ );
	$addon->register();
} );

function ac_addon_acf() {
	return new AdvancedCustomFields( __FILE__ );
}