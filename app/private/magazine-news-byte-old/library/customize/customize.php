<?php
/**
 * Hoot Customizer framework is an extended version of the
 * Customizer Library v1.3.0, Copyright 2010 - 2014, WP Theming http://wptheming.com
 * and is licensed under GPLv2
 *
 * This file is loaded at 'after_setup_theme' hook with 2 priority.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/** Include Hoot Customizer files **/
require_once ( hoot_data()->libdir . 'customize/functions.php' );
require_once ( hoot_data()->libdir . 'customize/interface.php' );
require_once ( hoot_data()->libdir . 'customize/sanitization.php' );

/** Include custom controls **/
foreach ( glob( hoot_data()->libdir . 'customize/control-*.php' ) as $file_path ) {
	include_once( $file_path );
}

/**
 * Class wrapper with useful methods for interacting with the hoot customizer.
 *
 * @since 3.0.0
 */
final class Hoot_Customize {

	/**
	 * The one instance of Hoot_Customize.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var Hoot_Customize The one instance for the singleton.
	 */
	private static $instance;

	/**
	 * The array for storing $options.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array Holds the options array.
	 */
	public $options = array();

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * Singleton from outside of this class.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function __construct() {

		/* Initialize Options Array */
		$this->options = array(
			'settings' => array(),
			'sections' => array(),
			'panels' => array(),
			);

	}

	/**
	 * Add customizer option to Options Array
	 *
	 * @since 3.0.0
	 * @access public
	 * @return void
	 */
	public function add_options( $options = array() ) {
		foreach ( array( 'settings', 'sections', 'panels' ) as $key ) {
			if ( isset( $options[ $key ] ) && is_array( $options[ $key ] ) && !empty( $options[ $key ] ) ) {
				$add_options = $options[ $key ];
				$add_options = apply_filters( "hoot_customize_add_{$key}" , $add_options );
				if ( is_array( $add_options ) && !empty( $add_options ) )
					$this->options[ $key ] = array_merge( $this->options[ $key ], $add_options );
			}
		}
	}

	/**
	 * Remove customizer settings from Options Array Settings
	 *
	 * @since 3.0.0
	 * @access public
	 * @return void
	 */
	public function remove_settings( $key ) {
		if ( is_array( $key ) ) {
			foreach ( $key as $singlekey ) {
				if ( isset( $this->options['settings'][$singlekey] ) )
					unset( $this->options['settings'][$singlekey] );
			}
		} elseif ( isset( $this->options['settings'][$key] ) ) {
			unset( $this->options['settings'][$key] );
		}
	}

	/**
	 * Get Options Array
	 *
	 * @since 3.0.0
	 * @access public
	 * @param string $return Select which option array to return, or to return whole array
	 * @return array
	 */
	public function get_options( $return = '' ) {
		switch( $return ) {
			case 'settings':
				if ( !empty( $this->options['settings'] ) )
					return $this->options['settings'];
				else
					return array();
				break;
			case 'sections':
				if ( !empty( $this->options['sections'] ) )
					return $this->options['sections'];
				else
					return array();
				break;
			case 'panels':
				if ( !empty( $this->options['panels'] ) )
					return $this->options['panels'];
				else
					return array();
				break;
		}
		return $this->options;
	}

	/**
	 * Instantiate or return the one Hoot_Customize instance.
	 *
	 * @since 3.0.0
	 * @access public
	 * @return Hoot_Customize
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

/** Allow to include functions and files **/
do_action( 'hoot_customize_loaded' );