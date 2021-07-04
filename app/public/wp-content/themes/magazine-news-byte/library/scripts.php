<?php
/**
 * Functions for handling JavaScript.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/* Register scripts. */
add_action( 'wp_enqueue_scripts', 'hoot_register_scripts', 0 );

/* Load scripts. It's a good practice to load any other script before the main script. Hence users can enqueue scripts at default priority 10, so that the main script.js is always loaded at the end. */
add_action( 'wp_enqueue_scripts', 'hoot_enqueue_scripts', 12 );

/**
 * Registers JavaScript files using the wp_register_script() function.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_register_scripts() {

	/* Get scripts. */
	$scripts = hoot_get_scripts();

	/* Loop through each script and register it. */
	foreach ( $scripts as $script => $args ) {

		$defaults = array( 
			'handle'    => $script, 
			'src'       => '',
			'deps'      => null,
			'version'   => false,
			'in_footer' => true
		);

		$args = wp_parse_args( $args, $defaults );

		if ( !empty( $args['src'] ) ) {
			wp_register_script(
				sanitize_key( $args['handle'] ),
				esc_url( $args['src'] ),
				is_array( $args['deps'] ) ? $args['deps'] : null,
				preg_replace( '/[^a-z0-9_\-.]/', '', strtolower( $args['version'] ) ),
				is_bool( $args['in_footer'] ) ? $args['in_footer'] : ''
			);
		}

	}

}

/**
 * Tells WordPress to load the scripts using the wp_enqueue_script() function.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_enqueue_scripts() {

	/* Get scripts. */
	$scripts = hoot_get_scripts();

	/* Loop through each script and enqueue it. */
	foreach ( $scripts as $script => $args )
		if ( !empty( $args['src'] ) )
			wp_enqueue_script( sanitize_key( $script ) );

}

/**
 * Returns an array of the available scripts for use in themes.
 *
 * @since 3.0.0
 * @access public
 * @return array
 */
function hoot_get_scripts() {

	/* Initialize */
	$scripts = array();

	if ( defined( 'HOOT_DEBUG' ) )
		$loadminified = ( HOOT_DEBUG ) ? false : true;
	else
		$loadminified = hoot_get_mod( 'load_minified', 0 );

	/* If a child theme is active, add the parent theme's scripts. */
	// Cannot use 'hoot_locate_script()' as the function will always return child
	// theme script. Hence we have to manually locate and add parent script.
	if ( is_child_theme() ) {

		/* Get the parent theme script (if a '.min' version of the script exists, use it) */
		if ( $loadminified && file_exists( trailingslashit( hoot_data()->template_dir ) . 'script.min.js' ) ) {
			$src = hoot_data()->template_uri . 'script.min.js';
		} elseif ( file_exists( trailingslashit( hoot_data()->template_dir ) . 'script.js' ) ) {
			$src = hoot_data()->template_uri . 'script.js';
		}

		if ( !empty( $src ) )
			$scripts['hoot-template-script'] = array(
				'src' => $src,
				'version' => hoot_data()->template_version,
				);
	}

	/* Add the active theme script (if a '.min' version of the script exists, use it) */
	if ( $loadminified && file_exists( hoot_data()->child_dir . 'script.min.js' ) ) {
		$scriptsrc = hoot_data()->child_uri . 'script.min.js';
	} elseif ( file_exists( hoot_data()->child_dir . 'script.js' ) ) {
		$scriptsrc = hoot_data()->child_uri . 'script.js';
	}

	$handle = ( is_child_theme() ) ? 'hoot-child-script' : 'hoot-template-script';
	if ( !empty( $scriptsrc ) )
		$scripts[ $handle ] = array(
			'src' => $scriptsrc,
			'version' => ( is_child_theme() ) ? hoot_data()->childtheme_version : hoot_data()->template_version,
			);

	/* Return the array of scripts. */
	return apply_filters( 'hoot_scripts', $scripts );
}