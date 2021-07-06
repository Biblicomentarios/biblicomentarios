<?php

class Swift_Performance_Prebuild_Booster {

      private $ver;

      private $buffer = array();

      // Initialize Prebuild Buffer
      public function __construct(){
            // Set version
            $this->ver = hash('crc32', json_encode(get_option('swift_performance_options')));

            // Init buffer
            if (Swift_Performance::check_option('prebuild-booster', 1)){
                  if (class_exists('Memcached')) {
                        $this->buffer = (array)Swift_Performance_Cache::memcached_get('_prebuild_booster_' . $this->ver);
                  }
                  else {
                        $this->buffer = (array)get_transient('swift_performance_prebuild_booster_' . $this->ver);
                  }
            }
      }

      /*
       * Check if resource exists
       * @param string $resource
       * @return boolean
       */
      public function check($resource){
            $key = md5($resource);
            return isset($this->buffer[$key]);
      }

      /*
      * Get resource
      * @param string $resource
      * @return string
      */
      public function get($resource){
            $key = md5($resource);
            return (isset($this->buffer[$key]) ? $this->buffer[$key] : '');
      }

      /*
      * Add resource to buffer
      * @param string $key
      * @param string $value
      */
      public function set($resource, $value){
            $key = md5($resource);
            if (Swift_Performance::check_option('prebuild-booster', 1)){
                  $this->buffer[$key] = $value;
            }
      }

      /**
       * Save buffer to memory or DB
       */
      public function save(){
            if (Swift_Performance::check_option('prebuild-booster', 1)){
                  if (class_exists('Memcached')) {
      			Swift_Performance_Cache::memcached_set('_prebuild_booster_' . $this->ver, $this->buffer, 600);
      		}
      		else {
      			Swift_Performance::safe_set_transient('swift_performance_prebuild_booster_' . $this->ver, $this->buffer, 600);
      		}
            }
      }

      /**
      * Clear all prebuild booster instance
      */
      public static function clear(){
            if (class_exists('Memcached')) {
                  $memcached = Swift_Performance_Cache::get_memcache_instance();
                  if (is_object($memcached)){
                        $keys = $memcached->getAllKeys();
                        foreach((array)$keys as $item) {
                            if(preg_match('~^swift-performance__prebuild_booster~', $item)) {
                                $memcached->delete($item);
                                Swift_Performance::log('Clear prebuild booster ' . $item, 9);
                            }
                        }
                  }
            }
            else {
                  global $wpdb;
                  $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_swift_performance_prebuild_booster%'");
                  $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_swift_performance_prebuild_booster%'");
                  Swift_Performance::log('Clear prebuild booster db', 9);
            }
      }

}

?>