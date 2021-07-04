<?php
/**
 * HootKit Config
 * This file is loaded at 'after_setup_theme' hook @priority 90
 *
 * @package Hootkit
 */

namespace HootKit\Inc;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\HootKit\Inc\Helper_Config' ) ) :

	class Helper_Config {

		/**
		 * Class Instance
		 */
		private static $instance;

		/**
		 * Config
		 */
		public static $config = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			if ( null === self::$config ) {
				self::$config = self::defaults();
				add_action( 'after_setup_theme', array( $this, 'default_presets' ), 5 );
			}

			add_action( 'after_setup_theme', array( $this, 'themeregister' ), 90 );
			add_action( 'after_setup_theme', array( $this, 'sanitizeconfig' ), 92 );
			add_action( 'after_setup_theme', array( $this, 'setactivemodules' ), 93 );

		}

		/**
		 * Register theme config
		 */
		public function themeregister() {

			$themeconfig = apply_filters( 'hootkit_register', array() );
			$themeconfig = $this->maybe_restructure( $themeconfig );

			self::$config = wp_parse_args( $themeconfig, self::$config );

		}

		/**
		 * Restructure config array from theme if needed
		 */
		private function maybe_restructure( $themeconfig ) {
			if ( !empty( $themeconfig['modules'] ) && is_array( $themeconfig['modules'] ) ) {
				// Rename slider slugs
				if ( !empty( $themeconfig['modules']['sliders'] ) ) {
					foreach ( $themeconfig['modules']['sliders'] as $slkey => $name ) {
						if ( \in_array( $name, array( 'image', 'postimage' ) ) )
							$themeconfig['modules']['sliders'][$slkey] = 'slider-' . $name;
					}
				}
				// Merge module associative array to a sequential array
				// if( array_keys( $themeconfig['modules'] ) !== range( 0, count( $themeconfig['modules'] ) - 1 ) ) {
				if ( !empty( $themeconfig['modules']['widgets'] ) ) {
					$newconfig_modules = array();
					foreach ( $themeconfig['modules'] as $mergearray )
						if ( is_array( $mergearray ) )
							$newconfig_modules = array_merge( $newconfig_modules, $mergearray );
					$themeconfig['modules'] = $newconfig_modules;
				}
			}
			return $themeconfig;
		}

		/**
		 * Sanitize config array from theme
		 */
		public function sanitizeconfig() {
			$hkmodules = hootkit()->get_mods( 'modules' );
			$hksupports = hootkit()->get_mods( 'supports' );

			/* Sanitize Theme Supported Modules against HootKit modules and arrange in order of hootkitmods */
			$modarray = array();
			if ( !empty( self::$config['modules'] ) && is_array( self::$config['modules'] ) ) {
				foreach ( $hkmodules as $modname => $modsettings ) {
					if ( in_array( $modname, self::$config['modules'] ) )
						$modarray[] = $modname;
				}
			}
			self::$config['modules'] = $modarray;

			/* Sanitize Theme Supported Premium Modules against HootKit modules */
			$themeslug = ( function_exists( 'hoot_data' ) ) ? strtolower( preg_replace( '/[^a-zA-Z0-9]+/', '-', trim( hoot_data( 'template_name' ) ) ) ) : '';
			if ( !empty( $themeslug ) && in_array( $themeslug, self::$config['themelist'] ) ) {
				if ( !empty( self::$config['premium'] ) && is_array( self::$config['premium'] ) ) {
					foreach ( self::$config['premium'] as $modkey => $modname ) {
						if ( !array_key_exists( $modname, $hkmodules ) )
							unset( self::$config['premium'][$modkey] );
					}
				}
			} else {
				self::$config['premium'] = array();
			}

			/* Sanitize Theme specific supported settings against HootKit supported settings */
			if ( !empty( self::$config['supports'] ) && is_array( self::$config['supports'] ) ) {
				foreach ( self::$config['supports'] as $skey => $support ) {
					if ( !in_array( $support, $hksupports ) )
						unset( self::$config['supports'][ $skey ] );
				}
			}

			/* Remove woocommerce modules if plugin is inactive */
			if ( ! class_exists( 'WooCommerce' ) ) {
				foreach ( self::$config['modules'] as $modkey => $modname ) {
					if ( !empty( $hkmodules[$modname]['requires'] ) && in_array( 'woocommerce', $hkmodules[$modname]['requires'] ) )
						unset( self::$config['modules'][$modkey] );
				}
			}

		}

		/**
		 * Set User Activated modules
		 *
		 * @since  1.1.0
		 */
		public function setactivemodules() {

			$activemodules = get_option( 'hootkit-activemods', false );
			$store = array();

			foreach ( array( 'widget', 'block', 'misc' ) as $type ) {
				if ( $activemodules !== false ) {
					if ( !empty( $activemodules[ $type . '-disabled' ] ) )
						$store[ $type ] = array();
					elseif ( !empty( $activemodules[ $type ] ) && is_array( $activemodules[ $type ] ) )
						$store[ $type ] = $activemodules[ $type ];
					else
						$store[ $type ] = array_intersect( hootkit()->get_modtype( $type, true ), self::$config['modules'] );
				} else {
					// Set default active modules :: all deactive if (bool) false :: all active if empty
					if ( self::$config['activemods'][ $type ] === false )
						$store[ $type ] = array();
					elseif ( !empty( self::$config['activemods'][ $type ] ) && is_array( self::$config['activemods'][ $type ] ) )
						$store[ $type ] = self::$config['activemods'];
					else
						$store[ $type ] = array_intersect( hootkit()->get_modtype( $type, true ), self::$config['modules'] );
				}
			}
			self::$config['activemods'] = apply_filters( 'hootkit_active_modules', $store );
			// Set to free version: var_dump(hootkit()->get_modtype( 'widget', true )); var_dump(self::$config); exit;

		}

		/**
		 * Config Structure (Defaults)
		 */
		public static function defaults() {
			return array(
				// Set true for all non wphoot themes
				'nohoot'    => true,
				// If theme is loading its own css, hootkit wont load its own default styles
				'theme_css' => false,
				// Theme Supported Modules
				// @todo 'ticker' width bug: css width percentage does not work inside table/flex layout => theme should remove support if theme markup does not explicitly support this (i.e. max-width provided for ticker boxes inside table cells)
				'modules'   => array( 'slider-image', 'slider-postimage', 'announce', 'content-blocks', 'content-posts-blocks', 'cta', 'icon', 'post-grid', 'post-list', 'social-icons', 'ticker', 'content-grid', 'cover-image', ),
				// Premium modules list
				'premium' => array(),
				// Active Modules (user settings)
				// Optional: Themes can pass an array here to set them as defaults (before user settings saved)
				//           Set to boolean true for all active by default, empty array / false for all deactive by default
				'activemods' => array(
					'widget' => array(),
					'block' => array(),
					'misc' => array(),
				),
				// Misc theme specific settings
				// JNES@deprecated <= Unos v2.7.1 @12.18
				'settings' => array(),
				// Misc theme specific settings
				'supports' => array(),
				// wpHoot Themes
				'themelist' => array(
					'chromatic',		'dispatch',			'responsive-brix',
					'brigsby',			'creattica',
					'metrolo',			'juxter',			'divogue',
					'hoot-ubix',		'magazine-hoot',	'dollah',
					'hoot-business',	'hoot-du',
					'unos',				'unos-publisher',	'unos-magazine-vu',
					'unos-business',	'unos-glow',		'unos-magazine-black',
					'unos-storebell',	'unos-minimastore',
					'nevark',			'neux',				'magazine-news-byte',
				),
				// Default Styles
				'presets'   => array(),
				// Default Styles
				'presetcombo'   => array(),
			);
		}

		/**
		 * Config Structure (Defaults)
		 * >> after hootkit() is available (constructor executed)
		 */
		public static function default_presets() {
			self::$config['presets'] = array(
				'white'  => hootkit()->get_string('white'),
				'black'  => hootkit()->get_string('black'),
				'brown'  => hootkit()->get_string('brown'),
				'blue'   => hootkit()->get_string('blue'),
				'cyan'   => hootkit()->get_string('cyan'),
				'green'  => hootkit()->get_string('green'),
				'yellow' => hootkit()->get_string('yellow'),
				'amber'  => hootkit()->get_string('amber'),
				'orange' => hootkit()->get_string('orange'),
				'red'    => hootkit()->get_string('red'),
				'pink'   => hootkit()->get_string('pink'),
			);
			self::$config['presetcombo'] = array(
				'white'        => hootkit()->get_string('white'),
				'black'        => hootkit()->get_string('black'),
				'brown'        => hootkit()->get_string('brown'),
				'brownbright'  => hootkit()->get_string('brownbright'),
				'blue'         => hootkit()->get_string('blue'),
				'bluebright'   => hootkit()->get_string('bluebright'),
				'cyan'         => hootkit()->get_string('cyan'),
				'cyanbright'   => hootkit()->get_string('cyanbright'),
				'green'        => hootkit()->get_string('green'),
				'greenbright'  => hootkit()->get_string('greenbright'),
				'yellow'       => hootkit()->get_string('yellow'),
				'yellowbright' => hootkit()->get_string('yellowbright'),
				'amber'        => hootkit()->get_string('amber'),
				'amberbright'  => hootkit()->get_string('amberbright'),
				'orange'       => hootkit()->get_string('orange'),
				'orangebright' => hootkit()->get_string('orangebright'),
				'red'          => hootkit()->get_string('red'),
				'redbright'    => hootkit()->get_string('redbright'),
				'pink'         => hootkit()->get_string('pink'),
				'pinkbright'   => hootkit()->get_string('pinkbright'),
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

	Helper_Config::get_instance();

endif;