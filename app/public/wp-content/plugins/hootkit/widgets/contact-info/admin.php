<?php
/**
 * Contact Info Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Contact_Info_Widget
 */
class HootKit_Contact_Info_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-contact-info';
		$settings['name'] = hootkit()->get_string('contact-info');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Contact Information', 'hootkit' ),
			// 'classname'		=> 'hoot-contact-info-widget', // CSS class applied to frontend widget container via 'before_widget' arg
		);
		$settings['control_options'] = array();
		$settings['form_options'] = array(
			//'name' => can be empty or false to hide the name
			'title' => array(
				'name'		=> __( 'Title', 'hootkit' ),
				'type'		=> 'text',
			),
			'subtitle' => array(
				'name'		=> __( 'Sub Title (optional)', 'hootkit' ),
				'type'		=> 'text',
			),
			'address' => array(
				'name'		=> __( 'Address', 'hootkit' ),
				'type'		=> 'textarea',
				'settings'	=> array( 'rows' => 3 ),
			),
			'phone' => array(
				'name'		=> __( 'Phone', 'hootkit' ),
				'type'		=> 'text',
			),
			'email' => array(
				'name'		=> __( 'Email', 'hootkit' ),
				'type'		=> 'text',
				'sanitize'	=> 'email',
			),
			'profiles' => array(
				'name'		=> __( 'Profiles', 'hootkit' ),
				'type'		=> 'group',
				'options'	=> array(
					'item_name'	=> __( 'Contact Link', 'hootkit' ),
					'maxlimit'	=> 4,
					'limitmsg'	=> ( ( hootkit()->get_config( 'nohoot' ) ) ? __( 'Only 4 profiles allowed. Please use a wpHoot theme to add more profiles.', 'hootkit' ) : __( 'Only 4 profiles available in the Free version of the theme.', 'hootkit' ) ),
					'sortable'	=> true,
				),
				'fields'	=> array(
					'icon' => array(
						'name'		=> __( 'Profile', 'hootkit' ),
						'type'		=> 'select',
						'options'	=> hoot_enum_social_profiles(),
					),
					'text' => array(
						'name'		=> __( 'Display Text (Optional)', 'hootkit' ),
						'type'		=> 'text',
					),
					'url' => array(
						'name'		=> __( "URL (enter email address if you selected 'Email' in Profile above)", 'hootkit' ), // @NU Skype user id
						'std'		=> 'http://',
						'type'		=> 'text',
						'sanitize'	=> 'contact_info_sanitize_url',
					),
				),
			),
			'customcss' => array(
				'name'		=> __( 'Widget Options', 'hootkit' ),
				'type'		=> 'collapse',
				'fields'	=> array(
					'class' => array(
						'name'		=> __( 'Custom CSS Class', 'hootkit' ),
						'desc'		=> __( 'Give this widget a custom css classname', 'hootkit' ),
						'type'		=> 'text',
					),
					'mt' => array(
						'name'		=> __( 'Margin Top', 'hootkit' ),
						'desc'		=> __( '(in pixels) Leave empty to load default margins.<br>Hint: You can use negative numbers also.', 'hootkit' ),
						'type'		=> 'text',
						'settings'	=> array( 'size' => 3 ),
						'sanitize'	=> 'integer',
					),
					'mb' => array(
						'name'		=> __( 'Margin Bottom', 'hootkit' ),
						'desc'		=> __( '(in pixels) Leave empty to load default margins.<br>Hint: You can use negative numbers also.', 'hootkit' ),
						'type'		=> 'text',
						'settings'	=> array( 'size' => 3 ),
						'sanitize'	=> 'integer',
					),
				),
			),
		);

		if ( !in_array( 'widget-subtitle', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['subtitle'] );
		}

		$settings = apply_filters( 'hootkit_contact_info_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'contact-info', false );
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'contact-info' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_contact_info_register(){
	register_widget( 'HootKit_Contact_Info_Widget' );
}
add_action( 'widgets_init', 'hootkit_contact_info_register' );

/**
 * Custom Sanitization Function
 * @param string $value    Field Value
 * @param string $name     Custom sanitization ID
 * @param array  $instance Widget instance (values)
 * @return string
 */
function hootkit_sanitize_contact_info_url( $value, $name, $instance ){
	if ( $name == 'contact_info_sanitize_url' ) {
		if ( !empty( $instance['icon'] ) && $instance['icon'] == 'fa-skype' ) // @NU
			$new = sanitize_user( $value, true );
		elseif ( !empty( $instance['icon'] ) && $instance['icon'] == 'fa-envelope' )
			$new = ( is_email( $value ) ) ? sanitize_email( $value ) : '';
		else
			$new = esc_url_raw( $value );
		return $new;
	}
	return $value;
}
add_filter( 'hoot_admin_widget_sanitize_field', 'hootkit_sanitize_contact_info_url', 10, 3 );