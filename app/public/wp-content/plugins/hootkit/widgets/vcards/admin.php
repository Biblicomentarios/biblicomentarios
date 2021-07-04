<?php
/**
 * Vcards Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Vcards_Widget
 */
class HootKit_Vcards_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-vcards';
		$settings['name'] = hootkit()->get_string('vcards');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display ID Cards for Testimonials, Teams etc.', 'hootkit' ),
			// 'classname'		=> 'hoot-vcards-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
			'columns' => array(
				'name'		=> __( 'Number Of Columns', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> '4',
				'options'	=> array(
					'1'	=> __( '1', 'hootkit' ),
					'2'	=> __( '2', 'hootkit' ),
					'3'	=> __( '3', 'hootkit' ),
					'4'	=> __( '4', 'hootkit' ),
					'5'	=> __( '5', 'hootkit' ),
				),
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
			'vcards' => array(
				'name'		=> __( 'Vcards', 'hootkit' ),
				'type'		=> 'group',
				'options'	=> array(
					'item_name'	=> __( 'Vcard', 'hootkit' ),
					'maxlimit'	=> 4,
					'limitmsg'	=> ( ( hootkit()->get_config( 'nohoot' ) ) ? __( 'Only 4 vcards allowed. Please use a wpHoot theme to add more vcards.', 'hootkit' ) : __( 'Only 4 vcards available in the Free version of the theme.', 'hootkit' ) ),
					'sortable'	=> true,
				),
				'fields'	=> array(
					'image' => array(
						'name'		=> __('Image', 'hootkit'),
						'type'		=> 'image',
					),
					'content' => array(
						'name'		=> __('Text', 'hootkit'),
						'type'		=> 'textarea',
					),
					'content_desc' => array(
						'name'		=> '<span style="font-size:12px;"><em>' . __('Use &lt;h4&gt; tag for headlines. Example', 'hootkit') . '</em></span>',
						'type'		=> '<br /><code style="font-size: 11px;">' . __( '&lt;h4&gt;John Doe&lt;/h4&gt;<br>&lt;cite&gt;Designation Subtext&lt;/cite&gt;<br>Some description about John..<br>&lt;a href="http://url.com"&gt;Website&lt;/a&gt;', 'hootkit' ) . '</code>',
					),
					'icon1' => array(
						'name'		=> __( 'Social Icon 1', 'hootkit' ),
						'type'		=> 'select',
						'options'	=> hoot_enum_social_profiles(),
					),
					'url1' => array(
						'name'		=> __( 'URL 1', 'hootkit' ),
						'type'		=> 'text',
						'sanitize'	=> 'vcard_link_sanitize_url',
					),
					'icon2' => array(
						'name'		=> __( 'Social Icon 2', 'hootkit' ),
						'type'		=> 'select',
						'options'	=> hoot_enum_social_profiles(),
					),
					'url2' => array(
						'name'		=> __( 'URL 2', 'hootkit' ),
						'type'		=> 'text',
						'sanitize'	=> 'vcard_link_sanitize_url',
					),
					'icon3' => array(
						'name'		=> __( 'Social Icon 3', 'hootkit' ),
						'type'		=> 'select',
						'options'	=> hoot_enum_social_profiles(),
					),
					'url3' => array(
						'name'		=> __( 'URL 3', 'hootkit' ),
						'type'		=> 'text',
						'sanitize'	=> 'vcard_link_sanitize_url',
					),
					'icon4' => array(
						'name'		=> __( 'Social Icon 4', 'hootkit' ),
						'type'		=> 'select',
						'options'	=> hoot_enum_social_profiles(),
					),
					'url4' => array(
						'name'		=> __( 'URL 4', 'hootkit' ),
						'type'		=> 'text',
						'sanitize'	=> 'vcard_link_sanitize_url',
					),
					'icon5' => array(
						'name'		=> __( 'Social Icon 5', 'hootkit' ),
						'type'		=> 'select',
						'options'	=> hoot_enum_social_profiles(),
					),
					'url5' => array(
						'name'		=> __( 'URL 5', 'hootkit' ),
						'type'		=> 'text',
						'sanitize'	=> 'vcard_link_sanitize_url',
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

		$settings = apply_filters( 'hootkit_vcards_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'vcards', false );
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'vcards' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_vcards_register(){
	register_widget( 'HootKit_Vcards_Widget' );
}
add_action( 'widgets_init', 'hootkit_vcards_register' );

/**
 * Custom Sanitization Function
 * @param string $value    Field Value
 * @param string $name     Custom sanitization ID
 * @param array  $instance Widget instance (values)
 * @return string
 */
function hootkit_sanitize_vcard_url( $value, $name, $instance ){
	if ( $name == 'vcard_link_sanitize_url' ) {
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
add_filter( 'hoot_admin_widget_sanitize_field', 'hootkit_sanitize_vcard_url', 10, 3 );