<?php

/**
 * Class StockpackSettings
 *
 * Fetches settings (decouple from admin)
 */
class StockpackSettings {
    /**
     * @var Singleton The reference the *Singleton* instance of this class
     */
    private static $instance;

    /**
     * @var WeDevs_Settings_API
     */
    public $settings_api;

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

    }

    public function get_license_state() {
        return $this->get_option( 'license_state', 'stockpack_advanced' );
    }

    public function get_safe_search() {
        return $this->get_option( 'safe_search', 'stockpack_basics' );
    }

    public function get_file_name_change_setting() {
        return $this->get_option( 'file_name_change', 'stockpack_basics' );
    }

    public function get_premium_providers_caption_setting() {
        return $this->get_option( 'caption_premium_providers', 'stockpack_advanced' );
    }

    public function get_standard_fields_caption_setting() {
        return $this->get_option( 'caption_standard_fields', 'stockpack_advanced' );
    }

    public function get_featured_caption_setting() {
        return $this->get_option( 'caption_featured_image', 'stockpack_advanced' );
    }

    /**
     * @return mixed
     */
    public function get_api_key() {
        $api_key = $this->get_option( 'auth_token', 'stockpack_basics' );

        if ( ! $api_key && defined( 'STOCKPACK_TOKEN' ) ) {
            return STOCKPACK_TOKEN;
        }

        return $api_key;
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

}
