<?php
/**
 * Plugin Name:       HootKit
 * Description:       HootKit is a great companion plugin for WordPress themes by wpHoot.
 * Version:           2.0.2
 * Author:            wphoot
 * Author URI:        https://wphoot.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hootkit
 * Domain Path:       /languages
 * @package           Hootkit
 */

use \HootKit\Inc\Helper_Config;
use \HootKit\Inc\Helper_Strings;
use \HootKit\Inc\Helper_Mods;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Uncomment the line below to load unminified CSS and JS, and add other developer data to code.
 */
// define( 'HOOT_DEBUG', true );
if ( !defined( 'HOOT_DEBUG' ) && defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG )
	define( 'HOOT_DEBUG', true );

/**
 * The core plugin class.
 *
 * @since   1.0.0
 * @package Hootkit
 */
if ( ! class_exists( 'HootKit' ) ) :

	class HootKit {

		/**
		 * Plugin Info
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public $version;
		public $name;
		public $slug;
		public $file;
		public $dir;
		public $uri;
		public $plugin_basename;

		/**
		 * Set marker for older theme versions
		 *
		 * @since  1.1.0
		 * @access public
		 * @var    bool
		 */
		public $deprecated = false;

		/**
		 * Constructor method.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Plugin Info
			$this->version         = '2.0.2';
			$this->name            = 'HootKit';
			$this->slug            = 'hootkit';
			$this->file            = __FILE__;
			$this->dir             = trailingslashit( plugin_dir_path( __FILE__ ) );
			$this->uri             = trailingslashit( plugin_dir_url( __FILE__ ) );
			$this->plugin_basename = plugin_basename(__FILE__);

			// Load Plugin Files and Helpers
			$this->loader();

			// Plugin Loader - Load config, and modules based on config
			// -> Register HootKit configuration after theme has loaded (so that theme can hook in to alter Hootkit config)
			// -> init hook may be a bit late for us to load since 'widgets_init' is used to intialize widgets (unless we hook into init at 0, which is a bit messy)
			add_action( 'after_setup_theme', array( $this, 'loadplugin' ), 95 );

		}

		/**
		 * Load Plugin Files and Helpers
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function loader() {

			require_once( $this->dir . 'include/class-activation.php' );
			require_once( $this->dir . 'include/class-config.php' );
			require_once( $this->dir . 'include/class-helper-strings.php' );
			require_once( $this->dir . 'include/class-helper-mods.php' );
			require_once( $this->dir . 'include/class-helper-assets.php' );

		}

		/**
		 * Plugin Loader
		 * Load config, and modules based on config
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function loadplugin() {

			$this->deprecated = ( class_exists( 'Hoot_Theme' ) || class_exists( 'Hootubix_Theme' ) || class_exists( 'Maghoot_Theme' ) || class_exists( 'Dollah_Theme' ) ) ? true : false;

			// Dont load for non hoot themes for now - till hootkit widgets are migrated to gutenberg blocks
			// > since hoot themes are using remove_theme_support( 'widgets-block-editor' ) for now.
			if ( !$this->get_config( 'nohoot' ) || apply_filters( 'hootkit_forceload_nohoot', false ) ) {
			if ( !$this->deprecated || apply_filters( 'hootkit_forceload_deprecated', false ) ) {

				// Load Limited Core/Helper Functions
				// Template Functions - may be required in admin for creating live preview eg. so page builder
				require_once( $this->dir . 'include/template-functions.php' );

				// Load Limited Library for Non Hoot themes :: some deprecated theme versions 'may' have nohoot set to true
				if ( $this->get_config( 'nohoot' ) ) {
					require_once( $this->dir . 'include/hoot-library.php' );
					require_once( $this->dir . 'include/hoot-library-icons.php' );
				}

				// Admin Functions
				if ( is_admin() ) {
					require_once( $this->dir . 'admin/functions.php' );
				}

				// Modules
				require_once( $this->dir . 'widgets/class-widgets.php' );
				require_once( $this->dir . 'misc/class-miscmods.php' );

			}
			}

		}

		/**
		 * Get Config values.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  string $key    Config value to return / else return entire array
		 * @param  string $subkey Check for $subkey if config value is an array
		 * @return mixed
		 */
		public function get_config( $key = '', $subkey = '' ) {

			// Early Check in case config has changed
			// Now redundant since config is loaded within this->loader
			if ( empty( Helper_Config::$config ) )
				return array();

			// Return the value
			if ( $key && is_string( $key ) ) {
				if ( isset( Helper_Config::$config[ $key ] ) ) {
					if ( $subkey && ( is_string( $subkey ) || is_integer( $subkey ) ) ) {
						return ( isset( Helper_Config::$config[ $key ][ $subkey] ) ) ? Helper_Config::$config[ $key ][ $subkey ] : array();
					} else {
						return Helper_Config::$config[ $key ];
					}
				} else {
					return array();
				}
			} else {
				return Helper_Config::$config;
			}

		}

		/**
		 * Get String values.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  string $key
		 * @param  string $default
		 * @return string
		 */
		public function get_string( $key, $default = '' ) {
			$return = '';
			if ( !is_array( Helper_Strings::$strings ) ) {
				$return = '';
			} else {
				$return = ( !empty( Helper_Strings::$strings[ $key ] ) ? Helper_Strings::$strings[ $key ] : '' );
			}
			if ( !empty( $return ) && is_string( $return ) )
				return $return;
			elseif ( !empty( $default ) && is_string( $default ) )
				return $default;
			else return ucwords( str_replace( array( '-', '_' ), ' ' , $key ) );
		}

		/**
		 * Get HootKit modules
		 *
		 * @since  1.2.0
		 * @access public
		 * @param  string $key 'modules' 'supports'
		 * @param  string $subkey Check for $subkey if $key value is an array
		 * @return mixed
		 */
		public function get_mods( $key = '', $subkey = '' ) {
			if ( $key && is_string( $key ) ) {
				if ( isset( Helper_Mods::$mods[ $key ] ) ) {
					if ( $subkey && ( is_string( $subkey ) || is_integer( $subkey ) ) ) {
						return ( isset( Helper_Mods::$mods[ $key ][ $subkey] ) ) ? Helper_Mods::$mods[ $key ][ $subkey ] : array();
					} else {
						return Helper_Mods::$mods[ $key ];
					}
				} else {
					return array();
				}
			} else {
				return Helper_Mods::$mods;
			}
		}

		/**
		 * Get Active Modules
		 *
		 * @since  2.0.0
		 */
		public function get_activemods( $type = '' ) {
			if ( $type && is_string( $type ) )
				retrun( ( isset( Helper_Config::$config['activemods'][ $type ] ) ) ? Helper_Config::$config['activemods'][ $type ] : array() );
			else
				return Helper_Config::$config['activemods'];
		}

		/**
		 * Get HootKit modules of type
		 *
		 * @since  2.0.0
		 * @param $type 'widget' 'block' 'misc'
		 */
		public function get_modtype( $type, $keys = false ) {
			$modtypes = array();
			foreach ( Helper_Mods::$mods['modules'] as $slug => $atts )
				if ( isset( $atts['types'] ) && \in_array( $type, $atts['types'] ) )
					$modtypes[ $slug ] = $atts;
			return ( ( $keys === false ) ? $modtypes : array_keys( $modtypes ) );
		}

		/**
		 * Returns the instance
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {

			static $instance = null;

			if ( is_null( $instance ) ) {
				$instance = new self;
			}

			return $instance;
		}

	}

	/**
	 * Gets the instance of the `HootKit` class. This function is useful for quickly grabbing data
	 * used throughout the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	function hootkit() {
		return HootKit::get_instance();
	}

	// Lets roll!
	hootkit();

endif;