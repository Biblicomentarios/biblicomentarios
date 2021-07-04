<?php

/**
 * Class StockpackAdmin
 *
 * Handle the settings admin page
 */
class StockpackAdmin {
    /**
     * @var Singleton The reference the *Singleton* instance of this class
     */
    private static $instance;

    /**
     * @var WeDevs_Settings_API
     */
    public $settings_api;

    /**
     * @var string plugin version
     */
    public $version = '3.2.4';

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Stockpack constructor.
     */
    protected function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }

    /**
     *
     */
    public function init() {
        $this->actions();
        $this->filters();
    }

    /**
     *
     */
    public function actions() {
        add_action( 'wp_loaded', array( $this, 'maybe_show_notice' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
    }

    public function filters() {
        add_filter( 'stockpack_download_timeout', array( $this, 'download_timeout' ), 10, 1 );
    }

    public function download_timeout( $limit ) {
        $overwrite_limit = (int) $this->get_option( 'download_timeout', 'stockpack_debug' );
        if ( $overwrite_limit > 0 ) {
            return $overwrite_limit;
        }

        return $limit;
    }

    /**
     *  Register settings
     */
    public function register_settings() {

        $this->settings_api = new WeDevs_Settings_API();
        //set sections and fields
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize them
        $this->settings_api->admin_init();
    }

    public function register_settings_page() {
        $capability = apply_filters( 'stockpack_settings_capability', 'upload_files' );

        add_options_page( __( 'StockPack', 'stockpack' ), __( 'StockPack', 'stockpack' ), $capability, 'stockpack',
            array( $this, 'plugin_page' )
        );
    }

    public function maybe_show_notice() {

        add_action( 'admin_notices', array( $this, 'compatibility_notice' ) );
        if ( ! PAnD::is_admin_notice_active( 'disable-media-notice-forever' ) ) {
            return;
        }
        add_action( 'admin_init', array( 'PAnD', 'init' ) );

        add_action( 'admin_notices', array( $this, 'media_notice' ) );

        add_action( 'wsa_form_bottom_stockpack_debug', array( $this, 'debug_info' ) );
    }

    public function compatibility_notice() {
        // all compatibility issues were resolved
    }

    public function media_notice() {
        global $pagenow;
        if ( $pagenow != 'upload.php' ) {
            return;
        }

        $capability = apply_filters( 'stockpack_notices_capability', 'upload_files' );
        if ( current_user_can( $capability ) ) {
            echo '<div data-dismissible="disable-media-notice-forever" class="notice notice-warning is-dismissible">
          <p>' . __( 'If you are looking for StockPack you will find it on the Media Tab when you use the WordPress uploader. Just go to a post or page and try to insert an image. You will see the StockPack tab at the top.  You can read more here:', 'stockpack' ) . ' <a href="https://stockpack.co/blog/what-to-do-if-stockpack-tab-is-not-showing-up/">' . __( 'What to do if stockpack tab is not showing up', 'stockpack' ) . '</a></p>
         </div>';
        }

    }

    public function plugin_page() {

        echo '<div class="wrap">';
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();
        $link_text = esc_textarea( __( 'Get your token', 'stockpack' ) );
        echo '<a class="license-key-link" href="https://stockpack.co/register" target="_blank">' . $link_text . '</a>';

        echo '</div>';
    }

    public function get_featured_caption_setting() {
        return $this->get_option( 'caption_featured_image', 'stockpack_advanced' );
    }

    /**
     * @return mixed
     */
    public function get_api_key() {
        return $this->get_option( 'auth_token', 'stockpack_basics' );
    }

    /**
     * @return mixed
     */
    public function set_api_key( $token ) {
        return $this->set_option( 'auth_token', 'stockpack_basics', $token );
    }

    public function debug_info() {
        global $wp_version;
        $theme = wp_get_theme();
        $memory = $this->wc_let_to_num( WP_MEMORY_LIMIT );

        if ( function_exists( 'memory_get_usage' ) ) {
            $system_memory = $this->wc_let_to_num( @ini_get( 'memory_limit' ) );
            $memory = max( $memory, $system_memory );
        }
        $data = [
            'active_plugins' => get_option( 'active_plugins' ),
            'theme'          => $theme->name,
            'wp_version'     => $wp_version,
            'token'          => $this->get_api_key(),
            'plugin_version' => $this->version,
            'is_multisite'   => is_multisite(),
            'memory'         => size_format( $memory ),
            'server_info'    => $this->wc_get_server_info()
        ];

        echo "<br><br/><i>".__('Copy this information when you write to support with a bug issue','stockpack')."</i><br/>";
        echo "<textarea cols='80' rows='10'>" . json_encode( $data ) . "</textarea>";
    }


    /**
     * WC function to convert php data to int
     *
     * @param $size
     *
     * @return int
     */
    private function wc_let_to_num( $size ) {
        $l = substr( $size, - 1 );
        $ret = (int) substr( $size, 0, - 1 );
        switch ( strtoupper( $l ) ) {
            case 'P':
                $ret *= 1024;
            // No break.
            case 'T':
                $ret *= 1024;
            // No break.
            case 'G':
                $ret *= 1024;
            // No break.
            case 'M':
                $ret *= 1024;
            // No break.
            case 'K':
                $ret *= 1024;
            // No break.
        }

        return $ret;
    }

    /**
     * WC function to get server related info.
     *
     * @return array
     */
    private function wc_get_server_info() {
        $server_data = array();

        if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
            $server_data['software'] = $_SERVER['SERVER_SOFTWARE']; // @phpcs:ignore
        }

        if ( function_exists( 'phpversion' ) ) {
            $server_data['php_version'] = phpversion();
        }

        if ( function_exists( 'ini_get' ) ) {
            $server_data['php_post_max_size'] = size_format( $this->wc_let_to_num( ini_get( 'post_max_size' ) ) );
            $server_data['php_time_limt'] = ini_get( 'max_execution_time' );
            $server_data['php_max_input_vars'] = ini_get( 'max_input_vars' );
            $server_data['php_suhosin'] = extension_loaded( 'suhosin' ) ? 'Yes' : 'No';
        }

        $server_data['php_max_upload_size'] = size_format( wp_max_upload_size() );
        $server_data['php_default_timezone'] = date_default_timezone_get();
        $server_data['php_soap'] = class_exists( 'SoapClient' ) ? 'Yes' : 'No';
        $server_data['php_fsockopen'] = function_exists( 'fsockopen' ) ? 'Yes' : 'No';
        $server_data['php_curl'] = function_exists( 'curl_init' ) ? 'Yes' : 'No';

        return $server_data;
    }


    /**
     * @return array
     */
    private function get_settings_sections() {
        return array(
            array(
                'id'    => 'stockpack_basics',
                'title' => __( 'General', 'stockpack' ),
            ),
            array(
                'id'    => 'stockpack_advanced',
                'title' => __( 'Advanced', 'stockpack' ),
            ),
            array(
                'id'    => 'stockpack_debug',
                'title' => __( 'Debug', 'stockpack' ),
            ),
        );
    }

    /**
     * @return array
     */
    private function get_settings_fields() {
        return array(
            'stockpack_basics'   => array(
                array(
                    'name'    => 'auth_token',
                    'label'   => __( 'Token', 'stockpack' ),
                    'desc'    => __( 'This is the token that is used for authentication', 'stockpack' ),
                    'type'    => 'text',
                    'default' => '',
                    'size'    => 'validate-stockpack-key regular' //small hack to add class
                ),
                array(
                    'name'    => 'safe_search',
                    'label'   => __( 'Apply safe search', 'stockpack' ),
                    'desc'    => __( 'Some providers support this mode to prevent nude images to show up in the search', 'stockpack' ),
                    'options' => array(
                        'yes' => __( 'Yes', 'stockpack' ),
                        'no'  => __( 'No', 'stockpack' )
                    ),
                    'type'    => 'radio',
                    'default' => 'yes',
                ),
                array(
                    'name'    => 'file_name_change',
                    'label'   => __( 'Enable file name change', 'stockpack' ),
                    'desc'    => __( 'You can set the desired filename prior to download. This is a good SEO idea if you want it, but it\'s a small extra step', 'stockpack' ),
                    'options' => array(
                        'yes' => __( 'Yes', 'stockpack' ),
                        'no'  => __( 'No', 'stockpack' )
                    ),
                    'type'    => 'radio',
                    'default' => 'no',
                )
            ),
            'stockpack_advanced' => array(
                array(
                    'name'    => 'caption_premium_providers',
                    'label'   => __( 'Enable premium providers caption', 'stockpack' ),
                    'desc'    => __( 'You can enable automatic caption for premium providers if you want to', 'stockpack' ),
                    'options' => array(
                        'yes' => __( 'Yes', 'stockpack' ),
                        'no'  => __( 'No', 'stockpack' )
                    ),
                    'type'    => 'radio',
                    'default' => 'no',
                ),
                array(
                    'name'    => 'caption_standard_fields',
                    'label'   => __( 'Enable caption fields', 'stockpack' ),
                    'desc'    => __( 'You can enable additional fields for images that you can use to standardize captions. This is generally used to provide credits.', 'stockpack' ),
                    'options' => array(
                        'yes' => __( 'Yes', 'stockpack' ),
                        'no'  => __( 'No', 'stockpack' )
                    ),
                    'type'    => 'radio',
                    'default' => 'no',
                ),
                array(
                    'name'    => 'caption_featured_image',
                    'label'   => __( 'Enable featured image caption', 'stockpack' ),
                    'desc'    => __( 'You can enable captions for featured images to be appended. If you want to use this for non stockpack images, you also need the above setting.', 'stockpack' ),
                    'options' => array(
                        'yes' => __( 'Yes', 'stockpack' ),
                        'no'  => __( 'No', 'stockpack' )
                    ),
                    'type'    => 'radio',
                    'default' => 'no',
                ),
                array(
                    'name'    => 'license_state',
                    'label'   => __( 'Search with authorization', 'stockpack' ),
                    'desc'    => __( 'Providers that support oauth can get the license state for all images directly. You see it in the sidebar. The downside is that the searches are slower', 'stockpack' ),
                    'options' => array(
                        'yes' => __( 'Yes', 'stockpack' ),
                        'no'  => __( 'No', 'stockpack' )
                    ),
                    'type'    => 'radio',
                    'default' => 'no',
                ),
            ),
            'stockpack_debug'    => array(
                array(
                    'name'    => 'download_timeout',
                    'label'   => __( 'Download timeout in seconds', 'stockpack' ),
                    'desc'    => __( 'If you have issues downloading the images, increasing this might help', 'stockpack' ),
                    'type'    => 'number',
                    'default' => 30,
                ),
            )
        );
    }

    /**
     * Get the value of a settings field
     *
     * @param string $option settings field name
     * @param string $section the section name this field belongs to
     * @param string $default default text if it's not found
     *
     * @return mixed
     */
    private function get_option( $option, $section, $default = '' ) {

        $options = get_option( $section, array() );

        if ( isset( $options[ $option ] ) ) {
            return $options[ $option ];
        }

        return $default;
    }

    /**
     * Set the value of a settings field
     *
     * @param string $option settings field name
     * @param string $section the section name this field belongs to
     * @param string $value
     *
     * @return mixed
     */
    private function set_option( $option, $section, $value ) {

        $options = get_option( $section, array() );
        if ( ! is_array( $options ) ) {
            $options = array();
        }

        $options[ $option ] = $value;

        update_option( $section, $options );
    }
}
