<?php

/**
 * Plugin Name: Swift Performance early loader
 */

class Swift_Performance_Loader {

	public static function load(){

		if (defined('SWIFT_PERFORMANCE_DISABLE_EARLY_LOAD') && SWIFT_PERFORMANCE_DISABLE_EARLY_LOAD){
			return;
		}

		wp_cookie_constants();
		$plugins = get_option('active_plugins');
		$plugin_file = 'C:\Users\Juan Marichal\Local Sites\biblicomentarios\app\public\wp-content\plugins\swift-performance-lite/performance.php';
		if (in_array('swift-performance-lite/performance.php', (array)$plugins)){
			if (file_exists($plugin_file)){
				include_once $plugin_file;
			}
			// Try fallback (staging, moving, other special cases)
			else if (file_exists(WP_PLUGIN_DIR . '/swift-performance/performance.php')){
				include_once WP_PLUGIN_DIR . '/swift-performance/performance.php';
			}
		}
	}
}
Swift_Performance_Loader::load();
?>
