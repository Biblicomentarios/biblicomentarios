<?php
/**
 * Cover Image Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Cover_Image_Widget
 */
class HootKit_Cover_Image_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-cover-image';
		$settings['name'] = hootkit()->get_string('cover-image');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Image with Text overlay', 'hootkit' ),
			// 'classname'		=> 'hoot-cover-image-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
			'height' => array(
				'name'		=> __( 'Image Height', 'hootkit' ),
				'desc'		=> __( 'Leave empty to display full sized non-cropped image', 'hootkit' ),
				'type'		=> 'text',
				'std'		=> '330',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'seppost' => array(
				'name'		=> __( 'OVERLAY TEXT:', 'hootkit' ),
				// 'desc'		=> __( 'Overlay Text:', 'hootkit' ),
				'type'		=> 'separator',
			),
			'image' => array(
				'name'		=> __( 'Image', 'hootkit' ),
				'type'		=> 'image',
			),
			'content_title' => array(
				'name'		=> __( 'Title', 'hootkit' ),
				'type'		=> 'text',
			),
			'content_subtitle' => array(
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
			'boxes' => array(
				'name'		=> __( 'Multiple Cover Images Slider', 'hootkit' ),
				'type'		=> 'group',
				'desc'		=> __( 'Adding more than 1 cover image to turn it into a slider', 'hootkit' ),
				'options'	=> array(
					'item_name'	=> __( 'Cover Image', 'hootkit' ),
					'dellimit'	=> true,
					'sortable'	=> true,
				),
				'fields'	=> array(
					'image' => array(
						'name'		=> __( 'Image', 'hootkit' ),
						'type'		=> 'image',
					),
					'content_title' => array(
						'name'		=> __( 'Title', 'hootkit' ),
						'type'		=> 'text',
					),
					'content_subtitle' => array(
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
			unset( $settings['form_options']['content-subtitle'] );
		}

		$settings = apply_filters( 'hootkit_cover_image_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'cover-image', false );
		// Use Hootkit template if theme does not have one
		$widget_template = ( $widget_template ) ? $widget_template : hootkit()->dir . 'widgets/cover-image/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'cover-image' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_cover_image_widget_register(){
	register_widget( 'HootKit_Cover_Image_Widget' );
}
add_action( 'widgets_init', 'hootkit_cover_image_widget_register' );