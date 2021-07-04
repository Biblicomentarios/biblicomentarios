<?php
/**
 * Style-Builder Class for sanitizing and building CSS styles to be printed.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Wrapper function to create general CSS style with Style-Builder class
 * 
 * $hoot_style_builder->add will sanitizes the property and adds it using 'wp_add_inline_style()'
 *
 * @since 3.0.0
 * @access public
 * @param array $args Arguments array
 *                    'selector' *required => html class/id/tag
 *                    'property' *required => string: name of the css property
 *                                            array: array of $property_name => $value, $idtag, $important, $typography_reset
 *                    'value'              => value of the css property
 *                                            (redundant if $property is an array)
 *                    'idtag'              => setting id used in wp admin (may be used in live preview js)
 *                                            used for fetching background and typography settings
 *                                            (redundant if $property is an array)
 *                    'media'              => media-query
 *                    'important'          => (redundant if $property is an array)
 *                    'typography_reset'   => for 'typography' options only - whether to re-apply all text properties
 * @return void
 */
function hoot_add_css_rule( $args ) {

	global $hoot_style_builder;

	extract( wp_parse_args( $args, array(
		'selector'         => '',
		'property'         => '',
		'value'            => '',
		'idtag'            => '',
		'media'            => '',
		'important'        => false,
		'typography_reset' => false,
	) ) );

	if ( empty( $selector ) || empty( $property) )
		return;

	if ( !is_array( $property ) ) {

		$hoot_style_builder->add( $selector, $property, $value, $idtag, $media, $important, $typography_reset );

	} else {

		foreach ( $property as $addproperty => $prop_value ) {
			if ( !is_array( $prop_value ) && !empty( $prop_value ) ) {
				$addvalue = $prop_value;
				$addidtag = $addimportant = $addreset = '';
			} else {
				$addvalue     = ( empty( $prop_value[0] ) ) ? '' : $prop_value[0];
				$addidtag     = ( empty( $prop_value[1] ) ) ? '' : $prop_value[1];
				$addimportant = ( empty( $prop_value[2] ) ) ? '' : $prop_value[2];
				$addreset     = ( empty( $prop_value[3] ) ) ? '' : $prop_value[3];
			}
			$hoot_style_builder->add( $selector, $addproperty, $addvalue, $addidtag, $media, $addimportant, $addreset );
		}

	}
}

/**
 * A class of helper functions to create styles
 * 
 * @since 3.0.0
 */
class Hoot_Style_Builder {

	/**
	 * The one instance of Hoot_Customize.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var Hoot_Customize The one instance for the singleton.
	 */
	private static $instance;

	/**
	 * The array for storing css rules.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var array Holds the css rules array.
	 */
	private $cssrules = array();

	/**
	 * The array for storing media specific css rules.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var array Holds the media specific css rules array.
	 */
	private $mediarules = array();

	/**
	 * The string with rendered css rules.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var array Holds the media specific css rules array.
	 */
	private $dynamiccss = '';

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * Singleton from outside of this class.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function __construct() {

		/**
		 * Hook into 'wp_enqueue_scripts' as 'wp_add_inline_style()' requires stylesheet $handle to be
		 * already registered. Main stylesheet with handle 'style' is registered by the framework via
		 * 'wp_enqueue_scripts' hook at priority 0
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'build_css' ), 99 );

	}

	/**
	 * Create Dynamic CSS styles from an array.
	 *
	 * @since 3.0.0
	 * @access public
	 * @return void
	 */
	function build_css(){

		/* Allow hook for child themes to add dynamic css rules */

		do_action( 'hoot_dynamic_cssrules' );

		/* Build Inline CSS */

		$css = '';
		if ( defined( 'HOOT_DEBUG' ) )
			$loadminified = ( HOOT_DEBUG ) ? false : true;
		else
			$loadminified = hoot_get_mod( 'load_minified', 0 );
		if ( !$loadminified ) {
			$n = "\n";
			$t = "\t";
		} else {
			$n = $t = '';
		}
		$n = $t = ' ';

		if ( !empty( $this->cssrules ) ) {
			$css .= $this->build_css_string( $this->cssrules, $n, $t );
		}

		if ( !empty( $this->mediarules ) ) {
			foreach ( $this->mediarules as $mediaquery => $mediarules ) {
				$css .= $n . '@media ' . $mediaquery . '{';
				$css .= $this->build_css_string( $mediarules, $n, $t ) . $n;
				$css .= '}';
			}
		}

		/* Allow child themes to add additional conditional css */

		$this->dynamiccss = wp_strip_all_tags( apply_filters( 'hoot_dynamic_css', $css ) );

		/* Print CSS */

		if ( !empty( $this->dynamiccss ) ) {
			$handle = apply_filters( 'hoot_style_builder_inline_style_handle', 'hoot-style' );
			wp_add_inline_style( sanitize_html_class( $handle ), $this->dynamiccss );
		}

	}

	/**
	 * Create Formatted CSS string
	 *
	 * @since 3.0.0
	 * @access public
	 * @param array $cssrules
	 * @param string $n Line ending
	 * @param string $t Starting Tab
	 * @return string
	 */
	function build_css_string( $cssrules, $n = '', $t = '' ){

		$css = '';

		foreach ( $cssrules as $selector => $rules ) {
			if ( is_array( $rules ) ) {
				$css .= $n . $selector . ' {';
				foreach ( $rules as $property => $value ) {
					if ( !empty( $value['value'] ) ) {
						$important = ( !empty( $value['important'] ) ) ? ' !important' : '';
						$css .= $n . $t . $property . ': ' . $value['value'] . $important . ';';
					}
				}
				$css .= $n . '} ';
			}
		}

		return $css;
	}

	/**
	 * Sanitize and add CSS styles to cssrules array.
	 * The entire property array is replaced for a selector
	 * Example: Changing only color['value'] is not possible. The entire 'color'
	 *          array (value, important, idtag) will replace the old 'color' array if exists.
	 *
	 * @since 3.0.0
	 * @access public
	 * @param string $selector html class/id/tag
	 * @param string $property name of the css property
	 * @param string $value value of the css property
	 * @param string $idtag setting id used in wp admin (may be used in live preview js)
	 *                      used for fetching background and typography settings
	 * @param string $media media-query
	 * @param bool|string $important
	 * @param bool $typography_reset Used for 'typography' property - whether to re-apply all text properties
	 * @return void
	 */
	function add( $selector, $property, $value = '', $idtag = '', $media = '', $important = false, $typography_reset = false ){

		/* Sanitize the CSS property */
		$merge = $this->sanitize_css_rule( $property, $value, $idtag, $important, $typography_reset );

		/* Add rule if there is one */
		if ( !empty( $merge ) && is_array( $merge ) ) {

			if ( !empty( $media ) ) {
				if ( empty( $this->mediarules[$media][$selector] ) )
					$this->mediarules[$media][$selector] = array();
				$this->mediarules[$media][$selector] = array_merge( $this->mediarules[$media][$selector], $merge );
			} else {
				if ( empty( $this->cssrules[$selector] ) )
					$this->cssrules[$selector] = array();
				$this->cssrules[$selector] = array_merge( $this->cssrules[$selector], $merge );
			}

		}
	}

	/**
	 * Get CSS styles from cssrules array.
	 *
	 * @since 3.0.0
	 * @access public
	 * @return array|false
	 */
	function get( $selector = '' ){
		if ( !empty( $selector ) ) {
			if ( isset( $this->cssrules[$selector] ) )
				return $this->cssrules[$selector];
			else
				return false;
		} else
			return $this->cssrules;
	}

	/**
	 * Get CSS styles from mediarules array.
	 *
	 * @since 3.0.0
	 * @access public
	 * @return array|false
	 */
	function get_media( $media = '', $selector = '' ){
		if ( !empty( $media ) ) {
			if ( isset( $this->mediarules[$media] ) ) {
				if ( !empty( $selector ) ) {
					if ( isset( $this->mediarules[$media][$selector] ) )
						return $this->mediarules[$media][$selector];
					else
						return false;
				} else
					return $this->mediarules[$media];
			} else
				return false;
		} else {
			return $this->mediarules;
		}
	}

	/**
	 * Remove CSS styles from cssrules array.
	 *
	 * @since 3.0.0
	 * @access public
	 * @return string
	 */
	function remove( $selectors ){
		if ( is_array( $selectors ) ) {
			foreach ( $selectors as $selector ) {
				if ( isset( $this->cssrules[$selector] ) )
					unset( $this->cssrules[$selector] );
			}
		} elseif ( isset( $this->cssrules[$selectors] ) ) {
			unset( $this->cssrules[$selectors] );
		}
	}

	/**
	 * Remove CSS styles from mediarules array.
	 *
	 * @since 3.0.0
	 * @access public
	 * @return string
	 */
	function remove_media( $media, $selectors ){
		if ( isset( $this->mediarules[$media] ) ) {
			if ( is_array( $selectors ) ) {
				foreach ( $selectors as $selector ) {
					if ( isset( $this->mediarules[$media][$selector] ) )
						unset( $this->mediarules[$media][$selector] );
				}
			} elseif ( isset( $this->mediarules[$media][$selectors] ) ) {
				unset( $this->mediarules[$media][$selectors] );
			}
		}
	}

	/**
	 * Remove CSS styles from cssrules array.
	 *
	 * @since 3.0.0
	 * @access public
	 * @return string
	 */
	function remove_style( $selector, $property ){
		if ( !empty( $this->cssrules[$selector] ) && isset( $this->cssrules[$selector][$property] ) )
			unset( $this->cssrules[$selector][$property] );
	}

	/**
	 * Remove CSS styles from mediarules array.
	 *
	 * @since 3.0.0
	 * @access public
	 * @return string
	 */
	function remove_media_style( $media, $selector, $property ){
		if ( isset( $this->mediarules[$media] ) && !empty( $this->mediarules[$media][$selector] ) && isset( $this->mediarules[$media][$selector][$property] ) )
			unset( $this->mediarules[$media][$selector][$property] );
	}

	/**
	 * Create general CSS style
	 *
	 * @since 3.0.0
	 * @access public
	 * @param string $property name of the css property
	 * @param string $value value of the css property
	 * @param string $idtag setting id used in wp admin (may be used in live preview js)
	 *                      used for fetching background and typography settings
	 * @param bool|string $important
	 * @param bool $typography_reset Used for 'typography' property
	 * @return mixed empty if sanitization failed, else the sanitized property array
	 */
	function sanitize_css_rule( $property, $value = '', $idtag = '', $important = false, $typography_reset = false ) {

		if ( empty( $property ) )
			return '';
		if ( $property == 'background' || $property == 'font' || $property == 'typography' ) {
			if ( empty( $value ) && empty( $idtag ) ) return '';
		} else {
			if ( empty( $value ) ) return '';
		}

		/** Sanitize CSS values **/
		if ( $value != 'inherit' ) : switch( $property ):

			case 'color':
			case 'background-color':
			case 'border-color':
			case 'border-right-color':
			case 'border-bottom-color':
			case 'border-top-color':
			case 'border-left-color':
				if ( 'none' == $value || 'transparent' == $value )
					$value = 'transparent';
				else
					$value = hoot_sanitize_color( $value );
				break;

			case 'background':
				if ( !empty( $value ) ) {
					if ( 'none' == $value || 'transparent' == $value ) {
						$value = 'none';
					} else {
						$value = hoot_sanitize_color( $value );
					}
				} elseif ( !empty( $idtag ) ) {
					// use the background function for multiple background properties
					return $this->sanitize_background( $idtag, $important );
				}
				break;

			case 'background-image':
				$check = esc_url( $value );
				$value = ( !empty( $check ) ) ? 'url("' . esc_url( $value ) . '")' : '';
				break;

			case 'background-repeat':
				$value = hoot_sanitize_background_repeat( $value );
				break;

			case 'background-position':
				$value = hoot_sanitize_background_position( $value );
				break;

			case 'background-attachment':
				$value = hoot_sanitize_background_attachment( $value );
				break;

			case 'background-size':
				$value = hoot_sanitize_background_size( $value );
				break;

			case 'box-shadow':
			case '-moz-box-shadow':
			case '-webkit-box-shadow':
				$value = esc_attr( $value );
				break;

			case 'typography':
			case 'font':
				if ( !empty( $value ) ) {
					$property = 'font-family';
					$value = hoot_sanitize_fontface( $value );
				} elseif ( !empty( $idtag ) ) {
					// use the typography function for multiple font properties
					return $this->sanitize_typography( $idtag, $important, $typography_reset );
				}
				break;

			case 'font-family':
				$value = hoot_sanitize_fontface( $value );
				break;

			case 'font-style':
				$recognized = array( 'inherit', 'initial', 'italic', 'normal', 'oblique' );
				$value = in_array( $value, $recognized ) ? $value : '';
				break;

			case 'font-weight':
				$value_check = intval( $value );
				if ( !empty( $value_check ) ) {
					// for numerical weights like 300, 600 etc.
					$value = $value_check;
				} else {
					// for strings like 'bold', 'light', 'lighter' etc.
					$recognized = array( 'bold', 'bolder', 'inherit', 'initial', 'lighter', 'normal' );
					$value = in_array( $value, $recognized ) ? $value : '';
				}
				break;

			case 'text-decoration':
				$recognized = array( 'blink', 'inherit', 'initial', 'line-through', 'overline', 'underline' );
				$value = in_array( $value, $recognized ) ? $value : '';
				break;

			case 'text-transform':
				$recognized = array( 'capitalize', 'inherit', 'initial', 'lowercase', 'none', 'uppercase' );
				$value = in_array( $value, $recognized ) ? $value : '';
				break;

			case 'font-size':
			case 'padding':
			case 'padding-right':
			case 'padding-bottom':
			case 'padding-left':
			case 'padding-top':
			case 'margin':
			case 'margin-right':
			case 'margin-bottom':
			case 'margin-left':
			case 'margin-top':
			case 'height':
			case 'max-height':
			case 'min-height':
			case 'width':
			case 'max-width':
			case 'min-width':
				$value_check = preg_replace('/px|em|rem/','', $value);
				$value_check = intval( $value_check );
				$value = ( !empty( $value_check ) || '0' === $value_check || 0 === $value_check ) ? $value : '';
				break;

			case 'opacity':
				$value_check = intval( $value );
				$value = ( !empty( $value_check ) || '0' === $value_check || 0 === $value_check ) ? $value : '';
				break;

		endswitch; endif;

		/* Return */
		if ( empty( $value ) ) {
			// if $value is empty => failed sanitization checks
			return '';
		} else {
			return array(
				$property => array(
					'value'     => $value,
					'important' => $important,
					'idtag'     => $idtag,
				),
			);
		}

	}

	/**
	 * Sanitize and create CSS styles from a background type.
	 *
	 * @since 3.0.0
	 * @access public
	 * @param string $idtag
	 * @param bool|string $important
	 * @return mixed empty if sanitization failed, else array of sanitized properties arrays
	 */
	function sanitize_background( $idtag, $important = false ) {

		if ( empty( $idtag ) || !is_string( $idtag ) )
			return '';

		/* Variables */
		$background = array();
		$properties = array( 'color', 'type', 'pattern', 'image', 'repeat', 'position', 'attachment', 'size' );

		/* Get Background Values */
		foreach ( $properties as $property ) {
			$prop_value = hoot_get_mod( "{$idtag}-{$property}" );
			if ( !empty( $prop_value ) )
				$background[ $property ] = $prop_value ;
		}

		return $this->sanitize_backgroundarray( $background,  $idtag, $important );
	}

	/**
	 * Sanitize and create CSS styles from a background type.
	 *
	 * @since 3.0.0
	 * @access public
	 * @param string $idtag
	 * @param bool|string $important
	 * @return mixed empty if sanitization failed, else array of sanitized properties arrays
	 */
	function sanitize_backgroundarray( $backgroundarray, $idtag = '', $important = false ) {

		if ( empty( $idtag ) || !is_string( $idtag ) )
			$idtag = '';

		/* Variables */
		$output = array();
		$properties = array( 'color', 'type', 'pattern', 'image', 'repeat', 'position', 'attachment', 'size' );

		$backgroundarray = wp_parse_args( $backgroundarray, array(
			'color' => '',
			'type' => 'predefined',
			'pattern' => 0,
			'image' => '',
			'repeat' => '',
			'position' => '',
			'attachment' => '',
			'size' => '',
		) );
		extract( $backgroundarray, EXTR_SKIP );

		/* Create CSS Rules */
		if ( !empty( $color ) ) {
			$output['color'] = $this->sanitize_css_rule( 'background-color', $color, $idtag . '-color', $important );
		}

		if ( 'custom' == $type ) {
			$check = esc_url( $image );
			if ( !empty( $check ) ) {

				$output['image'] = $this->sanitize_css_rule( 'background-image', $image, $idtag . '-image', $important );

				if ( !empty( $repeat ) )
					$output['repeat'] = $this->sanitize_css_rule( 'background-repeat', $repeat, $idtag . '-repeat', $important );

				if ( !empty( $position ) ) 
					$output['position'] = $this->sanitize_css_rule( 'background-position', $position, $idtag . '-position', $important );

				if ( !empty( $attachment ) )
					$output['attachment'] = $this->sanitize_css_rule( 'background-attachment', $attachment, $idtag . '-attachment', $important );

				if ( !empty( $size ) && $size != 'original' )
					$output['size'] = $this->sanitize_css_rule( 'background-size', $size, $idtag . '-size', $important );

			}
		}
		else { // 'predefined' == $type || empty

			// 'hoot_sanitize_background_pattern' will return 0 if pattern = 0 i.e. user selected the first options of 'No Pattern'
			$pattern = hoot_sanitize_background_pattern( $pattern );
			if ( $pattern ) {
				$background_image = hoot_data()->template_uri . $pattern;
				$output['image'] = $this->sanitize_css_rule( 'background-image', $background_image, $idtag . '-pattern', $important );
				$output['repeat'] = $this->sanitize_css_rule( 'background-repeat', 'repeat', '', $important );
			}

		}

		$output = apply_filters( 'hoot_style_builder_background', $output, $idtag, $important );

		/* Return */
		if ( is_array( $output ) && !empty( $output ) ) {
			$return = array();
			foreach ( $output as $rule ) {
				if ( !empty( $rule ) && is_array( $rule ) )
					$return = array_merge( $return, $rule );
			}
			return $return;
		}

		/* Failed Sanitization */
		return '';
	}

	/**
	 * Sanitize and create CSS styles from a typography type.
	 *
	 * @since 3.0.0
	 * @access public
	 * @param string $idtag
	 * @param bool|string $important
	 * @param bool $reset Reset earlier css rules from stylesheets etc.
	 * @return mixed empty if sanitization failed, else array of sanitized properties arrays
	 */
	function sanitize_typography( $idtag, $important = false, $reset = false ) {

		if ( empty( $idtag ) || !is_string( $idtag ) )
			return;

		/* Variables */
		$typography = array();
		$properties = array( 'size', 'face', 'style', 'color' );

		/* Get Typography Values */
		foreach ( $properties as $property ) {
			$prop_value = hoot_get_mod( "{$idtag}-{$property}" );
			if ( !empty( $prop_value ) )
				$typography[ $property ] = $prop_value;
		}

		return $this->sanitize_typographyarray( $typography, $idtag, $important, $reset );
	}

	/**
	 * Sanitize and create CSS styles from a typography type.
	 *
	 * @since 3.0.0
	 * @access public
	 * @param string $idtag
	 * @param bool|string $important
	 * @param bool $reset Reset earlier css rules from stylesheets etc.
	 * @return mixed empty if sanitization failed, else array of sanitized properties arrays
	 */
	function sanitize_typographyarray( $typographyarray, $idtag = '', $important = false, $reset = false ) {

		if ( empty( $idtag ) || !is_string( $idtag ) )
			$idtag = '';

		/* Variables */
		$output = array();
		$properties = array( 'size', 'face', 'style', 'color' );

		$typographyarray = wp_parse_args( $typographyarray, array(
			'size' => '',
			'face' => '',
			'style' => '',
			'color' => '',
		) );
		extract( $typographyarray, EXTR_SKIP );

		/* Create CSS Rules */
		if ( !empty( $color ) ) {
			$output['color'] = $this->sanitize_css_rule( 'color', $color, $idtag . '-color', $important );
		}

		if ( !empty( $size ) ) {
			$output['font-size'] = $this->sanitize_css_rule( 'font-size', $size, $idtag . '-size', $important );
		}

		if ( !empty( $face ) ) {
			$output['font-family'] = $this->sanitize_css_rule( 'font-family', $face, $idtag . '-face', $important );
		}

		$style = ( !empty( $style ) ) ? hoot_sanitize_font_style( $style ) : '';
		if ( !empty( $style ) ) {
			switch ( $style ) {
				case 'italic':
					$output['font-style'] = $this->sanitize_css_rule( 'font-style', 'italic', $idtag . '-style', $important );
					break;
				case 'bold':
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', 'bold', $idtag . '-style', $important );
					break;
				case 'bold italic':
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', 'bold', $idtag . '-style', $important );
					$output['font-style'] = $this->sanitize_css_rule( 'font-style', 'italic', $idtag . '-style', $important );
					break;
				case 'semibold':
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', '600', $idtag . '-style', $important );
					break;
				case 'semibold italic':
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', '600', $idtag . '-style', $important );
					$output['font-style'] = $this->sanitize_css_rule( 'font-style', 'italic', $idtag . '-style', $important );
					break;
				case 'lighter':
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', 'lighter', $idtag . '-style', $important );
					break;
				case 'lighter italic':
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', 'lighter', $idtag . '-style', $important );
					$output['font-style'] = $this->sanitize_css_rule( 'font-style', 'italic', $idtag . '-style', $important );
					break;
				case 'uppercase':
					$output['text-transform'] = $this->sanitize_css_rule( 'text-transform', 'uppercase', $idtag . '-style', $important );
					break;
				case 'uppercase italic':
					$output['text-transform'] = $this->sanitize_css_rule( 'text-transform', 'uppercase', $idtag . '-style', $important );
					$output['font-style'] = $this->sanitize_css_rule( 'font-style', 'italic', $idtag . '-style', $important );
					break;
				case 'uppercase bold':
					$output['text-transform'] = $this->sanitize_css_rule( 'text-transform', 'uppercase', $idtag . '-style', $important );
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', 'bold', $idtag . '-style', $important );
					break;
				case 'uppercase bold italic':
					$output['text-transform'] = $this->sanitize_css_rule( 'text-transform', 'uppercase', $idtag . '-style', $important );
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', 'bold', $idtag . '-style', $important );
					$output['font-style'] = $this->sanitize_css_rule( 'font-style', 'italic', $idtag . '-style', $important );
					break;
				case 'uppercase semibold':
					$output['text-transform'] = $this->sanitize_css_rule( 'text-transform', 'uppercase', $idtag . '-style', $important );
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', '600', $idtag . '-style', $important );
					break;
				case 'uppercase semibold italic':
					$output['text-transform'] = $this->sanitize_css_rule( 'text-transform', 'uppercase', $idtag . '-style', $important );
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', '600', $idtag . '-style', $important );
					$output['font-style'] = $this->sanitize_css_rule( 'font-style', 'italic', $idtag . '-style', $important );
					break;
				case 'uppercase lighter':
					$output['text-transform'] = $this->sanitize_css_rule( 'text-transform', 'uppercase', $idtag . '-style', $important );
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', 'lighter', $idtag . '-style', $important );
					break;
				case 'uppercase lighter italic':
					$output['text-transform'] = $this->sanitize_css_rule( 'text-transform', 'uppercase', $idtag . '-style', $important );
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', 'lighter', $idtag . '-style', $important );
					$output['font-style'] = $this->sanitize_css_rule( 'font-style', 'italic', $idtag . '-style', $important );
					break;
				case 'none': default:
					break;
			}
			if ( $reset ) {
				if ( empty( $output['font-style'] ) )
					$output['font-style'] = $this->sanitize_css_rule( 'font-style', 'normal', $idtag . '-style', $important );
				if ( empty( $output['text-transform'] ) )
					$output['text-transform'] = $this->sanitize_css_rule( 'text-transform', 'none', $idtag . '-style', $important );
				if ( empty( $output['font-weight'] ) )
					$output['font-weight'] = $this->sanitize_css_rule( 'font-weight', 'normal', $idtag . '-style', $important );
			}
		}

		$output = apply_filters( 'hoot_style_builder_typography', $output, $idtag, $important, $reset );

		/* Return */
		if ( is_array( $output ) && !empty( $output ) ) {
			$return = array();
			foreach ( $output as $rule ) {
				if ( !empty( $rule ) && is_array( $rule ) )
					$return = array_merge( $return, $rule );
			}
			return $return;
		}

		/* Failed Sanitization */
		return '';
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

/* Initialize class */
global $hoot_style_builder;
$hoot_style_builder = Hoot_Style_Builder::get_instance();