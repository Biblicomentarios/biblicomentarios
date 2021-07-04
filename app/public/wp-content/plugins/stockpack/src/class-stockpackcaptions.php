<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! class_exists( 'StockpackCaptions' ) ) {
    class StockpackCaptions {
        /**
         * @var Singleton The reference the *Singleton* instance of this class
         */
        private static $instance;

        /** @var StockpackAdmin */
        public $admin;

        /** @var StockpackQuery */
        public $query;

        /** @var StockpackSettings */
        public $settings;

        public $initialized = false;

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
            add_action( 'init', array( $this, 'init' ) );
        }

        /**
         *
         */
        public function init() {
            if ( $this->initialized ) {
                return $this;
            }
            $this->initialized = true;
            $this->admin = \StockpackAdmin::get_instance();
            $this->query = \StockpackQuery::get_instance();
            $this->settings = \StockpackSettings::get_instance();
            $this->actions();
            $this->filters();

            return $this;
        }

        /**
         *
         */
        public function filters() {
            add_filter( 'stockpack_caption', array( $this, 'add_caption_to_premium' ), 10, 2 );
            add_filter( 'attachment_fields_to_edit', array( $this, 'add_caption_fields' ), 10, 2 );
            add_filter( 'attachment_fields_to_save', array( $this, 'save_caption_fields' ), 10, 2 );
        }

        /**
         *
         */
        public function actions() {

        }

        public function add_caption_to_premium( $caption, $image ) {
            if ( ! $this->query->is_provider_premium( $image->data->provider ) ) {
                return $caption;
            }
            if ( $this->settings->get_premium_providers_caption_setting() === 'yes' ) {
                return $caption;
            }

            return '';

        }

        public function add_caption_fields( $form_fields, $post ) {
            if ( $this->settings->get_standard_fields_caption_setting() !== 'yes' ) {
                return $form_fields;
            }

            $checked = get_post_meta( $post->ID, 'stockpack_caption_generate', true );
            $checked = ( $checked == 1 ) ? 'checked="checked"' : "";
            $form_fields['stockpack_caption_generate'] = array(
                'label' => __( 'Generate', 'stockpack' ),
                'input' => 'html',
                'html'  => '<input type="checkbox" id="attachments-' . $post->ID . '-checkbox_field" name="attachments[' . $post->ID . '][stockpack_caption_generate]" value="1" ' . $checked . ' /> ',
                'value' => 1,
                'helps' => 'Changing fields will generate a new caption if this is checked. Uncheck if you want to prevent the caption being overwritten',
            );

            $form_fields['stockpack_author_name'] = array(
                'label' => __( 'Author', 'stockpack' ),
                'input' => 'text',
                'value' => get_post_meta( $post->ID, 'stockpack_author_name', true ),
                'helps' => 'The full name of the author (caption/credit)',
            );

            $form_fields['stockpack_author_url'] = array(
                'label' => __( 'Author URL', 'stockpack' ),
                'input' => 'text',
                'value' => get_post_meta( $post->ID, 'stockpack_author_url', true ),
                'helps' => 'The full URL of the author (caption/credit)',
            );

            $form_fields['stockpack_provider'] = array(
                'label' => __( 'Source', 'stockpack' ),
                'input' => 'text',
                'value' => get_post_meta( $post->ID, 'stockpack_provider', true ),
                'helps' => 'The full name of the image source. It can also be used as the image Title (caption/credit)',
            );

            $form_fields['stockpack_image_url'] = array(
                'label' => __( 'Source URL', 'stockpack' ),
                'input' => 'text',
                'value' => get_post_meta( $post->ID, 'stockpack_image_url', true ),
                'helps' => 'The full URL of the image (caption/credit)',
            );

            $form_fields['stockpack_license'] = array(
                'label' => __( 'License', 'stockpack' ),
                'input' => 'text',
                'value' => get_post_meta( $post->ID, 'stockpack_license', true ),
                'helps' => 'The full name of the image license source (caption/credit)',
            );

            $form_fields['stockpack_license_url'] = array(
                'label' => __( 'License URL', 'stockpack' ),
                'input' => 'text',
                'value' => get_post_meta( $post->ID, 'stockpack_license_url', true ),
                'helps' => 'The full URL of the image license source (caption/credit)',
            );

            $form_fields['stockpack_modification'] = array(
                'label' => __( 'Mods', 'stockpack' ),
                'input' => 'text',
                'value' => get_post_meta( $post->ID, 'stockpack_modification', true ),
                'helps' => 'State any alterations, eg:desaturated (caption/credit)',
            );

            $form_fields['stockpack_extra'] = array(
                'label' => __( 'Extra', 'stockpack' ),
                'input' => 'text',
                'value' => get_post_meta( $post->ID, 'stockpack_extra', true ),
                'helps' => 'You can use this field to add further description. It will show up first and create a new line',
            );


            return $form_fields;
        }

        function generate_license_link( $post, $attachment ) {
            if ( ! isset( $attachment['stockpack_license'] ) ) {
                return $post;
            }

            $autocomplete_options = array(
                'ccby'   => array(
                    'name' => 'CC BY 4.0',
                    'url'  => 'https://creativecommons.org/licenses/by/4.0/',
                ),
                'cc0'    => array(
                    'name' => 'CC0',
                    'url'  => 'https://creativecommons.org/share-your-work/public-domain/cc0/'
                ),
                'public' => array(
                    'name' => 'Public Domain',
                    'url'  => 'https://creativecommons.org/share-your-work/public-domain/'

                )
            );

            $autocomplete_options = apply_filters( 'stockpack_autocomplete_licenses', $autocomplete_options, $attachment );


            $key = str_replace( ' ', '', strtolower( $attachment['stockpack_license'] ) );
            if ( isset( $autocomplete_options[ $key ] ) ) {
                update_post_meta( $post['ID'], 'stockpack_license', $autocomplete_options[ $key ]['name'] );
                update_post_meta( $post['ID'], 'stockpack_license_url', $autocomplete_options[ $key ]['url'] );
            }

            return $post;
        }


        function save_caption_fields( $post, $attachment ) {
            if ( $this->settings->get_standard_fields_caption_setting() !== 'yes' ) {
                return $post;
            }

            $fields = array(
                'stockpack_author_name',
                'stockpack_author_url',
                'stockpack_provider',
                'stockpack_image_url',
                'stockpack_license',
                'stockpack_license_url',
                'stockpack_modification',
                'stockpack_caption_generate',
                'stockpack_modification'
            );

            foreach ( $fields as $field ) {
                if ( isset( $attachment[ $field ] ) ) {
                    update_post_meta( $post['ID'], $field, $attachment[ $field ] );
                } else {
                    delete_post_meta( $post['ID'], $field );
                }
            }


            $post = $this->generate_license_link( $post, $attachment );
            if ( isset( $attachment['stockpack_caption_generate'] ) ) {
                $post['post_excerpt'] = $this->generate_caption( $attachment );
            }

            return $post;
        }

        public function generate_caption( $attachment ) {
            /** Based on @https://wiki.creativecommons.org/wiki/best_practices_for_attribution
             * Code left verbose to be simpler to modify and copy
             */

        $caption ='';
            if ( isset( $attachment['stockpack_extra'] ) && $attachment['stockpack_extra']) {
                $caption .= '<div class="stockpack-extra-caption">'.$attachment['stockpack_extra'].'</div>';
            }

            $caption .= __( 'Photo by', 'stockpack' ) . ' ';
            /** Author */
            if ( isset( $attachment['stockpack_author_url'] ) && $attachment['stockpack_author_url'] && ( isset( $attachment['stockpack_author_name'] ) ) && $attachment['stockpack_author_name'] ) {
                $caption .= '<a href="' . $attachment['stockpack_author_url'] . '">';
            }
            if ( isset( $attachment['stockpack_author_name'] ) && $attachment['stockpack_author_name'] ) {
                $caption .= $attachment['stockpack_author_name'];
            }

            if ( isset( $attachment['stockpack_author_url'] ) && $attachment['stockpack_author_url'] && ( isset( $attachment['stockpack_author_name'] ) ) && $attachment['stockpack_author_name'] ) {
                $caption .= '</a>';
            }
            /** End Author */


            /** Provider/Image title/Image Source */
            if ( ( isset( $attachment['stockpack_provider'] ) && $attachment['stockpack_provider'] ) ) {
                $caption .= ' ' . __( 'on', 'stockpack' ) . ' ';
            }

            if ( isset( $attachment['stockpack_image_url'] ) && $attachment['stockpack_image_url'] && ( isset( $attachment['stockpack_provider'] ) ) && $attachment['stockpack_provider'] ) {
                $caption .= '<a href="' . $attachment['stockpack_image_url'] . '">';
            }

            if ( isset( $attachment['stockpack_provider'] ) && $attachment['stockpack_provider'] ) {
                $caption .= $attachment['stockpack_provider'];
            }

            if ( isset( $attachment['stockpack_image_url'] ) && $attachment['stockpack_image_url'] && ( isset( $attachment['stockpack_provider'] ) ) && $attachment['stockpack_provider'] ) {
                $caption .= '</a>';
            }
            /** End Provider/Image title/Image Source */


            /** License */
            if ( ( isset( $attachment['stockpack_license'] ) && $attachment['stockpack_license'] ) || ( isset( $attachment['stockpack_license_url'] ) && $attachment['stockpack_license_url'] ) ) {
                $caption .= ' ' . __( 'used under', 'stockpack' ) . ' ';
            }

            if ( isset( $attachment['stockpack_license_url'] ) && $attachment['stockpack_license_url'] && ( isset( $attachment['stockpack_license'] ) && $attachment['stockpack_license'] ) ) {
                $caption .= '<a href="' . $attachment['stockpack_license_url'] . '">';
            }

            if ( isset( $attachment['stockpack_license'] ) && $attachment['stockpack_license'] ) {
                $caption .= $attachment['stockpack_license'];
            }

            if ( isset( $attachment['stockpack_license_url'] ) && $attachment['stockpack_license_url'] && ( isset( $attachment['stockpack_license'] ) && $attachment['stockpack_license'] ) ) {
                $caption .= '</a>';
            }
            /** End License */

            if ( isset( $attachment['stockpack_modification'] ) && $attachment['stockpack_modification'] ) {
                $caption .= ' / ' . $attachment['stockpack_modification'];
            }

            return apply_filters( 'stockpack_generated_caption', $caption, $attachment );


        }

        public function featured_image_caption( $attachment_id ) {
            return stockpack_fetch_caption( $attachment_id );
        }


    }

    $GLOBALS['stockpack_captions'] = StockpackCaptions::get_instance();
}

