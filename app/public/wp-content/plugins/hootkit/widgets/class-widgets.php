<?php
/**
 * HootKit Widgets Module
 *
 * @since   2.0.0
 * @package Hootkit
 */

namespace HootKit\Mods;
use \HootKit\Inc\Helper_Assets;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\HootKit\Mods\Widgets' ) ) :

	class Widgets {

		/**
		 * Class Instance
		 */
		private static $instance;

		/**
		 * Active widgets array
		 */
		private $activewidgets = array();

		/**
		 * Required components array
		 */
		private $requires = array();

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->activewidgets = hootkit()->get_config( 'activemods', 'widget' );
		
			if ( !empty( $this->activewidgets ) ) {

				$modules = hootkit()->get_mods( 'modules' );
				foreach ( $this->activewidgets as $widget )
					if ( !empty( $modules[$widget]['requires'] ) )
						$this->requires = array_merge( $this->requires, $modules[$widget]['requires'] );

				require_once( hootkit()->dir . 'widgets/class-hk-widget.php' );

				$this->load_assets();
				$this->load_adminassets();
				$this->load_widgets();

			}

		}

		/**
		 * Load assets
		 *
		 * @since  2.0.0
		 */
		private function load_assets() {

			if ( in_array( 'lightslider', $this->requires ) ) {
				Helper_Assets::add_style( 'lightSlider' );
				Helper_Assets::add_script( 'jquery-lightSlider' );
			}

			if ( in_array( 'circliful', $this->requires ) )
				Helper_Assets::add_script( 'jquery-circliful' ); // ::=> Hootkit does not load Waypoints. It is upto the theme to deploy waypoints.

			if ( in_array( 'font-awesome', $this->requires ) )
				Helper_Assets::add_style( 'font-awesome' );

			if( ! hootkit()->get_config( 'theme_css' ) )
				Helper_Assets::add_style( hootkit()->slug );

			Helper_Assets::add_script( hootkit()->slug . '-widgets' );

		}

		/**
		 * Load assets
		 *
		 * @since  2.0.0
		 */
		private function load_adminassets() {

			$hooks = ( defined( 'SITEORIGIN_PANELS_VERSION' ) && version_compare( SITEORIGIN_PANELS_VERSION, '2.0' ) >= 0 ) ?
						array( 'widgets.php', 'post.php', 'post-new.php' ):
						array( 'widgets.php' );
			// SiteOrigin Page Builder compatibility - Load css for Live Preview in backend
			// > Limitation: dynamic css is not loaded // @todo test all widgets (inc sliders)
			// if( $widgetload && $this->get_config( 'theme_css' ) && function_exists( 'hoot_locate_style' ) ) {
			// 	wp_enqueue_style( 'theme-hootkit', hoot_data()->template_uri . 'hootkit/hootkit.css' );
			// 	// wp_enqueue_style( 'theme-style', hoot_data()->template_uri . 'style.css' ); // Loads all styles including headings, grid etc -> Not Needed // Loads grid etc for widget post grid etc -> Needed
			// }

			if ( in_array( 'font-awesome', $this->requires ) )
				Helper_Assets::add_adminstyle( 'font-awesome', $hooks );

			if ( in_array( 'select2', $this->requires ) ) {
				Helper_Assets::add_adminstyle( 'select2', $hooks );
				Helper_Assets::add_adminscript( 'select2', $hooks );
			}

			if ( in_array( 'wp-media', $this->requires ) )
				Helper_Assets::add_adminmedia( $hooks );

			Helper_Assets::add_adminstyle( 'wp-color-picker', $hooks );
			Helper_Assets::add_adminscript( 'wp-color-picker', $hooks );

			Helper_Assets::add_adminstyle( hootkit()->slug . '-adminwidgets', $hooks );
			Helper_Assets::add_adminscript( hootkit()->slug . '-adminwidgets', $hooks ); // @todo: separate script based on components

		}

		/**
		 * Load individual widgets
		 *
		 * @since  2.0.0
		 */
		private function load_widgets() {

			foreach ( $this->activewidgets as $widget )
				if ( file_exists( hootkit()->dir . 'widgets/' . sanitize_file_name( $widget ) . '/admin.php' ) )
					require_once( hootkit()->dir . 'widgets/' . sanitize_file_name( $widget ) . '/admin.php' );

		}

		/**
		 * Returns the instance
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

	}

	Widgets::get_instance();

endif;