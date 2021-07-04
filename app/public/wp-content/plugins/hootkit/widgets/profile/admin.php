<?php
/**
 * Profile Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Profile_Widget
 */
class HootKit_Profile_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-profile';
		$settings['name'] = hootkit()->get_string('profile');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Profile (about) block', 'hootkit' ),
			// 'classname'		=> 'hoot-profile-widget', // CSS class applied to frontend widget container via 'before_widget' arg
		);
		$settings['control_options'] = array();
		$settings['form_options'] = array(
			//'name' => can be empty or false to hide the name
			'title' => array(
				'name'		=> __( 'Title (optional)', 'hootkit' ),
				'type'		=> 'text',
			),
			'subtitle' => array(
				'name'		=> __( 'Sub Title (optional)', 'hootkit' ),
				'type'		=> 'text',
			),
			'image' => array(
				'name'		=> __('Image', 'hootkit'),
				'type'		=> 'image',
			),
			'img_style' => array(
				'name'		=> __( 'Image Style', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'circle',
				'options'	=> array(
					'circle'	=> __( 'Thumbnail (circle)', 'hootkit' ),
					'square'	=> __( 'Thumbnail (square)', 'hootkit' ),
					'full'		=> __( 'Full Size', 'hootkit' ),
				),
			),
			'content' => array(
				'name'		=> __( 'Content', 'hootkit' ),
				'type'		=> 'textarea',
			),
			'link_type' => array(
				'name'		=> __( 'Link Type', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'button',
				'options'	=> array(
					'button'	=> __( 'Button', 'hootkit' ),
					'text'		=> __( 'Text', 'hootkit' ),
				),
			),
			'button_text' => array(
				'name'		=> __( 'Link Text', 'hootkit' ),
				'type'		=> 'text',
				'std'		=> __( 'KNOW MORE', 'hootkit' ),
			),
			'url' => array(
				'name'		=> __( 'Link URL', 'hootkit' ),
				'desc'		=> __( 'Leave empty if you dont want to show link', 'hootkit' ),
				'type'		=> 'text',
				'sanitize'	=> 'url',
			),
			'icon1' => array(
				'name'		=> __( 'Social Icon 1', 'hootkit' ),
				'type'		=> 'select',
				'options'	=> hoot_enum_social_profiles(),
			),
			'url1' => array(
				'name'		=> __( 'URL 1', 'hootkit' ),
				'type'		=> 'text',
				'sanitize'	=> 'profile_link_sanitize_url',
			),
			'icon2' => array(
				'name'		=> __( 'Social Icon 2', 'hootkit' ),
				'type'		=> 'select',
				'options'	=> hoot_enum_social_profiles(),
			),
			'url2' => array(
				'name'		=> __( 'URL 2', 'hootkit' ),
				'type'		=> 'text',
				'sanitize'	=> 'profile_link_sanitize_url',
			),
			'icon3' => array(
				'name'		=> __( 'Social Icon 3', 'hootkit' ),
				'type'		=> 'select',
				'options'	=> hoot_enum_social_profiles(),
			),
			'url3' => array(
				'name'		=> __( 'URL 3', 'hootkit' ),
				'type'		=> 'text',
				'sanitize'	=> 'profile_link_sanitize_url',
			),
			'icon4' => array(
				'name'		=> __( 'Social Icon 4', 'hootkit' ),
				'type'		=> 'select',
				'options'	=> hoot_enum_social_profiles(),
			),
			'url4' => array(
				'name'		=> __( 'URL 4', 'hootkit' ),
				'type'		=> 'text',
				'sanitize'	=> 'profile_link_sanitize_url',
			),
			'icon5' => array(
				'name'		=> __( 'Social Icon 5', 'hootkit' ),
				'type'		=> 'select',
				'options'	=> hoot_enum_social_profiles(),
			),
			'url5' => array(
				'name'		=> __( 'URL 5', 'hootkit' ),
				'type'		=> 'text',
				'sanitize'	=> 'profile_link_sanitize_url',
			),
			'border' => array(
				'name'		=> __( 'Border', 'hootkit' ),
				'desc'		=> __( 'Top and bottom borders.', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'none none',
				'options'	=> array(
					'line line'		=> __( 'Top - Line || Bottom - Line', 'hootkit' ),
					'line shadow'	=> __( 'Top - Line || Bottom - DoubleLine', 'hootkit' ),
					'line none'		=> __( 'Top - Line || Bottom - None', 'hootkit' ),
					'shadow line'	=> __( 'Top - DoubleLine || Bottom - Line', 'hootkit' ),
					'shadow shadow'	=> __( 'Top - DoubleLine || Bottom - DoubleLine', 'hootkit' ),
					'shadow none'	=> __( 'Top - DoubleLine || Bottom - None', 'hootkit' ),
					'none line'		=> __( 'Top - None || Bottom - Line', 'hootkit' ),
					'none shadow'	=> __( 'Top - None || Bottom - DoubleLine', 'hootkit' ),
					'none none'		=> __( 'Top - None || Bottom - None', 'hootkit' ),
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

		$settings = apply_filters( 'hootkit_profile_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'profile', false );
		// Use Hootkit template if theme does not have one
		$widget_template = ( $widget_template ) ? $widget_template : hootkit()->dir . 'widgets/profile/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'profile' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_profile_widget_register(){
	register_widget( 'HootKit_Profile_Widget' );
}
add_action( 'widgets_init', 'hootkit_profile_widget_register' );

/**
 * Custom Sanitization Function
 * @param string $value    Field Value
 * @param string $name     Custom sanitization ID
 * @param array  $instance Widget instance (values)
 * @return string
 */
function hootkit_sanitize_profile_url( $value, $name, $instance ){
	if ( $name == 'profile_link_sanitize_url' ) {
		$key = array_search( $value, $instance, true );
		if ( !$key ) return false;
		$key = substr( $key, -1 );

		if ( !empty( $instance["icon{$key}"] ) && $instance["icon{$key}"] == 'fa-skype' ) // @NU
			$new = sanitize_user( $value, true );
		elseif ( !empty( $instance["icon{$key}"] ) && $instance["icon{$key}"] == 'fa-envelope' )
			$new = ( is_email( $value ) ) ? sanitize_email( $value ) : '';
		else
			$new = esc_url_raw( $value );

		return $new;
	}
	return $value;
}
add_filter( 'hoot_admin_widget_sanitize_field', 'hootkit_sanitize_profile_url', 10, 3 );