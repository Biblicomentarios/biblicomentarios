<?php
/**
 * Products Cart Icon Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Products_Carticon_Widget
 */
class HootKit_Products_Carticon_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-products-carticon';
		$settings['name'] = hootkit()->get_string('products-carticon');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Woocommerce Cart Icon', 'hootkit' ),
			// 'classname'		=> 'hoot-products-carticon-widget', // CSS class applied to frontend widget container via 'before_widget' arg
		);
		$settings['control_options'] = array();
		$settings['form_options'] = array(
			//'name' => can be empty or false to hide the name
			'title' => array(
				'name'		=> __( 'Title (optional)', 'hootkit' ),
				'type'		=> 'text',
				'std'		=> __( 'Products', 'hootkit' ),
			),
			'carticon' => array(
				'name'		=> __( 'Cart Icon', 'hootkit' ),
				'type'		=> 'icon',
				'options'	=> array( array(
											'fa-cart-arrow-down fas', 'fa-cart-plus fas', 'fa-shopping-cart fas', /*'fa-opencart fab',*/
											/*'fa-ship fas',*/ 'fa-shipping-fast fas', 'fa-shopping-bag fas', 'fa-shopping-basket fas',
										) ),
				'std'		=> 'fa-shopping-cart fas',
			),
			'show_items' => array(
				'name'		=> __( 'Show Number Of Items', 'hootkit' ),
				'type'		=> 'checkbox',
				'std'		=> '1',
			),
			'show_total' => array(
				'name'		=> __( 'Show Total Cart Value', 'hootkit' ),
				'type'		=> 'checkbox',
				'std'		=> '1',
			),
			'background' => array(
				'name'		=> __( 'Background (optional)', 'hootkit' ),
				'desc'		=> __( 'Leave empty for no background.', 'hootkit' ),
				'std'		=> '#000000',
				'type'		=> 'color',
			),
			'fontcolor' => array(
				'name'		=> __( 'Font Color (optional)', 'hootkit' ),
				'desc'		=> __( 'Leave empty to use default font colors.', 'hootkit' ),
				'std'		=> '#ffffff',
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

		$settings = apply_filters( 'hootkit_products_carticon_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'products-carticon', false );
		// Use Hootkit template if theme does not have one
		$widget_template = ( $widget_template ) ? $widget_template : hootkit()->dir . 'widgets/products-carticon/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'products-carticon' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_products_carticon_widget_register(){
	register_widget( 'HootKit_Products_Carticon_Widget' );
}
add_action( 'widgets_init', 'hootkit_products_carticon_widget_register' );

/**
 * Display Ajax Function
 */
add_action( 'wp_ajax_hk_carticon_refresh', 'hootkit_carticon_refresh' );
add_action( 'wp_ajax_nopriv_hk_carticon_refresh', 'hootkit_carticon_refresh' );
function hootkit_carticon_refresh() {
	$result = array(
		'hasitems' => '',
		'items' => '',
		'cartvalue' => '',
	);
	if ( wp_verify_nonce( $_REQUEST['nonce'], 'hootkit-carticon-widget' ) ) {
		// $carticon = $_REQUEST['carticon'];
		$cartempty = WC()->cart->is_empty();
		$result['hasitems']  = ( $cartempty ) ? 'no' : 'yes';
		$result['items']     = ( !$cartempty ) ? WC()->cart->get_cart_contents_count() : apply_filters( 'hk_carticon_itemnumber_when_noitem', 0 );
		$result['cartvalue'] = ( !$cartempty ) ? WC()->cart->get_cart_subtotal() : apply_filters( 'hk_carticon_value_when_noitem', 0 );
	}
	echo json_encode( $result );
	die();
}