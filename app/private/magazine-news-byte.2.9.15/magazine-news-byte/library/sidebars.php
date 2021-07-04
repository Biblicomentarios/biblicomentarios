<?php
/**
 * Helper functions for working with the WordPress sidebar system. This file creates a simple
 * function for registering HTML5-ready sidebars instead of the default WordPress unordered lists.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Wrapper function for WordPress' register_sidebar() function.
 * The argument passed must have the ID, name, and description of the sidebar.
 *
 * @since 3.0.0
 * @access public
 * @param array $args
 * @return string Sidebar ID.
 */
function hoot_register_sidebar( $args ) {

	// Set up some default sidebar arguments.
	$defaults = array(
		'id'            => '',
		'name'          => '',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>'
	);

	// Parse the arguments.
	$args = wp_parse_args( $args, apply_filters( 'hoot_sidebar_defaults', $defaults ) );

	// Remove action.
	remove_action( 'widgets_init', '__return_false', 95 );

	// Register the sidebar.
	return register_sidebar( apply_filters( 'hoot_sidebar_args', $args ) );
}

# Compatibility for when a theme doesn't register any sidebars.
add_action( 'widgets_init', '__return_false', 95 );

/**
 * Function for grabbing a dynamic sidebar name.
 *
 * @since 3.0.0
 * @access public
 * @global array $wp_registered_sidebars
 * @param string $sidebar_id
 * @return string
 */
function hoot_get_sidebar_name( $sidebar_id ) {
	global $wp_registered_sidebars;
	return isset( $wp_registered_sidebars[ $sidebar_id ] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : '';
}

/**
 * Checks if a widget exists.  Pass in the widget class name.  This function is useful for
 * checking if the widget exists before directly calling 'the_widget()' within a template.
 *
 * @since 3.0.0
 * @access public
 * @param string $widget
 * @return bool
 */
function hoot_widget_exists( $widget ) {
	return isset( $GLOBALS['wp_widget_factory']->widgets[ $widget ] );
}