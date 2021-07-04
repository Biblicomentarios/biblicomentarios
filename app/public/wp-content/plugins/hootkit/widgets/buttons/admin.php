<?php
/**
 * Buttons Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Buttons_Widget
 */
class HootKit_Buttons_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-buttons';
		$settings['name'] = hootkit()->get_string('buttons');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Buttons', 'hootkit' ),
			// 'classname'		=> 'hoot-buttons-widget', // CSS class applied to frontend widget container via 'before_widget' arg
		);
		$settings['control_options'] = array();
		$settings['form_options'] = array(
			//'name' => can be empty or false to hide the name
			'align' => array(
				'name'		=> __( 'Alignment', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'center',
				'options'	=> array(
					'left'		=> __( 'Left', 'hootkit' ),
					'center'	=> __( 'Center', 'hootkit' ),
					'right'		=> __( 'Right', 'hootkit' ),
				),
			),
			'size' => array(
				'name'		=> __( 'Button Size', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'small',
				'options'	=> array(
					'small'		=> __( 'Small', 'hootkit' ),
					'medium'	=> __( 'Medium', 'hootkit' ),
					'large'		=> __( 'Large', 'hootkit' ),
				),
			),
			'target' => array(
				'name'		=> __( 'Open Links In New Window', 'hootkit' ),
				'type'		=> 'checkbox',
			),
			'buttons' => array(
				'name'		=> __( 'Buttons', 'hootkit' ),
				'type'		=> 'group',
				'options'	=> array(
					'item_name'	=> __( 'Button', 'hootkit' ),
					'maxlimit'	=> 4,
					'limitmsg'	=> ( ( hootkit()->get_config( 'nohoot' ) ) ? __( 'Only 4 buttons allowed. Please use a wpHoot theme to add more buttons.', 'hootkit' ) : __( 'Only 4 buttons available in the Free version of the theme.', 'hootkit' ) ),
					'sortable'	=> true,
				),
				'fields'	=> array(
					'text' => array(
						'name'		=> __( 'Button Text (required)', 'hootkit' ),
						'type'		=> 'text',
						'std'		=> __( 'Click Here', 'hootkit' ),
					),
					'url' => array(
						'name'		=> __( 'Button URL', 'hootkit' ),
						'type'		=> 'text',
						'sanitize'	=> 'url',
					),
					'preset' => array(
						'name'		=> __( 'Preset', 'hootkit' ),
						'type'		=> 'smallselect',
						// 'std'		=> 'accent',
						'options'	=> hootkit()->get_config( 'presets' ),
					),
					'fontcolor' => array(
						'name'		=> __( 'Text Color (optional)', 'hootkit' ),
						'desc'		=> __( 'Leave empty to use above preset colors.', 'hootkit' ),
						// 'std'		=> '#aa0000',
						'type'		=> 'color',
					),
					'background' => array(
						'name'		=> __( 'Background (optional)', 'hootkit' ),
						'desc'		=> __( 'Leave empty to use above preset colors.', 'hootkit' ),
						// 'std'		=> '#aa0000',
						'type'		=> 'color',
					),
				),
			),
			'content' => array(
				'name'		=> __( 'Content before Buttons (optional)', 'hootkit' ),
				'type'		=> 'textarea',
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

		$settings = apply_filters( 'hootkit_buttons_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'buttons', false );
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'buttons' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_buttons_register(){
	register_widget( 'HootKit_Buttons_Widget' );
}
add_action( 'widgets_init', 'hootkit_buttons_register' );