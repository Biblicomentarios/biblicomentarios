<?php
/**
 * Customizer Functions
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Function to modify the settings array and prepare it for Customizer Library Interface functions
 * 
 * @since 3.0.0
 * @access public
 * @param array $settings
 * @return array
 */
function hoot_customize_prepare_settings( $settings ){

	// Return array
	$value = array();

	// Unique count to create id
	static $count = 1;

	foreach ( $settings as $key => $setting ) {
		if ( isset( $setting['type'] ) ) :

			$new_value = array();
			$new_value = apply_filters( 'hoot_customize_prepare_settings', $new_value, $key, $setting, $count );
			if ( !empty( $new_value ) )
				$value = array_merge( $value, $new_value );
			else
				$value[ $key ] = $setting;

			$count++;

		else:

			// Add setting as is
			$value[ $key ] = $setting;

		endif;
	}

	// Return prepared settings array
	return $value;
}
add_filter( 'hoot_customize_add_settings', 'hoot_customize_prepare_settings', 999 );

/**
 * Helper function to return defaults
 *
 * @since 3.0.0
 * @param string
 * @return mixed $default
 */
function hoot_customize_get_default( $setting ) {

	$hoot_customize = Hoot_Customize::get_instance();
	$settings = $hoot_customize->get_options('settings');

	if ( isset( $settings[$setting]['default'] ) )
		return $settings[$setting]['default'];
	else
		return '';

}

/**
 * Helper function to return choices
 *
 * @since 3.0.0
 * @param string
 * @return mixed $default
 */
function hoot_customize_get_choices( $setting ) {

	$hoot_customize = Hoot_Customize::get_instance();
	$settings = $hoot_customize->get_options('settings');

	if ( isset( $settings[$setting]['choices'] ) ) {
		return $settings[$setting]['choices'];
	}

}

/**
 * Helper function to get theme mod value and sanitize it
 * 
 * If no value has been saved, it returns $default provided by the theme.
 * If no $default provided, it checks the default value specified in the customizer settings..
 * 
 * @since 3.0.0
 * @access public
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function hoot_get_mod( $name, $default = NULL ) {

	if ( is_customize_preview() ) {

		// We cannot use "if ( !empty( $mod ) )" as this will become false for empty values, and hence fallback to default. isset() is not an option either as $mod is always set. Hence we calculate the default here, and supply it as second argument to get_theme_mod()
		$default = ( $default !== NULL ) ? $default : hoot_customize_get_default( $name );
		$mod = get_theme_mod( $name, $default );

		return apply_filters( 'hoot_get_mod_customize', $mod, $name, $default );

	} else {

		// Return Value
		$returnvalue = false;

		// Cache
		static $mods = NULL;

		// Set cache with database values
		if ( !$mods ) {
			$mods = get_theme_mods();
			$mods = apply_filters( 'hoot_get_mods', $mods );
		}

		// Return value if set
		if ( isset( $mods[$name] ) ) {
			$returnvalue = $mods[$name];
		}
		// Return default if provided
		elseif ( $default !== NULL ) {
			$returnvalue = $default;
		}
		// Return default theme option value specified in customizer settings
		else {
			$default = hoot_customize_get_default( $name );
			if ( !empty( $default ) ) {
				$returnvalue = $default;
			}
		}

		// Filter applied as in get_theme_mod() core function
		$returnvalue = apply_filters( "theme_mod_{$name}", $returnvalue );

		if ( $returnvalue !== false ) {
			// Sanitize Value
			$returnvalue = hoot_sanitize_get_mod( $returnvalue, $name );
		}

		return $returnvalue;

	}

}