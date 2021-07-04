<?php
/**
 * Content Blocks Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Content_Blocks_Widget
 */
class HootKit_Content_Blocks_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-content-blocks';
		$settings['name'] = hootkit()->get_string('content-blocks');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Styled Content Blocks', 'hootkit' ),
			// 'classname'		=> 'hoot-content-blocks-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
			'style' => array(
				'name'		=> __( 'Blocks Style', 'hootkit' ),
				'type'		=> 'images',
				'std'		=> 'style1',
				'options'	=> array(
					'style1'	=> hootkit()->uri . 'assets/images/content-block-style-1.png',
					'style2'	=> hootkit()->uri . 'assets/images/content-block-style-2.png',
					'style3'	=> hootkit()->uri . 'assets/images/content-block-style-3.png',
					'style4'	=> hootkit()->uri . 'assets/images/content-block-style-4.png',
					'style5'	=> hootkit()->uri . 'assets/images/content-block-style-5.png',
					'style6'	=> hootkit()->uri . 'assets/images/content-block-style-6.png',
				),
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
			'boxes' => array(
				'name'		=> __( 'Content Boxes', 'hootkit' ),
				'type'		=> 'group',
				'options'	=> array(
					'item_name'	=> __( 'Content Box', 'hootkit' ),
					'dellimit'	=> true,
					'sortable'	=> true,
				),
				'fields'	=> array(
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
					'image' => array(
						'name'		=> __( 'Image', 'hootkit' ),
						'desc'		=> __( 'Remove any icon below to use image', 'hootkit' ),
						'type'		=> 'image',
					),
					'icon' => array(
						'name'		=> __( 'Icon', 'hootkit' ),
						'desc'		=> __( 'Use an icon instead of image', 'hootkit' ),
						'type'		=> 'icon',
					),
					'icon_style' => array(
						'name'		=> __( 'Icon Style', 'hootkit' ),
						'type'		=> 'smallselect',
						'std'		=> 'circle',
						'options'	=> array(
							'none'		=> __( 'None', 'hootkit' ),
							'circle'	=> __( 'Circle', 'hootkit' ),
							'square'	=> __( 'Square', 'hootkit' ),
						),
					),
					'link' => array(
						'name'		=> __( 'Link Text (optional)', 'hootkit' ),
						'type'		=> 'text',
						'std'		=> __( 'Know More', 'hootkit' ),
					),
					'url'=> array(
						'name'		=> __( 'Link URL (optional)', 'hootkit' ),
						'type'		=> 'text',
						'sanitize'	=> 'url',
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

		if ( !in_array( 'content-blocks-style5', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['style']['options']['style5'] );
		}
		if ( !in_array( 'content-blocks-style6', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['style']['options']['style6'] );
		}
		if ( !in_array( 'widget-subtitle', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['subtitle'] );
		}

		$settings = apply_filters( 'hootkit_content_blocks_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'content-blocks', false );
		// Use Hootkit template if theme does not have one
		$widget_template = ( $widget_template ) ? $widget_template : hootkit()->dir . 'widgets/content-blocks/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'content-blocks' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_content_blocks_widget_register(){
	register_widget( 'HootKit_Content_Blocks_Widget' );
}
add_action( 'widgets_init', 'hootkit_content_blocks_widget_register' );