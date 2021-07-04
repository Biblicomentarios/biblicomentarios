<?php
/**
 * Content Grid Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Content_Grid_Widget
 */
class HootKit_Content_Grid_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-content-grid';
		$settings['name'] = hootkit()->get_string('content-grid');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Content in a Grid', 'hootkit' ),
			// 'classname'		=> 'hoot-content-grid-widget', // CSS class applied to frontend widget container via 'before_widget' arg
		);
		$settings['control_options'] = array();
		$settings['form_options'] = array(
			//'name' => can be empty or false to hide the name
			'title' => array(
				'name'		=> __( 'Title (Optional)', 'hootkit' ),
				'type'		=> 'text',
			),
			'subtitle' => array(
				'name'		=> __( 'Sub Title (optional)', 'hootkit' ),
				'type'		=> 'text',
			),
			'columns' => array(
				'name'		=> __( 'Number Of Columns', 'hootkit' ),
				'desc'		=> __( "First grid takes up 2 columns by default. <br/> (You can change it to standard 1x1 size in 'First Grid' options below)", 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> '4',
				'options'	=> array(
					// '1'	=> __( '1', 'hootkit' ),
					'2'	=> __( '2', 'hootkit' ),
					'3'	=> __( '3', 'hootkit' ),
					'4'	=> __( '4', 'hootkit' ),
					'5'	=> __( '5', 'hootkit' ),
				),
			),
			'unitheight' => array(
				'name'		=> __( 'Grid Unit (Image) Size', 'hootkit' ),
				'desc'		=> __( 'Default: 215 (in pixels)', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'boxes' => array(
				'name'		=> __( 'Grid Boxes', 'hootkit' ),
				'type'		=> 'group',
				'options'	=> array(
					'item_name'	=> __( 'Grid Box', 'hootkit' ),
					'dellimit'	=> true,
					'sortable'	=> true,
				),
				'fields'	=> array(
					'image' => array(
						'name'		=> __( 'Image', 'hootkit' ),
						'type'		=> 'image',
					),
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
						/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
						'desc'		=> sprintf( __( 'Use &lt;big&gt; tag for big font size. Example:%3$s%1$s&lt;big&gt;Huge Words&lt;/big&gt;%2$s', 'hootkit' ), '<code>', '</code>', '<br>' ),
						'type'		=> 'textarea',
					),
					'url'=> array(
						'name'		=> __( 'Link URL (optional)', 'hootkit' ),
						'type'		=> 'text',
						'sanitize'	=> 'url',
					),
					'caption_bg' => array(
						'name'		=> __( 'Text Styling', 'hootkit' ),
						'type'		=> 'select',
						'std'		=> 'light-on-dark',
						'options'	=> array(
							'dark'			=> __( 'Dark Font', 'hootkit' ),
							'light'			=> __( 'Light Font', 'hootkit' ),
							'dark-on-light'	=> __( 'Dark Font / Light Background', 'hootkit' ),
							'light-on-dark'	=> __( 'Light Font / Dark Background', 'hootkit' ),
						),
					),
					'caption_align' => array(
						'name'		=> __( 'Text Alignment', 'hootkit' ),
						'type'		=> 'smallselect',
						'std'		=> 'bottom-center',
						'options'	=> array(
							'top-left'		=> __( 'Top Left', 'hootkit' ),
							'top-center'	=> __( 'Top Center', 'hootkit' ),
							'top-right'		=> __( 'Top Right', 'hootkit' ),
							'middle-left'	=> __( 'Middle Left', 'hootkit' ),
							'middle-center'	=> __( 'Middle Center', 'hootkit' ),
							'middle-right'	=> __( 'Middle Right', 'hootkit' ),
							'bottom-left'	=> __( 'Bottom Left', 'hootkit' ),
							'bottom-center'	=> __( 'Bottom Center', 'hootkit' ),
							'bottom-right'	=> __( 'Bottom Right', 'hootkit' ),
						),
					),
					'caption_align_dist'=> array(
						'name'		=> __( 'Text Distance from Edges', 'hootkit' ),
						'type'		=> 'smallselect',
						// 'sanitize'	=> 'percent',
						// 'settings'	=> array( 'size' => 3 ),
						'desc'		=> __( '(percentage distance from the edge)', 'hootkit' ), // <br><strong>Default: 2% (Recommended: between 0-5)</strong>
						'options'	=> array(
							'0'	=> __( '0', 'hootkit' ),
							'1'	=> __( '1', 'hootkit' ),
							'2'	=> __( '2', 'hootkit' ),
							'3'	=> __( '3', 'hootkit' ),
							'4'	=> __( '4', 'hootkit' ),
							'5'	=> __( '5', 'hootkit' ),
							'6'	=> __( '6', 'hootkit' ),
							'7'	=> __( '7', 'hootkit' ),
							'8'	=> __( '8', 'hootkit' ),
							'9'	=> __( '9', 'hootkit' ),
							'10'=> __( '10', 'hootkit' ),
							'11'=> __( '11', 'hootkit' ),
							'12'=> __( '12', 'hootkit' ),
							'13'=> __( '13', 'hootkit' ),
							'14'=> __( '14', 'hootkit' ),
							'15'=> __( '15', 'hootkit' ),
						),
					),
					'button1' => array(
						'name'		=> sprintf( __( 'Button %1$s Text', 'hootkit' ), '1' ),
						'type'		=> 'text',
						'settings'	=> array( 'size' => 16 ),
					),
					'buttonurl1'=> array(
						'name'		=> sprintf( __( 'Button %1$s URL', 'hootkit' ), '1' ),
						'type'		=> 'text',
						'sanitize'	=> 'url',
						'settings'	=> array( 'size' => 16 ),
					),
					'buttoncolor1' => array(
						'name'		=> sprintf( __( 'Button %1$s Color', 'hootkit' ), '1' ),
						'type'		=> 'color',
					),
					'buttonfont1' => array(
						'name'		=> sprintf( __( 'Button %1$s Text Color', 'hootkit' ), '1' ),
						'type'		=> 'color',
					),
					'button2' => array(
						'name'		=> sprintf( __( 'Button %1$s Text', 'hootkit' ), '2' ),
						'type'		=> 'text',
						'settings'	=> array( 'size' => 16 ),
					),
					'buttonurl2'=> array(
						'name'		=> sprintf( __( 'Button %1$s URL', 'hootkit' ), '2' ),
						'type'		=> 'text',
						'sanitize'	=> 'url',
						'settings'	=> array( 'size' => 16 ),
					),
					'buttoncolor2' => array(
						'name'		=> sprintf( __( 'Button %1$s Color', 'hootkit' ), '2' ),
						'type'		=> 'color',
					),
					'buttonfont2' => array(
						'name'		=> sprintf( __( 'Button %1$s Text Color', 'hootkit' ), '2' ),
						'type'		=> 'color',
					),
				),
			),
			'firstgrid' => array(
				'name'		=> __( 'First Grid', 'hootkit' ),
				'type'		=> 'collapse',
				'settings'	=> array( 'state' => 'open' ),
				'fields'	=> array(
					'standard' => array(
						'name'		=> __( 'Display as Standard 1x1 Size', 'hootkit' ),
						'desc'		=> __( 'By default, first grid is double in size and takes up space of 2 Columns x 2 Rows', 'hootkit' ),
						'type'		=> 'checkbox',
					),
					'count' => array(
						'name'		=> __( 'Number of Boxes', 'hootkit' ),
						'desc'		=> __( 'Selecting more than 1 box will <strong>convert the first grid into a SLIDER</strong>', 'hootkit' ),
						'type'		=> 'smallselect',
						'std'		=> '1',
						'options'	=> array(
							'1'	=> __( '1', 'hootkit' ),
							'2'	=> __( '2', 'hootkit' ),
							'3'	=> __( '3', 'hootkit' ),
							'4'	=> __( '4', 'hootkit' ),
							'5'	=> __( '5', 'hootkit' ),
							'6'	=> __( '6', 'hootkit' ),
							'7'	=> __( '7', 'hootkit' ),
							'8'	=> __( '8', 'hootkit' ),
							'9'	=> __( '9', 'hootkit' ),
							'10'=> __( '10', 'hootkit' ),
						),
					),
					'fix' => array(
						'type'		=> '<input type="hidden" name="%name%" id="%id%" value="na" class="%class%">',
						// Bugfix: This field is added since all the fields in collapsible are checkboxes. So when all checkbox are unchecked, value for "widget-hoot-content-grid-widget[N][firstgrid]" in the instance is returned as false by the browsers instead of an array with all emements = 0 (empty string value is ok, but we still add a dummy value)
					),
				),
			),
			'sepcss' => array(
				'type'		=> 'separator',
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

		$settings = apply_filters( 'hootkit_content_grid_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'content-grid', false );
		// Use Hootkit template if theme does not have one
		$widget_template = ( $widget_template ) ? $widget_template : hootkit()->dir . 'widgets/content-grid/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'content-grid' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_content_grid_widget_register(){
	register_widget( 'HootKit_Content_Grid_Widget' );
}
add_action( 'widgets_init', 'hootkit_content_grid_widget_register' );