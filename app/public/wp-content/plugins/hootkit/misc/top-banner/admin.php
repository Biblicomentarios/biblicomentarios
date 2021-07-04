<?php
/**
 * Top Banner Modules
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Load Customizer Lib
if ( !function_exists( 'hootkit_add_customizer_options' ) )
	require_once( hootkit()->dir . 'misc/customizer.php' );

// Add admin customizer options
add_filter( 'hootkit_customizer_options', 'hootkit_topbanner_customizer_options' );

// Include display template
// Alternate use: add_action( "{$themeslug}_before_topbar", 'hootkit_topbanner_display' ); where $themeslug = ( !empty( hoot_data()->basetemplate_slug ) ) ? hoot_data()->basetemplate_slug : strtolower( preg_replace( '/[^a-zA-Z0-9]+/', '_', trim( hoot_data()->template_name ) ) );
add_action( 'wp_body_open', 'hootkit_topbanner_display' );

/**
 * Add admin customizer options
 *
 * @since 1.1.1
 * @access public
 * @param array $options
 * @return array
 */
function hootkit_topbanner_customizer_options( $options ) {

	$settings = array();
	$sections = array();
	$panels = array();

	$section = 'hk-topbanner';

	$sections[ $section ] = array(
		'title'			=> esc_html__( 'Site Top Banner (HootKit)', 'hootkit' ),
		'priority'		=> '7', // 2:links; 3:Frontpage; 5:Setup; 10:Header
		'description'	=> esc_html__( "'Top Banner' is displayed at the top of the site above header", 'hootkit' ),
	);

	$settings['hktb_background'] = array(
		'label'		=> esc_html__( 'Background Image', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'image',
		'description' => esc_html__( "This image acts as a 'background' only (i.e. it will get cropped to fill the background). To display an image as a banner, add image to 'Content' option below instead.", 'hootkit' ),
		// 'priority'	=> '10',
		'transport' => 'postMessage',
	);

	$settings['hktb_url'] = array(
		'label'		=> esc_html__( 'Banner Link URL', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'url',
		'input_attrs' => array( 'placeholder' => 'http://' ),
		// 'description' => esc_html__( 'Add a link to entire content in banner area', 'hootkit' ),
		// 'priority'	=> '10',
		'transport' => 'postMessage',
	);

	$settings['hktb_url_target'] = array(
		'section'	=> $section,
		'type'		=> 'checkbox',
		'description' => esc_html__( 'Open link in new window?', 'hootkit' ),
		'transport' => 'postMessage',
	);

	$settings['hktb_url_scope'] = array(
		'label'		=> esc_html__( 'Banner Link Scope', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'radio',
		'choices'	=> array(
			'background'	=> esc_html__( 'Entire Background', 'hootkit' ),
			'content'		=> esc_html__( 'Content Box', 'hootkit' ),
		),
		'default'	=> 'background',
		// 'priority'	=> '10',
		'transport' => 'postMessage', // to work with 'selective_refresh' added via 'hktb_content'
	);

	$settings['hktb_content_stretch'] = array(
		'label'		=> esc_html__( 'Content Box Size', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'radioimage',
		'choices'	=> array(
			'grid'		=> hootkit()->uri . 'assets/images/topbanner-cb-style-1.png',
			'stretch'	=> hootkit()->uri . 'assets/images/topbanner-cb-style-2.png',
		),
		'default'	=> 'grid',
		'description' => esc_html__( "Stretched option can be useful if you are displaying an image HTML in the 'Content' option below", 'hootkit' ),
		// 'priority'	=> '10',
		'transport' => 'postMessage',
	);

	$settings['hktb_content_nopad'] = array(
		// 'label'		=> esc_html__( 'Remove paddings / spaces at corners?', 'hootkit' ),
		// 'sublabel'	=> esc_html__( 'Remove paddings / spaces at corners?', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'checkbox',
		// 'default'	=> 1,
		'description' => esc_html__( 'Remove paddings / spaces at corners?', 'hootkit' ),
		// 'priority'	=> '10',
		'active_callback' => 'hootkit_callback_tb_content_nopad', /*** Use JS API (in customize.js) for conditional controls using 'hktb_content_stretch' setting in their active_callback - for quicker response ***/
		'transport' => 'postMessage',
	);

	$settings['hktb_content_bg'] = array(
		'label'		=> esc_html__( 'Content Style', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'select',
		'choices'	=> array(
			'dark'			=> esc_html__( 'Dark Font', 'hootkit' ),
			'light'			=> esc_html__( 'Light Font', 'hootkit' ),
			'dark-on-light'	=> esc_html__( 'Dark Font / Light Background', 'hootkit' ),
			'light-on-dark'	=> esc_html__( 'Light Font / Dark Background', 'hootkit' ),
		),
		'default'	=> 'light-on-dark',
		// 'priority'	=> '10',
		'transport' => 'postMessage',
	);

	$settings['hktb_content_title'] = array(
		'label'		=> esc_html__( 'Content Title', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'text',
		// 'priority'	=> '10',
		'transport' => 'postMessage', // to work with 'selective_refresh' added via 'hktb_content'
	);

	$settings['hktb_content'] = array(
		'label'		=> esc_html__( 'Content', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'textarea',
		// 'priority'	=> '10',
		'selective_refresh' => array( 'hktb_content_partial', array(
			'selector'            => '#topbanner',
			'settings'            => array( 'hktb_content', 'hktb_content_title', 'hktb_url_scope' ),
			'render_callback'     => 'hootkit_topbanner_display',
			'container_inclusive' => true,
			) ),
	);

	$settings['hktb_content_description'] = array(
		// 'label'		=> esc_html__( 'Banner Content', 'hootkit' ),
		'section'	=> $section,
		'type'		=> 'content',
		/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		'content' => '<span style="display:block;margin-top:-15px;">'. sprintf( esc_html__( '%1$s%3$s
			%5$sInsert Timer%6$s
			You can add a HootKit timer to your content using shortcode. The values are the %7$send time%8$s
			%9$s[HKtimer year="%7$s2029%8$s" month="%7$s12%8$s" day="%7$s31%8$s" hour="%7$s23%8$s" minute="%7$s59%8$s"]%14$s

			%4$s%3$s
			%5$sUse HTML tags to style your content.:%6$s
			%10$s<h5> Heading </h5>%14$s
			%11$s<b> Bold </b>%14$s
			%11$s<strong> Bold </strong>%14$s
			%12$s<em> Emphasize (italic) </em>%14$s
			%13$s<mark> Marked (highlighted) </mark>%14$s

			%4$s%3$s
			%5$sAdd image using img html:%6$s
			%9$s<img src=" %7$shttp://website.com/image.png%8$s ">%14$s
			If you are adding a large image to Content (to display as a full width image banner), it can be useful to set %15$sContent Box Size%16$s option above to %15$sStretched%16$s
			%4$s%2$s
			', 'hootkit' ), '<ul style="list-style:disc;margin-left:1.5em">','</ul>','<li>','</li>',
								 '<strong style="display:block;margin:10px 0 0;">', '</strong>',
								 '<span style="text-decoration:underline">', '</span>',
								 '<code style="display:block;margin:2px 0;font-style:normal;">',
								 '<code style="display:block;margin:2px 0;font-style:normal;font-weight:bold;font-size:1.2em">',
								 '<code style="display:block;margin:2px 0;font-style:normal;font-weight:bold">',
								 '<code style="display:block;margin:2px 0;font-style:normal;font-style:italic">',
								 '<code style="display:block;margin:2px 0;font-style:normal;color:#a7a746">', '</code>',
								 '<strong>', '</strong>'
				),
		// 'description' => '<span style="font-style:normal">' . esc_html__( '', 'hootkit' ) . '</span>',
		// 'priority'	=> '10',
	);

	return array_replace_recursive( $options, array(
		'settings' => $settings,
		'sections' => $sections,
		'panels'   => $panels,
		) );
}

/**
 * Callback Functions for customizer settings
 */
function hootkit_callback_tb_content_nopad( $control ) {
	$selector = $control->manager->get_setting('hktb_content_stretch')->value();
	return ( $selector == 'stretch' ) ? true : false;
}

/**
 * Display top banner in theme
 *
 * @since 1.1.1
 * @access public
 */
function hootkit_topbanner_display(){
	/* include ( apply_filters( 'hootkit_top_banner_template', hootkit()->dir . 'misc/top-banner/view.php' ) ); */
	// Allow theme/child-themes to use their own template
	$topbanner_template = hoot_get_widget( 'top-banner', false );
	// Use Hootkit template if theme does not have one
	$topbanner_template = ( $topbanner_template ) ? $topbanner_template : hootkit()->dir . 'misc/top-banner/view.php';
	// Fire up the template
	if ( is_string( $topbanner_template ) && file_exists( $topbanner_template ) ) include ( $topbanner_template );
}