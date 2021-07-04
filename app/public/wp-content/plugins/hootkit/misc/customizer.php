<?php
/**
 * Customizer Modules
 * This file is loaded at 'after_setup_theme' hook with 95 priority in HootKit->loadplugin()->class_miscmods
 *
 * @package Hootkit
 */

use \HootKit\Inc\Helper_Assets;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Return if Hoot_Customize class doesn't exist => Added for brevity, as this should not be the case
if ( !class_exists( 'Hoot_Customize' ) )
	return;

// Hook the options functions into 'init' hook @priority 15 (loads after themes options lite/prim loaded @priority 0)
// as this does not contain settings related to registering widgets ( which itself is hooked to 'init' at priority 1 )
add_action( 'init', 'hootkit_add_customizer_options', 15 );

// Load assets (as needed)
add_action( 'customize_preview_init', 'hootkit_customize_preview_js' );
add_action( 'customize_controls_enqueue_scripts', 'hootkit_customizer_enqueue_scripts', 15 ); // Load scripts at priority 15 so that Hoot Customizer Interface (11) / Custom Controls (10) / Theme Customize Controls (12) have loaded their scripts

/**
 * Binds JS handlers to make Customizer preview reload changes asynchronously.
 *
 * @since 1.1.1
 * @return void
 */
function hootkit_customize_preview_js() {
	$script_uri = Helper_Assets::get_uri( 'admin/assets/customize-preview', 'js' );
	if ( $script_uri )
		wp_enqueue_script( 'hootkit-customize-preview', $script_uri, array( 'customize-preview' ), hootkit()->version, true );
	// wp_localize_script( 'hootkit-customize-preview', 'hootkitPreviewData', array() );
}

/**
 * Enqueue custom scripts to customizer screen
 *
 * @since 1.1.1
 * @return void
 */
function hootkit_customizer_enqueue_scripts() {
	$script_uri = Helper_Assets::get_uri( 'admin/assets/customize-controls', 'js' );
	if ( $script_uri )
		wp_enqueue_script( 'hootkit-customize-controls', $script_uri, array( 'jquery', 'wp-color-picker', 'customize-controls', 'hoot-customize' ), hootkit()->version, true );
}

/**
 * Add customizer options for hootkit
 *
 * @since 1.1.1
 * @access public
 * @return void
 */
function hootkit_add_customizer_options() {

	// Options
	$options = array(
		'settings' => array(),
		'sections' => array(),
		'panels'   => array(),
	);
	$options = wp_parse_args( apply_filters( 'hootkit_customizer_options', $options ), $options );

	// Get hoot customize
	$hoot_customize = Hoot_Customize::get_instance();

	// Add Options
	$hoot_customize->add_options( array(
		'settings' => $options['settings'],
		'sections' => $options['sections'],
		'panels'   => $options['panels'],
		) );

}