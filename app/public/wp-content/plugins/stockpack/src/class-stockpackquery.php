<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! class_exists( 'StockpackQuery' ) ) {
    class StockpackQuery {
        /**
         * @var Singleton The reference the *Singleton* instance of this class
         */
        private static $instance;

        /**
         * Api url
         */
        private $url = 'https://api.stockpack.co/api';
        /**
         * Api version
         */
        const VERSION = 'v1';

        public $admin;

        public $settings;

        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * StockPack constructor.
         */
        protected function __construct() {
            if ( ! defined( 'STOCKPACK_URL' ) ) {
                $this->url = 'https://api.stockpack.co/api';
            } else {
                $this->url = STOCKPACK_URL;
            }

            add_action( 'init', array( $this, 'init' ) );
        }

        /**
         *
         */
        public function init() {
            $this->admin = \StockpackAdmin::get_instance();
            $this->settings = \StockpackSettings::get_instance();
            $this->actions();
            $this->filters();
        }

        /**
         *
         */
        public function filters() {

        }

        /**
         *
         */
        public function actions() {
            add_action( 'wp_ajax_query-stockpack', array( $this, 'query' ) ); // executed when logged in
            add_action( 'wp_ajax_license_cost-stockpack', array(
                $this,
                'license_cost'
            ) ); // executed when logged in
            add_action( 'wp_ajax_download-stockpack', array( $this, 'download' ) ); // executed when logged in
            add_action( 'wp_ajax_cache-stockpack', array( $this, 'cache' ) ); // executed when logged in
            add_action( 'wp_ajax_validate-stockpack', array( $this, 'validate' ) ); // executed when logged in
            add_action( 'wp_ajax_terms-stockpack', array( $this, 'terms' ) ); // executed when logged in
            add_action( 'wp_ajax_token-stockpack', array( $this, 'token' ) ); // executed when logged in
        }

        public function terms() {
            check_ajax_referer( 'stockpack_terms', 'security' );

            $provider = '';
            if ( isset( $_REQUEST['provider'] ) ) {
                $provider = sanitize_key( $_REQUEST['provider'] );
            }

            wp_send_json_success( update_option( 'terms_accepted_' . $provider, true ) );
        }

        public function license_cost() {
            check_ajax_referer( 'stockpack_license_cost', 'security' );
            $media_id = '';
            if ( isset( $_REQUEST['media_id'] ) ) {
                $media_id = sanitize_text_field( $_REQUEST['media_id'] );
            }

            $provider = 0;
            if ( isset( $_REQUEST['provider'] ) ) {
                $provider = sanitize_text_field( $_REQUEST['provider'] );
            }

            $response = $this->get_license_cost( $media_id, $provider );

            if ( isset( $response->data->error ) ) {
                wp_send_json_error( $this->handle_license_errors( $response->data ) );
                die();
            }

            if ( is_wp_error( $response ) ) {
                wp_send_json_error( $this->handle_license_errors( $response ) );
                die();
            }

            if ( $response->data->cost == - 1 ) {
                $response->data->cost_message = __( 'You already own the asset, you can proceed without additional cost', 'stockpack' );
            }

            if ( $response->data->cost == 1 && ! $response->data->cost_message ) {
                $response->data->cost_message = __( 'You will be charged for one image on you provider account', 'stockpack' );
            }

            wp_send_json_success( $response->data, true );
        }

        public function token() {
            global $stockpack;
            check_ajax_referer( 'stockpack_token', 'security' );
            $token = '';
            if ( isset( $_REQUEST['token'] ) ) {
                $token = sanitize_text_field( $_REQUEST['token'] );
            }
            wp_send_json_success( $stockpack->admin->set_api_key( $token ), true );
        }

        /**
         *
         */
        public function validate() {
            check_ajax_referer( 'stockpack_validate', 'security' );
            $key = '';
            if ( isset( $_REQUEST['key'] ) ) {
                $key = sanitize_key( $_REQUEST['key'] );
            }
            wp_send_json_success( $this->call( 'GET', 'validate/token', array( 'key' => $key ) ) );
        }

        /**
         *
         */
        public function query() {
            check_ajax_referer( 'stockpack_query', 'security' );
            if ( ! current_user_can( 'upload_files' ) ) {
                wp_send_json_error();
            }
            $query = array();
            if ( isset( $_REQUEST['query'] ) ) {
                $query = array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['query'] ) );
            }

            if ( ! isset( $query['query'] ) ) {
                return wp_send_json_success( array() );
            }

            $images = $this->images( $query );
            if ( is_wp_error( $images ) ) {
                /** @var WP_Error $images */
                wp_send_json_error( $images );
                die();
            }

            if ( isset( $images->data->error ) ) {
                wp_send_json_error( $this->handle_search_errors( $images->data ) );
                die();
            }

            wp_send_json_success( $this->format( $images->data ) );
        }

        /**
         *
         */
        public function cache() {
            check_ajax_referer( 'stockpack_cache', 'security' );

            if ( ! current_user_can( 'upload_files' ) ) {
                wp_send_json_error();
            }
            $id = 0;

            if ( isset( $_REQUEST['media_id'] ) ) {
                $id = sanitize_text_field( $_REQUEST['media_id'] );
            }
            $args = array(
                'post_type'   => 'attachment',
                'post_status' => 'inherit',
                'meta_query'  => array(
                    array(
                        'key'   => 'stockpack_id',
                        'value' => $id
                    )
                )
            );
            $query = new WP_Query( $args );
            if ( ! $query->have_posts() ) {
                wp_send_json_error( array( 'message' => __( 'Image not found in cache', 'stockpack' ) ) );
            }

            wp_send_json_success( wp_prepare_attachment_for_js( $query->post->ID ) );

        }

        /**
         *
         */
        public function download() {
            check_ajax_referer( 'stockpack_download', 'security' );
            if ( ! current_user_can( 'upload_files' ) ) {
                wp_send_json_error( array( 'message' => __( 'You are not allowed to upload files', 'stockpack' ) ) );
            }

            $id = 0;
            if ( isset( $_REQUEST['media_id'] ) ) {
                $id = sanitize_text_field( $_REQUEST['media_id'] );
            }
            $post_id = 0;
            if ( isset( $_REQUEST['post_id'] ) ) {
                $post_id = sanitize_text_field( $_REQUEST['post_id'] );
            }
            $provider = 0;
            if ( isset( $_REQUEST['provider'] ) ) {
                $provider = sanitize_text_field( $_REQUEST['provider'] );
            }
            $must_license = false;
            if ( isset( $_REQUEST['must_license'] ) ) {
                $must_license = sanitize_text_field( $_REQUEST['must_license'] );
            }
            $new_filename = "";
            if ( isset( $_REQUEST['new_filename'] ) ) {
                $new_filename = sanitize_text_field( $_REQUEST['new_filename'] );
            }
            $search = isset( $_REQUEST['search_key'] ) ? sanitize_title( $_REQUEST['search_key'] ) : $this->get_domain();
            $description = isset( $_REQUEST['description'] ) ? sanitize_textarea_field( $_REQUEST['description'] ) : '';
            $image = $this->image( $id, $post_id, $description, $search, $must_license, $provider, $new_filename );

            if ( is_wp_error( $image ) ) {
                if ( strpos( $image->get_error_code(), 'limit_reached' ) !== false ) {
                    wp_send_json_error( $image );
                    die();
                }
                wp_send_json_error( array( 'message' => $image->get_error_message() ) );
            }

            wp_send_json_success( wp_prepare_attachment_for_js( $image ) );

        }



        public function is_provider_premium( $provider ) {
            return in_array( $provider, array(
                    'adobe_stock',
                    'getty',
                    'istock',
                    'deposit_photos'
                )
            );
        }

        public function images( $query = [] ) {

            return $this->call( 'GET', 'images/search', $query );
        }


        /**
         * @param $data
         *
         * @return array
         */
        private function format( $data ) {
            return $data;
        }


        private function get_license_cost( $media_id, $provider ) {

            return $this->call( 'GET', 'images/license-cost', array(
                'id'       => $media_id,
                'locale'   => get_locale(),
                'provider' => $provider
            ) );

        }

        private function image( $media_id, $post_id, $description = '', $search = '', $must_license = 0, $provider = 0, $new_filename = '' ) {

            $image = $this->call( 'GET', 'images/download',
                array(
                    'id'           => $media_id,
                    'must_license' => $must_license,
                    'provider'     => $provider
                )
            );

            if ( ! isset( $image->data->download ) ) {
                if ( is_wp_error( $image ) ) {
                    return $image;
                }

                return $this->handle_download_errors( $image->data );
            }

            $filename = $image->data->name;

            if ( $new_filename ) {
                $filename = $new_filename;
            }

            $caption = $image->data->caption ?: '';
            $attachment_id = $this->upload_remote_image_and_attach( $image->data->download, $post_id, $description, $filename, $search, $caption, $image );
            if ( is_wp_error( $attachment_id ) ) {
                return $attachment_id;
            }
            if ( ! $attachment_id ) {
                return new WP_Error( __( 'upload_failure', 'There has been a problem with the upload. Please try again', 'stockpack' ) );
            }

            update_post_meta( $attachment_id, 'stockpack_id', $media_id );

            return $attachment_id;
        }

        private function handle_license_errors( $response ) {
            if ( isset( $response->code ) ) {
                switch ( $response->code ) {
                    case 'token_expired':
                        return new WP_Error( 'license_cost_failure', __( 'Please reconnect the account on StockPack, the permissions have expired. Go the providers page on stockpack.co/providers', 'stockpack' ) );
                }
            }

            return new WP_Error( 'license_cost_failure', __( 'There has been a problem fetching the cost. Please try to reconnect the account on the providers page on stockpack.co/providers', 'stockpack' ) );
        }

        private function handle_download_errors( $response ) {
            if ( isset( $response->code ) ) {
                switch ( $response->code ) {
                    case 'token_expired':
                        return new WP_Error( 'token_expired', __( 'Please reconnect the account on StockPack, the permissions have expired. Go the providers page on stockpack.co/providers', 'stockpack' ) );
                }
            }

            return new WP_Error( 'download_failure', __( 'There has been a problem with the download. Try to reconnect the account for the provider on StockPack. If the issue persists, contact stockpack support.', 'stockpack' ) );
        }

        private function handle_search_errors( $response ) {
            if ( isset( $response->code ) ) {
                switch ( $response->code ) {
                    case 'token_expired':
                        return new WP_Error( 'token_expired', __( 'Please reconnect the account on stockpack, the permissions have expired. Go to the providers page on stockpack.co/providers', 'stockpack' ) );
                }
            }

            return new WP_Error( 'search_failure', __( 'There has been a problem with the api, please check the accounts on the providers page in stockpack.co/providers', 'stockpack' ) );

        }


        /**
         * @param       $method
         * @param       $path
         * @param array $query
         *
         * @return mixed|null
         */
        private function call( $method, $path, $query = [] ) {
            /** @var StockPack $stockpack */
            global $stockpack;


            if ( ! isset( $query['key'] ) ) {
                $api_key = $stockpack->settings->get_api_key();
                if ( $api_key ) {
                    $query = array_merge( $query,
                        [
                            'api_token' => $api_key
                        ]
                    );
                } else {
                    $path .= '-trial';
                }
            }

            $query = array_merge( $query,
                [
                    'referral'    => get_bloginfo( 'url' ),
                    'safe_search' => $stockpack->settings->get_safe_search(),
                    'user_data'   => $stockpack->settings->get_license_state(),
                ]
            );

            $url = $this->url . '/' . self::VERSION . '/' . $path . '?' . http_build_query( $query );

            do_action( 'stockpack_before_api_request', $url, $query );

            $response = wp_remote_get( $url, array(
                'timeout' => 10
            ) );

            do_action( 'stockpack_after_api_request', $url, $query, $response );

            if ( is_wp_error( $response ) ) {
                return $response;
            }

            $response_code = wp_remote_retrieve_response_code( $response );
            if ( $response_code !== 200 ) {
                switch ( $response_code ) {
                    case 429: // too many requests
                        return $this->limit_exceeded( $response );
                    case 401: // access denied
                        return $this->invalid_token( $response );
                    default:
                        return new WP_Error( 1, __( 'Can\'t connect to StockPack server.', 'stockpack' ) );
                }
            }

            $response_body = wp_remote_retrieve_body( $response );

            return json_decode( $response_body );
        }

        private function limit_exceeded( $response ) {
            /** @var StockPack $stockpack */
            global $stockpack;


            $limit = wp_remote_retrieve_header( $response, 'x-ratelimit-limit' );
            $retry_after = wp_remote_retrieve_header( $response, 'retry-after' );
            $retry_after = $this->human_seconds( $retry_after );
            $retry_after = '<span class ="stockpack-timer">' . $retry_after . '</span>';
            if ( $limit > 50 ) {
                return new WP_Error( 'premium_limit_reached', __( 'Request limit reached, the reset will be in ', 'stockpack' ) . $retry_after );
            }
            $api_key = $stockpack->admin->get_api_key();
            if ( $api_key ) {
                return new WP_Error( 'free_limit_reached', __( 'Request limit reached, the reset will be in ', 'stockpack' ) . $retry_after );
            }


            return new WP_Error( 'anonymous_limit_reached', __( 'Request limit reached, the reset will be in ', 'stockpack' ) . $retry_after );
        }

        private function invalid_token( $response ) {
            return new WP_Error( 'invalid_token', __( 'Request denied. Please check your token!', 'stockpack' ) );
        }

        private function support_caption_translation( $caption ) {
            if ( ! $caption ) {
                return '';
            }

            $caption = str_replace( 'Photo by', __( 'Photo by', 'stockpack' ), $caption );
            $caption = str_replace( ' on ', _x( ' on ', 'Make sure to keep the space', 'stockpack' ), $caption );

            return $caption;
        }

        /**
         * @param        $image_url
         * @param        $parent_id
         *
         * @param        $description
         *
         * @param        $name
         * @param string $search
         *
         * @param string $caption
         *
         * @return bool|int|WP_Error
         */
        private function upload_remote_image_and_attach( $image_url, $parent_id, $description, $name, $search = '', $caption = '', $image ) {

            $get = wp_remote_get( $image_url, array(
                    'timeout' => apply_filters( 'stockpack_download_timeout', 30 )
                )
            );
            if ( is_wp_error( $get ) ) {
                return new WP_Error( 100, __( 'Image download failed, please try again. Errors:', 'stockpack' ) . PHP_EOL . $get->get_error_message() );
            }
            $type = wp_remote_retrieve_header( $get, 'content-type' );
            if ( ! $type ) {
                return new WP_Error( 100, __( 'Image type couldn\'t be determined', 'stockpack' ) );
            }
            $name = $this->update_extension( $name, $type );

            $mirror = wp_upload_bits( $name, '', wp_remote_retrieve_body( $get ) );
            $attachment = array(
                'post_title'     => $this->strip_extension($name),
                'post_content'   => $description,
                'post_excerpt'   => apply_filters( 'stockpack_caption', $this->support_caption_translation( $caption ), $image ),
                'post_mime_type' => $type
            );
            $attach_id = wp_insert_attachment( $attachment, $mirror['file'], $parent_id );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            update_post_meta( $attach_id, '_wp_attachment_image_alt', $description );
            $this->store_attachment_meta($attach_id,$image);

            do_action( 'stockpack_after_attachment_uploaded', $attach_id,$image );

            return $attach_id;

        }

        private function store_attachment_meta($attach_id, $image){
            $data = $image->data;
            update_post_meta($attach_id,'stockpack_media_id',$data->id);
            update_post_meta($attach_id,'stockpack_provider',$data->meta->provider);
            update_post_meta($attach_id,'stockpack_author_url',$data->meta->author_url);
            update_post_meta($attach_id,'stockpack_author_name',$data->meta->author_name);
            update_post_meta($attach_id,'stockpack_image_url',$data->meta->image_url);
            update_post_meta($attach_id,'stockpack_download_timestamp',time());
        }

        /**
         * @return mixed
         */
        private function get_domain() {
            $url = get_bloginfo( 'url' );
            $parse = wp_parse_url( $url );

            return strtok( $parse['host'], '.' );
        }


        private function human_seconds( $seconds, $separator = ":" ) {
            return sprintf( "%02d%s%02d%s%02d", floor( $seconds / 3600 ), $separator, ( $seconds / 60 ) % 60, $separator, $seconds % 60 );
        }

        private function update_extension( $name, $mime ) {
            $name = explode( '.', $name );
            $extension = $this->mime2ext( $mime );
            if ( $extension ) {
                return $name[0] . '.' . $extension;
            }

            return $name[0] . '.' . $name[1];

        }

        private function strip_extension( $name ) {
            $name = explode( '.', $name );
            return $name[0];
        }

        private function mime2ext( $mime ) {
            $mime = str_replace( ';charset=UTF-8', '', $mime );
            $mime_map = [
                'video/3gpp2'                                                               => '3g2',
                'video/3gp'                                                                 => '3gp',
                'video/3gpp'                                                                => '3gp',
                'application/x-compressed'                                                  => '7zip',
                'audio/x-acc'                                                               => 'aac',
                'audio/ac3'                                                                 => 'ac3',
                'application/postscript'                                                    => 'ai',
                'audio/x-aiff'                                                              => 'aif',
                'audio/aiff'                                                                => 'aif',
                'audio/x-au'                                                                => 'au',
                'video/x-msvideo'                                                           => 'avi',
                'video/msvideo'                                                             => 'avi',
                'video/avi'                                                                 => 'avi',
                'application/x-troff-msvideo'                                               => 'avi',
                'application/macbinary'                                                     => 'bin',
                'application/mac-binary'                                                    => 'bin',
                'application/x-binary'                                                      => 'bin',
                'application/x-macbinary'                                                   => 'bin',
                'image/bmp'                                                                 => 'bmp',
                'image/x-bmp'                                                               => 'bmp',
                'image/x-bitmap'                                                            => 'bmp',
                'image/x-xbitmap'                                                           => 'bmp',
                'image/x-win-bitmap'                                                        => 'bmp',
                'image/x-windows-bmp'                                                       => 'bmp',
                'image/ms-bmp'                                                              => 'bmp',
                'image/x-ms-bmp'                                                            => 'bmp',
                'application/bmp'                                                           => 'bmp',
                'application/x-bmp'                                                         => 'bmp',
                'application/x-win-bitmap'                                                  => 'bmp',
                'application/cdr'                                                           => 'cdr',
                'application/coreldraw'                                                     => 'cdr',
                'application/x-cdr'                                                         => 'cdr',
                'application/x-coreldraw'                                                   => 'cdr',
                'image/cdr'                                                                 => 'cdr',
                'image/x-cdr'                                                               => 'cdr',
                'zz-application/zz-winassoc-cdr'                                            => 'cdr',
                'application/mac-compactpro'                                                => 'cpt',
                'application/pkix-crl'                                                      => 'crl',
                'application/pkcs-crl'                                                      => 'crl',
                'application/x-x509-ca-cert'                                                => 'crt',
                'application/pkix-cert'                                                     => 'crt',
                'text/css'                                                                  => 'css',
                'text/x-comma-separated-values'                                             => 'csv',
                'text/comma-separated-values'                                               => 'csv',
                'application/vnd.msexcel'                                                   => 'csv',
                'application/x-director'                                                    => 'dcr',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
                'application/x-dvi'                                                         => 'dvi',
                'message/rfc822'                                                            => 'eml',
                'application/x-msdownload'                                                  => 'exe',
                'video/x-f4v'                                                               => 'f4v',
                'audio/x-flac'                                                              => 'flac',
                'video/x-flv'                                                               => 'flv',
                'image/gif'                                                                 => 'gif',
                'application/gpg-keys'                                                      => 'gpg',
                'application/x-gtar'                                                        => 'gtar',
                'application/x-gzip'                                                        => 'gzip',
                'application/mac-binhex40'                                                  => 'hqx',
                'application/mac-binhex'                                                    => 'hqx',
                'application/x-binhex40'                                                    => 'hqx',
                'application/x-mac-binhex40'                                                => 'hqx',
                'text/html'                                                                 => 'html',
                'image/x-icon'                                                              => 'ico',
                'image/x-ico'                                                               => 'ico',
                'image/vnd.microsoft.icon'                                                  => 'ico',
                'text/calendar'                                                             => 'ics',
                'application/java-archive'                                                  => 'jar',
                'application/x-java-application'                                            => 'jar',
                'application/x-jar'                                                         => 'jar',
                'image/jp2'                                                                 => 'jp2',
                'video/mj2'                                                                 => 'jp2',
                'image/jpx'                                                                 => 'jp2',
                'image/jpm'                                                                 => 'jp2',
                'image/jpeg'                                                                => 'jpg',
                'image/pjpeg'                                                               => 'jpg',
                'application/x-javascript'                                                  => 'js',
                'application/json'                                                          => 'json',
                'text/json'                                                                 => 'json',
                'application/vnd.google-earth.kml+xml'                                      => 'kml',
                'application/vnd.google-earth.kmz'                                          => 'kmz',
                'text/x-log'                                                                => 'log',
                'audio/x-m4a'                                                               => 'm4a',
                'application/vnd.mpegurl'                                                   => 'm4u',
                'audio/midi'                                                                => 'mid',
                'application/vnd.mif'                                                       => 'mif',
                'video/quicktime'                                                           => 'mov',
                'video/x-sgi-movie'                                                         => 'movie',
                'audio/mpeg'                                                                => 'mp3',
                'audio/mpg'                                                                 => 'mp3',
                'audio/mpeg3'                                                               => 'mp3',
                'audio/mp3'                                                                 => 'mp3',
                'video/mp4'                                                                 => 'mp4',
                'video/mpeg'                                                                => 'mpeg',
                'application/oda'                                                           => 'oda',
                'audio/ogg'                                                                 => 'ogg',
                'video/ogg'                                                                 => 'ogg',
                'application/ogg'                                                           => 'ogg',
                'application/x-pkcs10'                                                      => 'p10',
                'application/pkcs10'                                                        => 'p10',
                'application/x-pkcs12'                                                      => 'p12',
                'application/x-pkcs7-signature'                                             => 'p7a',
                'application/pkcs7-mime'                                                    => 'p7c',
                'application/x-pkcs7-mime'                                                  => 'p7c',
                'application/x-pkcs7-certreqresp'                                           => 'p7r',
                'application/pkcs7-signature'                                               => 'p7s',
                'application/pdf'                                                           => 'pdf',
                'application/octet-stream'                                                  => 'pdf',
                'application/x-x509-user-cert'                                              => 'pem',
                'application/x-pem-file'                                                    => 'pem',
                'application/pgp'                                                           => 'pgp',
                'application/x-httpd-php'                                                   => 'php',
                'application/php'                                                           => 'php',
                'application/x-php'                                                         => 'php',
                'text/php'                                                                  => 'php',
                'text/x-php'                                                                => 'php',
                'application/x-httpd-php-source'                                            => 'php',
                'image/png'                                                                 => 'png',
                'image/x-png'                                                               => 'png',
                'application/powerpoint'                                                    => 'ppt',
                'application/vnd.ms-powerpoint'                                             => 'ppt',
                'application/vnd.ms-office'                                                 => 'ppt',
                'application/msword'                                                        => 'doc',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
                'application/x-photoshop'                                                   => 'psd',
                'image/vnd.adobe.photoshop'                                                 => 'psd',
                'audio/x-realaudio'                                                         => 'ra',
                'audio/x-pn-realaudio'                                                      => 'ram',
                'application/x-rar'                                                         => 'rar',
                'application/rar'                                                           => 'rar',
                'application/x-rar-compressed'                                              => 'rar',
                'audio/x-pn-realaudio-plugin'                                               => 'rpm',
                'application/x-pkcs7'                                                       => 'rsa',
                'text/rtf'                                                                  => 'rtf',
                'text/richtext'                                                             => 'rtx',
                'video/vnd.rn-realvideo'                                                    => 'rv',
                'application/x-stuffit'                                                     => 'sit',
                'application/smil'                                                          => 'smil',
                'text/srt'                                                                  => 'srt',
                'image/svg+xml'                                                             => 'svg',
                'application/x-shockwave-flash'                                             => 'swf',
                'application/x-tar'                                                         => 'tar',
                'application/x-gzip-compressed'                                             => 'tgz',
                'image/tiff'                                                                => 'tiff',
                'text/plain'                                                                => 'txt',
                'text/x-vcard'                                                              => 'vcf',
                'application/videolan'                                                      => 'vlc',
                'text/vtt'                                                                  => 'vtt',
                'audio/x-wav'                                                               => 'wav',
                'audio/wave'                                                                => 'wav',
                'audio/wav'                                                                 => 'wav',
                'application/wbxml'                                                         => 'wbxml',
                'video/webm'                                                                => 'webm',
                'audio/x-ms-wma'                                                            => 'wma',
                'application/wmlc'                                                          => 'wmlc',
                'video/x-ms-wmv'                                                            => 'wmv',
                'video/x-ms-asf'                                                            => 'wmv',
                'application/xhtml+xml'                                                     => 'xhtml',
                'application/excel'                                                         => 'xl',
                'application/msexcel'                                                       => 'xls',
                'application/x-msexcel'                                                     => 'xls',
                'application/x-ms-excel'                                                    => 'xls',
                'application/x-excel'                                                       => 'xls',
                'application/x-dos_ms_excel'                                                => 'xls',
                'application/xls'                                                           => 'xls',
                'application/x-xls'                                                         => 'xls',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
                'application/vnd.ms-excel'                                                  => 'xlsx',
                'application/xml'                                                           => 'xml',
                'text/xml'                                                                  => 'xml',
                'text/xsl'                                                                  => 'xsl',
                'application/xspf+xml'                                                      => 'xspf',
                'application/x-compress'                                                    => 'z',
                'application/x-zip'                                                         => 'zip',
                'application/zip'                                                           => 'zip',
                'application/x-zip-compressed'                                              => 'zip',
                'application/s-compressed'                                                  => 'zip',
                'multipart/x-zip'                                                           => 'zip',
                'text/x-scriptzsh'                                                          => 'zsh',
                'application/illustrator'                                                   => 'ai',
                'application/eps'                                                           => 'eps',
                'application/x-eps'                                                         => 'eps',
                'image/eps'                                                                 => 'eps',
                'image/x-eps'                                                               => 'eps'
            ];

            return isset( $mime_map[ $mime ] ) === true ? $mime_map[ $mime ] : false;
        }

    }

    $GLOBALS['stockpack_query'] = StockpackQuery::get_instance();
}
