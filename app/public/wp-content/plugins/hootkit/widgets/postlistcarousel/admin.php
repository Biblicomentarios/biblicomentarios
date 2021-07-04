<?php
/**
 * Slider (PostListCarousel) Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Slider_Postlistcarousel_Widget
 */
class HootKit_Slider_Postlistcarousel_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-slider-postlistcarousel';
		$settings['name'] = hootkit()->get_string('postlistcarousel');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Posts List in a vertical Carousel', 'hootkit' ),
			// 'classname'		=> 'hoot-slider-postlistcarousel-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
				'name'		=> __( 'Carousel Style', 'hootkit' ),
				'type'		=> 'images',
				'std'		=> 'style1',
				'options'	=> array(
					'style1'	=> hootkit()->uri . 'assets/images/postlistcarousel-style-1.png',
					'style2'	=> hootkit()->uri . 'assets/images/postlistcarousel-style-2.png',
				),
			),
			'unitheight' => array(
				'name'		=> __( 'Custom Image Height', 'hootkit' ),
				'desc'		=> __( 'Default: 80 for Style1 / 215 for Style2', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'items' => array(
				'name'		=> __( 'Carousel Items', 'hootkit' ),
				'desc'		=> __( 'Number of items visible in carousel. Default: 3', 'hootkit' ),
				'type'		=> 'text',
				'std'		=> 4,
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'count' => array(
				'name'		=> __( 'Number of Posts', 'hootkit' ),
				'desc'		=> ( ( hootkit()->get_config( 'nohoot' ) ) ? __( '<strong>Only 4 posts allowed. Please use a wpHoot theme to add more posts.</strong>', 'hootkit' ) : __( '<strong>Only 4 posts available in the Free version of the theme.</strong>', 'hootkit' ) ),
				'type'		=> 'smallselect',
				'std'		=> '4',
				'options'	=> array(
					'1' => __( '1', 'hootkit' ),
					'2' => __( '2', 'hootkit' ),
					'3' => __( '3', 'hootkit' ),
					'4' => __( '4', 'hootkit' ),
				),
			),
			'offset' => array(
				'name'		=> __( 'Offset', 'hootkit' ),
				'desc'		=> __( 'Number of posts to skip from the start. Leave empty to start from the latest post.', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'category' => array(
				'name'		=> __( 'Category (Optional)', 'hootkit' ),
				'desc'		=> __( 'Only include posts from these categories. Leave empty to display posts from all categories.', 'hootkit' ),
				'type'		=> 'multiselect',
				'options'	=> (array)Hoot_List::categories(0),
			),
			'exccategory' => array(
				'name'		=> __( 'Exclude Category (Optional)', 'hootkit' ),
				'desc'		=> __( 'Exclude posts from these categories.', 'hootkit' ),
				'type'		=> 'multiselect',
				'options'	=> (array)Hoot_List::categories(0),
			),
			'viewall' => array(
				'name'		=> __( "'View All Posts' link", 'hootkit' ),
				'desc'		=> __( 'Links to your Blog page. If you have a Category selected below, then this will link to the Category Archive page.', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'none',
				'options'	=> array(
					'none'		=> __( 'Do not display', 'hootkit' ),
					'top'		=> __( 'Show at Top', 'hootkit' ),
					'bottom'	=> __( 'Show at Bottom', 'hootkit' ),
				),
			),
			'nav' => array(
				'name'		=> __( 'Navigation', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'arrows',
				'options'	=> array(
					'arrows'  => __( 'Display Arrows', 'hootkit' ),
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
			'seppost' => array(
				'name'		=> __( 'Individual Posts:', 'hootkit' ),
				// 'desc'		=> __( 'INDIVIDUAL POSTS', 'hootkit' ),
				'type'		=> 'separator',
			),
			'show_author' => array(
				'name'		=> __( 'Show Author', 'hootkit' ),
				'type'		=> 'checkbox',
				'std'		=> 1,
			),
			'show_date' => array(
				'name'		=> __( 'Show Post Date', 'hootkit' ),
				'type'		=> 'checkbox',
				'std'		=> 1,
			),
			'show_comments' => array(
				'name'		=> __( 'Show number of comments', 'hootkit' ),
				'type'		=> 'checkbox',
			),
			'show_cats' => array(
				'name'		=> __( 'Show Categories', 'hootkit' ),
				'type'		=> 'checkbox',
			),
			'show_tags' => array(
				'name'		=> __( 'Show Tags', 'hootkit' ),
				'type'		=> 'checkbox',
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

		$settings = apply_filters( 'hootkit_slider_postlistcarousel_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$slider_template = hoot_get_widget( 'slider-verticalcarousel', false, 'post' );
		if ( empty( $slider_template ) ) $slider_template = hoot_get_widget( 'slider-postlistcarousel', false ); // Pre Theme v.2.9.0 compatibility
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'slider-postlistcarousel' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $slider_template ) && file_exists( $slider_template ) ) include ( hootkit()->dir . 'widgets/postlistcarousel/view-setup.php' );
	}

}

/**
 * Register Widget
 */
function hootkit_slider_postlistcarousel_widget_register(){
	register_widget( 'HootKit_Slider_Postlistcarousel_Widget' );
}
add_action( 'widgets_init', 'hootkit_slider_postlistcarousel_widget_register' );