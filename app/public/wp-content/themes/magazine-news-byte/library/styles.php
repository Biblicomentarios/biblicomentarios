<?php
/**
 * Functions for handling theme main stylesheets in the frontend.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/* Register Main styles. */
add_action( 'wp_enqueue_scripts', 'hoot_register_styles', 0 );

/* Load Main styles. It's a good practice to load any other stylesheet before the main style. Hence users can enqueue custom stylesheets at default priority 10, so that the main style.css is always loaded at the end. */
add_action( 'wp_enqueue_scripts', 'hoot_enqueue_styles', 12 );

/* Load the development stylsheet (unminified) in script debug mode. */
add_filter( 'stylesheet_uri', 'hoot_min_stylesheet_uri', 5, 2 );

/* Filters the WP locale stylesheet. */
// Child theme users can add this filter if they need it.
// add_filter( 'locale_stylesheet_uri', 'hoot_locale_stylesheet_uri', 5 );

/* Load admin stylesheet files */
add_action( 'admin_enqueue_scripts', 'hoot_register_adminstyles' );

/**
 * Registers stylesheets
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_register_styles() {

	/* Get styles. */
	$styles = hoot_get_styles();

	/* Loop through each style and register it. */
	foreach ( $styles as $style => $args ) {

		$defaults = array( 
			'handle'  => $style, 
			'src'     => '',
			'deps'    => null,
			'version' => false,
			'media'   => 'all'
		);

		$args = wp_parse_args( $args, $defaults );

		if ( !empty( $args['src'] ) ) {
			wp_register_style(
				sanitize_key( $args['handle'] ),
				esc_url( $args['src'] ),
				is_array( $args['deps'] ) ? $args['deps'] : null,
				preg_replace( '/[^a-z0-9_\-.]/', '', strtolower( $args['version'] ) ),
				esc_attr( $args['media'] )
			);
		}

	}

}

/**
 * Tells WordPress to load the styles using the wp_enqueue_style() function.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_enqueue_styles() {

	/* Get styles. */
	$styles = hoot_get_styles();

	/* Loop through each style and enqueue it. */
	foreach ( $styles as $style => $args )
		if ( !empty( $args['src'] ) )
			wp_enqueue_style( sanitize_key( $style ) );

}

/**
 * Returns an array of the available styles for use in themes.
 *
 * @since 3.0.0
 * @access public
 * @return array
 */
function hoot_get_styles() {

	/* Initialize */
	$styles = array();

	/* If a child theme is active, add the parent theme's style. */
	// Cannot use 'hoot_locate_style()' as the function will always return child
	// theme stylesheet. Hence we have to manually locate and add parent stylesheet.
	if ( is_child_theme() ) {

		if ( defined( 'HOOT_DEBUG' ) )
			$loadminified = ( HOOT_DEBUG ) ? false : true;
		else
			$loadminified = hoot_get_mod( 'load_minified', 0 );

		/* Get the parent theme stylesheet (if a '.min' version of the stylesheet exists, use it) */
		if ( $loadminified && file_exists( hoot_data()->template_dir . "style.min.css" ) )
			$src = hoot_data()->template_uri . "style.min.css";
		else
			// We can skip file_exists for src as parent style.css will always be there.
			$src = hoot_data()->template_uri . "style.css";

		$styles['hoot-style'] = array(
			'src' => $src,
			'version' => hoot_data()->template_version,
			);
	}

	/* Add the active theme style. */
	$handle = ( is_child_theme() ) ? 'hoot-child-style' : 'hoot-style';
	$styles[ $handle ] = array(
		'src' => get_stylesheet_uri(),
		'version' => ( is_child_theme() ) ? hoot_data()->childtheme_version : hoot_data()->template_version,
		);

	/* Return the array of styles. */
	return apply_filters( 'hoot_styles', $styles );
}

/**
 * Filters the 'stylesheet_uri' returned by get_stylesheet_uri() to allow loading minimized
 * version of main 'style.css' file. It will detect if a 'style.min.css' file is available
 * and use it if HOOT_DEBUG is disabled.
 *
 * @since 3.0.0
 * @access public
 * @param string $stylesheet_uri The URI of the active theme's stylesheet.
 * @param string $stylesheet_dir_uri The directory URI of the active theme's stylesheet.
 * @return string $stylesheet_uri
 */
function hoot_min_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {

	if ( defined( 'HOOT_DEBUG' ) )
		$loadminified = ( HOOT_DEBUG ) ? false : true;
	else
		$loadminified = hoot_get_mod( 'load_minified', 0 );

	/* Use the .min stylesheet if available. */
	if ( $loadminified ) {

		/* Remove the stylesheet directory URI from the file name. */
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );

		/* Change the stylesheet name to 'style.min.css'. */
		$stylesheet = str_replace( '.css', ".min.css", $stylesheet );

		/* If the stylesheet exists in the stylesheet directory, set the stylesheet URI to the dev stylesheet. */
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $stylesheet ) )
			$stylesheet_uri = esc_url( trailingslashit( $stylesheet_dir_uri ) . $stylesheet );

	}

	/* Return the theme stylesheet. */
	return $stylesheet_uri;

}

/**
 * Filters 'locale_stylesheet_uri' with a more robust version for checking locale/language/region/direction
 * stylesheets.
 *
 * @since 3.0.0
 * @access public
 * @param string  $stylesheet_uri
 * @return string
 */
function hoot_locale_stylesheet_uri( $stylesheet_uri ) {
	$locale_style = hoot_get_locale_style();
	return $locale_style ? esc_url( $locale_style ) : $stylesheet_uri;
}

/**
 * Searches for a locale stylesheet.  This function looks for stylesheets in the 'css' folder in the following
 * order:  1) $lang-$region.css, 2) $region.css, 3) $lang.css, and 4) $text_direction.css.  It first checks
 * the child theme for these files.  If they are not present, it will check the parent theme.  This is much
 * more robust than the WordPress locale stylesheet, allowing for multiple variations and a more flexible
 * hierarchy.
 *
 * @since 3.0.0
 * @access public
 * @return string
 */
function hoot_get_locale_style() {

	$styles = array();

	// Get the locale, language, and region.
	$locale = strtolower( str_replace( '_', '-', get_locale() ) );
	$lang   = strtolower( hoot_get_language() );
	$region = strtolower( hoot_get_region() );

	$styles[] = "css/{$locale}.css";

	if ( $region !== $locale )
		$styles[] = "css/{$region}.css";

	if ( $lang !== $locale )
		$styles[] = "css/{$lang}.css";

	$styles[] = is_rtl() ? 'css/rtl.css' : 'css/ltr.css';

	foreach ( $styles as $style ) {
		$uri = hoot_locate_style( $style );
		if ( !empty( $uri ) ) return $uri;
	}
	return '';
}


/**
 * Registers the admin stylesheet files.  The function does not load the stylesheet.  
 * It merely registers it with WordPress.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_register_adminstyles() {
	if ( apply_filters( 'hoot_force_theme_fa', true, 'admin' ) )
		wp_deregister_style( 'font-awesome' ); // Bug Fix for plugins using older font-awesome library
	$style_uri = hoot_locate_style( hoot_data()->liburi . 'fonticons/font-awesome' );
	wp_register_style( 'font-awesome', $style_uri, false, '5.0.10' );
}