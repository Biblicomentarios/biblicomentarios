<?php

/**
 * Plugin Name: Luv Framework
 *
 */


if (!defined('LUV_FRAMEWORK_PATH')){
      define('LUV_FRAMEWORK_PATH', trailingslashit(__DIR__));
}

if (!defined('LUV_FRAMEWORK_URL')){
      define('LUV_FRAMEWORK_URL', trailingslashit(plugins_url( '', __FILE__ )));
}

if (!class_exists('Luv_Framework')){
      include_once apply_filters('luv_framework_classes_dir', LUV_FRAMEWORK_PATH . 'classes/') . 'class.framework.php';
}

if (function_exists('determine_locale')){
	$locale = apply_filters( 'plugin_locale', determine_locale(), 'luv-framework' );
}
else {
	global $locale;
}
load_textdomain( 'luv-framework', LUV_FRAMEWORK_PATH . '/language/luv-framework-' . $locale . '.mo' );

// Helpers
if (!function_exists('luv_framework_kses')){
      function luv_framework_kses($string){
            return wp_kses($string, array(
                  'pre' => array(),
                  'a'   => array(
                        'href' => array(),
                        'title' => array(),
                        'target' => array()
                  ),
                  'br'  => array(),
                  'p'   => array(),
                  'i'   => array(),
                  'b'   => array(),
            ));
      }
}
?>
