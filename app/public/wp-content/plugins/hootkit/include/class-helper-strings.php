<?php
/**
 * HootKit Strings
 *
 * @package Hootkit
 */

namespace HootKit\Inc;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\HootKit\Inc\Helper_Strings' ) ) :

	class Helper_Strings {

		/**
		 * Class Instance
		 */
		private static $instance;

		/**
		 * Strings
		 */
		public static $strings = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			if ( null === self::$strings ) {
				$strings = self::defaults();
				self::$strings = wp_parse_args( apply_filters( 'hootkit_strings', array() ), $strings );
			}
		}

		/**
		 * Default Strings
		 */
		public static function defaults() {
			return array(

				'sliders'     => __( 'Slider Widgets',  'hootkit' ),
				'content'     => __( 'Content Widgets', 'hootkit' ),
				'post'        => __( 'Post Widgets',    'hootkit' ),
				'display'     => __( 'Display Widgets', 'hootkit' ),
				'woocom'      => __( 'Woocommerce',     'hootkit' ),
				'misc'        => __( 'Misc. Features',  'hootkit' ),


				'widget-prefix' => __( 'HK > ',  'hootkit' ),

				'image'            => __( 'Slider Images',       'hootkit' ),
				'slider-image'     => __( 'Slider Images',       'hootkit' ),
				'postimage'        => __( 'Posts Slider',        'hootkit' ),
				'slider-postimage' => __( 'Posts Slider',        'hootkit' ),

				'carousel'         => __( 'Carousel',            'hootkit' ),
				'postcarousel'     => __( 'Posts Carousel',      'hootkit' ),
				'postlistcarousel' => __( 'Posts List Carousel', 'hootkit' ),

				'productcarousel'         => __( 'Products Carousel',      'hootkit' ),
				'productlistcarousel'     => __( 'Products List Carousel', 'hootkit' ),

				'content-products-blocks' => __( 'Products Blocks',       'hootkit' ),
				'product-list'            => __( 'Products List',         'hootkit' ),
				'products-ticker'         => __( 'Products Ticker',       'hootkit' ),
				'products-search'         => __( 'Products Search',       'hootkit' ),
				'products-carticon'       => __( 'Products Cart Icon',    'hootkit' ),
				'fly-cart'                => __( 'Offscreen WooCommerce Cart', 'hootkit' ),

				'announce'             => __( 'Announce',       'hootkit' ),
				'content-blocks'       => __( 'Content Blocks', 'hootkit' ),
				'content-posts-blocks' => __( 'Posts Blocks',   'hootkit' ),
				'cta'                  => __( 'Call To Action', 'hootkit' ),
				'icon'                 => __( 'Icon',           'hootkit' ),
				'post-grid'            => __( 'Posts Grid',     'hootkit' ),
				'post-list'            => __( 'Posts List',     'hootkit' ),
				'social-icons'         => __( 'Social Icons',   'hootkit' ),
				'ticker'               => __( 'Ticker',         'hootkit' ),
				'ticker-posts'         => __( 'Posts Ticker',   'hootkit' ),
				'profile'              => __( 'About/Profile',  'hootkit' ),
				'content-grid'         => __( 'Content Grid',   'hootkit' ),
				'cover-image'          => __( 'Cover Image',    'hootkit' ),

				'contact-info'         => __( 'Contact Info',   'hootkit' ),
				'number-blocks'        => __( 'Number Blocks',  'hootkit' ),
				'vcards'               => __( 'Vcards',         'hootkit' ),
				'buttons'              => __( 'Buttons',        'hootkit' ),
				'icon-list'            => __( 'Icon List',      'hootkit' ),
				'notice'               => __( 'Notice Box',     'hootkit' ),
				'toggle'               => __( 'Toggle',         'hootkit' ),
				'tabs'                 => __( 'Tabs',           'hootkit' ),


				'top-banner'      => __( 'Top Banner',        'hootkit' ),
				'shortcode-timer' => __( 'Timer (shortcode)', 'hootkit' ),


				'white'        => __( 'White',           'hootkit' ),
				'black'        => __( 'Black',           'hootkit' ),
				'brown'        => __( 'Brown',           'hootkit' ),
				'brownbright'  => __( 'Brown (Bright)',  'hootkit' ),
				'blue'         => __( 'Blue',            'hootkit' ),
				'bluebright'   => __( 'Blue (Bright)',   'hootkit' ),
				'cyan'         => __( 'Cyan',            'hootkit' ),
				'cyanbright'   => __( 'Cyan (Bright)',   'hootkit' ),
				'green'        => __( 'Green',           'hootkit' ),
				'greenbright'  => __( 'Green (Bright)',  'hootkit' ),
				'yellow'       => __( 'Yellow',          'hootkit' ),
				'yellowbright' => __( 'Yellow (Bright)', 'hootkit' ),
				'amber'        => __( 'Amber',           'hootkit' ),
				'amberbright'  => __( 'Amber (Bright)',  'hootkit' ),
				'orange'       => __( 'Orange',          'hootkit' ),
				'orangebright' => __( 'Orange (Bright)', 'hootkit' ),
				'red'          => __( 'Red',             'hootkit' ),
				'redbright'    => __( 'Red (Bright)',    'hootkit' ),
				'pink'         => __( 'Pink',            'hootkit' ),
				'pinkbright'   => __( 'Pink (Bright)',   'hootkit' ),

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

	Helper_Strings::get_instance();

endif;