<?php
/**
 * Hoot Framework v3 Library
 * Hoot framework is the engine behind wpHoot Themes and Plugins
 *
 * Loads up all the necessary libraries and functions.
 * This file should be loaded before anything else.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/* Define parent theme, and child theme constants. */
add_action( 'after_setup_theme', 'hoot_lib_constants', 1 );

/* Load the core functions/classes */
add_action( 'after_setup_theme', 'hoot_lib_core', 2 );

/**
 * Defines the constant paths for use throughout the theme.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
if ( !function_exists( 'hoot_lib_constants' ) ) :
function hoot_lib_constants() {

	// Set Hoot Version.
	hoot_set_data( 'hoot_version', '3.0.2' );

	// Theme directory paths
	hoot_set_data( 'template_dir', trailingslashit( get_template_directory() ) );
	hoot_set_data( 'child_dir',    trailingslashit( get_stylesheet_directory() ) );
	// Theme directory URIs
	hoot_set_data( 'template_uri', trailingslashit( get_template_directory_uri() ) );
	hoot_set_data( 'child_uri',    trailingslashit( get_stylesheet_directory_uri() ) );

	// Set Theme Location Constants
	hoot_set_data( 'libdir',       trailingslashit( hoot_data()->template_dir . 'library' ) );
	hoot_set_data( 'liburi',       trailingslashit( hoot_data()->template_uri . 'library' ) );
	hoot_set_data( 'incdir',       trailingslashit( hoot_data()->template_dir . 'include' ) );
	hoot_set_data( 'incuri',       trailingslashit( hoot_data()->template_uri . 'include' ) );

	// Set theme detail Constants
	hoot_set_data( 'theme', wp_get_theme() );
	if ( is_child_theme() ) {
		hoot_set_data( 'childtheme_version',  hoot_data( 'theme' )->get( 'Version' ) );
		hoot_set_data( 'childtheme_name',     hoot_data( 'theme' )->get( 'Name' ) );
		hoot_set_data( 'childtheme_author_uri',hoot_data( 'theme' )->get( 'AuthorURI' ) );
		if ( is_object( hoot_data( 'theme' )->parent() ) ) {
			hoot_set_data( 'template_version',    hoot_data( 'theme' )->parent()->get( 'Version' ) );
			hoot_set_data( 'template_name',       hoot_data( 'theme' )->parent()->get( 'Name' ) );
			hoot_set_data( 'template_author',     hoot_data( 'theme' )->parent()->get( 'Author' ) );
			hoot_set_data( 'template_author_uri', hoot_data( 'theme' )->parent()->get( 'AuthorURI' ) );
		} else {
			hoot_set_data( 'template_version',    '1.0' );
			hoot_set_data( 'template_name',       'Magazine News Byte' );
			hoot_set_data( 'template_author',     'wpHoot' );
			hoot_set_data( 'template_author_uri', 'https://wphoot.com/' );
		}
	} else {
		hoot_set_data( 'template_version',    hoot_data( 'theme' )->get( 'Version' ) );
		hoot_set_data( 'template_name',       hoot_data( 'theme' )->get( 'Name' ) );
		hoot_set_data( 'template_author',     hoot_data( 'theme' )->get( 'Author' ) );
		hoot_set_data( 'template_author_uri', hoot_data( 'theme' )->get( 'AuthorURI' ) );
	}

	// Custom allowed tags to accomodate microdata schema to be used in wp_kses()
	global $allowedposttags;
	$hootallowedtags = $allowedposttags;
	$hootallowedtags[ 'time' ] = array( 'id' => 1, 'class' => 1, 'datetime' => 1, 'title' => 1 );
	$hootallowedtags[ 'meta' ] = array( 'content' => 1 );
	$hootallowedtags[ 'style' ] = array( 'type' => 1 );
	foreach ( $hootallowedtags as $key => $value ) {
		if ( !empty( $value ) ) $hootallowedtags[ $key ]['itemprop'] = $hootallowedtags[ $key ]['itemscope'] = $hootallowedtags[ $key ]['itemtype'] = 1;
	}
	hoot_set_data( 'hootallowedtags', $hootallowedtags );

}
endif;

/**
 * Loads the core classes & functions. These files are needed before loading anything else in the 
 * theme because they have required functions for use. Many of the files run filters that 
 * may be removed using child theme or plugins.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
if ( !function_exists( 'hoot_lib_core' ) ) :
function hoot_lib_core() {

	/* Load language functions */
	require_once( hoot_data()->libdir . 'i18n.php' );
	/* Load schema attributes */
	require_once( hoot_data()->libdir . 'attr-schema.php' );
	/* Load context functions */
	require_once( hoot_data()->libdir . 'context.php' );
	/* Load up functions hooked to filters */
	require_once( hoot_data()->libdir . 'filters.php' );
	/* Load the data set functions => also needed for sanitization. */
	require_once( hoot_data()->libdir . 'enum.php' );
	/* Load Sanitization functions. */
	require_once( hoot_data()->libdir . 'sanitization.php' );
	/* Load Helper functions */
	require_once( hoot_data()->libdir . 'helpers.php' );
	/* Load Media functions */
	require_once( hoot_data()->libdir . 'media.php' );
	/* Load functions hooked to head */
	require_once( hoot_data()->libdir . 'head.php' );
	/* Load the scripts functions. */
	require_once( hoot_data()->libdir . 'scripts.php' );
	/* Load the styles functions. */
	require_once( hoot_data()->libdir . 'styles.php' );
	/* Load the Taxonomy Image extension */
	require_once( hoot_data()->libdir . 'taxonomy-image.php' );
	/* Load template location functions. */
	require_once( hoot_data()->libdir . 'locations.php' );
	/* Load several theme template functions. */
	require_once( hoot_data()->libdir . 'template.php' );
	/* Load Sidebar functions */
	require_once( hoot_data()->libdir . 'sidebars.php' );
	/* Load Color functions */
	require_once( hoot_data()->libdir . 'color.php' );
	/* Load the Customize extension */
	require_once( hoot_data()->libdir . 'customize/customize.php' );
	/* Load the Style Builder class */
	require_once( hoot_data()->libdir . 'style-builder.php' );

}
endif;

/* Create an empty object for storing hoot data */
global $hoot_data;
if ( !isset( $hoot_data ) || !is_object( $hoot_data ) )
	$hoot_data = new stdClass();

/**
 * This function is useful for quickly grabbing data
 *
 * @since 3.0.0
 * @access public
 * @param string
 * @return mixed
 */
if ( !function_exists( 'hoot_data' ) ) :
function hoot_data( $key = '', $subkey = '' ) {
	global $hoot_data;

	// Return entire data object if no key provided
	if ( ! $key ) {
		return $hoot_data;
	}

	// Return data value
	elseif ( $key && is_string( $key ) ) {
		if ( isset( $hoot_data->$key ) ) {

			if ( !$subkey || ( !is_string( $subkey ) && !is_integer( $subkey ) ) )
				return $hoot_data->$key;

			if ( is_object( $hoot_data->$key ) )
				return ( isset( $hoot_data->$key->$subkey ) ) ? $hoot_data->$key->$subkey : null;

			if ( is_array( $hoot_data->$key ) ) {
				$arr = $hoot_data->$key;
				return ( isset( $arr[ $subkey ] ) ) ? $arr[ $subkey ] : null;
			}

		} else {

			// $key has not been set in $hoot_data
			return null;

		}
	}

	// $key provided but isn't a string - Nothing!

}
endif;
/* Declare 'hoot_get_data' for brevity */
if ( !function_exists( 'hoot_get_data' ) ) :
function hoot_get_data( $key = '', $subkey = '' ) {
	return hoot_data( $key, $subkey );
}
endif;

/**
 * Sets properties of the hoot_data class. This function is useful for quickly setting data
 *
 * @since 3.0.0
 * @access public
 * @param string
 * @param mixed
 * @param bool $override
 * @return void
 */
if ( !function_exists( 'hoot_set_data' ) ) :
function hoot_set_data( $key, $value, $override = true ) {
	global $hoot_data;
	if ( !isset( $hoot_data->$key ) || $override )
		$hoot_data->$key = $value;
}
endif;

/**
 * Unsets properties of the hoot_data class. This function is useful for quickly setting data
 *
 * @since 3.0.0
 * @access public
 * @param string
 * @return void
 */
if ( !function_exists( 'hoot_unset_data' ) ) :
function hoot_unset_data( $key ) {
	global $hoot_data;
	if ( isset( $hoot_data->$key ) )
		unset( $hoot_data->$key );
}
endif;

/* Setup complete */
do_action( 'hoot_library_loaded' );