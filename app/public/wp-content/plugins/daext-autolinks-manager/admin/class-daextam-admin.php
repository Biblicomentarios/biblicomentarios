<?php

/*
 * this class should be used to work with the administrative side of wordpress
 */

class Daextam_Admin
{

    protected static $instance = null;
    private $shared = null;

    private $screen_id_statistics = null;
    private $screen_id_autolinks = null;
    private $screen_id_categories = null;
    private $screen_id_term_groups = null;
	private $screen_id_help = null;
	private $screen_id_pro_version = null;
    private $screen_id_options = null;

    private function __construct()
    {

        //assign an instance of the plugin info
        $this->shared = Daextam_Shared::get_instance();

        //Load admin stylesheets and JavaScript
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        //Write in back end head
        add_action('admin_head', array($this, 'wr_admin_head'));

        //Add the admin menu
        add_action('admin_menu', array($this, 'me_add_admin_menu'));

        //Load the options API registrations and callbacks
        add_action('admin_init', array($this, 'op_register_options'));

        //Add the meta box
        add_action('add_meta_boxes', array($this, 'create_meta_box'));

        //Save the meta box
        add_action('save_post', array($this, 'save_meta_box'));

        //this hook is triggered during the creation of a new blog
        add_action('wpmu_new_blog', array($this, 'new_blog_create_options_and_tables'), 10, 6);

        //this hook is triggered during the deletion of a blog
        add_action('delete_blog', array($this, 'delete_blog_delete_options_and_tables'), 10, 1);

    }

    /*
     * return an instance of this class
     */
    public static function get_instance()
    {

        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;

    }

    /*
     * write in the admin head
     */
    public function wr_admin_head()
    {

        echo '<script type="text/javascript">';
        echo 'var daextamAjaxUrl = "' . admin_url('admin-ajax.php') . '";';
        echo 'var daextamNonce = "' . wp_create_nonce("daextam") . '";';
        echo 'var daextamAdminUrl ="' . get_admin_url() . '";';
        echo '</script>';

    }

    /*
     * Enqueue admin specific styles.
     */
    public function enqueue_admin_styles()
    {

        $screen = get_current_screen();

        //Menu Statistics
        if ($screen->id == $this->screen_id_statistics) {

            //Framework Menu
            wp_enqueue_style($this->shared->get('slug') . '-framework-menu',
                $this->shared->get('url') . 'admin/assets/css/framework/menu.css', array(), $this->shared->get('ver'));

            //Statistics Menu
            wp_enqueue_style($this->shared->get('slug') . '-menu-statistics',
                $this->shared->get('url') . 'admin/assets/css/menu-statistics.css', array(), $this->shared->get('ver'));

            //jQuery UI Tooltip
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-tooltip',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-tooltip.css', array(),
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_style($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-chosen-custom',
                $this->shared->get('url') . 'admin/assets/css/chosen-custom.css', array(), $this->shared->get('ver'));

        }

        //Menu Autolinks
        if ($screen->id == $this->screen_id_autolinks) {

            //Framework Menu
            wp_enqueue_style($this->shared->get('slug') . '-framework-menu',
                $this->shared->get('url') . 'admin/assets/css/framework/menu.css', array(), $this->shared->get('ver'));

            //Autolinks Menu
            wp_enqueue_style($this->shared->get('slug') . '-menu-autolinks',
                $this->shared->get('url') . 'admin/assets/css/menu-autolinks.css', array(), $this->shared->get('ver'));

            //jQuery UI Dialog
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog-custom',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog-custom.css', array(),
                $this->shared->get('ver'));

            //jQuery UI Tooltip
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-tooltip',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-tooltip.css', array(),
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_style($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-chosen-custom',
                $this->shared->get('url') . 'admin/assets/css/chosen-custom.css', array(), $this->shared->get('ver'));

        }

        //Menu Categories
        if ($screen->id == $this->screen_id_categories) {

            //Framework Menu
            wp_enqueue_style($this->shared->get('slug') . '-framework-menu',
                $this->shared->get('url') . 'admin/assets/css/framework/menu.css', array(), $this->shared->get('ver'));

            //Categories Menu
            wp_enqueue_style($this->shared->get('slug') . '-menu-categories',
                $this->shared->get('url') . 'admin/assets/css/menu-categories.css', array(), $this->shared->get('ver'));

            //jQuery UI Dialog
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog-custom',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog-custom.css', array(),
                $this->shared->get('ver'));

            //jQuery UI Tooltip
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-tooltip',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-tooltip.css', array(),
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_style($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-chosen-custom',
                $this->shared->get('url') . 'admin/assets/css/chosen-custom.css', array(), $this->shared->get('ver'));

        }

        //Menu Term Groups
        if ($screen->id == $this->screen_id_term_groups) {

            //Framework Menu
            wp_enqueue_style($this->shared->get('slug') . '-framework-menu',
                $this->shared->get('url') . 'admin/assets/css/framework/menu.css', array(), $this->shared->get('ver'));

            //Term Groups Menu
            wp_enqueue_style($this->shared->get('slug') . '-menu-term-groups',
                $this->shared->get('url') . 'admin/assets/css/menu-term-groups.css', array(),
                $this->shared->get('ver'));

            //jQuery UI Dialog
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog-custom',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog-custom.css', array(),
                $this->shared->get('ver'));

            //jQuery UI Tooltip
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-tooltip',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-tooltip.css', array(),
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_style($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-chosen-custom',
                $this->shared->get('url') . 'admin/assets/css/chosen-custom.css', array(), $this->shared->get('ver'));

        }

	    //Menu Help
	    if ($screen->id == $this->screen_id_help) {

		    //Pro Version Menu
		    wp_enqueue_style($this->shared->get('slug') . '-menu-help',
			    $this->shared->get('url') . 'admin/assets/css/menu-help.css', array(), $this->shared->get('ver'));

	    }

	    //Menu Pro Version
	    if ($screen->id == $this->screen_id_pro_version) {

		    //Pro Version Menu
		    wp_enqueue_style($this->shared->get('slug') . '-menu-pro-version',
			    $this->shared->get('url') . 'admin/assets/css/menu-pro-version.css', array(), $this->shared->get('ver'));

	    }

        //Menu Options
        if ($screen->id == $this->screen_id_options) {

            //Framework Options
            wp_enqueue_style($this->shared->get('slug') . '-framework-options',
                $this->shared->get('url') . 'admin/assets/css/framework/options.css', array(),
                $this->shared->get('ver'));

            //jQuery UI Tooltip
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-tooltip',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-tooltip.css', array(),
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_style($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-chosen-custom',
                $this->shared->get('url') . 'admin/assets/css/chosen-custom.css', array(), $this->shared->get('ver'));

        }

        $meta_box_post_types_a = $this->shared->get_post_types_with_ui();
        if (in_array($screen->id, $meta_box_post_types_a)) {

            //Post Editor
            wp_enqueue_style($this->shared->get('slug') . '-meta-box',
                $this->shared->get('url') . 'admin/assets/css/post-editor.css', array(), $this->shared->get('ver'));

            //Chosen
            wp_enqueue_style($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-chosen-custom',
                $this->shared->get('url') . 'admin/assets/css/chosen-custom.css', array(), $this->shared->get('ver'));

        }

    }

    /*
     * Enqueue admin-specific JavaScript.
     */
    public function enqueue_admin_scripts()
    {

        $wp_localize_script_data = array(
            'deleteText'         => esc_html__('Delete', 'daext-autolinks-manager'),
            'cancelText'         => esc_html__('Cancel', 'daext-autolinks-manager'),
            'chooseAnOptionText' => esc_html__('Choose an Option ...', 'daext-autolinks-manager'),
        );

        $screen = get_current_screen();

        //Menu Statistics
        if ($screen->id == $this->screen_id_statistics) {

            //Statistics Menu
            wp_enqueue_script($this->shared->get('slug') . '-menu-statistics',
                $this->shared->get('url') . 'admin/assets/js/menu-statistics.js', 'jquery', $this->shared->get('ver'));

            //jQuery UI Tooltip
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script($this->shared->get('slug') . '-jquery-ui-tooltip-init',
                $this->shared->get('url') . 'admin/assets/js/jquery-ui-tooltip-init.js', 'jquery',
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_script($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.js', array('jquery'),
                $this->shared->get('ver'));
            wp_enqueue_script($this->shared->get('slug') . '-chosen-init',
                $this->shared->get('url') . 'admin/assets/js/chosen-init.js', array('jquery'),
                $this->shared->get('ver'));
            wp_localize_script($this->shared->get('slug') . '-chosen-init', 'objectL10n', $wp_localize_script_data);

        }

        //Menu Autolinks
        if ($screen->id == $this->screen_id_autolinks) {

            //Autolinks Menu
            wp_enqueue_script($this->shared->get('slug') . '-menu-autolinks',
                $this->shared->get('url') . 'admin/assets/js/menu-autolinks.js', array('jquery', 'jquery-ui-dialog'),
                $this->shared->get('ver'));
            wp_localize_script($this->shared->get('slug') . '-menu-autolinks', 'objectL10n', $wp_localize_script_data);

            //jQuery UI Tooltip
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script($this->shared->get('slug') . '-jquery-ui-tooltip-init',
                $this->shared->get('url') . 'admin/assets/js/jquery-ui-tooltip-init.js', 'jquery',
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_script($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.js', array('jquery'),
                $this->shared->get('ver'));
            wp_enqueue_script($this->shared->get('slug') . '-chosen-init',
                $this->shared->get('url') . 'admin/assets/js/chosen-init.js', array('jquery'),
                $this->shared->get('ver'));
            wp_localize_script($this->shared->get('slug') . '-chosen-init', 'objectL10n', $wp_localize_script_data);

        }

        //Menu Categories
        if ($screen->id == $this->screen_id_categories) {

            //Autolinks Menu
            wp_enqueue_script($this->shared->get('slug') . '-menu-categories',
                $this->shared->get('url') . 'admin/assets/js/menu-categories.js', array('jquery', 'jquery-ui-dialog'),
                $this->shared->get('ver'));
            wp_localize_script($this->shared->get('slug') . '-menu-categories', 'objectL10n', $wp_localize_script_data);

            //jQuery UI Tooltip
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script($this->shared->get('slug') . '-jquery-ui-tooltip-init',
                $this->shared->get('url') . 'admin/assets/js/jquery-ui-tooltip-init.js', 'jquery',
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_script($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.js', array('jquery'),
                $this->shared->get('ver'));
            wp_enqueue_script($this->shared->get('slug') . '-chosen-init',
                $this->shared->get('url') . 'admin/assets/js/chosen-init.js', array('jquery'),
                $this->shared->get('ver'));
            wp_localize_script($this->shared->get('slug') . '-chosen-init', 'objectL10n', $wp_localize_script_data);

        }

        //Menu Term Groups
        if ($screen->id == $this->screen_id_term_groups) {

            //Autolinks Menu
            wp_enqueue_script($this->shared->get('slug') . '-menu-autolinks',
                $this->shared->get('url') . 'admin/assets/js/menu-term-groups.js', array('jquery', 'jquery-ui-dialog'),
                $this->shared->get('ver'));
            wp_localize_script($this->shared->get('slug') . '-menu-term-groups', 'objectL10n',
                $wp_localize_script_data);

            //jQuery UI Tooltip
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script($this->shared->get('slug') . '-jquery-ui-tooltip-init',
                $this->shared->get('url') . 'admin/assets/js/jquery-ui-tooltip-init.js', 'jquery',
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_script($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.js', array('jquery'),
                $this->shared->get('ver'));
            wp_enqueue_script($this->shared->get('slug') . '-chosen-init',
                $this->shared->get('url') . 'admin/assets/js/chosen-init.js', array('jquery'),
                $this->shared->get('ver'));
            wp_localize_script($this->shared->get('slug') . '-chosen-init', 'objectL10n', $wp_localize_script_data);

        }

        //Menu Options
        if ($screen->id == $this->screen_id_options) {

            //jQuery UI Tooltip
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script($this->shared->get('slug') . '-jquery-ui-tooltip-init',
                $this->shared->get('url') . 'admin/assets/js/jquery-ui-tooltip-init.js', 'jquery',
                $this->shared->get('ver'));

            //Chosen
            wp_enqueue_script($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.js', array('jquery'),
                $this->shared->get('ver'));
            wp_enqueue_script($this->shared->get('slug') . '-chosen-init',
                $this->shared->get('url') . 'admin/assets/js/chosen-init.js', array('jquery'),
                $this->shared->get('ver'));
            wp_localize_script($this->shared->get('slug') . '-chosen-init', 'objectL10n', $wp_localize_script_data);

        }

        $meta_box_post_types_a = $this->shared->get_post_types_with_ui();
        if (in_array($screen->id, $meta_box_post_types_a)) {

            //Chosen
            wp_enqueue_script($this->shared->get('slug') . '-chosen',
                $this->shared->get('url') . 'admin/assets/inc/chosen/chosen-min.js', array('jquery'),
                $this->shared->get('ver'));
            wp_enqueue_script($this->shared->get('slug') . '-chosen-init',
                $this->shared->get('url') . 'admin/assets/js/chosen-init.js', array('jquery'),
                $this->shared->get('ver'));
            wp_localize_script($this->shared->get('slug') . '-chosen-init', 'objectL10n', $wp_localize_script_data);

        }

    }

    /*
     * plugin activation
     */
    public function ac_activate($networkwide)
    {

        /*
         * delete options and tables for all the sites in the network
         */
        if (function_exists('is_multisite') and is_multisite()) {

            /*
             * if this is a "Network Activation" create the options and tables
             * for each blog
             */
            if ($networkwide) {

                //get the current blog id
                global $wpdb;
                $current_blog = $wpdb->blogid;

                //create an array with all the blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

                //iterate through all the blogs
                foreach ($blogids as $blog_id) {

                    //swith to the iterated blog
                    switch_to_blog($blog_id);

                    //create options and tables for the iterated blog
                    $this->ac_initialize_options();
                    $this->ac_create_database_tables();

                }

                //switch to the current blog
                switch_to_blog($current_blog);

            } else {

                /*
                 * if this is not a "Network Activation" create options and
                 * tables only for the current blog
                 */
                $this->ac_initialize_options();
                $this->ac_create_database_tables();

            }

        } else {

            /*
             * if this is not a multisite installation create options and
             * tables only for the current blog
             */
            $this->ac_initialize_options();
            $this->ac_create_database_tables();

        }

    }

    //create the options and tables for the newly created blog
    public function new_blog_create_options_and_tables($blog_id, $user_id, $domain, $path, $site_id, $meta)
    {

        global $wpdb;

        /*
         * if the plugin is "Network Active" create the options and tables for
         * this new blog
         */
        if (is_plugin_active_for_network('daext-autolinks-manager/init.php')) {

            //get the id of the current blog
            $current_blog = $wpdb->blogid;

            //switch to the blog that is being activated
            switch_to_blog($blog_id);

            //create options and database tables for the new blog
            $this->ac_initialize_options();
            $this->ac_create_database_tables();

            //switch to the current blog
            switch_to_blog($current_blog);

        }

    }

    //delete options and tables for the deleted blog
    public function delete_blog_delete_options_and_tables($blog_id)
    {

        global $wpdb;

        //get the id of the current blog
        $current_blog = $wpdb->blogid;

        //switch to the blog that is being activated
        switch_to_blog($blog_id);

        //create options and database tables for the new blog
        $this->un_delete_options();
        $this->un_delete_database_tables();

        //switch to the current blog
        switch_to_blog($current_blog);

    }

    /*
     * initialize plugin options
     */
    private function ac_initialize_options()
    {

	    foreach($this->shared->get('options') as $key => $value){
		    add_option($key, $value);
	    }

    }

    /*
     * Create the plugin database tables.
     */
    private function ac_create_database_tables()
    {

        global $wpdb;

        //Get the database character collate that will be appended at the end of each query
        $charset_collate = $wpdb->get_charset_collate();

        //check database version and create the database
        if (intval(get_option($this->shared->get('slug') . '_database_version'), 10) < 1) {

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            //create *prefix*_statistic
            $table_name = $wpdb->prefix . $this->shared->get('slug') . "_statistic";
            $sql        = "CREATE TABLE $table_name (
                statistic_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                post_id BIGINT,
                content_length BIGINT,
                auto_links BIGINT
            ) $charset_collate";
            dbDelta($sql);

            //create *prefix*_autolink
            $table_name = $wpdb->prefix . $this->shared->get('slug') . "_autolink";
            $sql        = "CREATE TABLE $table_name (
                autolink_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100),
                category_id BIGINT,
                keyword VARCHAR(255),
                url VARCHAR(2083),
                title VARCHAR(255),
                open_new_tab TINYINT(1),
                use_nofollow TINYINT(1),
                case_sensitive_search TINYINT(1),
                `limit` INT,
                priority INT,
                left_boundary SMALLINT,
                right_boundary SMALLINT,
                keyword_before VARCHAR(255),
                keyword_after VARCHAR(255),
                post_types TEXT,
                categories TEXT,
                tags TEXT,
                term_group_id BIGINT
            ) $charset_collate";
            dbDelta($sql);

            //create *prefix*_category
            $table_name = $wpdb->prefix . $this->shared->get('slug') . "_category";
            $sql        = "CREATE TABLE $table_name (
                category_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100),
                description VARCHAR(255)
            ) $charset_collate";
            dbDelta($sql);

            //create *prefix*_term
            $table_name = $wpdb->prefix . $this->shared->get('slug') . "_term_group";
            $query_part = '';
            for ($i = 1; $i <= 50; $i++) {
                $query_part .= 'post_type_' . $i . ' TEXT,';
                $query_part .= 'taxonomy_' . $i . ' TEXT,';
                $query_part .= 'term_' . $i . ' BIGINT';
                if ($i !== 50) {
                    $query_part .= ',';
                }
            }
            $sql = "CREATE TABLE $table_name (
                term_group_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100),
                $query_part
            ) $charset_collate";
            dbDelta($sql);

            //Update database version
            update_option($this->shared->get('slug') . '_database_version', "1");

        }

    }

    /*
     * Plugin delete.
     */
    static public function un_delete()
    {

        /*
         * Delete options and tables for all the sites in the network.
         */
        if (function_exists('is_multisite') and is_multisite()) {

            //get the current blog id
            global $wpdb;
            $current_blog = $wpdb->blogid;

            //create an array with all the blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

            //iterate through all the blogs
            foreach ($blogids as $blog_id) {

                //switch to the iterated blog
                switch_to_blog($blog_id);

                //create options and tables for the iterated blog
                Daextam_Admin::un_delete_options();
                Daextam_Admin::un_delete_database_tables();

            }

            //switch to the current blog
            switch_to_blog($current_blog);

        } else {

            /*
             * If this is not a multisite installation delete options and tables only for the current blog.
             */
            Daextam_Admin::un_delete_options();
            Daextam_Admin::un_delete_database_tables();

        }

    }

    /*
     * Delete plugin options.
     */
    static public function un_delete_options()
    {

        //assign an instance of Daextam_Shared
        $shared = Daextam_Shared::get_instance();

	    foreach($shared->get('options') as $key => $value){
		    delete_option($key);
	    }

    }

    /*
     * Delete plugin database tables.
     */
    static public function un_delete_database_tables()
    {

        //assign an instance of Daextam_Shared
        $shared = Daextam_Shared::get_instance();

        global $wpdb;

        $table_name = $wpdb->prefix . $shared->get('slug') . "_statistic";
        $sql        = "DROP TABLE $table_name";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . $shared->get('slug') . "_autolink";
        $sql        = "DROP TABLE $table_name";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . $shared->get('slug') . "_category";
        $sql        = "DROP TABLE $table_name";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . $shared->get('slug') . "_term_group";
        $sql        = "DROP TABLE $table_name";
        $wpdb->query($sql);

    }

    /*
     * Register the admin menu.
     */
    public function me_add_admin_menu()
    {

        add_menu_page(
            esc_html__('AM', 'daext-autolinks-manager'),
            esc_html__('Autolinks', 'daext-autolinks-manager'),
            'manage_options',
            $this->shared->get('slug') . '-statistics',
            array($this, 'me_display_menu_statistics'),
            'dashicons-admin-links'
        );

        $this->screen_id_statistics = add_submenu_page(
            $this->shared->get('slug') . '-statistics',
            esc_html__('AM - Statistics', 'daext-autolinks-manager'),
            esc_html__('Statistics', 'daext-autolinks-manager'),
            'manage_options',
            $this->shared->get('slug') . '-statistics',
            array($this, 'me_display_menu_statistics')
        );

        $this->screen_id_autolinks = add_submenu_page(
            $this->shared->get('slug') . '-statistics',
            esc_html__('AM - Autolinks', 'daext-autolinks-manager'),
            esc_html__('Autolinks', 'daext-autolinks-manager'),
            'manage_options',
            $this->shared->get('slug') . '-autolinks',
            array($this, 'me_display_menu_autolinks')
        );

        $this->screen_id_categories = add_submenu_page(
            $this->shared->get('slug') . '-statistics',
            esc_html__('AM - Categories', 'daext-autolinks-manager'),
            esc_html__('Categories', 'daext-autolinks-manager'),
            'manage_options',
            $this->shared->get('slug') . '-categories',
            array($this, 'me_display_menu_categories')
        );

        $this->screen_id_term_groups = add_submenu_page(
            $this->shared->get('slug') . '-statistics',
            esc_html__('AM - Term Groups', 'daext-autolinks-manager'),
            esc_html__('Term Groups', 'daext-autolinks-manager'),
            'manage_options',
            $this->shared->get('slug') . '-term-groups',
            array($this, 'me_display_menu_term_groups')
        );

	    $this->screen_id_help = add_submenu_page(
		    $this->shared->get('slug') . '-statistics',
		    esc_html__('AM - Help', 'daext-autolinks-manager'),
		    esc_html__('Help', 'daext-autolinks-manager'),
		    'manage_options',
		    $this->shared->get('slug') . '-help',
		    array($this, 'me_display_menu_help')
	    );

	    $this->screen_id_pro_version = add_submenu_page(
		    $this->shared->get('slug') . '-statistics',
		    esc_html__('AM - Help', 'daext-autolinks-manager'),
		    esc_html__('Pro Version', 'daext-autolinks-manager'),
		    'manage_options',
		    $this->shared->get('slug') . '-pro-version',
		    array($this, 'me_display_menu_pro_version')
	    );

        $this->screen_id_options = add_submenu_page(
            $this->shared->get('slug') . '-statistics',
            esc_html__('AM - Options', 'daext-autolinks-manager'),
            esc_html__('Options', 'daext-autolinks-manager'),
            'manage_options',
            $this->shared->get('slug') . '-options',
            array($this, 'me_display_menu_options')
        );

    }

    /*
     * includes the statistics view
     */
    public function me_display_menu_statistics()
    {
        include_once('view/statistics.php');
    }

    /*
     * includes the autolinks view
     */
    public function me_display_menu_autolinks()
    {
        include_once('view/autolinks.php');
    }

    /*
     * includes the categories view
     */
    public function me_display_menu_categories()
    {
        include_once('view/categories.php');
    }

    /*
     * includes the term groups view
     */
    public function me_display_menu_term_groups()
    {
        include_once('view/term_groups.php');
    }

	/*
     * includes the help view
     */
	public function me_display_menu_help()
	{
		include_once('view/help.php');
	}

	/*
     * includes the help view
     */
	public function me_display_menu_pro_version()
	{
		include_once('view/pro_version.php');
	}

    /*
     * includes the options view
     */
    public function me_display_menu_options()
    {
        include_once('view/options.php');
    }

    /*
     * register options
     */
    public function op_register_options()
    {

        //section defaults ---------------------------------------------------------------------------------------------
        add_settings_section(
            'daextam_defaults_settings_section',
            null,
            null,
            'daextam_defaults_options'
        );

        add_settings_field(
            'defaults_category_id',
            esc_html__('Category', 'daext-autolinks-manager'),
            array($this, 'defaults_category_id_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_category_id',
            array($this, 'defaults_category_id_validation')
        );

        add_settings_field(
            'defaults_open_new_table',
            esc_html__('Open New Tab', 'daext-autolinks-manager'),
            array($this, 'defaults_open_new_tab_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_open_new_tab',
            array($this, 'defaults_open_new_tab_validation')
        );

        add_settings_field(
            'defaults_use_nofollow',
            esc_html__('Use Nofollow', 'daext-autolinks-manager'),
            array($this, 'defaults_use_nofollow_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_use_nofollow',
            array($this, 'defaults_use_nofollow_validation')
        );

        add_settings_field(
            'defaults_post_types',
            esc_html__('Post Types', 'daext-autolinks-manager'),
            array($this, 'defaults_post_types_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_post_types',
            array($this, 'defaults_post_types_validation')
        );

        add_settings_field(
            'defaults_categories',
            esc_html__('Categories', 'daext-autolinks-manager'),
            array($this, 'defaults_categories_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_categories',
            array($this, 'defaults_categories_validation')
        );

        add_settings_field(
            'defaults_tags',
            esc_html__('Tags', 'daext-autolinks-manager'),
            array($this, 'defaults_tags_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_tags',
            array($this, 'defaults_tags_validation')
        );

        add_settings_field(
            'defaults_term_group_id',
            esc_html__('Term Group', 'daext-autolinks-manager'),
            array($this, 'defaults_term_group_id_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_term_group_id',
            array($this, 'defaults_term_group_id_validation')
        );

        add_settings_field(
            'defaults_case_sensitive_search',
            esc_html__('Case Sensitive Search', 'daext-autolinks-manager'),
            array($this, 'defaults_case_sensitive_search_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_case_sensitive_search',
            array($this, 'defaults_case_sensitive_search_validation')
        );

        add_settings_field(
            'defaults_left_boundary',
            esc_html__('Left Boundary', 'daext-autolinks-manager'),
            array($this, 'defaults_left_boundary_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_left_boundary',
            array($this, 'defaults_left_boundary_validation')
        );

        add_settings_field(
            'defaults_right_boundary',
            esc_html__('Right Boundary', 'daext-autolinks-manager'),
            array($this, 'defaults_right_boundary_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_right_boundary',
            array($this, 'defaults_right_boundary_validation')
        );

        add_settings_field(
            'defaults_limit',
            esc_html__('Limit', 'daext-autolinks-manager'),
            array($this, 'defaults_limit_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_limit',
            array($this, 'defaults_limit_validation')
        );

        add_settings_field(
            'defaults_priority',
            esc_html__('Priority', 'daext-autolinks-manager'),
            array($this, 'defaults_priority_callback'),
            'daextam_defaults_options',
            'daextam_defaults_settings_section'
        );

        register_setting(
            'daextam_defaults_options',
            'daextam_defaults_priority',
            array($this, 'defaults_priority_validation')
        );

        //section analysis ---------------------------------------------------------------------------------------------
        add_settings_section(
            'daextam_analysis_settings_section',
            null,
            null,
            'daextam_analysis_options'
        );

        add_settings_field(
            'analysis_set_max_execution_time',
            esc_html__('Set Max Execution Time', 'daext-autolinks-manager'),
            array($this, 'analysis_set_max_execution_time_callback'),
            'daextam_analysis_options',
            'daextam_analysis_settings_section'
        );

        register_setting(
            'daextam_analysis_options',
            'daextam_analysis_set_max_execution_time',
            array($this, 'analysis_set_max_execution_time_validation')
        );

        add_settings_field(
            'analysis_max_execution_time_value',
            esc_html__('Max Execution Time Value', 'daext-autolinks-manager'),
            array($this, 'analysis_max_execution_time_value_callback'),
            'daextam_analysis_options',
            'daextam_analysis_settings_section'
        );

        register_setting(
            'daextam_analysis_options',
            'daextam_analysis_max_execution_time_value',
            array($this, 'analysis_max_execution_time_value_validation')
        );

        add_settings_field(
            'analysis_set_memory_limit',
            esc_html__('Set Memory Limit', 'daext-autolinks-manager'),
            array($this, 'analysis_set_memory_limit_callback'),
            'daextam_analysis_options',
            'daextam_analysis_settings_section'
        );

        register_setting(
            'daextam_analysis_options',
            'daextam_analysis_set_memory_limit',
            array($this, 'analysis_set_memory_limit_validation')
        );

        add_settings_field(
            'analysis_memory_limit_value',
            esc_html__('Memory Limit Value', 'daext-autolinks-manager'),
            array($this, 'analysis_memory_limit_value_callback'),
            'daextam_analysis_options',
            'daextam_analysis_settings_section'
        );

        register_setting(
            'daextam_analysis_options',
            'daextam_analysis_memory_limit_value',
            array($this, 'analysis_memory_limit_value_validation')
        );

        add_settings_field(
            'analysis_limit_posts_analysis',
            esc_html__('Limit Posts Analysis', 'daext-autolinks-manager'),
            array($this, 'analysis_limit_posts_analysis_callback'),
            'daextam_analysis_options',
            'daextam_analysis_settings_section'
        );

        register_setting(
            'daextam_analysis_options',
            'daextam_analysis_limit_posts_analysis',
            array($this, 'analysis_limit_posts_analysis_validation')
        );

        add_settings_field(
            'analysis_post_types',
            esc_html__('Post Types', 'daext-autolinks-manager'),
            array($this, 'analysis_post_types_callback'),
            'daextam_analysis_options',
            'daextam_analysis_settings_section'
        );

        register_setting(
            'daextam_analysis_options',
            'daextam_analysis_post_types',
            array($this, 'analysis_post_types_validation')
        );

        //section advanced ---------------------------------------------------------------------------------------------
        add_settings_section(
            'daextam_advanced_settings_section',
            null,
            null,
            'daextam_advanced_options'
        );

        add_settings_field(
            'advanced_enable_autolinks',
            esc_html__('Enable Autolinks', 'daext-autolinks-manager'),
            array($this, 'advanced_enable_autolinks_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_enable_autolinks',
            array($this, 'advanced_enable_autolinks_validation')
        );

        add_settings_field(
            'advanced_filter_priority',
            esc_html__('Filter Priority', 'daext-autolinks-manager'),
            array($this, 'advanced_filter_priority_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_filter_priority',
            array($this, 'advanced_filter_priority_validation')
        );

        add_settings_field(
            'advanced_enable_test_mode',
            esc_html__('Test Mode', 'daext-autolinks-manager'),
            array($this, 'advanced_enable_test_mode_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_enable_test_mode',
            array($this, 'advanced_enable_test_mode_validation')
        );

        add_settings_field(
            'advanced_random_prioritization',
            esc_html__('Random Prioritization', 'daext-autolinks-manager'),
            array($this, 'advanced_random_prioritization_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_random_prioritization',
            array($this, 'advanced_random_prioritization_validation')
        );

        add_settings_field(
            'advanced_ignore_self_autolinks',
            esc_html__('Ignore Self Autolinks', 'daext-autolinks-manager'),
            array($this, 'advanced_ignore_self_autolinks_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_ignore_self_autolinks',
            array($this, 'advanced_ignore_self_autolinks_validation')
        );

        add_settings_field(
            'advanced_categories_and_tags_verification',
            esc_html__('Categories & Tags Verification', 'daext-autolinks-manager'),
            array($this, 'advanced_categories_and_tags_verification_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_categories_and_tags_verification',
            array($this, 'advanced_categories_and_tags_verification_validation')
        );

        add_settings_field(
            'advanced_general_limit_mode',
            esc_html__('General Limit Mode', 'daext-autolinks-manager'),
            array($this, 'advanced_general_limit_mode_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_general_limit_mode',
            array($this, 'advanced_general_limit_mode_validation')
        );

        add_settings_field(
            'advanced_general_limit_characters_per_autolink',
            esc_html__('General Limit (Characters per Autolink)', 'daext-autolinks-manager'),
            array($this, 'advanced_general_limit_characters_per_autolink_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_general_limit_characters_per_autolink',
            array($this, 'advanced_general_limit_characters_per_autolink_validation')
        );

        add_settings_field(
            'advanced_general_limit_amount',
            esc_html__('General Limit (Amount)', 'daext-autolinks-manager'),
            array($this, 'advanced_general_limit_amount_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_general_limit_amount',
            array($this, 'advanced_general_limit_amount_validation')
        );

        add_settings_field(
            'advanced_same_url_limit',
            esc_html__('Same URL Limit', 'daext-autolinks-manager'),
            array($this, 'advanced_same_url_limit_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_same_url_limit',
            array($this, 'advanced_same_url_limit_validation')
        );

        add_settings_field(
            'advanced_protected_tags',
            esc_html__('Protected Tags', 'daext-autolinks-manager'),
            array($this, 'advanced_protected_tags_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_protected_tags',
            array($this, 'advanced_protected_tags_validation')
        );

        add_settings_field(
            'advanced_protected_gutenberg_blocks',
            esc_html__('Protected Gutenberg Blocks', 'daext-autolinks-manager'),
            array($this, 'advanced_protected_gutenberg_blocks_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_protected_gutenberg_blocks',
            array($this, 'advanced_protected_gutenberg_blocks_validation')
        );

        add_settings_field(
            'advanced_protected_gutenberg_custom_blocks',
            esc_html__('Protected Gutenberg Custom Blocks', 'daext-autolinks-manager'),
            array($this, 'advanced_protected_gutenberg_custom_blocks_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_protected_gutenberg_custom_blocks',
            array($this, 'advanced_protected_gutenberg_custom_blocks_validation')
        );

        add_settings_field(
            'advanced_protected_gutenberg_custom_void_blocks',
            esc_html__('Protected Gutenberg Custom Void Blocks', 'daext-autolinks-manager'),
            array($this, 'advanced_protected_gutenberg_custom_void_blocks_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_protected_gutenberg_custom_void_blocks',
            array($this, 'advanced_protected_gutenberg_custom_void_blocks_validation')
        );

        add_settings_field(
            'advanced_supported_terms',
            esc_html__('Supported Terms', 'daext-autolinks-manager'),
            array($this, 'advanced_supported_terms_callback'),
            'daextam_advanced_options',
            'daextam_advanced_settings_section'
        );

        register_setting(
            'daextam_advanced_options',
            'daextam_advanced_supported_terms',
            array($this, 'advanced_supported_terms_validation')
        );

    }

    //defaults options callbacks and validations -----------------------------------------------------------------------
    public function defaults_category_id_callback($args)
    {

        $html = '<select id="daextam-defaults-term-group-id" name="daextam_defaults_category_id" class="daext-display-none">';

        $html .= '<option value="0" ' . selected(intval(get_option("daextam_defaults_category_id")), 0,
                false) . '>' . esc_html__('None', 'daext-autolinks-manager') . '</option>';

        global $wpdb;
        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_category";
        $sql        = "SELECT category_id, name FROM $table_name ORDER BY category_id DESC";
        $category_a = $wpdb->get_results($sql, ARRAY_A);

        foreach ($category_a as $key => $category) {
            $html .= '<option value="' . $category['category_id'] . '" ' . selected(intval(get_option("daextam_defaults_category_id")),
                    $category['category_id'], false) . '>' . esc_html(stripslashes($category['name'])) . '</option>';
        }

        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('The category of the autolink. This option determines the default value of the "Category" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_category_id_validation($input)
    {

        return intval($input, 10);

    }

    public function defaults_open_new_tab_callback($args)
    {

        $html = '<select id="daextam-defaults-open-new-tab" name="daextam_defaults_open_new_tab" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_open_new_tab")), 0,
                false) . ' value="0">' . esc_html__('No', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_open_new_tab")), 1,
                false) . ' value="1">' . esc_html__('Yes', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('If you select "Yes" the link generated on the defined keyword opens the linked document in a new tab. This option determines the default value of the "Open New Tab" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_open_new_tab_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function defaults_use_nofollow_callback($args)
    {

        $html = '<select id="daextam-defaults-use-nofollow" name="daextam_defaults_use_nofollow" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_use_nofollow")), 0,
                false) . ' value="0">' . esc_html__('No', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_use_nofollow")), 1,
                false) . ' value="1">' . esc_html__('Yes', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('If you select "Yes" the link generated on the defined keyword will include the rel="nofollow" attribute. This option determines the default value of the "Use Nofollow" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_use_nofollow_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function defaults_post_types_callback($args)
    {

        $defaults_post_types_a = get_option("daextam_defaults_post_types");

        $available_post_types_a = get_post_types(array(
            'public'  => true,
            'show_ui' => true
        ));

        //Remove the "attachment" post type
        $available_post_types_a = array_diff($available_post_types_a, array('attachment'));

        $html = '<select id="daextam-defaults-categories" name="daextam_defaults_post_types[]" class="daext-display-none" multiple>';

        foreach ($available_post_types_a as $single_post_type) {
            if (is_array($defaults_post_types_a) and in_array($single_post_type, $defaults_post_types_a)) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $post_type_obj = get_post_type_object($single_post_type);
            $html          .= '<option value="' . $single_post_type . '" ' . $selected . '>' . esc_html($post_type_obj->label) . '</option>';
        }

        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which post types the defined keywords will be automatically converted to a link. Leave this field empty to convert the keyword in any post type. This option determines the default value of the "Post Types" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_post_types_validation($input)
    {

        if (is_array($input)) {
            return $input;
        } else {
            return '';
        }

    }

    public function defaults_categories_callback($args)
    {

        $defaults_categories_a = get_option("daextam_defaults_categories");

        $html = '<select id="daextam-defaults-categories" name="daextam_defaults_categories[]" class="daext-display-none" multiple>';

        $categories = get_categories(array(
            'hide_empty' => 0,
            'orderby'    => 'term_id',
            'order'      => 'DESC'
        ));

        foreach ($categories as $category) {
            if (is_array($defaults_categories_a) and in_array($category->term_id, $defaults_categories_a)) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $html .= '<option value="' . $category->term_id . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
        }

        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which categories the defined keywords will be automatically converted to a link. Leave this field empty to convert the keyword in any category. This option determines the default value of the "Categories" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_categories_validation($input)
    {

        if (wp_is_numeric_array($input)) {
            return $input;
        } else {
            return '';
        }

    }

    public function defaults_tags_callback($args)
    {

        $defaults_tags_a = get_option("daextam_defaults_tags");

        $html = '<select id="daextam-defaults-categories" name="daextam_defaults_tags[]" class="daext-display-none" multiple>';

        $categories = get_categories(array(
            'hide_empty' => 0,
            'orderby'    => 'term_id',
            'order'      => 'DESC',
            'taxonomy'   => 'post_tag'
        ));

        foreach ($categories as $category) {
            if (is_array($defaults_tags_a) and in_array($category->term_id, $defaults_tags_a)) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $html .= '<option value="' . $category->term_id . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
        }

        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which tags the defined keywords will be automatically converted to a link. Leave this field empty to convert the keyword in any tag. This option determines the default value of the "Tags" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_tags_validation($input)
    {

        if (wp_is_numeric_array($input)) {
            return $input;
        } else {
            return '';
        }

    }

    public function defaults_term_group_id_callback($args)
    {

        $html = '<select id="daextam-defaults-term-group-id" name="daextam_defaults_term_group_id" class="daext-display-none">';

        $html .= '<option value="0" ' . selected(intval(get_option("daextam_defaults_term_group_id")), 0,
                false) . '>' . esc_html__('None', 'daext-autolinks-manager') . '</option>';

        global $wpdb;
        $table_name   = $wpdb->prefix . $this->shared->get('slug') . "_term_group";
        $sql          = "SELECT term_group_id, name FROM $table_name ORDER BY term_group_id DESC";
        $term_group_a = $wpdb->get_results($sql, ARRAY_A);

        foreach ($term_group_a as $key => $term_group) {
            $html .= '<option value="' . $term_group['term_group_id'] . '" ' . selected(intval(get_option("daextam_defaults_term_group_id")),
                    $term_group['term_group_id'],
                    false) . '>' . esc_html(stripslashes($term_group['name'])) . '</option>';
        }

        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('The terms that will be compared with the ones available on the posts where the autolinks are applied. Please note that when a term group is selected the "Categories" and "Tags" options will be ignored. This option determines the default value of the "Term Group" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_term_group_id_validation($input)
    {

        return intval($input, 10);

    }

    public function defaults_case_sensitive_search_callback($args)
    {

        $html = '<select id="daextam-defaults-case-sensitive-search" name="daextam_defaults_case_sensitive_search" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_case_sensitive_search")), 0,
                false) . ' value="0">' . esc_html__('No', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_case_sensitive_search")), 1,
                false) . ' value="1">' . esc_html__('Yes', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('If you select "No" the defined keyword will match both lowercase and uppercase variations. This option determines the default value of the "Case Sensitive Search" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_case_sensitive_search_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function defaults_left_boundary_callback($args)
    {

        $html = '<select id="daextam-defaults-left-boundary" name="daextam_defaults_left_boundary" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_left_boundary")), 0,
                false) . ' value="0">' . esc_html__('Generic', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_left_boundary")), 1,
                false) . ' value="1">' . esc_html__('White Space', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_left_boundary")), 2,
                false) . ' value="2">' . esc_html__('Comma', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_left_boundary")), 3,
                false) . ' value="3">' . esc_html__('Point', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_left_boundary")), 4,
                false) . ' value="4">' . esc_html__('None', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('Use this option to match keywords preceded by a generic boundary or by a specific character. This option determines the default value of the "Left Boundary" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_left_boundary_validation($input)
    {

        if (intval($input, 10) >= 0 and intval($input, 10) <= 4) {
            return intval($input, 10);
        } else {
            return intval(get_option('daextam_defaults_left_boundary'), 10);
        }

    }

    public function defaults_right_boundary_callback($args)
    {

        $html = '<select id="daextam-defaults-right-boundary" name="daextam_defaults_right_boundary" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_right_boundary")), 0,
                false) . ' value="0">' . esc_html__('Generic', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_right_boundary")), 1,
                false) . ' value="1">' . esc_html__('White Space', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_right_boundary")), 2,
                false) . ' value="2">' . esc_html__('Comma', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_right_boundary")), 3,
                false) . ' value="3">' . esc_html__('Point', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_defaults_right_boundary")), 4,
                false) . ' value="4">' . esc_html__('None', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('Use this option to match keywords followed by a generic boundary or by a specific character. This option determines the default value of the "Right Boundary" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function defaults_right_boundary_validation($input)
    {

        if (intval($input, 10) >= 0 and intval($input, 10) <= 4) {
            return intval($input, 10);
        } else {
            return intval(get_option('daextam_defaults_right_boundary'), 10);
        }

    }

    public function defaults_limit_callback($args)
    {

        $html = '<input maxlength="7" type="text" id="daextam_defaults_limit" name="daextam_defaults_limit" class="regular-text" value="' . intval(get_option("daextam_defaults_limit"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('With this option you can determine the maximum number of matches of the defined keyword automatically converted to a link. This option determines the default value of the "Limit" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function defaults_limit_validation($input)
    {

        if ( ! preg_match($this->shared->regex_number_ten_digits, $input) or intval($input, 10) < 1 or intval($input,
                10) > 1000000) {
            add_settings_error('daextam_defaults_limit', 'daextam_defaults_limit',
                esc_html__('Please enter a number from 1 to 1000000 in the "Limit" option.', 'daext-autolinks-manager'));
            $output = get_option('daextam_defaults_limit');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }


    public function defaults_priority_callback($args)
    {

        $html = '<input maxlength="7" type="text" id="daextam_defaults_priority" name="daextam_defaults_priority" class="regular-text" value="' . intval(get_option("daextam_defaults_priority"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('The priority value determines the order used to apply the autolinks on the post. This option determines the default value of the "Priority" field available in the "Autolinks" menu.',
                'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function defaults_priority_validation($input)
    {

        if ( ! preg_match($this->shared->regex_number_ten_digits, $input) or intval($input, 10) < 0 or intval($input,
                10) > 1000000) {
            add_settings_error('daextam_defaults_priority', 'daextam_defaults_priority',
                esc_html__('Please enter a number from 1 to 1000000 in the "Priority" option.', 'daext-autolinks-manager'));
            $output = get_option('daextam_defaults_priority');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }

    //analysis options callbacks and validations -----------------------------------------------------------------------
    public function analysis_set_max_execution_time_callback($args)
    {

        $html = '<select id="daextam-analysis-set-max-execution-time" name="daextam_analysis_set_max_execution_time" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_analysis_set_max_execution_time")), 0,
                false) . ' value="0">' . esc_html__('No', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_analysis_set_max_execution_time")), 1,
                false) . ' value="1">' . esc_html__('Yes', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('Select "Yes" to enable your custom "Max Execution Time Value" on long running scripts.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function analysis_set_max_execution_time_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function analysis_max_execution_time_value_callback($args)
    {

        $html = '<input maxlength="7" type="text" id="daextam_analysis_max_execution_time_value" name="daextam_analysis_max_execution_time_value" class="regular-text" value="' . intval(get_option("daextam_analysis_max_execution_time_value"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('This value determines the maximum number of seconds allowed to execute long running scripts.', 'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function analysis_max_execution_time_value_validation($input)
    {

        if ( ! preg_match($this->shared->regex_number_ten_digits, $input) or intval($input, 10) > 1000000) {
            add_settings_error('daextam_analysis_max_execution_time_value', 'daextam_analysis_max_execution_time_value',
                esc_html__('Please enter a valid value in the "Memory Limit Value" option.', 'daext-autolinks-manager'));
            $output = get_option('daextam_analysis_max_execution_time_value');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }

    public function analysis_set_memory_limit_callback($args)
    {

        $html = '<select id="daextam-analysis-set-memory-limit" name="daextam_analysis_set_memory_limit" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_analysis_set_memory_limit")), 0,
                false) . ' value="0">' . esc_html__('No', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_analysis_set_memory_limit")), 1,
                false) . ' value="1">' . esc_html__('Yes', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('Select "Yes" to enable your custom "Memory Limit Value" on long running scripts.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function analysis_set_memory_limit_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function analysis_memory_limit_value_callback($args)
    {

        $html = '<input maxlength="7" type="text" id="daextam_analysis_memory_limit_value" name="daextam_analysis_memory_limit_value" class="regular-text" value="' . intval(get_option("daextam_analysis_memory_limit_value"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('This value determines the PHP memory limit in megabytes allowed to execute long running scripts.', 'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function analysis_memory_limit_value_validation($input)
    {

        if ( ! preg_match($this->shared->regex_number_ten_digits, $input) or intval($input, 10) > 1000000) {
            add_settings_error('daextam_analysis_memory_limit_value', 'daextam_analysis_memory_limit_value',
                esc_html__('Please enter a valid value in the "Memory Limit Value" option.', 'daext-autolinks-manager'));
            $output = get_option('daextam_analysis_memory_limit_value');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }

    public function analysis_limit_posts_analysis_callback($args)
    {

        $html = '<input maxlength="7" type="text" id="daextam_analysis_limit_posts_analysis" name="daextam_analysis_limit_posts_analysis" class="regular-text" value="' . intval(get_option("daextam_analysis_limit_posts_analysis"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('With this options you can determine the maximum number of posts analyzed to get information about your autolinks. If you select for example "1000", the analysis performed by the plugin will use your latest "1000" posts.',
                'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function analysis_limit_posts_analysis_validation($input)
    {

        if ( ! preg_match($this->shared->regex_number_ten_digits, $input) or intval($input, 10) > 1000000) {
            add_settings_error('daextam_analysis_limit_posts_analysis', 'daextam_analysis_limit_posts_analysis',
                esc_html__('Please enter a valid value in the "Limit Post Analysis" option.', 'daext-autolinks-manager'));
            $output = get_option('daextam_analysis_limit_posts_analysis');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }


    public function analysis_post_types_callback($args)
    {

        $analysis_post_types_a = get_option("daextam_analysis_post_types");

        $available_post_types_a = get_post_types(array(
            'public'  => true,
            'show_ui' => true
        ));

        //Remove the "attachment" post type
        $available_post_types_a = array_diff($available_post_types_a, array('attachment'));

        $html = '<select id="daextam-analysis-categories" name="daextam_analysis_post_types[]" class="daext-display-none" multiple>';

        foreach ($available_post_types_a as $single_post_type) {
            if (is_array($analysis_post_types_a) and in_array($single_post_type, $analysis_post_types_a)) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $post_type_obj = get_post_type_object($single_post_type);
            $html          .= '<option value="' . $single_post_type . '" ' . $selected . '>' . esc_html($post_type_obj->label) . '</option>';
        }

        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which post types the analysis should be performed. Leave this field empty to perform the analysis in any post type.', 'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function analysis_post_types_validation($input)
    {

        if (is_array($input)) {
            return $input;
        } else {
            return '';
        }

    }

    //advanced options callbacks and validations ---------------------------------------------------------------------
    public function advanced_enable_autolinks_callback($args)
    {

        $html = '<select id="daextam-advanced-enable-autolinks" name="daextam_advanced_enable_autolinks" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_enable_autolinks")), 0,
                false) . ' value="0">' . esc_html__('No', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_enable_autolinks")), 1,
                false) . ' value="1">' . esc_html__('Yes', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('This option determines the default status of the "Enable Autolinks" option available in the "Autolinks Manager" meta box.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function advanced_enable_autolinks_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function advanced_filter_priority_callback($args)
    {

        $html = '<input maxlength="11" type="text" id="daextam_advanced_filter_priority" name="daextam_advanced_filter_priority" class="regular-text" value="' . intval(get_option("daextam_advanced_filter_priority"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('This option determines the priority of the filter used to apply the autolinks. A lower number corresponds with an earlier execution.', 'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function advanced_filter_priority_validation($input)
    {

        if (intval($input, 10) < -2147483648 or intval($input, 10) > 2147483646) {
            add_settings_error('daextam_advanced_filter_priority', 'daextam_advanced_filter_priority',
                esc_html__('Please enter a number from -2147483648 to 2147483646 in the "Filter Priority" option.',
                    'daext-autolinks-manager'));
            $output = get_option('daextam_advanced_filter_priority');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }

    public function advanced_enable_test_mode_callback($args)
    {

        $html = '<select id="daextam-advanced-enable-test-mode" name="daextam_advanced_enable_test_mode" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_enable_test_mode")), 0,
                false) . ' value="0">' . esc_html__('No', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_enable_test_mode")), 1,
                false) . ' value="1">' . esc_html__('Yes', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('With the test mode enabled the autolinks will be applied to your posts, pages or custom post types only if the user that is requesting the posts, pages or custom post types is the website administrator.', 'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function advanced_enable_test_mode_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function advanced_random_prioritization_callback($args)
    {

        $html = '<select id="daextam-advanced-random-prioritization" name="daextam_advanced_random_prioritization" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_random_prioritization")), 0,
                false) . ' value="0">' . esc_html__('No', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_random_prioritization")), 1,
                false) . ' value="1">' . esc_html__('Yes', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__("With this option enabled the order used to apply the autolinks with the same priority is randomized on a per-post basis. With this option disabled the order used to apply the autolinks with the same priority is the order used to add them in the back-end. It's recommended to enable this option for a better distribution of the autolinks.",
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function advanced_random_prioritization_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function advanced_ignore_self_autolinks_callback($args)
    {

        $html = '<select id="daextam-advanced-ignore-self-autolinks" name="daextam_advanced_ignore_self_autolinks" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_ignore_self_autolinks")), 0,
                false) . ' value="0">' . esc_html__('No', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_ignore_self_autolinks")), 1,
                false) . ' value="1">' . esc_html__('Yes', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('With this option enabled, the autolinks which have as a target the post where they should be applied, will be ignored.', 'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function advanced_ignore_self_autolinks_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function advanced_categories_and_tags_verification_callback($args)
    {

        $html = '<select id="daextam-advanced-categories-and-tags-verification" name="daextam_advanced_categories_and_tags_verification" class="daext-display-none">';
        $html .= '<option ' . selected(get_option("daextam_advanced_categories_and_tags_verification"), 'post',
                false) . ' value="post">' . esc_html__('Post', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(get_option("daextam_advanced_categories_and_tags_verification"), 'any',
                false) . ' value="any">' . esc_html__('Any', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('If "Post" is selected categories and tags will be verified only in the "post" post type, if "Any" is selected categories and tags will be verified in any post type.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function advanced_categories_and_tags_verification_validation($input)
    {

        switch ($input) {
            case 'post':
                return 'post';
            default:
                return 'any';
        }

    }

    public function advanced_general_limit_mode_callback($args)
    {

        $html = '<select id="daextam-advanced-general-limit-mode" name="daextam_advanced_general_limit_mode" class="daext-display-none">';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_general_limit_mode")), 0,
                false) . ' value="0">' . esc_html__('Auto', 'daext-autolinks-manager') . '</option>';
        $html .= '<option ' . selected(intval(get_option("daextam_advanced_general_limit_mode")), 1,
                false) . ' value="1">' . esc_html__('Manual', 'daext-autolinks-manager') . '</option>';
        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('If "Auto" is selected the maximum number of autolinks per post is automatically generated based on the length of the post, in this case the "General Limit (Characters per Autolinks)" option is used. If "Manual" is selected the maximum number of autolinks per post is equal to the value of the "General Limit (Amount)" option.',
                'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function advanced_general_limit_mode_validation($input)
    {

        return intval($input, 10) == 1 ? '1' : '0';

    }

    public function advanced_general_limit_characters_per_autolink_callback($args)
    {

        $html = '<input maxlength="7" type="text" id="daextam_advanced_general_limit_characters_per_autolink" name="daextam_advanced_general_limit_characters_per_autolink" class="regular-text" value="' . intval(get_option("daextam_advanced_general_limit_characters_per_autolink"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('This value is used to automatically determine the maximum number of autolinks per post when the "General Limit Mode" option is set to "Auto".',
                'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function advanced_general_limit_characters_per_autolink_validation($input)
    {

        if ( ! preg_match($this->shared->regex_number_ten_digits, $input) or intval($input, 10) < 1 or intval($input,
                10) > 1000000) {
            add_settings_error('daextam_advanced_general_limit_characters_per_autolink',
                'daextam_advanced_general_limit_characters_per_autolink',
                esc_html__('Please enter a number from 1 to 1000000 in the "General Limit (Characters per Autolink)" option.',
                    'daext-autolinks-manager'));
            $output = get_option('daextam_advanced_general_limit_characters_per_autolink');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }

    public function advanced_general_limit_amount_callback($args)
    {

        $html = '<input maxlength="7" type="text" id="daextam_advanced_general_limit_amount" name="daextam_advanced_general_limit_amount" class="regular-text" value="' . intval(get_option("daextam_advanced_general_limit_amount"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('This value determines the maximum number of autolinks per post when the "General Limit Mode" option is set to "Manual".',
                'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function advanced_general_limit_amount_validation($input)
    {

        if ( ! preg_match($this->shared->regex_number_ten_digits, $input) or intval($input, 10) < 1 or intval($input,
                10) > 1000000) {
            add_settings_error('daextam_advanced_general_limit_amount', 'daextam_advanced_general_limit_amount',
                esc_html__('Please enter a number from 1 to 1000000 in the "General Limit (Amount)" option.', 'daext-autolinks-manager'));
            $output = get_option('daextam_advanced_general_limit_amount');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }

    public function advanced_same_url_limit_callback($args)
    {

        $html = '<input maxlength="7" type="text" id="daextam_advanced_same_url_limit" name="daextam_advanced_same_url_limit" class="regular-text" value="' . intval(get_option("daextam_advanced_same_url_limit"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('This option limits the number of autolinks with the same URL to a specific value.', 'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function advanced_same_url_limit_validation($input)
    {

        if ( ! preg_match($this->shared->regex_number_ten_digits, $input) or intval($input, 10) < 1 or intval($input,
                10) > 1000000) {
            add_settings_error('daextam_advanced_same_url_limit', 'daextam_advanced_same_url_limit',
                esc_html__('Please enter a number from 1 to 1000000 in the "Same URL Limit" option.', 'daext-autolinks-manager'));
            $output = get_option('daextam_advanced_same_url_limit');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }

    public function advanced_supported_terms_callback($args)
    {

        $html = '<input maxlength="7" type="text" id="daextam_advanced_supported_terms" name="daextam_advanced_supported_terms" class="regular-text" value="' . intval(get_option("daextam_advanced_supported_terms"),
                10) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr__('This option determines the maximum number of terms supported in a single term group.', 'daext-autolinks-manager') . '"></div>';
        echo $html;

    }

    public function advanced_supported_terms_validation($input)
    {

        if ( ! preg_match($this->shared->regex_number_ten_digits, $input) or intval($input, 10) < 1 or intval($input,
                10) > 50) {
            add_settings_error('daextam_advanced_supported_terms', 'daextam_advanced_supported_terms',
                esc_html__('Please enter a number from 1 to 50 in the "Supported Terms" option.', 'daext-autolinks-manager'));
            $output = get_option('daextam_advanced_supported_terms');
        } else {
            $output = $input;
        }

        return intval($output, 10);

    }

    public function advanced_protected_tags_callback($args)
    {

        $advanced_protected_tags_a = get_option("daextam_advanced_protected_tags");

        $html = '<select id="daextam-advanced-protected-tags" name="daextam_advanced_protected_tags[]" class="daext-display-none" multiple>';

        $list_of_html_tags = array(
            'a',
            'abbr',
            'acronym',
            'address',
            'applet',
            'area',
            'article',
            'aside',
            'audio',
            'b',
            'base',
            'basefont',
            'bdi',
            'bdo',
            'big',
            'blockquote',
            'body',
            'br',
            'button',
            'canvas',
            'caption',
            'center',
            'cite',
            'code',
            'col',
            'colgroup',
            'datalist',
            'dd',
            'del',
            'details',
            'dfn',
            'dir',
            'div',
            'dl',
            'dt',
            'em',
            'embed',
            'fieldset',
            'figcaption',
            'figure',
            'font',
            'footer',
            'form',
            'frame',
            'frameset',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'head',
            'header',
            'hgroup',
            'hr',
            'html',
            'i',
            'iframe',
            'img',
            'input',
            'ins',
            'kbd',
            'keygen',
            'label',
            'legend',
            'li',
            'link',
            'map',
            'mark',
            'menu',
            'meta',
            'meter',
            'nav',
            'noframes',
            'noscript',
            'object',
            'ol',
            'optgroup',
            'option',
            'output',
            'p',
            'param',
            'pre',
            'progress',
            'q',
            'rp',
            'rt',
            'ruby',
            's',
            'samp',
            'script',
            'section',
            'select',
            'small',
            'source',
            'span',
            'strike',
            'strong',
            'style',
            'sub',
            'summary',
            'sup',
            'table',
            'tbody',
            'td',
            'textarea',
            'tfoot',
            'th',
            'thead',
            'time',
            'title',
            'tr',
            'tt',
            'u',
            'ul',
            'var',
            'video',
            'wbr'
        );

        foreach ($list_of_html_tags as $key => $tag) {
            $html .= '<option value="' . $tag . '" ' . $this->shared->selected_array($advanced_protected_tags_a,
                    $tag) . '>' . $tag . '</option>';
        }

        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which HTML tags the autolinks should not be applied.', 'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function advanced_protected_tags_validation($input)
    {

        if (is_array($input)) {
            return $input;
        } else {
            return '';
        }

    }

    public function advanced_protected_gutenberg_blocks_callback($args)
    {

        $advanced_protected_gutenberg_blocks_a = get_option("daextam_advanced_protected_gutenberg_blocks");

        $html = '<select id="daextam-advanced-protected-gutenberg-embeds" name="daextam_advanced_protected_gutenberg_blocks[]" class="daext-display-none" multiple>';

        $html .= '<option value="paragraph" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'paragraph') . '>' . esc_html__('Paragraph', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="image" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'image') . '>' . esc_html__('Image', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="heading" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'heading') . '>' . esc_html__('Heading', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="gallery" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'gallery') . '>' . esc_html__('Gallery', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="list" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'list') . '>' . esc_html__('List', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="quote" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'quote') . '>' . esc_html__('Quote', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="audio" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'audio') . '>' . esc_html__('Audio', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="cover-image" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'cover-image') . '>' . esc_html__('Cover Image', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="subhead" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'subhead') . '>' . esc_html__('Subhead', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="video" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'video') . '>' . esc_html__('Video', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="code" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'code') . '>' . esc_html__('Code', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="html" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'html') . '>' . esc_html__('Custom HTML', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="preformatted" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'preformatted') . '>' . esc_html__('Preformatted', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="pullquote" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'pullquote') . '>' . esc_html__('Pullquote', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="table" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'table') . '>' . esc_html__('Table', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="verse" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'verse') . '>' . esc_html__('Verse', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="button" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'button') . '>' . esc_html__('Button', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="columns" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'columns') . '>' . esc_html__('Columns (Experimentals)', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="more" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'more') . '>' . esc_html__('More', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="nextpage" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'nextpage') . '>' . esc_html__('Page Break', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="separator" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'separator') . '>' . esc_html__('Separator', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="spacer" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'spacer') . '>' . esc_html__('Spacer', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="text-columns" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'text-columns') . '>' . esc_html__('Text Columnns', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="shortcode" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'shortcode') . '>' . esc_html__('Shortcode', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="categories" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'categories') . '>' . esc_html__('Categories', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="latest-posts" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'latest-posts') . '>' . esc_html__('Latest Posts', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="embed" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'embed') . '>' . esc_html__('Embed', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/twitter" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/twitter') . '>' . esc_html__('Twitter', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/youtube" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/youtube') . '>' . esc_html__('YouTube', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/facebook" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/facebook') . '>' . esc_html__('Facebook', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/instagram" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/instagram') . '>' . esc_html__('Instagram', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/wordpress" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/wordpress') . '>' . esc_html__('WordPress', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/soundcloud" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/soundcloud') . '>' . esc_html__('SoundCloud', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/spotify" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/spotify') . '>' . esc_html__('Spotify', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/flickr" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/flickr') . '>' . esc_html__('Flickr', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/vimeo" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/vimeo') . '>' . esc_html__('Vimeo', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/animoto" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/animoto') . '>' . esc_html__('Animoto', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/cloudup" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/cloudup') . '>' . esc_html__('Cloudup', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/collegehumor" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/collegehumor') . '>' . esc_html__('CollegeHumor', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/dailymotion" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/dailymotion') . '>' . esc_html__('DailyMotion', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/funnyordie" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/funnyordie') . '>' . esc_html__('Funny or Die', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/hulu" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/hulu') . '>' . esc_html__('Hulu', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/imgur" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/imgur') . '>' . esc_html__('Imgur', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/issuu" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/issuu') . '>' . esc_html__('Issuu', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/kickstarter" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/kickstarter') . '>' . esc_html__('Kickstarter', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/meetup-com" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/meetup-com') . '>' . esc_html__('Meetup.com', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/mixcloud" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/mixcloud') . '>' . esc_html__('Mixcloud', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/photobucket" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/photobucket') . '>' . esc_html__('Photobucket', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/polldaddy" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/polldaddy') . '>' . esc_html__('Polldaddy', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/reddit" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/reddit') . '>' . esc_html__('Reddit', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/reverbnation" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/reverbnation') . '>' . esc_html__('ReverbNation', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/screencast" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/screencast') . '>' . esc_html__('Screencast', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/scribd" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/scribd') . '>' . esc_html__('Scribd', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/slideshare" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/slideshare') . '>' . esc_html__('Slideshare', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/smugmug" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/smugmug') . '>' . esc_html__('SmugMug', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/speaker" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/speaker') . '>' . esc_html__('Speaker', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/ted" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/ted') . '>' . esc_html__('Ted', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/tumblr" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/tumblr') . '>' . esc_html__('Tumblr', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/videopress" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/videopress') . '>' . esc_html__('VideoPress', 'daext-autolinks-manager') . '</option>';
        $html .= '<option value="core-embed/wordpress-tv" ' . $this->shared->selected_array($advanced_protected_gutenberg_blocks_a,
                'core-embed/wordpress-tv') . '>' . esc_html__('WordPress.tv', 'daext-autolinks-manager') . '</option>';

        $html .= '</select>';
        $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which Gutenberg blocks the autolinks should not be applied.', 'daext-autolinks-manager') . '"></div>';

        echo $html;

    }

    public function advanced_protected_gutenberg_blocks_validation($input)
    {

        if (is_array($input)) {
            return $input;
        } else {
            return '';
        }

    }

    public function advanced_protected_gutenberg_custom_blocks_callback($args)
    {

        $html = '<input type="text" id="daextam_advanced_protected_gutenberg_custom_blocks" name="daextam_advanced_protected_gutenberg_custom_blocks" class="regular-text" value="' . esc_attr(get_option("daextam_advanced_protected_gutenberg_custom_blocks")) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr(__('Enter a list of Gutenberg custom blocks, separated by a comma.', 'daext-autolinks-manager')) . '"></div>';

        echo $html;

    }

    public function advanced_protected_gutenberg_custom_blocks_validation($input)
    {

        if (strlen(trim($input)) > 0 and ! preg_match($this->shared->regex_list_of_gutenberg_blocks, $input)) {
            add_settings_error('daextam_advanced_protected_gutenberg_custom_blocks',
                'daextam_advanced_protected_gutenberg_custom_blocks',
                __('Please enter a valid list of Gutenberg custom blocks separated by a comma in the "Protected Gutenberg Custom Blocks" option.',
                    'daext-autolinks-manager'));
            $output = get_option('daextam_advanced_protected_gutenberg_custom_blocks');
        } else {
            $output = $input;
        }

        return $output;

    }

    public function advanced_protected_gutenberg_custom_void_blocks_callback($args)
    {

        $html = '<input type="text" id="daextam_advanced_protected_gutenberg_custom_void_blocks" name="daextam_advanced_protected_gutenberg_custom_void_blocks" class="regular-text" value="' . esc_attr(get_option("daextam_advanced_protected_gutenberg_custom_void_blocks")) . '" />';
        $html .= '<div class="help-icon" title="' . esc_attr(__('Enter a list of Gutenberg custom void blocks, separated by a comma.', 'daext-autolinks-manager')) . '"></div>';

        echo $html;

    }

    public function advanced_protected_gutenberg_custom_void_blocks_validation($input)
    {

        if (strlen(trim($input)) > 0 and ! preg_match($this->shared->regex_list_of_gutenberg_blocks, $input)) {
            add_settings_error('daextam_advanced_protected_gutenberg_custom_void_blocks',
                'daextam_advanced_protected_gutenberg_custom_void_blocks',
                __('Please enter a valid list of Gutenberg custom void blocks separated by a comma in the "Protected Gutenberg Custom Void Blocks" option.',
                    'daext-autolinks-manager'));
            $output = get_option('daextam_advanced_protected_gutenberg_custom_void_blocks');
        } else {
            $output = $input;
        }

        return $output;

    }

    //meta box ---------------------------------------------------------------------------------------------------------
    public function create_meta_box()
    {

        if (current_user_can('manage_options')) {

            add_meta_box('daextam-autolinks-manager',
                esc_html__('Autolinks Manager', 'daext-autolinks-manager'),
                array($this, 'autolinks_manager_meta_box_callback'),
                null,
                'normal',
                'high',

                /*
                 * Reference:
                 *
                 * https://make.wordpress.org/core/2018/11/07/meta-box-compatibility-flags/
                 */
                array(

                        /*
                         * It's not confirmed that this meta box works in the block editor.
                         */
                        '__block_editor_compatible_meta_box' => false,

                        /*
                         * This meta box should only be loaded in the classic editor interface, and the block editor
                         * should not display it.
                         */
                        '__back_compat_meta_box' => true

                ));

        }

    }

    public function autolinks_manager_meta_box_callback($post)
    {

        $enable_autolinks = get_post_meta($post->ID, '_daextam_enable_autolinks', true);

        //if the $enable_autolinks is empty use the Enable Autolinks option as a default value
        if (mb_strlen(trim($enable_autolinks)) === 0) {
            $enable_autolinks = get_option($this->shared->get('slug') . '_advanced_enable_autolinks');
        }

        ?>

        <table class="form-table table-autolinks-manager">
            <tbody>

            <tr>
                <th scope="row"><label><?php esc_html_e('Enable Autolinks:', 'daext-autolinks-manager'); ?></label></th>
                <td>
                    <select id="daextam-enable-autolinks" name="daextam_enable_autolinks">
                        <option <?php selected(intval($enable_autolinks, 10), 0); ?> value="0"><?php esc_html_e('No', 'daext-autolinks-manager'); ?></option>
                        <option <?php selected(intval($enable_autolinks, 10), 1); ?> value="1"><?php esc_html_e('Yes', 'daext-autolinks-manager'); ?></option>
                    </select>
                </td>
            </tr>

            </tbody>
        </table>

        <?php

        //Use nonce for verification
        wp_nonce_field(plugin_basename(__FILE__), 'daextam_nonce');

    }

    //Save the Autolinks Options meta data
    public function save_meta_box($post_id)
    {

        //Security Verifications Start ---------------------------------------------------------------------------------

        //Verify if this is an auto save routine. Don't do anything if our form has not been submitted.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        /*
         * Verify if this came from the our screen and with proper authorization, because save_post can be triggered at
         * other times/
         */
        if ( ! isset($_POST['daextam_nonce']) || ! wp_verify_nonce($_POST['daextam_nonce'], plugin_basename(__FILE__))) {
            return;
        }

        //Verify the capability
        if ( ! current_user_can('manage_options')) {
            return;
        }

        //Security Verifications End -----------------------------------------------------------------------------------

        //Save the "Enable Autolinks"
        update_post_meta($post_id, '_daextam_enable_autolinks', intval($_POST['daextam_enable_autolinks'], 10));

    }

}