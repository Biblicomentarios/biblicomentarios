<?php
/**
 * Plugin Name: Swift Performance Lite
 * Plugin URI: https://swiftperformance.io
 * Description: Boost your WordPress site
 * Version: 2.3.5
 * Author: SWTE
 * Author URI: https://swteplugins.com
 * Text Domain: swift-performance
 */

if (!class_exists('Swift_Performance')){
	class Swift_Performance {

		/**
		 * Swift Performance instance
		 */
		public static $instance;

		/**
		 * Cache current thread
		 */

		public $thread_cache;

		/**
		 * Loaded modules
		 */
		public $modules = array();

		/**
		 * Global Memcached API object
		 */
		public $memcached;

		/**
		 * HTTP HOST
		 */
		public static $http_host;

		/**
		 * Log buffer
		 */
		public $log_buffer = array();

		/**
		 * Framework options
		 */
		public static $luvoptions;

		/**
		 * Create instance
		 */
		public function __construct() {
			if (defined('SWIFT_PERFORMANCE_DISABLE_PLUGIN') && SWIFT_PERFORMANCE_DISABLE_PLUGIN){
				return;
			}

			// Create instance
			if (empty(Swift_Performance::$instance)){
				Swift_Performance::$instance = $this;
			}

			$GLOBALS['swift_performance'] = Swift_Performance::$instance;

			do_action('swift_performance_before_init');

			Swift_Performance::$http_host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : parse_url(home_url(), PHP_URL_HOST));

			if (Swift_Performance::check_option('activate-whitelabel', 1) && !defined('SWIFT_PERFORMANCE_WHITELABEL')){
				define('SWIFT_PERFORMANCE_WHITELABEL', true);
			}

			if (defined('SWIFT_PERFORMANCE_WHITELABEL') && SWIFT_PERFORMANCE_WHITELABEL){
				include_once 'includes/classes/class.whitelabel.php';
			}

			// Plugin Loader
			@$this->modules['plugin-organizer'] =  include_once 'modules/plugin-organizer/plugin-organizer.php';

			// Ignore user abort for remote prebuild
			if (isset($_SERVER['HTTP_X_PREBUILD']) && $_SERVER['HTTP_X_PREBUILD'] == md5(NONCE_SALT)){
				ignore_user_abort(true);
				if (function_exists('fastcgi_finish_request')){
					fastcgi_finish_request();
				}
			}

			add_action('plugins_loaded', array('Swift_Performance', 'db_install'));

			// Set constants
			if (!defined('SWIFT_PERFORMANCE_URI')){
				define('SWIFT_PERFORMANCE_URI', trailingslashit(plugins_url() . '/'. basename(__DIR__)));
			}

			if (!defined('SWIFT_PERFORMANCE_DIR')){
				define('SWIFT_PERFORMANCE_DIR', trailingslashit(__DIR__));
			}

			if (!defined('SWIFT_PERFORMANCE_VER')){
				define('SWIFT_PERFORMANCE_VER', '2.3.5');
			}

			if (!defined('SWIFT_PERFORMANCE_DB_VER')){
				define('SWIFT_PERFORMANCE_DB_VER', '1.7');
			}

			if (!defined('SWIFT_PERFORMANCE_API_URL')){
				define('SWIFT_PERFORMANCE_API_URL', 'https://apiv3.swteplugins.com/');
			}

			if (!defined('SWIFT_PERFORMANCE_PLUGIN_BASENAME')){
				$plugin_basename = plugin_basename(__FILE__);
				// fallback for symlinks
				if (!file_exists(trailingslashit(WP_PLUGIN_DIR) . $plugin_basename)){
					foreach (get_option('active_plugins') as $plugin_file){
						if (file_exists(trailingslashit(WP_PLUGIN_DIR) . $plugin_file) && md5_file(trailingslashit(WP_PLUGIN_DIR) . $plugin_file) == md5_file(__FILE__)){
							$plugin_basename = $plugin_file;
						}
					}
				}
				define('SWIFT_PERFORMANCE_PLUGIN_BASENAME', $plugin_basename);
			}

			if (!defined('SWIFT_PERFORMANCE_LOGO_URI')){
				if (in_array(Swift_Performance::license_type(), array('lite', 'offline'))){
					define('SWIFT_PERFORMANCE_LOGO_URI', SWIFT_PERFORMANCE_URI . 'images/logo-lite.png');
				}
				else {
					define('SWIFT_PERFORMANCE_LOGO_URI', SWIFT_PERFORMANCE_URI . 'images/logo.png');
				}
			}

			if (!defined('SWIFT_PERFORMANCE_PLUGIN_NAME')){
				define('SWIFT_PERFORMANCE_PLUGIN_NAME', __( 'Swift Performance', 'swift-performance' ));
			}

			if (!defined('SWIFT_PERFORMANCE_SLUG')){
				define('SWIFT_PERFORMANCE_SLUG', 'swift-performance');
			}

			if (!defined('SWIFT_PERFORMANCE_CACHE_BASE_DIR')){
				define('SWIFT_PERFORMANCE_CACHE_BASE_DIR', trailingslashit(SWIFT_PERFORMANCE_SLUG));
			}

			if (!defined('SWIFT_PERFORMANCE_TABLE_PREFIX')){
				global $wpdb;
				define('SWIFT_PERFORMANCE_TABLE_PREFIX', $wpdb->prefix . 'swift_performance_');
			}

			if (!defined('SWIFT_PERFORMANCE_PREBUILD_TIMEOUT')){
				$timeout = get_option('swift_performance_timeout');
				define('SWIFT_PERFORMANCE_PREBUILD_TIMEOUT', (empty($timeout) ? 300 : $timeout));
			}

			if (!defined('SWIFT_PERFORMANCE_WARMUP_LIMIT')){
				define('SWIFT_PERFORMANCE_WARMUP_LIMIT', 10000);
			}

			// Pro features
			if (file_exists(SWIFT_PERFORMANCE_DIR . 'modules/pro/pro-features.php')){
				$this->modules['pro'] =  require_once 'modules/pro/pro-features.php';
			}

			// Clean htaccess and scheduled events on deactivate
			register_deactivation_hook( basename(dirname(__FILE__)) . '/performance.php', array('Swift_Performance', 'deactivate'));

			// Regenerate htaccess on activation
			register_activation_hook( basename(dirname(__FILE__)) . '/performance.php', array('Swift_Performance', 'activate'));

			// Preview mode
			if (isset($_GET['swift-preview'])){
				global $swift_performance_options;
				$swift_performance_options = get_option('swift_performance_preview', array());
				$_COOKIE = $_REQUEST = $_POST = $_GET = array();
				define('SWIFT_PERFORMANCE_DISABLE_CACHE', true);

				Swift_Performance::set_option('optimize-prebuild-only',0);
				Swift_Performance::set_option('merge-background-only',0);

				add_filter('swift_performance_is_cacheable', '__return_true');
				add_filter('luv_framework_option_name', function(){
					return 'swift_performance_preview';
				});

				// append ?preview to all internal links
	                  include_once SWIFT_PERFORMANCE_DIR . 'modules/asset-manager/dom-parser.php';
				add_action('init', function(){
					ob_start(function($buffer){
						$html = swift_performance_str_get_html(Swift_Performance_Asset_Manager::html_auto_fix($buffer));
						if ($html !== false){
							foreach ($html->find('a') as $node){
								if (strpos($node->href, Swift_Performance::$http_host) !== false){
									$node->href = add_query_arg('swift-preview',1,$node->href);
								}
							}
							return $html;
						}
						return $buffer;
					});
				});
			}

			// Load textdomain
			load_plugin_textdomain( 'swift-performance', FALSE, basename( dirname( __FILE__ ) ) . '/language/' );


			// Include framework
			if (!defined('LUV_FRAMEWORK_PATH')){
			      define('LUV_FRAMEWORK_PATH', SWIFT_PERFORMANCE_DIR . 'includes/luv-framework/' );
			}

			if (!defined('LUV_FRAMEWORK_URL')){
			      define('LUV_FRAMEWORK_URL', SWIFT_PERFORMANCE_URI . 'includes/luv-framework/' );
			}
			include_once 'includes/luv-framework/framework.php';
			include_once 'includes/luv-framework/framework-config.php';

			add_filter('luv_framework_render_options', array(__CLASS__, 'panel_template'));
			add_filter('luv_framework_enqueue_assets', function($result, $hook){
				return ($hook == 'tools_page_'.SWIFT_PERFORMANCE_SLUG);
			}, 10, 2);

			// Keep purchase key on import
			add_filter('luv_framework_import_options', function($options){
				$options['purchase-key'] = Swift_Performance::get_option('purchase-key');
				return $options;
			});

			// Clear cache after import
			add_action('luv_framework_import', array('Swift_Performance_Cache', 'clear_all_cache'));

			// Include WP_CLI
			include_once 'includes/classes/class.cli.php';

			// Include AJAX
			include_once 'includes/classes/class.ajax.php';

			// Include 3rd party handler
			include_once 'includes/classes/class.third-party.php';

			// Prebuild Booster
			include_once 'includes/classes/class.prebuild-booster.php';

			// Include Metaboxes
			if (Swift_Performance::check_option('page-specific-rules',1)){
				include_once 'includes/classes/class.meta-boxes.php';
			}

			// Override settings per page
			if (Swift_Performance::check_option('page-specific-rules',1)){
				add_action('template_redirect', function(){
					$post_id = get_the_ID();
					if (!empty($post_id)){
						$settings = apply_filters('swift_performance_per_page_settings', get_post_meta($post_id, 'swift_performance_options', true));
						if (isset($settings['override-globals']) && $settings['override-globals'] == 1){
							foreach((array)$settings as $key => $value){
								Swift_Performance::set_option($key, $value);
							}
						}
					}
				});
			}

			// Include setup wizard
			if ((is_admin() || defined('DOING_CRON')) && apply_filters('swift-performance-setup-wizard-enabled', true)){
				require_once SWIFT_PERFORMANCE_DIR . 'includes/setup/setup.php';
				new Swift_Performance_Setup();

				add_action('admin_init', function(){
					if (!defined('DOING_AJAX') && get_option('swift-perforomance-initial-setup-wizard') === false && get_transient('swift-performance-setup') === 'uid:'.get_current_user_id()){
						delete_transient('swift-performance-setup');
						wp_redirect(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'setup'), admin_url('tools.php')));
						die;
					}
				});
			}

			// Init Swift Performance
			$this->init();

			// Load assets on backend
			add_action('admin_enqueue_scripts', array($this, 'load_assets'),11);

			// Create prebuild cache hook
			add_action('swift_performance_prebuild_cache', array('Swift_Performance', 'prebuild_cache'));
			add_action('swift_performance_prebuild_page_cache', array('Swift_Performance', 'prebuild_page_cache'));

			// API messages hook
			add_action('swift_performance_api_messages', array('Swift_Performance', 'api_messages'));

			// Stat hook
			add_action('swift_performance_collect_anonymized_data', array('Swift_Performance', 'collect_anonymized_data'));

			// Early loader hook
			add_action('swift_performance_early_loader', array('Swift_Performance', 'early_loader'));

			// Clear cache, manage rewrite rules, scheduled jobs after options was saved
			add_action('luv_framework_swift_performance_options_saved', array('Swift_Performance', 'options_saved'));

			// Generate plugin header
			add_action('swift_performance_options_saved', array('Swift_Performance', 'update_plugin_header'));
			add_action('upgrader_process_complete', array('Swift_Performance', 'upgrader_process_complete'), 10, 2);

			// Create cache expiry cron schedule
			add_filter( 'cron_schedules',	function ($schedules){
				// Common cache
				$schedules['swift_performance_cache_expiry'] = array(
					'interval' => max(Swift_Performance::get_option('cache-garbage-collection-time'), 1),
					'display' => sprintf(__('%s Cache Expiry'), SWIFT_PERFORMANCE_PLUGIN_NAME)
				);

				// Assets cache
				$schedules['swift_performance_assets_cache_expiry'] = array(
					'interval' => 3600,
					'display' => sprintf(__('%s Assets Cache Expiry'), SWIFT_PERFORMANCE_PLUGIN_NAME)
				);

				return $schedules;
			});

			// Admin menus
			if (Swift_Performance::check_option('disable-toolbar',1,'!=')){
				add_action('admin_bar_menu', array('Swift_Performance', 'toolbar_items'),100);
			}

			// Clear cache
			add_action('init', function(){
				if (!isset($_GET['swift-performance-action'])){
					return;
				}

				$user = wp_get_current_user();

				// All cache
				if ($_GET['swift-performance-action'] == 'clear-all-cache' && (array_intersect((array)Swift_Performance::get_option('clear-cache-roles'), $user->roles ) || current_user_can('manage_options')) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'clear-swift-cache')){
					Swift_Performance_Cache::clear_all_cache();
					self::add_notice(esc_html__('All cache cleared', 'swift-performance'), 'success');
				}

				// Page cache
				if ($_GET['swift-performance-action'] == 'clear-page-cache' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'clear-swift-cache')){
					if (array_intersect((array)Swift_Performance::get_option('clear-cache-roles'), $user->roles ) || current_user_can('manage_options')){
						Swift_Performance_Cache::clear_permalink_cache($_GET['permalink']);
						wp_redirect($_GET['permalink']);
						die;
					}
					// Maybe editor
					else {
						add_action('template_redirect', function(){
							if (current_user_can('edit_post', get_the_ID())){
								Swift_Performance_Cache::clear_permalink_cache($_GET['permalink']);
								wp_redirect($_GET['permalink']);
								die;
							}
						});
					}
				}

				if ($_GET['swift-performance-action'] == 'clear-assets-cache' && (array_intersect((array)Swift_Performance::get_option('clear-cache-roles'), $user->roles ) || current_user_can('manage_options')) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'clear-swift-assets-cache')){
					Swift_Performance_Asset_Manager::clear_assets_cache();
					self::add_notice(esc_html__('Assets cache cleared', 'swift-performance'), 'success');
				}

				if ($_GET['swift-performance-action'] == 'purge-cdn' && (array_intersect((array)Swift_Performance::get_option('clear-cache-roles'), $user->roles ) || current_user_can('manage_options')) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'purge-swift-cdn')){
					if (self::check_option('enable-caching', 1)){
						Swift_Performance_Cache::clear_all_cache();
					}
					else if (self::check_option('merge-scripts',1) || self::check_option('merge-styles',1)){
						Swift_Performance_Asset_Manager::clear_assets_cache();
					}
					else {
						Swift_Performance_CDN_Manager::purge_cdn();
					}
				}
			});

			// Show runtime Messages
			add_action('admin_notices', array($this, 'admin_notices'));

			// Heartbeat
			add_action('init', function(){
				// Disable on specific pages
				$disabled_pages = array();
				foreach ((array)Swift_Performance::get_option('disable-heartbeat') as $key => $value) {
					if ($value == 1){
						$disabled_pages = array_merge($disabled_pages, explode(',',$key));
					}
				}
				if (!empty($disabled_pages)){
					global $pagenow;
					if (in_array($pagenow, $disabled_pages)){
						wp_deregister_script('heartbeat');
					}
				}
			},1);

			// Override frequency
			add_filter( 'heartbeat_settings', function($settings){
				$interval = Swift_Performance::get_option('heartbeat-frequency');

				if (!empty($interval)){
					$settings['interval'] = $interval;
				}
				return $settings;
			});

			// Create clear cache hook for scheduled events
			add_action('swift_performance_clear_short_lifespan', array('Swift_Performance_Cache', 'clear_short_lifespan'));

			if (Swift_Performance::check_option('cache-expiry-mode', 'timebased')){
				add_action('swift_performance_clear_cache', array('Swift_Performance_Cache', 'clear_all_cache'));
				add_action('swift_performance_clear_expired', array('Swift_Performance_Cache', 'clear_expired'));
			}

			// Create clear assets cache hook for scheduled events
			add_action('swift_performance_clear_assets_proxy_cache', array('Swift_Performance_Asset_Manager', 'clear_assets_proxy_cache'));

			// Add plugin actions
			add_filter('plugin_action_links', function ($links, $file) {
				if ($file == plugin_basename(__FILE__)) {
					$links['deactivate'] = '<a href="' . esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'deactivate', 'swift-nonce' => wp_create_nonce('swift-performance-setup')), admin_url('tools.php'))) . '">'.__('Deactivate','swift-performance').'</a>';
					$settings_link = '<a href="' . add_query_arg('subpage', 'settings', menu_page_url(SWIFT_PERFORMANCE_SLUG, false)) . '">'.__('Settings','swift-performance').'</a>';
					array_unshift($links, $settings_link);
				}

				return $links;
			}, 10, 2);

			// Log 404 queries
			add_action('template_redirect', function(){
				if (is_404()){
					Swift_Performance::log('404 Error: ' . $_SERVER['REQUEST_URI'], 6);
				}
			});

			// Disable Appcache for the client as it was depricated and removed in 2.1.7
			add_action('init', function(){
				if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/' . SWIFT_PERFORMANCE_SLUG .'.appcache'){
					// Disable general cache
					@$GLOBALS['swift_performance']->modules['cache']->disabled_cache = true;

					header('Content-Type: text/cache-manifest');

					// Appcache was depricated and removed in 2.1.7
					header("HTTP/1.0 410 Gone");
					die;
				}
			});

			// Remote cron response
			add_action('init', function(){
				if (isset($_GET['doing_wp_cron']) && isset($_GET['sprc']) && Swift_Performance::check_option('remote-cron', '1')){
					status_header('202');
					Swift_Performance::flush_connection();
				}
			});

			// Detect 3rd Party cache
			add_action('plugins_loaded', array('Swift_Performance_Third_Party', 'detect_cache'));

			// Bypass nonce authentication for not logged in users
			if (Swift_Performance::is_feature_available('bypass_nonce')){
				Swift_Performance_Pro::bypass_nonce();
			}

			// Collect Anonymized Data
			if(!wp_next_scheduled('swift_performance_collect_anonymized_data')) {
				wp_schedule_event(time(), 'daily', 'swift_performance_collect_anonymized_data');
			}

			add_action('init', function(){
				if (Swift_Performance::license_type() == 'offline' && get_transient('swift_performance_activate_notice') == false){
					set_transient('swift_performance_activate_notice', true, 1209600);
					ob_start();
					include apply_filters('swift_performance_template_dir', SWIFT_PERFORMANCE_DIR . 'templates/') . 'activate-notice.php';
					Swift_Performance::add_notice(ob_get_clean(), 'warning', 'plugin/update-action');
				}
			});

			do_action('swift_performance_init');
		}

		/**
		 * Load assets
		 * @param string $hook
		 */
		public function load_assets($hook) {
			$messages 			= apply_filters('swift_performance_admin_notices', get_option('swift_performance_messages', array()));
			$has_permanent_message	= apply_filters('swift_performance_has_permanent_message', false);
			foreach ($messages as $message) {
				if (isset($message['permanent']) && $message['permanent'] === true){
					$has_permanent_message = true;
					break;
				}
			}

			if(apply_filters('swift_performance_enqueue_assets', ($has_permanent_message || $hook == 'tools_page_'.SWIFT_PERFORMANCE_SLUG || $hook == 'post-new.php' || $hook == 'post.php'))) {
				wp_enqueue_script('wp-pointer');
				wp_enqueue_style( 'wp-pointer' );
				wp_enqueue_script( SWIFT_PERFORMANCE_SLUG, SWIFT_PERFORMANCE_URI . 'js/scripts.js', array('jquery'), SWIFT_PERFORMANCE_VER );
				wp_localize_script( SWIFT_PERFORMANCE_SLUG, 'swift_performance', array('i18n' => $this->i18n(),'nonce' => wp_create_nonce('swift-performance-ajax-nonce'), 'cron' => apply_filters('swift_performance_cron_url', defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ? site_url('wp-cron.php?doing_wp_cron') : '') ));
				if(defined('SWIFT_PERFORMANCE_WHITELABEL') && SWIFT_PERFORMANCE_WHITELABEL){
					wp_enqueue_style( SWIFT_PERFORMANCE_SLUG, SWIFT_PERFORMANCE_URI . 'css/whitelabel.css', array(), SWIFT_PERFORMANCE_VER );
				}
				else {
					wp_enqueue_style( SWIFT_PERFORMANCE_SLUG, SWIFT_PERFORMANCE_URI . 'css/styles.css', array(), SWIFT_PERFORMANCE_VER );
				}
			}

			if ($has_permanent_message){
				wp_enqueue_style('luv-framework-fields', LUV_FRAMEWORK_URL . 'assets/css/fields.css');
				wp_enqueue_style('font-awesome-5', LUV_FRAMEWORK_URL . 'assets/icons/fa5/css/all.min.css');
			}
		}

		/**
		 * Localize JS messages
		 */
		public function i18n(){
			return array(
				'Do you want to clear all logs?' => esc_html__('Do you want to clear all logs?', 'swift-performance'),
				'Do you want to reset prebuild links?' => esc_html__('Do you want to reset prebuild links?', 'swift-performance'),
				'Not set' => esc_html__('Not set', 'swift-performance'),
				'Settings were changed. Would you like to clear all cache?' => esc_html__('Settings were changed. Would you like to clear all cache?'),
				'Dismiss' => esc_html__('Dismiss', 'swift-performance'),
				'Swift Performance Lazyload' => sprintf(esc_html__('%s Lazyload', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME)
			);
		}

		/**
		 * Init Swift Performance
		 */
		public function init(){
			if (!defined('SWIFT_PERFORMANCE_CACHE_DIR')){
				$cache_path = self::get_option('cache-path');
				define('SWIFT_PERFORMANCE_CACHE_DIR', trailingslashit(empty($cache_path) ? WP_CONTENT_DIR . '/cache/' : $cache_path) . SWIFT_PERFORMANCE_CACHE_BASE_DIR . Swift_Performance::$http_host . '/');
			}

			if (!defined('SWIFT_PERFORMANCE_CACHE_URL')){
				if (Swift_Performance::check_option('caching-strict-host',1)){
					define('SWIFT_PERFORMANCE_CACHE_URL', str_replace(ABSPATH, trailingslashit(site_url()), SWIFT_PERFORMANCE_CACHE_DIR));
				}
				else {
					define('SWIFT_PERFORMANCE_CACHE_URL', str_replace(ABSPATH, parse_url(home_url(), PHP_URL_SCHEME) . '://' . Swift_Performance::$http_host . Swift_Performance::home_dir() . '/', SWIFT_PERFORMANCE_CACHE_DIR));
				}
			}


			// Cache
			$this->modules['cache'] =  require_once 'modules/cache/cache.php';

			// CDN Manager
			if (self::check_option('enable-cdn', 1)){
				$this->modules['cdn-manager'] =  require_once 'modules/cdn/cdn-manager.php';
			}

			// Asset Manager
			$this->modules['asset-manager'] = require_once 'modules/asset-manager/asset-manager.php';

			// Image optimizer
			$this->modules['image-optimizer'] =  require_once 'modules/image-optimizer/image-optimizer.php';

			// DB optimizer
			$this->modules['db-optimizer'] =  require_once 'modules/db-optimizer/db-optimizer.php';

			// Google Analytics
			if (self::check_option('bypass-ga', 1)){
				$this->modules['ga'] =  require_once 'modules/google-analytics/google-analytics.php';
			}

		}

		/**
		 * Print admin notices
		 */
		public function admin_notices(){
			// Disable admin notices (except API messages)
			if (Swift_Performance::check_option('disable-admin-notices',1) && Swift_Performance::is_feature_available('disable_admin_notices')){
				add_filter('swift_performance_admin_notices', array('Swift_Performance_Pro', 'disable_admin_notices'));
			}

			global $pagenow;
			if ($pagenow == 'post-new.php' || $pagenow == 'post.php'){
				return;
			}

			// Show messages only for admin
			if (!current_user_can('manage_options')){
				return;
			}

			$messages = array_filter((array)apply_filters('swift_performance_admin_notices', get_option('swift_performance_messages', array())));
			foreach((array)$messages as $message_id => $message){
				// Skip empty messages
				if (!isset($message['message']) || empty($message['message'])){
					continue;
				}

				$class = ($message['type'] == 'success' ? 'updated' : ($message['type'] == 'warning' ? 'update-nag' : ($message['type'] == 'error' ? 'error' : 'notice')));
				if (defined('SWIFT_PERFORMANCE_WHITELABEL') && SWIFT_PERFORMANCE_WHITELABEL){
					echo '<div class="'.$class.'" data-message-id="' . esc_attr($message_id) . '" style="padding:25px 10px 10px 10px;position: relative;display: block;"><span style="color:#888;position:absolute;top:5px;left:5px;">'.SWIFT_PERFORMANCE_PLUGIN_NAME.'</span>'.$message['message'].'</div>';
				}
				else {
					echo '<div class="swift-performance-notice '.$class.'" data-message-id="' . esc_attr($message_id) . '" style="padding:25px 10px 10px 10px;position: relative;display: block;">';
					if ((defined('SWIFT_PERFORMANCE_WHITELABEL') && SWIFT_PERFORMANCE_WHITELABEL) || (!isset($message['permanent']) || $message['permanent'] == false)){
						echo '<div style="color: #777;margin: -20px 0 10px 0">' . SWIFT_PERFORMANCE_PLUGIN_NAME . '</div>';
					}
					else {
						echo '<img src="' . SWIFT_PERFORMANCE_LOGO_URI . '">';
					}
					echo $message['message'].'</div>';
				}
				if (!isset($message['permanent']) || $message['permanent'] == false){
					unset($messages[$message_id]);
				}
			}
			if (empty($messages)){
				delete_option('swift_performance_messages');
			}
			else {
				update_option('swift_performance_messages', $messages);
			}
		}

		/**
		 * Get default warmup priority
		 * @return int
		 */
		public static function get_default_warmup_priority(){
			global $wpdb;
			$table_name = SWIFT_PERFORMANCE_TABLE_PREFIX . 'warmup';
			return apply_filters('swift_performance_defult_warmup_priority', max(11,(int)$wpdb->get_var("SELECT priority FROM {$table_name} WHERE menu_item = 1 ORDER BY priority DESC LIMIT 1")));
		}

		/**
		 * Get URLs which should be precached
		 * @param boolean $is_flat
		 * @return array
		 */
		public static function get_prebuild_urls($is_flat = true, $uncached = false){
	 		global $wpdb;
			$where	= '';
	 		$table_name = SWIFT_PERFORMANCE_TABLE_PREFIX . 'warmup';

			if ($uncached){
				$where = " WHERE type = ''";
			}
	 		$maybe_urls = $wpdb->get_results("SELECT url, priority, timestamp, type FROM {$table_name}{$where} ORDER BY priority", ARRAY_A);
	 		if (!empty($maybe_urls) || $uncached){
	 			// If we need only the URLS
	 			if ($is_flat){
	 				$flat = array();
	 				foreach ($maybe_urls as $maybe_url){
	 					$flat[trailingslashit($maybe_url['url'])] = $maybe_url['url'];
	 				}
	 				return apply_filters('swift_performance_warmup_urls_flat', $flat);
	 			}
				$not_flat = array();
				foreach ($maybe_urls as $maybe_url){
					$not_flat[trailingslashit($maybe_url['url'])] = $maybe_url;
				}
	 			return apply_filters('swift_performance_warmup_urls', $not_flat);
	 		}


			// Build warmup table

			// Run only one thread
			if (get_transient('swift_performance_initial_prebuild_links') !== false){
				return array();
			}


			ignore_user_abort(true);
			// Finish request for FPM on AJAX
			if (defined('DOING_AJAX') && DOING_AJAX){
				if (function_exists('fastcgi_finish_request')){
					fastcgi_finish_request();
				}
			}

			// Extend timeout
			$timeout = Swift_Performance::set_time_limit(300, 'build_warmup_table');

			switch (Swift_Performance::get_option('warmup-table-source')){
				case 'url-list':
					$urls = array();
					foreach (explode("\n",Swift_Performance::get_option('warmup-table-url-list')) as $url){
						$url	= trim($url);

						// Skip empty lines
						if (empty($url)){
							continue;
						}

						$url	= trailingslashit(esc_url(!preg_match('~^http~',$url) ? Swift_Performance::home_url() . $url : $url));
						if (Swift_Performance_Cache::is_object_cacheable($url)){
							$urls[Swift_Performance::get_warmup_id($url)] = $url;
						}
					}
					break;
				case 'sitemap':
					$urls = array();
					if (Swift_Performance::check_option('warmup-sitemap', '', '!=')){
						$response = wp_remote_get(esc_url(Swift_Performance::get_option('warmup-sitemap')), array('timeout' => 60, 'sslverify' => false));
						if (!is_wp_error($response) && !empty($response['body'])){
							$sitemaps = simplexml_load_string($response['body']);
							if (isset($sitemaps->sitemap)){
								foreach ($sitemaps as $sitemap){
									if (isset($sitemap->loc) && !empty($sitemap->loc)){
										$sitemap_response = wp_remote_get($sitemap->loc, array('timeout' => 60, 'sslverify' => false));
										if (!is_wp_error($sitemap_response) && !empty($sitemap_response['body'])){
											foreach (simplexml_load_string($sitemap_response['body']) as $url){
												if (Swift_Performance_Cache::is_object_cacheable($url->loc)){
													$urls[Swift_Performance::get_warmup_id($url->loc)] = (string)$url->loc;
												}
											}
										}
									}
								}
							}
							elseif (isset($sitemaps->url)){
								foreach ($sitemaps->url as $url){
									if (Swift_Performance_Cache::is_object_cacheable($url->loc)){
										$urls[Swift_Performance::get_warmup_id($url->loc)] = (string)$url->loc;
									}
								}
							}
						}
					}
					break;
				case 'manual':
					$urls = array();
					foreach ((array)Swift_Performance::get_option('warmup-pages') as $page_id){
						$url	= get_permalink($page_id);
						$urls[Swift_Performance::get_warmup_id($url)] = $url;
					}
					break;
				case 'auto':
				default:
					$urls = Swift_Performance::generate_warmup_table();
					break;
			}

			$urls 	= array_unique($urls);
	 		$not_flat	= $values = array();

	 		$priority	= 10;
	 		$index = 0;

			$max_allowed_packet		= $wpdb->get_row("SHOW VARIABLES LIKE 'max_allowed_packet'", ARRAY_A);
			$max_allowed_packet_size	= (isset($max_allowed_packet['Value']) && !empty($max_allowed_packet['Value']) ? $max_allowed_packet['Value']*0.9 : 1024*970);
			$menu_item_ids			= $wpdb->get_col("SELECT meta_value FROM {$wpdb->postmeta} LEFT JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.meta_value WHERE meta_key = '_menu_item_object_id' AND post_type != 'nav_menu_item'");

			// Limit URLs
			$urls = array_slice($urls, 0, SWIFT_PERFORMANCE_WARMUP_LIMIT);

	 		foreach ((array)apply_filters('swift_performance_warmup_urls_to_save',$urls) as $key => $url){
				// Is it menu item
				$is_menu_item	= 0;
				$maybe_post_id	= url_to_postid($url);
				if (!empty($maybe_post_id) && in_array($maybe_post_id, $menu_item_ids)){
					$is_menu_item = 1;
				}

	 			// Build blocks
	 			$_values = '("'.esc_sql($key).'", "' . esc_url($url) . '", ' . (int)apply_filters('swift_performance_default_warmup_priority', $priority, $key, $url) .', "'.(int)$is_menu_item.'"),';

	 			if (!isset($values[$index])){
	 				$values[$index] = '';
	 			}

	 			// Next block
	 			if (strlen($values[$index] . $_values) > max($max_allowed_packet_size, 1024*970)){
	 				$index++;
	 			}

	 			$values[$index] .= $_values;
	 			$not_flat[trailingslashit($url)] = array('url' => $url, 'priority' => $priority);

	 			$priority += 10;
	 		}
	 		foreach ($values as $value){
	 			$value = trim($value, ',') . ';';
	 			Swift_Performance::mysql_query("INSERT IGNORE INTO {$table_name} (id, url, priority, menu_item) VALUES " . $value);
	 		}


			delete_transient('swift_performance_initial_prebuild_links');

	 		// If we need only the URLS
	 		if ($is_flat){
	 			return apply_filters('swift_performance_warmup_urls_flat', $urls);
	 		}
	 		// Imitate $wpdb->get_results
	 		else {
	 			return apply_filters('swift_performance_warmup_urls', $not_flat);
	 		}
	 	}

		/**
		 * Generate warmup table links
		 * @return array
		 */
		public static function generate_warmup_table(){
			global $wpdb;

			$timeout = Swift_Performance::set_time_limit(300, 'build_warmup_table');

			$home_url = '';
	 		$urls = $already_added = array();
			$warmup_elements = array(
				'home' => array(),
				'menu-items' => array(),
				'archives' => array(),
				'categories' => array(),
				'tags' => array(),
				'posts' => array(),
			);

	 		set_transient('swift_performance_initial_prebuild_links', true, $timeout);

			// Home
			if (Swift_Performance_Cache::is_object_cacheable(home_url())){
				$warmup_elements['home'][Swift_Performance::get_warmup_id(home_url())] = trailingslashit(home_url());
			}

	 		// Post types
	 		$post_types = array();
	 		foreach (Swift_Performance::get_post_types(Swift_Performance::get_option('exclude-post-types')) as $post_type){
	 			$post_types[] = "'{$post_type}'";

				// Archive
				if (Swift_Performance::check_option('cache-archive',1)){
		 			$archive = get_post_type_archive_link( $post_type );
		 			if ($archive !== false){
		 				$url = get_post_type_archive_link( $post_type );
		 				if (Swift_Performance_Cache::is_object_cacheable($url)){
		 					$warmup_elements['archives'][Swift_Performance::get_warmup_id($url)] = $url;
		 				}
					}
				}

				// Terms
				if (Swift_Performance::check_option('cache-terms',1)){
					$taxonomy_objects = get_object_taxonomies( $post_type, 'objects' );
					foreach ($taxonomy_objects as $key => $value) {
						$terms = get_terms($key);
						foreach ( $terms as $term ) {
							$url = get_term_link( $term );
							if (Swift_Performance_Cache::is_object_cacheable($url)){
								if (preg_match('~(product_cat|category)~',$key)){
									$warmup_elements['categories'][Swift_Performance::get_warmup_id($url)] = $url;
								}
								else {
									$warmup_elements['tags'][Swift_Performance::get_warmup_id($url)] = $url;
								}
							}
						}
					}
				}

	 		}

			$menu_item_ids	= $wpdb->get_col("SELECT meta_value FROM {$wpdb->postmeta} LEFT JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.meta_value WHERE meta_key = '_menu_item_object_id' AND post_type != 'nav_menu_item'");
			$public_post_ids	= $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN(".implode(',', $post_types).") ORDER BY post_date DESC");

	 		$posts = array_merge((array)$menu_item_ids, (array)$public_post_ids);

	 		// WPML
	 		if ((!defined('SWIFT_PERFORMANCE_WPML_WARMUP') || SWIFT_PERFORMANCE_WPML_WARMUP) && function_exists('icl_get_languages') && class_exists('SitePress')){
				if((!defined('SWIFT_PERFORMANCE_WPML_CHECK_TRANSLATIONS') || SWIFT_PERFORMANCE_WPML_CHECK_TRANSLATIONS) && $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}icl_translations'") == $wpdb->prefix . 'icl_translations') {
					$translations = $wpdb->get_col("SELECT DISTINCT element_id FROM {$wpdb->prefix}icl_translations WHERE language_code != source_language_code AND source_language_code IS NOT NULL and element_type LIKE 'post_%'");
					$posts = array_diff($posts, $translations);
				}
	 			global $sitepress;
	 			$languages = icl_get_languages('skip_missing=0&orderby=KEY&order=DIR&link_empty_to=str');
	 			foreach ($languages as $language){
	 				$sitepress->switch_lang($language['code'], true);
	 				foreach ($posts as $post_id){
	 					wp_cache_flush();
	 					$permalink = get_permalink($post_id);
	 					if (Swift_Performance_Cache::is_object_cacheable($permalink, $post_id)){
							if (in_array($post_id, $menu_item_ids)){
								$warmup_elements['menu-items'][Swift_Performance::get_warmup_id($permalink)] = $permalink;
							}
							else {
 								$warmup_elements['posts'][Swift_Performance::get_warmup_id($permalink)] = $permalink;
							}
	 					}
	 				}
	 			}
	 		}
	 		else {
	 			foreach ($posts as $post_id){
	 				wp_cache_flush();
	 				$permalink = get_permalink($post_id);
	 				if (Swift_Performance_Cache::is_object_cacheable($permalink, $post_id)){
						if (in_array($post_id, $menu_item_ids)){
							$warmup_elements['menu-items'][Swift_Performance::get_warmup_id($permalink)] = $permalink;
						}
						else {
							$warmup_elements['posts'][Swift_Performance::get_warmup_id($permalink)] = $permalink;
						}
	 				}
	 			}
	 		}

			// Remove homepage from menu items and posts
			$warmup_elements['menu-items'] = array_diff_key($warmup_elements['menu-items'], $warmup_elements['home']);
			$warmup_elements['archives'] = array_diff_key($warmup_elements['archives'], $warmup_elements['home']);
			$warmup_elements['posts'] = array_diff_key($warmup_elements['posts'], $warmup_elements['archives'], $warmup_elements['home'], $warmup_elements['menu-items']);

			// Order warmup elements
			$warmup_elements = array_replace(array_flip((array)Swift_Performance::get_option('warmup-priority-order')), $warmup_elements);
			foreach ($warmup_elements as $warmup_element) {
				$urls = array_merge($urls, $warmup_element);
			}

			// Limit pages
			$urls = array_slice($urls, 0, SWIFT_PERFORMANCE_WARMUP_LIMIT);

			return $urls;
		}

		/**
		 * Prebuild cache callback
		 */
		public static function prebuild_cache(){
			global $wpdb;

			$permalinks = Swift_Performance::get_prebuild_urls(true, true);

			// Extend timeout
			$time_limit = ini_get('max_execution_time');
			if (!Swift_Performance::is_function_disabled('set_time_limit')){
				$time_limit = Swift_Performance::set_time_limit(SWIFT_PERFORMANCE_PREBUILD_TIMEOUT, 'prebuild_cache');
			}

			// Prebuild done
			if (count($permalinks) == 0){
				Swift_Performance::log('Prebuild cache done', 9);
				Swift_Performance::stop_prebuild();
				return;
			}

			// Reschedule prebuild
			Swift_Performance::log('Reschedule prebuild cache.', 9);
			Swift_Performance::clear_hook('swift_performance_prebuild_cache');
			wp_schedule_single_event(time() + SWIFT_PERFORMANCE_PREBUILD_TIMEOUT, 'swift_performance_prebuild_cache');


			$current_process = mt_rand(0,PHP_INT_MAX);
			Swift_Performance::set_transient('swift_performance_prebuild_cache_pid', $current_process, 600);
			Swift_Performance::log('Prebuild cache ('.$current_process.') start', 9);

			foreach ($permalinks as $permalink){
				$prebuild_process = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = '_transient_swift_performance_prebuild_cache_pid'");
				if ($prebuild_process !== false && $prebuild_process != $current_process){
					Swift_Performance::log('Prebuild cache ('.$current_process.') stop', 9);
					break;
				}

				Swift_Performance::prebuild_cache_hit($permalink);
				do_action('swift_performance_prebuild_cache_hit', $permalink);

				// Maybe delay
				$prebuild_delay = (int)Swift_Performance::get_option('prebuild-speed');
				if (!empty($prebuild_delay) && $prebuild_delay > 0){
					sleep($prebuild_delay);
				}
			}

			delete_transient('swift_performance_prebuild_cache_pid');

		}

		/**
		 * Prebuild post cache callback
		 * @param string|array $permalinks
		 */
		public static function prebuild_page_cache($permalinks){
			$permalinks = (array)$permalinks;
			if (empty($permalinks)){
				return;
			}

			Swift_Performance::log('Prebuild post cache ('.$permalinks[0].')', 9);

			// Extend timeout
			Swift_Performance::set_time_limit(SWIFT_PERFORMANCE_PREBUILD_TIMEOUT, 'prebuild_page_cache');

			foreach ($permalinks as $permalink){
				Swift_Performance::prebuild_cache_hit($permalink);
			}

		}

		/**
		 * Hit page for prebuild cache
		 * @param string $permalink page to hit
		 */
		public static function prebuild_cache_hit($permalink){
			global $wpdb;

			if (!Swift_Performance_Cache::is_object_cacheable($permalink)){
				$reasons = (!empty(Swift_Performance::get_instance()->log_buffer) ? ' Reason: ' . implode(', ', Swift_Performance::get_instance()->log_buffer) : '');
				Swift_Performance::log('Page is not cacheable.' . $reasons . ' URL: ' . $permalink, 8);
				return;
			}

			if (preg_match('~(\d+)\-(revision|autosave)\-v(\d+)/?~',$permalink) || preg_match('~__trashed/?$~',$permalink)){
				return;
			}

			$max_threads = (defined('SWIFT_PERFORMANCE_THREADS') ? SWIFT_PERFORMANCE_THREADS : Swift_Performance::get_option('max-threads'));

			// Avoid armageddon (prebuild without thread limit)
			if (Swift_Performance::check_option('prebuild-speed', -1) && (self::check_option('limit-threads', 1, '!=') || ((string)$max_threads !== '0' && empty($max_threads)))){
				Swift_Performance::set_option('limit-threads', 1);
				Swift_Performance::set_option('max-threads', 3);
			}

			$threads = Swift_Performance::wait_for_thread();
			if (empty($threads)){
				Swift_Performance::log('There is no empty thread, reschedule prebuild cache.', 9);
				Swift_Performance::stop_prebuild();
				wp_schedule_single_event(time() + 60, 'swift_performance_prebuild_cache');
				return;
			}

			set_transient('swift_performance_prebuild_cache_hit', $permalink, SWIFT_PERFORMANCE_PREBUILD_TIMEOUT);
			Swift_Performance::log('Prebuild cache hit page: ' . $permalink, 9);

	            // Cloudflare
	            if(Swift_Performance::check_option('cloudflare-auto-purge',1) && Swift_Performance_Cache::get_cloudflare_headers() !== false){
	                  Swift_Performance_Cache::purge_cloudflare_zones($permalink);
				usleep(500000);
	            }

	            // Varnish
	            if(Swift_Performance::check_option('varnish-auto-purge',1)){
	                  Swift_Performance_Cache::purge_varnish_url($permalink);
	            }

			// Use prebuild proxy cloud for remote prebuild
			if (Swift_Performance::check_option('enable-remote-prebuild-cache',1)){
				$permalink = SWIFT_PERFORMANCE_API_URL . 'prebuild/' . $permalink;
				add_filter('swift_performance_prebuild_headers',function($headers){
					$headers['SWTE-PURCHASE-KEY'] = Swift_Performance::get_option('purchase-key');
					$headers['site'] = Swift_Performance::home_url();
					return $headers;
				});
			}

			// Hit page
			if (Swift_Performance::check_option('prebuild-speed', -1)){
				wp_remote_get($permalink, array('headers' => apply_filters('swift_performance_prebuild_headers',array('X-merge-assets' => 'true', 'X-Prebuild' => md5(NONCE_SALT))), 'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:52.0) Gecko/20100101 Firefox/52.0', 'timeout' => 1, 'sslverify' => false));
				$response = '';
			}
			else {
				$response = wp_remote_get($permalink, array('headers' => apply_filters('swift_performance_prebuild_headers',array('X-merge-assets' => 'true', 'X-Prebuild' => 'true')), 'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:52.0) Gecko/20100101 Firefox/52.0', 'timeout' => 120, 'sslverify' => false));
			}

			// Skip on error
			if (is_wp_error($response)){
				delete_transient('swift_performance_prebuild_cache_hit');
				Swift_Performance::log('Prebuild cache error: ' . $response->get_error_message(), 6);
				return;
			}

			if (!empty($response) && isset($response['http_response'])){
				$response_object = $response['http_response']->get_response_object();

				// Remove 404 pages from Warmup table if cache 404 is not enabled and autowarmup is enabled
				if ($response['response']['code'] == 404 && Swift_Performance::check_option('cache-404',0) && (Swift_Performance::check_option('warmup-table-source', 'auto') || Swift_Performance::check_option('autoupdate-warmup-table', 1))){
					do_action('swift_performance_remove_404', $permalink);
					Swift_Performance_Cache::clear_single_cached_item($permalink);
				}

				// Stop prebuild if server resources were exhausted
				if (in_array($response['response']['code'], array(500, 502, 503, 504, 508))){
					delete_transient('swift_performance_prebuild_cache_hit');
					Swift_Performance::log('Prebuild cache ('.$permalink.') failed. Error code: ' . $response['response']['code'], 1);
					Swift_Performance::log('Prebuild cache stopped due an error ('.$response['response']['code'].')', 6);
					Swift_Performance::stop_prebuild();
					return;
				}
				else if (isset($response_object->redirects) && !empty($response_object->redirects)){
					$id = Swift_Performance::get_warmup_id($permalink);
					Swift_Performance::mysql_query($wpdb->prepare("UPDATE " . SWIFT_PERFORMANCE_TABLE_PREFIX . "warmup SET type = 'redirect' WHERE id = %s LIMIT 1", $id));

					$_response = wp_safe_remote_get($permalink, array('headers' => apply_filters('swift_performance_prebuild_headers',array('X-merge-assets' => 'true', 'X-Prebuild' => md5(NONCE_SALT))), 'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:52.0) Gecko/20100101 Firefox/52.0', 'timeout' => 15, 'redirection' =>0, 'sslverify' => false));

					$redirected_to = wp_remote_retrieve_header($_response, 'location');
					Swift_Performance::log($permalink . ' has been redirected to ' . $redirected_to , 6);

					if (Swift_Performance::check_option('warmup-remove-redirects',1)){
						do_action('swift_performance_remove_redirect', $permalink);
						Swift_Performance_Cache::clear_single_cached_item($permalink);
					}
				}

				if (Swift_Performance::check_option('mobile-support', 1)){

					// Hit page
					if (Swift_Performance::check_option('prebuild-speed', -1)){
						wp_remote_get($permalink, array('headers' => apply_filters('swift_performance_mobile_prebuild_headers', array('X-merge-assets' => 'true', 'X-Prebuild' => md5(NONCE_SALT))), 'user-agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25', 'timeout' => 1, 'sslverify' => false));
						$response = '';
					}
					else {
						$response = wp_remote_get($permalink, array('headers' => apply_filters('swift_performance_mobile_prebuild_headers', array('X-merge-assets' => 'true', 'X-Prebuild' => 'true')), 'user-agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25', 'timeout' => 120, 'sslverify' => false));
					}

					// Skip on error
					if (is_wp_error($response)){
						delete_transient('swift_performance_prebuild_cache_hit');
						Swift_Performance::log('Prebuild cache error: ' . $response->get_error_message(), 6);
						return;
					}

					// Stop prebuild if server resources were exhausted
					if (!empty($response) && in_array($response['response']['code'], array(500, 502, 503, 504, 508))){
						delete_transient('swift_performance_prebuild_cache_hit');
						Swift_Performance::log('Prebuild cache ('.$permalink.') failed. Error code: ' . $response['response']['code'], 1);
						Swift_Performance::log('Prebuild cache stopped due an error', 6);
						Swift_Performance::stop_prebuild();
						return;
					}
				}
			}
			delete_transient('swift_performance_prebuild_cache_hit');
		}

		/**
		 * Stop prebuild cache
		 */
		public static function stop_prebuild(){
			Swift_Performance::set_transient('swift_performance_prebuild_cache_pid', 'stop', SWIFT_PERFORMANCE_PREBUILD_TIMEOUT);
			Swift_Performance::clear_hook('swift_performance_prebuild_cache');
			Swift_Performance::clear_hook('swift_performance_prebuild_page_cache');
			delete_transient('swift_performance_prebuild_cache_hit');
		}

		/**
		 * Add toolbar options
		 * @param WP_Admin_Bar $admin_bar
		 */
		public static function toolbar_items($admin_bar){
			$user = wp_get_current_user();
			if (array_intersect((array)Swift_Performance::get_option('clear-cache-roles'), $user->roles ) || current_user_can('manage_options')){
				$current_page = site_url(str_replace(site_url(), '', 'http'.(isset($_SERVER['HTTPS']) ? 's' : '') . '://' . Swift_Performance::$http_host . $_SERVER['REQUEST_URI']));
				list($current_page_nq,) = explode('?',$current_page);

				$admin_bar->add_menu(array(
					'id'    => 'swift-performance',
					'title' => SWIFT_PERFORMANCE_PLUGIN_NAME,
					'href'  => esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG), admin_url('tools.php')))
				 ));

				// Admin users only
				if (current_user_can('manage_options')){
					if(Swift_Performance::check_option('purchase-key', '', '!=')){
		 				$admin_bar->add_menu(array(
		 					'id'    => 'swift-image-optimizer',
		 					'parent' => 'swift-performance',
		 					'title' => esc_html__('Image Optimizer', 'swift-performance'),
		 					'href'  => esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'image-optimizer'), admin_url('tools.php')))
		 				));
		 			}
					$admin_bar->add_menu(array(
						'id'    => 'swift-db-optimizer',
						'parent' => 'swift-performance',
						'title' => esc_html__('DB Optimizer', 'swift-performance'),
						'href'  => esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'db-optimizer'), admin_url('tools.php')))
					));
					$admin_bar->add_menu(array(
						'id'    => 'swift-plugin-organizer',
						'parent' => 'swift-performance',
						'title' => esc_html__('Plugin Organizer', 'swift-performance'),
						'href'  => esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'plugin-organizer'), admin_url('tools.php')))
					));
				}
				if(Swift_Performance::check_option('enable-caching', 1)){
					$admin_bar->add_menu(array(
						'id'    => 'clear-swift-cache',
						'parent' => 'swift-performance',
						'title' => esc_html__('Clear All Cache', 'swift-performance'),
						'href'  => esc_url(wp_nonce_url(add_query_arg('swift-performance-action', 'clear-all-cache', $current_page), 'clear-swift-cache')),
					));

					if (!is_admin() && Swift_Performance_Cache::is_cached($current_page_nq)){
						$admin_bar->add_menu(array(
							'id'    => 'swift-cache-single',
							'parent' => 'swift-performance',
							'title' => esc_html__('Clear Page Cache', 'swift-performance'),
							'href'  => esc_url(wp_nonce_url(add_query_arg(array('swift-performance-action' => 'clear-page-cache', 'permalink' => urlencode($current_page_nq)), $current_page), 'clear-swift-cache')),
						));
						$admin_bar->add_menu(array(
							'id'    => 'swift-view-cached',
							'parent' => 'swift-performance',
							'title' => esc_html__('View Cached', 'swift-performance'),
							'href'  => esc_url(add_query_arg('force-cached', '1', $current_page_nq)),
						));
					}
				}
				if(Swift_Performance::check_option('enable-caching', 1, '!=') && (Swift_Performance::check_option('merge-scripts', 1) || Swift_Performance::check_option('merge-styles', 1))){
					$admin_bar->add_menu(array(
						'id'    => 'clear-swift-assets-cache',
						'parent' => 'swift-performance',
						'title' => esc_html__('Clear Assets Cache', 'swift-performance'),
						'href'  => esc_url(wp_nonce_url(add_query_arg('swift-performance-action', 'clear-assets-cache', $current_page), 'clear-swift-assets-cache')),
					));
				}
				if (Swift_Performance::check_option('enable-cdn',1) && Swift_Performance::check_option('maxcdn-key','','!=') && Swift_Performance::check_option('maxcdn-secret','','!=')){
					$admin_bar->add_menu(array(
						'id'    => 'purge-swift-cdn',
						'parent' => 'swift-performance',
						'title' => esc_html__('Purge CDN (All zones)', 'swift-performance'),
						'href'  => esc_url(wp_nonce_url(add_query_arg('swift-performance-action', 'purge-cdn', $current_page), 'purge-swift-cdn')),
					));
				}

				// Admin users only
				if (current_user_can('manage_options')){
					$admin_bar->add_menu(array(
						'id'    => 'swift-settings',
						'parent' => 'swift-performance',
						'title' => esc_html__('Settings', 'swift-performance'),
						'href'  => esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'settings'), admin_url('tools.php'))),
					));

					if (apply_filters('swift-performance-setup-wizard-enabled', true)){
						$admin_bar->add_menu(array(
							'id'    => 'swift-setup-wizard',
							'parent' => 'swift-performance',
							'title' => esc_html__('Setup Wizard', 'swift-performance'),
							'href'  => esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'setup'), admin_url('tools.php'))),
						));
					}
				}
			}
			else if (!is_admin() && current_user_can('edit_post', get_the_ID())){
				$current_page = site_url(str_replace(home_url(), '', 'http'.(isset($_SERVER['HTTPS']) ? 's' : '') . '://' . Swift_Performance::$http_host . $_SERVER['REQUEST_URI']));
				list($current_page_nq,) = explode('?',$current_page);

				if(Swift_Performance::check_option('enable-caching', 1)){
					if (Swift_Performance_Cache::is_cached($current_page_nq)){
						$admin_bar->add_menu(array(
							'id'    => 'swift-cache-single',
							'title' => esc_html__('Clear Page Cache', 'swift-performance'),
							'href'  => esc_url(wp_nonce_url(add_query_arg(array('swift-performance-action' => 'clear-page-cache', 'permalink' => urlencode($current_page_nq)), $current_page), 'clear-swift-cache')),
						));
						$admin_bar->add_menu(array(
							'id'    => 'swift-view-cached',
							'title' => esc_html__('View Cached', 'swift-performance'),
							'href'  => esc_url(add_query_arg('force-cached', '1', $current_page_nq)),
						));
					}
				}
			}
		}

		/**
		 * Clean htaccess, scheduled hooks, and remove early Loader on deactivation
		 */
		public static function deactivate(){
			global $wpdb;
			$rules = array();

			// Clear all cache
			Swift_Performance_Cache::clear_all_cache();

			// Clear scheduled hooks
			Swift_Performance::clear_hook('swift_performance_clear_cache');
			Swift_Performance::clear_hook('swift_performance_clear_expired');
			Swift_Performance::clear_hook('swift_performance_clear_short_lifespan');
			Swift_Performance::clear_hook('swift_performance_clear_assets_proxy_cache');
			Swift_Performance::clear_hook('swift_performance_prebuild_cache');

			// Clean up htaccess and early loader
			Swift_Performance::write_rewrite_rules($rules);
			Swift_Performance::early_loader(true);

			Swift_Performance_Image_Optimizer::uninstall();
		}

		/**
		 * Delete DB tables, options on uninstall
		 */
		public static function uninstall(){
			global $wpdb;

			$settings = apply_filters('swift_performance_deactivation_settings',get_option('swift-performance-deactivation-settings'));

			// Delete settings
			if (!isset($settings['keep-settings']) || empty($settings['keep-settings'])){
				if (!defined('SWIFT_PERFORMANCE_WHITELABEL') || SWIFT_PERFORMANCE_WHITELABEL === false){
					add_filter('luv_framework_get_options', '__return_false');
					delete_option('swift_performance_options');
				}

				delete_option('swift_performance_options-transients');
				delete_option('swift_performance_plugin_organizer');
				delete_option('swift-perforomance-critical-font');
				delete_option('swift_performance_timeout');

				// DELETE postmeta
				$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'swift-performance'");
				$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'swift_performance_options'");

				// DELETE usermeta
				$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key = 'swift_pointers'");
			}

			// Custom htaccess
			$server_software = self::server_software();
			if (isset($settings['keep-custom-htaccess']) && !empty($settings['keep-custom-htaccess']) && !Swift_Performance::disable_file_edit() && $server_software == 'apache'){
				$custom_htaccess = Swift_Performance::get_option('custom-htaccess');
				$custom_htaccess = trim($custom_htaccess);
				if (!empty($custom_htaccess)){
					$rules['custom-htaccess'] = $custom_htaccess;
				}
			}

			// Delete warmup table
			if (!isset($settings['keep-warmup-table']) || empty($settings['keep-warmup-table'])){
				if (is_multisite()){
					$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'swift_performance_warmup');
				}
				else {
					$wpdb->query('DROP TABLE IF EXISTS ' . SWIFT_PERFORMANCE_TABLE_PREFIX . 'warmup');
				}
				delete_option(SWIFT_PERFORMANCE_TABLE_PREFIX . 'db_version');
			}

			// Clear logs
			if (!isset($settings['keep-logs']) || empty($settings['keep-logs'])){
				$logpath = Swift_Performance::get_option('log-path');
				if (file_exists($logpath)){
					$files = array_diff(scandir($logpath), array('.','..'));
					foreach ($files as $file) {
						@unlink(trailingslashit($logpath) . $file);
					}
					@rmdir($logpath);
				}
			}

			// Delete logs
			$logpath = Swift_Performance::get_option('log-path');
			if (file_exists($logpath)){
				$files = array_diff(scandir($logpath), array('.','..'));
				foreach ($files as $file) {
					@unlink(trailingslashit($logpath) . $file);
				}
			}

			// Delete options
			delete_option('swift_performance_rewrites');
			delete_option('swift-performance-deactivation-settings');
			delete_option('swift_performance_messages');
			delete_option('swift-perforomance-initial-setup-wizard');
			delete_option('swift-performance-developer-mode');
			delete_option('swift-performance-license');
			delete_option('external_updates-swift-performance');

			// DELETE all prefixed transients (eg ajax/dynamic cache, prebuild, etc)
			$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_swift_performance_%'");
			$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_swift_performance_%'");
		}

		/**
		 * Generate htaccess and scheduled hooks on activation
		 */
		public static function activate(){
			// Prepare the setup wizard
			set_transient('swift-performance-setup', 'uid:'.get_current_user_id(), 300);

			// Backup htaccess
			if (self::server_software() == 'apache' && file_exists(Swift_Performance::get_home_path() . '.htaccess') && apply_filters('swift_performance_backup_htaccess', true)){
				copy(Swift_Performance::get_home_path() . '.htaccess', (Swift_Performance::get_home_path() . '.htaccess_swift_backup'));
			}

			// Schedule clear cache
			Swift_Performance::schedule_clear_cache();

			// Build rewrite rules
			$rules = self::build_rewrite_rules();
			self::write_rewrite_rules($rules);

			// Start prebuild if auto prebuild is enabled
			if (Swift_Performance::check_option('automated_prebuild_cache',1)){
	                  wp_schedule_single_event(time(), 'swift_performance_prebuild_cache');
	                  Swift_Performance::log('Prebuild cache scheduled', 9);
	            }

			// Early loader
			self::early_loader();
		}

		/**
		 * Write rewrite rules, clear scheduled hooks, set schedule (if necessary), clear cache on save
		 */
		public static function options_saved(){
			// Refresh options
			global $swift_performance_options;
	 	 	$swift_performance_options = get_option('swift_performance_options', array());

			// Build rewrite rules
			$rules = self::build_rewrite_rules();
			self::write_rewrite_rules($rules);

			// Clear previously scheduled hooks
			Swift_Performance::clear_hook('swift_performance_clear_cache');
			Swift_Performance::clear_hook('swift_performance_clear_expired');
			Swift_Performance::clear_hook('swift_performance_clear_short_lifespan');
			Swift_Performance::clear_hook('swift_performance_clear_assets_proxy_cache');

			// Clear prebuild booster transient
			Swift_Performance_Prebuild_Booster::clear();

			// Schedule clear cache
			Swift_Performance::schedule_clear_cache();

			// Remote Cron
			if (Swift_Performance::check_option('remote-cron',1)){
				$response = Swift_Performance::api('cron/schedule/', array(
					'cron_url'	=> Swift_Performance::site_url() . 'wp-cron.php?doing_wp_cron',
					'frequency'	=> Swift_Performance::get_option('remote-cron-frequency')
				));

				if ($response !== false && isset($response['type']) && $response['type'] == 'success'){
					Swift_Performance::log('Remote Cron scheduled (' . Swift_Performance::get_option('remote-cron-frequency') . ')', 9);
				}
				else {
					Swift_Performance::log('Remote Cron failed (' . $response['message'] . ')', 1);
				}
			}

			self::early_loader();
			do_action('swift_performance_options_saved');
		}

		/**
		 * Build rewrite rules based on settings and server software
		 */
		public static function build_rewrite_rules(){

			if(!wp_next_scheduled('swift_performance_api_messages')) {
				wp_schedule_event(time(), 'twicedaily', 'swift_performance_api_messages');
		    	}

			$rules = $errors = array();
			$server_software = self::server_software();
			try{

				// Custom htaccess
				if (Swift_Performance::check_option('custom-htaccess', '', '!=') && !Swift_Performance::disable_file_edit() && $server_software == 'apache'){
					$rules['custom-htaccess'] = Swift_Performance::get_option('custom-htaccess');
				}

				// Compression
				if (Swift_Performance::check_option('enable-caching', 1) && Swift_Performance::check_option('enable-gzip', 1)) {
					switch($server_software){
						case 'apache':
							$rules['compression'] = apply_filters('swift_performance_browser_gzip', file_get_contents(SWIFT_PERFORMANCE_DIR . 'modules/cache/rewrites/htaccess-deflate.txt'));
							break;
						case 'nginx':
							$rules['compression'] = apply_filters('swift_performance_browser_gzip', file_get_contents(SWIFT_PERFORMANCE_DIR . 'modules/cache/rewrites/nginx-deflate.txt'));
							break;
						default:
							throw new Exception(esc_html__('Advanced Cache Control doesn\'t supported on your server', 'swift-performance'));
					}
				}
				// Browser cache
				if (Swift_Performance::check_option('enable-caching', 1) && Swift_Performance::check_option('browser-cache', 1)){
					switch($server_software){
						case 'apache':
							$rules['cache-control'] = apply_filters('swift_performance_browser_cache', file_get_contents(SWIFT_PERFORMANCE_DIR . 'modules/cache/rewrites/htaccess-browser-cache.txt'));
							break;
						case 'nginx':
							$rules['cache-control'] = apply_filters('swift_performance_browser_cache', file_get_contents(SWIFT_PERFORMANCE_DIR . 'modules/cache/rewrites/nginx-browser-cache.txt'));
							break;
						default:
							throw new Exception(esc_html__('Advanced Cache Control doesn\'t supported on your server', 'swift-performance'));
					}
				}
				if (Swift_Performance::check_option('enable-caching', 1) && Swift_Performance::check_option('caching-mode', 'disk_cache_rewrite')){
					switch($server_software){
						case 'apache':
							$rules['basic'] = apply_filters('swift_performance_cache_rewrites', include_once SWIFT_PERFORMANCE_DIR . 'modules/cache/rewrites/htaccess.php');
							break;
						case 'nginx':
							$rules['basic'] = apply_filters('swift_performance_cache_rewrites', include_once SWIFT_PERFORMANCE_DIR . 'modules/cache/rewrites/nginx.php');
							break;
						default:
							throw new Exception(esc_html__('Rewrite mode isn\'t supported on your server', 'swift-performance'));
					}
				}
				// CORS
				switch($server_software){
					case 'apache':
						$rules['cors'] = apply_filters('swift_performance_cors_rules', file_get_contents(SWIFT_PERFORMANCE_DIR . 'modules/cdn/rewrites/htaccess.txt'));
						break;
					case 'nginx':
						$rules['cache-control'] = apply_filters('swift_performance_cors_rules', file_get_contents(SWIFT_PERFORMANCE_DIR . 'modules/cdn/rewrites/nginx.txt'));
						break;
					default:
						throw new Exception(esc_html__('Advanced Cache Control doesn\'t supported on your server', 'swift-performance'));
				}

				// WebP
				if (Swift_Performance::check_option('serve-webp', 'rewrite') && $server_software == 'apache'){
					$rules['webp'] = apply_filters('swift_performance_cache_rewrites', include_once SWIFT_PERFORMANCE_DIR . 'modules/image-optimizer/rewrites/htaccess.php');
				}


				Swift_Performance::log('Build rewrite rules', 9);
				return $rules;
			}
			catch(Exception $e){
				self::add_notice($e->getMessage(), 'error');
				Swift_Performance::log('Build rewrite rules error: ' . $e->getMessage(), 1);
			}
		}


		/**
		 * Write rewrite rules if it is possible, otherwise add warning with rules
		 * @param array $rules
		 */
		public static function write_rewrite_rules($rules = array()){
			$multisite_padding = (is_multisite() ? ' - ' . hash('crc32',home_url()) : '');
			$server_software = self::server_software();
			if ($server_software == 'apache' && file_exists(Swift_Performance::get_home_path() . '.htaccess')){
				$htaccess = file_get_contents(Swift_Performance::get_home_path() . '.htaccess');
				$htaccess = preg_replace("~###BEGIN ".SWIFT_PERFORMANCE_PLUGIN_NAME."{$multisite_padding}###(.*)###END ".SWIFT_PERFORMANCE_PLUGIN_NAME."{$multisite_padding}###\n?~is", '', $htaccess);

				// Avoid duplicated GZIP rules
				if (preg_match('~(AddOutputFilterByType DEFLATE|mod_gzip_item)~', $htaccess)){
					unset($rules['compression']);
				}

				// Avoid duplicated browser cache rules
				if (preg_match('~ExpiresByType~', $htaccess)){
					unset($rules['cache-control']);
				}

				// Generate rules
				$rewrites = '';
				if (!empty($rules)){
					$rewrites = "###BEGIN ".SWIFT_PERFORMANCE_PLUGIN_NAME."{$multisite_padding}###\n" . implode("\n", $rules) . "\n###END ".SWIFT_PERFORMANCE_PLUGIN_NAME."{$multisite_padding}###\n";
					if (Swift_Performance::detect_htaccess_redirects($htaccess)){
						$htaccess_parts = explode('# BEGIN WordPress', $htaccess);
						$htaccess = $htaccess_parts[0] . $rewrites . '# BEGIN WordPress' . $htaccess_parts[1];
					}
					else {
						$htaccess = $rewrites . $htaccess;
					}
				}

				// Write htaccess
				if (is_writable(Swift_Performance::get_home_path() . '.htaccess')){
					@file_put_contents(Swift_Performance::get_home_path() . '.htaccess', $htaccess);
				}
				else {
					Swift_Performance::log(Swift_Performance::get_home_path() . '.htaccess' . ' is not writable', 6);
				}
				update_option('swift_performance_rewrites', $rewrites, false);
			}
			else if ($server_software == 'nginx'){
				$rewrites = "###BEGIN ".SWIFT_PERFORMANCE_PLUGIN_NAME."{$multisite_padding}###\n" . implode("\n", $rules) . "\n###END ".SWIFT_PERFORMANCE_PLUGIN_NAME."{$multisite_padding}###\n";
				update_option('swift_performance_rewrites', $rewrites, false);
			}
		}

		/**
		 * Detect www-nonwww and force SSL redirects in htaccess
		 * @param string $htaccess
		 * @return boolean
		 */
		public static function detect_htaccess_redirects($htaccess){
			// Return false if proper WordPress padding is missing
			if (strpos($htaccess, '# BEGIN WordPress') === false){
				return false;
			}

			// Check host based redirects
			if (strpos($htaccess, 'RewriteCond %{HTTP_HOST} ^') !== false){
				return true;
			}

			// Port based redirects
			if (strpos($htaccess, 'RewriteCond %{SERVER_PORT} ') !== false){
				return true;
			}

			// Environment variable based redirects
			if (strpos($htaccess, 'RewriteCond %{HTTPS} ') !== false){
				return true;
			}

			return false;
		}

		/**
		 * Set messages
		 * @param string $message
		 * @param string $type
		 * @param string $id
		 */
		public static function add_notice($message, $type = 'info', $id = ''){
			if (empty(trim($message))){
				return;
			}
			if (!empty($id)){
				$permanent = true;
			}
			else {
				$permanent	= false;
				$id		= md5($message.$type);
			}
			$messages		= get_option('swift_performance_messages', array());
			$messages[$id]	= array('message' => $message, 'type' => $type, 'permanent' => $permanent);
			update_option('swift_performance_messages', $messages);
			Swift_Performance::log('Admin notice has been added (id: '.$id.')', 9);
		}

		/**
		 * Wait for free thread, and return the number of free threads
		 * @param int $wait max wait in seconts
		 */
		public static function wait_for_thread($wait = 30){
			$wait	= apply_filters('swift_performance_wait_for_thread_timeout', $wait);
			$sleep = apply_filters('swift_performance_wait_for_thread_step', 5);
			$max_threads = (defined('SWIFT_PERFORMANCE_THREADS') ? SWIFT_PERFORMANCE_THREADS : Swift_Performance::get_option('max-threads'));

			// Always returns 1 if limit threads is not enabled or if max-threads option is not zero but it isn't exists
			if (self::check_option('limit-threads', 1, '!=') || ((string)$max_threads !== '0' && empty($max_threads))){
				return 1;
			}

			$timeout = time() + $wait;
			do {
				$active_threads	= Swift_Performance::get_thread_array();
				$free_threads	= (int)$max_threads - count($active_threads);

 				// Return free threads
				if ($free_threads > 0){
					return $free_threads;
				}

				// Timeout
				if ($timeout < time()){
					return 0;
				}

				// Wait
				Swift_Performance::log('Wait for thread timeout('.$sleep.'s).', 9);

				sleep($sleep);
			} while ($free_threads <= 0);

			return 0;
		}

		/**
		 * Get thread transient
		 * @return array
		 */
		public static function get_thread_array(){
			global $wpdb;
			$serialized = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = '_transient_swift_performance_threads'");
			$threads = maybe_unserialize($serialized);

			// No active threads
			if (empty($serialized) || !is_array($threads)){
				return array();
			}

			// Clear expired threads
			foreach ($threads as $key => $value) {
				if ($value < time()){
					unset($threads[$key]);
				}
			}

			return $threads;
		}

		/**
		 * Define SWIFT_PERFORMANCE_THREAD constant to cache thread availability
		 */
		public static function set_thread($is_available){
			if (!defined('SWIFT_PERFORMANCE_THREAD')){
				Swift_Performance::log('Thread for ' . (is_ssl() ? 'https://' : 'http://') . Swift_Performance::$http_host . $_SERVER['REQUEST_URI'] . ' is ' . ($is_available ? 'available' : 'not available'), 9);

				if (!$is_available && !defined('SWIFT_PERFORMANCE_DISABLE_CACHE')){
					define('SWIFT_PERFORMANCE_DISABLE_CACHE', true);
				}

				define('SWIFT_PERFORMANCE_THREAD', $is_available);
			}
		}

		/**
		 * Get free threads
		 * @return boolean is there free thread or not
		 */
		public static function get_thread(){
			if (defined('SWIFT_PERFORMANCE_THREAD')){
				return SWIFT_PERFORMANCE_THREAD;
			}

			$key = md5(Swift_Performance::$http_host . $_SERVER['REQUEST_URI'] . (defined('LOGGED_IN_COOKIE') && isset($_COOKIE[LOGGED_IN_COOKIE]) ? $_COOKIE[LOGGED_IN_COOKIE] : ''));

			// Disable optimizing for users
			if (Swift_Performance::check_option('optimize-prebuild-only', 1) && !isset($_SERVER['HTTP_X_PREBUILD'])){
				Swift_Performance::set_thread(false);
				return false;
			}

			$max_threads = (defined('SWIFT_PERFORMANCE_THREADS') ? SWIFT_PERFORMANCE_THREADS : self::get_option('max-threads'));
			// Always returns true if limit threads is not enabled or if max-threads option is zero or it isn't exists
			if (self::check_option('limit-threads', 1, '!=') || ((string)$max_threads !== '0' && empty($max_threads))){
				Swift_Performance::set_thread(true);
				return true;
			}

			$threads = Swift_Performance::get_thread_array();

			// Don't work on same page in same time
			if (isset($thread[$key])){
				Swift_Performance::set_thread(false);
				return false;
			}

			if (count($threads) >= (int)$max_threads){
				Swift_Performance::set_thread(false);
				return false;
			}

			Swift_Performance::set_thread(true);
			return true;
		}

		/**
		 * Lock worker thread
		 * @param string $hook action hook to unlock thread
		 */
		public static function lock_thread($hook){
			$key = md5(Swift_Performance::$http_host . $_SERVER['REQUEST_URI'] . (defined('LOGGED_IN_COOKIE') && isset($_COOKIE[LOGGED_IN_COOKIE]) ? $_COOKIE[LOGGED_IN_COOKIE] : ''));

			$threads = Swift_Performance::get_thread_array();

			$threads[$key] = time() + 600;
			Swift_Performance::set_transient('swift_performance_threads', $threads, 600);
			if (!empty($hook)){
				add_action($hook, array('Swift_Performance', 'unlock_thread'), 9);
			}
			Swift_Performance::log('Lock thread for ' . (is_ssl() ? 'https://' : 'http://') . Swift_Performance::$http_host . $_SERVER['REQUEST_URI'], 9);
		}

		/**
		 * Unlock worker thread
		 */
		public static function unlock_thread(){
			$key = md5(Swift_Performance::$http_host . $_SERVER['REQUEST_URI'] . (defined('LOGGED_IN_COOKIE') && isset($_COOKIE[LOGGED_IN_COOKIE]) ? $_COOKIE[LOGGED_IN_COOKIE] : ''));

			$threads = Swift_Performance::get_thread_array();

			if (!empty($threads) && isset($threads[$key])){
				unset($threads[$key]);
				Swift_Performance::set_transient('swift_performance_threads', $threads, 600);
			}
			Swift_Performance::log('Unlock thread for ' . (is_ssl() ? 'https://' : 'http://') . Swift_Performance::$http_host . $_SERVER['REQUEST_URI'], 9);
		}

		/**
		 * Check is dev mode active
		 * @return boolean
		 */
		public static function is_developer_mode_active(){
			if (get_option('swift-performance-developer-mode') > time()){
				return true;
			}

			// Delete option if expired
			delete_option('swift-performance-developer-mode');
			return false;
		}

		/**
		 * Extend is_admin to check if current page is login or register page
		 */
		public static function is_admin() {
			global $pagenow;
	    		return apply_filters('swift_performance_is_admin', (is_admin() || in_array( $pagenow, array( 'wp-login.php', 'wp-register.php' )) || (isset($_GET['vc_editable']) && $_GET['vc_editable'] == 'true') || isset($_GET['customize_theme']) ));
		}

		/**
		 * Bypass built in function to be able call it early
		 */
		public static function is_user_logged_in(){
			return apply_filters('swift_performance_is_user_logged_in', (defined('LOGGED_IN_COOKIE') && isset($_COOKIE[LOGGED_IN_COOKIE]) && !empty($_COOKIE[LOGGED_IN_COOKIE])));
		}

		/**
		 * Bypass built in function to be able call it early
		 */
		public static function is_404(){
			global $wp_query;
			return apply_filters('swift_performance_is_404', (isset( $wp_query ) && !empty($wp_query) ? is_404() : false));
		}

		/**
		 * Bypass built in function to be able call it early
		 */
		public static function is_author(){
			global $wp_query;
			return apply_filters('swift_performance_is_author', (isset( $wp_query ) && !empty($wp_query) ? is_author() : false));
		}

		/**
		 * Bypass built in function to be able call it early
		 */
		public static function is_archive(){
			global $wp_query;
			return apply_filters('swift_performance_is_archive', (isset( $wp_query ) && !empty($wp_query) ? is_archive() : false));
		}

		/**
		 * Bypass built in function to be able call it early
		 */
		public static function is_feed(){
			global $wp_query;
			return apply_filters('swift_performance_is_feed', (isset( $wp_query ) && !empty($wp_query) ? is_feed() : false));
		}

		/**
		 * Check is the current request a REST API request
		 */
		public static function is_rest($route = ''){
			global $wp_query;
			if (empty($wp_query)){
				return apply_filters('swift_performance_is_rest', false);
			}
			$rest_url = get_rest_url() . $route;

			if (preg_match('~^'.preg_quote(parse_url($rest_url, PHP_URL_PATH)).'~', $_SERVER['REQUEST_URI'])){
				return apply_filters('swift_performance_is_rest', true);
			}
			return apply_filters('swift_performance_is_rest', false);
		}

		/**
		 * Is current post password protected
		 * @return boolean;
		 */
		public static function is_password_protected($post_id = 0){
			// Specific post_id
			if (!empty($post_id)){
				$post = get_post($post_id);
			}
			// Use global $post
			else {
				global $post;
			}

			return apply_filters('swift_performance_is_password_protected', (isset($post->post_password) && !empty($post->post_password)));
		}

		/**
		 * Check is the current page an AMP page
		 */
		public static function is_amp($buffer) {
	    		return apply_filters('swift_performance_is_amp', (preg_match('~<html([^>])?\samp(\s|>)~', $buffer)));
		}

		/**
		 * Check is the current page sitemap
		 */
		public static function is_sitemap($buffer) {
			return apply_filters('swift_performance_is_sitemap', (!preg_match('~<html([^>]*)>~', $buffer) && (preg_match('~<sitemap([^>]*)>~', $buffer) || preg_match('~<urlset([^>]*)>~', $buffer))));
		}

		/**
		 * Check is browser mobile
		 */
		public static function is_mobile() {
			if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
				$is_mobile = false;
			} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
				|| strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
				|| strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
				|| strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
				|| strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
				|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
				|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
					$is_mobile = true;
			} else {
				$is_mobile = false;
			}

			return apply_filters('swift_performance_is_mobile', $is_mobile);
		}

		/**
		 * Check if disable file edit is enabled
		 */
		public static function disable_file_edit(){
			return (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) || (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS);
		}

		/**
		 * Bypass built in function to be able get unfiltered home url
		 * @return string
		 */
		public static function home_url(){
			if (defined('WP_HOME')){
				return apply_filters('swift_performance_home_url', trailingslashit(WP_HOME));
			}

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if (isset($alloptions['home'])){
				$home_url = $alloptions['home'];
			}
			else {
				global $wpdb;
				$home_url = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'home'");
			}

			// Fallback for special installations eg GoDaddy
			if (empty($home_url)){
				$home_url = home_url();
			}

			return apply_filters('swift_performance_home_url', trailingslashit($home_url));
		}

		/**
		 * Bypass built in function to be able get home path early
		 * @return string
		 */
		public static function get_home_path(){
			$home    = set_url_scheme( get_option( 'home' ), 'http' );
			$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );

			if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
				$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
				$pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
				$home_path           = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
				$home_path           = trailingslashit( $home_path );
			} else {
				$home_path = ABSPATH;
			}

			return str_replace( '\\', '/', $home_path );
		}

		/**
		 * Bypass built in function to be able get unfiltered site url
		 * @return string
		 */
		public static function site_url(){
			if (defined('WP_SITEURL')){
				return apply_filters('swift_performance_site_url', trailingslashit(WP_SITEURL));
			}

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if (isset($alloptions['siteurl'])){
				$site_url = $alloptions['siteurl'];
			}
			else {
				global $wpdb;
				$site_url = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'siteurl'");
			}

			// Fallback for special installations eg GoDaddy
			if (empty($site_url)){
				$site_url = site_url();
			}

			return apply_filters('swift_performance_site_url', trailingslashit($site_url));
		}

		/**
		 * Returns WordPress install home directory (with leading slash, no trailing slash)
		 * @return string
		 */
		public static function home_dir(){
			return apply_filters('swift_performance_home_dir', rtrim(parse_url(Swift_Performance::home_url(), PHP_URL_PATH), '/'));
		}

		/**
		 * Bypass built in set_transient function (force use DB instead object cache)
		 *
		 * @param string $transient  Transient name. Expected to not be SQL-escaped. Must be
		 *                           172 characters or fewer in length.
		 * @param mixed  $value      Transient value. Must be serializable if non-scalar.
		 *                           Expected to not be SQL-escaped.
		 * @param int    $expiration Optional. Time until expiration in seconds. Default 0 (no expiration).
		 * @return bool False if value was not set and true if value was set.
		 */
		public static function set_transient( $transient, $value, $expiration = 0 ) {

			$expiration = (int) $expiration;

			/**
			 * Filters a specific transient before its value is set.
			 *
			 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
			 *
			 * @since 3.0.0
			 * @since 4.2.0 The `$expiration` parameter was added.
			 * @since 4.4.0 The `$transient` parameter was added.
			 *
			 * @param mixed  $value      New value of transient.
			 * @param int    $expiration Time until expiration in seconds.
			 * @param string $transient  Transient name.
			 */
			$value = apply_filters( "pre_set_transient_{$transient}", $value, $expiration, $transient );

			/**
			 * Filters the expiration for a transient before its value is set.
			 *
			 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
			 *
			 * @since 4.4.0
			 *
			 * @param int    $expiration Time until expiration in seconds. Use 0 for no expiration.
			 * @param mixed  $value      New value of transient.
			 * @param string $transient  Transient name.
			 */
			$expiration = apply_filters( "expiration_of_transient_{$transient}", $expiration, $value, $transient );

			$transient_timeout = '_transient_timeout_' . $transient;
			$transient_option = '_transient_' . $transient;
			if ( false === get_option( $transient_option ) ) {
				$autoload = 'yes';
				if ( $expiration ) {
					$autoload = 'no';
					add_option( $transient_timeout, time() + $expiration, '', 'no' );
				}
				$result = add_option( $transient_option, $value, '', $autoload );
			} else {
				// If expiration is requested, but the transient has no timeout option,
				// delete, then re-create transient rather than update.
				$update = true;
				if ( $expiration ) {
					if ( false === get_option( $transient_timeout ) ) {
						delete_option( $transient_option );
						add_option( $transient_timeout, time() + $expiration, '', 'no' );
						$result = add_option( $transient_option, $value, '', 'no' );
						$update = false;
					} else {
						update_option( $transient_timeout, time() + $expiration );
					}
				}
				if ( $update ) {
					$result = update_option( $transient_option, $value );
				}
			}


			if ( $result ) {

				/**
				 * Fires after the value for a specific transient has been set.
				 *
				 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
				 *
				 * @since 3.0.0
				 * @since 3.6.0 The `$value` and `$expiration` parameters were added.
				 * @since 4.4.0 The `$transient` parameter was added.
				 *
				 * @param mixed  $value      Transient value.
				 * @param int    $expiration Time until expiration in seconds.
				 * @param string $transient  The name of the transient.
				 */
				do_action( "set_transient_{$transient}", $value, $expiration, $transient );

				/**
				 * Fires after the value for a transient has been set.
				 *
				 * @since 3.0.0
				 * @since 3.6.0 The `$value` and `$expiration` parameters were added.
				 *
				 * @param string $transient  The name of the transient.
				 * @param mixed  $value      Transient value.
				 * @param int    $expiration Time until expiration in seconds.
				 */
				do_action( 'setted_transient', $transient, $value, $expiration );
			}
			return $result;
		}

		/**
		 * Check Swift Performance settings
		 * @param string $key
		 * @param mixed $value
		 * @return boolean
		 */
		public static function check_option($key, $value, $condition = '='){
		      if ($condition == '='){
		            return self::get_option($key) == $value;
		      }
		      else if ($condition == '!='){
		            return self::get_option($key) != $value;
		      }
			else if ($condition == '<'){
		            return self::get_option($key) < $value;
		      }
			else if ($condition == '>'){
		            return self::get_option($key) > $value;
		      }
			else if (strtoupper($condition) == 'IN'){
				return in_array(self::get_option($key), (array)$value);
			}

		}

		/**
		 * Check Swift Performance option is set
		 * @param string $key
		 * @return boolean
		 */
		public static function is_option_set($key, $is_array = false){
			$value = self::get_option($key);
		      if ($is_array){
				$value = array_filter((array)$value);
			}
			return !empty($value);
		}

		/**
		 * Get Swift Performance option
		 * @param string $key
		 * @param mixed $default
		 * @return mixed
		 */
		public static function get_option($key, $default = '', $suppress_filters = false){
		      global $swift_performance_options;
			if (empty($swift_performance_options)){
			    $swift_performance_options = get_option('swift_performance_options', array());
			}
		      if (isset($swift_performance_options[$key])){
				if ($suppress_filters){
					return $swift_performance_options[$key];
				}
		            return apply_filters('swift_performance_option_' . $key, apply_filters('swift_performance_option', $swift_performance_options[$key], $key));
		      }
		      else {
				if ($suppress_filters){
					return false;
				}
		            return apply_filters('swift_performance_option_' . $key, apply_filters('swift_performance_option', false, $key));
		      }
		}

		/**
		 * Set Swift Performance option runtime
		 * @param string $key
		 * @param mixed $default
		 */
		public static function set_option($key, $value){
			add_filter('swift_performance_option_' . $key, function() use ($value){
				return $value;
			});
		}

		/**
		 * Update Swift Performance option permanently
		 * @param string $key
		 * @param mixed $default
		 */
		public static function update_option($key, $value){
			global $swift_performance_options;
			$swift_performance_options[$key] = $value;
			update_option('swift_performance_options', $swift_performance_options);
		}

		/**
		 * Remove Swift Performance option permanently
		 * @param string $key
		 */
		public static function remove_option($key){
			global $swift_performance_options;
			unset($swift_performance_options[$key]);
			update_option('swift_performance_options', $swift_performance_options);
		}

		/**
		 * Reset plugin options to default
		 */
		public static function reset_options(){
			global $swift_performance_options;
			$options    = Luv_Framework_Fields::$instances['options']['swift_performance_options']->get_defaults();
			$options['purchase-key'] = Swift_Performance::get_option('purchase-key');

			update_option('swift_performance_options', $options);
			$swift_performance_options = $options;
		}

		/*
		 * Install/Uninstall Early Loader
		 */
		public static function early_loader($deactivate = false){
			$create = false;

			// Chack the current settings
			$create = Swift_Performance::check_option('early-load', 1);


			// Check Plugin Organizer
			if (!$create){
				$swift_performance_plugin_organizer = get_option('swift_performance_plugin_organizer', array());
				$rules	= (isset($swift_performance_plugin_organizer['rules']) ? array_filter($swift_performance_plugin_organizer['rules']) : array());
		            $rules	= apply_filters('swift-performance-plugin-rules', $rules);
		            $create	=  (!empty($rules));
			}


			// Use Loader
			if (!$deactivate && $create){
				// Create mu-plugins dir if not exists
				if (!file_exists(WPMU_PLUGIN_DIR)){
					@mkdir(WPMU_PLUGIN_DIR, 0777);
				}
				// Copy loader to mu-plugins
				if (file_exists(WPMU_PLUGIN_DIR)){
					$loader = file_get_contents(SWIFT_PERFORMANCE_DIR . 'modules/cache/loader.php');
					$loader = str_replace('%PLUGIN_NAME%', apply_filters('swift_performance_early_loader_plguin_name', SWIFT_PERFORMANCE_PLUGIN_NAME . ' early loader'), $loader);
					$loader = str_replace('%PLUGIN_DIR%', SWIFT_PERFORMANCE_DIR, $loader);
					$loader = str_replace('%PLUGIN_SLUG%', SWIFT_PERFORMANCE_PLUGIN_BASENAME, $loader);
					@file_put_contents(trailingslashit(WPMU_PLUGIN_DIR) . 'swift-performance-loader.php', $loader);
				}
			}
			else if (file_exists(trailingslashit(WPMU_PLUGIN_DIR) . 'swift-performance-loader.php')){
				@unlink(trailingslashit(WPMU_PLUGIN_DIR) . 'swift-performance-loader.php');
			}
		}

		/**
		 * Determine the server software
		 */
		public static function server_software(){
			return (preg_match('~(apache|litespeed|LNAMP|Shellrent)~i', $_SERVER['SERVER_SOFTWARE']) ? 'apache' : (preg_match('~(nginx|flywheel)~i', $_SERVER['SERVER_SOFTWARE']) ? 'nginx' : 'unknown'));
		}

		/**
		 * Call API
		 * @param string $endpoint
		 * @param array $body POST body
		 * @return string
		 */
		public static function api($endpoint, $body = array()){
			// Use API only if purchase key was set
			if (Swift_Performance::check_option('purchase-key', '')){
				return false;
			}

			// Check credit
			if (preg_match('~^(css/|script)~', $endpoint) && Swift_Performance::license_type() == 'lite'){
				$credit = Swift_Performance::get_credit();
				if ($credit['compute'] <= 0){
					Swift_Performance::log('Not enough credit for this action ('.$endpoint.')', 6);
					return false;
				}
			}

			Swift_Performance::log('Call API ('.$endpoint.')', 9);
			$response = wp_remote_post (
				SWIFT_PERFORMANCE_API_URL . $endpoint ,array(
						'timeout' => 300,
						'sslverify' => false,
						'user-agent' => 'SwiftPerformance',
						'headers' => array (
								'SWTE-PURCHASE-KEY' => trim(self::get_option('purchase-key')),
								'SWTE-SITE' => Swift_Performance::home_url()
						),
						'body' => $body
				)
			);

			if (is_wp_error($response)){
				Swift_Performance::log('Compute API ('.$endpoint.') request error: ' . $response->get_error_message(), 1);
				return false;
			}
			else{
				$decoded = json_decode($response['body'], true);

				if (isset($decoded['credits'])){
					Swift_Performance::update_credit($decoded['credits']);
				}

				if ($response['response']['code'] != 200){
					Swift_Performance::log('Compute API ('.$endpoint.') request error: HTTP error ' . $response['response']['code'], 1);
					if ($response['response']['code'] == 403){
						Swift_Performance::log('API connection error: IP address is blocked', 1);
						Swift_Performance::update_option('use-compute-api', 0);
						Swift_Performance::update_option('use-script-compute-api', 0);
					}
					return false;
				}

				if (empty($decoded)){
					Swift_Performance::log('Compute API ('.$endpoint.') body was empty', 6);
					return false;
				}

				return $decoded;
			}
		}

		/**
		 * Check Update info
		 * @return array|boolean
		 */
		public static function update_info(){
			if (Swift_Performance::license_type() == 'offline'){
				return;
			}

			$validate = wp_remote_get(add_query_arg(
				array(
					'purchase-key' => Swift_Performance::get_option('purchase-key'),
					'site' => Swift_Performance::home_url(),
			 		'beta' => Swift_Performance::get_option('enable-beta')
				),
				SWIFT_PERFORMANCE_API_URL . 'update/info/'
			));

			if (!is_wp_error($validate)){
	                  if ($validate['response']['code'] == 200){
	                        $details = json_decode($validate['body'], true);

					return $details;
	                  }
	                  else {
	                        return false;
	                  }
	            }
			return false;
		}

		/**
		 * Get API messages
		 */
		public static function api_messages(){
			if (Swift_Performance::license_type() == 'offline'){
				return;
			}

			$body = array(
				'version' => SWIFT_PERFORMANCE_VER
			);

			$response = wp_remote_post (
				SWIFT_PERFORMANCE_API_URL . 'message' ,array(
						'timeout' => 300,
						'sslverify' => false,
						'user-agent' => 'SwiftPerformance',
						'headers' => array (
								'SWTE-PURCHASE-KEY' => trim(self::get_option('purchase-key')),
								'SWTE-SITE' => Swift_Performance::home_url()
						),
						'body' => $body
				)
			);
			if (!is_wp_error($response)){
				$notices = json_decode($response['body'], true);

				if (!empty($notices['notices'])){
					foreach ((array)$notices['notices'] as $key => $notice) {
						$message = '<div class="swift-clear-cache-notice">'.$notice['text'].'</div><div class="swift-notice-buttonset"><a href="#" class="swift-btn swift-btn-gray" data-swift-dismiss-notice>' . esc_html__('Dismiss', 'swift-performance') . '</a></div>';
						Swift_Performance::add_notice($message, $notice['type'], 'API_MESSAGE_'.$key);
					}
				}
			}
		}

		/**
		 * Collect anonymized data
		 */
		public static function collect_anonymized_data(){
			if (Swift_Performance::check_option('collect-anonymized-data',1)){
				global $wpdb;
				$theme	= wp_get_theme();
				$posts	= $wpdb->get_results("SELECT post_type, COUNT(*) as count FROM {$wpdb->posts} GROUP BY post_type");
				$images	= $wpdb->get_results("SELECT status, COUNT(*) as count FROM " . SWIFT_PERFORMANCE_IMAGE_TABLE . " GROUP BY status");

				$body = array(
					'phpversion'	=> phpversion(),
					'server'		=> Swift_Performance::server_software(),
					'plugins'		=> json_encode(get_option('active_plugins')),
					'theme'		=> $theme->name,
					'posts'		=> json_encode($posts),
					'images'		=> json_encode($images)
				);

				$response = wp_remote_post (
					SWIFT_PERFORMANCE_API_URL . 'utils/stat' ,array(
							'timeout' => 300,
							'sslverify' => false,
							'user-agent' => 'SwiftPerformance',
							'body' => $body
					)
				);
			}
		}

		/**
		 * Check API connection
		 * @param boolean $debug
		 * @return boolean|array
		 */
		public static function check_api($debug = false, $purchase_key = ''){

			if (empty($purchase_key)){
				$license = Swift_Performance::license_type();

				if ($license == 'offline'){
					if ($debug){
						return array(
							'code'	=> 0,
							'response'	=> 'Purchase key is not set'
						);
					}
					return false;
				}

				$purchase_key = Swift_Performance::get_option('purchase-key');
			}

			$validate = wp_remote_get(SWIFT_PERFORMANCE_API_URL . 'user/validate/?purchase-key=' . $purchase_key . '&site=' . Swift_Performance::home_url(), array('timeout' => 300));
			if (is_wp_error($validate)){
				if ($debug){
					return array(
						'code'	=> 0,
						'response'	=> $validate->get_error_message()
					);
				}
				return false;
			}
			else {
				$decoded = json_decode($validate['body'], true);

				if (isset($decoded['credits'])){
					Swift_Performance::update_credit($decoded['credits']);
				}

	                  if ($validate['response']['code'] == 200){
	                       return true;
	                  }
	                  else {
					if ($debug){
						if (preg_match('~has banned your IP address \(([^\)]*)\)~', $validate['body'], $ip)){
							return array(
								'code'	=> $validate['response']['code'],
								'response'	=> __('Your server\'s IP has been banned due abusing our API server (too many invalid requests).', 'swift-performance') . sprintf(__('IP: %s', 'swift-performance'), $ip[1])
							);
				            }
				            else {
							return array(
								'code'	=> $validate['response']['code'],
								'response'	=> $decoded['message']
							);
				            }
					}
	                        return false;
	                  }
	            }
		}

		/**
	       * Get image id from url
	       * @param string $url
	       * @return int
	       */
	      public static function get_image_id($url){
			$images = wp_cache_get('swift_performace_image_ids');
			if ($images === false) {
				global $wpdb;
				$images = array();
				foreach ($wpdb->get_results("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file'", ARRAY_A) as $image){
					$images[$image['meta_value']] = $image['post_id'];
				}
				wp_cache_set( 'swift_performace_image_ids', $images );
			}

	            $upload_dir = wp_upload_dir();

	            $image = str_replace(trailingslashit(apply_filters('swift_performance_media_host', preg_replace('~https?:~','',$upload_dir['baseurl']))), '', preg_replace('~https?:~','', apply_filters('swift_performance_get_image_id_url', $url) ));
			$image_2 = preg_replace('~-(\d*)x(\d*)\.(jpe?g|gif|png)$~', '.$3', $image);
	            return (isset($images[$image]) ? $images[$image] : (isset($images[$image_2]) ? $images[$image_2] : false));
	      }

		/**
		 * Get post types
		 */
		public static function get_post_types($exclude = array()){
			global $wpdb;
			$exclude = array_merge((array)$exclude, array('revision', 'nav_menu_item','shop_order','shop_coupon'));
			$post_types = $wpdb->get_col("SELECT DISTINCT post_type FROM {$wpdb->posts} WHERE post_status = 'publish'");
			return array_diff($post_types, $exclude);
		}

		/**
		 * Get canonicalized path from URL
		 * @param string $address
		 * @return string
		 */
		public static function canonicalize($address){
		    $address = explode('/', $address);
		    $keys = array_keys($address, '..');

		    foreach($keys AS $keypos => $key){
		        array_splice($address, $key - ($keypos * 2 + 1), 2);
		    }

		    $address = implode('/', $address);
		    $address = str_replace('./', '', $address);

		    return $address;
		}

		/**
		 * Base64 decode array or string
		 * @param array|string $encoded
		 * @return mixed
		 */
		public static function base64_decode_deep($encoded){
			if (is_array($encoded)){
				return array_map('base64_decode', $encoded);
			}
			else {
				return base64_decode($encoded);
			}
		}

		/**
		 * Format size
		 * @param integer $bytes
		 * @return string
		 */
		public static function formatted_size($bytes = 0){
			if ($bytes > 1024*1024*500){
				return sprintf(esc_html__(' %s Gb', 'swift-performance'), number_format($bytes/1024/1024/1024,2));
			}
			else if ($bytes > 1024*500){
				return sprintf(esc_html__(' %s Mb', 'swift-performance'), number_format($bytes/1024/1024,2));
			}
			else {
				return sprintf(esc_html__(' %s Kb', 'swift-performance'), number_format($bytes/1024,2));
			}
		}

		/**
		 * Write log
		 * @param string $event
		 * @param loglevel $event
		 */
		public static function log($event, $loglevel = 9){
			$loglevels = array(
				'9' => 'Event',
				'8' => 'Notice',
				'6' => 'Warning',
				'1' => 'Error'
			);

			if (!defined('SWIFT_PERFORMANCE_MAX_LOG_ENTRIES')){
				define('SWIFT_PERFORMANCE_MAX_LOG_ENTRIES', 1000);
			}

			if (Swift_Performance::check_option('enable-logging', 1) && Swift_Performance::get_option('loglevel') >= $loglevel){
				$load = $memory_usage = $cpu = $memory = $stat = '';
				if (function_exists('sys_getloadavg')) {
					@$load		= sys_getloadavg();
				}
				if (function_exists('memory_get_usage')) {
					@$memory_usage	= memory_get_usage();
				}
				if (is_array($load) && isset($load[0])){
					$cpu	= ' CPU:' . number_format($load[0], 2) . '%';
				}
				if (!empty($memory_usage)){
					$memory	= ' Memory:' . number_format($memory_usage/1024/1024, 2) . 'Mb';
				}
				if (!empty($cpu) || !empty($memory)){
					$stat = ' (' . $memory . $cpu . ' )';
				}
				$file		= Swift_Performance::get_option('log-path') . date('Y-m-d') . '.txt';
			 	$entry	= get_date_from_gmt( date( 'Y-m-d H:i:s', time() ), get_option('date_format') . ' ' . 'H:i:s' ) . ' [' . $loglevels[$loglevel] . '] ' . wp_kses($event, array()) . $stat;
				$log		= @file_get_contents($file);
				$entries	= explode("\n", $log);
				$entries	= array_slice($entries, -SWIFT_PERFORMANCE_MAX_LOG_ENTRIES);
				$entries[]	= $entry;
				@file_put_contents($file, implode("\n", $entries));
			}
		}

		/**
		 * Add new entry to log buffer
		 * @param $message
		 */
		 public static function log_buffer($message){
			 $key = md5(strtolower(trim($message)));
			 Swift_Performance::get_instance()->log_buffer[$key] = $message;
		 }

		/**
		 * Admin panel template loader
		 */
		public static function panel_template(){
			$template_dir = apply_filters('swift_performance_template_dir', SWIFT_PERFORMANCE_DIR . (defined('SWIFT_PERFORMANCE_WHITELABEL') && SWIFT_PERFORMANCE_WHITELABEL ? 'templates/whitelabel/' : 'templates/'));
			include_once $template_dir . 'header.php';

			$subpage = (isset($_GET['subpage']) ? $_GET['subpage'] : 'dashboard');

			switch ($subpage) {
				case 'dasboard':
				default:
					include_once $template_dir . 'dashboard.php';
					break;
				case 'settings':
					return true;
				case 'image-optimizer':
					if (Swift_Performance::check_option('purchase-key','','!=')){
						include_once $template_dir . 'image-optimizer.php';
					}
					else {
						include_once $template_dir . 'set-purchase-key.php';
					}
					break;
				case 'db-optimizer':
					include_once $template_dir . 'db-optimizer.php';
					break;
				case 'plugin-organizer':
					include_once $template_dir . 'plugin-organizer.php';
					break;
				case 'debug-api':
					include_once $template_dir . 'debug-api.php';
					break;
			}

			include_once $template_dir . 'footer.php';

			return false;
		}

		/**
		 * Return available menu elements as an array
		 */
		public static function get_menu(){
			$elements = array(
				1 => array('slug' => 'dashboard', 'name' => __('Dashboard', 'swift-performance'), 'icon' => 'fas fa-clipboard'),
				3 => array('slug' => 'settings', 'name' =>  __('Settings', 'swift-performance'), 'icon' => 'fas fa-cogs'),
				4 => array('slug' => 'image-optimizer', 'name' =>  __('Image Optimizer', 'swift-performance'), 'icon' => 'fas fa-images'),
				5 => array('slug' => 'db-optimizer', 'name' =>  __('Database Optimizer', 'swift-performance'), 'icon' => 'fas fa-database'),
				6 => array('slug' => 'plugin-organizer', 'name' =>  __('Plugin Organizer', 'swift-performance'), 'icon' => 'fas fa-plug'),
			);

			return apply_filters('swift-performance-dashboard-menu', $elements);
		}

		/**
		 * Return actual cache sizes and cached files
		 * @return array
		 */
		 public static function cache_status(){
	 		$basedir = trailingslashit(Swift_Performance::get_option('cache-path')) . SWIFT_PERFORMANCE_CACHE_BASE_DIR;

	 		$files = array();
	 		$cache_size = Swift_Performance::cache_dir_size($basedir);

	 		if (Swift_Performance::check_option('caching-mode', array('disk_cache_rewrite', 'disk_cache_php'), 'IN')){
	 			foreach (apply_filters('swift_performance_enabled_hosts', array(parse_url(Swift_Performance::home_url(), PHP_URL_HOST))) as $host){
	 				$cache_dir = $basedir . $host;
	 				if (file_exists($cache_dir)){
	 					$Directory = new RecursiveDirectoryIterator($cache_dir);
	 					$Iterator = new RecursiveIteratorIterator($Directory);
	 					$Regex = new RegexIterator($Iterator, '#(@prefix/([_a-z0-9]+)/)?(desktop|mobile)/(unauthenticated|(authenticated/(a-z0-9+)))/((index|404)\.html|index\.xml|index\.json)$#i', RecursiveRegexIterator::GET_MATCH);
	 					foreach($Regex as $filename=>$file){
							$url			= parse_url(Swift_Performance::home_url(), PHP_URL_SCHEME) . '://' . preg_replace('~(desktop|mobile)/(authenticated|unauthenticated)(/[abcdef0-9]*)?/((index|404)\.(html|xml|json))~','',trim(str_replace($cache_dir, basename($cache_dir), $filename),'/'));
							$files[$url] 	= $url;
	 					}
	 				}
	 			}
	 		}
	 		else if (Swift_Performance::check_option('caching-mode', 'memcached_php')){
	 			$memcached = Swift_Performance_Cache::get_memcache_instance();
	 			$keys = $memcached->getAllKeys();
	 			foreach((array)$keys as $item) {
	 				if(preg_match('~^swift-performance~', $item)) {
	 					$raw_url = preg_replace('~^swift-performance_~', '', $item);
	 					$url = trailingslashit(Swift_Performance::home_url() . trim(preg_replace('~(desktop|mobile)/(authenticated|unauthenticated)(/[abcdef0-9]*)?/((index|404)\.(html|xml|json))~i','',str_replace(SWIFT_PERFORMANCE_CACHE_DIR, trailingslashit(basename(SWIFT_PERFORMANCE_CACHE_DIR)), $raw_url)),'/'));
	 					if (!preg_match('~\.gz$~', $item)){
	 					  	$files[$url] = $url;
	 				  	}
	 					$cached = Swift_Performance_Cache::memcached_get($raw_url);
	 					$cache_size  += strlen($cached['content']);
	 			    	}
	 			}
	 		}

	 		global $wpdb;

	 		// All known links
	 		$table_name = SWIFT_PERFORMANCE_TABLE_PREFIX . 'warmup';
	 		$all		= $wpdb->get_var("SELECT COUNT(DISTINCT TRIM(TRAILING '/' FROM url)) url FROM {$table_name}");
	 		$not_cached = $wpdb->get_var("SELECT COUNT(DISTINCT TRIM(TRAILING '/' FROM url)) url FROM {$table_name} WHERE type = ''");
	 		$cached_404 = $wpdb->get_var("SELECT COUNT(DISTINCT TRIM(TRAILING '/' FROM url)) url FROM {$table_name} WHERE type = '404'");
	 		$error	= $wpdb->get_var("SELECT COUNT(DISTINCT TRIM(TRAILING '/' FROM url)) url FROM {$table_name} WHERE type = 'error'");

	 		// Count cached AJAX objects
	 		$ajax_objects	= $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '%_transient_timeout_swift_performance_ajax_%'");
	 		$ajax_size		= $wpdb->get_var("SELECT SUM(LENGTH(option_value)) as size FROM {$wpdb->options} WHERE option_name LIKE '%_transient_swift_performance_ajax_%'");

	 		// Count cached dynamic pages
	 		$dynamic_pages	= $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '%_transient_swift_performance_dynamic_%'");
	 		$dynamic_size	= $wpdb->get_var("SELECT SUM(LENGTH(option_value)) as size FROM {$wpdb->options} WHERE option_name LIKE '%_transient_swift_performance_dynamic_%'");



	 		return array(
	 			'all'	=> (int)$all,
	 			'cached' => count($files),
	 			'not-cached' => $not_cached,
	 			'cached-404' => $cached_404,
	 			'error' => $error,
	 			'ajax_objects' => $ajax_objects,
	 			'ajax_size' => (int)$ajax_size,
	 			'dynamic_pages' => $dynamic_pages,
	 			'dynamic_size' => (int)$dynamic_size,
	 			'cache_size' => $cache_size,
	 			'files' => $files
	 		);
	 	}

		/**
		 * Count folder size recursively
		 * @param string $dir
		 * @return int
		 */
		public static function cache_dir_size($dir){
			$size = 0;
			$items = glob(rtrim($dir, '/').'/*', GLOB_NOSORT);
		    	foreach ((array)$items as $item) {
		      	$size += is_file($item) ? filesize($item) : Swift_Performance::cache_dir_size($item);
		    	}
		    	return $size;
		}

		/**
		 * Check is specified function disabled
		 * @param string $function_name
		 * @return boolean
		 */
		public static function is_function_disabled($function_name) {
			$disabled = explode(',', ini_get('disable_functions'));
			$result = (in_array($function_name, $disabled) || !function_exists($function_name));
			if ($result){
				Swift_Performance::log($function_name . ' is disabled on the server.', 6);
			}
			return $result;
		}

		/**
		 * Increase PHP timeout
		 * @param int $timeout
		 * @param string $hook
		 * @return int
		 */
		public static function set_time_limit($timeout, $hook){
			$default	= ini_get('max_execution_time');
			$timeout	= apply_filters('swift_performance_timeout_' . $hook, $timeout, $default);
			if (!Swift_Performance::is_function_disabled('set_time_limit') && !defined('SWIFT_PERFORMANCE_DISABLE_SET_TIME_LIMIT') && $timeout > $default){
				set_time_limit($timeout);
				return $timeout;
			}
			return $default;
		}

		/**
		 * Schedule Clear Cache
		 */
		public static function schedule_clear_cache(){

			// Schedule clear cache for short lifespan pages
			$short_lifespan_pages = (array)Swift_Performance::get_option('short-lifespan-pages');
			if (self::check_option('enable-caching', 1) && !empty($short_lifespan_pages)){
				if (!wp_next_scheduled( 'swift_performance_clear_short_lifespan')) {
					wp_schedule_event(time() + 3600, 'hourly', 'swift_performance_clear_short_lifespan');
				}
			}

			// Schedule clear cache if cache mode is timebased
			if (self::check_option('enable-caching', 1) && self::check_option('cache-expiry-mode', 'timebased')){
				if (!wp_next_scheduled( 'swift_performance_clear_expired')) {
					wp_schedule_event(time() + self::get_option('cache-expiry-time'), 'swift_performance_cache_expiry', 'swift_performance_clear_expired');
				}
			}

			// Schedule clear assets cache if proxy is enabled
			if (self::check_option('merge-scripts', 1) && self::check_option('proxy-3rd-party-assets', 1)){
				if (!wp_next_scheduled( 'swift_performance_clear_assets_proxy_cache')) {
					wp_schedule_event(time(), 'swift_performance_assets_cache_expiry', 'swift_performance_clear_assets_proxy_cache');
				}
			}
		}


		/**
		 * Create DB for the plugin
		 */
		public static function db_install(){
			global $wpdb;

			$table_name = SWIFT_PERFORMANCE_TABLE_PREFIX . 'warmup';
			$sql = "CREATE TABLE {$table_name} (
				id VARCHAR(32) NOT NULL,
				url VARCHAR(500) NOT NULL,
				priority INT(10) NOT NULL,
				menu_item TINYINT(1) NOT NULL,
				timestamp INT(11) NOT NULL,
				type VARCHAR(10) NOT NULL,
				PRIMARY KEY (id),
				KEY url (url),
				KEY priority (priority)
			);";

			$current_db_version = get_option(SWIFT_PERFORMANCE_TABLE_PREFIX . 'db_version');
			if (empty($current_db_version)){
				self::mysql_query($sql);
				update_option( SWIFT_PERFORMANCE_TABLE_PREFIX . "db_version", SWIFT_PERFORMANCE_DB_VER );
			}
			else if ($current_db_version !== SWIFT_PERFORMANCE_DB_VER){
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );

				update_option( SWIFT_PERFORMANCE_TABLE_PREFIX . "db_version", SWIFT_PERFORMANCE_DB_VER );
			}
		}

		/**
		 * Run MySQL query
		 * @param string $query
		 */
		public static function mysql_query($query){
			global $wpdb;
			if ( ! empty( $wpdb->dbh ) && $wpdb->use_mysqli ) {
				mysqli_query( $wpdb->dbh, $query );
			} elseif ( ! empty( $wpdb->dbh ) ) {
				mysql_query( $query, $wpdb->dbh );
			}
		}

		/**
		 * Get a unique hash of the current pageview
		 * @return string
		 */
		public static function get_unique_id(){
			$url_path = preg_replace('~\?(.*)$~', '',$_SERVER['REQUEST_URI']);
			return hash('crc32', $url_path) . '_' . hash('crc32', serialize($_GET)) .'_'. hash('crc32', serialize($_POST));
		}

		/**
		 * Sanitize given URL and return id for warmup table
		 * @param string $url
		 * @return string
		 */
		public static function get_warmup_id($url){
			// return dynamic cache id
			if (strpos($url, '?')){
				$url_path		= str_replace(Swift_Performance::home_url(), '/', preg_replace('~\?(.*)$~', '',$url));
				$query_string	= preg_replace('~([^\?]*)\?~', '',$url);
				parse_str($query_string, $get);
				return hash('crc32', $url_path) . '_' . hash('crc32', serialize($get)) .'_'. hash('crc32', serialize(array()));
			}

			return md5(trailingslashit(preg_replace('~(https?://)?(www\.)?~','', urldecode($url))));
		}

		/**
		 * Update plugin header for whitelabel
		 */
		public static function update_plugin_header(){
			// Update plugin header only in Pro version
			if (Swift_Performance::license_type() !== 'pro'){
				return;
			}

			if (Swift_Performance::is_feature_available('update_plugin_header')){
				Swift_Performance_Pro::update_plugin_header();
			}
		}

		/**
		 * Update plugin header after update
		 */
		public static function upgrader_process_complete($upgrader, $options){
			if($options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'])) {
				foreach($options['plugins'] as $plugin) {
					if($plugin == plugin_basename( __FILE__ )) {
						self::update_plugin_header();
					}
				}
			}
		}

		/**
		 * Check transient size and set transient
		 * @param string $transient
		 * @param string $value
		 * @param string $timeout
		 */
		public static function safe_set_transient($transient, $value, $timeout){
			global $wpdb;
			$max_allowed_packet		= $wpdb->get_row("SHOW VARIABLES LIKE 'max_allowed_packet'", ARRAY_A);
			$max_allowed_packet_size	= (isset($max_allowed_packet['Value']) && !empty($max_allowed_packet['Value']) ? $max_allowed_packet['Value']*0.9 : 1024*970);

			if (strlen(serialize($value)) < apply_filters('swift_performance_max_transient_size', min($max_allowed_packet_size, 5242880))){
				set_transient($transient, $value, $timeout);
			}
		}

		/**
		 * Clear all cron jobs with a particular hook
		 * @param string $hook
		 */
		public static function clear_hook( $hook ) {
		    $crons = _get_cron_array();
		    if ( empty( $crons ) ) {
		        return;
		    }
		    foreach( $crons as $timestamp => $cron ) {
		        if ( ! empty( $cron[$hook] ) )  {
		            unset( $crons[$timestamp][$hook] );
		        }

		        if ( empty( $crons[$timestamp] ) ) {
		            unset( $crons[$timestamp] );
		        }
		    }
		    _set_cron_array( $crons );
		}

		/**
		 * Send header if it is possible
		 * @param string $str
		 */
		public static function header($str){
			if (!headers_sent($filename, $linenum)) {
				header($str);
			}
			else {
				Swift_Performance::log("Can't send header: {$str} Headers already sent in {$filename} on line {$linenum}", 8);
			}
		}

		/**
		 * Return Swift Performance instance
		 */
		public static function get_instance(){
			return Swift_Performance::$instance;
		}

		/**
		 * Get license type
		 */
		public static function license_type(){
			if (Swift_Performance::check_option('purchase-key', '')){
				return Swift_Performance::default_license();
			}

			$license = get_option('swift-performance-license');

			if (empty($license)){
				$response = Swift_Performance::api('user/license');
				if (isset($response['license']) && !empty($response['license'])){
					$license_data = array('type' => $response['license']);
					if ($response['license'] == 'lite'){
						$response['credits'] = (array)$response['credits'];
					}
					update_option('swift-performance-license', $license_data);
				}
				else {
					Swift_Performance::log('Get license info failed' . json_encode($response), 1);
					return Swift_Performance::default_license();
				}
			}

			return (isset($license['type']) ? $license['type'] : Swift_Performance::default_license());
		}

		/**
		 * Get default license type
		 * @return string
		 */
		public static function default_license(){
			if (defined('SWIFT_PERFORMANCE_FILE')){
				return 'pro';
			}

			return 'offline';
		}

		/**
		 * Update remaining credit for Lite users
		 * @param array $credit array('compute'=> n, 'io' => n)
		 */
		public static function update_credit($credit){
			$license_data = get_option('swift-performance-license');
			foreach ($credit as $key => $value){
				$license_data['credit'][$key] = $value;
			}

			// Notifications
			if ($license_data['credit']['compute'] <= 300 && $license_data['credit']['compute'] > 0){
				Swift_Performance::credit_notification('compute', 'low');
			}
			elseif ($license_data['credit']['compute'] == 0){
				Swift_Performance::credit_notification('compute', 'zero');
			}

			if ($license_data['credit']['io'] <= 150 && $license_data['credit']['io'] > 0){
				Swift_Performance::credit_notification('io', 'low');
			}
			elseif ($license_data['credit']['io'] == 0){
				Swift_Performance::credit_notification('io', 'zero');
			}

			update_option('swift-performance-license', $license_data);
		}

		/**
		 * Send notification about low credit
		 * @param string $credit credit type
		 * @param string $notification notification type
		 */
		public static function credit_notification($credit, $notification){
			$transient_key = 'swift_performance_' . $credit.'_'.$notification;
			if (get_transient($transient_key) == 1){
				return;
			}

			set_transient($transient_key, 1, 15*DAY_IN_SECONDS);

			// Get admin e-mail and name
			$admin_email	= get_option('admin_email');
			$admin		= get_user_by('email', $admin_email);
			if ($admin !== false){
				$first_name	= get_user_meta($admin->ID, 'first_name', true);
				$email['name'] = (!empty($first_name) ? $first_name : $admin->get('user_nicename'));
			}
			else {
				$email['name'] = 'Administrator';
			}

			if ($credit == 'compute'){
				if ($notification == 'low'){
					$email['title']	= esc_html__('Page optimization credit is low', 'swift-performance');
					$email['content']	= sprintf(esc_html__('If you run out of credit, you will not be able to use API features, which can %simpact your scores%s%s', 'swift-performance'), '<strong style="color:#ed2f1d">', '<sup>*</sup>', '</strong>');
					$notice = esc_html__('Page optimization credit is low. If you run out of credit, you will not be able to use API features, which can impact your scores.', 'swift-performance');
				}
				else{
					$email['title']	= esc_html__('Page optimization credit has run out', 'swift-performance');
					$email['content']	= sprintf(esc_html__('Until your monthly quota will be reseted you will not be able to use API features, which can %simpact your scores%s%s', 'swift-performance'), '<strong style="color:#ed2f1d">', '<sup>*</sup>', '</strong>');
					$notice = esc_html__('Page optimization credit is has run out. Until your monthly quota will be reseted, you will not be able to use API features, which can impact your scores.', 'swift-performance');
				}

				$email['score_from'] = '<img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/a-grade.png" style="vertical-align:middle;">';
				$email['score_to'] = '<img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/c-grade.png" style="vertical-align:middle;margin-left:15px;">';
			}

			if ($credit == 'io'){
				global $wpdb;
				$unoptimized = $wpdb->get_var("SELECT COUNT(*) FROM " . SWIFT_PERFORMANCE_IMAGE_TABLE . " WHERE status IN (0,2,3)");


				if ($notification == 'low'){
					$email['title']	= esc_html__('Image optimization credit is low', 'swift-performance');
					if ($unoptimized > 0){
						$email['content']	.= sprintf(esc_html__('If you run out of credit, you will not be able to use Image Optimizer. You have %s%d%s images which are not optimzed yet. Upgrade to Swift Performance Pro to %sget unlimited Image Optimizer!%s', 'swift-performance'), '<strong style="color:#ed2f1d">', $unoptimized, '</strong>', '<strong style="color:#ed2f1d">', '</strong>');
						$notice = sprintf(esc_html__('Image optimization credit is low. If you run out of credit, you will not be able to use Image Optimizer. %sYou have %s%d images which are not optimzed yet%s. Upgrade to Swift Performance Pro to get unlimited Image Optimizer!', 'swift-performance'), '<br>', '<strong>', $unoptimized, '</strong>');
					}
					else {
						$email['content']	= sprintf(esc_html__('If you run out of credit, you will not be able to use Image Optimizer, which can %simpact your scores%s%s', 'swift-performance'), '<strong style="color:#ed2f1d">', '<sup>*</sup>', '</strong>');
						$notice = esc_html__('Image optimization credit is low. If you run out of credit, you will not be able to use Image Optimizer, which can impact your scores.', 'swift-performance');
					}
				}
				else {
					$email['title']	= esc_html__('Image optimization credit has run out', 'swift-performance');
					if ($unoptimized > 0){
						$email['content']	.= sprintf(esc_html__('Until your monthly quota will be reseted, you will not be able to use Image Optimizer. You have %s%d%s images which are not optimzed yet. Upgrade to Swift Performance Pro to %sget unlimited Image Optimizer!%s', 'swift-performance'), '<strong style="color:#ed2f1d">', $unoptimized, '</strong>', '<strong style="color:#ed2f1d">', '</strong>');
						$notice = sprintf(esc_html__('Image optimization credit has run out. Until your monthly quota will be reseted, you will not be able to use Image Optimizer. %sYou have %s%d images which are not optimzed yet%s. Upgrade to Swift Performance Pro to get unlimited Image Optimizer!', 'swift-performance'), '<br>', '<strong>', $unoptimized, '</strong>');
					}
					else {
						$email['content']	= sprintf(esc_html__('Until your monthly quota will be reseted, you will not be able to use Image Optimizer, which can %simpact your scores%s%s', 'swift-performance'), '<strong style="color:#ed2f1d">', '<sup>*</sup>', '</strong>');
						$notice = esc_html__('Image optimization credit has run out. Until your monthly quota will be reseted, you will not be able to use Image Optimizer, which can impact your scores', 'swift-performance');

					}
				}

				$email['score_from'] = '<img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/c-grade.png" style="vertical-align:middle;">';
				$email['score_to'] = '<img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/a-grade.png" style="vertical-align:middle;">';
			}

			// Add admin notice
			$notice = '<div class="swift-clear-cache-notice">' . $notice . '</div><div class="swift-notice-buttonset"><a target="_blank" class="swift-btn swift-btn-green" href="' . Swift_Performance::get_upgrade_url('admin-notice-' . $credit . '-' . $notification) . '">Upgrade Pro Now</a><a href="#" class="swift-btn swift-btn-gray" data-swift-dismiss-notice>' . esc_html__('Dismiss', 'swift-performance') .'</a></div>';
			Swift_Performance::add_notice($notice, 'warning', 'credit-notification');

			// Send e-mail notification
			ob_start();
			include apply_filters('swift_performance_template_dir', SWIFT_PERFORMANCE_DIR . 'templates/') . 'e-mail/credit-notification.php';
			$content = ob_get_clean();

			add_filter('wp_mail_content_type', $that = function(){
				return 'text/html';
			});

			wp_mail($admin_email, $email['title'], $content);
			remove_filter('wp_mail_content_type', $that);
		}

		/**
		 * Get remaining credit for Lite users
		 * @return array array('compute' => n, 'io' => n)
		 */
		public static function get_credit(){
			if (Swift_Performance::license_type() !== 'lite'){
				return false;
			}

			$license_data = get_option('swift-performance-license');
			return $license_data['credit'];
		}

		/**
		 * Get upgrade URL and add query stirngs
		 */
		public static function get_upgrade_url($source = ''){
			$query = array(
				'key'		=> md5(Swift_Performance::get_option('purchase-key')),
				'source'	=> $source,
				'ref'		=> apply_filters('swte_affiliate_id', (defined('SWTE_AFFILIATE_ID') ? SWTE_AFFILIATE_ID : ''))
			);
			return add_query_arg(array_filter($query), 'https://swiftperformance.io/upgrade-pro/');
		}

		/**
		 * Padding string or strings in array recursively with given value
		 * @param string str
		 * @param string padding
		 * @return mixed
		 */
		public static function padding_str($str = '', $padding = ''){
			if (is_array($str)){
				foreach ($str as $key => $value){
					$str[$key] = Swift_Performance::padding_str($value, $padding);
				}
			}
			else {
				$str = $padding . $str . $padding;
			}

			return $str;
		}

		/**
	       * Close connection early and keep executing in background
	       */
	      public static function flush_connection(){
	            //Headers
	            header("Content-Type: image/gif");
	            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	            header("Cache-Control: post-check=0, pre-check=0", false);
	            header("Pragma: no-cache");

	            ob_start();
	            //1x1 Transparent Gif
	            echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
	            //Send full content and keep executeing
	            header('Connection: close');
	            header('Content-Length: '.ob_get_length());
	            @ob_end_flush();
	            @ob_flush();
	            @flush();
	            if (function_exists('fastcgi_finish_request')){
	                  fastcgi_finish_request();
	            }
	      }

		/**
		 * Check if pro feature is available
		 */
		 public static function is_feature_available($feature){
			 //Let Swift Performance Extra to load pro features
			 if (!class_exists('Swift_Performance_Pro')){
				 $instance = Swift_Performance::get_instance();
				 do_action('swift_performance_load_pro_features', $instance);
			 }

			 return (Swift_Performance::is_option_set('purchase-key') && class_exists('Swift_Performance_Pro') && method_exists('Swift_Performance_Pro', $feature));
		 }
	}
}

add_action('plugins_loaded', function(){
	if (defined('SWIFT_PERFORMANCE_PRO')){
		if (!function_exists('deactivate_plugins')){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		Swift_Performance::write_rewrite_rules();
		deactivate_plugins(plugin_basename(__FILE__), true);
            wp_schedule_single_event(time(), 'swift_performance_early_loader');
	}
});

new Swift_Performance();
?>
