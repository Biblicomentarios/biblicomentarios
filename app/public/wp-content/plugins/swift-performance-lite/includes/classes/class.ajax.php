<?php

class Swift_Performance_Ajax {

      /**
       * Init ajax object
       */
      public function __construct(){
            // Ajax handlers
            add_action('wp_ajax_swift_performance_clear_cache', array($this, 'ajax_clear_all_cache'));
            add_action('wp_ajax_swift_performance_custom_purge', array($this, 'ajax_custom_purge'));
            add_action('wp_ajax_swift_performance_clear_assets_cache', array($this, 'ajax_clear_assets_cache'));
            add_action('wp_ajax_swift_performance_update_prebuild_priority', array($this, 'ajax_update_prebuild_priority'));
            add_action('wp_ajax_swift_performance_prebuild_cache', array($this, 'ajax_prebuild_cache'));
            add_action('wp_ajax_swift_performance_stop_prebuild_cache', array($this, 'ajax_stop_prebuild_cache'));
            add_action('wp_ajax_swift_performance_single_prebuild', array($this, 'ajax_single_prebuild'));
            add_action('wp_ajax_swift_performance_single_clear_cache', array($this, 'ajax_single_clear_cache'));
            add_action('wp_ajax_swift_performance_single_dynamic_clear_cache', array($this, 'ajax_single_dynamic_clear_cache'));
            add_action('wp_ajax_swift_performance_single_ajax_clear_cache', array($this, 'ajax_single_ajax_clear_cache'));
            add_action('wp_ajax_swift_performance_remove_warmup_url', array($this, 'ajax_remove_warmup_url'));
            add_action('wp_ajax_swift_performance_add_warmup_url', array($this, 'ajax_add_warmup_url'));
            add_action('wp_ajax_swift_performance_reset_warmup', array($this, 'ajax_reset_warmup'));
            add_action('wp_ajax_swift_performance_show_rewrites', array($this, 'ajax_show_rewrites'));
            add_action('wp_ajax_swift_performance_change_thread_limit', array($this, 'ajax_change_thread_limit'));
            add_action('wp_ajax_swift_performance_cache_status', array($this, 'ajax_cache_status'));
            add_action('wp_ajax_swift_performance_show_log', array($this, 'ajax_show_log'));
            add_action('wp_ajax_swift_performance_clear_logs', array($this, 'ajax_clear_logs'));
            add_action('wp_ajax_swift_performance_toggle_dev_mode', array($this, 'ajax_toggle_dev_mode'));
            add_action('wp_ajax_swift_performance_bypass_cron', array($this, 'ajax_bypass_cron'));
            add_action('wp_ajax_swift_performance_preview', array($this, 'ajax_preview'));
            add_action('wp_ajax_swift_performance_dismiss_pointer', array($this, 'ajax_dismiss_pointer'));
            add_action('wp_ajax_swift_performance_dismiss_notice', array($this, 'ajax_dismiss_notice'));
            add_action('wp_ajax_swift_performance_debug_api', array($this, 'ajax_debug_api'));

            add_action('wp_ajax_swift_performance_ajaxify', array($this, 'ajaxify'));
            add_action('wp_ajax_nopriv_swift_performance_ajaxify', array($this, 'ajaxify'));

            add_action('wp_ajax_swift_performance_send_license_key', array($this, 'send_license_key'));
            add_action('wp_ajax_swift_performance_activate', array($this, 'activate'));
      }


      /**
	 * Clear all cache ajax callback
	 */
	public function ajax_clear_all_cache(){
		// Check user and nonce
		$this->ajax_auth();

            $type = (isset($_REQUEST['type']) ? $_REQUEST['type'] : 'all');

            switch ($type) {
                  case 'ajax':
                        Swift_Performance_Cache::clear_transients('ajax');
                        break;
                  case 'dynamic':
                        Swift_Performance_Cache::clear_transients('dynamic');
                        break;
                  case 'homepage':
                        Swift_Performance::log('Ajax action: (clear homepage cache)', 9);
                        Swift_Performance_Cache::clear_permalink_cache(site_url());
                        break;
                  case 'all':
                  default:
                        Swift_Performance::log('Ajax action: (clear all cache)', 9);
                        Swift_Performance_Cache::clear_all_cache();
                        break;
            }


		wp_send_json(
			array(
				'type' => 'success',
				'text' => __('Cache cleared', 'swift-performance')
			)
		);
	}

      /**
	 * Custom purge callback
	 */
	public function ajax_custom_purge(){
		// Check user and nonce
		$this->ajax_auth();

            $urls = array();
            if (isset($_REQUEST['rule']) && !empty($_REQUEST['rule'])){
                  $urls = Swift_Performance_Cache::custom_purge($_REQUEST['rule']);
            }

            $count = count($urls);

		wp_send_json(
			array(
				'type' => 'success',
				'text' => sprintf(_n('Custom Purge done. %d url was purged', 'Custom Purge done. %d urls were purged', $count ,'swift-performance'), $count)
			)
		);
	}

	/**
	 * Clear assets cache ajax callback
	 */
	public function ajax_clear_assets_cache(){
		// Check user and nonce
		$this->ajax_auth();

		Swift_Performance::log('Ajax action: (clear assets cache)', 9);

		Swift_Performance_Asset_Manager::clear_assets_cache();
		wp_send_json(
			array(
				'type' => 'success',
				'text' => __('Assets cache cleared', 'swift-performance')
			)
		);
	}

	/**
	 * Change prebuild priority ajax callback
	 */
	public function ajax_update_prebuild_priority(){
		// Check user and nonce
		$this->ajax_auth();

		$table_name = SWIFT_PERFORMANCE_TABLE_PREFIX . 'warmup';
		parse_str($_REQUEST['data'], $data);

		global $wpdb;
		foreach ($data['priorities'] as $key => $value) {
                  Swift_Performance::log('Update prebuild priority: ' . esc_html($key) . '|' . esc_html($value), 9);
			$wpdb->update($table_name, array('priority' => (int)$value), array('id' => esc_sql($key)));
		}

		wp_send_json(
			array(
				'type' => 'success'
			)
		);
	}

	/**
	 * Single prebuild ajax callback
	 */
	public function ajax_single_prebuild(){
		// Check user and nonce
		$this->ajax_auth();

		if (isset($_REQUEST['url'])){
                  // Set prebuild speed to avoid using ASYNC prebuild
                  Swift_Performance::set_option('prebuild-speed', 0);

			Swift_Performance::prebuild_cache_hit($_REQUEST['url']);
                  do_action('swift_performance_prebuild_cache_hit', $_REQUEST['url']);
		}

		$time = Swift_Performance_Cache::get_cache_time($_REQUEST['url']);

		wp_send_json(
			array(
				'type' => 'success',
				'date' => (empty($time) ? '-' : get_date_from_gmt( date( 'Y-m-d H:i:s', $time ), get_option('date_format') . ' ' .get_option('time_format') )),
				'status' => Swift_Performance_Cache::get_cache_type($_REQUEST['url'])
			)
		);
	}

	/**
	 * Single clear cache ajax callback
	 */
	public function ajax_single_clear_cache(){
		// Check user and nonce
		$this->ajax_auth();

		if (isset($_REQUEST['url'])){
			Swift_Performance_Cache::clear_permalink_cache($_REQUEST['url']);
		}

		$time = Swift_Performance_Cache::get_cache_time($_REQUEST['url']);

		wp_send_json(
			array(
				'type' => 'success',
				'date' => (empty($time) ? '-' : date_i18n('Y-m-d H:i:s', $time)),
				'status' => Swift_Performance_Cache::get_cache_type($_REQUEST['url'])
			)
		);
	}

      /**
	 * Single clear dynamic cache ajax callback
	 */
	public function ajax_single_dynamic_clear_cache(){
		// Check user and nonce
		$this->ajax_auth();

		if (isset($_REQUEST['url'])){
			delete_transient('swift_performance_dynamic_' . $_REQUEST['url']);
		}

		wp_send_json(
			array(
				'type' => 'success',
                        'message' => __('Dynamic cache page has been cleared', 'swift_performance')
			)
		);
	}

      /**
	 * Single clear AJAX cache ajax callback
	 */
	public function ajax_single_ajax_clear_cache(){
		// Check user and nonce
		$this->ajax_auth();

		if (isset($_REQUEST['url'])){
			delete_transient('swift_performance_ajax_' . $_REQUEST['url']);
		}

		wp_send_json(
			array(
				'type' => 'success',
                        'message' => __('Ajax cache page has been cleared', 'swift_performance')
			)
		);
	}

	/**
	 * Remove warmup URL ajax callback
	 */
	public function ajax_remove_warmup_url(){
		// Check user and nonce
		$this->ajax_auth();

		if (isset($_REQUEST['url'])){
                  Swift_Performance::set_option('automated_prebuild_cache', 0);

                  // Clear from cache
                  Swift_Performance_Cache::clear_permalink_cache($_REQUEST['url']);

                  // Remove from warmup table
			global $wpdb;
			$table_name = SWIFT_PERFORMANCE_TABLE_PREFIX . 'warmup';
			$wpdb->delete($table_name, array('url' => $_REQUEST['url']));
                  Swift_Performance::log('Remove warmup URL: ' . esc_html($_REQUEST['url']), 9);
		}

		$time = Swift_Performance_Cache::get_cache_time($_REQUEST['url']);

		wp_send_json(
			array(
				'type' => 'success',
				'date' => (empty($time) ? '-' : date_i18n('Y-m-d H:i:s', $time)),
				'status' => Swift_Performance_Cache::get_cache_type($_REQUEST['url'])
			)
		);
	}

	/**
	 * Add warmup URL ajax callback
	 */
	public function ajax_add_warmup_url(){
		global $wpdb;

		// Check user and nonce
		$this->ajax_auth();

		if (!isset($_REQUEST['url']) || empty($_REQUEST['url'])){
			wp_send_json(
				array(
					'type' => 'critical',
					'text' => __('The given URL was empty.', 'swift-performance')
				)
			);
			die;
		}

		$url 		= $_REQUEST['url'];
		$priority	= (isset($_REQUEST['priority']) ? (int)$_REQUEST['priority'] : Swift_Performance::get_default_warmup_priority());

		$host = parse_url($url, PHP_URL_HOST);
		if (empty($host)){
			$url = home_url($url);
		}

		if (parse_url($url, PHP_URL_HOST) !== parse_url(home_url(), PHP_URL_HOST)){
			wp_send_json(
				array(
					'type' => 'critical',
					'text' => __('The given URL was invalid (internal links only).', 'swift-performance')
				)
			);
			die;
		}

		$table_name = SWIFT_PERFORMANCE_TABLE_PREFIX . 'warmup';
		$wpdb->query($wpdb->prepare("INSERT IGNORE INTO {$table_name} (id, url, priority, menu_item) VALUES (%s, %s, %d, 0)", Swift_Performance::get_warmup_id($url), $url, $priority ));

            Swift_Performance::log('Add warmup URL: ' . esc_html($url), 9);

		wp_send_json(
			array(
				'type' => 'success',
			)
		);
	}

	/**
	 * Single clear cache ajax callback
	 */
	public function ajax_reset_warmup(){
		// Check user and nonce
		$this->ajax_auth();

		global $wpdb;
            // Drop and re-create warmup table
            $wpdb->query('DROP TABLE IF EXISTS ' . SWIFT_PERFORMANCE_TABLE_PREFIX . 'warmup');
            delete_option(SWIFT_PERFORMANCE_TABLE_PREFIX . 'db_version');
            delete_transient('swift_performance_initial_prebuild_links');
            Swift_Performance::db_install();
            Swift_Performance_Cache::clear_all_cache();
            Swift_Performance::get_prebuild_urls();

            Swift_Performance::log('Reset warmup table', 9);

		wp_send_json(
			array(
				'type' => 'success',
                        'text' => __('Warmup Table has been reset successfully', 'swift-performance')
			)
		);
	}

	/**
	 * Prebuild cache ajax callback
	 */
	public function ajax_prebuild_cache(){
		// Check user and nonce
		$this->ajax_auth();

		Swift_Performance::log('Ajax action: (prebuild cache)', 9);

            Swift_Performance::stop_prebuild();
		wp_schedule_single_event(time(), 'swift_performance_prebuild_cache');
		wp_send_json(
			array(
				'type' => 'success',
				'text' => __('Prebuilding cache is in progress', 'swift-performance')
			)
		);
	}

      /**
	 * Stop prebuild cache ajax callback
	 */
	public function ajax_stop_prebuild_cache(){
		// Check user and nonce
		$this->ajax_auth();

		Swift_Performance::log('Ajax action: (stop prebuild cache)', 9);
		Swift_Performance::stop_prebuild();
		wp_send_json(
			array(
				'type' => 'success',
				'text' => __('Prebuilding cache stopped by user', 'swift-performance')
			)
		);
	}

	/**
	 * Show the rewrite rules
	 */
	public function ajax_show_rewrites(){
		// Check user and nonce
		$this->ajax_auth();

		switch (Swift_Performance::server_software()){
			case 'apache':
				$htaccess = Swift_Performance::get_home_path() . '.htaccess';

		            if (file_exists($htaccess) && is_writable($htaccess)){
		               	$message    = __('It seems that your htaccess is writable, you don\'t need to add rules manually.', 'swift-performance');
                              $type       = 'success';
		            }
				else {
					$message = __('It seems that your htaccess is NOT writable, you need to add rules manually.', 'swift-performance');
                              $type       = 'warning';
				}
				break;
			case 'nginx':
				$message = __('You need to add rewrite rules manually to your Nginx config file.', 'swift-performance');
                        $type       = 'warning';
				break;
			default:
				$message = __('Caching with rewrites currently available on Apache and Nginx only.', 'swift-performance');
                        $type       = 'warning';


		}

		wp_send_json(
			array(
				'title'	=> esc_html__('Rewrite Rules', 'swift-performance'),
				'type'	=> $type,
				'text'	=> $message,
				'rewrites'	=> get_option('swift_performance_rewrites'),
			)
		);
	}

	/**
	 * Show the active threads
	 */
	public function ajax_change_thread_limit(){
		// Check user and nonce
		$this->ajax_auth();

		$max_threads = Swift_Performance::get_option('max-threads');
		$max_threads += (int)$_POST['limit'];
		Swift_Performance::update_option('max-threads', max(0, $max_threads));

            Swift_Performance::log('Change thread limit to ' . $max_threads, 9);
		die;
	}

	/**
	 * Show the cache status
	 */
	public function ajax_cache_status(){
            global $wpdb;

		// Check user and nonce
		$this->ajax_auth();

		$result = Swift_Performance::cache_status();

		// Prebuild status
		$prebuild_status = '';
		$prebuild_hit = get_transient('swift_performance_prebuild_cache_hit');
            $prebuild_pid = get_transient('swift_performance_prebuild_cache_pid');

		if (!empty($prebuild_hit)){
			$prebuild_status = sprintf(esc_html__('Prebuild cache in progress: %s'), urldecode($prebuild_hit)) . "\n";
		}
            else if (!empty($prebuild_pid) && $prebuild_pid != 'stop'){
                  $prebuild_status = esc_html__('Prebuild cache is idle') . "\n";
            }

            // Threads
		$threads = Swift_Performance::get_thread_array();

            // Single statuses
            $status_list = array();
            if (!empty($_POST['ids'])){
                  // Prepare ids
                  $__in = '';
                  foreach ((array)$_POST['ids'] as $id){
                        $__in .= "'" . esc_sql($id) . "',";
                  }
                  $__in = trim($__in, ',');

                  // Prepare status list
                  $status_list = $wpdb->get_results("SELECT id, type, timestamp FROM " . SWIFT_PERFORMANCE_TABLE_PREFIX . "warmup WHERE id IN ({$__in})", ARRAY_A);

                  $status_list = array_map(function($item){
                        $item['date'] = ($item['timestamp'] > 0 ? get_date_from_gmt(date('Y-m-d H:i:s', $item['timestamp']), get_option('date_format') . ' ' .get_option('time_format')) : '-');
                        return $item;
                  }, $status_list);
            }

            // Single dynamic statuses
            if (!empty($_POST['dids'])){
                  foreach ($_POST['dids'] as $did) {
                        $transient = get_transient('swift_performance_dynamic_' . $did);
                        if (!empty($transient)){
                              $status_list[] = array(
                                    'id'        => $did,
                                    'type'      => 'html',
                                    'timestamp' => $transient['time'],
                                    'date'      => ($transient['time'] > 0 ? get_date_from_gmt(date('Y-m-d H:i:s', $transient['time']), get_option('date_format') . ' ' .get_option('time_format')) : '-')
                              );
                        }
                        else {
                              $status_list[] = array(
                                    'id'        => $did,
                                    'type'      => 'not-cached',
                                    'timestamp' => 0,
                                    'date'      => '-'
                              );
                        }
                  }
            }

            $dynamic_cache_status_table_html = '';
            if (Swift_Performance::check_option('dynamic-caching', 1)){
                  include_once SWIFT_PERFORMANCE_DIR . 'includes/cache-status/dynamic-cache-status.php';

                  $dynamic_cache_status_table = new Swift_Performance_Dynamic_Cache_Status_Table();
                  $dynamic_cache_status_table->prepare_items();
                  ob_start();
                  $dynamic_cache_status_table->display();
                  $dynamic_cache_status_table_html = ob_get_clean();
            }

            $ajax_cache_status_table_html = '';
            if ($result['ajax_objects'] > 0){
                  include_once SWIFT_PERFORMANCE_DIR . 'includes/cache-status/ajax-cache-status.php';

                  $ajax_cache_status_table = new Swift_Performance_Ajax_Cache_Status_Table();
                  $ajax_cache_status_table->prepare_items();

                  ob_start();
                  $ajax_cache_status_table->display();
                  $ajax_cache_status_table_html = ob_get_clean();
            }

		wp_send_json(
			array(
				'title'		=> esc_html__('Cache status', 'swift-performance'),
				'type'		=> 'info',
				'prebuild'		=> $prebuild_status,
                        'cache_status'    => array(
                              array('value' => round($result['cached']/max(1,$result['all'])*100), 'label' => esc_html__('Cached', 'swift-performance')),
                              array('value' => round($result['not-cached']/max(1,$result['all'])*100), 'label' => esc_html__('Not Cached', 'swift-performance')),
                              array('value' => round($result['cached-404']/max(1,$result['all'])*100), 'label' => esc_html__('Cached 404', 'swift-performance')),
                              array('value' => round($result['error']/max(1,$result['all'])*100), 'label' => esc_html__('Not Cacheable', 'swift-performance')),
                        ),
				'all_pages'		=> $result['all'],
                        'ajax_objects'	=> $result['ajax_objects'],
                        'ajax_size'	      => Swift_Performance::formatted_size($result['ajax_size']),
                        'ajax_table'      => $ajax_cache_status_table_html,
                        'dynamic_pages'	=> $result['dynamic_pages'],
                        'dynamic_size'	=> Swift_Performance::formatted_size($result['dynamic_size']),
                        'dynamic_table'   => $dynamic_cache_status_table_html,
				'cached_pages'	=> $result['cached'],
				'size'		=> Swift_Performance::formatted_size($result['cache_size']),
				'threads' 		=> count($threads) . '/' . (Swift_Performance::check_option('limit-threads', 1) ? Swift_Performance::get_option('max-threads') : '&#8734;'),
                        'status_list'     => $status_list,
                        'credit'         => Swift_Performance::get_credit()
			)
		);
	}

	/**
	 * Show the latest log
	 */
	public function ajax_show_log(){
		// Check user and nonce
		$this->ajax_auth();

		if (file_exists(Swift_Performance::get_option('log-path') . date('Y-m-d') . '.txt')){
			$log = explode("\n", file_get_contents(Swift_Performance::get_option('log-path') . date('Y-m-d') . '.txt'));
			$log = array_reverse($log);
			$log = implode("\n", $log);
		}
		else {
			$log = __('Log is empty', 'swift-performance');
		}

		wp_send_json(
			array(
				'title'	=> sprintf(esc_html__('Log - %s', 'swift-performance'), date_i18n(get_option( 'date_format' ))),
				'type'	=> 'info',
				'status'	=> $log
			)
		);
	}

	/**
	 * Show the latest log
	 */
	public function ajax_clear_logs(){
		// Check user and nonce
		$this->ajax_auth();

		$logpath = Swift_Performance::get_option('log-path');
		if (file_exists($logpath)){
			$files = array_diff(scandir($logpath), array('.','..'));
			foreach ($files as $file) {
				@unlink(trailingslashit($logpath) . $file);
			}
                  Swift_Performance::log('Logs cleared', 9);
		}
		else {
			$log = __('Log is empty', 'swift-performance');
		}

		die;
	}

      /**
       * Toggle developer mode
       */
      public function ajax_toggle_dev_mode(){
            // Check user and nonce
		$this->ajax_auth();
            if (Swift_Performance::is_developer_mode_active()){
                  delete_option('swift-performance-developer-mode');
                  $message = __('Developer mode deactivated', 'swift-performance');
            }
            else {
                  Swift_Performance_Cache::clear_all_cache();
                  update_option('swift-performance-developer-mode', time() + (3 * 3600));
                  $message = __('Developer mode is active. Caching will bypassed.', 'swift-performance');
            }

            wp_send_json(
			array(
				'type' => 'success',
				'text' => $message
			)
		);
      }

      /**
      * Check user and nonce
      */
      public function ajax_auth($role = 'manage_options'){
            if (!current_user_can($role) || !isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'swift-performance-ajax-nonce')){
                  wp_send_json(
                        array(
                              'type' => 'critical',
                              'text' => __('Your session has expired. Please refresh the page and try again.', 'swift-performance')
                        )
                  );
                  die;
            }
      }

      /**
       * Bypass default WP-cron
       */
      public function ajax_bypass_cron(){
            Swift_Performance::set_time_limit(3600, 'ajax_bypass_cron');

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
            ob_end_flush();
            ob_flush();
            flush();

           	$cron_jobs = get_option( 'cron' );
           	foreach ((array)$cron_jobs as $timestamp => $jobs) {
                  if ($timestamp <= time() && !empty($jobs)){
                        foreach ((array)$jobs as $hook => $list){
                              if (preg_match('~swift_performance~', $hook)){
                                    foreach ($list as $item){
                                          if ($item['schedule'] === false){
                                                do_action($hook, $item['args']);
                                                wp_clear_scheduled_hook( $hook, $item['args'] );
                                          }
                                    }
                              }
                        }
                  }
            }

      }

      /**
       * Show preview
       */
      public function ajax_preview(){
            $this->ajax_auth();

            $options = get_option('swift_performance_options');
            foreach ($options as $key => $value) {
                  $value = (isset($_POST['_luv_' . $key]) ? stripslashes_deep($_POST['_luv_' . $key]) : '');
                  $options[$key] = $value;

            }
            update_option('swift_performance_preview', $options);
            wp_send_json(
                  array(
                        'url' => add_query_arg('swift-preview','1',home_url())
                  )
            );
      }

      /**
       * Dismiss tooltip
       */
      public function ajax_dismiss_pointer(){
            $this->ajax_auth();

            $pointers   = (array)get_user_meta(get_current_user_id(), 'swift_pointers', true);
            $pointers[$_POST['id']] = $_POST['id'];
            update_user_meta(get_current_user_id(), 'swift_pointers', $pointers);

      }

      /**
       * Dismiss notice
       */
      public function ajax_dismiss_notice(){
            $this->ajax_auth();

            $messages = (array)apply_filters('swift_performance_admin_notices', get_option('swift_performance_messages', array()));
		unset($messages[$_POST['id']]);
		update_option('swift_performance_messages', $messages);

      }

      /**
       * Debug API
       */
      public function ajax_debug_api(){
            $this->ajax_auth();

            $message = Swift_Performance::check_api(true);

            wp_send_json(
			array(
				'title'	=> esc_html__('Debug API Connection', 'swift-performance'),
				'type'	=> ($message === true ? 'success' : 'error'),
				'status'	=> ($message === true ? __('API connection is working', 'swift-performance') : $message['response'])
			)
		);
      }

      /**
       * Generate lite license key
       */

      public function send_license_key(){
            $this->ajax_auth();

            $data = array(
                  'site'      => Swift_Performance::home_url(),
                  'name'      => $_POST['name'],
                  'email'     => urldecode($_POST['email'])
            );

            if (isset($_POST['anonym-stats']) && $_POST['anonym-stats'] == 'accepted'){
                  Swift_Performance::update_option('collect-anonymized-data', 1);
            }

            $response = wp_remote_post(SWIFT_PERFORMANCE_API_URL . 'user/register_lite', array('body' => $data));

            if (is_wp_error($response)){
                  wp_send_json(array(
                        'result'    => 'error',
                        'message'   => $response->get_error_message()
                  ));
            }
            else if ($response['response']['code'] == 200){
                  wp_send_json(array(
                        'result' => 'success'
                  ));
            }
            else {
                  $decoded = json_decode($response['body'], true);
                  wp_send_json(array(
                        'result'    => 'error',
                        'message'   => $decoded['message']
                  ));
            }
      }

      public function activate(){
            $this->ajax_auth();

            $license_key      = trim($_REQUEST['license-key']);
            $validate         = Swift_Performance::check_api(true, $license_key);

      	if ($validate === true) {
                  Swift_Performance::update_option('purchase-key', $license_key);
                  delete_option('swift-performance-license');

      		wp_send_json(array(
                        'result' => 'success'
                  ));
      	}
      	else {
                  wp_send_json(array(
                        'result'    => 'error',
                        'message'   => $validate['response']
                  ));
      	}
      }

      /**
       * Ajaxify Widgets, Shortcode and Blocks
       */
      public function ajaxify(){
            $data = json_decode(base64_decode($_POST['data']), true);

            if (!empty($data[1])){
                  global $post;
                  $post = get_post($data[1]);
            }

            switch ($data[0]){
                  case 'block':
                        $block_data = Swift_Performance_Cache::get_lazyload_buffer($data[2]);
                        $block      = new WP_Block($block_data);
                        echo do_shortcode($block->render());
                        break;
                  case 'shortcode':
                        $attributes = '';
                        $shortcode = Swift_Performance_Cache::get_lazyload_buffer($data[2]);

                        if (isset($shortcode[1])){
                              foreach ((array)$shortcode[1] as $key => $value){
                                    $attributes.= $key . '="'.$value.'" ';
                              }
                        }

                        echo do_shortcode('[' . $shortcode[0] . ' ' . $attributes . ']');
                        break;
                  case 'woo-price':
                        $product_id = $data[2];
                        $product    = wc_get_product($product_id);
                        if ($product !== false && method_exists($product, 'get_price_html')){
                              echo $product->get_price_html();
                        }
                        break;
                  case 'widget':
                        $widget_data = Swift_Performance_Cache::get_lazyload_buffer($data[2]);
                        the_widget($widget_data[0], $widget_data[1], $widget_data[2]);
                        break;
                  case 'elementor':
                        $widget_data = Swift_Performance_Cache::get_lazyload_buffer($data[2]);
                        $document = Elementor\Plugin::$instance->documents->get($data[1]);
			      Elementor\Plugin::$instance->documents->switch_to_document( $document );
	                  echo $document->render_element($widget_data);
                        break;
            }
            die;
      }

}

new Swift_Performance_Ajax();

?>
