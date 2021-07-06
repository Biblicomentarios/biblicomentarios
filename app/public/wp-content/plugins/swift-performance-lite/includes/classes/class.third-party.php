<?php

class Swift_Performance_Third_Party {

      /**
       * Create Swift_Performance_Third_Party object
       */
      public function __construct(){

            // Sitepress domain mapping
            add_filter('swift_performance_enabled_hosts', array(__CLASS__, 'sitepress_domain_mapping'));

            // NginxCache cache purge support / https://wordpress.org/plugins/nginx-cache/
            add_filter('nginx_cache_purge_actions', function($actions){
                  $actions[] = 'swift_performance_before_clear_all_cache';
                  return $actions;
            });

            if (defined('KINSTAMU_VERSION')){
                  Swift_Performance_Third_Party::kinsta();
            }
      }

      /**
       * Detect third party cache
       * should run after plugins_loaded
       */
      public static function detect_cache(){
            $detected = false;

            // WP Engine detected
            if (class_exists("WpeCommon")) {
                  $detected = true;
            }

            // SG Optimizer detected
            if (function_exists('sg_cachepress_purge_cache')) {
                  $sg_cachepress = get_option('sg_cachepress');

                  if (isset($sg_cachepress['enable_cache']) && $sg_cachepress['enable_cache'] === 1){
                        $detected = true;
                  }
            }

            // Third party cache was detected
            if ($detected && !defined('SWIFT_PERFORMANCE_DISABLE_CACHE')){
                  // Hide caching options in settings
                  add_action('luv_framework_before_render_sections', function($that){
                        unset($that->args['sections']['caching']);

                        // optimize-prebuild-only
                        unset($that->args['sections']['optimization']['general']['fields'][2]);

                        // merge-background-only
                        unset($that->args['sections']['optimization']['general']['fields'][3]);
                  });

                  // Force disable prebuild/background only modes
                  Swift_Performance::update_option('optimize-prebuild-only', 0);
                  Swift_Performance::update_option('merge-background-only', 0);

                  // Disable caching
                  define('SWIFT_PERFORMANCE_DISABLE_CACHE', true);
            }

      }

      /**
       * Clear known third party caches
       */
      public static function clear_cache(){
            // Godaddy
            if (class_exists("\\WPaaS\\Cache")){
                  \WPaaS\Cache::ban();
            }

            // WP Engine
            if (class_exists("WpeCommon")) {
                  if (method_exists('WpeCommon', 'purge_varnish_cache')){
                        WpeCommon::purge_varnish_cache();
                  }
                  if (method_exists('WpeCommon', 'purge_memcached')){
                      WpeCommon::purge_memcached();
                  }
                  if (method_exists('WpeCommon', 'clear_maxcdn_cache')){
                      WpeCommon::clear_maxcdn_cache();
                  }
            }

            // Siteground
            if (function_exists('sg_cachepress_purge_cache')) {
                  sg_cachepress_purge_cache();
            }

            // Nginx helper support
            do_action('rt_nginx_helper_purge_all');

            // Runcache
            if (class_exists('RunCache_Purger')){
                  RunCache_Purger::flush_home();
            }

            // WSA_Cachepurge_WP
            if (method_exists('WSA_Cachepurge_WP', 'purge_cache')){
                  WSA_Cachepurge_WP::purge_cache();
            }
      }

      /**
       * Add filter for enabled hosts
       * @param array $hosts
       * @return array
       */
      public static function sitepress_domain_mapping($hosts){
            global $sitepress;
            if (!empty($sitepress) && is_callable(array($sitepress, 'get_setting'))){
                  $domains = $sitepress->get_setting( 'language_domains', array() );
                  if (!empty($domains)){
                        $hosts = array_merge($hosts, $domains);
                  }
            }
            return $hosts;
      }

      /**
       * Improve Kinsta compatibility
       */
      public function kinsta(){
            add_filter('swift_performance_option_separate-js', function(){
                  return 1;
            });

            add_filter('swift_performance_option_separate-css', function(){
                  return 1;
            });

            add_action( 'transition_post_status', function($new_status, $old_status, $post){
                  Swift_Performance_Asset_Manager::clear_assets_cache($post->ID);
            }, 11,3);

		add_action( 'pre_post_update', array( 'Swift_Performance_Cache', 'clear_post_cache' ), 11);
		add_action( 'post_updated', array( 'Swift_Performance_Cache', 'clear_post_cache' ), 11);
		add_action( 'wp_trash_post', array( 'Swift_Performance_Cache', 'clear_post_cache' ), 11);

		add_action( 'wp_insert_comment', function($comment_id, $comment){
      		if (1 === $comment->comment_approved ){
      			Swift_Performance_Cache::clear_post_cache($comment->comment_post_ID);
      		}
            }, 11, 2);
		add_action( 'edit_comment', function($comment_id, $comment){
      		if (1 === $comment->comment_approved ){
      			Swift_Performance_Cache::clear_post_cache($comment->comment_post_ID);
      		}
            }, 11, 2);
		add_action( 'transition_comment_status', function($new_status, $old_status, $comment) {
      		if ( 'approved' === $new_status || 'approved' === $old_status ) {
      			Swift_Performance_Cache::clear_post_cache($comment->comment_post_ID);
      		}
            }, 11,3);
		add_action( 'wp_update_nav_menu', function(){
                  Swift_Performance_Cache::clear_all_cache();
            }, 11);

            add_action('wp_ajax_kinsta_clear_cache_all', function(){
                  check_ajax_referer( 'kinsta-clear-cache-all', 'kinsta_nonce' );
                  Swift_Performance_Cache::clear_all_cache();

            }, 9);

            add_action('wp_ajax_kinsta_clear_cache_full_page', function(){
                  check_ajax_referer( 'kinsta-clear-cache-full-page', 'kinsta_nonce' );
                  Swift_Performance_Cache::clear_all_cache();
            }, 9);


      }



}

return new Swift_Performance_Third_Party();

?>
