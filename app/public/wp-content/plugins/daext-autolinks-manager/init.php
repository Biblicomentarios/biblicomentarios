<?php

/*
Plugin Name: Autolinks Manager
Description: Generates autolinks for your WordPress website.
Version: 1.10.01
Author: DAEXT
Author URI: https://daext.com
Text Domain: daext-autolinks-manager
*/

//Prevent direct access to this file
if ( ! defined('WPINC')) {
    die();
}

//Class shared across public and admin
require_once(plugin_dir_path(__FILE__) . 'shared/class-daextam-shared.php');

//Public
require_once(plugin_dir_path(__FILE__) . 'public/class-daextam-public.php');
add_action('plugins_loaded', array('Daextam_Public', 'get_instance'));

//Perform the Gutenberg related activities only if Gutenberg is present
if ( function_exists( 'register_block_type' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'blocks/src/init.php' );
}

//Admin
if (is_admin() && ( ! defined('DOING_AJAX') || ! DOING_AJAX)) {

    //Admin
    require_once(plugin_dir_path(__FILE__) . 'admin/class-daextam-admin.php');
    add_action('plugins_loaded', array('Daextam_Admin', 'get_instance'));

    //Activate
    register_activation_hook(__FILE__, array(Daextam_Admin::get_instance(), 'ac_activate'));

}

//Ajax
if (defined('DOING_AJAX') && DOING_AJAX) {

    //Admin
    require_once(plugin_dir_path(__FILE__) . 'class-daextam-ajax.php');
    add_action('plugins_loaded', array('Daextam_Ajax', 'get_instance'));

}