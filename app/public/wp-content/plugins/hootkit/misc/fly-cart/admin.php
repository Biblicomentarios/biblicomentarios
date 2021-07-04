<?php
/**
 * Fly Cart Modules
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Load Customizer Lib
if ( !function_exists( 'hootkit_add_customizer_options' ) )
	require_once( hootkit()->dir . 'misc/customizer.php' );

// Add admin customizer options
add_filter( 'hootkit_customizer_options', 'hootkit_flycart_customizer_options' );

// Include display template
// Alternate use: add_action( "{$themeslug}_main_wrapper_end", 'hootkit_flycart_display' ); where $themeslug = ( !empty( hoot_data()->basetemplate_slug ) ) ? hoot_data()->basetemplate_slug : strtolower( preg_replace( '/[^a-zA-Z0-9]+/', '_', trim( hoot_data()->template_name ) ) );
add_action( 'wp_footer', 'hootkit_flycart_display' );

/**
 * Add admin customizer options
 *
 * @since 1.2.0
 * @access public
 * @param array $options
 * @return array
 */
function hootkit_flycart_customizer_options( $options ) {

	$settings = array();
	$sections = array();
	$panels = array();
	$imagepath = hootkit()->uri . 'admin/assets/';

	$section = 'hk-flycart';
	$panel = 'woocommerce';

	$sections[ $section ] = array(
		'title'			=> esc_html__( 'Offscreen Cart (HootKit)', 'hootkit' ),
		// 'priority'		=> '7',
		'panel'			=> $panel,
	);

	$settings['hkfc_icon'] = array(
		'label'		=> esc_html__( 'Button Icon', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'radioimage',
		'choices'     => array(
			'fa-cart-arrow-down fas' => $imagepath . 'flycarticon01.jpg',
			'fa-cart-plus fas'       => $imagepath . 'flycarticon02.jpg',
			'fa-shopping-cart fas'   => $imagepath . 'flycarticon03.jpg',
			'fa-shipping-fast fas'   => $imagepath . 'flycarticon04.jpg',
			'fa-shopping-bag fas'    => $imagepath . 'flycarticon05.jpg',
			'fa-shopping-basket fas' => $imagepath . 'flycarticon06.jpg',
		),
		'default'	=> 'fa-shopping-cart fas',
		// 'priority'	=> '10',
		'transport' => 'postMessage',
	);

	$settings['hkfc_location'] = array(
		'label'		=> esc_html__( 'Cart Panel Location', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'select',
		'choices'     => array(
			'left'   => esc_html__( 'Left Edge of screen', 'hootkit'),
			'right'  => esc_html__( 'Right Edge of screen', 'hootkit'),
		),
		'default'	=> 'right',
		// 'priority'	=> '10',
		'transport' => 'postMessage',
	);

	$settings['hkfc_showonadd'] = array(
		'label'		=> esc_html__( 'Briefly show Cart panel when product is added/removed', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'checkbox',
		// 'priority'	=> '10',
		'default'	=> 1,
		'transport' => 'postMessage',
	);

	return array_replace_recursive( $options, array(
		'settings' => $settings,
		'sections' => $sections,
		'panels'   => $panels,
		) );
}

/**
 * Display fly cart in theme
 *
 * @since 1.2.0
 * @access public
 */
function hootkit_flycart_display(){
	/* include ( apply_filters( 'hootkit_fly_cart_template', hootkit()->dir . 'misc/fly-cart/view.php' ) ); */
	// Allow theme/child-themes to use their own template
	$flycart_template = hoot_get_widget( 'fly-cart', false );
	// Use Hootkit template if theme does not have one
	$flycart_template = ( $flycart_template ) ? $flycart_template : hootkit()->dir . 'misc/fly-cart/view.php';
	// Fire up the template
	if ( is_string( $flycart_template ) && file_exists( $flycart_template ) ) include ( $flycart_template );
}