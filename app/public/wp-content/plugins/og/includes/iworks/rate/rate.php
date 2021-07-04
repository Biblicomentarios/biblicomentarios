<?php
/**
 * iWorks_Rate - Dashboard Notification module.
 *
 * @version 2.0.3
 * @author  iworks (Marcin Pietrzak)
 *
 */
if ( ! class_exists( 'iworks_rate' ) ) {
	class iworks_rate {

		/**
		 * This class version.
		 *
		 * @since 1.0.1
		 * @var   string
		 */
		private $version = '2.0.3';

		/**
		 * $wpdb->options field name.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		protected $option_name = 'iworks_rates';

		/**
		 * List of all registered plugins.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		protected $plugins = array();

		/**
		 * Module options that are stored in database.
		 * Timestamps are stored here.
		 *
		 * Note that this option is stored in site-meta for multisite installs.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		protected $stored = array();

		/**
		 * Initializes and returns the singleton instance.
		 *
		 * @since  1.0.0
		 */
		static public function instance() {
			static $Inst = null;
			if ( null === $Inst ) {
				$Inst = new iworks_rate();
			}
			return $Inst;
		}

		/**
		 * Set up the iworks_rate module. Private singleton constructor.
		 *
		 * @since  1.0.0
		 */
		private function __construct() {
			/**
			 * settings
			 */
			$this->stored = wp_parse_args(
				get_site_option( $this->option_name, false, false ),
				array()
			);
			/**
			 * actions
			 */
			add_action( 'load-index.php', array( $this, 'load' ) );
			add_action( 'iworks-register-plugin', array( $this, 'register' ), 5, 3 );
			add_action( 'wp_ajax_iworks_rate_button', array( $this, 'ajax_button' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			/**
			 * own hooks
			 */
			add_filter( 'iworks_rate_assistance', array( $this, 'filter_get_assistance_widget' ), 10, 2 );
			add_filter( 'iworks_rate_love', array( $this, 'filter_get_love_widget' ), 10, 2 );
		}

		/**
		 * Inicialize admin area
		 *
		 * @since 2.0.2
		 */
		public function admin_init() {
			foreach ( $this->plugins as $plugin_file => $plugin ) {
				add_filter( 'plugin_action_links_' . $plugin_file, array( $this, 'add_donate_link' ), 10, 4 );
			}
		}

		/**
		 * Add donate link to plugin_row_meta.
		 *
		 * @since 2.0.2
		 *
		 * @param array  $actions An array of the plugin's metadata, including the version, author, author URI, and plugin URI.
		 */
		public function add_donate_link( $actions, $plugin_file, $plugin_data, $context ) {
			$settings_page_url = esc_url( apply_filters( 'iworks_rate_settings_page_url_' . $plugin_data['slug'], null ) );
			if ( ! empty( $settings_page_url ) ) {
				$actions['settings'] = sprintf(
					'<a href="%s">%s</a>',
					$settings_page_url,
					__( 'Settings', 'og' )
				);
			}
			$actions['donate'] = sprintf(
				'<a href="https://ko-fi.com/iworks?utm_source=%s&utm_medium=plugin-links" target="_blank">%s</a>',
				$plugin_data['slug'],
				__( 'Provide us a coffee', 'og' )
			);
			return $actions;
		}

		public function load() {
			$plugin_id = $this->choose_plugin();
			if ( empty( $plugin_id ) ) {
				return;
			}
			$this->plugin_id = $plugin_id;
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'admin_notices', array( $this, 'show' ) );
		}

		/**
		 * Save persistent module-data to the WP database.
		 *
		 * @since  1.0.0
		 */
		protected function store_data() {
			update_site_option( $this->option_name, $this->stored );
		}

		/**
		 * Action handler for 'iworks-register-plugin'
		 * Register an active plugin.
		 *
		 * @since  1.0.0
		 * @param  string $plugin_id WordPress plugin-ID (see: plugin_basename).
		 * @param  string $title Plugin name for display.
		 * @param  string $slug the plugin slug on wp.org
		 */
		public function register( $plugin_id, $title, $slug ) {
			// Ignore incorrectly registered plugins to avoid errors later.
			if ( empty( $plugin_id ) || empty( $title ) || empty( $slug ) ) {
				return;
			}
			$data                        = array(
				'title' => $title,
				'slug'  => $slug,
			);
			$this->plugins[ $plugin_id ] = $data;
			/*
			 * When the plugin is registered the first time we store some infos
			 * in the persistent module-data that help us later to find out
			 * if/which message should be displayed.
			 */
			if ( empty( $this->stored[ $plugin_id ] ) ) {
				$this->stored[ $plugin_id ] = wp_parse_args(
					array(
						'registered' => time(),
						'show_at'    => time() + rand( 7, 14 ) * DAY_IN_SECONDS,
						'rated'      => 0,
						'hide'       => 0,
					),
					$data
				);
				// Finally save the details.
				$this->store_data();
			}
		}

		/**
		 * Ajax handler called when the user chooses the CTA button.
		 *
		 * @since  1.0.0
		 */
		public function ajax_button() {
			$plugin_id = filter_input( INPUT_POST, 'plugin_id', FILTER_SANITIZE_STRING );
			if ( empty( $plugin_id ) ) {
				wp_send_json_error();
			}
			if ( ! isset( $this->plugins[ $plugin_id ] ) ) {
				wp_send_json_error();
			}
			switch ( filter_input( INPUT_POST, 'button', FILTER_SANITIZE_STRING ) ) {
				case '':
				case 'add-review':
					$this->add_weeks( $plugin_id );
					wp_send_json_success();
				case 'hide':
					$this->add_weeks( $plugin_id );
					$this->hide( $plugin_id );
					wp_send_json_success();
				case 'donate':
					$this->add_months( $plugin_id );
					wp_send_json_success();
			}
			wp_send_json_success();
		}

		public function hide( $plugin_id ) {
			if ( ! isset( $this->stored[ $plugin_id ] ) ) {
				return;
			}
			$this->stored[ $plugin_id ]['rated'] = time();
			$this->store_data();
		}

		private function add_weeks( $plugin_id ) {
			if ( ! isset( $this->stored[ $plugin_id ] ) ) {
				return;
			}
			$this->stored[ $plugin_id ]['show_at'] = time() + rand( 2, 3 ) * WEEK_IN_SECONDS + rand( 0, 3 ) * DAY_IN_SECONDS;
			$this->store_data();
		}

		private function add_months( $plugin_id ) {
			if ( ! isset( $this->stored[ $plugin_id ] ) ) {
				return;
			}
			$this->stored[ $plugin_id ]['show_at'] = time() + rand( 10, 15 ) * WEEK_IN_SECONDS + rand( 0, 7 ) * DAY_IN_SECONDS;
			$this->store_data();
		}

		/**
		 * Ajax handler called when the user chooses the dismiss button.
		 *
		 * @since  1.0.0
		 */
		public function dismiss() {
			$plugin = $this->get_plugin_from_post();
			if ( is_wp_error( $plugin ) ) {
				wp_send_json_error();
			}
			wp_send_json_success();
		}

		/**
		 * Action handler for 'load-index.php'
		 * Set-up the Dashboard notification.
		 *
		 * @since  1.0.0
		 */
		public function enqueue() {
			wp_enqueue_style(
				__CLASS__,
				plugin_dir_url( __FILE__ ) . 'admin.css',
				array(),
				$this->version
			);
			wp_enqueue_script(
				__CLASS__,
				plugin_dir_url( __FILE__ ) . 'admin.js',
				array(),
				$this->version,
				true
			);
		}

		/**
		 * Action handler for 'admin_notices'
		 * Display the Dashboard notification.
		 *
		 * @since  1.0.0
		 */
		public function show() {
			$this->render_message( $this->plugin_id );
		}

		/**
		 * Check to see if there is a pending message to display and returns
		 * the message details if there is.
		 *
		 * Note that this function is only called on the main Dashboard screen
		 * and only when logged in as super-admin.
		 *
		 * @since  1.0.0
		 * @return object|false
		 *         string $plugin WordPress plugin ID?
		 */
		protected function choose_plugin() {
			if ( wp_is_mobile() ) {
				return false;
			}
			/**
			 * list
			 */
			$choosen = array();
			/**
			 * change time by filter
			 */
			$now = apply_filters( 'iworks_rate_set_custom_time', time() );
			foreach ( $this->stored as $plugin_id => $item ) {
				if ( ! isset( $this->plugins[ $plugin_id ] ) ) {
					if ( isset( $this->stored[ $plugin_id ] ) ) {
						unset( $this->stored[ $plugin_id ] );
						$this->store_data();
					}
					continue;
				}
				if ( intval( $item['show_at'] ) > $now ) {
					continue;
				}
				$choosen[] = $plugin_id;
			}
			if ( empty( $choosen ) ) {
				return false;
			}
			return $choosen[ array_rand( $choosen ) ];
		}

		/**
		 * Renders the actual Notification message.
		 *
		 * @since  1.0.0
		 */
		protected function render_message( $plugin_id ) {
			$file   = $this->get_file( 'thanks' );
			$plugin = $this->get_plugin_data_by_plugin_id( $plugin_id );
			load_template( $file, true, $plugin );
		}

		/**
		 * @since 2.0.1
		 */
		private function get_file( $file, $group = '' ) {
			return sprintf(
				'%s/templates/%s%s%s.php',
				dirname( __FILE__ ),
				$group,
				'' === $group ? '' : '/',
				sanitize_title( $file )
			);
		}

		/**
		 * @since 2.0.1
		 */
		private function get_plugin_data_by_plugin_id( $plugin_id ) {
			$plugin              = wp_parse_args(
				$this->plugins[ $plugin_id ],
				$this->stored[ $plugin_id ]
			);
			$plugin['plugin_id'] = $plugin_id;
			$plugin['logo']      = apply_filters( 'iworks_rate_notice_logo_style', '', $plugin );
			$plugin['ajax_url']  = admin_url( 'admin-ajax.php' );
			$plugin['classes']   = array(
				'iworks-rate',
				'iworks-rate-' . $plugin['slug'],
				'iworks-rate-notice',
			);
			if ( ! empty( $plugin['logo'] ) ) {
				$plugin['classes'][] = 'has-logo';
			}
			$plugin['url']         = esc_url(
				sprintf(
					_x( 'https://wordpress.org/plugins/%s', 'plugins home', 'og' ),
					$plugin['slug']
				)
			);
			$plugin['support_url'] = esc_url(
				sprintf(
					_x( 'https://wordpress.org/support/plugin/%s', 'plugins support home', 'og' ),
					$plugin['slug']
				)
			);
			return $plugin;
		}

		/**
		 * @since 2.0.1
		 */
		private function get_plugin_id_by_slug( $slug ) {
			foreach ( $this->stored as $plugin_id => $plugin ) {
				if ( $slug === $plugin['slug'] ) {
					return $plugin_id;
				}
			}
			return new WP_Error();
		}

		/**
		 * @since 2.0.1
		 */
		public function filter_get_assistance_widget( $content, $slug ) {
			$plugin_id = $this->get_plugin_id_by_slug( $slug );
			if ( is_wp_error( $plugin_id ) ) {
				return $content;
			}
			$this->enqueue();
			$plugin = $this->get_plugin_data_by_plugin_id( $plugin_id );
			$file   = $this->get_file( 'support', 'widgets' );
			ob_start();
			load_template( $file, true, $plugin );
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}

		/**
		 * @since 2.0.1
		 */
		public function filter_get_love_widget( $content, $slug ) {
			$plugin_id = $this->get_plugin_id_by_slug( $slug );
			if ( is_wp_error( $plugin_id ) ) {
				return $content;
			}
			$this->enqueue();
			$plugin = $this->get_plugin_data_by_plugin_id( $plugin_id );
			$file   = $this->get_file( 'donate', 'widgets' );
			ob_start();
			load_template( $file, true, $plugin );
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}

	// Initialize the module.
	iworks_rate::instance();
}
