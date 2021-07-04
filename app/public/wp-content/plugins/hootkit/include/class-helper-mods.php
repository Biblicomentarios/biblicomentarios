<?php
/**
 * HootKit Modules
 *
 * @package Hootkit
 */

namespace HootKit\Inc;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\HootKit\Inc\Helper_Mods' ) ) :

	class Helper_Mods {

		/**
		 * Class Instance
		 */
		private static $instance;

		/**
		 * Mods
		 */
		public static $mods = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			if ( null === self::$mods ) {
				self::$mods = self::defaults();
				add_action( 'after_setup_theme', array( $this, 'remove_deprecated' ), 12 );
			}
		}

		/**
		 * Remove Deprecated Modules
		 */
		public function remove_deprecated() {
			// Remove all widgets
			if ( apply_filters( 'hootkit_deprecate_widgets', false ) )
				foreach ( self::$mods['modules'] as $mod => $atts )
					if ( ( $key = array_search( 'widget', $atts['types'] ) ) !== false ) {
						unset( self::$mods['modules'][ $mod ]['types'][ $key ] );
						if ( empty( self::$mods['modules'][ $mod ]['types'] ) )
							unset( self::$mods['modules'][ $mod ] );
					}
		}

		/**
		 * Default Module Atts
		 */
		public static function defaults() {
			return array(

				'supports'    => array(
					'cta-styles', 'content-blocks-style5', 'content-blocks-style6', 'slider-styles', 'widget-subtitle',
					'grid-widget', // JNES@deprecated <= HootKit v1.1.3 @9.20 postgrid=>grid-widget
					'list-widget', // JNES@deprecated <= HootKit v1.1.3 @9.20 postslist=>list-widget
				),

				'modules' => array(

					// DISPLAY SET: Sliders
					'slider-image' => array(
						'types'       => array( 'widget' ),                  // Module Types available
						'requires'    => array( 'lightslider', 'wp-media' ), // Required components
						'displaysets' => array( 'sliders' ),                 // Settings Set
						'desc'        => '',                                 // Settings info popover
					),
					'carousel' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'lightslider', 'wp-media' ), /*'font-awesome'*/
						'displaysets' => array( 'sliders' ),
					),
					'ticker' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'font-awesome' ),
						'displaysets' => array( 'sliders' ),
					),

					// DISPLAY SET: Posts
					'content-posts-blocks' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'select2' ),
						'displaysets' => array( 'post' ),
					),
					'post-grid' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'lightslider', 'select2' ),
						'displaysets' => array( 'post' ),
					),
					'post-list' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'select2' ),
						'displaysets' => array( 'post' ),
					),
					'postcarousel' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'lightslider', 'select2' ),
						'displaysets' => array( 'sliders', 'post' ),
					),
					'postlistcarousel' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'lightslider', 'select2' ),
						'displaysets' => array( 'sliders', 'post' ),
					),
					'ticker-posts' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'select2' ),
						'displaysets' => array( 'sliders', 'post' ),
					),
					'slider-postimage' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'lightslider', 'select2' ),
						'displaysets' => array( 'sliders', 'post' ),
					),

					// DISPLAY SET: Content
					'announce' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'font-awesome' ),
						'displaysets' => array( 'content' ),
					),
					'profile' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'wp-media' ),
						'displaysets' => array( 'content' ),
					),
					'cta' => array(
						'types'       => array( 'widget' ),
						'displaysets' => array( 'content' ),
					),
					'content-blocks' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'font-awesome', 'wp-media' ),
						'displaysets' => array( 'content' ),
					),
					'content-grid' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'lightslider', 'wp-media' ),
						'displaysets' => array( 'content' ),
					),
					'contact-info' => array(
						'types'       => array( 'widget' ),
						'displaysets' => array( 'content' ),
					),
					'icon-list' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'font-awesome' ),
						'displaysets' => array( 'content' ),
					),
					'notice' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'font-awesome' ),
						'displaysets' => array( 'content' ),
					),
					'number-blocks' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'circliful' ),
						'displaysets' => array( 'content' ),
					),
					'tabs' => array(
						'types'       => array( 'widget' ),
						'displaysets' => array( 'content' ),
					),
					'toggle' => array(
						'types'       => array( 'widget' ),
						'displaysets' => array( 'content' ),
					),
					'vcards' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'wp-media' ),
						'displaysets' => array( 'content' ),
					),

					// DISPLAY SET: Display
					'buttons' => array(
						'types'       => array( 'widget' ),
						'displaysets' => array( 'display' ),
					),
					'cover-image' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'lightslider', 'wp-media' ),
						'displaysets' => array( 'display' ),
					),
					'icon' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'font-awesome' ),
						'displaysets' => array( 'display' ),
					),
					'social-icons' => array(
						'types'       => array( 'widget' ),
						'displaysets' => array( 'display' ),
					),

					// DISPLAY SET: Misc
					'top-banner' => array(
						'types'       => array( 'misc' ),
						'requires'    => array( 'customizer' ),
						'displaysets' => array( 'misc' ),
					),
					'shortcode-timer' => array(
						'types'       => array( 'misc' ),
						'displaysets' => array( 'misc' ),
					),
					'fly-cart' => array(
						'types'       => array( 'misc' ),
						'requires'    => array( 'woocommerce', 'font-awesome', 'customizer' ),
						'displaysets' => array( 'woocom', 'misc' ),
					),

					// DISPLAY SET: WooCom
					'products-carticon' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'woocommerce', 'font-awesome' ),
						'displaysets' => array( 'woocom' ),
					),
					'content-products-blocks' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'woocommerce', 'select2' ),
						'displaysets' => array( 'woocom' ),
					),
					'product-list' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'woocommerce', 'select2' ),
						'displaysets' => array( 'woocom' ),
					),
					'productcarousel' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'woocommerce', 'lightslider', 'select2' ),
						'displaysets' => array( 'sliders', 'woocom' ),
					),
					'productlistcarousel' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'woocommerce', 'lightslider', 'select2' ),
						'displaysets' => array( 'sliders', 'woocom' ),
					),
					'products-ticker' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'woocommerce', 'select2' ),
						'displaysets' => array( 'sliders', 'woocom' ),
					),
					'products-search' => array(
						'types'       => array( 'widget' ),
						'requires'    => array( 'woocommerce' ),
						'displaysets' => array( 'woocom' ),
					),
				),

			);
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

	Helper_Mods::get_instance();

endif;