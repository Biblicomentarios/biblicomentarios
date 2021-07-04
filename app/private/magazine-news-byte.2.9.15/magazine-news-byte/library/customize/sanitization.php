<?php
/**
 * Declare Sanitization
 * - for retreiving theme_mod values using 'hoot_get_mod()'
 * - for 'sanitize_callback' attribute while adding settings to customize with '$wp_customize->add_setting'
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Sanitization function for return value of 'hoot_get_mod()'
 * 
 * @since 3.0.0
 * @access public
 * @param mixed $value
 * @param string $name
 * @return mixed
 */
function hoot_sanitize_get_mod( $value, $name ) {

	static $settings = array();

	/** Get Setting array from the Customizer Class **/
	if ( empty( $settings ) ) {
		$hoot_customize = Hoot_Customize::get_instance();
		$settings = $hoot_customize->get_options('settings');
	}

	if ( isset( $settings[ $name ] ) && isset( $settings[ $name ]['id'] ) ) {
		$id = $settings[ $name ]['id'];
		$setting = $settings[ $name ];

		// Use custom sanitization function is one is mentioned in settings
		if ( !empty( $setting['sanitize_callback'] ) && is_string( $setting['sanitize_callback'] ) && function_exists( $setting['sanitize_callback'] ) ) {

			$callback = $setting['sanitize_callback'];
			$value = $callback( $value, $name );

		}

		// Fallback to default functions
		else {

			$callback = hoot_customize_sanitize_callback( $setting['type'], $setting, $name );
			$value = ( is_string( $callback ) && function_exists( $callback ) ) ? $callback( $value, $name ) : false;

		} // endif

	} // endif

	return $value;
}

/**
 * Get sanitization function name for 'sanitize_callback' attribute during adding setting by '$wp_customize->add_setting'
 * Also used to get sanitization function name for sanitizing values for 'hoot_sanitize_get_mod'
 *
 * These callback functions will get passed '$value' and 'WP_Customize_Setting' Object
 * So we can't use some sanitization functions directly and need a wrapper for some of them
 *
 * @since 3.0.0
 * @param string $type
 * @param array $setting
 * @param string $name
 * @return string
 */
function hoot_customize_sanitize_callback( $type, $setting, $name ) {

	$callback = false;

	if ( 'text' == $type )
		$callback = 'hoot_sanitize_text';

	elseif ( 'textarea' == $type )
		$callback = 'hoot_sanitize_textarea';

	elseif ( 'url' == $type )
		$callback = 'esc_url';

	elseif ( 'upload' == $type || 'image' == $type )
		$callback = 'hoot_sanitize_upload';

	elseif ( 'checkbox' == $type )
		$callback = 'hoot_sanitize_checkbox';

	elseif ( 'range' == $type )
		$callback = 'hoot_sanitize_range';

	elseif ( 'dropdown-pages' == $type )
		$callback = 'absint';

	elseif ( 'color' == $type )
		// Since we are using default WP Customize Color, we won't have colors saved in database in rgba notation.
		// So we can use 'sanitize_hex_color' instead of 'hoot_sanitize_color'
		$callback = 'sanitize_hex_color';

	// Customizer specific wrapper
	elseif ( 'select' == $type || 'radio' == $type )
		$callback = 'hoot_sanitize_customize_enum';

	// Let custom controls define their sanitization function, and return
	// Custom controls will hook into this filter.
	// They will then check if $type corresponds to them. If so, they will return the sanitization callback function name
	return apply_filters( 'hoot_customize_sanitize_callback', $callback, $type, $setting, $name );

}

/**
 * Wrapper function for 'hoot_sanitize_enum' for customizer settings.
 * (this function is also used by custom radioimage control in addition to the select and radio controls from above)
 *
 * @since 3.0.0
 * @param string $value The value to sanitize.
 * @param mixed $setting 'WP_Customize_Setting' Object (called by Customizer) or Setting Name (called by hoot_get_mod)
 * @return mixed The sanitized value.
 */
function hoot_sanitize_customize_enum( $value, $setting ) {
	$name = '';
	if ( is_object( $setting ) )
		$name = $setting->id;
	elseif ( is_string( $setting ) )
		$name = $setting;

	$choices = hoot_customize_get_choices( $name );

	return hoot_sanitize_enum( $value, $choices );
}