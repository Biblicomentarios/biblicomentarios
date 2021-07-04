<?php
/**
 * Products Search Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Products_Search_Widget
 */
class HootKit_Products_Search_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-products-search';
		$settings['name'] = hootkit()->get_string('products-search');
		$settings['widget_options'] = array(
			'description'	=> __( 'Woocommerce Products Search', 'hootkit' ),
			// 'classname'		=> 'hoot-products-search-widget', // CSS class applied to frontend widget container via 'before_widget' arg
		);
		$settings['control_options'] = array();
		$settings['form_options'] = array(
			//'name' => can be empty or false to hide the name
			'title' => array(
				'name'		=> __( 'Title (optional)', 'hootkit' ),
				'type'		=> 'text',
			),
			'placeholder' => array(
				'name'		=> __( 'Placeholder Text', 'hootkit' ),
				'type'		=> 'text',
				'std'		=> __( 'Search Products', 'hootkit' ),
			),
			'show_cats' => array(
				'name'		=> __( 'Display Category Selector', 'hootkit' ),
				'type'		=> 'checkbox',
				'std'		=> '1',
			),
			// 'category' => array(
			// 	'name'		=> __( 'Products Category (Optional)', 'hootkit' ),
			// 	'desc'		=> __( 'Only include search results from these categories. Category Selector would not be displayed if you pre-select categories here.', 'hootkit' ),
			// 	'type'		=> 'multiselect',
			// 	'optionsfn'	=> 'hoot_list_products_category',
			// ),
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

		$settings = apply_filters( 'hootkit_products_search_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'products-search', false );
		// Use Hootkit template if theme does not have one
		$widget_template = ( $widget_template ) ? $widget_template : hootkit()->dir . 'widgets/products-search/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'products-search' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_products_search_widget_register(){
	register_widget( 'HootKit_Products_Search_Widget' );
}
add_action( 'widgets_init', 'hootkit_products_search_widget_register' );