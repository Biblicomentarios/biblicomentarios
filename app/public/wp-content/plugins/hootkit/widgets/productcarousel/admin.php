<?php
/**
 * Slider (ProductCarousel) Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Slider_Productcarousel_Widget
 */
class HootKit_Slider_Productcarousel_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-slider-productcarousel';
		$settings['name'] = hootkit()->get_string('productcarousel');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Product in a Carousel', 'hootkit' ),
			// 'classname'		=> 'hoot-slider-productscarousel-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
			'items' => array(
				'name'		=> __( 'Carousel Items', 'hootkit' ),
				'desc'		=> __( 'Number of items visible in carousel.', 'hootkit' ),
				'type'		=> 'text',
				'std'		=> 4,
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'count' => array(
				'name'		=> __( 'Number of Products', 'hootkit' ),
				'desc'		=> ( ( hootkit()->get_config( 'nohoot' ) ) ? __( '<strong>Only 4 Products allowed. Please use a wpHoot theme to add more Products.</strong>', 'hootkit' ) : __( '<strong>Only 4 Products available in the Free version of the theme.</strong>', 'hootkit' ) ),
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
				'desc'		=> __( 'Number of products to skip from the start. Leave empty to start from the latest products.', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'viewall' => array(
				'name'		=> __( "'View All Products' link", 'hootkit' ),
				'desc'		=> __( 'Links to your Shop page. If you have a Category selected below, then this will link to the Products Category page.', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'none',
				'options'	=> array(
					'none'		=> __( 'Do not display', 'hootkit' ),
					'top'		=> __( 'Show at Top', 'hootkit' ),
					'bottom'	=> __( 'Show at Bottom', 'hootkit' ),
				),
			),
			'category' => array(
				'name'		=> __( 'Category (Optional)', 'hootkit' ),
				'desc'		=> __( 'Only include products from these categories. Leave empty to display products from all categories.', 'hootkit' ),
				'type'		=> 'multiselect',
				'optionsfn'	=> 'hoot_list_products_category',
			),
			'exccategory' => array(
				'name'		=> __( 'Exclude Category (Optional)', 'hootkit' ),
				'desc'		=> __( 'Exclude products from these categories.', 'hootkit' ),
				'type'		=> 'multiselect',
				'optionsfn'	=> 'hoot_list_products_category',
			),
			'imagesize' => array(
				'name'		=> __( 'Image Size', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'vertical',
				'options'	=> array(
					'rectangle' => __( 'Rectangle (cropped)', 'hootkit' ),
					'vertical'  => __( 'Vertical (cropped)', 'hootkit' ),
					'full'      => __( 'Full (no cropping)', 'hootkit' ),
				),
			),
			'show_rating' => array(
				'name'		=> __( 'Show Star Rating', 'hootkit' ),
				'type'		=> 'checkbox',
				'std'		=> '1',
			),
			'show_price' => array(
				'name'		=> __( 'Show Price', 'hootkit' ),
				'type'		=> 'checkbox',
				'std'		=> '1',
			),
			'show_addtocart' => array(
				'name'		=> __( "Show 'Add to Cart'", 'hootkit' ),
				'type'		=> 'checkbox',
				'std'		=> '1',
			),
			'show_cats' => array(
				'name'		=> __( 'Show Categories', 'hootkit' ),
				'type'		=> 'checkbox',
			),
			'show_tags' => array(
				'name'		=> __( 'Show Tags', 'hootkit' ),
				'type'		=> 'checkbox',
			),
			'fullcontent' => array(
				'name'		=> __( 'Content', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> 'excerpt',
				'options'	=> array(
					'desc'		=> __( 'Display Short Description', 'hootkit' ),
					'excerpt'	=> __( 'Display Excerpt', 'hootkit' ),
					// 'content'	=> __( 'Display Full Content', 'hootkit' ),
					'none'		=> __( 'None', 'hootkit' ),
				),
			),
			'excerptlength' => array(
				'name'		=> __( 'Custom Excerpt Length', 'hootkit' ),
				'desc'		=> __( 'Select <strong>\'Display Excerpt\'</strong> in option above. Leave empty for default excerpt length.', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
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

		$settings = apply_filters( 'hootkit_slider_productcarousel_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$slider_template = hoot_get_widget( 'slider-carousel', false, 'product' );
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'slider-productcarousel' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $slider_template ) && file_exists( $slider_template ) ) include ( hootkit()->dir . 'widgets/productcarousel/view-setup.php' );
	}

}

/**
 * Register Widget
 */
function hootkit_slider_productcarousel_widget_register(){
	register_widget( 'HootKit_Slider_Productcarousel_Widget' );
}
add_action( 'widgets_init', 'hootkit_slider_productcarousel_widget_register' );