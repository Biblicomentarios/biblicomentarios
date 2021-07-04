<?php
/**
 * Register custom theme menus
 * This file is loaded via the 'after_setup_theme' hook at priority '10'
 *
 * @package    Magazine News Byte
 * @subpackage Theme
 */

/**
 * Registers nav menu locations.
 *
 * @since 1.0
 * @access public
 * @return void
 */
add_action( 'init', 'magnb_base_register_menus', 5 );
function magnb_base_register_menus() {
	register_nav_menu( 'hoot-primary-menu', _x( 'Primary Menu', 'nav menu location', 'magazine-news-byte' ) );
}