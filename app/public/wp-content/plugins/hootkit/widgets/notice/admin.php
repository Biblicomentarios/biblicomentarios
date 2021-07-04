<?php
/**
 * Notice Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Notice_Widget
 */
class HootKit_Notice_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-notice';
		$settings['name'] = hootkit()->get_string('notice');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Notice Box', 'hootkit' ),
			// 'classname'		=> 'hoot-notice-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
			'content' => array(
				'name'		=> __( 'Content', 'hootkit' ),
				'type'		=> 'textarea',
			),
			'preset' => array(
				'name'		=> __( 'Preset', 'hootkit' ),
				'type'		=> 'smallselect',
				// 'std'		=> 'accent',
				'options'	=> array_merge( array(
					'success' => __( 'Notice (Success)', 'hootkit' ),
					'warning' => __( 'Notice (Warning)', 'hootkit' ),
					'error'   => __( 'Notice (Error)',   'hootkit' ),
					'info'    => __( 'Notice (Info)',    'hootkit' ),
					'note'    => __( 'Notice (Note)',    'hootkit' ),
					'flag'    => __( 'Notice (Flag)',    'hootkit' ),
					'pushpin' => __( 'Notice (Pushpin)', 'hootkit' ),
					'setting' => __( 'Notice (Setting)', 'hootkit' ),
				), hootkit()->get_config( 'presetcombo' ) ),
			),
			'icon' => array(
				'name'		=> __( 'Icon', 'hootkit' ),
				'desc'		=> __( 'Leave empty to use above preset icon.', 'hootkit' ),
				'type'		=> 'icon',
			),
			'iconsize' => array(
				'name'		=> __( 'Icon Size', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'medium',
				'options'	=> array(
					'small'		=> __( 'Small', 'hootkit' ),
					'medium'	=> __( 'Medium', 'hootkit' ),
					'large'		=> __( 'Large', 'hootkit' ),
					'huge'		=> __( 'Huge', 'hootkit' ),
				),
			),
			'iconcolor' => array(
				'name'		=> __( 'Icon Color (optional)', 'hootkit' ),
				'desc'		=> __( 'Leave empty to use above preset colors.', 'hootkit' ),
				// 'std'		=> '#aa0000',
				'type'		=> 'color',
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

		$settings = apply_filters( 'hootkit_notice_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'notice', false );
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'notice' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_notice_register(){
	register_widget( 'HootKit_Notice_Widget' );
}
add_action( 'widgets_init', 'hootkit_notice_register' );