<?php
if (! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Swift_Performance_Dynamic_Cache_Status_Table extends WP_List_Table
{
    public function get_columns()
    {
        $columns = array(
                  'status'            => __('Status', 'swift-performance'),
                  'url'               => __('URL', 'swift-performance'),
                  'date'              => __('Cache date', 'swift-performance'),
                  'timestamp'         => __('Timestamp', 'swift-performance'),
                  'expiry'            => __('Expiry', 'swift-performance'),
                  'expiry_timestamp'  => __('Expiry', 'swift-performance'),
            );
        return $columns;
    }

    public function prepare_items()
    {
        $items = $this->get_items();

        $columns = $this->get_columns();
        $hidden = array('timestamp', 'expiry_timestamp');
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        usort($items, array( &$this, 'usort_reorder' ));

        $per_page = max((int)Swift_Performance::get_option('warmup-per-page'), 1);
        $current_page = $this->get_pagenum();
        $total_items = count($items);

        $found_data = array_slice($items, (($current_page-1)*$per_page), $per_page);

        $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page'    => $per_page
            ));
        $this->items = $found_data;
    }

    public function column_default($item, $column_name)
    {
        return $item[ $column_name ];
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

    public function get_items()
    {
          $items = array();
          global $wpdb;
          $transients = $wpdb->get_col("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_swift_performance_dynamic_%'");

          foreach ($transients as $transient) {
                $page   = get_transient(str_replace('_transient_','',$transient));
                $id     = str_replace('_transient_swift_performance_dynamic_','',$transient);

                $items[] = array(
                      'id'                => $id,
                      'url'               => home_url($page['request_uri']),
                      'cache_status'      => true,
                      'params'            => $page['params'],
                      'timestamp'         =>  $page['time'],
                      'expiry'            =>  ($page['expiry'] > 0 ? $page['time'] + $page['expiry'] : 0),
                      'expiry_timestamp'  =>  ($page['expiry'] > 0 ? $page['time'] + $page['expiry'] : 0),
                );
          }

          return $items;
    }

    public function single_row( $item ) {
          echo '<tr data-id="' . $item['id'] . '">';
          $this->single_row_columns( $item );
          echo '</tr>';
    }

    public function column_status($item)
    {
          $status           = '<span title="' . esc_attr__('Cached', 'swift-performance') . '" class="dashicons dashicons-yes"></span>';
          return $status;
    }

    public function column_date($item)
    {
        return ($item['timestamp'] > 0 ? get_date_from_gmt(date('Y-m-d H:i:s', $item['timestamp']), get_option('date_format') . ' ' .get_option('time_format')) : '-');
    }

    public function column_expiry($item)
    {
        return ($item['expiry'] > 0 ? get_date_from_gmt(date('Y-m-d H:i:s', $item['expiry']), get_option('date_format') . ' ' .get_option('time_format')) : '-');
    }

    public function column_url($item)
    {
        $is_cached      = true;
        $request        = '';

        if (!empty($item['params'])){
            $params = array();
            foreach ((array)$item['params'] as $key => $value){
                    if ($key == 'action'){
                          continue;
                    }
                    $params[] = "{$key}: {$value}";
            }
            $request = '<br><small>'.implode(', ', $params).'</small>';
        }

        $actions = array(
                  'delete' => '<a class="clear-single-dynamic-url" data-id="'.esc_attr($item['id']).'" href="#">'.esc_html__('Clear cache', 'swift-performance').'</a>'
            );

        return sprintf('%1$s %2$s %3$s', urldecode($item['url']), $request, $this->row_actions($actions));
    }

    public function usort_reorder($a, $b)
    {
        $orderby = (! empty($_GET['orderby'])) ? $_GET['orderby'] : 'url';

        $order = (! empty($_GET['order'])) ? $_GET['order'] : 'asc';

        if ($orderby == 'timestamp') {
            $result = ($a['timestamp'] > $b['timestamp'] ? 1 : ($a['timestamp'] == $b['timestamp'] ? 0 : -1));
        } else {
            $result = strcmp($a[$orderby], $b[$orderby]);
        }
        return ($order === 'asc') ? $result : -$result;
    }

    protected function get_table_classes()
    {
        return array( 'widefat', 'fixed', 'striped', 'swift-performance-list-table swift-performance-long-url-table' );
    }

    /**
    * Message to be displayed when there are no items
    */
    public function no_items()
    {
      _e('Dynamic cache is empty', 'swift-performance');
    }
}
