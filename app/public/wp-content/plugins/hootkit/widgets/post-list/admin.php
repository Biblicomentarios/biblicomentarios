<?php
/**
 * Posts List Widget
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class HootKit_Posts_List_Widget
 */
class HootKit_Posts_List_Widget extends HK_Widget {

	function __construct() {

		$settings['id'] = 'hootkit-posts-list';
		$settings['name'] = hootkit()->get_string('post-list');
		$settings['widget_options'] = array(
			'description'	=> __( 'Display Posts List (all or specific category)', 'hootkit' ),
			// 'classname'		=> 'hoot-post-list-widget', // CSS class applied to frontend widget container via 'before_widget' arg
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
				'name'		=> __( 'List Style', 'hootkit' ),
				'type'		=> 'images',
				'std'		=> 'style1',
				'options'	=> array(
					'style0'	=> hootkit()->uri . 'assets/images/posts-list-style-0.png',
					'style1'	=> hootkit()->uri . 'assets/images/posts-list-style-1.png',
					'style2'	=> hootkit()->uri . 'assets/images/posts-list-style-2.png',
					//'style3'	=> hootkit()->uri . 'assets/images/posts-list-style-3.png',
				),
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
			'columns' => array(
				'name'		=> __( 'Number Of Columns', 'hootkit' ),
				'type'		=> 'smallselect',
				'std'		=> '1',
				'options'	=> array(
					'1'	=> __( '1', 'hootkit' ),
					'2'	=> __( '2', 'hootkit' ),
					'3'	=> __( '3', 'hootkit' ),
				),
			),
			'count1' => array(
				'name'		=> __( 'Number of Posts - 1st Column', 'hootkit' ),
				'desc'		=> __( 'Default: 3', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'count2' => array(
				'name'		=> __( 'Number of Posts - 2nd Column', 'hootkit' ),
				'desc'		=> __( 'Default: 3<br>(if selected 2 or 3 columns above)', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'count3' => array(
				'name'		=> __( 'Number of Posts - 3rd Column', 'hootkit' ),
				'desc'		=> __( 'Default: 3<br>(if selected 3 columns above)', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'offset' => array(
				'name'		=> __( 'Offset', 'hootkit' ),
				'desc'		=> __( 'Number of posts to skip from the start. Leave empty to start from the latest post.', 'hootkit' ),
				'type'		=> 'text',
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
			'show_content' => array(
				'name'		=> __( 'Content', 'hootkit' ),
				'type'		=> 'select',
				'std'		=> 'none',
				'options'	=> array(
					'excerpt'	=> __( 'Display Excerpt', 'hootkit' ),
					'content'	=> __( 'Display Full Content', 'hootkit' ),
					'none'		=> __( 'None', 'hootkit' ),
				),
			),
			'excerpt_length' => array(
				'name'		=> __( 'Custom Excerpt Length', 'hootkit' ),
				'desc'		=> __( 'Select <strong>\'Display Excerpt\'</strong> in option above. Leave empty for default excerpt length.', 'hootkit' ),
				'type'		=> 'text',
				'settings'	=> array( 'size' => 3, ),
				'sanitize'	=> 'absint',
			),
			'firstpost' => array(
				'name'		=> __( 'First Post', 'hootkit' ),
				'type'		=> 'collapse',
				'settings'	=> array( 'state' => 'open' ),
				'fields'	=> array(
					'size' => array(
						'name'		=> __( 'Thumbnail Size', 'hootkit' ),
						'type'		=> 'select',
						'std'		=> 'medium',
						'options'	=> array(
							'thumb'		=> __( 'Thumbnail (like other posts)', 'hootkit' ),
							'small'		=> __( 'Rectangular Small', 'hootkit' ),
							'medium'	=> __( 'Rectangular Medium', 'hootkit' ),
							'big'		=> __( 'Rectangular Big', 'hootkit' ),
							'full'		=> __( 'Full (Non Cropped)', 'hootkit' ),
						),
					),
					'author' => array(
						'name'		=> __( 'Show Author', 'hootkit' ),
						'type'		=> 'checkbox',
						'std'		=> 1,
					),
					'date' => array(
						'name'		=> __( 'Show Post Date', 'hootkit' ),
						'type'		=> 'checkbox',
						'std'		=> 1,
					),
					'comments' => array(
						'name'		=> __( 'Show number of comments', 'hootkit' ),
						'type'		=> 'checkbox',
					),
					'cats' => array(
						'name'		=> __( 'Show Categories', 'hootkit' ),
						'type'		=> 'checkbox',
					),
					'tags' => array(
						'name'		=> __( 'Show Tags', 'hootkit' ),
						'type'		=> 'checkbox',
					),
					'show_content' => array(
						'name'		=> __( 'Content', 'hootkit' ),
						'type'		=> 'select',
						'std'		=> 'excerpt',
						'options'	=> array(
							'excerpt'	=> __( 'Display Excerpt', 'hootkit' ),
							'content'	=> __( 'Display Full Content', 'hootkit' ),
							'none'		=> __( 'None', 'hootkit' ),
						),
					),
					'excerpt_length' => array(
						'name'		=> __( 'Custom Excerpt Length', 'hootkit' ),
						'desc'		=> __( 'Select <strong>\'Display Excerpt\'</strong> in option above. Leave empty for default excerpt length.', 'hootkit' ),
						'type'		=> 'text',
						'settings'	=> array( 'size' => 3, ),
						'sanitize'	=> 'absint',
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

		if ( !in_array( 'widget-subtitle', hootkit()->get_config( 'supports' ) ) ) {
			unset( $settings['form_options']['subtitle'] );
		}

		$settings = apply_filters( 'hootkit_posts_list_widget_settings', $settings );

		parent::__construct( $settings['id'], $settings['name'], $settings['widget_options'], $settings['control_options'], $settings['form_options'] );

	}

	/**
	 * Display the widget content
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		// Allow theme/child-themes to use their own template
		$widget_template = hoot_get_widget( 'post-list', false );
		// Use Hootkit template if theme does not have one
		$default = ( !in_array( 'list-widget', hootkit()->get_config( 'supports' ) ) ) ? hootkit()->dir . 'widgets/post-list/view-deprecated.php' : hootkit()->dir . 'widgets/post-list/view.php'; // JNES@deprecated <= HootKit v1.1.3 @9.20
		$widget_template = ( $widget_template ) ? $widget_template : $default;
		// Option to overwrite variables to keep html tags in title later sanitized during display => skips 'widget_title' filter (esc_html hooked) action on title; (Possibly redundant as html is sanitized in title during save)
		if ( apply_filters( 'hootkit_display_widget_extract_overwrite', false, 'post-list' ) ) extract( $instance, EXTR_OVERWRITE ); else extract( $instance, EXTR_SKIP );
		// Fire up the template
		if ( is_string( $widget_template ) && file_exists( $widget_template ) ) include ( $widget_template );
	}

}

/**
 * Register Widget
 */
function hootkit_posts_list_widget_register(){
	register_widget( 'HootKit_Posts_List_Widget' );
}
add_action( 'widgets_init', 'hootkit_posts_list_widget_register' );