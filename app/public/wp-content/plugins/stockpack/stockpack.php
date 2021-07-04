<?php
/*
 * Plugin Name: StockPack – Stock images in WordPress
 * Plugin URI: https://wordpress.org/plugins/stockpack/
 * Description: Direct image search in WordPress for Unsplash, Adobe Stock, Getty Images, iStock, Pixabay, Pexels and Deposit Photos
 * Author: Derikon Development
 * Author URI: https://derikon.com/
 * Version: 3.3.0
 * Text Domain: stockpack
 * Domain Path: /languages
 *
 * Copyright (c) 2016 Derikon Development
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.xdebu
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
*/


require( __DIR__ . '/vendor/autoload.php' );

define( 'STOCKPACK_DIR', __FILE__ );

if ( ! class_exists( 'Stockpack' ) ) {
    class Stockpack {

        /**
         * @var Singleton The reference the *Singleton* instance of this class
         */
        private static $instance;

        /** @var StockpackAdmin */
        public $admin;

        /** @var StockpackMedia */
        public $media;

        /** @var StockpackQuery */
        public $query;

        /** @var StockpackCaptions */
        public $captions;

        /** @var StockpackSettings */
        public $settings;

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
            $this->dependencies();
            add_action( 'init', array( $this, 'init' ) );
        }

        /**
         *
         */
        public function filters() {
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array(
                $this,
                'plugin_action_links'
            ) );
        }

        /**
         * Init the plugin after plugins_loaded so environment variables are set.
         */
        public function init() {
            // works
            load_plugin_textdomain( 'stockpack', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
            $this->filters();
        }

        /**
         *
         */
        public function dependencies() {
            $this->admin = \StockpackAdmin::get_instance();
            $this->media = \StockpackMedia::get_instance();
            $this->query = \StockpackQuery::get_instance();
            $this->captions = \StockpackCaptions::get_instance();
            $this->settings = \StockpackSettings::get_instance();
        }

        /**
         * Adds plugin action links
         *
         * @since 1.0.0
         */
        public function plugin_action_links( $links ) {
            $setting_link = $this->get_setting_link();
            $plugin_links = array(
                '<a href="' . $setting_link . '">' . __( 'Settings', 'stockpack' ) . '</a>',
            );

            return array_merge( $plugin_links, $links );
        }

        /**
         * Get setting link.
         *
         * @return string Setting link
         * @since 1.0.0
         *
         */
        public function get_setting_link() {

            return admin_url( 'options-general.php?page=stockpack' );
        }

    }

    add_action( 'after_setup_theme', 'stockpack_register_plugin' );
    function stockpack_register_plugin() {
        $load_stockpack = true;
        if ( ! is_admin() ) {
            $load_stockpack = false;
        }

        $stockpack_start = apply_filters( 'load_stockpack', $load_stockpack );
        if ( $stockpack_start || stockpack_frontend_load() || stockpack_cli_load() ) {
            $GLOBALS['stockpack'] = Stockpack::get_instance();
        }
    }

    function stockpack_frontend_load() {
        $load = false;
        if ( isset( $_GET['et_fb'] ) || isset ( $_GET['ct_builder'] ) || isset( $_GET['fl_builder'] ) || isset( $_GET['fb-edit'] ) || isset( $_GET['brizy-edit-iframe'] ) ) {
            $load = true;
        }

        return apply_filters( 'frontend_load_stockpack', $load );
    }

    function stockpack_cli_load() {
        $load = false;
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            WP_CLI::add_command( 'stockpack', 'StockpackCLI' );
            $load = true;
        }

        return apply_filters( 'cli_load_stockpack', $load );
    }

    function stockpack_late_init() {
        global $stockpack;
        // if loaded bail
        if ( $stockpack ) {
            return false;
        }
        stockpack_register_plugin();

    }

    add_action( 'get_header', 'stockpack_late_init' );

}

