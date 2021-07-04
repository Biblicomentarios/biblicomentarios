<?php
/**
 * Functions for outputting common site data in the '<head>' area of a site.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

# Adds common theme items to <head>.
add_action( 'wp_head', 'hoot_meta_charset', 0 );
add_action( 'wp_head', 'hoot_meta_viewport', 1 );
add_action( 'wp_head', 'hoot_meta_generator', 1 );
add_action( 'wp_head', 'hoot_link_pingback', 3 );

/**
 * Adds the meta charset to the header.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_meta_charset() {
	printf( '<meta charset="%s" />' . "\n", esc_attr( get_bloginfo( 'charset' ) ) );
}

/**
 * Adds the meta viewport to the header.
 *
 * @since 3.0.0
 * @access public
 */
function hoot_meta_viewport() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
 * Adds the theme generator meta tag. This is particularly useful for checking theme users version
 * when handling support requests.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_meta_generator() {
	printf( '<meta name="generator" content="%s %s" />' . "\n", esc_attr( hoot_data()->template_name ), esc_attr( hoot_data()->template_version ) );
}

/**
 * Adds the pingback link to the header.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_link_pingback() {
	if ( is_singular() && pings_open() )
		printf( '<link rel="pingback" href="%s" />' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
}