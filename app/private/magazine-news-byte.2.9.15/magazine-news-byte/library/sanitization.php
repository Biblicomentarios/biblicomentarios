<?php
/**
 * Sanitization functions and filters
 * Although sanitization functions can be used directly, this file hooks these
 * functions to their respective filter as well, so data can be sanitized simply
 * by applying the corresponding filter to it.
 *
 * Used for data sanitization in
 * * Style Builder, Color functions, saving values in customize, hoot_get_mod
 * * Meta Options may also use these functions (probably via filters)
 * * Common functions like hoot_sanitize_hex and hoot_sanitize_fa are used throughout including frontend templates
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * sanitize_html_class works just fine for a single class
 * This is an extension to sanitize_html_class for sanitizing more than one class (with spaces or in an array)
 *
 * @param mixed string/array
 * @param mixed $fallback
 * @return mixed string / $fallback
 */
if ( !function_exists( 'hoot_sanitize_html_classes' ) ):
function hoot_sanitize_html_classes( $class, $fallback = null ) {
	if ( is_string( $class ) ) {
		$class = explode( " ", $class );
	} 
	if ( is_array( $class ) && count( $class ) > 0 ) {
		$class = array_map( "sanitize_html_class", $class );
		return implode( " ", $class );
	}
	else { 
		return sanitize_html_class( $class, $fallback );
	}
}
endif;

/**
 * Sanitization for text input fields
 * This function should not be used for fields meant for attributes. This is simply an extension
 * to allow basic tags (like a, h1, strong, em) in input fields meant for display (example: Title field)
 * 
 * Use 'sanitize_text_field' for input fields meant for purposes other than display text.
 * @link http://developer.wordpress.org/reference/functions/sanitize_text_field/
 *
 * @param string $input
 * @returns string $output
 */
if ( !function_exists( 'hoot_sanitize_text' ) ):
function hoot_sanitize_text( $input ) {
	return wp_kses_post( $input );
}
endif;

/**
 * Validates that the $input is one of the avilable choices
 * for that specific option.
 *
 * @param string $input
 * @returns string $output
 */
if ( !function_exists( 'hoot_sanitize_enum' ) ):
function hoot_sanitize_enum( $input, $options ) {
	$output = '';
	if ( is_array( $options ) && array_key_exists( $input, $options ) ) {
		$output = $input;
	}
	return $output;
}
endif;

/**
 * Sanitization for textarea field
 *
 * @param $input string
 * @return $output sanitized string
 */
if ( !function_exists( 'hoot_sanitize_textarea' ) ):
function hoot_sanitize_textarea( $input ) {
	return wp_kses_post( $input );
}
endif;

/**
 * Sanitization for editor input.
 * Returns unfiltered HTML if user has permissions.
 * @NU
 *
 * @param string $input
 * @returns string $output
 */
if ( !function_exists( 'hoot_sanitize_editor' ) ):
function hoot_sanitize_editor( $input ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$output = $input;
	}
	else {
		$output = wpautop( wp_kses_post( $input ) );
	}
	return $output;
}
endif;

/**
 * Sanitizes a range value
 *
 * @param int|string $value
 * @return int|string
 */
if ( !function_exists( 'hoot_sanitize_range' ) ):
function hoot_sanitize_range( $value ) {
	if ( is_numeric( $value ) ) {
		return $value;
	}
	return 0;
}
endif;

/**
 * Sanitization for checkbox input
 *
 * @param $input string (1 or empty) checkbox state
 * @return $output '1' or false
 */
if ( !function_exists( 'hoot_sanitize_checkbox' ) ):
function hoot_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = 1;
	} else {
		$output = 0;
	}
	return $output;
}
endif;

/**
 * Sanitization for multicheck
 *
 * @param array|string checkbox values as array or comma separated string
 * @param array $options Valid choices
 * @param bool $searchkeys Whether to look in keys or values of the choices array
 * @return array|string of allowed choices
 */
if ( !function_exists( 'hoot_sanitize_multicheck' ) ):
function hoot_sanitize_multicheck( $input, $options, $searchkeys = false ) {
	if ( is_array( $input ) ) {
		$format = 'array';
	} elseif( is_string( $input ) ) {
		$format = 'string';
		$input = array_map( 'trim', explode( ',', $input ) );
	} else {
		return false;
	}

	if ( is_array( $input ) && is_array( $options ) ) {
		$output = array();
		foreach ( $input as $value ) {
			if ( $searchkeys ) {
				if ( array_key_exists( $value, $options ) ) $output[] = $value;
			} else {
				if ( in_array( $value, $options ) ) $output[] = $value;
			}
		}
		return ( $format == 'string' ) ? implode( ',', $output ) : $output;
	}

	return false;
}
endif;

/**
 * File upload sanitization.
 * Returns a sanitized filepath if it has a valid extension.
 *
 * @param string $input filepath
 * @returns string $output filepath
 */
if ( !function_exists( 'hoot_sanitize_upload' ) ):
function hoot_sanitize_upload( $input ) {
	$output = '';
	$filetype = wp_check_filetype( $input );
	if ( $filetype["ext"] ) {
		$output = esc_url( $input );
	}
	return $output;
}
endif;

/**
 * Sanitization for color input.
 *
 * @param string $input Color value. "#" may or may not be prepended to the string.
 * @return string Color in hexidecimal notation. "#" is prepended
 */
if ( !function_exists( 'hoot_sanitize_color' ) ):
function hoot_sanitize_color( $input ) {
	return ( strpos( $input, ',' ) !== false ) ? hoot_sanitize_rgba( $input ) : hoot_sanitize_hex( $input );
}
endif;

/**
 * Sanitize a color represented in hexidecimal notation.
 * Used to sanitize color value for color fields in color, typography, background options
 *
 * @param string Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param string The value that this function should return if it cannot be recognized as a color.
 * @return string Color in hexidecimal notation. "#" is prepended
 */
if ( !function_exists( 'hoot_sanitize_hex' ) ):
function hoot_sanitize_hex( $hex ) {

	if ( !is_string( $hex ) )
		return '';

	$hex = trim( $hex );

	// Strip recognized prefixes and then add #
	if ( 0 === strpos( $hex, '#' ) ) {
		$hex = substr( $hex, 1 );
	}
	elseif ( 0 === strpos( $hex, '%23' ) ) {
		$hex = substr( $hex, 3 );
	}
	$hex = '#' . $hex;

	/* Regex match for 3 or 6 digit hex */
	if ( 0 === preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $hex ) ) { // for 6 hex: ( '/^[0-9a-fA-F]{6}$/', $hex )
		return '';
	} else {
		if ( intval( strlen( $hex ) ) == 4 )
			return $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2] . $hex[3] . $hex[3];
		elseif ( intval( strlen( $hex ) ) == 7 )
			return $hex;
		else
			return '';
	}
}
endif;

/**
 * Sanitize a color represented in rgba notation.
 * Used to sanitize color value for color fields in color, typography, background options
 *
 * @param string Color in rgba notation.
 * @return string Color in rgba notation.
 */
if ( !function_exists( 'hoot_sanitize_rgba' ) ):
function hoot_sanitize_rgba( $rgba ) {
	$rgba = str_replace( array( 'rgba', 'rgb', '(' , ')' ), '', $rgba );
	$array = explode( ',', $rgba );
	if ( is_array( $array ) && count( $array ) > 2 ) {
		$array = array_map( "floatval", $array );
		$r = ( $array[0] >= 0 && $array[0] <= 255 ) ? $array[0] : false;
		$g = ( $array[1] >= 0 && $array[1] <= 255 ) ? $array[1] : false;
		$b = ( $array[2] >= 0 && $array[2] <= 255 ) ? $array[2] : false;
		$a = 1;
		if ( isset( $array[3] ) ) {
			$array[3] = ( $array[3] > 100 ) ? 100 : $array[3];
			$array[3] = ( $array[3] > 1 ) ? $array[3]/100 : $array[3];
			$a = ( $array[3] >= 0 && $array[3] <= 1 ) ? $array[3] : false;
		}
		if ( $r !== false && $g !== false && $b !== false && $a !== false )
			return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $a . ')';
	}
	return '';
}
endif;

/**
 * Sanitization for background repeat
 *
 * @returns string $value if it is valid
 */
if ( !function_exists( 'hoot_sanitize_background_repeat' ) ):
function hoot_sanitize_background_repeat( $value ) {
	$options = hoot_enum_background_repeat();
	if ( array_key_exists( $value, $options ) ) {
		return $value;
	}
	return '';
}
endif;

/**
 * Sanitization for background position
 *
 * @returns string $value if it is valid
 */
if ( !function_exists( 'hoot_sanitize_background_position' ) ):
function hoot_sanitize_background_position( $value ) {
	$options = hoot_enum_background_position();
	if ( array_key_exists( $value, $options ) ) {
		return $value;
	}
	return '';
}
endif;

/**
 * Sanitization for background attachment
 *
 * @returns string $value if it is valid
 */
if ( !function_exists( 'hoot_sanitize_background_attachment' ) ):
function hoot_sanitize_background_attachment( $value ) {
	$options = hoot_enum_background_attachment();
	if ( array_key_exists( $value, $options ) ) {
		return $value;
	}
	return '';
}
endif;

/**
 * Sanitization for background size
 *
 * @returns string $value if it is valid
 */
if ( !function_exists( 'hoot_sanitize_background_size' ) ):
function hoot_sanitize_background_size( $value ) {
	$options = hoot_enum_background_size();
	if ( array_key_exists( $value, $options ) ) {
		return $value;
	}
	return '';
}
endif;

/**
 * Sanitization for background type
 *
 * @returns $value if it is valid
 */
if ( !function_exists( 'hoot_sanitize_background_type' ) ):
function hoot_sanitize_background_type( $value ) {
	$options = hoot_enum_background_type();
	if ( array_key_exists( $value, $options ) ) {
		return $value;
	}
	return '';
}
endif;

/**
 * Sanitization for background pattern
 *
 * @returns $value if it is valid
 */
if ( !function_exists( 'hoot_sanitize_background_pattern' ) ):
function hoot_sanitize_background_pattern( $value ) {
	$options = hoot_enum_background_pattern();
	if ( array_key_exists( $value, $options ) ) {
		return $value;
	}
	return '';
}
endif;

/**
 * Sanitization for icon
 *
 * @returns string $input if it is valid
 */
if ( !function_exists( 'hoot_sanitize_icon' ) ):
function hoot_sanitize_icon( $input ) {
	$options = hoot_enum_icons();
	if ( in_array( $input, $options ) ) {
		return $input;
	}
	return '';
}
endif;

/**
 * Sanitization for font face
 *
 * @returns string $input if it is valid
 */
if ( !function_exists( 'hoot_sanitize_fontface' ) ):
function hoot_sanitize_fontface( $value ) {
	$options = hoot_enum_font_faces();
	$value = stripslashes( $value );
	if ( array_key_exists( $value, $options ) ) { 
		return $value;
	}
	return '';
}
endif;

/**
 * Sanitization for font size
 * @NU
 *
 * @returns string $input if it is valid
 */
if ( !function_exists( 'hoot_sanitize_font_size' ) ):
function hoot_sanitize_font_size( $value ) {
	$options = hoot_enum_font_sizes();
	$value_check = preg_replace('/px/','', $value);
	if ( in_array( (int) $value_check, $options ) ) {
		return $value;
	}
	return '';
}
endif;

/**
 * Sanitization for font style
 *
 * @returns string $input if it is valid
 */
if ( !function_exists( 'hoot_sanitize_font_style' ) ):
function hoot_sanitize_font_style( $value ) {
	$options = hoot_enum_font_styles();
	if ( array_key_exists( $value, $options ) ) {
		return $value;
	}
	return '';
}
endif;

/**
 * Font Awesome changed class names for several icons between v4 and v5
 * 
 * Properly migrate from FontAwesome 4 to FontAwesome 5 classnames
 * and return sanitized icon display class
 *
 * @since 3.0.0
 * @access public
 * @param string $icon
 * @return string
 */
if ( !function_exists( 'hoot_sanitize_fa' ) ):
function hoot_sanitize_fa( $icon ) {
	$icon = apply_filters( 'hoot_migration_icon', trim( $icon ) );
	$migration = array(
		'fa-500px' => 'fa-500px fab',
		'fa-address-book' => 'fa-address-book fas',
		'fa-address-book-o' => 'fa-address-book far',
		'fa-address-card' => 'fa-address-card fas',
		'fa-address-card-o' => 'fa-address-card far',
		'fa-adjust' => 'fa-adjust fas',
		'fa-adn' => 'fa-adn fab',
		'fa-align-center' => 'fa-align-center fas',
		'fa-align-justify' => 'fa-align-justify fas',
		'fa-align-left' => 'fa-align-left fas',
		'fa-align-right' => 'fa-align-right fas',
		'fa-amazon' => 'fa-amazon fab',
		'fa-ambulance' => 'fa-ambulance fas',
		'fa-american-sign-language-interpreting' => 'fa-american-sign-language-interpreting fas',
		'fa-anchor' => 'fa-anchor fas',
		'fa-android' => 'fa-android fab',
		'fa-angellist' => 'fa-angellist fab',
		'fa-angle-double-down' => 'fa-angle-double-down fas',
		'fa-angle-double-left' => 'fa-angle-double-left fas',
		'fa-angle-double-right' => 'fa-angle-double-right fas',
		'fa-angle-double-up' => 'fa-angle-double-up fas',
		'fa-angle-down' => 'fa-angle-down fas',
		'fa-angle-left' => 'fa-angle-left fas',
		'fa-angle-right' => 'fa-angle-right fas',
		'fa-angle-up' => 'fa-angle-up fas',
		'fa-apple' => 'fa-apple fab',
		'fa-archive' => 'fa-archive fas',
		'fa-area-chart' => 'fa-chart-area fas',
		'fa-arrow-circle-down' => 'fa-arrow-circle-down fas',
		'fa-arrow-circle-left' => 'fa-arrow-circle-left fas',
		'fa-arrow-circle-o-down' => 'fa-arrow-alt-circle-down far',
		'fa-arrow-circle-o-left' => 'fa-arrow-alt-circle-left far',
		'fa-arrow-circle-o-right' => 'fa-arrow-alt-circle-right far',
		'fa-arrow-circle-o-up' => 'fa-arrow-alt-circle-up far',
		'fa-arrow-circle-right' => 'fa-arrow-circle-right fas',
		'fa-arrow-circle-up' => 'fa-arrow-circle-up fas',
		'fa-arrow-down' => 'fa-arrow-down fas',
		'fa-arrow-left' => 'fa-arrow-left fas',
		'fa-arrow-right' => 'fa-arrow-right fas',
		'fa-arrows' => 'fa-arrows-alt fas',
		'fa-arrows-alt' => 'fa-expand-arrows-alt fas',
		'fa-arrows-h' => 'fa-arrows-alt-h fas',
		'fa-arrows-v' => 'fa-arrows-alt-v fas',
		'fa-arrow-up' => 'fa-arrow-up fas',
		'fa-assistive-listening-systems' => 'fa-assistive-listening-systems fas',
		'fa-asterisk' => 'fa-asterisk fas',
		'fa-at' => 'fa-at fas',
		'fa-audio-description' => 'fa-audio-description fas',
		'fa-backward' => 'fa-backward fas',
		'fa-balance-scale' => 'fa-balance-scale fas',
		'fa-ban' => 'fa-ban fas',
		'fa-bandcamp' => 'fa-bandcamp fab',
		'fa-bar-chart' => 'fa-chart-bar far',
		'fa-barcode' => 'fa-barcode fas',
		'fa-bars' => 'fa-bars fas',
		'fa-bath' => 'fa-bath fas',
		'fa-battery-empty' => 'fa-battery-empty fas',
		'fa-battery-full' => 'fa-battery-full fas',
		'fa-battery-half' => 'fa-battery-half fas',
		'fa-battery-quarter' => 'fa-battery-quarter fas',
		'fa-battery-three-quarters' => 'fa-battery-three-quarters fas',
		'fa-bed' => 'fa-bed fas',
		'fa-beer' => 'fa-beer fas',
		'fa-behance' => 'fa-behance fab',
		'fa-behance-square' => 'fa-behance-square fab',
		'fa-bell' => 'fa-bell fas',
		'fa-bell-o' => 'fa-bell far',
		'fa-bell-slash' => 'fa-bell-slash fas',
		'fa-bell-slash-o' => 'fa-bell-slash far',
		'fa-bicycle' => 'fa-bicycle fas',
		'fa-binoculars' => 'fa-binoculars fas',
		'fa-birthday-cake' => 'fa-birthday-cake fas',
		'fa-bitbucket' => 'fa-bitbucket fab',
		'fa-bitbucket-square' => 'fa-bitbucket fab',
		'fa-black-tie' => 'fa-black-tie fab',
		'fa-blind' => 'fa-blind fas',
		'fa-bluetooth' => 'fa-bluetooth fab',
		'fa-bluetooth-b' => 'fa-bluetooth-b fab',
		'fa-bold' => 'fa-bold fas',
		'fa-bolt' => 'fa-bolt fas',
		'fa-bomb' => 'fa-bomb fas',
		'fa-book' => 'fa-book fas',
		'fa-bookmark' => 'fa-bookmark fas',
		'fa-bookmark-o' => 'fa-bookmark far',
		'fa-braille' => 'fa-braille fas',
		'fa-briefcase' => 'fa-briefcase fas',
		'fa-btc' => 'fa-btc fab',
		'fa-bug' => 'fa-bug fas',
		'fa-building' => 'fa-building fas',
		'fa-building-o' => 'fa-building far',
		'fa-bullhorn' => 'fa-bullhorn fas',
		'fa-bullseye' => 'fa-bullseye fas',
		'fa-bus' => 'fa-bus fas',
		'fa-buysellads' => 'fa-buysellads fab',
		'fa-calculator' => 'fa-calculator fas',
		'fa-calendar' => 'fa-calendar-alt fas',
		'fa-calendar-check-o' => 'fa-calendar-check far',
		'fa-calendar-minus-o' => 'fa-calendar-minus far',
		'fa-calendar-o' => 'fa-calendar far',
		'fa-calendar-plus-o' => 'fa-calendar-plus far',
		'fa-calendar-times-o' => 'fa-calendar-times far',
		'fa-camera' => 'fa-camera fas',
		'fa-camera-retro' => 'fa-camera-retro fas',
		'fa-car' => 'fa-car fas',
		'fa-caret-down' => 'fa-caret-down fas',
		'fa-caret-left' => 'fa-caret-left fas',
		'fa-caret-right' => 'fa-caret-right fas',
		'fa-caret-square-o-down' => 'fa-caret-square-down far',
		'fa-caret-square-o-left' => 'fa-caret-square-left far',
		'fa-caret-square-o-right' => 'fa-caret-square-right far',
		'fa-caret-square-o-up' => 'fa-caret-square-up far',
		'fa-caret-up' => 'fa-caret-up fas',
		'fa-cart-arrow-down' => 'fa-cart-arrow-down fas',
		'fa-cart-plus' => 'fa-cart-plus fas',
		'fa-cc' => 'fa-closed-captioning far',
		'fa-cc-amex' => 'fa-cc-amex fab',
		'fa-cc-diners-club' => 'fa-cc-diners-club fab',
		'fa-cc-discover' => 'fa-cc-discover fab',
		'fa-cc-jcb' => 'fa-cc-jcb fab',
		'fa-cc-mastercard' => 'fa-cc-mastercard fab',
		'fa-cc-paypal' => 'fa-cc-paypal fab',
		'fa-cc-stripe' => 'fa-cc-stripe fab',
		'fa-cc-visa' => 'fa-cc-visa fab',
		'fa-certificate' => 'fa-certificate fas',
		'fa-chain-broken' => 'fa-unlink fas',
		'fa-check' => 'fa-check fas',
		'fa-check-circle' => 'fa-check-circle fas',
		'fa-check-circle-o' => 'fa-check-circle far',
		'fa-check-square' => 'fa-check-square fas',
		'fa-check-square-o' => 'fa-check-square far',
		'fa-chevron-circle-down' => 'fa-chevron-circle-down fas',
		'fa-chevron-circle-left' => 'fa-chevron-circle-left fas',
		'fa-chevron-circle-right' => 'fa-chevron-circle-right fas',
		'fa-chevron-circle-up' => 'fa-chevron-circle-up fas',
		'fa-chevron-down' => 'fa-chevron-down fas',
		'fa-chevron-left' => 'fa-chevron-left fas',
		'fa-chevron-right' => 'fa-chevron-right fas',
		'fa-chevron-up' => 'fa-chevron-up fas',
		'fa-child' => 'fa-child fas',
		'fa-chrome' => 'fa-chrome fab',
		'fa-circle' => 'fa-circle fas',
		'fa-circle-o' => 'fa-circle far',
		'fa-circle-o-notch' => 'fa-circle-notch fas',
		'fa-circle-thin' => 'fa-circle far',
		'fa-clipboard' => 'fa-clipboard fas',
		'fa-clock-o' => 'fa-clock far',
		'fa-clone' => 'fa-clone fas',
		'fa-cloud' => 'fa-cloud fas',
		'fa-cloud-download' => 'fa-cloud-download-alt fas',
		'fa-cloud-upload' => 'fa-cloud-upload-alt fas',
		'fa-code' => 'fa-code fas',
		'fa-code-fork' => 'fa-code-branch fas',
		'fa-codepen' => 'fa-codepen fab',
		'fa-codiepie' => 'fa-codiepie fab',
		'fa-coffee' => 'fa-coffee fas',
		'fa-cog' => 'fa-cog fas',
		'fa-cogs' => 'fa-cogs fas',
		'fa-columns' => 'fa-columns fas',
		'fa-comment' => 'fa-comment fas',
		'fa-commenting' => 'fa-comment-alt fas',
		'fa-commenting-o' => 'fa-comment-alt far',
		'fa-comment-o' => 'fa-comment far',
		'fa-comments' => 'fa-comments fas',
		'fa-comments-o' => 'fa-comments far',
		'fa-compass' => 'fa-compass fas',
		'fa-compress' => 'fa-compress fas',
		'fa-connectdevelop' => 'fa-connectdevelop fab',
		'fa-contao' => 'fa-contao fab',
		'fa-copyright' => 'fa-copyright fas',
		'fa-creative-commons' => 'fa-creative-commons fab',
		'fa-credit-card' => 'fa-credit-card fas',
		'fa-credit-card-alt' => 'fa-credit-card fas',
		'fa-crop' => 'fa-crop fas',
		'fa-crosshairs' => 'fa-crosshairs fas',
		'fa-css3' => 'fa-css3 fab',
		'fa-cube' => 'fa-cube fas',
		'fa-cubes' => 'fa-cubes fas',
		'fa-cutlery' => 'fa-utensils fas',
		'fa-dashcube' => 'fa-dashcube fab',
		'fa-database' => 'fa-database fas',
		'fa-deaf' => 'fa-deaf fas',
		'fa-delicious' => 'fa-delicious fab',
		'fa-desktop' => 'fa-desktop fas',
		'fa-deviantart' => 'fa-deviantart fab',
		'fa-diamond' => 'fa-gem far',
		'fa-digg' => 'fa-digg fab',
		'fa-dot-circle-o' => 'fa-dot-circle far',
		'fa-download' => 'fa-download fas',
		'fa-dribbble' => 'fa-dribbble fab',
		'fa-dropbox' => 'fa-dropbox fab',
		'fa-drupal' => 'fa-drupal fab',
		'fa-edge' => 'fa-edge fab',
		'fa-eercast' => 'fa-sellcast fab',
		'fa-eject' => 'fa-eject fas',
		'fa-ellipsis-h' => 'fa-ellipsis-h fas',
		'fa-ellipsis-v' => 'fa-ellipsis-v fas',
		'fa-empire' => 'fa-empire fab',
		'fa-envelope' => 'fa-envelope fas',
		'fa-envelope-o' => 'fa-envelope far',
		'fa-envelope-open' => 'fa-envelope-open fas',
		'fa-envelope-open-o' => 'fa-envelope-open far',
		'fa-envelope-square' => 'fa-envelope-square fas',
		'fa-envira' => 'fa-envira fab',
		'fa-eraser' => 'fa-eraser fas',
		'fa-etsy' => 'fa-etsy fab',
		'fa-eur' => 'fa-euro-sign fas',
		'fa-exchange' => 'fa-exchange-alt fas',
		'fa-exclamation' => 'fa-exclamation fas',
		'fa-exclamation-circle' => 'fa-exclamation-circle fas',
		'fa-exclamation-triangle' => 'fa-exclamation-triangle fas',
		'fa-expand' => 'fa-expand fas',
		'fa-expeditedssl' => 'fa-expeditedssl fab',
		'fa-external-link' => 'fa-external-link-alt fas',
		'fa-external-link-square' => 'fa-external-link-square-alt fas',
		'fa-eye' => 'fa-eye fas',
		'fa-eyedropper' => 'fa-eye-dropper fas',
		'fa-eye-slash' => 'fa-eye-slash fas',
		'fa-facebook' => 'fa-facebook-f fab',
		'fa-facebook-official' => 'fa-facebook fab',
		'fa-facebook-square' => 'fa-facebook-square fab',
		'fa-fast-backward' => 'fa-fast-backward fas',
		'fa-fast-forward' => 'fa-fast-forward fas',
		'fa-fax' => 'fa-fax fas',
		'fa-female' => 'fa-female fas',
		'fa-fighter-jet' => 'fa-fighter-jet fas',
		'fa-file' => 'fa-file fas',
		'fa-file-archive-o' => 'fa-file-archive far',
		'fa-file-audio-o' => 'fa-file-audio far',
		'fa-file-code-o' => 'fa-file-code far',
		'fa-file-excel-o' => 'fa-file-excel far',
		'fa-file-image-o' => 'fa-file-image far',
		'fa-file-o' => 'fa-file far',
		'fa-file-pdf-o' => 'fa-file-pdf far',
		'fa-file-powerpoint-o' => 'fa-file-powerpoint far',
		'fa-files-o' => 'fa-copy far',
		'fa-file-text' => 'fa-file-alt fas',
		'fa-file-text-o' => 'fa-file-alt far',
		'fa-file-video-o' => 'fa-file-video far',
		'fa-file-word-o' => 'fa-file-word far',
		'fa-film' => 'fa-film fas',
		'fa-filter' => 'fa-filter fas',
		'fa-fire' => 'fa-fire fas',
		'fa-fire-extinguisher' => 'fa-fire-extinguisher fas',
		'fa-firefox' => 'fa-firefox fab',
		'fa-first-order' => 'fa-first-order fab',
		'fa-flag' => 'fa-flag fas',
		'fa-flag-checkered' => 'fa-flag-checkered fas',
		'fa-flag-o' => 'fa-flag far',
		'fa-flask' => 'fa-flask fas',
		'fa-flickr' => 'fa-flickr fab',
		'fa-floppy-o' => 'fa-save far',
		'fa-folder' => 'fa-folder fas',
		'fa-folder-o' => 'fa-folder far',
		'fa-folder-open' => 'fa-folder-open fas',
		'fa-folder-open-o' => 'fa-folder-open far',
		'fa-font' => 'fa-font fas',
		'fa-font-awesome' => 'fa-font-awesome fab',
		'fa-fonticons' => 'fa-fonticons fab',
		'fa-fort-awesome' => 'fa-fort-awesome fab',
		'fa-forumbee' => 'fa-forumbee fab',
		'fa-forward' => 'fa-forward fas',
		'fa-foursquare' => 'fa-foursquare fab',
		'fa-free-code-camp' => 'fa-free-code-camp fab',
		'fa-frown-o' => 'fa-frown far',
		'fa-futbol-o' => 'fa-futbol far',
		'fa-gamepad' => 'fa-gamepad fas',
		'fa-gavel' => 'fa-gavel fas',
		'fa-gbp' => 'fa-pound-sign fas',
		'fa-genderless' => 'fa-genderless fas',
		'fa-get-pocket' => 'fa-get-pocket fab',
		'fa-gg' => 'fa-gg fab',
		'fa-gg-circle' => 'fa-gg-circle fab',
		'fa-gift' => 'fa-gift fas',
		'fa-git' => 'fa-git fab',
		'fa-github' => 'fa-github fab',
		'fa-github-alt' => 'fa-github-alt fab',
		'fa-github-square' => 'fa-github-square fab',
		'fa-gitlab' => 'fa-gitlab fab',
		'fa-git-square' => 'fa-git-square fab',
		'fa-glass' => 'fa-glass-martini fas',
		'fa-glide' => 'fa-glide fab',
		'fa-glide-g' => 'fa-glide-g fab',
		'fa-globe' => 'fa-globe fas',
		'fa-google' => 'fa-google fab',
		'fa-google-plus' => 'fa-google-plus-g fab',
		'fa-google-plus-official' => 'fa-google-plus fab',
		'fa-google-plus-square' => 'fa-google-plus-square fab',
		'fa-google-wallet' => 'fa-google-wallet fab',
		'fa-graduation-cap' => 'fa-graduation-cap fas',
		'fa-gratipay' => 'fa-gratipay fab',
		'fa-grav' => 'fa-grav fab',
		'fa-hacker-news' => 'fa-hacker-news fab',
		'fa-hand-lizard-o' => 'fa-hand-lizard far',
		'fa-hand-o-down' => 'fa-hand-point-down far',
		'fa-hand-o-left' => 'fa-hand-point-left far',
		'fa-hand-o-right' => 'fa-hand-point-right far',
		'fa-hand-o-up' => 'fa-hand-point-up far',
		'fa-hand-paper-o' => 'fa-hand-paper far',
		'fa-hand-peace-o' => 'fa-hand-peace far',
		'fa-hand-pointer-o' => 'fa-hand-pointer far',
		'fa-hand-rock-o' => 'fa-hand-rock far',
		'fa-hand-scissors-o' => 'fa-hand-scissors far',
		'fa-handshake-o' => 'fa-handshake far',
		'fa-hand-spock-o' => 'fa-hand-spock far',
		'fa-hashtag' => 'fa-hashtag fas',
		'fa-hdd-o' => 'fa-hdd far',
		'fa-header' => 'fa-heading fas',
		'fa-headphones' => 'fa-headphones fas',
		'fa-heart' => 'fa-heart fas',
		'fa-heartbeat' => 'fa-heartbeat fas',
		'fa-heart-o' => 'fa-heart far',
		'fa-history' => 'fa-history fas',
		'fa-home' => 'fa-home fas',
		'fa-hospital-o' => 'fa-hospital far',
		'fa-hourglass' => 'fa-hourglass fas',
		'fa-hourglass-end' => 'fa-hourglass-end fas',
		'fa-hourglass-half' => 'fa-hourglass-half fas',
		'fa-hourglass-o' => 'fa-hourglass far',
		'fa-hourglass-start' => 'fa-hourglass-start fas',
		'fa-houzz' => 'fa-houzz fab',
		'fa-h-square' => 'fa-h-square fas',
		'fa-html5' => 'fa-html5 fab',
		'fa-i-cursor' => 'fa-i-cursor fas',
		'fa-id-badge' => 'fa-id-badge fas',
		'fa-id-card' => 'fa-id-card fas',
		'fa-id-card-o' => 'fa-id-card far',
		'fa-ils' => 'fa-shekel-sign fas',
		'fa-imdb' => 'fa-imdb fab',
		'fa-inbox' => 'fa-inbox fas',
		'fa-indent' => 'fa-indent fas',
		'fa-industry' => 'fa-industry fas',
		'fa-info' => 'fa-info fas',
		'fa-info-circle' => 'fa-info-circle fas',
		'fa-inr' => 'fa-rupee-sign fas',
		'fa-instagram' => 'fa-instagram fab',
		'fa-internet-explorer' => 'fa-internet-explorer fab',
		'fa-ioxhost' => 'fa-ioxhost fab',
		'fa-italic' => 'fa-italic fas',
		'fa-joomla' => 'fa-joomla fab',
		'fa-jpy' => 'fa-yen-sign fas',
		'fa-jsfiddle' => 'fa-jsfiddle fab',
		'fa-key' => 'fa-key fas',
		'fa-keyboard-o' => 'fa-keyboard far',
		'fa-krw' => 'fa-won-sign fas',
		'fa-language' => 'fa-language fas',
		'fa-laptop' => 'fa-laptop fas',
		'fa-lastfm' => 'fa-lastfm fab',
		'fa-lastfm-square' => 'fa-lastfm-square fab',
		'fa-leaf' => 'fa-leaf fas',
		'fa-leanpub' => 'fa-leanpub fab',
		'fa-lemon-o' => 'fa-lemon far',
		'fa-level-down' => 'fa-level-down-alt fas',
		'fa-level-up' => 'fa-level-up-alt fas',
		'fa-life-ring' => 'fa-life-ring fas',
		'fa-lightbulb-o' => 'fa-lightbulb far',
		'fa-line-chart' => 'fa-chart-line fas',
		'fa-link' => 'fa-link fas',
		'fa-linkedin' => 'fa-linkedin-in fab',
		'fa-linkedin-square' => 'fa-linkedin fab',
		'fa-linode' => 'fa-linode fab',
		'fa-linux' => 'fa-linux fab',
		'fa-list' => 'fa-list fas',
		'fa-list-alt' => 'fa-list-alt fas',
		'fa-list-ol' => 'fa-list-ol fas',
		'fa-list-ul' => 'fa-list-ul fas',
		'fa-location-arrow' => 'fa-location-arrow fas',
		'fa-lock' => 'fa-lock fas',
		'fa-long-arrow-down' => 'fa-long-arrow-alt-down fas',
		'fa-long-arrow-left' => 'fa-long-arrow-alt-left fas',
		'fa-long-arrow-right' => 'fa-long-arrow-alt-right fas',
		'fa-long-arrow-up' => 'fa-long-arrow-alt-up fas',
		'fa-low-vision' => 'fa-low-vision fas',
		'fa-magic' => 'fa-magic fas',
		'fa-magnet' => 'fa-magnet fas',
		'fa-male' => 'fa-male fas',
		'fa-map' => 'fa-map fas',
		'fa-map-marker' => 'fa-map-marker-alt fas',
		'fa-map-o' => 'fa-map far',
		'fa-map-pin' => 'fa-map-pin fas',
		'fa-map-signs' => 'fa-map-signs fas',
		'fa-mars' => 'fa-mars fas',
		'fa-mars-double' => 'fa-mars-double fas',
		'fa-mars-stroke' => 'fa-mars-stroke fas',
		'fa-mars-stroke-h' => 'fa-mars-stroke-h fas',
		'fa-mars-stroke-v' => 'fa-mars-stroke-v fas',
		'fa-maxcdn' => 'fa-maxcdn fab',
		'fa-meanpath' => 'fa-font-awesome fab',
		'fa-medium' => 'fa-medium fab',
		'fa-medkit' => 'fa-medkit fas',
		'fa-meetup' => 'fa-meetup fab',
		'fa-meh-o' => 'fa-meh far',
		'fa-mercury' => 'fa-mercury fas',
		'fa-microchip' => 'fa-microchip fas',
		'fa-microphone' => 'fa-microphone fas',
		'fa-microphone-slash' => 'fa-microphone-slash fas',
		'fa-minus' => 'fa-minus fas',
		'fa-minus-circle' => 'fa-minus-circle fas',
		'fa-minus-square' => 'fa-minus-square fas',
		'fa-minus-square-o' => 'fa-minus-square far',
		'fa-mixcloud' => 'fa-mixcloud fab',
		'fa-mobile' => 'fa-mobile-alt fas',
		'fa-modx' => 'fa-modx fab',
		'fa-money' => 'fa-money-bill-alt far',
		'fa-moon-o' => 'fa-moon far',
		'fa-motorcycle' => 'fa-motorcycle fas',
		'fa-mouse-pointer' => 'fa-mouse-pointer fas',
		'fa-music' => 'fa-music fas',
		'fa-neuter' => 'fa-neuter fas',
		'fa-newspaper-o' => 'fa-newspaper far',
		'fa-object-group' => 'fa-object-group fas',
		'fa-object-ungroup' => 'fa-object-ungroup fas',
		'fa-odnoklassniki' => 'fa-odnoklassniki fab',
		'fa-odnoklassniki-square' => 'fa-odnoklassniki-square fab',
		'fa-opencart' => 'fa-opencart fab',
		'fa-openid' => 'fa-openid fab',
		'fa-opera' => 'fa-opera fab',
		'fa-optin-monster' => 'fa-optin-monster fab',
		'fa-outdent' => 'fa-outdent fas',
		'fa-pagelines' => 'fa-pagelines fab',
		'fa-paint-brush' => 'fa-paint-brush fas',
		'fa-paperclip' => 'fa-paperclip fas',
		'fa-paper-plane' => 'fa-paper-plane fas',
		'fa-paper-plane-o' => 'fa-paper-plane far',
		'fa-paragraph' => 'fa-paragraph fas',
		'fa-patreon' => 'fa-patreon fab',
		'fa-pause' => 'fa-pause fas',
		'fa-pause-circle' => 'fa-pause-circle fas',
		'fa-pause-circle-o' => 'fa-pause-circle far',
		'fa-paw' => 'fa-paw fas',
		'fa-paypal' => 'fa-paypal fab',
		'fa-pencil' => 'fa-pencil-alt fas',
		'fa-pencil-square' => 'fa-pen-square fas',
		'fa-pencil-square-o' => 'fa-edit far',
		'fa-percent' => 'fa-percent fas',
		'fa-phone' => 'fa-phone fas',
		'fa-phone-square' => 'fa-phone-square fas',
		'fa-picture-o' => 'fa-image far',
		'fa-pie-chart' => 'fa-chart-pie fas',
		'fa-pied-piper' => 'fa-pied-piper fab',
		'fa-pied-piper-alt' => 'fa-pied-piper-alt fab',
		'fa-pied-piper-pp' => 'fa-pied-piper-pp fab',
		'fa-pinterest' => 'fa-pinterest fab',
		'fa-pinterest-p' => 'fa-pinterest-p fab',
		'fa-pinterest-square' => 'fa-pinterest-square fab',
		'fa-plane' => 'fa-plane fas',
		'fa-play' => 'fa-play fas',
		'fa-play-circle' => 'fa-play-circle fas',
		'fa-play-circle-o' => 'fa-play-circle far',
		'fa-plug' => 'fa-plug fas',
		'fa-plus' => 'fa-plus fas',
		'fa-plus-circle' => 'fa-plus-circle fas',
		'fa-plus-square' => 'fa-plus-square fas',
		'fa-plus-square-o' => 'fa-plus-square far',
		'fa-podcast' => 'fa-podcast fas',
		'fa-power-off' => 'fa-power-off fas',
		'fa-print' => 'fa-print fas',
		'fa-product-hunt' => 'fa-product-hunt fab',
		'fa-puzzle-piece' => 'fa-puzzle-piece fas',
		'fa-qq' => 'fa-qq fab',
		'fa-qrcode' => 'fa-qrcode fas',
		'fa-question' => 'fa-question fas',
		'fa-question-circle' => 'fa-question-circle fas',
		'fa-question-circle-o' => 'fa-question-circle far',
		'fa-quora' => 'fa-quora fab',
		'fa-quote-left' => 'fa-quote-left fas',
		'fa-quote-right' => 'fa-quote-right fas',
		'fa-random' => 'fa-random fas',
		'fa-ravelry' => 'fa-ravelry fab',
		'fa-rebel' => 'fa-rebel fab',
		'fa-recycle' => 'fa-recycle fas',
		'fa-reddit' => 'fa-reddit fab',
		'fa-reddit-alien' => 'fa-reddit-alien fab',
		'fa-reddit-square' => 'fa-reddit-square fab',
		'fa-refresh' => 'fa-sync fas',
		'fa-registered' => 'fa-registered fas',
		'fa-renren' => 'fa-renren fab',
		'fa-repeat' => 'fa-redo fas',
		'fa-reply' => 'fa-reply fas',
		'fa-reply-all' => 'fa-reply-all fas',
		'fa-retweet' => 'fa-retweet fas',
		'fa-road' => 'fa-road fas',
		'fa-rocket' => 'fa-rocket fas',
		'fa-rss' => 'fa-rss fas',
		'fa-rss-square' => 'fa-rss-square fas',
		'fa-rub' => 'fa-ruble-sign fas',
		'fa-safari' => 'fa-safari fab',
		'fa-scissors' => 'fa-cut fas',
		'fa-scribd' => 'fa-scribd fab',
		'fa-search' => 'fa-search fas',
		'fa-search-minus' => 'fa-search-minus fas',
		'fa-search-plus' => 'fa-search-plus fas',
		'fa-sellsy' => 'fa-sellsy fab',
		'fa-server' => 'fa-server fas',
		'fa-share' => 'fa-share fas',
		'fa-share-alt' => 'fa-share-alt fas',
		'fa-share-alt-square' => 'fa-share-alt-square fas',
		'fa-share-square' => 'fa-share-square fas',
		'fa-share-square-o' => 'fa-share-square far',
		'fa-shield' => 'fa-shield-alt fas',
		'fa-ship' => 'fa-ship fas',
		'fa-shirtsinbulk' => 'fa-shirtsinbulk fab',
		'fa-shopping-bag' => 'fa-shopping-bag fas',
		'fa-shopping-basket' => 'fa-shopping-basket fas',
		'fa-shopping-cart' => 'fa-shopping-cart fas',
		'fa-shower' => 'fa-shower fas',
		'fa-signal' => 'fa-signal fas',
		'fa-sign-in' => 'fa-sign-in-alt fas',
		'fa-sign-language' => 'fa-sign-language fas',
		'fa-sign-out' => 'fa-sign-out-alt fas',
		'fa-simplybuilt' => 'fa-simplybuilt fab',
		'fa-sitemap' => 'fa-sitemap fas',
		'fa-skyatlas' => 'fa-skyatlas fab',
		'fa-skype' => 'fa-skype fab',
		'fa-slack' => 'fa-slack fab',
		'fa-sliders' => 'fa-sliders-h fas',
		'fa-slideshare' => 'fa-slideshare fab',
		'fa-smile-o' => 'fa-smile far',
		'fa-snapchat' => 'fa-snapchat fab',
		'fa-snapchat-ghost' => 'fa-snapchat-ghost fab',
		'fa-snapchat-square' => 'fa-snapchat-square fab',
		'fa-snowflake-o' => 'fa-snowflake far',
		'fa-sort' => 'fa-sort fas',
		'fa-sort-alpha-asc' => 'fa-sort-alpha-down fas',
		'fa-sort-alpha-desc' => 'fa-sort-alpha-up fas',
		'fa-sort-amount-asc' => 'fa-sort-amount-down fas',
		'fa-sort-amount-desc' => 'fa-sort-amount-up fas',
		'fa-sort-asc' => 'fa-sort-up fas',
		'fa-sort-desc' => 'fa-sort-down fas',
		'fa-sort-numeric-asc' => 'fa-sort-numeric-down fas',
		'fa-sort-numeric-desc' => 'fa-sort-numeric-up fas',
		'fa-soundcloud' => 'fa-soundcloud fab',
		'fa-space-shuttle' => 'fa-space-shuttle fas',
		'fa-spinner' => 'fa-spinner fas',
		'fa-spoon' => 'fa-utensil-spoon fas',
		'fa-spotify' => 'fa-spotify fab',
		'fa-square' => 'fa-square fas',
		'fa-square-o' => 'fa-square far',
		'fa-stack-exchange' => 'fa-stack-exchange fab',
		'fa-stack-overflow' => 'fa-stack-overflow fab',
		'fa-star' => 'fa-star fas',
		'fa-star-half' => 'fa-star-half fas',
		'fa-star-half-o' => 'fa-star-half far',
		'fa-star-o' => 'fa-star far',
		'fa-steam' => 'fa-steam fab',
		'fa-steam-square' => 'fa-steam-square fab',
		'fa-step-backward' => 'fa-step-backward fas',
		'fa-step-forward' => 'fa-step-forward fas',
		'fa-stethoscope' => 'fa-stethoscope fas',
		'fa-sticky-note' => 'fa-sticky-note fas',
		'fa-sticky-note-o' => 'fa-sticky-note far',
		'fa-stop' => 'fa-stop fas',
		'fa-stop-circle' => 'fa-stop-circle fas',
		'fa-stop-circle-o' => 'fa-stop-circle far',
		'fa-street-view' => 'fa-street-view fas',
		'fa-strikethrough' => 'fa-strikethrough fas',
		'fa-stumbleupon' => 'fa-stumbleupon fab',
		'fa-stumbleupon-circle' => 'fa-stumbleupon-circle fab',
		'fa-subscript' => 'fa-subscript fas',
		'fa-subway' => 'fa-subway fas',
		'fa-suitcase' => 'fa-suitcase fas',
		'fa-sun-o' => 'fa-sun far',
		'fa-superpowers' => 'fa-superpowers fab',
		'fa-superscript' => 'fa-superscript fas',
		'fa-table' => 'fa-table fas',
		'fa-tablet' => 'fa-tablet-alt fas',
		'fa-tachometer' => 'fa-tachometer-alt fas',
		'fa-tag' => 'fa-tag fas',
		'fa-tags' => 'fa-tags fas',
		'fa-tasks' => 'fa-tasks fas',
		'fa-taxi' => 'fa-taxi fas',
		'fa-telegram' => 'fa-telegram fab',
		'fa-telegram-plane' => 'fa-telegram-plane fab',
		'fa-television' => 'fa-tv fas',
		'fa-tencent-weibo' => 'fa-tencent-weibo fab',
		'fa-terminal' => 'fa-terminal fas',
		'fa-text-height' => 'fa-text-height fas',
		'fa-text-width' => 'fa-text-width fas',
		'fa-th' => 'fa-th fas',
		'fa-themeisle' => 'fa-themeisle fab',
		'fa-thermometer-empty' => 'fa-thermometer-empty fas',
		'fa-thermometer-full' => 'fa-thermometer-full fas',
		'fa-thermometer-half' => 'fa-thermometer-half fas',
		'fa-thermometer-quarter' => 'fa-thermometer-quarter fas',
		'fa-thermometer-three-quarters' => 'fa-thermometer-three-quarters fas',
		'fa-th-large' => 'fa-th-large fas',
		'fa-th-list' => 'fa-th-list fas',
		'fa-thumbs-down' => 'fa-thumbs-down fas',
		'fa-thumbs-o-down' => 'fa-thumbs-down far',
		'fa-thumbs-o-up' => 'fa-thumbs-up far',
		'fa-thumbs-up' => 'fa-thumbs-up fas',
		'fa-thumb-tack' => 'fa-thumbtack fas',
		'fa-ticket' => 'fa-ticket-alt fas',
		'fa-times' => 'fa-times fas',
		'fa-times-circle' => 'fa-times-circle fas',
		'fa-times-circle-o' => 'fa-times-circle far',
		'fa-tint' => 'fa-tint fas',
		'fa-toggle-off' => 'fa-toggle-off fas',
		'fa-toggle-on' => 'fa-toggle-on fas',
		'fa-trademark' => 'fa-trademark fas',
		'fa-train' => 'fa-train fas',
		'fa-transgender' => 'fa-transgender fas',
		'fa-transgender-alt' => 'fa-transgender-alt fas',
		'fa-trash' => 'fa-trash-alt fas',
		'fa-trash-o' => 'fa-trash-alt far',
		'fa-tree' => 'fa-tree fas',
		'fa-trello' => 'fa-trello fab',
		'fa-tripadvisor' => 'fa-tripadvisor fab',
		'fa-trophy' => 'fa-trophy fas',
		'fa-truck' => 'fa-truck fas',
		'fa-try' => 'fa-lira-sign fas',
		'fa-tty' => 'fa-tty fas',
		'fa-tumblr' => 'fa-tumblr fab',
		'fa-tumblr-square' => 'fa-tumblr-square fab',
		'fa-twitch' => 'fa-twitch fab',
		'fa-twitter' => 'fa-twitter fab',
		'fa-twitter-square' => 'fa-twitter-square fab',
		'fa-umbrella' => 'fa-umbrella fas',
		'fa-underline' => 'fa-underline fas',
		'fa-undo' => 'fa-undo fas',
		'fa-universal-access' => 'fa-universal-access fas',
		'fa-university' => 'fa-university fas',
		'fa-unlock' => 'fa-unlock fas',
		'fa-unlock-alt' => 'fa-unlock-alt fas',
		'fa-upload' => 'fa-upload fas',
		'fa-usb' => 'fa-usb fab',
		'fa-usd' => 'fa-dollar-sign fas',
		'fa-user' => 'fa-user fas',
		'fa-user-circle' => 'fa-user-circle fas',
		'fa-user-circle-o' => 'fa-user-circle far',
		'fa-user-md' => 'fa-user-md fas',
		'fa-user-o' => 'fa-user far',
		'fa-user-plus' => 'fa-user-plus fas',
		'fa-users' => 'fa-users fas',
		'fa-user-secret' => 'fa-user-secret fas',
		'fa-user-times' => 'fa-user-times fas',
		'fa-venus' => 'fa-venus fas',
		'fa-venus-double' => 'fa-venus-double fas',
		'fa-venus-mars' => 'fa-venus-mars fas',
		'fa-viacoin' => 'fa-viacoin fab',
		'fa-viadeo' => 'fa-viadeo fab',
		'fa-viadeo-square' => 'fa-viadeo-square fab',
		'fa-video-camera' => 'fa-video fas',
		'fa-vimeo' => 'fa-vimeo-v fab',
		'fa-vimeo-square' => 'fa-vimeo-square fab',
		'fa-vine' => 'fa-vine fab',
		'fa-vk' => 'fa-vk fab',
		'fa-volume-control-phone' => 'fa-phone-volume fas',
		'fa-volume-down' => 'fa-volume-down fas',
		'fa-volume-off' => 'fa-volume-off fas',
		'fa-volume-up' => 'fa-volume-up fas',
		'fa-weibo' => 'fa-weibo fab',
		'fa-weixin' => 'fa-weixin fab',
		'fa-whatsapp' => 'fa-whatsapp fab',
		'fa-wheelchair' => 'fa-wheelchair fas',
		'fa-wheelchair-alt' => 'fa-accessible-icon fab',
		'fa-wifi' => 'fa-wifi fas',
		'fa-wikipedia-w' => 'fa-wikipedia-w fab',
		'fa-window-close' => 'fa-window-close fas',
		'fa-window-close-o' => 'fa-window-close far',
		'fa-window-maximize' => 'fa-window-maximize fas',
		'fa-window-minimize' => 'fa-window-minimize fas',
		'fa-window-restore' => 'fa-window-restore fas',
		'fa-windows' => 'fa-windows fab',
		'fa-wordpress' => 'fa-wordpress fab',
		'fa-wpbeginner' => 'fa-wpbeginner fab',
		'fa-wpexplorer' => 'fa-wpexplorer fab',
		'fa-wpforms' => 'fa-wpforms fab',
		'fa-wrench' => 'fa-wrench fas',
		'fa-xing' => 'fa-xing fab',
		'fa-xing-square' => 'fa-xing-square fab',
		'fa-yahoo' => 'fa-yahoo fab',
		'fa-y-combinator' => 'fa-y-combinator fab',
		'fa-yelp' => 'fa-yelp fab',
		'fa-yoast' => 'fa-yoast fab',
		'fa-youtube' => 'fa-youtube fab',
		'fa-youtube-play' => 'fa-youtube fab',
		'fa-youtube-square' => 'fa-youtube fab',
	);

	// Sanitize and return icon
	if ( empty( $migration[$icon] ) ) {
		return ( is_string( $icon ) ) ? hoot_sanitize_html_classes( $icon ) : '';
	} else {
		return $migration[$icon];
	}
}
endif;