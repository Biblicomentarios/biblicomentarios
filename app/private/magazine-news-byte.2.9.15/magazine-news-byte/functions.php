<?php
/**
 *                  _   _             _   
 *  __      ___ __ | | | | ___   ___ | |_ 
 *  \ \ /\ / / '_ \| |_| |/ _ \ / _ \| __|
 *   \ V  V /| |_) |  _  | (_) | (_) | |_ 
 *    \_/\_/ | .__/|_| |_|\___/ \___/ \__|
 *           |_|                          
 * -------------------------------------------
 * ------- code inspired from Hybrid, --------
 * -- Underscores Theme, Customizer Library --
 * -- (see readme file for copyright info.) --
 * -------------------------------------------
 *
 * :: Theme's main functions file ::::::::::::
 * :: Initialize and setup the theme :::::::::
 *
 * To modify this theme, its a good idea to create a child theme. This way you can easily update
 * the main theme without loosing your changes. To know more about how to create child themes 
 * @see http://codex.wordpress.org/Theme_Development
 * @see http://codex.wordpress.org/Child_Themes
 *
 * Hooks, Actions and Filters are used throughout this theme. You should be able to do most of your
 * customizations without touching the main code. For more information on hooks, actions, and filters
 * @see http://codex.wordpress.org/Plugin_API
 *
 * @package    Magazine News Byte
 */

/**
 * Uncomment the line below to load unminified CSS and JS, and add other developer data to code.
 * - You can set this to true (default) for loading unminified files (useful for development/debugging)
 * - Or set it to false for loading minified files (for production i.e. live site)
 * 
 * NOTE: If you uncomment this line, HOOT_DEBUG value will override any option for minifying
 * files (if available) set via the theme options (customizer) in WordPress Admin
 */
// define( 'HOOT_DEBUG', true );
if ( !defined( 'HOOT_DEBUG' ) && defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG )
	define( 'HOOT_DEBUG', true );

// Get the template directory and make sure it has a trailing slash.
$hoot_template_dir = trailingslashit( get_template_directory() );

// Load the Core Hoot Framework Library files
require_once( $hoot_template_dir . 'library/init.php' );

// Load up the Theme files
require_once( $hoot_template_dir . 'include/hoot-theme.php' );