<?php
/**
 * Customize for sortlist, extend the WP customizer
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Sortlist Control Class extends the WP customizer
 *
 * @since 3.0.0
 */
// Only load in customizer (not in frontend)
if ( class_exists( 'WP_Customize_Control' ) ) :
class Hoot_Customize_Sortlist_Control extends WP_Customize_Control {

	/**
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $type = 'sortlist';

	/**
	 * Define variable to whitelist sublabel parameter
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $sublabel = '';

	/**
	 * Define variable to whitelist options parameter
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $options = '';

	/**
	 * Define variable to whitelist attributes parameter
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $attributes = '';

	/**
	 * Render the control's content.
	 * Allows the content to be overriden without having to rewrite the wrapper.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_content() {

		switch ( $this->type ) {

			case 'sortlist' :

				if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;

				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif;

				if ( ! empty( $this->sublabel ) ) : ?>
					<span class="description customize-control-sublabel"><?php echo wp_kses_post( $this->sublabel ); ?></span>
				<?php endif;

				/** Create sortlist **/

				$id = str_replace( '[', '-', str_replace( ']', '', $this->id ) );
				$choices = $this->choices;
				$rawvalue = $this->value();
				$valuearray = array();

				// We do not use JSON.stringify( $optionsform.serializeArray() ) and PHP json_decode().
					// This is because when using a dafult value, it is passed through get_theme_mod which creates a sprintf error ( since default string has a lot of % signs from http_build_query(): php equivalent of jquery.serialize() ). We dont face this issue if the value is stored, as sprintf is only applied on default in get_theme_mod. Also note, this error is only visible in customizer view.
				// Hence we use a mix. For normal stored values and js computation, we use jquery.serialize() and PHP parse_str().
				// However, for defaults, we still use json_encode instead of http_build_query due to above reason
				// If revert to stringify + json_decode():
					// Known bug: error in saving (") in textareas when using stringify + json_decode()
					// Make sure hidden input has value marked with single inverted (') to work with stringify which uses " in the string.
				if ( !empty( $rawvalue ) ) { // We either have a default, or value stored
					$value_json = json_decode( $rawvalue, true ); // We cant use parse_str because parse_str on json_encoded string still gives an array. However json_decode on jquery.serialize string returns null
					if ( !empty( $value_json ) && is_array( $value_json ) ) { // json_encoded default value
						$valuearray = $value_json;
					} else {
						parse_str($rawvalue, $valuearray);
					}
				}

				// Check if stored value / default value parsed properly to an array
				if ( !empty( $valuearray ) && is_array( $valuearray ) ) {
					$valuearray = $valuearray[ esc_attr( $id ) ];
					// Check choices if any missing (useful if a child theme adds choices after this setting has been saved atleast once in database)
					foreach ( $choices as $choiceid => $choicelabel )
						if ( !array_key_exists( $choiceid, $valuearray ) )
							$valuearray[ $choiceid ]['sortitem_hide'] = '';
					// Also check if a choice has been removed (by child theme) but its value still exists in stored value
					foreach ( $valuearray as $choiceid => $choicevalues )
						if ( empty( $choices[ $choiceid ] ) )
							unset( $valuearray[ $choiceid ] );
				} else {
				// no value stored, no default=> create empty array
					foreach ( $choices as $choiceid => $choicelabel )
						$valuearray[ $choiceid ] = $valuearray[ $choiceid ]['sortitem_hide'] = '';
				}

				$display = ( isset( $this->attributes['display-label'] ) && $this->attributes['display-label'] === false ) ? false : true; // default: true
				$display = ( !empty( $this->options ) && is_array( $this->options ) ) ? $display : true; // always true if no options

				$hideable = ( !empty( $this->attributes['hideable'] ) ); // default: false
				$hideable = ( !$display ) ? false : $hideable; // always false if display-label false

				$open = ( isset( $this->attributes['open-state'] ) ) ? ( ( $this->attributes['open-state'] === true ) ? 'data-openstate="all"' : 'data-openstate="' . esc_attr( $this->attributes['open-state'] ) . '"' ) : ''; // default: false
				$open = ( !$display ) ? 'data-openstate="all"' : $open; // always true if display-label false

				$sortable = ( !empty( $this->attributes['sortable'] ) ); // default: false
				$sortable = ( !$display ) ? false : $sortable; // always false if display-label false

				?>
				<ul id="hoot-control-sortlist-<?php echo esc_attr( $id ) ?>" class="hoot-control-sortlist <?php if ( $sortable ) echo 'sortable'; ?>" <?php echo $open; ?>>
					<?php foreach ( $valuearray as $choiceid => $choicevalues ) : ?>

						<li id="hoot-control-sortlist-<?php echo esc_attr( $choiceid ) ?>" class="hoot-control-sortlistitem <?php echo ( !$hideable || empty( $choicevalues['sortitem_hide'] ) ? '' : 'deactivated' ) ?>" data-choiceid="<?php echo esc_attr( $choiceid ) ?>">

							<?php if ( $display ) : ?>
								<span class="hoot-sortlistitem-head">
									<?php if ( $sortable ) : ?>
										<i class="sortlistitem-sort fas fa-arrows-alt"></i>
									<?php endif; ?>
									<span class="sortlistitem-label"><?php echo esc_html( $choices[$choiceid] ); ?></span>
									<?php if ( $hideable ) : ?>
										<i class="sortlistitem-display fas fa-eye"><span><?php esc_attr_e( 'Deactivate', 'magazine-news-byte' ) ?></span></i>
									<?php endif; ?>
									<?php $hidden = ( !$hideable || empty( $choicevalues['sortitem_hide'] ) ? '0' : '1' ); ?>
									<input class="hoot-control-sortlistitem-hide" name="<?php echo esc_attr( $id.'['.$choiceid.']'.'[sortitem_hide]' ); ?>" value="<?php echo esc_attr( $hidden ); ?>" type="hidden"/>
									<?php if ( !empty( $this->options[$choiceid] ) && is_array( $this->options[$choiceid] ) ) : ?>
										<i class="sortlistitem-expand fas fa-caret-down"></i>
									<?php endif; ?>
									<div class="clear"></div>
								</span>
							<?php endif; ?>

							<?php if ( !empty( $this->options[$choiceid] ) && is_array( $this->options[$choiceid] ) ) : ?>
								<span class="hoot-sortlistitem-options">
									<?php
									foreach ( $this->options[$choiceid] as $optionid => $option ) :
										if ( isset( $option['type'] ) ) { ?>

											<span class="hoot-sortlistitem-option hoot-sortlistitem-option-<?php echo sanitize_html_class( $option['type'] ); ?>"><?php

												if ( !empty( $option['label'] ) )
													echo '<span class="hoot-sortlistitem-option-title">' . esc_html( $option['label'] ) .'</span>';
												if ( !empty( $option['sublabel'] ) )
													echo '<span class="hoot-sortlistitem-option-sublabel description">' . wp_kses_post( $option['sublabel'] ) . '</span>';

												$optionname = $id.'['.$choiceid.']'.'['.$optionid.']';
												$optionvalue = !empty( $choicevalues[$optionid] ) ? $choicevalues[$optionid] : ( ( !empty( $option['default'] ) ) ? $option['default'] : '' );

												switch( $option['type'] ) :
													case 'text': ?>
														<input name="<?php echo esc_attr( $optionname ) ?>" type="text" value="<?php echo esc_attr( $optionvalue ) ?>"><?php
														break;
													case 'textarea': ?>
														<textarea rows="3" name="<?php echo esc_attr( $optionname ) ?>"><?php echo esc_textarea( $optionvalue ); ?></textarea><?php
														break;
													case 'checkbox': ?>
														<input name="<?php echo esc_attr( $optionname ) ?>" type="checkbox" value="1" <?php checked( $optionvalue ) ?>><?php
														break;
													case 'select': ?>
														<select name="<?php echo esc_attr( $optionname ) ?>">
															<?php
															if ( !empty( $option['choices'] ) && is_array( $option['choices'] ) ) {
																foreach ( $option['choices'] as $skey => $schoice ) { ?>
																	<option value="<?php echo esc_attr( $skey ); ?>" <?php selected( $optionvalue, $skey ) ?>><?php echo esc_html( $schoice ) ?></option>
																<?php
																}
															}
															?>
														</select><?php
														break;
													case 'radioimage': ?>
														<?php
														if ( !empty( $option['choices'] ) && is_array( $option['choices'] ) ) {
															foreach ( $option['choices'] as $skey => $simage ) {
																$checked = checked( $optionvalue, $skey, false ); ?>
																<label class="hoot-customize-radioimage<?php if ($checked) echo ' radiocheck' ?>">
																	<input type="radio" value="<?php echo esc_attr( $skey ); ?>" name="<?php echo esc_attr( $optionname ) ?>" <?php echo $checked; ?> />
																	<img src="<?php echo esc_url( $simage ); ?>" />
																</label>
															<?php
															}
															echo '<div class="clear"></div>';
														}
														break;
													case 'content':
														?><span id="<?php echo sanitize_html_class( $id.'_'.$choiceid.'_'.$optionid ); ?>" class="<?php echo sanitize_html_class( $id.'_'.$optionid ); ?> hoot-customize-control-sortlist-content"><?php echo wp_kses_post( $option['content'] ); ?></span><?php
														break;
												endswitch; ?>

											</span><?php

										}
									endforeach;
									?>
								</span>
							<?php endif; ?>

						</li>

					<?php endforeach; ?>
				</ul>
				<?php /* Since we are using JSON.stringify (and not jquery.serialize), make sure the input value uses ' instead of " */ ?>
				<input class="hoot-customize-control-sortlist" value='<?php echo esc_attr( $this->value() ) ?>' <?php $this->link(); ?> type="hidden"/>
				<?php

				break;

		}

	}

}
endif;

/**
 * Hook into control display interface
 *
 * @since 3.0.0
 * @param object $wp_customize
 * @param string $id
 * @param array $setting
 * @return void
 */
// Only load in customizer (not in frontend)
if ( class_exists( 'WP_Customize_Control' ) ) :
function hoot_customize_sortlist_control_interface ( $wp_customize, $id, $setting ) {
	if ( isset( $setting['type'] ) ) :
		if ( $setting['type'] == 'sortlist' || $setting['type'] == 'repeatable' ) {
			$setting['type'] = 'sortlist';
			$wp_customize->add_control(
				new Hoot_Customize_Sortlist_Control( $wp_customize, $id, $setting )
			);
		}
	endif;
}
add_action( 'hoot_customize_control_interface', 'hoot_customize_sortlist_control_interface', 10, 3 );
endif;

/**
 * Modify the settings array and prepare sortlist settings
 *
 * @since 3.0.0
 * @param array $value
 * @param string $key
 * @param array $setting
 * @param int $count
 * @return void
 */
function hoot_customize_prepare_sortlist_settings( $value, $key, $setting, $count ) {

	if ( $setting['type'] == 'sortlist' ) {

		$newdefault = array();
		foreach ( $setting['choices'] as $choiceid => $choicelabel ) {
			if ( isset( $setting['default'][$choiceid] ) ) {
				$newdefault[$choiceid] = array_merge( array( 'sortitem_hide' => '' ), $setting['default'][$choiceid] );
			} else {
				$newdefault[$choiceid]['sortitem_hide'] = '';
			}
		}

		if ( !empty( $newdefault ) )
			$setting['default'] = json_encode( array( $key => $newdefault ) );
		elseif ( isset( $setting['default'] ) )
			unset( $setting['default'] );

		$value[ $key ] = $setting;
	}

	return $value;

}
add_filter( 'hoot_customize_prepare_settings', 'hoot_customize_prepare_sortlist_settings', 10, 4 );

/**
 * Add sanitization function
 *
 * @since 3.0.0
 * @param string $callback
 * @param string $type
 * @param array $setting
 * @param string $name name (id) of the setting
 * @return string
 */
function hoot_customize_sanitize_sortlist_callback( $callback, $type, $setting, $name ) { 
	if ( $type == 'sortlist' || $type == 'repeatable' )
		$callback = 'hoot_sanitize_customize_sortlist';
	return $callback;
}
add_filter( 'hoot_customize_sanitize_callback', 'hoot_customize_sanitize_sortlist_callback', 5, 4 );

/**
 * Sanitize sortlist value.
 *
 * @since 3.0.0
 * @param string $value The value to sanitize.
 * @param mixed $setting 'WP_Customize_Setting' Object (called by Customizer) or Setting Name (called by hoot_get_mod)
 * @return string The sanitized value.
 */
function hoot_sanitize_customize_sortlist( $value, $setting ) {

	parse_str( $value, $valuearray );

	if ( !empty( $valuearray ) && is_array( $valuearray ) ) {

		$return = array();
		$id = array_keys( $valuearray );
		$id = current( $id ); // use $id instead of reset( $id ) : reset returns false if empty, only 1 element, so we good to go

		// Get Choice Options if exist
		$name = '';
		if ( is_object( $setting ) )
			$name = $setting->id;
		elseif ( is_string( $setting ) )
			$name = $setting;
		$hoot_customize = Hoot_Customize::get_instance();
		$settings = $hoot_customize->get_options('settings');
		$choices = ( isset( $settings[$name]['choices'] ) ) ? $settings[$name]['choices'] : array();
		$options = ( isset( $settings[$name]['options'] ) ) ? $settings[$name]['options'] : array();

		// Build return array
		foreach ( $valuearray[ $id ] as $choiceid => $choicevalues ) :
			// Sanitize choice name
			if ( !empty( $choices[$choiceid] ) ):
				// Sanitize choice values: hidden
				$return[$id][$choiceid]['sortitem_hide'] =
					( isset( $choicevalues['sortitem_hide'] ) ) ?
					$choicevalues['sortitem_hide'] : 0;
				// Sanitize choice values: options
				if ( !empty( $options[$choiceid] ) ){
					foreach ( $options[$choiceid] as $optionid => $optionarray ) {
						if ( !empty( $optionarray['type'] ) ) {
							switch( $optionarray['type'] ):
								// @todo : use sanitization.php functions
								case 'text':
								case 'textarea':
									global $allowedtags;
									$return[$id][$choiceid][$optionid] =
										( isset( $choicevalues[$optionid] ) ) ?
										wp_kses_post( $choicevalues[$optionid] , $allowedtags ) : '';
									break;
								case 'checkbox':
									$return[$id][$choiceid][$optionid] =
										( !empty( $choicevalues[$optionid] ) ) ?
										1 : 0;
									break;
								case 'select':
								case 'radio':
								case 'radioimage':
									$return[$id][$choiceid][$optionid] =
										( !array_key_exists( $choicevalues[$optionid], $optionarray['choices'] ) ) ?
										$choicevalues[$optionid] : $choicevalues[$optionid];
									break;
							endswitch;
						}
					}
				}
			endif;
		endforeach;

		// Same as jquery.serialize ( though it also converts entities. example: & to &amp; )
		// If using JSON.stringify, use json_encode instead
		return http_build_query( $return );

	} else {
		return '';
	}
}

/**
 * Utility function to map a sortlist option value to array
 * 
 * @since 3.0.0
 * @access public
 * @param array $value jquery.serialize() string
 * @param bool $returnid return id instead of the value array
 * @return array|false
 */
function hoot_sortlist( $rawvalue, $returnid = false ) {

	$valuearray = array();
	if ( !empty( $rawvalue ) ) { // We either have a default, or value stored
		$value_json = json_decode( $rawvalue, true ); // We cant use parse_str because parse_str on json_encoded string still gives an array. However json_decode on jquery.serialize string returns null
		if ( !empty( $value_json ) && is_array( $value_json ) ) { // json_encoded default value
			$valuearray = $value_json;
		} else {
			parse_str($rawvalue, $valuearray);
		}
	}

	if ( !empty( $valuearray ) && is_array( $valuearray ) ) {

		$return = array();
		$id = array_keys( $valuearray );
		$id = current( $id ); // use $id instead of reset( $id ) : reset returns false if empty, only 1 element, so we good to go

		if ( isset( $valuearray[ $id ] ) ) {
			if ( !$returnid )
				return $valuearray[ $id ];
			else
				return $id;
		} else {
			return false;
		}

	} else {
		return false;
	}
}