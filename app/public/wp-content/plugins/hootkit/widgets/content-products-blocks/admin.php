<?php
/**
 * Content Products Blocks Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Content_Products_Blocks_Widget
 */
class HootKit_Content_Products_Blocks_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-products-blocks';
		$settings['name'] = hootkit()->get_string('content-products-blocks');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Styled Content Blocks (Products)', 'hootkit' ),
			// 'classname'		=> 'hoot-products-blocks-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
					// 'style3'	=> hootkit()->uri . 'assets/images/content-block-style-3.png',
					'style4'	=> hootkit()->uri . 'assets/images/content-block-style-4.png',
					'style5'	=> hootkit()->uri . 'assets/images/content-block-style-5.png',
					'style6'	=> hootkit()->uri . 'assets/images/content-block-style-6.png',
				),
			),
			'viewall' => array(
				'name'		=> __( "'View All Products' link", 'hootkit' ),
				'desc'		=> __( 'Links to your Shop page. If you have a Category selected below, then this will link to the Product Category page.', 'hootkit' ),
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
			'columns' => array(
				'name'		=> __( 'Number Of Columns', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> '4',
				'options'	=> array(
					'1'	=> __( '1', 'hootkit' ),
					'2'	=> __( '2', 'hootkit' ),
					'3'	=> __( '3', 'hootkit' ),
					'4'	=> __( '4', 'hootkit' ),
					'5'	=> __( '5', 'hootkit' ),
				),
			),
			'count' => array(
				'name'		=> __( 'Number of Products to show', 'hootkit' ),
				'desc'		=> __( 'Default: 4', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'offset' => array(
				'name'		=> __( 'Offset', 'hootkit' ),
				'desc'		=> __( 'Number of products to skip from the start. Leave empty to start from the latest products.', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'show_rating' => array(
				'name'		=> __( 'Show Star Rating', 'hootkit' ),
				'type'		=> 'checkbox',
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
				'type'		=> 'select',
				'std'		=> 'none',
				'options'	=> array(
					'desc'		=> __( 'Display Short Description', 'hootkit' ),
					'excerpt'	=> __( 'Display Excerpt', 'hootkit' ),
					'content'	=> __( 'Display Full Content', 'hootkit' ),
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

		if ( !in_array( 'content-blocks-style5', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['style']['options']['style5'] );
		}
		if ( !in_array( 'content-blocks-style6', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['style']['options']['style6'] );
		}
		if ( !in_array( 'widget-subtitle', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['subtitle'] );
		}

		$settings = apply_filters( 'hootkit_content_products_blocks_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'content-products-blocks', false );
		// Use Hootkit template if theme does not have one
		$widget_template = ( $widget_template ) ? $widget_template : hootkit()->dir . 'widgets/content-products-blocks/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'content-products-blocks' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_content_products_blocks_widget_register(){
	register_widget( 'HootKit_Content_Products_Blocks_Widget' );
}
add_action( 'widgets_init', 'hootkit_content_products_blocks_widget_register' );