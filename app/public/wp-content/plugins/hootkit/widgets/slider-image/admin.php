<?php
/**
 * Slider (Image) Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Slider_Image_Widget
 */
class HootKit_Slider_Image_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-slider-image';
		$settings['name'] = hootkit()->get_string('slider-image');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Image Slider', 'hootkit' ),
			// 'classname'		=> 'hoot-slider-image-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
				'name'		=> __( 'Slider Style', 'hootkit' ),
				'type'		=> 'images',
				'std'		=> 'style1',
				'options'	=> array(
					'style1'	=> hootkit()->uri . 'assets/images/slider-style-1.png',
					'style2'	=> hootkit()->uri . 'assets/images/slider-style-2.png',
				),
			),
			'nav' => array(
				'name'		=> __( 'Navigation', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'both',
				'options'	=> array(
					'both'    => __( 'Arrows + Bullets', 'hootkit' ),
					'arrows'  => __( 'Arrows', 'hootkit' ),
					'bullets' => __( 'Bullets', 'hootkit' ),
					'none'    => __( 'None', 'hootkit' ),
				),
			),
			'pause' => array(
				'name'		=> __( 'Pause Time (1-15)', 'hootkit' ),
				'desc'		=> __( 'Seconds to pause on each slide.', 'hootkit' ),
				'type'		=> 'text',
				'std'		=> 5,
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'width' => array(
				'name'		=> __( 'Maximum Slider Width (Optional)', 'hootkit' ),
				'desc'		=> __( '<strong>(in pixels)</strong> By default the slider takes up the entire width available and the height is adjusted accordingly.', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'slides' => array(
				'name'		=> __( 'Slides', 'hootkit' ),
				'type'		=> 'group',
				'options'	=> array(
					'item_name'	=> __( 'Slide', 'hootkit' ),
					'maxlimit'	=> 4,
					'limitmsg'	=> ( ( hootkit()->get_config( 'nohoot' ) ) ? __( 'Only 4 slides allowed. Please use a wpHoot theme to add more slides.', 'hootkit' ) : __( 'Only 4 slides available in the Free version of the theme.', 'hootkit' ) ),
					'sortable'	=> true,
				),
				'fields'	=> array(
					'image' => array(
						'name'		=> __( 'Slide Image', 'hootkit' ),
						'type'		=> 'image',
					),
					'title' => array(
						'name'		=> __( 'Title', 'hootkit' ),
						'type'		=> 'text',
					),
					'caption' => array(
						'name'		=> __( 'Description', 'hootkit' ),
						'type'		=> 'textarea',
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
					'button' => array(
						'name'		=> __( 'Button Text (optional)', 'hootkit' ),
						'desc'		=> __( 'Leaving Button Text empty will link the whole image.', 'hootkit' ),
						'type'		=> 'text',
						'std'		=> __( 'Know More', 'hootkit' ),
					),
					'url' => array(
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

		if ( !in_array( 'slider-styles', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['style'] );
		}

		if ( !in_array( 'widget-subtitle', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['subtitle'] );
			unset( $settings['form_options']['slides']['fields']['subtitle'] );
		}

		$settings = apply_filters( 'hootkit_slider_image_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$slider_template = hoot_get_widget( 'slider-image', false, 'content' );
		// Use Hootkit template if theme does not have one
		$slider_template = ( $slider_template ) ? $slider_template : hootkit()->dir . 'widgets/slider/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'slider-image' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $slider_template ) && file_exists( $slider_template ) ) include ( hootkit()->dir . 'widgets/slider-image/view-setup.php' );
	}

}

/**
 * Register Widget
 */
function hootkit_slider_image_widget_register(){
	register_widget( 'HootKit_Slider_Image_Widget' );
}
add_action( 'widgets_init', 'hootkit_slider_image_widget_register' );