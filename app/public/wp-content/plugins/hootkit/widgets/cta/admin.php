<?php
/**
 * Call To Action Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_CTA_Widget
 */
class HootKit_CTA_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-cta';
		$settings['name'] = hootkit()->get_string('cta');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Call To Action block', 'hootkit' ),
			// 'classname'		=> 'hoot-cta-widget', // CSS class applied to frontend widget container via 'before_widget' arg
		);
		$settings['control_options'] = array();
		$settings['form_options'] = array(
			//'name' => can be empty or false to hide the name
			'headline' => array(
				'name'		=> __( 'Headline', 'hootkit' ),
				'type'		=> 'text',
			),
			'subtitle' => array(
				'name'		=> __( 'Sub Heading (optional)', 'hootkit' ),
				'type'		=> 'text',
			),
			'titlesize' => array(
				'name'		=> __( 'Title Size', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'default',
				'options'	=> array(
					'small'		=> __( 'Small', 'hootkit' ),
					'default'	=> __( 'Default (H3 Heading)', 'hootkit' ),
					'big'		=> __( 'Big', 'hootkit' ),
					'huge'		=> __( 'Huge', 'hootkit' ),
				),
			),
			'description' => array(
				'name'		=> __( 'Description', 'hootkit' ),
				'type'		=> 'textarea',
			),
			'link_type' => array(
				'name'		=> __( 'Link Type', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'button',
				'options'	=> array(
					'button'	=> __( 'Button', 'hootkit' ),
					'text'		=> __( 'Text', 'hootkit' ),
				),
			),
			'button_text' => array(
				'name'		=> __( 'Link Text', 'hootkit' ),
				'type'		=> 'text',
				'std'		=> __( 'KNOW MORE', 'hootkit' ),
			),
			'url' => array(
				'name'		=> __( 'Link URL', 'hootkit' ),
				'desc'		=> __( 'Leave empty if you dont want to show link', 'hootkit' ),
				'type'		=> 'text',
				'sanitize'	=> 'url',
			),
			'align' => array(
				'name'		=> __( 'Alignment', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'center',
				'options'	=> array(
					'left'		=> __( 'Left', 'hootkit' ),
					'center'	=> __( 'Center', 'hootkit' ),
					'right'		=> __( 'Right', 'hootkit' ),
				),
			),
			'style' => array(
				'name'		=> __( 'Box Style', 'hootkit' ),
				'type'		=> 'images',
				'std'		=> 'style1',
				'options'	=> array(
					'style1'	=> hootkit()->uri . 'assets/images/cta-style-1.png',
					'style2'	=> hootkit()->uri . 'assets/images/cta-style-2.png',
				),
			),
			'content_bg' => array(
				'name'		=> __( 'Content Styling', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'default',
				'options'	=> array(
					'default'		=> __( 'Default (no background)', 'hootkit' ),
					'dark-on-light'	=> __( 'Dark Font / Light Background', 'hootkit' ),
					'light-on-dark'	=> __( 'Light Font / Dark Background', 'hootkit' ),
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

		if ( !in_array( 'cta-styles', hootkit()->get_config( 'supports' ) )
			 && !in_array( 'cta-styles', hootkit()->get_config( 'settings' ) ) // JNES@deprecated <= Unos v2.7.1 @12.18
			)
			unset( $settings['form_options']['style'] );

		$settings = apply_filters( 'hootkit_cta_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'cta', false );
		// Use Hootkit template if theme does not have one
		$widget_template = ( $widget_template ) ? $widget_template : hootkit()->dir . 'widgets/cta/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'cta' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_cta_widget_register(){
	register_widget( 'HootKit_CTA_Widget' );
}
add_action( 'widgets_init', 'hootkit_cta_widget_register' );