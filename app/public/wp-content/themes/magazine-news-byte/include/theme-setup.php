<?php
/**
 * Theme Setup
 * This file is loaded using 'after_setup_theme' hook at priority 10
 *
 * @package    Magazine News Byte
 * @subpackage Theme
 */


/* === WordPress === */


// Automatically add <title> to head.
add_theme_support( 'title-tag' );

// Adds core WordPress HTML5 support.
add_theme_support( 'html5', array( 'script', 'style', 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

// Add theme support for WordPress Custom Logo
add_theme_support( 'custom-logo' );

// Add theme support for WordPress Custom Background
add_theme_support( 'custom-background', array(
	'default-color'      => magnb_default_style( 'site_background' ),
	// 'default-image'      => hoot_data()->template_uri . 'images/background.jpg',
	// 'default-repeat'     => 'no-repeat',
	// 'default-position-x' => 'center',
	// 'default-position-y' => 'top',
	// 'default-size'       => 'cover',
	// 'default-attachment' => 'fixed',
) );

// Add theme support for custom headers
add_theme_support( 'custom-header', array(
	'width' => 1440,
	'height' => 500,
	'flex-height' => true,
	'flex-width' => true,
	'default-image' => hoot_data()->template_uri . 'images/header.jpg',
	'header-text' => false
) );

// Adds theme support for WordPress 'featured images'.
add_theme_support( 'post-thumbnails' );

// Automatically add feed links to <head>.
add_theme_support( 'automatic-feed-links' );


/* === WordPress Blocks === */


// Add Gutenberg Wide Align support
add_theme_support( 'align-wide' );

// Temporarily remove Gutenberg Widgets Screen
if ( apply_filters( 'magnb_disable_widgets_block_editor', true ) ) {
	// remove_theme_support( 'widgets-block-editor' ); // Doesnt work when hooked into after_setup_theme
	add_filter( 'use_widgets_block_editor', '__return_false' ); // @see `wp_use_widgets_block_editor()`
	add_action( 'widgets_init', 'magnb_remove_theme_support_widgets_block_editor' );
}
function magnb_remove_theme_support_widgets_block_editor(){ remove_theme_support( 'widgets-block-editor' ); }

// Add accent colors to Block Pallete - hook to init to have default vals for accent via hoot_get_mod
if ( apply_filters( 'magnb_editor_color_palette', true ) )
	add_action( 'init', 'magnb_block_color_palette' );
function magnb_block_color_palette(){
	$defaults = array(
		'#000000' => array( 'black',                 __( 'Black', 'magazine-news-byte' ) ),
		'#abb8c3' => array( 'cyan-bluish-gray',      __( 'Cyan bluish gray', 'magazine-news-byte' ) ),
		'#ffffff' => array( 'white',                 __( 'White', 'magazine-news-byte' ) ),
		'#f78da7' => array( 'pale-pink',             __( 'Pale pink', 'magazine-news-byte' ) ),
		'#cf2e2e' => array( 'vivid-red',             __( 'Vivid red', 'magazine-news-byte' ) ),
		'#ff6900' => array( 'luminous-vivid-orange', __( 'Luminous vivid orange', 'magazine-news-byte' ) ),
		'#fcb900' => array( 'luminous-vivid-amber',  __( 'Luminous vivid amber', 'magazine-news-byte' ) ),
		'#7bdcb5' => array( 'light-green-cyan',      __( 'Light green cyan', 'magazine-news-byte' ) ),
		'#00d084' => array( 'vivid-green-cyan',      __( 'Vivid green cyan', 'magazine-news-byte' ) ),
		'#8ed1fc' => array( 'pale-cyan-blue',        __( 'Pale cyan blue', 'magazine-news-byte' ) ),
		'#0693e3' => array( 'vivid-cyan-blue',       __( 'Vivid cyan blue', 'magazine-news-byte' ) ),
		'#9b51e0' => array( 'vivid-purple',          __( 'Vivid purple', 'magazine-news-byte' ) ),
	);
	$load = false;
	$palette = array();
	$accent = hoot_get_mod( 'accent_color' );
	if ( !array_key_exists( $accent, $defaults ) ) {
		$load = true;
		$palette[] = array(
			'name' => __( 'Accent Color', 'magazine-news-byte' ),
			'slug' => 'accent',
			'color' => $accent
		);
	}
	$accentfont = hoot_get_mod( 'accent_font' );
	if ( !array_key_exists( $accentfont, $defaults ) ) {
		$load = true;
		$palette[] = array(
			'name' => __( 'Accent Font Color', 'magazine-news-byte' ),
			'slug' => 'accent-font',
			'color' => $accentfont
		);
	}
	if ( $load ) {
		foreach ( $defaults as $key => $value )
			$palette[] = array(
				'name' => $value[1],
				'slug' => $value[0],
				'color' => $key
			);
		add_theme_support( 'editor-color-palette', $palette );
	}
}


/* === WordPress Jetpack === */


add_theme_support( 'infinite-scroll', array(
	'type' => apply_filters( 'magnb_jetpack_infinitescroll_type', '' ), // scroll or click - currently add support for both
	'container' => apply_filters( 'magnb_jetpack_infinitescroll_container', 'content-wrap' ),
	'footer' => false,
	'wrapper' => true,
	'render' => apply_filters( 'magnb_jetpack_infinitescroll_render', 'magnb_jetpack_infinitescroll_render' ),
) );


/* === WooCommerce Plugin === */


// Woocommerce support and init load theme woo functions
if ( class_exists( 'WooCommerce' ) ) {
	add_theme_support( 'woocommerce' );
	if ( file_exists( hoot_data()->template_dir . 'woocommerce/functions.php' ) )
		include_once( hoot_data()->template_dir . 'woocommerce/functions.php' );
}


/** One click demo import **/

// Disable branding
add_filter( 'pt-ocdi/disable_pt_branding', 'magnb_disable_pt_branding' );
function magnb_disable_pt_branding() {
	return true;
}


/* === Hootkit Plugin === */


// Load theme's Hootkit functions if plugin is active
if ( class_exists( 'HootKit' ) && file_exists( hoot_data()->template_dir . 'hootkit/functions.php' ) )
	include_once( hoot_data()->template_dir . 'hootkit/functions.php' );


/* === Tribe The Events Calendar Plugin === */


// Load support if plugin active
if ( class_exists( 'Tribe__Events__Main' ) ) {

	// Hook into 'wp' to use conditional hooks
	add_action( 'wp', 'magnb_tribeevent', 10 );

	// Add hooks based on view
	// @since 2.7.3
	function magnb_tribeevent() {
		if ( is_post_type_archive( 'tribe_events' ) || ( function_exists( 'tribe_is_events_home' ) && tribe_is_events_home() ) ) {
			add_filter( 'theme_mod_archive_type', 'magnb_tribeevent_archivetype', 5 );
			add_filter( 'theme_mod_archive_post_content', 'magnb_tribeevent_archive', 5 );
			add_filter( 'theme_mod_archive_post_meta', 'magnb_tribeevent_archive_postmeta', 5 );
			add_action( 'magnb_display_loop_meta', 'magnb_tribeevent_loopmeta', 5 );
		}
		if ( is_singular( 'tribe_events' ) ) {
			add_action( 'magnb_display_loop_meta', 'magnb_tribeevent_loopmeta_single', 5 );
		}
	}

	// Modify theme options and displays
	// @since 2.7.3
	function magnb_tribeevent_archivetype( $type ) { return 'big'; }
	function magnb_tribeevent_archive( $content ) { return 'full-content'; }
	function magnb_tribeevent_archive_postmeta( $args ) { return ''; }
	function magnb_tribeevent_loopmeta( $display ) { return false; }
	function magnb_tribeevent_loopmeta_single( $display ) {
		the_post(); rewind_posts(); // Bug Fix
		return false;
	}

}


/* === AMP Plugin ===
 * @ref https://wordpress.org/plugins/amp/
 * @ref https://www.hostinger.in/tutorials/wordpress-amp/
 * @ref https://validator.ampproject.org/
 * @ref https://amp.dev/documentation/guides-and-tutorials/learn/validation-workflow/validation_errors/
 * @credit https://amp-wp.org/documentation/developing-wordpress-amp-sites/how-to-develop-with-the-amp-plugin/
 * @credit https://amp-wp.org/documentation/how-the-plugin-works/amp-plugin-serving-strategies/
*/
// Call 'is_amp_endpoint' after 'parse_query' hook
add_action( 'wp', 'magnb_amp', 5 );
function magnb_amp(){
	if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		add_action( 'wp_enqueue_scripts', 'magnb_amp_remove_scripts', 999 );
		add_filter( 'hoot_attr_body', 'magnb_amp_attr_body' );
		add_filter( 'theme_mod_mobile_submenu_click', 'magnb_amp_emptymod' );
		// add_filter( 'theme_mod_custom_js', 'magnb_amp_emptymod' );
	}
}
function magnb_amp_remove_scripts(){
	$dequeue = array_map( 'wp_dequeue_script', array(
		'comment-reply', 'jquery', 'hoverIntent', 'jquery-superfish', 'jquery-fitvids', 'jquery-parallax', 'resizesensor', 'jquery-theia-sticky-sidebar',
		'hoot-theme', 'hoot-theme-premium',
		'jquery-lightGallery', 'jquery-isotope',
		'jquery-waypoints', 'jquery-waypoints-sticky', 'hoot-scroller',
		'hootkit', 'jquery-lightSlider', 'jquery-circliful',
	) );
}
function magnb_amp_attr_body( $attr ) {
	$attr['class'] = ( empty( $attr['class'] ) ) ? ' hootamp' : $attr['class'] . ' hootamp';
	return $attr;
}
function magnb_amp_emptymod(){
	return 0;
}


/* === Breadcrumb NavXT Plugin === */


// Load support if plugin active
if ( class_exists( 'bcn_breadcrumb' ) ) {

	// Enclose pretext in span
	add_filter( 'bcn_widget_pretext', 'magnb_bcn_pretext' );

	// Enclose pretext in span
	// @since 2.7.3
	function magnb_bcn_pretext( $pretext ) {
		if ( empty( $pretext ) ) return '';
		return '<span class="hoot-bcn-pretext">' . $pretext . '</span>';
	}

}


/* === Theme Hooks === */


/**
 * Handle content width for embeds and images.
 * Hooked into 'init' so that we can pull custom content width from theme options
 *
 * @since 1.0
 * @return void
 */
function magnb_set_content_width() {
	$width = intval( hoot_get_mod( 'site_width' ) );
	$width = !empty( $width ) ? $width : 1260;
	$GLOBALS['content_width'] = absint( $width );
}
add_action( 'init', 'magnb_set_content_width', 10 );

/**
 * Modify the '[...]' Read More Text
 *
 * @since 1.0
 * @return string
 */
function magnb_readmoretext( $more ) {
	$read_more = esc_html( hoot_get_mod('read_more') );
	/* Translators: %s is the HTML &rarr; symbol */
	// $read_more = ( empty( $read_more ) ) ? sprintf( __( 'Continue Reading %s', 'magazine-news-byte' ), '&rarr;' ) : $read_more;
	$read_more = ( empty( $read_more ) ) ? __( 'Continue Reading', 'magazine-news-byte' ) : $read_more;
	return $read_more;
}
add_filter( 'hoot_readmoretext', 'magnb_readmoretext' );

/**
 * Modify the exceprt length.
 * Make sure to set the priority correctly such as 999, else the default WordPress filter on this function will run last and override settng here.
 *
 * @since 1.0
 * @return void
 */
function magnb_custom_excerpt_length( $length ) {
	if ( is_admin() )
		return $length;

	$excerpt_length = intval( hoot_get_mod('excerpt_length') );
	if ( !empty( $excerpt_length ) )
		return $excerpt_length;
	return 50;
}
add_filter( 'excerpt_length', 'magnb_custom_excerpt_length', 999 );

/**
 * Register recommended plugins via TGMPA
 *
 * @since 1.0
 * @return void
 */
function magnb_tgmpa_plugins() {
	// Array of plugin arrays. Required keys are name and slug.
	// Since source is from the .org repo, it is not required.
	$plugins = array(
		array(
			'name'     => __( '(HootKit) Magazine NewsByte Sliders, Widgets', 'magazine-news-byte' ),
			'slug'     => 'hootkit',
			'required' => false,
		),
	);

	// $wpv = get_bloginfo( 'version' );
	// if ( version_compare( $wpv, '5.8', '>=' ) ) {
	// 	$plugins[] = array(
	// 		'name'     => __( 'Classic Widgets', 'magazine-news-byte' ),
	// 		'slug'     => 'classic-widgets',
	// 		'required' => false,
	// 	);
	// }
	$plugins = apply_filters( 'magnb_tgmpa_plugins', $plugins );

	// Array of configuration settings.
	$config = array(
		'is_automatic' => true,
	);
	// Register plugins with TGM_Plugin_Activation class
	tgmpa( $plugins, $config );
}
add_filter( 'tgmpa_register', 'magnb_tgmpa_plugins' );