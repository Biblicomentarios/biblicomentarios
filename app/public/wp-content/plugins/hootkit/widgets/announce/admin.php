<?php
/**
 * Announce Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Announce_Widget
 */
class HootKit_Announce_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-announce';
		$settings['name'] = hootkit()->get_string('announce');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Announcement Message', 'hootkit' ),
			// 'classname'		=> 'hoot-announce-widget', // CSS class applied to frontend widget container via 'before_widget' arg
		);
		$settings['control_options'] = array();
		$settings['form_options'] = array(
			//'name' => can be empty or false to hide the name
			'icon' => array(
				'name'		=> __( 'Icon', 'hootkit' ),
				'type'		=> 'icon',
			),
			'headline' => array(
				'name'		=> __( 'Headline', 'hootkit' ),
				'type'		=> 'text',
			),
			'message' => array(
				'name'		=> __( 'Message', 'hootkit' ),
				'type'		=> 'text',
			),
			'iconsize' => array(
				'name'		=> __( "Icon Size (Optional)", 'hootkit' ),
				'desc'		=> __( '(in pixels) Leave empty for default', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 10, ),
				'sanitize'	=> 'absint',
			),
			'iconcolor' => array(
				'name'		=> __( 'Icon Color (Optional)', 'hootkit' ),
				'desc'		=> __( 'Leave empty to use font color.', 'hootkit' ),
				// 'std'		=> '#aa0000',
				'type'		=> 'color',
			),
			'headlinesize' => array(
				'name'		=> __( "Headline Size (Optional)", 'hootkit' ),
				'desc'		=> __( '(in pixels) Leave empty for default', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 10, ),
				'sanitize'	=> 'absint',
			),
			'background' => array(
				'name'		=> __( 'Background (Optional)', 'hootkit' ),
				'desc'		=> __( 'Leave empty for no background.', 'hootkit' ),
				// 'std'		=> '#aa0000',
				'type'		=> 'color',
			),
			'fontcolor' => array(
				'name'		=> __( 'Text Color (Optional)', 'hootkit' ),
				'desc'		=> __( 'Leave empty to use default color.', 'hootkit' ),
				// 'std'		=> '#aa0000',
				'type'		=> 'color',
			),
			'url' => array(
				'name'		=> __( 'Link URL (Optional)', 'hootkit' ),
				'type'		=> 'text',
				'sanitize'	=> 'url',
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

		$settings = apply_filters( 'hootkit_announce_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'announce', false );
		// Use Hootkit template if theme does not have one
		$widget_template = ( $widget_template ) ? $widget_template : hootkit()->dir . 'widgets/announce/view.php';
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'announce' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_announce_widget_register(){
	register_widget( 'HootKit_Announce_Widget' );
}
add_action( 'widgets_init', 'hootkit_announce_widget_register' );