<?php
/**
 * Builds out customize options
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Configure and add panels, sections, settings/controls for the theme customizer
 *
 * @since 3.0.0
 * @param object $wp_customize The global customizer object.
 * @return void
 */
function hoot_customize_register( $wp_customize ) {

	$hoot_customize = Hoot_Customize::get_instance();
	$options = $hoot_customize->get_options();
	if ( empty( $options ) ) {
		return;
	}

	/** Add the panels **/
	if ( !empty( $options['panels'] ) && is_array( $options['panels'] ) ) {
		hoot_customize_add_panels( $options['panels'], $wp_customize );
	}

	/** Add the sections **/
	if ( !empty( $options['sections'] ) && is_array( $options['sections'] ) ) {
		hoot_customize_add_sections( $options['sections'], $wp_customize );
	}

	/** Exit if no settings to add **/
	if ( empty( $options['settings'] ) || !is_array( $options['settings'] ) )
		return;

	/** Objects added.. Use this hook instead of 'customize_register' hook to remove or modify any Customizer object, and to access the Customizer Manager. For adding, continue using 'customize_register' **/
	do_action( 'hoot_customize_registered', $wp_customize, $hoot_customize );

	// Sets the priority for each control added
	$loop = 0;

	// Add certain style attributes to allowed kses list (for description etc)
	add_filter( 'safe_style_css', 'hoot_customize_display_style_ksescss' );

	/** Loop through each of the settings **/
	foreach ( $options['settings'] as $id => $setting ) :
		if ( isset( $setting['type'] ) ) :

			/** Prepare Setting **/

			// Apply a default sanitization if one isn't set and
			// set blank active_callback if one isn't set
			$callback = hoot_customize_sanitize_callback( $setting['type'], $setting, $id );
			$setting = wp_parse_args( $setting, array(
				'label'             => '',
				'section'           => '',
				'sanitize_callback' => ( ( is_string( $callback ) && function_exists( $callback ) ) ? $callback : false ),
				'active_callback'   => '',
			) );

			// Set Priority (increment priority by 10 to allow child themes to insert controls in between)
			if ( ! isset( $setting['priority'] ) || ! is_numeric( $setting['priority'] ) ) {
				$loop += 10;
				$setting['priority'] = $loop;
			}
			if ( defined( 'HOOT_DEBUG' ) && true === HOOT_DEBUG )
				hoot_debug_info( "[{$setting['priority']}] {$id}\n" );

			// Set and prepare description
			$setting['description'] = empty( $setting['description'] ) ? '' : $setting['description'];
			$setting['description'] =  ( is_array( $setting['description'] ) ) ? (
										( !empty( $setting['description']['text'] ) ) ? $setting['description']['text'] : ''
										) : $setting['description'];

			/** Add selective refresh if available **/
			// Note: cannot apply selective_refresh for a setting which is used in any control's active_callback functions to determine if that control is active or not.

			if ( !empty( $setting['selective_refresh'] ) && is_array( $setting['selective_refresh'] ) && is_string( $setting['selective_refresh'][0] ) && !empty( $setting['selective_refresh'][1] ) && is_array( $setting['selective_refresh'][1] ) && !empty( $setting['selective_refresh'][1]['selector'] ) && !empty( $setting['selective_refresh'][1]['settings'] ) && !empty( $setting['selective_refresh'][1]['render_callback'] ) ) {
				$setting['transport'] = 'postMessage';
				$wp_customize->selective_refresh->add_partial( $setting['selective_refresh'][0], $setting['selective_refresh'][1] );
			}
			if ( isset( $setting['selective_refresh'] ) ) unset( $setting['selective_refresh'] );

			/** Add the setting **/

			hoot_customize_add_setting( $wp_customize, $id, $setting );

			/** Adds control **/

			switch ( $setting['type'] ) :

				/* input Text */
				case 'text':
				case 'url':
				case 'select':
				case 'radio':
				case 'checkbox':
				case 'range':
				case 'dropdown-pages':
					$wp_customize->add_control( $id, $setting );
					break;

				/* Textarea */
				case 'textarea':
					$wp_customize->add_control( $id, $setting );
					break;

				/* Color Picker */
				case 'color':
					$wp_customize->add_control(
						new WP_Customize_Color_Control( $wp_customize, $id, $setting )
					);
					break;

				/* Image Upload */
				case 'image':
					$wp_customize->add_control(
						new WP_Customize_Image_Control( $wp_customize, $id, array(
							'label'             => $setting['label'],
							'section'           => $setting['section'],
							'sanitize_callback' => $setting['sanitize_callback'],
							'priority'          => $setting['priority'],
							'active_callback'   => $setting['active_callback'],
							'description'       => $setting['description']
						) )
					);
					break;

				/* File Upload */
				case 'upload':
					$wp_customize->add_control(
						new WP_Customize_Upload_Control( $wp_customize, $id, array(
							'label'             => $setting['label'],
							'section'           => $setting['section'],
							'sanitize_callback' => $setting['sanitize_callback'],
							'priority'          => $setting['priority'],
							'active_callback'   => $setting['active_callback'],
							'description'       => $setting['description']
						) )
					);
					break;

				/* Allow custom controls to hook into interface */
				default:
					do_action( 'hoot_customize_control_interface', $wp_customize, $id, $setting );

			endswitch;

		endif;
	endforeach;

	// Remove style attributes added to allowed kses list (for description etc)
	remove_filter( 'safe_style_css', 'hoot_customize_display_style_ksescss' );

}

function hoot_customize_display_style_ksescss( $styles ) {
	$styles[] = 'display';
	$styles[] = 'list-style'; // list-style-type already in safe list
	return $styles;
};

add_action( 'customize_register', 'hoot_customize_register', 99 );

/**
 * Add the customizer panels
 * 
 * @since 3.0.0
 * @param array $panels
 * @return void
 */
function hoot_customize_add_panels( $panels, $wp_customize ) {

	$loop = 0;

	foreach ( $panels as $id => $panel ) {

		if ( ! isset( $panel['description'] ) ) {
			$panel['description'] = FALSE;
		}
		if ( ! isset( $panel['priority'] ) || ! is_numeric( $panel['priority'] ) ) {
			$loop += 10;
			$panel['priority'] = $loop;
		}
		if ( defined( 'HOOT_DEBUG' ) && true === HOOT_DEBUG )
			hoot_debug_info( "Panel [{$panel['priority']}] {$id}\n" );

		// Add Panel
		$wp_customize->add_panel( $id, $panel );

	}

}

/**
 * Add the customizer sections
 *
 * @since 3.0.0
 * @param array $sections
 * @return void
 */
function hoot_customize_add_sections( $sections, $wp_customize ) {

	$loop = 0;

	foreach ( $sections as $id => $section ) {

		if ( ! isset( $section['description'] ) ) {
			$section['description'] = FALSE;
		}
		if ( ! isset( $section['priority'] ) || ! is_numeric( $section['priority'] ) ) {
			$loop += 5;
			$section['priority'] = $loop;
		}
		if ( defined( 'HOOT_DEBUG' ) && true === HOOT_DEBUG )
			hoot_debug_info( "Section [{$section['priority']}] {$id}\n" );

		// Add Section
		$wp_customize->add_section( $id, $section );

	}

}

/**
 * Add the setting and proper sanitization
 *
 * @since 3.0.0
 * @param string $id
 * @param array $setting
 * @return void
 */
function hoot_customize_add_setting( $wp_customize, $id, $setting ) {

	$add_setting = wp_parse_args( $setting, array(
		'default'              => NULL,
		'option_type'          => 'theme_mod',
		'capability'           => 'edit_theme_options',
		'theme_supports'       => NULL,
		'transport'            => NULL,
		'sanitize_callback'    => 'wp_kses_post',
		'sanitize_js_callback' => NULL
	) );

	// Add Setting
	$wp_customize->add_setting( $id, array(
			'default'              => $add_setting['default'],
			'type'                 => $add_setting['option_type'],
			'capability'           => $add_setting['capability'],
			'theme_supports'       => $add_setting['theme_supports'],
			'transport'            => $add_setting['transport'],
			'sanitize_callback'    => $add_setting['sanitize_callback'],
			'sanitize_js_callback' => $add_setting['sanitize_js_callback']
		)
	);

}

/**
 * Enqueue scripts to customizer screen
 *
 * @since 3.0.0
 * @return void
 */
function hoot_customize_enqueue_scripts() {

	// Enqueue Styles
	wp_enqueue_style( 'font-awesome' );
	$style_uri = hoot_locate_style( hoot_data()->liburi . 'css/customize' );
	wp_enqueue_style( 'hoot-customize', $style_uri, array(),  hoot_data()->hoot_version );

	// Enqueue Scripts
	$script_uri = hoot_locate_script( hoot_data()->liburi . 'js/customize' );
	wp_enqueue_script( 'hoot-customize', $script_uri, array( 'jquery', 'wp-color-picker', 'customize-controls' ), hoot_data()->hoot_version, true );

	// Localize Script
	$data = apply_filters( 'hoot_customize_control_footer_js_data_object', array() );
	if ( is_array( $data ) && !empty( $data ) )
		wp_localize_script( 'hoot-customize', 'hoot_customize_data', $data );

}
// Load scripts at priority 11 so that Hoot Customizer Custom Controls have loaded their scripts
add_action( 'customize_controls_enqueue_scripts', 'hoot_customize_enqueue_scripts', 11 );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since 3.0.0
 * @return void
 */
function hoot_customize_preview_js() {

	// Check in /premium/include first if exist => else loads from lite/include version (usefule for overriding files)
	// If same name files exist and both need to be loaded, use file_exists() instead of hoot_locate_script()
	$script_uri = ( function_exists( 'hoot_locate_script' ) ) ? hoot_locate_script( hoot_data()->incuri . 'admin/js/customize-preview' ) : '';
	if ( $script_uri )
		wp_enqueue_script( 'hoot-customize-preview', $script_uri, array( 'customize-preview' ), hoot_data()->hoot_version, true );

}
add_action( 'customize_preview_init', 'hoot_customize_preview_js' );