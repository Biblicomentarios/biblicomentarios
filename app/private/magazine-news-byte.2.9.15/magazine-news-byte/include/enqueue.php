<?php
/**
 * Enqueue scripts and styles for the theme.
 * This file is loaded via the 'after_setup_theme' hook at priority '10'
 *
 * @package    Magazine News Byte
 * @subpackage Theme
 */

/* Add custom scripts. */
add_action( 'wp_enqueue_scripts', 'magnb_enqueue_scripts', 9 );

/* Set data for theme scripts localization. hootData is actually localized at priority 11, so populate data before that at priority 9 */
add_action( 'wp_enqueue_scripts', 'magnb_localize_script', 9 );

/* Load theme script. Set priority to 11 so that the main script.js is loaded after @12, while other scripts are loaded before @10.*/
add_action( 'wp_enqueue_scripts', 'magnb_base_enqueue_scripts', 11 );

/* Add custom styles. Set priority to default 10 so that theme's main style is loaded after these styles @12, and can thus easily override any style without over-qualification. */
add_action( 'wp_enqueue_scripts', 'magnb_enqueue_styles', 10 );

/* Dequeue font awesome */
add_action( 'wp_enqueue_scripts', 'magnb_dequeue_fontawesome', 99 );

/**
 * Load scripts for the front end.
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'magnb_enqueue_scripts' ) ) :
function magnb_enqueue_scripts() {

	/* Load the comment reply script on singular posts with open comments if threaded comments are supported. */
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
		wp_enqueue_script( 'comment-reply' );

	/* Load jquery */
	wp_enqueue_script( 'jquery' );

	/* Load Superfish and WP's hoverIntent */
	wp_enqueue_script( 'hoverIntent' );
	$script_uri = hoot_locate_script( 'js/jquery.superfish' );
	wp_enqueue_script( 'jquery-superfish', $script_uri, array( 'jquery', 'hoverIntent'), '1.7.5', true );

	/* Load fitvids */
	$script_uri = hoot_locate_script( 'js/jquery.fitvids' );
	wp_enqueue_script( 'jquery-fitvids', $script_uri, array( 'jquery' ), '1.1', true );

	/* Load parallax */
	$script_uri = hoot_locate_script( 'js/jquery.parallax' );
	wp_enqueue_script( 'jquery-parallax', $script_uri, array( 'jquery' ), '1.4.2', true );

	/* Load Theia Sticky Sidebar */
	if ( !hoot_get_mod( 'disable_sticky_sidebar' ) ) {
		$script_uri = hoot_locate_script( 'js/resizesensor' );
		wp_enqueue_script( 'resizesensor', $script_uri, array(), '1.7.0', true );
		$script_uri = hoot_locate_script( 'js/jquery.theia-sticky-sidebar' );
		wp_enqueue_script( 'jquery-theia-sticky-sidebar', $script_uri, array( 'jquery', 'resizesensor' ), '1.7.0', true );
	}

}
endif;

/**
 * Set data for theme scripts localization
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'magnb_localize_script' ) ) :
function magnb_localize_script() {

	$scriptdata = hoot_data( 'scriptdata' );
	if ( empty( $scriptdata ) )
		$scriptdata = array();

	// Sticky Sidebar
	if ( hoot_get_mod( 'disable_sticky_sidebar' ) || ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) )
		$scriptdata['stickySidebar'] = 'disable';

	hoot_set_data( 'scriptdata', $scriptdata );

}
endif;

/**
 * Load scripts for the front end.
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'magnb_base_enqueue_scripts' ) ) :
function magnb_base_enqueue_scripts() {

	/* Load Theme Javascript */
	$script_uri = hoot_locate_script( 'js/hoot.theme' );
	wp_enqueue_script( 'hoot-theme', $script_uri, array( 'jquery' ), hoot_data()->template_version, true );

	/* Pass data to various Theme Scripts. We use jquery so that data is available to all (jquery dependant) theme loaded scripts */
	$scriptdata = hoot_data( 'scriptdata' );
	if ( !empty( $scriptdata ) && is_array( $scriptdata ) )
		wp_localize_script( 'hoverIntent', 'hootData', $scriptdata );
	// Temp Fix WP 5.5 => localize replaces 'jquery' with 'jquery-core', however in 5.5 jquery is loaded with id 'jquery' and not 'jquery-core' (unless
	// 'Enable jQuery Migrate Helper' plugin is activated). Hence use hoverIntent instead of 'jquery' (aka jquery-core) till we get a permanent fix
	// @see https://stackoverflow.com/questions/29723872/

}
endif;

/**
 * Load stylesheets for the front end.
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'magnb_enqueue_styles' ) ) :
function magnb_enqueue_styles() {

	/* Load Google Fonts if 'google-fonts' is active. */
	$style_uri = magnb_google_fonts_enqueue_url();
	if ( $style_uri )
		wp_enqueue_style( 'magnb-googlefont', $style_uri, array(), null );

	/* Load font awesome if 'font-awesome' is active. */
	if ( apply_filters( 'hoot_force_theme_fa', true, 'frontend' ) )
		wp_deregister_style( 'font-awesome' ); // Bug Fix for plugins using older font-awesome library
	$style_uri = hoot_locate_style( hoot_data()->liburi . 'fonticons/font-awesome' );
	wp_enqueue_style( 'font-awesome', $style_uri, false, '5.0.10' );
	add_action('wp_head', 'magnb_preload_fonticon', 5); // @1 doesnt work from within 'wp_enqueue_scripts', while @8 is too late and preload gets added after font-awesome <style>

}
endif;

/**
 * Preload webfont to help with Page Speed
 *
 * @since 1.9.15
 */
if ( !function_exists( 'magnb_preload_fonticon' ) ) :
function magnb_preload_fonticon() { ?>
<link rel="preload" href="<?php echo hoot_data()->liburi; ?>fonticons/webfonts/fa-solid-900.woff2" as="font" crossorigin="anonymous">
<link rel="preload" href="<?php echo hoot_data()->liburi; ?>fonticons/webfonts/fa-regular-400.woff2" as="font" crossorigin="anonymous">
<link rel="preload" href="<?php echo hoot_data()->liburi; ?>fonticons/webfonts/fa-brands-400.woff2" as="font" crossorigin="anonymous">
<?php }
endif;

/**
 * Dequeue font awesome from frontend if a similar handle exists (registered by another plugin)
 * but it is already enqueued using the theme
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'magnb_dequeue_fontawesome' ) ) :
function magnb_dequeue_fontawesome() {
	if ( wp_style_is( 'fontawesome' ) )
		wp_dequeue_style( 'fontawesome' );
}
endif;