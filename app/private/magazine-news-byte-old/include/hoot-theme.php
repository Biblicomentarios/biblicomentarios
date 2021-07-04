<?php
/**
 * Hoot Theme files
 *
 * @package    Magazine News Byte
 * @subpackage Theme
 */

/* Load theme includes. Must keep priority 10 for theme constants to be available. */
add_action( 'after_setup_theme', 'magnb_includes', 10 );

/**
 * Loads the theme files supported by themes and template-related functions/classes. Functionality 
 * in these files should not be expected within the theme setup function.
 *
 * @since 1.0
 * @access public
 * @return void
 */
function magnb_includes() {

	/* Load the Theme Specific HTML attributes */
	require_once( hoot_data()->incdir . 'attr.php' );
	/* Load enqueue functions */
	require_once( hoot_data()->incdir . 'enqueue.php' );
	/* Load the dynamic css functions. */
	require_once( hoot_data()->incdir . 'css.php' );
	/* Load template tags. */
	require_once( hoot_data()->incdir . 'template-helpers.php' );
	/* Set the fonts. */
	require_once( hoot_data()->incdir . 'admin/fonts.php' );
	/* Set image sizes. */
	require_once( hoot_data()->incdir . 'admin/media.php' );
	/* Set menus */
	require_once( hoot_data()->incdir . 'admin/menus.php' );
	/* Set sidebars */
	require_once( hoot_data()->incdir . 'admin/sidebars.php' );
	/* Load TGMPA Class */
	if ( apply_filters( 'magnb_load_tgmpa', file_exists( hoot_data()->incdir . 'admin/class-tgm-plugin-activation.php' ) ) )
		require_once( hoot_data()->incdir . 'admin/class-tgm-plugin-activation.php' );
	/* Load Customizer Options */
	if ( apply_filters( 'magnb_customize_load_trt', file_exists( hoot_data()->incdir . 'admin/trt-customize-pro/class-customize.php' ) ) )
		require_once( hoot_data()->incdir . 'admin/trt-customize-pro/class-customize.php' );
	require_once( hoot_data()->incdir . 'admin/customizer-options.php' );
	/* Load the about page. */
	if ( apply_filters( 'magnb_load_about', file_exists( hoot_data()->incdir . 'admin/about.php' ) ) )
		require_once( hoot_data()->incdir . 'admin/about.php' );
	/* Load the theme setup file */
	require_once( hoot_data()->incdir . 'theme-setup.php' );

	/* Load deprecated functions */
	require_once( hoot_data()->incdir . 'deprecated.php' );

}

/* Transition filter for version 2.9.0 : Doesnt resolve customizer but hopefully user will visit atleast one admin screen before customizer */
add_filter( 'hoot_get_mods', 'magnb_transition_get_mods', 2 );

/**
 * Function for seamless transition for changed option/values in version 2.9.0
 * Updated 2.9.0 for frontpage sidebar option
 *
 * @since 2.9.0
 * @access public
 * @return void
 */
function magnb_transition_get_mods( $mods ) {

	if ( !isset( $mods['sidebar_fp'] ) ) {
		if ( 'page' == get_option('show_on_front' ) ) {
			if ( function_exists( 'hoot_get_metaoption' ) && hoot_get_metaoption( 'sidebar_type', get_option( 'page_on_front' ) ) == 'custom' ) {
				$mods['sidebar_fp'] = hoot_get_metaoption( 'sidebar', get_option( 'page_on_front' ) );
			} else {
				$mods['sidebar_fp'] = 'full-width';
			}
		} else {
			$mods['sidebar_fp'] = ( isset( $mods['sidebar_archives'] ) ) ? $mods['sidebar_archives'] : ( isset( $mods['sidebar'] ) ? $mods['sidebar'] : 'wide-right' );
		}
		set_theme_mod( 'sidebar_fp', $mods['sidebar_fp'] );
	}

	// var_dump(get_theme_mods());exit;
	return $mods;
}

/* Theme Setup complete */
do_action( 'magnb_loaded' );