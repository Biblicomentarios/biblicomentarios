<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! class_exists( 'StockpackMedia' ) ) {
    class StockpackMedia {
        /**
         * @var Singleton The reference the *Singleton* instance of this class
         */
        private static $instance;

        /**
         * @var WeDevs_Settings_API
         */
        public $settings_api;

        /** @var StockpackAdmin */
        public $admin;

        /** @var StockpackSettings */
        public $settings;

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
         * StockPack constructor.
         */
        protected function __construct() {
            add_action( 'init', array( $this, 'init' ) );
        }

        /**
         *
         */
        public function init() {
            $this->admin = \StockpackAdmin::get_instance();
            $this->settings = \StockpackSettings::get_instance();
            $this->filters();
            $this->actions();
        }

        /**
         *
         */
        public function enqueue( $admin = true, $no_dialog = false ) {
            $this->enqueue_script( $admin );
            $this->enqueue_style( $no_dialog );
            $this->enqueue_settings( $admin );
        }

        public function enqueue_script( $admin ) {
            $name = 'stockpack-load-admin.js';
            $script = 'stockpack';

            $acfe_classic_editor = false;
            if (is_plugin_active( 'acf-extended-pro/acf-extended.php' )) {
                if( function_exists('acfe_get_setting') ) {
                    $acfe_classic_editor = acfe_get_setting('modules/classic_editor');
                }
            }

            if ( ! $admin || is_plugin_active( 'classic-editor/classic-editor.php' ) || $acfe_classic_editor ) {
                add_thickbox();
                wp_enqueue_media();
                $name = 'stockpack-load-frontend.js';
                $script = 'b-stockpack';
            }


            // only brizy removes these scripts
            if ( wp_script_is( 'wp-color-picker' ) === false ) {
                wp_register_script( 'wp-color-picker', "/wp-admin/js/color-picker.min.js", array( 'mediaelement' ), false, 1 );
            }

            if ( wp_script_is( 'jquery-ui-dialog' ) === false ) {
                wp_register_script( 'jquery-ui-dialog', "/wp-includes/js/jquery/ui/dialog.min.js", array(
                    'jquery-ui-resizable',
                    'jquery-ui-draggable',
                    'jquery-ui-button',
                    'jquery-ui-position'
                ), false, 1 );
            }

            wp_enqueue_script( $script, plugins_url( '/dist/js/' . $name, STOCKPACK_DIR ), array(
                'media-views',
                'wp-color-picker',
                'jquery-ui-dialog'
            ), $this->version, true );

            // make sure divi media library is added after
            if ( wp_script_is( 'et_pb_media_library' ) === true ) {
                wp_dequeue_script( 'et_pb_media_library' );
                wp_deregister_script( 'et_pb_media_library' );
                wp_enqueue_script( 'et_pb_media_library', ET_BUILDER_URI . '/scripts/ext/media-library.js', array(
                    'media-editor',
                    $script
                ), ET_BUILDER_PRODUCT_VERSION, true );
            }
        }

        public function enqueue_style( $no_dialog ) {
            global $wp_version;

            wp_enqueue_style( 'wp-color-picker' );
            if ( ! $no_dialog && ! ( isset( $_GET['ct_builder'] ) ) ) {
                wp_enqueue_style( 'wp-jquery-ui-dialog' );
            }
            wp_enqueue_style( 'stockpack-admin', plugins_url( '/dist/css/stockpack.css', STOCKPACK_DIR ), false, $this->version );


            if ( version_compare( $wp_version, '5.3', '<=' ) ) {
                wp_enqueue_style( 'stockpack-admin-old', plugins_url( '/dist/css/stockpack-old-admin.css', STOCKPACK_DIR ), false, $this->version );
            }

            if(is_plugin_active( 'media-library-organizer/media-library-organizer.php' ) ){
                wp_enqueue_style( 'stockpack-mlo-compatibility', plugins_url( '/dist/css/stockpack-mlo-compatibility.css', STOCKPACK_DIR ), false, $this->version );
            }
        }

        public function enqueue_settings( $admin ) {
            if ( $admin && function_exists( 'get_current_screen' ) ) {
                $screen = get_current_screen();
                if ( $screen->id === 'settings_page_stockpack' ) {
                    wp_enqueue_script( 'stockpack-admin', plugins_url( '/dist/js/stockpack-settings.js', STOCKPACK_DIR ), array( 'jquery' ), $this->version, true );
                    wp_enqueue_style( 'settings-stockpack', plugins_url( '/dist/css/stockpack-settings.css', STOCKPACK_DIR ), array(), $this->version );
                }
            }
        }

        public function enqueue_frontend() {
            $this->enqueue( false );
        }

        public function enqueue_elementor() {
            $this->enqueue( true, true );
        }

        public function enqueue_elementor_before() {
            wp_enqueue_style( 'wp-jquery-ui-dialog' );
        }

        public function enqueue_themefusion() {
            $this->enqueue( true, true );
            $this->load_frontend_style();
        }

        public function enqueue_bb() {
            echo '<script src="' . plugins_url( '/dist/js/stockpack-load-admin.js', STOCKPACK_DIR ) . '"></script>';
        }

        public function enqueue_admin() {
            $this->enqueue( true );
        }

        public function load_frontend_style() {
            wp_enqueue_style( 'stockpack-frontend', plugins_url( '/dist/css/stockpack-frontend.css', STOCKPACK_DIR ), false, $this->version );
        }


        /**
         *
         */
        public function filters() {
            add_filter( 'media_view_strings', array( $this, 'tab_text' ), 10, 2 );
            add_filter( 'media_view_settings', array( $this, 'settings' ), 10, 2 );
        }

        public function actions() {
            add_action( 'admin_head', array( $this, 'templates' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ), 99 );
            add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_elementor_before' ) );
            add_action( 'elementor/editor/footer', array( $this, 'templates' ) );
            // elementor is special
            add_action( 'elementor/editor/footer', array( $this, 'enqueue_elementor' ), 99 );
            add_action( 'elementor/editor/footer', array( $this, 'load_frontend_style' ), 99 );

            add_action( 'fusion_builder_enqueue_live_scripts', array( $this, 'enqueue_themefusion' ), 99 );


            if ( stockpack_frontend_load() ) {
                add_action( 'wp_enqueue_scripts', array( $this, 'templates' ) );

                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );

                add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_style' ), 99 );

            }

            if ( ( isset( $_GET['fl_builder'] ) ) ) {
                add_action( 'wp_footer', array( $this, 'enqueue_bb' ), 100 );
            }
        }


        /**
         *
         */
        public function templates() {
            include_once( __DIR__ . '/../templates/attachment.php' );
            include_once( __DIR__ . '/../templates/empty.php' );
            include_once( __DIR__ . '/../templates/details.php' );
            include_once( __DIR__ . '/../templates/downloader.php' );
            include_once( __DIR__ . '/../templates/dialog.php' );
            include_once( __DIR__ . '/../templates/attribution.php' );
            include_once( __DIR__ . '/../templates/no-search.php' );
        }

        /**
         * @param $strings
         * @param $post
         *
         * @return array
         */
        public function tab_text( $strings, $post ) {
            if ( ! defined( 'STOCKPACK_DASHBOARD_URL' ) ) {
                $dashboard_url = 'https://stockpack.co';
            } else {
                $dashboard_url = STOCKPACK_DASHBOARD_URL;
            }

            $plugin_url = str_replace( '/src/', '', plugin_dir_url( __FILE__ ) );

            $strings['stockpack'] = array(
                'title'           => __( 'StockPack', 'stockpack' ),
                'button'          => __( 'Insert into post', 'stockpack' ),
                'retry'           => __( 'Retry', 'stockpack' ),
                'noMedia'         => __( 'No images have been found', 'stockpack' ),
                'noSearch'        => __( 'Start searching for images', 'stockpack' ),
                'inspiration'     => array(
                    'unsure' => __( 'Not sure what to search for?', 'stockpack' ),
                    'random' => array(
                        'item_1' => array(
                            'message' => __( 'Try "balloons" in the top right search input', 'stockpack' ),
                            'image'   => $plugin_url . '/images/balloons.png',
                        ),
                        'item_2' => array(
                            'message' => __( 'Try "unicorn" in the top right search input', 'stockpack' ),
                            'image'   => $plugin_url . '/images/unicorn.png',
                        ),
                        'item_3' => array(
                            'message' => __( 'Try "ice cream" in the top right search input', 'stockpack' ),
                            'image'   => $plugin_url . '/images/icecream.png',
                        ),
                        'item_4' => array(
                            'message' => __( 'Try "moon" in the top right search input', 'stockpack' ),
                            'image'   => $plugin_url . '/images/moon.png',
                        ),
                        'item_5' => array(
                            'message' => __( 'Try "coffee" in the top right search input', 'stockpack' ),
                            'image'   => $plugin_url . '/images/coffee.png',
                        ),
                        'item_6' => array(
                            'message' => __( 'Try "tea" in the top right search input', 'stockpack' ),
                            'image'   => $plugin_url . '/images/tea.png',
                        ),
                        'item_7' => array(
                            'message' => __( 'Try "lighthouse" in the top right search input', 'stockpack' ),
                            'image'   => $plugin_url . '/images/lighthouse.png',
                        ),
                        'item_8' => array(
                            'message' => __( 'Try "mountains" in the top right search input', 'stockpack' ),
                            'image'   => $plugin_url . '/images/mountains.png',
                        )


                    ),
                ),
                'error'           => [
                    'default'                 => __( 'There has been an error', 'stockpack' ),
                    'token_expired'           => __( 'The access you granted for this provider has expired', 'stockpack' ),
                    'invalid_token'           => __( 'Double check the token on the settings page!', 'stockpack' ),
                    'premium_limit_reached'   => __( 'Premium account requests limit reached', 'stockpack' ),
                    'free_limit_reached'      => __( 'Free account requests limit reached', 'stockpack' ),
                    'anonymous_limit_reached' => __( 'Anonymous requests limit reached', 'stockpack' ),
                ],
                'link'            => __( 'Set token', 'stockpack' ),
                'search'          => __( 'Search images', 'stockpack' ),
                'close'           => __( 'Close', 'stockpack' ),
                'download'        => __( 'Download', 'stockpack' ),
                'licenseAction'   => __( 'License', 'stockpack' ),
                'alreadyLicensed' => __( 'Licensed', 'stockpack' ),
                'license'         => __( 'Your token is not valid', 'stockpack' ),
                'advanced'        => __( 'Advanced search', 'stockpack' ),
                'filters'         => [
                    'gender'      => [
                        'label'  => __( 'Gender', 'stockpack' ),
                        'female' => __( 'Female', 'stockpack' ),
                        'male'   => __( 'Male', 'stockpack' ),
                    ],
                    'provider'    => [
                        'label'   => __( 'Provider', 'stockpack' ),
                        'default' => __( 'Default', 'stockpack' ),
                        'values'  => apply_filters( 'stockpack_providers', [
                            'Adobe Stock'    => __( 'Adobe Stock', 'stockpack' ),
                            'Deposit Photos' => __( 'Deposit Photos', 'stockpack' ),
                            'Getty'          => __( 'Getty Images', 'stockpack' ),
                            'iStock'         => __( 'iStock', 'stockpack' ),
                            'Pixabay'        => __( 'Pixabay', 'stockpack' ),
                            'Pexels'         => __( 'Pexels', 'stockpack' ),
                            'Unsplash'       => __( 'Unsplash', 'stockpack' ),
                        ] )
                    ],
                    'orientation' => [
                        'label'      => __( 'Orientation', 'stockpack' ),
                        'horizontal' => __( 'Horizontal', 'stockpack' ),
                        'vertical'   => __( 'Vertical', 'stockpack' ),
                    ],
                    'image_type'  => [
                        'label'        => __( 'Image type', 'stockpack' ),
                        'photo'        => __( 'Photos', 'stockpack' ),
                        'vector'       => __( 'Vectors', 'stockpack' ),
                        'illustration' => __( 'Illustrations', 'stockpack' ),
                    ],
                    'categories'  => [
                        'label'   => __( 'Category', 'stockpack' ),
                        'default' => __( 'Any Category', 'stockpack' ),
                        'values'  => [
                            'Abstract'               => __( 'Abstract', 'stockpack' ),
                            'Animals/Wildlife'       => __( 'Animals/Wildlife', 'stockpack' ),
                            'The Arts'               => __( 'The Arts', 'stockpack' ),
                            'Backgrounds/Textures'   => __( 'Backgrounds/Textures', 'stockpack' ),
                            'Beauty/Fashion'         => __( 'Beauty/Fashion', 'stockpack' ),
                            'Buildings/Landmarks'    => __( 'Buildings/Landmarks', 'stockpack' ),
                            'Business/Finance'       => __( 'Business/Finance', 'stockpack' ),
                            'Celebrities'            => __( 'Celebrities', 'stockpack' ),
                            'Editorial'              => __( 'Editorial', 'stockpack' ),
                            'Education'              => __( 'Education', 'stockpack' ),
                            'Food and Drink'         => __( 'Food and Drink', 'stockpack' ),
                            'Healthcare/Medical'     => __( 'Healthcare/Medical', 'stockpack' ),
                            'Holidays'               => __( 'Holidays', 'stockpack' ),
                            'Illustrations/Clip-Art' => __( 'Illustrations/Clip-Art', 'stockpack' ),
                            'Industrial'             => __( 'Industrial', 'stockpack' ),
                            'Interiors'              => __( 'Interiors', 'stockpack' ),
                            'Miscellaneous'          => __( 'Miscellaneous', 'stockpack' ),
                            'Nature'                 => __( 'Nature', 'stockpack' ),
                            'Objects'                => __( 'Objects', 'stockpack' ),
                            'Parks/Outdoor'          => __( 'Parks/Outdoor', 'stockpack' ),
                            'People'                 => __( 'People', 'stockpack' ),
                            'Religion'               => __( 'Religion', 'stockpack' ),
                            'Science'                => __( 'Science', 'stockpack' ),
                            'Signs/Symbols'          => __( 'Signs/Symbols', 'stockpack' ),
                            'Sports/Recreation'      => __( 'Sports/Recreation', 'stockpack' ),
                            'Technology'             => __( 'Technology', 'stockpack' ),
                            'Transportation'         => __( 'Transportation', 'stockpack' ),
                            'Vectors'                => __( 'Vectors', 'stockpack' ),
                            'Vintage'                => __( 'Vintage', 'stockpack' ),
                        ]
                    ],
                    'safe'        => [
                        'label' => __( 'Safe', 'stockpack' ),
                        'yes'   => __( 'Yes', 'stockpack' ),
                        'no'    => __( 'No', 'stockpack' ),
                    ],
                    'color'       => [
                        'text' => __( 'Color', 'stockpack' ),
                    ],

                ],
                'terms'           => [
                    'agree'          => __( 'I agree', 'stockpack' ),
                    'cancel'         => __( 'Cancel', 'stockpack' ),
                    'adobe_stock'    => [
                        'title'   => __( 'Terms agreement', 'stockpack' ),
                        'message' => __( 'Before you download the first image you need to agree to the terms of service of Adobe Stock. This will only be asked once and then we will store it for all subsequent downloads. You are a direct user of the Adobe Stock website and their terms apply. ', 'stockpack' ),
                        'link'    => 'https://stock.adobe.com/license-terms'
                    ],
                    'getty'          => [
                        'title'   => __( 'Terms agreement', 'stockpack' ),
                        'message' => __( 'Before you download the first image you need to agree to the terms of service of Getty Images. This will only be asked once and then we will store it for all subsequent downloads. You are a direct user of the Getty images website and their terms apply. ', 'stockpack' ),
                        'link'    => 'https://www.gettyimages.com/eula'
                    ],
                    'istock'         => [
                        'title'   => __( 'Terms agreement', 'stockpack' ),
                        'message' => __( 'Before you download the first image you need to agree to the terms of service of iStock. This will only be asked once and then we will store it for all subsequent downloads. You are a direct user of the iStock website and their terms apply. ', 'stockpack' ),
                        'link'    => 'https://www.istockphoto.com/legal/license-agreement'
                    ],
                    'pixabay'        => [
                        'title'   => __( 'Terms agreement', 'stockpack' ),
                        'message' => __( 'Before you download the first image you need to agree to the terms of service of Pixabay. This will only be asked once and then we will store it for all subsequent downloads. You are a direct user of the Pixabay website and their terms apply. ', 'stockpack' ),
                        'link'    => 'https://pixabay.com/service/terms/'
                    ],
                    'pexels'         => [
                        'title'   => __( 'Terms agreement', 'stockpack' ),
                        'message' => __( 'Before you download the first image you need to agree to the terms of service of Pexels. This will only be asked once and then we will store it for all subsequent downloads. You are a direct user of the Pexels website and their terms apply. ', 'stockpack' ),
                        'link'    => 'https://pexels.com/service/terms/'
                    ],
                    'unsplash'       => [
                        'title'   => __( 'Terms agreement', 'stockpack' ),
                        'message' => __( 'Before you download the first image you need to agree to the terms of service of Unsplash. This will only be asked once and then we will store it for all subsequent downloads. You are a direct user of the Unsplash website and their terms apply. ', 'stockpack' ),
                        'link'    => 'https://unsplash.com/terms'
                    ],
                    'deposit_photos' => [
                        'title'   => __( 'Terms agreement', 'stockpack' ),
                        'message' => __( 'Before you download the first image you need to agree to the terms of service of Deposit Photos. This will only be asked once and then we will store it for all subsequent downloads. You are a direct user of the Deposit Photos website and their terms apply. ', 'stockpack' ),
                        'link'    => 'https://depositphotos.com/terms-of-use.html'
                    ],
                ],
                'licensePopup'    => [
                    'proceed'         => __( 'Proceed', 'stockpack' ),
                    'checkInProgress' => __( 'Cost checking is in progress, please wait', 'stockpack' ),
                    'cancel'          => __( 'Cancel', 'stockpack' ),
                    'adobe_stock'     => [
                        'title'            => __( 'License and download', 'stockpack' ),
                        'message'          => __( 'Licensing an image will provide you a non watermarked image from Adobe Stock.', 'stockpack' ),
                        'status'           => __( 'Fetching cost...', 'stockpack' ),
                        'external'         => __( 'Adobe stock offers multiple types of accounts and multiple types of licenses. Currently only Standard is supported trough the plugin. You can use the url bellow to license this directly on the Adobe Stock website, in case your account allows that. In the case of credits missing, you can add more credits and retry.', 'stockpack' ),
                        'directLicenseUrl' => __( 'Adobe License Page', 'stockpack' ),
                    ],
                    'getty'           => [
                        'title'            => __( 'License and download', 'stockpack' ),
                        'message'          => __( 'Licensing an image will provide you a non watermarked image from Getty Images.', 'stockpack' ),
                        'status'           => __( 'Fetching cost...', 'stockpack' ),
                        'external'         => __( 'Getty images offers multiple types of accounts and multiple types of licenses. Currently we couldn\'t find an appropriate package linked to your account for this image. You can license the image from the Getty website. If you make sure that you account has the credits and the package required you can retry to license.', 'stockpack' ),
                        'directLicenseUrl' => __( 'Getty License Page', 'stockpack' ),
                    ],
                    'istock'          => [
                        'title'            => __( 'License and download', 'stockpack' ),
                        'message'          => __( 'Licensing an image will provide you a non watermarked image from iStock', 'stockpack' ),
                        'status'           => __( 'Fetching cost...', 'stockpack' ),
                        'external'         => __( 'iStock images offers multiple types of accounts and multiple types of licenses. Currently we couldn\'t find an appropriate package linked to your account for this image. You can license the image from the iStock website. If you make sure that you account has the credits and the package required you can retry to license.', 'stockpack' ),
                        'directLicenseUrl' => __( 'iStock License Page', 'stockpack' ),
                    ],
                    'deposit_photos'  => [
                        'title'            => __( 'License and download', 'stockpack' ),
                        'message'          => __( 'Licensing an image will provide you a non watermarked image from Deposit Photos.', 'stockpack' ),
                        'status'           => __( 'Fetching cost...', 'stockpack' ),
                        'external'         => __( 'Currently only sizes available trough the subscription package are supported by the plugin.  You can use the url bellow to license this directly on the Deposit Photos website, in case your account allows that. If you subscription expired renew it and retry.', 'stockpack' ),
                        'directLicenseUrl' => __( 'Deposit Photos License Page', 'stockpack' ),
                    ],
                ],
                'attribution'     => [
                    'adobe_stock'    => [
                        'author_info' => __( 'Image info is available in the sidebar', 'stockpack' ),
                        'message'     => __( 'You are searching images from', 'stockpack' ),
                        'link'        => 'https://stockpack.co/recommended/adobe_stock',
                        'link_title'  => 'Adobe Stock'
                    ],
                    'default'        => [
                        'author_info' => __( 'Stock provider is being fetched...', 'stockpack' ),
                        'message'     => __( 'Loading data...', 'stockpack' ),
                        'link'        => '',
                        'link_title'  => ''
                    ],
                    'getty'          => [
                        'author_info' => __( 'Image info is available in the sidebar', 'stockpack' ),
                        'message'     => __( 'You are searching images from', 'stockpack' ),
                        'link'        => 'https://stockpack.co/recommended/getty',
                        'link_title'  => 'Getty Images',
                        'warning'     => __( 'Watermarked images from Getty Images are allowed for test only (not publicly available), for up to 30 days following download', 'stockpack' ),
                    ],
                    'istock'         => [
                        'author_info' => __( 'Image info is available in the sidebar', 'stockpack' ),
                        'message'     => __( 'You are searching images from', 'stockpack' ),
                        'link'        => 'https://stockpack.co/recommended/istock',
                        'link_title'  => 'iStock',
                        'warning'     => __( 'Watermarked images from iStock are allowed for test only (not publicly available), for up to 30 days following download', 'stockpack' ),
                    ],
                    'pixabay'        => [
                        'author_info' => __( 'Author info is available in the sidebar', 'stockpack' ),
                        'message'     => __( 'You are searching images from', 'stockpack' ),
                        'link'        => 'https://stockpack.co/recommended/pixabay',
                        'link_title'  => 'Pixabay'
                    ],
                    'pexels'         => [
                        'author_info' => __( 'Author info is available in the sidebar', 'stockpack' ),
                        'message'     => __( 'You are searching images from', 'stockpack' ),
                        'link'        => 'https://stockpack.co/recommended/pexels',
                        'link_title'  => 'Pexels'
                    ],
                    'unsplash'       => [
                        'author_info' => __( 'Author info is available in the sidebar', 'stockpack' ),
                        'message'     => __( 'You are searching images from', 'stockpack' ),
                        'link'        => 'https://stockpack.co/recommended/unsplash',
                        'link_title'  => 'Unsplash'
                    ],
                    'deposit_photos' => [
                        'author_info' => __( 'Author info is available in the sidebar', 'stockpack' ),
                        'message'     => __( 'You are searching images from', 'stockpack' ),
                        'link'        => 'https://stockpack.co/recommended/deposit_photos',
                        'link_title'  => 'Deposit Photos',
                        'warning'     => __( 'Watermarked images from Deposit Photos are allowed for testing only', 'stockpack' ),
                    ],
                ],
                'limit'           => [
                    'premium'   => [
                        'title'   => __( 'Requests limit reached', 'stockpack' ),
                        'message' => __( 'You have reached the requests limit. This gets reset, but if you reach this often and you need a bigger solution you can contact StockPack support to discuss available options.', 'stockpack' )
                    ],
                    'anonymous' => [
                        'title'   => __( 'You need an account to continue', 'stockpack' ),
                        'message' => __( 'You have used all the requests available without an account.', 'stockpack' ),
                        'iframe'  => [
                            'src' => $dashboard_url . '/register?skim=1',
                            'id'  => 'stockpack-token-iframe',
                        ],
                        'status'  => [
                            'initial'   => __( 'Fetching remote website ...', 'stockpack' ),
                            'login'     => __( 'Sign in using the form below to continue', 'stockpack' ),
                            'register'  => __( 'Create an account in the window below to continue', 'stockpack' ),
                            'providers' => __( 'Token is being stored, you will be able to continue shortly', 'stockpack' ),
                            'elsewhere' => __( 'To get back to login you can close this window and open it again, or use the back buttons inside the window', 'stockpack' )
                        ]
                    ],
                    'free'      => [
                        'title'   => __( 'You need the premium upgrade to continue', 'stockpack' ),
                        'message' => __( 'You have reached the requests limit for the free account.', 'stockpack' ),
                        'iframe'  => [
                            'src' => $dashboard_url . '/login?skim=1&upgrade=1',
                            'id'  => 'stockpack-upgrade-iframe',
                        ],
                        'status'  => [
                            'initial'   => __( 'Fetching remote website ...', 'stockpack' ),
                            'providers' => __( 'Refreshing token, you will be able to continue shortly', 'stockpack' ),
                            'login'     => __( 'Sign in using the form below to continue', 'stockpack' ),
                            'register'  => __( 'Create an account in the window below to continue', 'stockpack' ),
                            'billing'   => __( 'Once you upgrade your account you will get upgraded limits which will allow you to proceed right away', 'stockpack' ),
                            'elsewhere' => __( 'To get back to login you can close this window and open it again, or use the back buttons inside the window', 'stockpack' )
                        ]
                    ],
                    'reset'     => __( 'Reset time passed, you can try again', 'stockpack' ),
                    'contact'   => __( 'Contact', 'stockpack' ),
                    'cancel'    => __( 'Cancel', 'stockpack' )
                ],
                'filename'        => [
                    'placeholder' => __( 'Overwrite filename (No extension)' )
                ]
            );

            return $strings;
        }

        public function settings( $strings, $post ) {
            $strings['stockpack'] = array(
                'terms'                    => [
                    'adobe_stock'    => get_option( 'terms_accepted_adobe_stock', false ),
                    'getty'          => get_option( 'terms_accepted_getty', false ),
                    'istock'         => get_option( 'terms_accepted_istock', false ),
                    'pixabay'        => get_option( 'terms_accepted_pixabay', false ),
                    'pexels'         => get_option( 'terms_accepted_pexels', false ),
                    'unsplash'       => get_option( 'terms_accepted_unsplash', false ),
                    'deposit_photos' => get_option( 'terms_accepted_deposit_photos', false ),
                ],
                'nonce_terms'              => wp_create_nonce( 'stockpack_terms' ),
                'nonce_license_cost'       => wp_create_nonce( 'stockpack_license_cost' ),
                'nonce_query'              => wp_create_nonce( 'stockpack_query' ),
                'nonce_download'           => wp_create_nonce( 'stockpack_download' ),
                'nonce_cache'              => wp_create_nonce( 'stockpack_cache' ),
                'nonce_token'              => wp_create_nonce( 'stockpack_token' ),
                'nonce_validate'           => wp_create_nonce( 'stockpack_validate' ),
                'settings_url'             => admin_url( 'options-general.php?page=stockpack' ),
                'contact_url'              => 'https://stockpack.co/contact',
                'filename_change'          => $this->settings->get_file_name_change_setting(),
                'premium_provider_caption' => $this->settings->get_premium_providers_caption_setting()
            );

            return $strings;
        }


    }

    $GLOBALS['stockpack_media'] = StockpackMedia::get_instance();
}
