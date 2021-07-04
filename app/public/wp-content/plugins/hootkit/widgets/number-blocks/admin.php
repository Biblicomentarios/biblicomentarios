<?php
/**
 * Number Blocks Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Number_Blocks_Widget
 */
class HootKit_Number_Blocks_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-number-blocks';
		$settings['name'] = hootkit()->get_string('number-blocks');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Styled Number Blocks', 'hootkit' ),
			// 'classname'		=> 'hoot-number-blocks-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
			'size' => array(
				'name'		=> __( 'Circle Size', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'small',
				'options'	=> array(
					'tiny'		=> __( 'Tiny', 'hootkit' ),
					'small'		=> __( 'Small', 'hootkit' ),
					'medium'	=> __( 'Medium', 'hootkit' ),
					'large'		=> __( 'Large', 'hootkit' ),
					'huge'		=> __( 'Huge', 'hootkit' ),
				),
			),
			'width' => array(
				'name'		=> __( 'Circle Width', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'thin',
				'options'	=> array(
					'thin'		=> __( 'Thin', 'hootkit' ),
					'medium'	=> __( 'Medium', 'hootkit' ),
					'heavy'		=> __( 'Heavy', 'hootkit' ),
				),
			),
			'boxes' => array(
				'name'		=> __( 'Number Boxes', 'hootkit' ),
				'type'		=> 'group',
				'options'	=> array(
					'item_name'	=> __( 'Number Box', 'hootkit' ),
					'maxlimit'	=> 4,
					'limitmsg'	=> ( ( hootkit()->get_config( 'nohoot' ) ) ? __( 'Only 4 boxes allowed. Please use a wpHoot theme to add more boxes.', 'hootkit' ) : __( 'Only 4 boxes available in the Free version of the theme.', 'hootkit' ) ),
					'sortable'	=> true,
				),
				'fields'	=> array(
					'percent' => array(
						'name'		=> __( 'Circle percentage (Required)', 'hootkit' ),
						'desc'		=> __( 'A number between 0-100 used to calculate the circle length around the number. Note: You can use a shortcode in this field as well - the shortcode should result in a number.', 'hootkit' ),
						'type'		=> 'text',
						'std'		=> '75',
						'settings'	=> array( 'size' => 3, ),
						// 'sanitize'	=> 'absint', // allow shortcodes
					),
					'number' => array(
						'name'		=> __( 'Display Number (Optional)', 'hootkit' ),
						'desc'		=> __( 'Leave empty to use above percentage (a % sign will be automatically added). Note: You can use a shortcode in this field as well - the shortcode should result in a number.', 'hootkit' ),
						'type'		=> 'text',
						// 'std'		=> '75', // Having a default value creates a bug when user intentionally leaves the field blank
						'settings'	=> array( 'size' => 3, ),
						// 'sanitize'	=> 'absint', // allow shortcodes
					),
					'displayprefix' => array(
						'name'		=> __( 'Prefix', 'hootkit' ),
						'desc'		=> __( "This will only work if you have a 'Display Number' above", 'hootkit' ),
						'type'		=> 'text',
						'settings'	=> array( 'size' => 3, ),
					),
					'displaysuffix' => array(
						'name'		=> __( 'Suffix', 'hootkit' ),
						'desc'		=> __( "This will only work if you have a 'Display Number' above", 'hootkit' ),
						'type'		=> 'text',
						'settings'	=> array( 'size' => 3, ),
					),
					'color' => array(
						'name'		=> __( 'Color', 'hootkit' ),
						'type'		=> 'color',
						'std'		=> '#e7ac44',
					),
					'content' => array(
						'name'		=> __( 'Text', 'hootkit' ),
						'type'		=> 'textarea',
					),
					'content_desc' => array(
						'name'		=> '<span style="font-size:12px;"><em>' . __('Use &lt;h4&gt; tag for headlines. Example', 'hootkit') . '</em></span>',
						'type'		=> '<br><code style="font-size: 11px;">' . __( '&lt;h4&gt;Skill/Feature Title&lt;/h4&gt;<br>Some description about this feature..<br>&lt;a href="http://example.com"&gt;Link Text&lt;/a&gt;', 'hootkit' ) . '</code>',
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

		$settings = apply_filters( 'hootkit_number_blocks_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'number-blocks', false );
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'number-blocks' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_number_blocks_register(){
	register_widget( 'HootKit_Number_Blocks_Widget' );
}
add_action( 'widgets_init', 'hootkit_number_blocks_register' );