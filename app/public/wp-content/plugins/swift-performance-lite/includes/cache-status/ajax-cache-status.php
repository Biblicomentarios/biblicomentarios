<?php
if (! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

if (! class_exists('Swift_Performance_Dynamic_Cache_Status_Table')) {
      require_once(SWIFT_PERFORMANCE_DIR . 'includes/cache-status/dynamic-cache-status.php');
}

class Swift_Performance_Ajax_Cache_Status_Table extends Swift_Performance_Dynamic_Cache_Status_Table
{

      public function get_columns()
      {
          $columns = array(
                    'status'            => __('Status', 'swift-performance'),
                    'action'            => __('Action', 'swift-performance'),
                    'date'              => __('Cache date', 'swift-performance'),
                    'timestamp'         => __('Timestamp', 'swift-performance'),
                    'expiry'            => __('Expiry', 'swift-performance'),
                    'expiry_timestamp'  => __('Expiry', 'swift-performance'),
              );
          return $columns;
      }

      public function get_sortable_columns()
      {
          $sortable_columns = array(
                  'url'               => array('url',false),
                  'date'              => array('timestamp',false),
                  'expiry'            => array('expiry_timestamp',false),
              );
          return $sortable_columns;
      }

      public function column_action($item)
      {
            $is_cached      = true;
            $actions = array(
                  'delete' => '<a class="clear-single-ajax-url" data-id="'.esc_attr($item['id']).'" href="#">'.esc_html__('Clear cache', 'swift-performance').'</a>'
            );

            $params = array();
            foreach ((array)$item['params'] as $key => $value){
                  if ($key == 'action'){
                        continue;
                  }
                  $params[] = "{$key}: {$value}";
            }

            $request = '<br><small>'.implode(', ', $params).'</small>';

            return sprintf('%1$s %2$s %3$s', $item['action'], $request, $this->row_actions($actions));
      }

    public function get_items()
    {
         $items = array();
         global $wpdb;
         $transients = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_swift_performance_ajax_%'", ARRAY_A);
         foreach ($transients as $transient) {
            $page   = get_transient(str_replace('_transient_timeout_','',$transient['option_name']));
            $id     = str_replace('_transient_timeout_swift_performance_ajax_','',$transient['option_name']);

            $items[] = array(
                     'id'                => $id,
                     'action'            => strtolower($page['params']['action']),
                     'params'            => $page['params'],
                     'cache_status'      => true,
                     'timestamp'         =>  $page['time'],
                     'expiry'            =>  $transient['option_value'],
            );
         }

         return $items;
      }

    /**
    * Message to be displayed when there are no items
    */
    public function no_items()
    {
      _e('Ajax cache is empty', 'swift-performance');
    }
}
