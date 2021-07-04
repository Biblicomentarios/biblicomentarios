<?php

/**
 * The core plugin class
 *
 * @since      1.0.0
 */

class Waka_Bulk_Page {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Also set the hooks for the admin area
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'WAKA_BULK_PAGE_VERSION' ) ) {
            $this->version = WAKA_BULK_PAGE_VERSION;
        } else {
            $this->version = '1.0.2';
        }
        $this->plugin_name = 'waka-bulk-page';

        $this->define_admin_hooks();
    }


    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Waka_Bulk_Page_Admin( $this->get_plugin_name(), $this->get_version() );

        add_action( 'plugins_loaded', array($plugin_admin, 'load_plugin_textdomain') );
        add_action( 'admin_enqueue_scripts', array($plugin_admin, 'enqueue_styles') );
        add_action( 'admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts') );
        add_action( 'admin_menu', array($plugin_admin, 'menu'));
        add_action( "plugin_action_links_" . WAKA_BULK_PAGE_BASENAME, array(&$this, 'add_settings_link') );
    }

    /**
     * Adds a link to the Plugins admin page to this plugin interface
     *
     * @since   1.0.0
     * @param $links
     * @return array
     */
    public function add_settings_link( $links ) {
        $links = array_merge( array('<a href="' . admin_url('edit.php?post_type=page&page=' . $this->plugin_name) . '">' . __('Settings') . '</a>'), $links);
        return $links;
    }

    /**
     * Get the name of the plugin (unique ID)
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }


    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
