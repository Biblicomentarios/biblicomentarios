<?php
/**
 * HootKit Misc Module
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

if ( ! class_exists( '\HootKit\Mods\MiscMods' ) ) :

	class MiscMods {

		/**
		 * Class Instance
		 */
		private static $instance;

		/**
		 * Active miscmods array
		 */
		private $activemiscmods = array();

		/**
		 * Required components array
		 */
		private $requires = array();

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->activemiscmods = hootkit()->get_config( 'activemods', 'misc' );
		
			if ( !empty( $this->activemiscmods ) ) {

				$modules = hootkit()->get_mods( 'modules' );
				foreach ( $this->activemiscmods as $miscmod )
					if ( !empty( $modules[$miscmod]['requires'] ) )
						$this->requires = array_merge( $this->requires, $modules[$miscmod]['requires'] );

				$this->load_assets();
				$this->load_adminassets();
				$this->load_miscmods();

			}

		}

		/**
		 * Load assets
		 *
		 * @since  2.0.0
		 */
		private function load_assets() {

			if ( in_array( 'font-awesome', $this->requires ) )
				Helper_Assets::add_style( 'font-awesome' );

			if( ! hootkit()->get_config( 'theme_css' ) )
				Helper_Assets::add_style( hootkit()->slug );

			Helper_Assets::add_script( hootkit()->slug . '-miscmods' );
			add_action( 'wp_enqueue_scripts', array( $this, 'localize_script' ), 11 );

		}

		/**
		 * Pass script data
		 *
		 * @since  2.0.0
		 */
		public function localize_script() {
			wp_localize_script(
				hootkit()->slug . '-miscmods',
				'hootkitMiscmodsData',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' )
				)
			);
		}

		/**
		 * Load assets
		 *
		 * @since  2.0.0
		 */
		private function load_adminassets() {

			if ( in_array( 'customizer', $this->requires ) )
				require_once( hootkit()->dir . 'misc/customizer.php' );

		}

		/**
		 * Load individual miscmods
		 *
		 * @since  2.0.0
		 */
		private function load_miscmods() {

			foreach ( $this->activemiscmods as $miscmod )
				if ( file_exists( hootkit()->dir . 'misc/' . sanitize_file_name( $miscmod ) . '/admin.php' ) )
					require_once( hootkit()->dir . 'misc/' . sanitize_file_name( $miscmod ) . '/admin.php' );

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

	MiscMods::get_instance();

endif;