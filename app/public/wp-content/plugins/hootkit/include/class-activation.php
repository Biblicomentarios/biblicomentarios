<?php
/**
 * HootKit Activation
 *
 * @package Hootkit
 */

namespace HootKit\Inc;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\HootKit\Inc\Activation' ) ) :

	class Activation {

		/**
		 * Class Instance
		 */
		private static $instance;

		/**
		 * Constructor
		 */
		public function __construct() {

			// Use hootkit()->file here leads to looping (since this file is called during
			// HootKit Constructor method) making the constructir run additional number of times
			// (De)Activation hooks cannot be registered inside 'plugins_loaded' hook.
			// @see https://developer.wordpress.org/reference/functions/register_activation_hook/
			$file = str_replace( array(
				'include\class-activation',
				'include/class-activation'
				), 'hootkit', __FILE__ );

			// Load Text Domain
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

			// Run on plugin activation
			register_activation_hook( $file, array( $this, 'plugin_activate' ) );
			add_action( 'plugins_loaded', array( $this, 'plugin_activate_backward' ), 0 );

			// Run on plugin deactivation
			register_deactivation_hook( $file, array( $this, 'plugin_deactivate' ) );

		}

		/**
		 * Load Plugin Text Domain
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function load_plugin_textdomain() {
			$rootdir = dirname( hootkit()->plugin_basename );
			$lang_dir = apply_filters( 'hootkit_languages_directory', $rootdir . '/languages/' );

			load_plugin_textdomain(
				hootkit()->slug,
				false,
				// https://www.php.net/manual/en/function.dirname.php 7.0.0 Added the optional levels parameter.
				// Pre PHP 7 : dirname() expects exactly 1 parameter, 2 given
				// basename( dirname( __FILE__ , 2 ) ) . '/languages/' // dirname( plugin_basename( __FILE__ ), 2 ) . '/languages/'
				$lang_dir
			);

		}

		/**
		 * Run when plugin is activated
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function plugin_activate() {
			$previous_activation = get_option( 'hootkit-activate' );
			if ( !$previous_activation ) add_option( 'hootkit-activate', time() );
		}
		/**
		 * Set activation for version prior to 1.1.0
		 */
		public function plugin_activate_backward() {
			if ( is_admin() && empty( get_option( 'hootkit-activate' ) ) )
				add_option( 'hootkit-activate', time() - ( 7 * 24 * 60 * 60 ) );
		}

		/**
		 * Run when plugin is deactivated
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function plugin_deactivate() {
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

	Activation::get_instance();

endif;