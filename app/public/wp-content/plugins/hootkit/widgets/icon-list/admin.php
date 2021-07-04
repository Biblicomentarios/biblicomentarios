<?php
/**
 * Icon List Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Icon_List_Widget
 */
class HootKit_Icon_List_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-icon-list';
		$settings['name'] = hootkit()->get_string('icon-list');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Icon List', 'hootkit' ),
			// 'classname'		=> 'hoot-icon-list-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
			'icon' => array(
				'name'		=> __( 'Icon', 'hootkit' ),
				'type'		=> 'icon',
			),
			'iconsize' => array(
				'name'		=> __( 'Icon Size', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'small',
				'options'	=> array(
					'small'		=> __( 'Small', 'hootkit' ),
					'medium'	=> __( 'Medium', 'hootkit' ),
					'large'		=> __( 'Large', 'hootkit' ),
					'huge'		=> __( 'Huge', 'hootkit' ),
				),
			),
			'iconcolor' => array(
				'name'		=> __( 'Icon Color (optional)', 'hootkit' ),
				'desc'		=> __( 'Leave empty to use default text color.', 'hootkit' ),
				// 'std'		=> '#aa0000',
				'type'		=> 'color',
			),
			'items' => array(
				'name'		=> __( 'List', 'hootkit' ),
				'type'		=> 'group',
				'options'	=> array(
					'item_name'	=> __( 'List Item', 'hootkit' ),
					'maxlimit'	=> 4,
					'limitmsg'	=> ( ( hootkit()->get_config( 'nohoot' ) ) ? __( 'Only 4 items allowed. Please use a wpHoot theme to add more items.', 'hootkit' ) : __( 'Only 4 items available in the Free version of the theme.', 'hootkit' ) ),
					'sortable'	=> true,
				),
				'fields'	=> array(
					'text' => array(
						'name'		=> __( 'Text', 'hootkit' ),
						'type'		=> 'textarea',
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

		$settings = apply_filters( 'hootkit_icon_list_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'icon-list', false );
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'icon-list' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_icon_list_register(){
	register_widget( 'HootKit_Icon_List_Widget' );
}
add_action( 'widgets_init', 'hootkit_icon_list_register' );