<?php

/**
 * Visitor conditions under which to (not) show an ad
 *
 * @since 1.5.4
 */
class Advanced_Ads_Visitor_Conditions {

	/**
	 * Instance of Advanced_Ads_Visitor_Conditions
	 *
	 * @var Advanced_Ads_Visitor_Conditions
	 */
	protected static $instance;

	/**
	 * Registered visitor conditions
	 *
	 * @var array $conditions
	 */
	public $conditions;

	/**
	 * Start of name in form elements
	 */
	const FORM_NAME = 'advanced_ad[visitors]';

	/**
	 * Advanced_Ads_Visitor_Conditions constructor
	 */
	public function __construct() {

		// register conditions.
		$this->conditions = apply_filters(
			'advanced-ads-visitor-conditions',
			array(
				'mobile'   => array(
					// type of the condition.
					'label'       => __( 'device', 'advanced-ads' ),
					'description' => __( 'Display ads only on mobile devices or hide them.', 'advanced-ads' ),
					'metabox'     => array( 'Advanced_Ads_Visitor_Conditions', 'mobile_is_or_not' ), // callback to generate the metabox.
					'check'       => array( 'Advanced_Ads_Visitor_Conditions', 'check_mobile' ), // callback for frontend check.
					'helplink'    => ADVADS_URL . 'manual/display-ads-either-on-mobile-or-desktop/#utm_source=advanced-ads&utm_medium=link&utm_campaign=edit-visitor-mobile', // link to help section.
				),
				'loggedin' => array(
					'label'        => __( 'logged-in visitor', 'advanced-ads' ),
					'description'  => __( 'Whether the visitor has to be logged in or not in order to see the ads.', 'advanced-ads' ),
					'metabox'      => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_is_or_not' ), // callback to generate the metabox.
					'check'        => array( 'Advanced_Ads_Visitor_Conditions', 'check_logged_in' ), // callback for frontend check.
					'passive_info' => array(
						'hash_fields' => null,
						'remove'      => 'login',
						'function'    => 'is_user_logged_in',
					),
				),
			)
		);
	}

	/**
	 * Load instance of Advanced_Ads_Visitor_Conditions
	 *
	 * @return Advanced_Ads_Visitor_Conditions
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Get the conditions array alphabetically by label
	 *
	 * @since 1.8.12
	 */
	public function get_conditions() {
		uasort( $this->conditions, 'Advanced_Ads_Admin::sort_condition_array_by_label' );

		return $this->conditions;
	}

	/**
	 * Callback to render the mobile condition using the "is not" condition
	 *
	 * @param array  $options options of the condition.
	 * @param int    $index index of the condition.
	 * @param string $form_name name of the form, falls back to class constant.
	 */
	public static function mobile_is_or_not( $options, $index = 0, $form_name = '' ) {

		if ( ! isset( $options['type'] ) || '' === $options['type'] ) {
			return;
		}

		$type_options = self::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		// form name basis.
		$name = self::get_form_name_with_index( $form_name, $index );

		// options.
		$operator = isset( $options['operator'] ) ? $options['operator'] : 'is';

		include ADVADS_BASE_PATH . 'admin/views/conditions/condition-device.php';

		if ( ! defined( 'AAR_SLUG' ) ) {
			?><p><?php
			sprintf(
				wp_kses(
				// translators: %s is a URL. Please don’t change it.
					__( 'Display ads by the available space on the device or target tablets with the <a href="%s" target="_blank">Responsive add-on</a>', 'advanced-ads' ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				),
				ADVADS_URL . 'add-ons/responsive-ads/#utm_source=advanced-ads&utm_medium=link&utm_campaign=edit-visitor-responsive'
			);
			?></p><?php
		}
	}

	/**
	 * Callback to display the "is not" condition
	 *
	 * @param array  $options options of the condition.
	 * @param int    $index index of the condition.
	 * @param string $form_name name of the form, falls back to class constant.
	 */
	public static function metabox_is_or_not( $options, $index = 0, $form_name = '' ) {

		if ( ! isset( $options['type'] ) || '' === $options['type'] ) {
			return;
		}

		$type_options = self::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		// form name basis.
		$name = self::get_form_name_with_index( $form_name, $index );

		// options.
		$operator = isset( $options['operator'] ) ? $options['operator'] : 'is';

		include ADVADS_BASE_PATH . 'admin/views/conditions/condition-is-or-not.php';
	}

	/**
	 * Callback to display the any condition based on a number
	 *
	 * @param array  $options options of the condition.
	 * @param int    $index index of the condition.
	 * @param string $form_name name of the form, falls back to class constant.
	 */
	public static function metabox_number( $options, $index = 0, $form_name = '' ) {

		if ( ! isset( $options['type'] ) || '' === $options['type'] ) {
			return;
		}

		$type_options = self::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		// form name basis.
		$name = self::get_form_name_with_index( $form_name, $index );

		// options.
		$value    = isset( $options['value'] ) ? $options['value'] : 0;
		$operator = isset( $options['operator'] ) ? $options['operator'] : 'is_equal';

		include ADVADS_BASE_PATH . 'admin/views/conditions/condition-number.php';
	}

	/**
	 * Callback to display the any condition based on a number
	 *
	 * @param array  $options options of the condition.
	 * @param int    $index index of the condition.
	 * @param string $form_name name of the form, falls back to class constant.
	 */
	public static function metabox_string( $options, $index = 0, $form_name = '' ) {

		if ( ! isset( $options['type'] ) || '' === $options['type'] ) {
			return;
		}

		$type_options = self::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		// form name basis.
		$name = self::get_form_name_with_index( $form_name, $index );

		// options.
		$value    = isset( $options['value'] ) ? $options['value'] : '';
		$operator = isset( $options['operator'] ) ? $options['operator'] : 'contains';

		include ADVADS_BASE_PATH . 'admin/views/conditions/condition-string.php';
	}

	/**
	 * Controls frontend checks for conditions
	 *
	 * @param array       $options options of the condition.
	 * @param bool|object $ad false or Advanced_Ads_Ad.
	 *
	 * @return bool false, if ad can’t be delivered
	 */
	public static function frontend_check( $options = array(), $ad = false ) {
		$visitor_conditions = self::get_instance()->conditions;

		if ( is_array( $options ) && isset( $visitor_conditions[ $options['type'] ]['check'] ) ) {
			$check = $visitor_conditions[ $options['type'] ]['check'];
		} else {
			return true;
		}

		// call frontend check callback.
		if ( method_exists( $check[0], $check[1] ) ) {
			return call_user_func( array( $check[0], $check[1] ), $options, $ad );
		}

		return true;
	}

	/**
	 * Render the list of set visisor conditions
	 *
	 * @param array  $set_conditions array of existing conditions.
	 * @param string $list_target ID of the list with the conditions.
	 * @param string $form_name prefix of the form.
	 */
	public static function render_condition_list( array $set_conditions, $list_target = '', $form_name = '' ) {

		$conditions = self::get_instance()->get_conditions();

		// use default form name if not given explicitly.
		// @todo create a random form name, in case we have more than one form per page and the parameter is not given.
		$form_name = ! $form_name ? self::FORM_NAME : $form_name;

		include ADVADS_BASE_PATH . 'admin/views/conditions/visitor-conditions-list.php';

		/**
		 * Prepare condition form
		 *
		 * @todo if needed, allow to disable the form to add new conditions
		 */

		// add mockup conditions if add-ons are missing.
		$pro_conditions = array();
		if ( ! defined( 'AAP_VERSION' ) ) {
			$pro_conditions[] = __( 'browser language', 'advanced-ads' );
			$pro_conditions[] = __( 'cookie', 'advanced-ads' );
			$pro_conditions[] = __( 'max. ad clicks', 'advanced-ads' );
			$pro_conditions[] = __( 'max. ad impressions', 'advanced-ads' );
			$pro_conditions[] = __( 'new visitor', 'advanced-ads' );
			$pro_conditions[] = __( 'page impressions', 'advanced-ads' );
			$pro_conditions[] = __( 'referrer url', 'advanced-ads' );
			$pro_conditions[] = __( 'user agent', 'advanced-ads' );
			$pro_conditions[] = __( 'user can (capabilities)', 'advanced-ads' );
			$pro_conditions[] = __( 'user role', 'advanced-ads' );
		}
		if ( ! defined( 'AAGT_VERSION' ) ) {
			$pro_conditions[] = __( 'geo location', 'advanced-ads' );
		}
		if ( ! defined( 'AAR_VERSION' ) ) {
			$pro_conditions[] = __( 'browser width', 'advanced-ads' );
		}
		asort( $pro_conditions );

		// the action to call using AJAX.
		$action            = 'load_visitor_conditions_metabox';
		$connector_default = 'and';

		include ADVADS_BASE_PATH . 'admin/views/conditions/visitor-conditions-form-top.php';
		include ADVADS_BASE_PATH . 'admin/views/conditions/conditions-form.php';
	}

	/**
	 * Render connector option
	 *
	 * @param int    $index incremental index of the options.
	 * @param string $value connector value.
	 * @param string $form_name name of the form, falls back to class constant.
	 *
	 * @return string HTML of the connector option
	 * @todo combine this with the same function used for Display Conditions
	 *
	 * @since 1.7.0.4
	 */
	public static function render_connector_option( $index = 0, $value = 'or', $form_name ) {

		$label = ( 'or' === $value ) ? __( 'or', 'advanced-ads' ) : __( 'and', 'advanced-ads' );

		$name = self::get_form_name_with_index( $form_name, $index );

		// create random value to identify the form field.
		$rand = md5( $form_name );

		return '<input type="checkbox" name="' . $name . '[connector]' . '" value="or" id="advads-conditions-' .
		       $index . '-connector-' . $rand . '"' .
		       checked( 'or', $value, false )
		       . '><label for="advads-conditions-' . $index . '-connector-' . $rand . '">' . $label . '</label>';
	}

	/**
	 * Helper function to the name of a form field.
	 * falls back to default
	 *
	 * @param string $form_name form name if submitted.
	 * @param int    $index index of the condition.
	 *
	 * @return string
	 */
	public static function get_form_name_with_index( $form_name = '', $index = 0 ) {
		return ! empty( $form_name ) ? $form_name . '[' . $index . ']' : self::FORM_NAME . '[' . $index . ']';
	}

	/**
	 * Check mobile visitor condition in frontend
	 *
	 * @param array $options options of the condition.
	 *
	 * @return bool true if can be displayed
	 */
	public static function check_mobile( $options = array() ) {

		if ( ! isset( $options['operator'] ) ) {
			return true;
		}

		switch ( $options['operator'] ) {
			case 'is':
				if ( ! wp_is_mobile() ) {
					return false;
				}
				break;
			case 'is_not':
				if ( wp_is_mobile() ) {
					return false;
				}
				break;
		}

		return true;
	}

	/**
	 * Check mobile visitor condition in frontend
	 *
	 * @param array $options options of the condition.
	 *
	 * @return bool true if can be displayed
	 * @since 1.6.3
	 */
	public static function check_logged_in( $options = array() ) {

		if ( ! isset( $options['operator'] ) ) {
			return true;
		}

		switch ( $options['operator'] ) {
			case 'is':
				if ( ! is_user_logged_in() ) {
					return false;
				}
				break;
			case 'is_not':
				if ( is_user_logged_in() ) {
					return false;
				}
				break;
		}

		return true;
	}

	/**
	 * Helper for check with strings
	 *
	 * @param string $string string that is going to be checked.
	 * @param array  $options options of this condition.
	 *
	 * @return bool true if ad can be displayed
	 * @since 1.6.3
	 */
	public static function helper_check_string( $string = '', $options = array() ) {

		if ( ! isset( $options['operator'] ) || ! isset( $options['value'] ) || '' === $options['value'] ) {
			return true;
		}

		$operator = $options['operator'];
		$value    = $options['value'];

		// check the condition by mode and bool.
		$condition = true;
		switch ( $operator ) {
			// referrer contains string on any position.
			case 'contain':
				$condition = stripos( $string, $value ) !== false;
				break;
			// referrer does not contain string on any position.
			case 'contain_not':
				$condition = stripos( $string, $value ) === false;
				break;
			// referrer starts with the string.
			case 'start':
				$condition = stripos( $string, $value ) === 0;
				break;
			// referrer does not start with the string.
			case 'start_not':
				$condition = stripos( $string, $value ) !== 0;
				break;
			// referrer ends with the string.
			case 'end':
				$condition = substr( $string, - strlen( $value ) ) === $value;
				break;
			// referrer does not end with the string.
			case 'end_not':
				$condition = substr( $string, - strlen( $value ) ) !== $value;
				break;
			// referrer is equal to the string.
			case 'match':
				// strings do match, but should not or not match but should.
				$condition = strcasecmp( $value, $string ) === 0;
				break;
			// referrer is not equal to the string.
			case 'match_not':
				// strings do match, but should not or not match but should.
				$condition = strcasecmp( $value, $string ) !== 0;
				break;
			// string is a regular expression.
			case 'regex':
				// check regular expression first.
				if ( @preg_match( $value, null ) === false ) {
					Advanced_Ads::log( "Advanced Ads: regular expression '$value' in visitor condition is broken." );
				} else {
					$condition = preg_match( $value, $string );
				}
				break;
			// string is not a regular expression.
			case 'regex_not':
				if ( @preg_match( $value, null ) === false ) {
					Advanced_Ads::log( "Advanced Ads: regular expression '$value' in visitor condition is broken." );
				} else {
					$condition = ! preg_match( $value, $string );
				}
				break;
		}

		return $condition;
	}
}
