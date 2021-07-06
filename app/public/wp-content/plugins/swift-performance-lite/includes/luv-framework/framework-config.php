<?php
// Intelligent caching backward compatibility
if (Swift_Performance::check_option('cache-expiry-mode', 'intelligent')){
      Swift_Performance::update_option('cache-expiry-mode', 'actionbased');
}

// Async script delivery backward compatibility
if (Swift_Performance::check_option('delay-async-scripts', 1)){
      Swift_Performance::remove_option('delay-async-scripts', 1);
      Swift_Performance::update_option('script-delivery', 'delay');
}

// Get post types
$post_types = Swift_Performance::get_post_types();
$post_types = array_combine($post_types, $post_types);

// Get page list
global $wpdb;
$pages = array();
foreach ($wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'", ARRAY_A) as $_page) {
    $pages[$_page['ID']] = $_page['post_title'];
}

// Check IP source automatically for GA
$ga_ip_source = '';
foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_CF_CONNECTING_IP','REMOTE_ADDR') as $source) {
    if (isset($_SERVER[$source]) && !empty($_SERVER[$source])) {
	  $ga_ip_source = $source;
	  break;
    }
}

// Basic options
$cache_modes = array(
	'disk_cache_php' => esc_html__('Disk Cache with PHP', 'swift-performance'),
);

// Add disk cache + rewrite option if it is available
if (in_array(Swift_Performance::server_software(), array('apache', 'nginx'))){
      $cache_modes['disk_cache_rewrite'] = esc_html__('Disk Cache with Rewrites', 'swift-performance');
}

// Memcached support
if (class_exists('Memcached')) {
    $cache_modes['memcached_php'] = esc_html__('Memcached with PHP', 'swift-performance');
}

// WebP options
if (Swift_Performance::server_software() == 'apache'){
      $swift_webp = array(
            'none'      => esc_html__('Don\'t use WebP', 'swift-performace'),
            'picture'   => __('Use <picture> elements', 'swift-performace'),
            'rewrite'   => esc_html__('Use rewrites', 'swift-performace'),
      );
}
else {
      $swift_webp = array(
            'none'      => esc_html__('Don\'t use WebP', 'swift-performace'),
            'picture'   => __('Use <picture> elements', 'swift-performace'),
      );
}

// Plugin based options
$active_plugins = get_option('active_plugins');
$is_woocommerce_active = apply_filters('swift_performance_is_woocommerce_active', in_array('woocommerce/woocommerce.php', $active_plugins));

$swift_countries = array();
if ($is_woocommerce_active) {
    @$swift_countries = apply_filters('woocommerce_countries', include WP_PLUGIN_DIR . '/woocommerce/i18n/countries.php');
}

// User roles
$roles = array();
foreach ((array)get_option($wpdb->prefix . 'user_roles') as $role_slug => $role) {
    $roles[$role_slug] = $role['name'];
}

/**
 * Validate Purchase Key
 * @param boolean|array $result
 * @param string $value
 */
function swift_performance_purchase_key_validate_callback($result, $value){
      delete_option('swift-performance-license');

	if (empty($value)){
		update_option('swift-performance-license', array('type' => 'offline'));
            return true;
	}

      $validate = Swift_Performance::check_api(true, $value);

	if ($validate === true) {
		return true;
	}
	else {
		return array(
			'error' => $validate['response']
		);
	}
}

/**
 * Validate log path
 * @param boolean|array $result
 * @param string $value
 */
function swift_performance_log_path_validate_callback($result, $value){
	// Stop here if logging isn't enabled at all
	if (!isset($_POST['_luv_enable-logging']) || $_POST['_luv_enable-logging'] != 1){
		return $result;
	}

	if (!file_exists($value)) {
	    @mkdir($value, 0777, true);
	    if (!file_exists($value)) {
		  return array('error' => __('Log directory doesn\'t exists', 'swift-performance'));
	    }
	} elseif (!is_dir($value)) {
	    return array('error' => __('Log directory should be a directory', 'swift-performance'));
	} elseif (!is_writable($value)) {
	    return array('error' => __('Log directory isn\'t writable for WordPress. Please change the permissions.', 'swift-performance'));
	}

	return $result;
}

/**
 * Validate caching mode
 * @param boolean|array $result
 * @param string $value
 */
function swift_performance_cache_mode_validate_callback($result, $value){
      // Check htaccess only for Apache
      if ($value != 'disk_cache_rewrite' || Swift_Performance::server_software() != 'apache') {
          return true;
      }

      $htaccess = Swift_Performance::get_home_path() . '.htaccess';

      if (!file_exists($htaccess)) {
          @touch($htaccess);
          if (!file_exists($htaccess)) {
              return array('warning' => __('htaccess doesn\'t exists', 'swift-performance'));
          }
      } elseif (!is_writable($htaccess)) {
          return array('warning' => __('htaccess isn\'t writable for WordPress. Please change the permissions.', 'swift-performance'));
      }

      return true;
}

/**
 * Validate caching mode
 * @param boolean|array $result
 * @param string $value
 */
function swift_performance_muplugins_validate_callback($result, $value){
      $muplugins_dir = WPMU_PLUGIN_DIR;

      if ($value == 1) {
          if (!file_exists($muplugins_dir)) {
             @mkdir($muplugins_dir, 0777);
             if (!file_exists($muplugins_dir)) {
                  return array('error' => __('MU Plugins directory doesn\'t exists', 'swift-performance'));
             }
          } elseif (!is_writable($muplugins_dir)) {
            return array('error' => __('MU Plugins directory isn\'t writable for WordPress. Please change the permissions.', 'swift-performance'));
          }
      }
}

/**
 * Validate caching mode
 * @param boolean|array $result
 * @param string $value
 */
function swift_performance_cache_path_validate_callback($result, $value){
      if (empty($value)){
            return array('error' => esc_html__('Cache directory is empty', 'swift-performance'));
      }
      if (!file_exists($value)) {
            @mkdir($value, 0777, true);
            if (!file_exists($value)) {
                  return array('error' => __('Cache directory doesn\'t exists', 'swift-performance'));
            }
      } elseif (!is_dir($value)) {
            return array('error' => __('Cache directory should be a directory', 'swift-performance'));
      } elseif (!is_writable($value)) {
            return array('error' => __('Cache directory isn\'t writable for WordPress. Please change the permissions.', 'swift-performance'));
      }

      return true;
}

/**
 * Validate custom htaccess
 * @param boolean|array $result
 * @param string $value
 */
function swift_performance_custom_htaccess_validate_callback($result, $value){
      // Check htaccess only for Apache
      if (empty($value) || Swift_Performance::server_software() != 'apache') {
          return true;
      }

      $htaccess = Swift_Performance::get_home_path() . '.htaccess';

      if (!file_exists($htaccess)) {
          @touch($htaccess);
          if (!file_exists($htaccess)) {
              return array('warning' => __('htaccess doesn\'t exists', 'swift-performance'));
          }
      } elseif (!is_writable($htaccess)) {
            return array('warning' => __('htaccess isn\'t writable for WordPress. Please change the permissions.', 'swift-performance'));
      }
      else if (Swift_Performance::disable_file_edit()){
            return array('warning' => __('DISALLOW_FILE_EDIT or DISALLOW_FILE_MODS is enabled, so you can\'t add custom htaccess', 'swift-performance'));
      }

      return true;
}

// Conditional hooks
add_action('luv_framework_before_fields_init', function($that){
      // Whitelabel
      if ((defined('SWIFT_PERFORMANCE_WHITELABEL') && SWIFT_PERFORMANCE_WHITELABEL) || Swift_Performance::license_type() !== 'pro'){
            unset($that->args['sections']['general']['subsections']['whitelabel']);
      }

      // Plugin based options
      $active_plugins = get_option('active_plugins');
      $is_woocommerce_active = apply_filters('swift_performance_is_woocommerce_active', in_array('woocommerce/woocommerce.php', $active_plugins));
      $is_cf7_active = apply_filters('swift_performance_is_wpcf7_active', in_array('contact-form-7/wp-contact-form-7.php', $active_plugins));
      $is_elementor_active = apply_filters('swift_performance_is_elementor_active', in_array('elementor/elementor.php', $active_plugins));

      if (!$is_woocommerce_active) {
          unset($that->args['sections']['plugins']['subsections']['woocommerce']);
      }

      if (!$is_cf7_active) {
          unset($that->args['sections']['plugins']['subsections']['wpcf7']);
      }

      if ($is_elementor_active){
            if (isset($that->args['sections']['optimization']['subsections']['scripts']['fields'])){
                  foreach ((array)$that->args['sections']['optimization']['subsections']['scripts']['fields'] as $key => $field){
                        if ($field['id'] == 'exclude-scripts'){
                              if (!isset($that->args['sections']['optimization']['subsections']['scripts']['fields'][$key]['default']) || !is_array($that->args['sections']['optimization']['subsections']['scripts']['fields'][$key]['default'])){
                                    $that->args['sections']['optimization']['subsections']['scripts']['fields'][$key]['default'] = array();
                              }
                              $that->args['sections']['optimization']['subsections']['scripts']['fields'][$key]['default'][] = 'webpack(-pro)?\.runtime\.min\.js';
                        }
                  }
            }
      }
      else {
            unset($that->args['sections']['plugins']['subsections']['elementor']);
      }

      if (!isset($that->args['sections']['plugins']['subsections']) || empty($that->args['sections']['plugins']['subsections'])){
            unset($that->args['sections']['plugins']);
      }
});

// Widget list
add_filter('luv_framework_render_field_lazyload-widgets', function($field){
      global $wp_widget_factory;
      foreach ($wp_widget_factory->widgets as $widget){
            $widget_class = get_class($widget);
            $field['options'][$widget_class] = $widget->name;
      }
      asort($field['options']);
      return $field;
});


// Add header
add_action('luv_framework_before_framework_header', function(){
      if (!isset($_GET['page']) || $_GET['page'] !== SWIFT_PERFORMANCE_SLUG){
            return;
      }

      if (defined('SWIFT_PERFORMANCE_WHITELABEL') && SWIFT_PERFORMANCE_WHITELABEL){
            // Whitelabel backward compatibility
            echo '<h2>' . esc_html__('Settings', 'swift-performance') . '</h2>';
      }
      else {
            $pointers = (array)get_user_meta(get_current_user_id(), 'swift_pointers', true);
            ?>
            <div class="swift-performance-settings-header">
                  <h2><?php esc_html_e('Settings', 'swift-performance');?></h2>
                  <div class="swift-settings-mode" <?php echo (!isset($pointers['settings-mode']) ? 'data-swift-pointer="settings-mode" data-swift-pointer-position="right" data-swift-pointer-content="' . esc_attr__('By default some options are hidden. You can switch to Advanced View to see all options', 'swift-performance') . '"' : '')?>>
                  <input type="radio" name="mode-switch" id="simple-switch" value="simple"<?php echo(Swift_Performance::check_option('settings-mode', 'simple') ? ' checked="checked"' : '');?>>
                  <label class="swift-btn swift-btn-blacknwhite" for="simple-switch"><?php esc_html_e('Simple View', 'swift-performance');?></label>
                  <input type="radio" name="mode-switch" id="advanced-switch" value="advanced"<?php echo(Swift_Performance::check_option('settings-mode', 'advanced') ? ' checked="checked"' : '');?>>
                  <label class="swift-btn swift-btn-blacknwhite" for="advanced-switch"><?php esc_html_e('Advanced View', 'swift-performance');?></label>
                  </div>
            </div>
            <?php
      }
});

// Preview button
add_action('luv_framework_before_header_buttons', function($fieldset){
      if (!isset($_GET['page']) || $_GET['page'] !== SWIFT_PERFORMANCE_SLUG){
            return;
      }

      echo '<li><a href="#" class="luv-framework-button swift-performance-ajax-preview" data-fieldset="#fieldset-' . $fieldset->unique_id . '">' . esc_html__('Preview', 'swift-performance') . '</a></li>';
});

// Advanced Switcher for whitelabel backward compatibility
add_action('luv_framework_before_framework_outer', function($fieldset){
      if (!isset($_GET['page']) || $_GET['page'] !== SWIFT_PERFORMANCE_SLUG){
            return;
      }

      if (defined('SWIFT_PERFORMANCE_WHITELABEL') && SWIFT_PERFORMANCE_WHITELABEL){
            $pointers = (array)get_user_meta(get_current_user_id(), 'swift_pointers', true);
            ?>
            <div class="swift-settings-mode" <?php echo (!isset($pointers['settings-mode']) ? 'data-swift-pointer="settings-mode" data-swift-pointer-position="right" data-swift-pointer-content="' . esc_attr__('By default some options are hidden. You can switch to Advanced View to see all options', 'swift-performance') . '"' : '')?>">
            <input type="radio" name="mode-switch" id="simple-switch" value="simple"<?php echo(Swift_Performance::check_option('settings-mode', 'simple') ? ' checked="checked"' : '');?>>
            <label class="swift-btn swift-btn-gray" for="simple-switch">Simple View</label>
            <input type="radio" name="mode-switch" id="advanced-switch" value="advanced"<?php echo(Swift_Performance::check_option('settings-mode', 'advanced') ? ' checked="checked"' : '');?>>
            <label class="swift-btn swift-btn-gray" for="advanced-switch">Advanced View</label>
            </div>
            <?php
      }
});

// Remove localized fields from export
add_filter('luv_framework_export_array', function($options){
      unset($options['purchase-key']);
      unset($options['cache-path']);
      unset($options['log-path']);
      unset($options['cloudflare-email']);
      unset($options['cloudflare-api-key']);
      unset($options['cloudflare-host']);
      unset($options['maxcdn-alias']);
      unset($options['maxcdn-key']);
      unset($options['maxcdn-secret']);
      return $options;
});

// Image Optimizer preset
add_action('luv_framework_custom_field_image-optimizer-preset', function(){
      echo '<div class="swift-performance-io-preset-container">';
      echo '<input type="radio" class="swift-performance-io-preset" name="_luv_image-optimizer-preset" id="io-preset-lossless" value="lossless" data-jpeg="100" data-png="100"' . (Swift_Performance::check_option('jpeg-quality', 100) && Swift_Performance::check_option('png-quality', 100) ? ' checked="checked"' : '') . '><label for="io-preset-lossless" href="#" class="swift-btn swift-btn-gray">' . __('Lossless', 'swift-performance') . '</label> ';
      echo '<input type="radio" class="swift-performance-io-preset" name="_luv_image-optimizer-preset" id="io-preset-slightly-lossy" value="slightly-lossy" data-jpeg="85" data-png="100"' . (Swift_Performance::check_option('jpeg-quality', 85) && Swift_Performance::check_option('png-quality', 100) ? ' checked="checked"' : '') . '><label for="io-preset-slightly-lossy" href="#" class="swift-btn swift-btn-gray">' . __('Slightly Lossy', 'swift-performance') . '</label> ';
      echo '<input type="radio" class="swift-performance-io-preset" name="_luv_image-optimizer-preset" id="io-preset-moderate" value="moderate" data-jpeg="70" data-png="90"' . (Swift_Performance::check_option('jpeg-quality', 70) && Swift_Performance::check_option('png-quality', 90) ? ' checked="checked"' : '') . '><label for="io-preset-moderate" href="#" class="swift-btn swift-btn-gray">' . __('Moderate', 'swift-performance') . '</label> ';
      echo '<input type="radio" class="swift-performance-io-preset" name="_luv_image-optimizer-preset" id="io-preset-agressive" value="agressive" data-jpeg="65" data-png="70"' . (Swift_Performance::check_option('jpeg-quality', 65) && Swift_Performance::check_option('png-quality', 70) ? ' checked="checked"' : '') . '><label for="io-preset-agressive" href="#" class="swift-btn swift-btn-gray">' . __('Agressive', 'swift-performance') . '</label> ';
      echo '</div>';
});

// Clear Cache Modal
add_action('luv_framework_after_render_sections', function(){
      ?>
      <div class="swift-confirm-clear-cache luv-hidden">
            <h6 class="luv-modal__title"><?php esc_html_e('Hey!', 'swift-performance');?></h6>
            <p class="luv-modal__text"><?php esc_html_e('Some modifications affected cache. Would you like to clear cache?', 'swift-performance');?></p>
            <a href="#" class="swift-btn swift-btn-brand" data-swift-clear-cache><?php esc_html_e('Clear cache', 'swift-performance');?></a>
            <a href="#" class="swift-btn swift-btn-black" data-luv-close-modal><?php esc_html_e('Dismiss', 'swift-performance');?></a>
      </div>
      <?php
});

// Reset Warmup Modal
add_action('luv_framework_after_render_sections', function(){
      ?>
      <div class="swift-confirm-reset-warmup luv-hidden">
            <h6 class="luv-modal__title"><?php esc_html_e('Hey!', 'swift-performance');?></h6>
            <p class="luv-modal__text"><?php esc_html_e('Some modifications affected Warmup Table. Would you like to reset it?', 'swift-performance');?></p>
            <a href="#" class="swift-btn swift-btn-brand" data-swift-reset-warmup><?php esc_html_e('Reset Warmup Table', 'swift-performance');?></a>
            <a href="#" class="swift-btn swift-btn-black" data-luv-close-modal><?php esc_html_e('Dismiss', 'swift-performance');?></a>
      </div>
      <?php
});


// Feature required
add_filter('luv_framework_field_file_to_include', function($file, $field){
      if (!isset($field['feature']) || empty($field['feature'])){
            return $file;
      }

      // Valid PRO license
      if (Swift_Performance::license_type() == 'pro' && Swift_Performance::is_option_set('purchase-key')){
            return $file;
      }

      if (Swift_Performance::license_type() !== 'pro' && $field['feature'] == 'pro_only'){
            return apply_filters('swift_performance_template_dir', SWIFT_PERFORMANCE_DIR . 'templates/luv-framework/') . 'pro-only.php';
      }

      if (in_array(Swift_Performance::license_type(), array('offline', 'nulled')) || !Swift_Performance::is_option_set('purchase-key')){
            return apply_filters('swift_performance_template_dir', SWIFT_PERFORMANCE_DIR . 'templates/luv-framework/') . 'license-required.php';
      }

      if (!Swift_Performance::is_feature_available($field['feature'])){
            return apply_filters('swift_performance_template_dir', SWIFT_PERFORMANCE_DIR . 'templates/luv-framework/') . 'not-available.php';
      }

      return $file;
},10,3);

Swift_Performance::$luvoptions = Luv_Framework::fields('option', array(
	'menu' => 'tools.php',
	'submenu' => SWIFT_PERFORMANCE_SLUG,
	'menu_title' => SWIFT_PERFORMANCE_PLUGIN_NAME,
	'page_title' => SWIFT_PERFORMANCE_PLUGIN_NAME,
	'option_name' => 'swift_performance_options',
	'ajax'	=> true,
      'nonce_id'  => 'swift_performance_options',
	'class'	=> 'swift-performance-settings',
	'sections' => array(
		'general' => array(
			'title'	=> esc_html__('General', 'swift-performance'),
			'icon'	=> 'fas fa-cog',
			'subsections' => array(
				'general' => array(
					'title'	=> esc_html__('General', 'swift-performance'),
					'fields'	=> array(
                                    array(
							'id'   => 'settings-mode',
							'type' => 'hidden',
							'default' => 'simple'
						),
						array(
							'id'				=> 'purchase-key',
							'type'			=> (Swift_Performance::license_type() == 'offline' ? 'hidden' : 'license'),
							'title'			=> esc_html__('Purchase Key', 'swift-performance'),
							'validate_callback'	=> 'swift_performance_purchase_key_validate_callback',
                                          'class'                 => 'should-refresh',
						),
						array(
							'id'		=> 'whitelabel',
							'type'	=> 'switch',
							'title'	=> esc_html__('Hide Footprints', 'swift-performance'),
		                              'desc'	=> sprintf(esc_html__('Prevent to add %s response header and HTML comment', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
		                              'default'	=> 0,
							'class'	=> 'should-clear-cache',
                                          'required'  => array('settings-mode', '=', 'advanced'),
						),
						array(
		                             'id'		=> 'use-compute-api',
		                             'type'		=> 'switch',
		                             'title'	=> esc_html__('Use Compute API', 'swift-performance'),
		                             'desc'       => esc_html__('Speed up merging process and decrease CPU usage.', 'swift-performance'),
                                         'info'		=> __('Compute API can speed up CPU extensive processes like generating Critical CSS, or minification.', 'swift-performance'),
		                             'default'	=> 0,
		                             'required'	=> array('purchase-key', 'NOT_EMPTY')
		                        ),
                                    array(
                                         'id'	      => 'clear-cache-roles',
                                         'type'       => 'checkbox',
                                         'title'      => esc_html__('Clear Cache Role', 'swift-performance'),
                                         'desc'       => esc_html__('User role to clear cache', 'swift-performance'),
                                         'info'       => __('You can add user roles to have clear cache ability.', 'swift-performance'),
                                         'options'    => $roles,
                                         'multiple'   => true,
                                         'default'    => array('administrator'),
                                         'required'	=> array(
                                               array('settings-mode', '=', 'advanced'),
                                          ),
                                   ),
                                    array(
		                             'id'		=> 'disable-admin-notices',
		                             'type'		=> 'switch',
		                             'title'	=> esc_html__('Disable Admin Notices', 'swift-performance'),
		                             'desc'       => sprintf(esc_html__('You can disable %s admin notices.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                                         'info'		=> sprintf(__('After update/activate/deactivate plugin, switch/update theme, or update WordPress core %s will show notices to clear cache. With this option you can disable these notices (but in some cases you still should clear cache).', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
		                             'default'	=> 0,
                                         'feature'    => 'disable_admin_notices',
		                             'required'	=> array(
                                               array('settings-mode', '=', 'advanced')
                                         )
		                        ),
                                    array(
		                             'id'		=> 'disable-toolbar',
		                             'type'		=> 'switch',
		                             'title'	=> esc_html__('Disable Toolbar', 'swift-performance'),
		                             'desc'       => sprintf(esc_html__('You can disable %s toolbar menu.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
		                             'default'	=> 0,
		                             'required'	=> array(
                                               array('settings-mode', '=', 'advanced')
                                         )
		                        ),
                                    array(
                                         'id'		=> 'page-specific-rules',
                                         'type'		=> 'switch',
                                         'title'	=> esc_html__('Page Specific Rules', 'swift-performance'),
                                         'desc'       => esc_html__('You can override settings on specific posts/pages.', 'swift-performance'),
                                         'info'       => sprintf(__('In page editor you can override page settings in %s metabox.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                                         'default'	=> 0,
                                         'required'	=> array(
                                               array('settings-mode', '=', 'advanced')
                                         )
                                    ),
						array(
						     'id'         => 'enable-beta',
						     'type'       => (Swift_Performance::license_type() != 'pro' ? 'hidden' : 'switch'),
						     'title'      => esc_html__('Beta Tester', 'swift-performance'),
						     'desc'		=> esc_html__('If you enable this option you will get updates in beta stage', 'swift-performance'),
						     'default'    => 0,
						     'required'   => array('purchase-key', 'NOT_EMPTY'),
						),
                                    array(
						     'id'         => 'collect-anonymized-data',
						     'type'       => 'switch',
						     'title'      => esc_html__('Collect Anonymized Data', 'swift-performance'),
						     'info'		=> esc_html__('PHP version, server software, active plugins, active theme, total pages, total posts (including custom post types), total images', 'swift-performance'),
						     'default'    => 0,
						),
						array(
		                             'id'		=> 'enable-logging',
		                             'type'		=> 'switch',
		                             'title'	=> esc_html__('Debug Log', 'swift-performance'),
		                             'desc'		=> esc_html__('Enable debug logging', 'swift-performance'),
                                         'info'		=> __('If you have any issues (eg caching/image optimizer is not working) you can start debugging here.', 'swift-performance'),
		                             'default' 	=> 0,
                                         'required'   => array('settings-mode', '=', 'advanced')
		                        ),
		                        array(
		                            	'id'		=> 'loglevel',
		                            	'type'	=> 'dropdown',
		                            	'title'	=> esc_html__('Loglevel', 'swift-performance'),
		                            	'required'	=> array('enable-logging', '=', 1),
		                            	'options'	=> array(
							   '9' => esc_html__('All', 'swift-performance'),
							   '6' => esc_html__('Warning', 'swift-performance'),
							   '1' => esc_html__('Error', 'swift-performance'),
		                            	),
							'default'    => '1',
						),
		                        array(
		                              'id'	      => 'log-path',
		                              'type'	=> 'text',
		                              'title'	=> esc_html__('Log Path', 'swift-performance'),
		                              'default'   => WP_CONTENT_DIR . '/swift-logs-'.hash('crc32', NONCE_SALT).'/',
		                              'required'  => array('enable-logging', '=', 1),
		                              'validate_callback' => 'swift_performance_log_path_validate_callback',
		                        ),
					)
				),
				'tweaks' => array(
					'title'	=> esc_html__('Tweaks', 'swift-performance'),
					'fields'	=> array(
		                        array(
		                             'id'         => 'custom-htaccess',
		                             'type'       => 'editor',
		                             'title'	=> esc_html__('Custom Htaccess', 'swift-performance'),
		                             'desc'       => sprintf(esc_html__('You can add custom rules before %s rules in the generated htaccess', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                                         'info'	      => sprintf(__('%s will add rules to the very beginning of htaccess. If you would like to put some rules before, you have to use this option.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
		                             'mode'       => 'text',
		                             'theme'      => 'monokai',
                                         'validate_callback' => 'swift_performance_custom_htaccess_validate_callback'
		                        ),

		                        array(
		                             'id'         => 'background-requests',
		                             'type'       => 'multi-text',
		                             'title'	=> esc_html__('Background Requests', 'swift-performance'),
		                             'desc'       => esc_html__('Specify key=value pairs. If one of these rules are match on $_REQUEST the process will run in background', 'swift-performance'),
                                         'info'	      => __('For some AJAX requests we doesn\'t need the response (eg post view stats). You can add rules to make this requests run in background, so the browser won\'t wait the response.<br><br>For example if there is a request: /?action=post_view_count you can set <i><b>action=post_view_count</b></i>', 'swift-performance'),
                                         'required'   => array('settings-mode', '=', 'advanced'),
                                         'feature'    => 'background_requests'
		                        ),
					)
				),
				'heartbeat' => array(
					'title'	=> esc_html__('Heartbeat', 'swift-performance'),
		                  'fields'	=> array(
		                         array(
		                            'id'	=> 'disable-heartbeat',
		                            'type'	=> 'checkbox',
		                            'title' => esc_html__('Disable Heartbeat', 'swift-performance'),
		                            'options' => array(
							    'index.php'                                            => esc_html__('Dashboard', 'swift-performance'),
							    'edit.php,post.php,post-new.php'                       => esc_html__('Posts/Pages', 'swift-performance'),
							    'upload.php,media-new.php'                             => esc_html__('Media', 'swift-performance'),
							    'edit-comments.php,comment.php'                        => esc_html__('Comments', 'swift-performance'),
							    'nav-menus.php'                                        => esc_html__('Menus', 'swift-performance'),
							    'widgets.php'                                          => esc_html__('Widgets', 'swift-performance'),
							    'theme-editor.php,plugin-editor.php'                   => esc_html__('Theme/Plugin Editor', 'swift-performance'),
							    'users.php,user-new.php,user-edit.php,profile.php'     => esc_html__('Users', 'swift-performance'),
							    'tools.php'                                            => esc_html__('Tools', 'swift-performance'),
							    'options-general.php'                                  => esc_html__('Settings', 'swift-performance'),
		                            ),
		                            'default' => '',
                                        'required' => array('settings-mode', '=', 'advanced'),
                                        'info'  => __('WordPress is using HeartBeat API to show real time notifications, notify users that a post is being edited by another user, etc. You can limit these requests where you don\'t really need them.', 'swift-performance')
		                         ),
		                         array(
		                              'id'         	=> 'heartbeat-frequency',
		                              'type'      	=> 'dropdown',
		                              'title'		=> esc_html__('Heartbeat Frequency', 'swift-performance'),
		                              'desc'	=> esc_html__('Override heartbeat frequency in seconds', 'swift-performance'),
		                              'options'    	=> array(
							     10 => 10,
							     20 => 20,
							     30 => 30,
							     40 => 40,
							     50 => 50,
							     60 => 60,
							     70 => 70,
							     80 => 80,
							     90 => 90,
							     100 => 100,
							     110 => 110,
							     120 => 120,
							     130 => 130,
							     140 => 140,
							     150 => 150,
							     160 => 160,
							     170 => 170,
							     180 => 180,
							     190 => 190,
							     200 => 200,
							     210 => 210,
							     220 => 220,
							     230 => 230,
							     240 => 240,
							     250 => 250,
							     260 => 260,
							     270 => 270,
							     280 => 280,
							     290 => 290,
							     300 => 300
		                              ),
							'default' => 60,
                                          'required' => array('settings-mode', '=', 'advanced'),
		                         )
		                   )
				),
                        'cron' => array(
                              'title'     => esc_html__('Cronjobs', 'swift-performance'),
                              'fields'    => array(
                                    array(
                                          'id'        => 'limit-wp-cron',
                                          'type'      => 'slider',
                                          'min'       => 0,
                                          'max'       => 100,
                                          'title'     => esc_html__('Limit WP Cron', 'swift-performance'),
                                          'desc'      => esc_html__('Prevent WP Cron being called on every page load.', 'swift-performance'),
                                          'info'      => esc_html__('100% means unlimited, 0% means WP Cron is fully disabled.', 'swift-performance'),
                                          'default'   => 100,
                                          'feature'    => 'limit_wp_cron',
                                          'required'  => array(
                                                array('settings-mode', '=', 'advanced'),
                                          )
                                    ),
                                    array(
 		                             'id'         => 'remote-cron',
 		                             'type'       => 'switch',
 		                             'title'      => esc_html__('Enable Remote Cron', 'swift-performance'),
 						     'desc'       => esc_html__('Set up a real cronjob with our API.', 'swift-performance'),
 						     'info'		=> __('If all of your pages are cached - or if you disabled the default WP Cron - WordPress cronjobs won\'t run properly. With Remote Cron service you can run cronjobs daily, twicedaily or hourly.', 'swift-performance'),
 		                             'default'    => 0,
 		                             'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('purchase-key', 'NOT_EMPTY'),
                                          )
 		                        ),
 						array(
 		                           'id'         => 'remote-cron-frequency',
 		                           'type'       => 'dropdown',
 		                           'title'		=> esc_html__('Remote Cron Frequency', 'swift-performance'),
 		                           'required'	=> array('remote-cron', '=', 1),
 		                           'options'	=> array(
 							   'daily'   => esc_html__('Daily', 'swift-performance'),
 							   'twicedaily' => esc_html__('Twice a day', 'swift-performance'),
 							   'hourly' => esc_html__('Hourly', 'swift-performance'),
 		                           ),
 		                           'default'    => 'daily',
 		                       ),
                              )
                        ),
				'general-ga' => array(
		                  'title' => esc_html__('Google Analytics', 'swift-performance'),
		                  'fields' => array(
						array(
		                             'id'         => 'bypass-ga',
		                             'type'       => 'switch',
		                             'title'      => esc_html__('Bypass Google Analytics', 'swift-performance'),
		                             'default'    => 0,
                                         'class'	=> 'should-clear-cache',
                                         'info'	      => sprintf(__('If you enable Bypass Analytics feature %s will block the default Google Analytics script, and will use AJAX requests and the <a href="https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters" target="_blank">Google Analytics Measurement protocol</a> instead.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
					      ),
		                        array(
							'id'			=> 'ga-tracking-id',
							'type'		=> 'text',
							'title'		=> esc_html__('Tracking ID', 'swift-performance'),
		                            	'desc'	      => esc_html__('Eg: UA-123456789-12', 'swift-performance'),
		                            	'required'		=> array('bypass-ga', '=', 1),
						),
						array(
							'id'			=> 'ga-ip-source',
							'type'		=> 'dropdown',
							'title'		=> esc_html__('IP Source', 'swift-performance'),
							'desc'            => sprintf(esc_html__('Select IP source if your server is behind proxy (eg: Cloudflare). Recommended: %s', 'swift-performance'), $ga_ip_source),
                                          'info'	      => sprintf(__('If you are using reverse proxy (like Cloudflare) you will need to set the IP source for Google Analytics. Most cases %s will detect the proper IP source automatically.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
							'options'		=> array(
							     'HTTP_CLIENT_IP' => 'HTTP_CLIENT_IP',
							     'HTTP_X_FORWARDED_FOR' => 'HTTP_X_FORWARDED_FOR',
							     'HTTP_X_FORWARDED' => 'HTTP_X_FORWARDED',
							     'HTTP_X_CLUSTER_CLIENT_IP' => 'HTTP_X_CLUSTER_CLIENT_IP',
							     'HTTP_FORWARDED_FOR' => 'HTTP_FORWARDED_FOR',
							     'HTTP_FORWARDED' => 'HTTP_FORWARDED',
							     'HTTP_CF_CONNECTING_IP' => 'HTTP_CF_CONNECTING_IP',
							     'REMOTE_ADDR' => 'REMOTE_ADDR'
							),
		                              'default'    => $ga_ip_source,
		                              'required'   => array(
                                                array('bypass-ga', '=', 1),
                                                array('settings-mode', '=', 'advanced')
                                          ),
		                        ),
		                        array(
							'id'         => 'ga-anonymize-ip',
							'type'       => 'switch',
							'title'      => esc_html__('Anonymize IP', 'swift-performance'),
                                          'info'	 => __('In some cases, you might need to anonymize the user\'s IP address before it has been sent to Google Analytics. If you enable this option Google Analytics will anonymize the IP as soon as technically feasible at the earliest possible stage.', 'swift-performance'),
							'required'   => array('bypass-ga', '=', 1),
							'default'    => 0
		                        ),
		                        array(
							'id'			=> 'delay-ga-collect',
							'type'		=> 'switch',
							'title'		=> esc_html__('Delay Collect', 'swift-performance'),
							'desc'            => esc_html__('Send AJAX request only after the first user interaction', 'swift-performance'),
                                          'info'            => __('If you enable this option Google Analytics requests will be send only after the user made a mouse move, keypress or scroll event. It will speed up initial loading time, but be careful, bounce rate statistics may will be distorted', 'swift-performance'),
							'default'		=> 1,
                                          'class'		=> 'should-clear-cache',
                                          'required'		=> array(
                                                array('bypass-ga', '=', 1),
                                                array('settings-mode', '=', 'advanced')
                                          ),
		                        ),
		                        array(
							'id'			=> 'ga-exclude-roles',
							'type'		=> 'checkbox',
							'title'		=> esc_html__('Exclude Users from Statistics', 'swift-performance'),
							'desc'            => esc_html__('Exclude selected user roles from statistics', 'swift-performance'),
                                          'info'            => __('You can exclude logged in users from Analytics by user role. It can be extremely useful for smaller sites to see real stats, because editors won\'t affect the statistics when they check the site.', 'swift-performance'),
							'options'		=> $roles,
							'multiple'		=> true,
                                          'feature'         => 'ga_exclude_roles',
                                          'required'		=> array(
                                                array('bypass-ga', '=', 1),
                                                array('settings-mode', '=', 'advanced')
                                          ),
		                        ),
                                    array(
							'id'		=> 'cookies-disabled',
							'title'	=> __('Disable Cookies', 'swift-performance'),
							'type'	=> 'switch',
							'desc'	=> sprintf(__('You can prevent %s to create cookies on frontend.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
							'info'	=> sprintf(__('Regarding GDPR you can\'t use some cookies until the visitor approve them. In that case you can prevent %s to create these cookies by default, and use swift_performance_cookies-disabled filter to override this option. Please note that %s uses cookies for Google Analytics Bypass.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME, SWIFT_PERFORMANCE_PLUGIN_NAME),
                                          'default'   => 0,
                                          'required'		=> array(
                                                array('bypass-ga', '=', 1),
                                                array('settings-mode', '=', 'advanced')
                                          ),
						),
		                    )
		            ),
				'whitelabel' => array(
		                  'title' => esc_html__('Whitelabel', 'swift-performance'),
		                  'fields' => array(
                                    array(
                                         'id'			=> 'activate-whitelabel',
                                         'type'			=> 'switch',
                                         'title'      	=> esc_html__('Activate Whitelabel', 'swift-performance'),
                                         'desc'	            => esc_html__('Enable this option to fully activate whitelabel features. If you enabled this option, this section will be hidden after you save settings', 'swift-performance'),
                                         'info'             => __('If you would like to reenable this section just add the following line to your wp-config.php: <pre>define("SWIFT_PERFORMANCE_WHITELABEL", false);</pre>'),
                                         'default'		=> 0,
                                         'required'         => array('settings-mode', '=', 'advanced')
                                    ),
						array(
							'id'         => 'whitelabel-plugin-name',
							'type'       => 'text',
							'title'      => esc_html__('Plugin Name', 'swift-performance'),
                                          'desc'       => esc_html__('You can rename the plugin here.', 'swift-performance'),
							'default'    => 'Swift Performance',
                                          'required'   => array('activate-whitelabel', '=', 1),
		                        ),
		                        array(
		                             'id'         => 'whitelabel-plugin-slug',
		                             'type'       => 'text',
		                             'title'      => esc_html__('Plugin Slug', 'swift-performance'),
                                         'desc'       => esc_html__('You can override the plugin slug here.', 'swift-performance'),
		                             'default'    => 'swift-performance',
                                         'required'   => array('activate-whitelabel', '=', 1),
					      ),
						array(
		                             'id'		=> 'whitelabel-cache-basedir',
		                             'type'		=> 'text',
		                             'title'	=> esc_html__('Cache Basedir', 'swift-performance'),
		                             'desc'       => esc_html__('Basedir name in cache folder. If you not set it will use the plugin slug', 'swift-performance'),
		                             'default'	=> 'swift-performance',
                                         'required'   => array('activate-whitelabel', '=', 1),
		                        ),
		                        array(
		                             'id'		=> 'whitelabel-table-prefix',
		                             'type'		=> 'text',
		                             'title'	=> esc_html__('Table Prefix', 'swift-performance'),
                                         'desc'       => esc_html__('Prefix for database tables', 'swift-performance'),
		                             'default'	=> $wpdb->prefix . 'swift_performance_',
                                         'required'   => array('activate-whitelabel', '=', 1),
		                        ),
		                        array(
		                             'id'			=> 'whitelabel-plugin-desc',
		                             'type'			=> 'text',
		                             'title'		=> esc_html__('Plugin desc', 'swift-performance'),
		                             'desc'             => esc_html__('You can override the plugin desc here', 'swift-performance'),
		                             'default'		=> 'Boost your WordPress site',
                                         'required'   => array('activate-whitelabel', '=', 1),
		                        ),
		                        array(
		                             'id'			=> 'whitelabel-plugin-author',
		                             'type'			=> 'text',
		                             'title'		=> esc_html__('Plugin Author', 'swift-performance'),
		                             'desc'             => esc_html__('You can override the plugin author here', 'swift-performance'),
		                             'default'		=> 'SWTE',
                                         'required'   => array('activate-whitelabel', '=', 1),
		                        ),
		                        array(
		                             'id'			=> 'whitelabel-plugin-uri',
		                             'type'			=> 'text',
		                             'title'		=> esc_html__('Plugin Site', 'swift-performance'),
		                             'desc'             => esc_html__('You can override the plugin site here', 'swift-performance'),
		                             'default'		=> 'https://swiftperformance.io',
                                         'required'   => array('activate-whitelabel', '=', 1),
		                        ),
		                        array(
		                             'id'			=> 'whitelabel-plugin-author-uri',
		                             'type'			=> 'text',
		                             'title'      	=> esc_html__('Plugin Author URI', 'swift-performance'),
		                             'desc'	            => esc_html__('You can override the plugin author URI here', 'swift-performance'),
		                             'default'		=> 'https://swteplugins.com',
                                         'required'   => array('activate-whitelabel', '=', 1),
		                        ),
		                  )
				)
			)
		),
		'media' => array(
			'title' => esc_html__('Media', 'swift-performance'),
			'icon' => 'fas fa-images fa-image',
			'subsections' => array(
                        'general' => array(
                              'title' => esc_html__('General', 'swift-performance'),
                              'fields' => array(
                                    array(
                                          'id'        => 'smart-lazyload',
                                          'type'      => 'switch',
                                          'title'     => esc_html__('Smart Lazyload', 'swift-performance'),
                                          'desc'      => esc_html__('Exclude above the fold iframes/images (including background images) from lazyloading.', 'swift-performance'),
                                          'default'   => 0,
                                          'feature'   => 'smart_lazyload',
                                          'class'	=> 'should-clear-cache'
                                    ),
                              )
                        ),
				'media-images' => array(
					'title' => esc_html__('Images', 'swift-performance'),
					'fields' => array(
						array(
							'id'         => 'optimize-uploaded-images',
							'type'       => 'switch',
							'title'      => esc_html__('Optimize Images on Upload', 'swift-performance'),
							'desc'       => esc_html__('Enable if you would like to optimize the images during the upload using the our Image Optimization API service.', 'swift-performance'),
                                          'info'       => sprintf(__('Already uploaded images can be optimized %shere%s', 'swift-performance'), '<a href="'.esc_url(add_query_arg(array('page' => 'swift-performance', 'subpage' => 'image-optimizer'), admin_url('tools.php'))).'" target="_blank">', '</a>'),
							'default'    => 0,
							'required'   => array('purchase-key', 'NOT_EMPTY')
						),
                                    array(
							'id'        => 'scan-images',
							'type'      => 'dropdown',
							'title'	=> esc_html__('Image source', 'swift-performance'),
                                          'desc'      => esc_html__('Select which images should be found and optimized by Image Optimizer.', 'swift-performance'),
                                          'options'   => array(
 						           'media'     => esc_html__('Media Library', 'swift-performance'),
 						           'content-dir' => esc_html__('WP-CONTENT Directory', 'swift-performance'),
 						      ),
                                          'default'   => 'soft',
							'required'  => array(
                                                array('settings-mode', '=', 'advanced')
                                          ),
						),
                                    array(
							'id'         => 'image-optimizer-preset',
                                          'action'     => 'image-optimizer-preset',
							'type'       => 'custom',
							'title'      => esc_html__('Image Optimizer', 'swift-performance'),
							'desc'       => esc_html__('Set image quality for image optimizer', 'swift-performance'),
                                          'info'       => __('You can use preset, or fine tuning quality manually', 'swift-performance'),
                                          'default'    => 'lossless',
							'required'   => array('purchase-key', 'NOT_EMPTY')
						),
						array(
							'id'         => 'jpeg-quality',
							'type'       => 'slider',
							'min'        => 0,
							'max'        => 100,
							'title'      => esc_html__('JPEG quality', 'swift-performance'),
                                          'class'      => 'half-width',
							'default'    => 100,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('purchase-key', 'NOT_EMPTY')
                                          )
						),
						array(
							'id'         => 'png-quality',
							'type'       => 'slider',
							'min'        => 0,
							'max'        => 100,
							'title'      => esc_html__('PNG quality', 'swift-performance'),
                                          'class'      => 'half-width',
							'default'    => 100,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('purchase-key', 'NOT_EMPTY')
                                          )
						),
						array(
							'id'         => 'resize-large-images',
							'type'       => 'switch',
							'title'      => esc_html__('Resize Large Images', 'swift-performance'),
							'desc'       => esc_html__('Resize images which are larger than maximum width', 'swift-performance'),
                                          'info'       => __('If you don\'t need really big images, only web images you can resize uploaded images which are too large.', 'swift-performance'),
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('purchase-key', 'NOT_EMPTY')
                                          )
						),
						array(
							'id'         => 'maximum-image-width',
							'type'       => 'number',
							'title'      => esc_html__('Maximum Image Width', 'swift-performance'),
							'desc'   => esc_html__('Specify maximum image width (px)', 'swift-performance'),
							'default'    => '1920',
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('resize-large-images', '=', 1)
                                          )
						),
						array(
							'id'         => 'keep-original-images',
							'type'       => 'switch',
							'title'      => esc_html__('Keep Original Images', 'swift-performance'),
                                          'desc'       => esc_html__('If you enable this option the image optimizer will keep original images.', 'swift-performance'),
                                          'info'       => __('It is recommended to keep original images on first try. If you realized that optimized images quality is not good enough, you can restore original images with one click, and reoptimize them on higher quality.<br><br> I you would like to save some space, you can also delete easily original images if you are satisfied with the optimization quality.', 'swift-performance'),
							'default'    => 1,
							'required'   => array(
								array('purchase-key', 'NOT_EMPTY'),
							)
						),
                                    array(
                                          'id'         => 'webp',
                                          'type'       => 'switch',
                                          'title'      => esc_html__('Generate WebP', 'swift-performance'),
                                          'desc'       => esc_html__('If you enable this option the image optimizer will generate WEBP version for the images.', 'swift-performance'),
                                          'default'    => 1,
                                          'required'   => array(
                                                array('purchase-key', 'NOT_EMPTY'),
                                          )
                                    ),
                                    array(
                                          'id'         => 'serve-webp',
                                          'type'       => 'dropdown',
                                          'title'      => esc_html__('Serve WebP', 'swift-performance'),
                                          'desc'       => esc_html__('Serve WebP images if possible.', 'swift-performance'),
                                          'options'    => $swift_webp,
                                          'class'	 => 'should-clear-cache',
                                          'default'    => 'none',
                                    ),
                                    array(
                                          'id'         => 'serve-webp-background',
                                          'type'       => 'switch',
                                          'title'      => esc_html__('Serve WebP Background Images', 'swift-performance'),
                                          'desc'       => esc_html__('Serve background WebP images if WebP version is exists.', 'swift-performance'),
                                          'class'	 => 'should-clear-cache',
                                          'required'   => array(
                                                array('serve-webp', '=', 'picture'),
                                          ),
                                          'default'    => 0,
                                    ),
                                    array(
							'id'         => 'exclude-webp',
							'type'       => 'multi-text',
							'title'      => esc_html__('Exclude Images', 'swift-performance'),
							'desc'       => __('Exclude images from being converted to <picture> if one of these strings is found in the match.', 'swift-performance'),
                                          'class'	 => 'should-clear-cache',
                                          'required'   => array(
                                                array('serve-webp', '=', 'picture'),
                                          )
						),
                                    array(
                                          'id'         => 'webp-no-cache',
                                          'type'       => 'switch',
                                          'title'      => esc_html__('Disable Image Proxy Cache', 'swift-performance'),
                                          'desc'       => esc_html__('Prevent proxies to cache images', 'swift-performance'),
                                          'default'    => 1,
                                          'required'   => array(
                                                array('serve-webp', '=', 'rewrite'),
                                          )
                                    ),
                                    array(
						     'id'         => 'preload-images-by-url',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Preload Image by URL', 'swift-performance'),
						     'desc'       => esc_html__('Specified images will be preloaded if one of these strings is found in the match.', 'swift-performance'),
                                         'class'	=> 'should-clear-cache',
                                         'feature'    => 'pro_only',
                                         'required'   => array('settings-mode', '=', 'advanced'),
						),
                                    array(
						     'id'         => 'preload-images-by-class',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Preload Image by CSS classname', 'swift-performance'),
						     'desc'       => esc_html__('Specify images which should be preloaded if the image tag, or parent element has one of these classnames.', 'swift-performance'),
                                         'class'	 => 'should-clear-cache',
                                         'feature'    => 'pro_only',
                                         'required'   => array('settings-mode', '=', 'advanced'),
						),
						array(
							'id'        => 'lazy-load-images',
							'type'      => 'switch',
							'title'     => esc_html__('Lazyload Images', 'swift-performance'),
                                          'desc'      => esc_html__('Enable if you would like lazy load for images.', 'swift-performance'),
                                          'info'      => __('If you enable this option, images will be replaced with the selected placeholder, and only images in the viewport will be loaded fully.', 'swift-performance'),
							'default'   => 1,
                                          'class'	=> 'should-clear-cache'
						),
						array(
							'id'        => 'exclude-lazy-load',
							'type'      => 'multi-text',
							'title'	=> esc_html__('Exclude Images URL', 'swift-performance'),
                                          'desc'      => esc_html__('Exclude images from being lazy loaded if one of these strings is found in the match.', 'swift-performance'),
                                          'info'      => __('It is recommended to exclude logo, and other small images which are important for the design or the user experience.', 'swift-performance'),
							'required'  => array('lazy-load-images', '=', 1),
                                          'class'     => 'should-clear-cache'
						),
                                    array(
                                          'id'        => 'exclude-lazy-load-class',
                                          'type'      => 'multi-text',
                                          'title'	=> esc_html__('Exclude Images by CSS classname', 'swift-performance'),
                                          'desc'      => esc_html__('Exclude images from being lazy loaded if the image tag, or parent element has one of these classnames.', 'swift-performance'),
                                          'required'  => array('lazy-load-images', '=', 1),
                                          'class'     => 'should-clear-cache'
                                    ),
                                    array(
							'id'         => 'respect-lazyload-standards',
							'type'       => 'switch',
							'title'      => esc_html__('Respect Lazyload Standards', 'swift-performance'),
                                          'desc'       => __('Exclude images which has skip-lazy class or data-skip-lazy attribute', 'swift-performance'),
							'default'    => 1,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('lazy-load-images', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
							'id'         => 'lazy-load-images-preload-point',
							'type'       => 'number',
							'title'      => esc_html__('Preload Sensitivity', 'swift-performance'),
                                          'desc'       => esc_html__('Specify how many pixels before the viewport should be images loaded', 'swift-performance'),
							'default'    => 50,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('lazy-load-images', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'load-images-on-user-interaction',
							'type'       => 'switch',
							'title'      => esc_html__('Load Images on User Interaction', 'swift-performance'),
                                          'desc'       => esc_html__('Enable if you would like to load full images only on user interaction (mouse move, scroll, touchstart)', 'swift-performance'),
                                          'info'       => __('In most cases you won\'t need that feature, however if you already excluded manually images "above the fold" from lazy loading, you can enable this option.', 'swift-performance'),
							'default'    => 0,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('lazy-load-images', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'base64-lazy-load-images',
							'type'       => 'switch',
							'title'      => esc_html__('Inline Lazy Load Images', 'swift-performance'),
                                          'desc'       => esc_html__('Use base64 encoded inline images for lazy load', 'swift-performance'),
                                          'info'       => __('Regarding that the low quality version of images are pretty small files you can inline them instead load them separately. With this option you can reduce number of requests.', 'swift-performance'),
							'default'    => 1,
							'required'   => array('lazy-load-images', '=', 1),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
							'id'        => 'lazyload-images-placeholder',
							'type'      => 'dropdown',
							'title'	=> esc_html__('Lazyload Placeholder', 'swift-performance'),
                                          'desc'      => esc_html__('The selected placeholder will be loaded until the image is not fully loaded.', 'swift-performance'),
                                          'options'   => array(
 						           'blurred'     => esc_html__('Blurred', 'swift-performance'),
 						           'transparent' => esc_html__('Transparent', 'swift-performance'),
                                               'low-quality' => esc_html__('Low Quality', 'swift-performance'),
 						      ),
                                          'default'   => 'blurred',
							'required'  => array(
                                                array('lazy-load-images', '=', 1),
                                                array('settings-mode', '=', 'advanced')
                                          ),
                                          'class'     => 'should-clear-cache'
						),
                                    array(
                                          'id'        => 'lazyload-background-images',
                                          'type'      => 'switch',
                                          'title'     => esc_html__('Lazyload Background Images', 'swift-performance'),
                                          'desc'      => esc_html__('Enable if you would like lazy load for background images.', 'swift-performance'),
                                          'info'      => __('If you enable this option, background images will be blocked by default, and only elements in the viewport will load the background image.', 'swift-performance'),
                                          'default'   => 0,
                                          'class'	=> 'should-clear-cache',
                                          'required'   => array('settings-mode', '=', 'advanced')
                                    ),
                                    array(
							'id'         => 'fix-missing-image-dimensions',
							'type'       => 'switch',
							'title'      => esc_html__('Fix Missing Dimensions', 'swift-performance'),
							'desc'       => esc_html__('Add width and height attribute to <img> tag if it is missing', 'swift-performance'),
							'default'    => 0,
                                          'class'	 => 'should-clear-cache',
                                          'feature'    => 'fix_missing_image_dimensions',
                                          'required'   => array('settings-mode', '=', 'advanced')
						),
                                    array(
							'id'         => 'force-responsive-images',
							'type'       => 'switch',
							'title'      => esc_html__('Force Responsive Images', 'swift-performance'),
							'desc'       => esc_html__('Force all images to use srcset attribute if it is possible', 'swift-performance'),
                                          'info'       => __('You will need this option only if your theme (or some of your plugins) is using images incorrectly, which is very rare. If you enable this option it will append srcset for all images which has multiple sizes in media library.', 'swift-performance'),
							'default'    => 0,
                                          'class'	 => 'should-clear-cache',
                                          'required'   => array('settings-mode', '=', 'advanced')
						),
                                    array(
		                             'id'         => 'gravatar-cache',
		                             'type'       => 'switch',
		                             'title'      => esc_html__('Gravatar Cache', 'swift-performance'),
		                             'desc'       => esc_html__('Cache avatars.', 'swift-performance'),
                                         'info'       => __('WordPress is using Gravatar for avatars by default. Unfortunately sometimes these requests are slower than your server. In that case you should cache these pictures to speed up load time.', 'swift-performance'),
		                             'default'    => 0,
                                         'class'	=> 'should-clear-cache'
		                        ),
		                        array(
		                             'id'         => 'gravatar-cache-expiry',
		                             'type'       => 'dropdown',
		                             'title'      => esc_html__('Gravatar Cache Expiry', 'swift-performance'),
                                         'desc'       => esc_html__('Avatar cache expiry.', 'swift-performance'),
                                         'info'       => __('If Gravatar cache is enabled, and a user change his/her avatar it should be changed in cache as well. You can set expiry time for Gavatar images here. If an image expires it will be loaded from Gravatar again, so changes can be applied.', 'swift-performance'),
		                             'default'    => 3600,
                                         'options'    => array(
                                               3600         => esc_html__('1 hour', 'swift-performance'),
                                               43200        => esc_html__('12 hours', 'swift-performance'),
                                               86400        => esc_html__('1 day', 'swift-performance'),
                                               604800       => esc_html__('1 week', 'swift-performance'),
                                               2592000      => esc_html__('1 month', 'swift-performance'),
                                         ),
		                             'required'   => array('gravatar-cache', '=', 1),
		                        ),
                                    array(
                                          'id'         => 'base64-small-images',
                                          'type'       => 'switch',
                                          'title'      => esc_html__('Inline Small Images', 'swift-performance'),
                                          'desc'       => esc_html__('Use base64 encoded inline images for small images', 'swift-performance'),
                                          'info'       => __('If you enable this option small images will be inlined, so you can reduce the number of HTTP requests.', 'swift-performance'),
                                          'default'    => 0,
                                          'class'	 => 'should-clear-cache'
                                    ),
                                    array(
							'id'         => 'base64-small-images-size',
							'type'       => 'number',
							'title'      => esc_html__('File Size Limit (bytes)', 'swift-performance'),
                                          'desc'       => esc_html__('File size limit for inline images', 'swift-performance'),
							'default'    => '1000',
							'required'   => array('base64-small-images', '=', 1),
                                          'class'      => 'should-clear-cache'
						),
						array(
							'id'         => 'exclude-base64-small-images',
							'type'       => 'multi-text',
							'title'      => esc_html__('Exclude Images', 'swift-performance'),
							'desc'       => esc_html__('Exclude images from being embedded if one of these strings is found in the match.', 'swift-performance'),
							'required'   => array('base64-small-images', '=', 1),
                                          'class'	 => 'should-clear-cache'
						),
					)
				),
				array(
					'title' => esc_html__('Embeds', 'swift-performance'),
					'id' => 'media-embeds',
					'class'     => 'advanced',
					'fields' => array(
                                    array(
							'id'         	=> 'smart-youtube-embed',
							'type'       	=> 'switch',
							'title'      	=> esc_html__('Youtube Smart Embed', 'swift-performance'),
							'desc'            => esc_html__('Load Youtube videos only on user interaction.', 'swift-performance'),
                                          'info'            => __('Load only thumbnail image for Youtube videos with a pseudo play button, and load the video and the player only on click/touch.', 'swift-performance'),
							'default'    	=> 0,
                                          'class'		=> 'should-clear-cache',
                                          'feature'         => 'youtube_smart_embed',
                                          'required'        => array('settings-mode', '=', 'advanced')
						),
                                    array(
							'id'			=> 'exclude-youtube-embed',
							'type'		=> 'multi-text',
							'title'		=> esc_html__('Exclude Youtube Videos', 'swift-performance'),
                                          'desc'            => esc_html__('Exclude videos from being smart embedded if one of these strings is found in the match.', 'swift-performance'),
                                          'info'            => __('If you have an autoplay video, you can exclude it with this option.', 'swift-performance'),
							'required'		=> array('smart-youtube-embed', '=', 1),
                                          'class'		=> 'should-clear-cache'
						),
                                    array(
							'id'         => 'smart-youtube-preload-point',
							'type'       => 'number',
							'title'      => esc_html__('Preload Sensitivity', 'swift-performance'),
                                          'desc'       => esc_html__('Specify how many pixels before the viewport should be youtube videos loaded on mobile devices', 'swift-performance'),
							'default'    => 50,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('smart-youtube-embed', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         	=> 'lazyload-iframes',
							'type'       	=> 'switch',
							'title'      	=> esc_html__('Lazy Load Iframes', 'swift-performance'),
							'desc'            => esc_html__('Enable if you would like lazy load for iframes.', 'swift-performance'),
                                          'info'            => __('Some embedded content (like Youtube videos) loads additional assets which are not necessary on initial pageload. You can lazyload them, so iframes will be loaded only before they arrives in the viewport.', 'swift-performance'),
							'default'    	=> 0,
                                          'class'		=> 'should-clear-cache',
                                          'required'        => array('settings-mode', '=', 'advanced')
						),
						array(
							'id'			=> 'exclude-iframe-lazyload',
							'type'		=> 'multi-text',
							'title'		=> esc_html__('Exclude Iframes by URL', 'swift-performance'),
                                          'desc'            => esc_html__('Exclude iframes from being lazy loaded if one of these strings is found in the match.', 'swift-performance'),
                                          'info'            => __('Unfortunately some iframes can be broken if they are lazyloaded. You can exclude them with this option.', 'swift-performance'),
							'required'		=> array('lazyload-iframes', '=', 1),
                                          'class'		=> 'should-clear-cache'
						),
                                    array(
                                          'id'        => 'exclude-iframe-lazyload-class',
                                          'type'      => 'multi-text',
                                          'title'	=> esc_html__('Exclude Iframes by CSS classname', 'swift-performance'),
                                          'desc'      => esc_html__('Exclude iframes from being lazy loaded if it has one of these classnames.', 'swift-performance'),
                                          'required'  => array('lazyload-iframes', '=', 1),
                                          'class'     => 'should-clear-cache'
                                    ),
                                    array(
							'id'         => 'respect-iframe-lazyload-standards',
							'type'       => 'switch',
							'title'      => esc_html__('Respect Lazyload Standards', 'swift-performance'),
                                          'desc'       => __('Exclude iframes which has skip-lazy class or data-skip-lazy attribute', 'swift-performance'),
							'default'    => 1,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('lazyload-iframes', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
							'id'         => 'lazyload-iframes-preload-point',
							'type'       => 'number',
							'title'      => esc_html__('Preload Sensitivity', 'swift-performance'),
                                          'desc'       => esc_html__('Specify how many pixels before the viewport should be iframes loaded', 'swift-performance'),
							'default'    => 50,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('lazyload-iframes', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'load-iframes-on-user-interaction',
							'type'       => 'switch',
							'title'      => esc_html__('Load Iframes on User Interaction', 'swift-performance'),
                                          'desc'       => esc_html__('Enable if you would like to load iframes only on user interaction (mouse move, scroll, touchstart)', 'swift-performance'),
                                          'info'       => __('If you don\'t have any iframes in the "above the fold" section you can load them very last, when the page was fully loaded and the user made some interactions as well. It does\'t only speed up the page load, but also can save some bandwidth if it is important (eg for mobile users).', 'swift-performance'),
							'default'    => 0,
							'required'   => array('lazyload-iframes', '=', 1),
                                          'class'	 => 'should-clear-cache'
						),
					)
				),
			)
		),
		'optimization' => array(
			'title' => esc_html__('Optimization', 'swift-performance'),
			'icon' => 'fas fa-magic',
			'subsections' => array(
				'general' => array(
		                   'title' => esc_html__('General', 'swift-performance'),
		                   'id' => 'asset-manager-general',
		                   'fields' => array(
		                        array(
							'id'         => 'server-push',
							'type'       => 'switch',
							'title'      => esc_html__('Enable Server Push', 'swift-performance'),
                                          'desc'       => esc_html__('Server push allows you to send site assets to the browser before it has even asked for them', 'swift-performance'),
							'default'    => 0,
							'required'   => array('enable-caching', '=', 1),
                                          'class'	 => 'should-clear-cache'
		                        ),
		                        array(
							'id'         => 'optimize-prebuild-only',
		                              'type'       => 'switch',
		                              'title'      => esc_html__('Optimize Prebuild Only', 'swift-performance'),
		                              'desc'       => esc_html__('In some cases optimizing the page takes some time. If you enable this option the plugin will optimize the page, only when prebuild cache process is running.', 'swift-performance'),
                                          'info'       => __('It is recommended to use this option, to prevent very long pageloads for the first visit (when the page is not cached yet)', 'swift-performance'),
		                              'default'    => 0,
		                              'required'   => array('enable-caching', '=', 1)
		                        ),
		                        array(
							'id'         => 'merge-background-only',
							'type'       => 'switch',
							'title'      => esc_html__('Optimize in Background', 'swift-performance'),
                                          'desc'       => esc_html__('In some cases optimizing the page takes some time. If you enable this option the plugin will optimize page in the background.', 'swift-performance'),
                                          'info'       => __('It is recommended to use this option, to prevent very long pageloads for the first visit (when the page is not cached yet)', 'swift-performance'),
							'default'    => 0,
							'required'   => array('enable-caching', '=', 1)
						),
                                    array(
							'id'         => 'optimize-404',
							'type'       => 'switch',
							'title'      => esc_html__('Optimize 404 pages', 'swift-performance'),
                                          'desc'       => esc_html__('Enable if you would like to optimize 404 pages', 'swift-performance'),
                                          'info'       => __('You may cache 404 pages, but usually you don\'t need to optimize them. You can save server resources if you keep this option disabled.', 'swift-performance'),
							'default'    => 0,
							'required'   => array(
                                                array('enable-caching', '=', 1),
                                                array('optimize-prebuild-only', '!=', 1)
                                          )
						),
                                    array(
		                             'id'		=> 'prebuild-booster',
		                             'type'		=> 'switch',
		                             'title'	=> esc_html__('Prebuild Booster', 'swift-performance'),
		                             'desc'       => sprintf(esc_html__('If you enable this option %s will use less resources during prebuild.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                                         'info'		=> __('If you enable this option it will reduce CPU usage and speed up prebuild process, however it can increase MySQL binlogs if it is enabled.', 'swift-performance'),
		                             'default'	=> 1,
		                             'required'	=> array(
                                               array('purchase-key', 'NOT_EMPTY'),
                                               array('settings-mode', '=', 'advanced')
                                         )
		                        ),
						array(
							'id'         => 'disable-emojis',
							'type'       => 'switch',
							'title'	 => esc_html__('Disable Emojis', 'swift-performance'),
                                          'desc'	 => esc_html__('Prevent WordPress to load emojis', 'swift-performance'),
                                          'info'       => __('Most sites are not using emojis at all, however WordPress is loading it by default. If you disable it you can decrease the number of requests and page size as well. ', 'swift-performance'),
							'default'    => 0,
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'	=> 'limit-threads',
							'type'	=> 'switch',
							'title' => esc_html__('Limit Simultaneous Threads', 'swift-performance'),
							'desc' => esc_html__('Limit maximum simultaneous threads. It can be useful on shared hosting environment to avoid 508 errors.', 'swift-performance'),
							'default' => 0
						),
						array(
							'id'         => 'max-threads',
							'type'       => 'number',
							'title'	=> esc_html__('Maximum Threads', 'swift-performance'),
							'desc'   => esc_html__('Number of maximum simultaneous threads.', 'swift-performance'),
							'default'    => 3,
							'required'   => array('limit-threads', '=', 1),
						),
                                    array(
                                         'id'		=> 'merge-assets-logged-in-users',
                                         'type'	=> 'switch',
                                         'title'	=> esc_html__('Merge Assets for Logged in Users', 'swift-performance'),
                                         'desc'      => esc_html__('Enable if you would like to merge styles and scripts for logged in users as well.', 'swift-performance'),
                                          'info'      => __('It is recommended to enable this option only if the site is using action based cache or the cache is cleared very rarely. Otherwise it will optimize for logged in users in real time, which can damage the user experience.', 'swift-performance'),
                                         'default'	=> 0,
                                          'required'   => array('settings-mode', '=', 'advanced')
                                   ),
                                   array(
                                        'id'         => 'dns-prefetch',
                                        'type'       => 'switch',
                                        'title'      => esc_html__('Prefetch DNS', 'swift-performance'),
                                        'desc'       => esc_html__('Prefetch DNS automatically.', 'swift-performance'),
                                        'info'       => __('DNS prefetching will resolve domain names before a user tries to follow a link, or before assets were loaded. It can decrease full load time, and also speed up outgoing links.', 'swift-performance'),
                                        'default'    => 1,
                                        'class'	=> 'should-clear-cache',
                                        'required'   => array('settings-mode', '=', 'advanced')
                                   ),
                                   array(
                                        'id'         => 'dns-prefetch-js',
                                        'type'       => 'switch',
                                        'title'      => esc_html__('Collect domains from scripts', 'swift-performance'),
                                        'desc'       => esc_html__('Collect domains from scripts for DNS Prefetch.', 'swift-performance'),
                                        'info'       => sprintf(__('If this option is enabled, %s will collect 3rd party domain names from javascript files as well. If it isn\'t enabled, it will collect domains only from HTML and CSS.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                                        'default'    => 0,
                                        'required'   => array('dns-prefetch', '=', 1),
                                        'class'      => 'should-clear-cache'
                                   ),
                                   array(
                                        'id'         => 'exclude-dns-prefetch',
                                        'type'       => 'multi-text',
                                        'title'	=> esc_html__('Exclude DNS Prefetch', 'swift-performance'),
                                        'desc'       => esc_html__('Exclude domains from DNS prefetch.', 'swift-performance'),
                                        'info'       => __('If you would like to prevent DNS prefetch for a domain you can add it here ', 'swift-performance'),
                                        'required'   => array('dns-prefetch', '=', 1),
                                        'class'	=> 'should-clear-cache'
                                   ),
                                   array(
                                        'id'			=> 'normalize-static-resources',
                                        'type'			=> 'switch',
                                        'title'		     => esc_html__('Normalize Static Resources', 'swift-performance'),
                                        'desc'             => esc_html__('Remove unnecessary query string from CSS, JS and image files.', 'swift-performance'),
                                        'default'     => 0,
                                            'class'		=> 'should-clear-cache'
                                   ),
					)
				),
				'scripts' => array(
					'title' => esc_html__('Scripts', 'swift-performance'),
					'fields' => array(
						array(
							'id'         => 'merge-scripts',
							'type'       => 'switch',
							'title'	 => esc_html__('Merge Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('Merge javascript files to reduce number of HTTP requests ', 'swift-performance'),
                                          'info'       => __('Merging scripts can reduce number of requests dramatically. Even if your server is using HTTP2 it can speed up the page loading, and also save some resources on server side (because the server needs to serve less requests).', 'swift-performance'),
							'default'    => 0,
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'async-scripts',
							'type'       => 'switch',
							'title'	 => esc_html__('Async Execute', 'swift-performance'),
                                          'desc'       => esc_html__('Execute merged javascript files asynchronously', 'swift-performance'),
                                          'info'       => sprintf(__('If you merged all scripts, even the first one can run, only when the full merged script was loaded. However if you enable this option, %s will split the merged script on client side on the fly and run each scripts when that part was loaded. It can speed up rendering time which is an important factor for user experience. ', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
							      array('merge-scripts', '=', 1),
							),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
							'id'         => 'script-delivery',
							'type'       => 'dropdown',
							'title'	 => esc_html__('Script Delivery', 'swift-performance'),
                                          'desc'       => esc_html__('Choose javascript delivery mode', 'swift-performance'),
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
							      array('merge-scripts', '=', 1),
                                                array('async-scripts', '=', 1),
							),
                                          'options'    => array(
                                                'simple' => esc_html__('Simple', 'swift-performance'),
                                                'delay'  => esc_html__('Delay Async Scripts', 'swift-performance'),
                                                'smart'  => esc_html__('Smart Delivery', 'swift-performance'),
                                          ),
                                          'default'    => 'simple',
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'merge-scripts-exlude-3rd-party',
							'type'       => 'switch',
							'title'	 => esc_html__('Exclude 3rd Party Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('Exclude 3rd party scripts from merged scripts', 'swift-performance'),
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-scripts', '=', 1)
                                          ),
							'default'    => 0,
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'exclude-scripts',
							'type'       => 'multi-text',
							'title'	 => esc_html__('Exclude Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('Exclude scripts from being merged if one of these strings is found in the match.', 'swift-performance'),
							'required'   => array('merge-scripts', '=', 1),
                                          'class'	 => 'should-clear-cache',
						),
						array(
							'id'         => 'footer-scripts',
							'type'       => 'multi-text',
							'title'	 => esc_html__('Footer Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('Exclude scripts from being merged and move them to footer, if one of these strings is found in the match.', 'swift-performance'),
                                          'info'       => __('It can be useful if you would like to exclude a script which is using a dependency from the merged scripts. For example, if jQuery is merged, but you want to exclude a script which is using jQuery.', 'swift-performance'),
							'required'   => array('merge-scripts', '=', 1),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
							'id'         => 'defer-scripts',
							'type'       => 'multi-text',
							'title'	 => esc_html__('Deferred Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('Exclude scripts from being merged but set defer attribute for them, if one of these strings is found in the match.', 'swift-performance'),
							'required'   => array('merge-scripts', '=', 1),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'exclude-inline-scripts',
							'type'       => 'multi-text',
							'title'	=> esc_html__('Exclude Inline Scripts', 'swift-performance'),
							'desc'   => esc_html__('Exclude scripts from being merged if one of these strings is found in the match.', 'swift-performance'),
							'required'   => array('merge-scripts', '=', 1),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'footer-inline-scripts',
							'type'       => 'multi-text',
							'title'	=> esc_html__('Footer Inline Scripts', 'swift-performance'),
							'desc'   => esc_html__('Exclude scripts from being merged and move them to footer, if one of these strings is found in the match.', 'swift-performance'),
							'required'   => array('merge-scripts', '=', 1),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'exclude-script-localizations',
							'type'       => 'switch',
							'title'	 => esc_html__('Exclude Script Localizations', 'swift-performance'),
                                          'desc'       => esc_html__('Exclude javascript localizations from merged scripts.', 'swift-performance'),
                                          'info'       => __('It is recommended to exclude script localizations, because they can increase the merged script\'s loading time, but there is no real benefit to including them. Please note that option will exclude all inline scripts which contains [[CDATA]]', 'swift-performance'),
							'default'    => 1,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-scripts', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'minify-scripts',
							'type'       => 'switch',
							'title'	=> esc_html__('Minify Javascripts', 'swift-performance'),
							'default'    => 1,
							'required'   => array('merge-scripts', '=', 1),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'use-script-compute-api',
							'type'       => 'switch',
							'title'	 => esc_html__('Minify with API', 'swift-performance'),
                                          'desc'       => esc_html__('Use Compute API for minify. Regarding that this minify method can be slower, use this option only if default JS minify cause javascript errors. ', 'swift-performance'),
                                          'info'       => __('Some scripts are not fully valid, but still operational (eg: missing semicolon). These scripts can cause issues when you minify them. If you use the API for script minify it can fix this parsing errors, but please note it will a bit slowdown the minifing process.', 'swift-performance'),
							'default'    => 0,
							'required'   => array(
                                                array('purchase-key', 'NOT_EMPTY'),
                                                array('settings-mode', '=', 'advanced'),
							      array('merge-scripts', '=', 1),
							      array('minify-scripts', '=', 1),
							),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'proxy-3rd-party-assets',
							'type'       => 'switch',
							'title'	=> esc_html__('Proxy 3rd Party Assets', 'swift-performance'),
							'desc'	=> esc_html__('Proxy 3rd party javascript and CSS files which created by javascript (eg: Google Analytics)', 'swift-performance'),
							'default'    => 0,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-scripts', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'include-3rd-party-assets',
							'type'       => 'multi-text',
							'title'	=> esc_html__('3rd Party Assets', 'swift-performance'),
							'desc'   => esc_html__('List scripts (full URL) which should being proxied.', 'swift-performance'),
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('proxy-3rd-party-assets', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
                                          'id'         => 'script-safe-mode',
                                          'type'       => 'switch',
                                          'title'	 => esc_html__('Safe Mode', 'swift-performance'),
                                          'desc'       => esc_html__('Prevent fail merged scirpt on JS error', 'swift-performance'),
                                          'info'       => __('If you enable this option, all scripts will be added to the merged script within a try-catch block', 'swift-performance'),
                                          'default'    => 0,
                                          'class'	 => 'should-clear-cache',
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-scripts', '=', 1)
                                          ),
                                    ),
						array(
							'id'         => 'separate-js',
							'type'       => 'switch',
							'title'	 => esc_html__('Separate Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('If you enable this option the plugin will save merged JS files for pages separately', 'swift-performance'),
							'default'    => 0,
							'required'   => array('merge-scripts', '=', 1),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'inline-merged-scripts',
							'type'       => 'switch',
							'title'	=> esc_html__('Print merged scripts inline', 'swift-performance'),
							'desc'   => esc_html__('Enable if you would like to print merged scripts into the footer, instead of a seperated file.', 'swift-performance'),
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-scripts', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
						array(
							'id'         => 'lazy-load-scripts',
							'type'       => 'multi-text',
							'title'	 => esc_html__('Lazy Load Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('Load scripts only after first user interaction, if one of these strings is found in the match.', 'swift-performance'),
                                          'info'       => __('With this feature you can be sure that included scripts will be loaded very last, and won\'t delay rendering process.', 'swift-performance'),
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-scripts', '=', 1),
                                                array('script-delivery', '!=', 'delay')
                                          ),
                                          'class'	 => 'should-clear-cache',
						),
						array(
							'id'         => 'include-scripts',
							'type'       => 'multi-text',
							'title'	 => esc_html__('Include Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('Include scripts manually. With this option you can preload script files what are loaded with javascript', 'swift-performance'),
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-scripts', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
							'id'         => 'block-scripts',
							'type'       => 'multi-text',
							'title'	 => esc_html__('Block Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('With this option you can block script files', 'swift-performance'),
                                          'info'       => esc_html__('This feature can be useful if you bypass a script functionality with a better solution.', 'swift-performance'),
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-scripts', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
                                          'id'         => 'disable-jquery-migrate',
                                          'type'       => 'switch',
                                          'title'	 => esc_html__('Disable jQuery Migrate', 'swift-performance'),
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                          ),
                                          'class'	 => 'should-clear-cache'
                                    ),
                                    array(
						     'id'         => 'preload-scripts',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Preload Scripts', 'swift-performance'),
						     'desc'       => esc_html__('Specify scripts which should be preloaded.', 'swift-performance'),
                                         'class'	 => 'should-clear-cache',
                                         'required'   => array('settings-mode', '=', 'advanced'),
						),
                                    array(
                                          'id'         => 'preprocess-scripts',
                                          'type'       => 'multi-text',
                                          'title'	 => esc_html__('Preprocess Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('Run scripts on server side if one of these strings is found in the match.', 'swift-performance'),
                                          'info'       => esc_html__('These scripts, and its dependencies will be executed on server side as well.', 'swift-performance'),
                                          'required'   => array('settings-mode', '=', 'advanced'),
                                          'class'	 => 'should-clear-cache',
                                          'feature'    => 'preprocess_scripts',
                                    ),
                                    array(
                                          'id'         => 'preprocess-inline-scripts',
                                          'type'       => 'multi-text',
                                          'title'	 => esc_html__('Preprocess Inline Scripts', 'swift-performance'),
                                          'desc'       => esc_html__('Run inline scripts on server side if one of these strings is found in the match.', 'swift-performance'),
                                          'required'   => array('settings-mode', '=', 'advanced'),
                                          'class'	 => 'should-clear-cache',
                                          'feature'    => 'preprocess_scripts',
                                    ),
                                    array(
                                         'id'         => 'extra-javascript',
                                         'type'       => 'editor',
                                         'title'	=> esc_html__('Custom Header JavaScript', 'swift-performance'),
                                         'desc'       => esc_html__('You can add extra JavaScript to header', 'swift-performance'),
                                         'info'       => __('If some function is not working properly with Async execute, Lazyload- elements or scripts you can add JS code snippets to fix it.', 'swift-performance'),
                                         'mode'       => 'javascript',
                                         'theme'    => 'monokai',
                                         'required'   => array(
                                              array('settings-mode', '=', 'advanced'),
                                          ),
                                          'class'	 => 'should-clear-cache'
                                    ),
                                    array(
                                         'id'         => 'extra-javascript-footer',
                                         'type'       => 'editor',
                                         'title'	=> esc_html__('Custom Footer JavaScript', 'swift-performance'),
                                         'desc'       => esc_html__('You can add extra JavaScript to footer', 'swift-performance'),
                                         'info'       => __('If some function is not working properly with Async execute, Lazyload- elements or scripts you can add JS code snippets to fix it.', 'swift-performance'),
                                         'mode'       => 'javascript',
                                         'theme'    => 'monokai',
                                         'required'   => array(
                                              array('settings-mode', '=', 'advanced'),
                                          ),
                                          'class'	 => 'should-clear-cache'
                                    ),
                                    array(
                                          'id'         => 'server-side-script',
                                          'type'       => 'editor',
                                          'mode'       => 'javascript',
                                          'theme'      => 'monokai',
                                          'title'	 => esc_html__('Server Side Script', 'swift-performance'),
                                          'desc'       => esc_html__('Run arbitary javascript on API', 'swift-performance'),
                                          'info'       => esc_html__('You can manipulate DOM with javascript, which can be executed on API to manipulate the generated DOM', 'swift-performance'),
                                          'class'	 => 'should-clear-cache',
                                          'feature'    => 'server_side_script',
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
							),
                                    ),
					)
				),
				'styles' => array(
		                  'title' => esc_html__('Styles', 'swift-performance'),
		                  'fields' => array(
						array(
						     'id'         => 'merge-styles',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Merge Styles', 'swift-performance'),
                                         'desc'       => esc_html__('Merge CSS files to reduce number of HTTP requests', 'swift-performance'),
                                         'info'       => __('Merging styles can reduce number of requests dramatically. Even if your server is using HTTP2 it can speed up the page loading, and also save some resources on server side (because the server needs to serve less requests).', 'swift-performance'),
						     'default'    => 0,
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'critical-css',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Generate Critical CSS', 'swift-performance'),
                                         'info'       => __('Critical CSS is an extract of the full CSS, which contains only that rules which are necessary to render the site on the initial load.', 'swift-performance'),
						     'default'    => 1,
						     'required'   => array('merge-styles', '=', 1),
                                         'class'	 => 'should-clear-cache'
						),
                                    array(
						     'id'         => 'critical-css-mode',
						     'type'       => 'dropdown',
						     'title'	=> esc_html__('Critical CSS method', 'swift-performance'),
                                         'desc'       => esc_html__('Choose which method would you like to use to generate Critical CSS.', 'swift-performance'),
                                         'info'       => __('Unused CSS mode will find unused CSS and remove it from Critical CSS. Viewport based mode will load only those rules, which are necessary to render "Above the fold" content. Please note, if you select Viewport based mode the full HTML will be sent to the API which may contains personal data. If it does you may have to include this in your privacy policy.', 'swift-performance'),
						     'default'    => 'v1',
						     'required'   => array(
                                               array('critical-css', '=', 1),
                                               array('use-compute-api', '=', 1),
                                         ),
                                         'options'    => array(
                                              'v1'      => esc_html__('Unused CSS', 'swift-performance'),
                                              'v2'      => esc_html__('Viewport based', 'swift-performance'),
                                         ),
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'extra-critical-css',
						     'type'       => 'editor',
						     'title'	=> esc_html__('Extra Critical CSS', 'swift-performance'),
                                         'desc'       => esc_html__('You can add extra CSS to Critical CSS here', 'swift-performance'),
                                         'info'       => __('If you would like to add some custom CSS rules to Critical CSS you can add them here.', 'swift-performance'),
						     'mode'       => 'css',
						     'theme'    => 'monokai',
						     'required'   => array(
						          array('merge-styles', '=', 1),
						          array('critical-css', '=', 1),
						      ),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
						     'id'         => 'extra-css',
						     'type'       => 'editor',
						     'title'	=> esc_html__('Extra CSS', 'swift-performance'),
                                         'desc'       => esc_html__('You can add extra rules to full CSS here', 'swift-performance'),
                                         'info'       => __('If some styling is not correct you can add CSS rules here to fix it.', 'swift-performance'),
						     'mode'       => 'css',
						     'theme'    => 'monokai',
						     'required'   => array(
						          array('settings-mode', '=', 'advanced'),
                                              array('merge-styles', '=', 1),
						      ),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
						     'id'         => 'load-full-css-on-scroll',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Load Full CSS On Scroll', 'swift-performance'),
                                         'desc'       => esc_html__('Load Full CSS only when user start scrolling.', 'swift-performance'),
                                         'info'       => __('Regarding that Critical CSS is enough to render the above the fold section, it is enough to load Full CSS only when the user start scrolling.', 'swift-performance'),
						     'required'   => array(
                                               array('inline_full_css', '!=', 1),
						           array('critical-css', '=', 1),
                                               array('critical-css-mode', '=', 'v2'),
						     ),
						     'default'    => 0,
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'disable-full-css',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Disable Full CSS', 'swift-performance'),
                                         'desc'       => esc_html__('Load Critical CSS only. Be careful, it may can cause styling issues.', 'swift-performance'),
                                         'info'       => __('On simple sites, which are using only a few modifications on the loaded site you can totally disable the full CSS. If you would like to use this, please be careful, and test all pages. If something is missing you can add them with Extra Critical CSS.', 'swift-performance'),
						     'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
						           array('merge-styles', '=', 1),
						           array('critical-css', '=', 1),
                                               array('critical-css-mode', '=', 'v1'),
						     ),
						     'default'    => 0,
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'compress-css',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Compress Critical CSS', 'swift-performance'),
                                         'desc'       => esc_html__('Extra compress for critical CSS', 'swift-performance'),
                                         'info'       => sprintf(__('If you enable this feature, %s will change all class names and ids in the critical CSS to a shorter one, so you can save some extra bytes.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
						     'default'    => 0,
						     'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-styles', '=', 1),
                                                array('critical-css', '=', 1),
                                                array('critical-css-mode', '=', 'v1'),
                                                array('disable-full-css', '=', 0),
						     ),
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'remove-keyframes',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Remove Keyframes', 'swift-performance'),
                                         'desc'       => esc_html__('Remove CSS animations from critical CSS', 'swift-performance'),
						     'default'    => 0,
						     'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
						           array('merge-styles', '=', 1),
						           array('critical-css', '=', 1),
                                               array('critical-css-mode', '=', 'v1'),
						     ),
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'inline_critical_css',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Print critical CSS inline', 'swift-performance'),
						     'desc'   => esc_html__('Enable if you would like to print the critical CSS into the header, instead of a seperated CSS file.', 'swift-performance'),
						     'required'   => array(
						           array('merge-styles', '=', 1),
						           array('critical-css', '=', 1),
						     ),
						     'default'    => 1,
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'inline_full_css',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Print full CSS inline', 'swift-performance'),
						     'desc'   => esc_html__('Enable if you would like to print the merged CSS into the footer, instead of a seperated CSS file.', 'swift-performance'),
                                         'info'       => __('Please note that this is a special feature only for special cases. If WordPress can write files on the server you shouldn\'t use this option, even if page speed scores are better, because with this you will prevent the browser to cache the CSS. and it will be downloaded each time when the visitor is navigating on your site.', 'swift-performance'),
						     'required'   => array('merge-styles', '=', 1),
						     'default'    => 0,
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'separate-css',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Separate Styles', 'swift-performance'),
						     'desc'   => esc_html__('If you enable this option the plugin will save merged CSS files for pages separately', 'swift-performance'),
						     'default'    => 0,
						     'required'   => array('merge-styles', '=', 1),
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'minify-css',
						     'type'       => 'dropdown',
						     'title'	=> esc_html__('Minify CSS', 'swift-performance'),
                                         'desc'       => esc_html__('Remove unnecessary whitespaces, shorten color codes and font weights', 'swift-performance'),
						     'default'    => 1,
						     'options'    => array(
						           0      => esc_html__('Don\'t minify', 'swift-performance'),
						           1      => esc_html__('Basic', 'swift-performance'),
						           2      => esc_html__('Full', 'swift-performance'),
						     ),
						     'required'   => array('merge-styles', '=', 1),
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'bypass-css-import',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Bypass CSS Import', 'swift-performance'),
                                         'desc'       => esc_html__('Include imported CSS files in merged styles.', 'swift-performance'),
						     'default'    => 1,
						     'required'   => array('merge-styles', '=', 1),
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'merge-styles-exclude-3rd-party',
						     'type'       => 'switch',
						     'title'	=> esc_html__('Exclude 3rd Party CSS', 'swift-performance'),
						     'desc'   => esc_html__('Exclude 3rd party CSS files (eg: Google Fonts CSS) from merged styles', 'swift-performance'),
						     'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('merge-styles', '=', 1)
                                         ),
						     'default'    => 0,
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'exclude-styles',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Exclude Styles', 'swift-performance'),
						     'desc'   => esc_html__('Exclude style from being merged if one of these strings is found in the file name. ', 'swift-performance'),
						     'required'   => array('merge-styles', '=', 1),
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'exclude-inline-styles',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Exclude Inline Styles', 'swift-performance'),
						     'desc'   => esc_html__('Exclude style from being merged if one of these strings is found in CSS. ', 'swift-performance'),
						     'required'   => array('merge-styles', '=', 1),
                                         'class'	 => 'should-clear-cache'
						),
						array(
						     'id'         => 'include-styles',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Include Styles', 'swift-performance'),
						     'desc'   => esc_html__('Include styles manually. With this option you can preload css files what are loaded with javascript', 'swift-performance'),
						     'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('merge-styles', '=', 1)
                                         ),
                                         'class'	 => 'should-clear-cache'
						),
                                    array(
						     'id'         => 'preload-styles',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Preload Styles', 'swift-performance'),
						     'desc'       => esc_html__('Specify styles which should be preloaded.', 'swift-performance'),
                                         'class'	 => 'should-clear-cache',
                                         'required'   => array('settings-mode', '=', 'advanced'),
						),
					)
				),
                        'fonts' => array(
		                  'title' => esc_html__('Fonts', 'swift-performance'),
		                  'fields' => array(
                                    array(
							'id'         => 'preload-fonts',
							'type'       => 'switch',
							'title'	 => esc_html__('Preload Fonts Automatically', 'swift-performance'),
							'desc'	 => esc_html__('Preload fonts automatically', 'swift-performance'),
							'default'    => 0,
                                          'class'	 => 'should-clear-cache',
                                          'feature'    => 'pro_only',
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-styles', '=', 1)
                                          )
						),
                                    array(
						     'id'         => 'exclude-preload-fonts',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Exclude Preload Fonts', 'swift-performance'),
						     'desc'   => esc_html__('Exclude font file from being preloaded. ', 'swift-performance'),
						     'required'   => array(
                                               array('preload-fonts', '=', 1),
                                               array('settings-mode', '=', 'advanced')
                                         ),
                                         'class'	 => 'should-clear-cache'
						),
                                    array(
						     'id'         => 'manual-preload-fonts',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Manual Preload Fonts', 'swift-performance'),
						     'desc'   => esc_html__('Preload fonts manually. ', 'swift-performance'),
                                         'feature'    => 'pro_only',
						     'required'   => array(
                                               array('settings-mode', '=', 'advanced')
                                         ),
                                         'class'	 => 'should-clear-cache'
						),
                                    array(
							'id'         => 'local-fonts',
							'type'       => 'switch',
							'title'	 => esc_html__('Use Local Fonts', 'swift-performance'),
							'desc'	 => esc_html__('Download and host fonts locally', 'swift-performance'),
							'default'    => 0,
                                          'class'	 => 'should-clear-cache',
                                          'feature'    => 'local_fonts',
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-styles', '=', 1)
                                          )
						),
                                    array(
						     'id'         => 'exclude-local-fonts',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Exclude Local Fonts', 'swift-performance'),
						     'desc'   => esc_html__('Exclude font file from being hosted locally. ', 'swift-performance'),
						     'required'   => array(
                                               array('local-fonts', '=', 1),
                                               array('settings-mode', '=', 'advanced')
                                         ),
                                         'class'	 => 'should-clear-cache'
						),
                                    array(
							'id'         => 'font-display',
							'type'       => 'switch',
							'title'	 => esc_html__('Force Swap Font Display', 'swift-performance'),
							'desc'	 => esc_html__('Set font-display property to swap', 'swift-performance'),
                                          'info'       => sprintf(__('The font-display property defines how font files are loaded and displayed by the browser. Swap instructs the browser to use the fallback font to display the text until the custom font has fully downloaded to avoid "flash of invisible text" (FOIT).', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
							'default'    => 0,
                                          'feature'    => 'font_display_swap',
                                          'class'	 => 'should-clear-cache',
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('merge-styles', '=', 1)
                                          )
						),
                                    array(
						     'id'         => 'exclude-font-display',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Exclude Force Swap Font Display', 'swift-performance'),
						     'desc'   => esc_html__('Exclude font family from being forced to use swap font-display. ', 'swift-performance'),
						     'required'   => array(
                                               array('font-display', '=', 1),
                                               array('settings-mode', '=', 'advanced')
                                         ),
                                         'class'	 => 'should-clear-cache'
						),
                              )
                        ),
                        'html' => array(
                              'title' => esc_html__('HTML', 'swift-performance'),
                              'fields' => array(
                                    array(
                                          'id'         => 'smart-render-html',
                                          'type'       => 'switch',
                                          'title'	 => esc_html__('Smart Render', 'swift-performance'),
                                          'desc'       => esc_html__('Delay rendering parts which are not present in above the fold content', 'swift-performance'),
                                          'info'       => __('Automatically set content-visibility, and set the size of the placeholder for containers which are not present in above the fold content.', 'swift-performance'),
                                          'default'    => 0,
                                          'feature'    => 'smart_render_html',
                                          'class'	 => 'should-clear-cache'
                                    ),
                                    array(
						     'id'         => 'exclude-smart-render',
						     'type'       => 'multi-text',
						     'title'	=> esc_html__('Exclude from Smart Render HTML', 'swift-performance'),
						     'desc'       => esc_html__('Specify CSS selectors to exclude elements from Smart Render HTML', 'swift-performance'),
						     'required'   => array(
                                               array('smart-render-html', '=', 1),
                                               array('settings-mode', '=', 'advanced')
                                         ),
                                         'class'	 => 'should-clear-cache'
						),
                                    array(
							'id'         => 'html-auto-fix',
							'type'       => 'switch',
							'title'	 => esc_html__('Fix Invalid HTML', 'swift-performance'),
							'desc'	 => esc_html__('Try to fix invalid HTML', 'swift-performance'),
                                          'info'       => sprintf(__('Sometimes themes and plugins contain invalid HTML, which doesn\'t cause issues in browser, because the browser can fix it on the fly, but it can cause issues with the DOM parser. If you enable this option %s will fix these issues automatically like the modern browsers does.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
							'default'    => 1,
                                          'class'	 => 'should-clear-cache',
                                          'required'   => array('settings-mode', '=', 'advanced')
						),
						array(
							'id'         => 'minify-html',
							'type'       => 'switch',
							'title'	 => esc_html__('Minify HTML', 'swift-performance'),
                                          'desc'	 => esc_html__('Remove unnecessary whitespaces from HTML', 'swift-performance'),
							'default'    => 0,
                                          'class'	 => 'should-clear-cache'
						),
                              )
                        )
			)
		),
		'caching' => array(
			'title'		=> esc_html__('Caching', 'swift-performance'),
			'icon'		=> 'fas fa-bolt',
			'subsections'	=> array(
				'cache' => array(
		                   'title' => esc_html__('General', 'swift-performance'),
		                   'fields' => array(
		                         array(
		                               'id'         => 'enable-caching',
		                               'type'	  => 'switch',
		                               'title'      => esc_html__('Enable Caching', 'swift-performance'),
		                               'default'    => 1,
                                           'class'	 => 'should-clear-cache'
		                         ),
		                         array(
		                               'id'                => 'caching-mode',
		                               'type'              => 'dropdown',
		                               'title'	         => esc_html__('Caching Mode', 'swift-performance'),
		                               'options'           => $cache_modes,
		                               'default'           => 'disk_cache_php',
		                               'required'          => array('enable-caching', '=', 1),
		                               'validate_callback' => 'swift_performance_cache_mode_validate_callback',
                                           'info'              => __('If rewrites are working on your server you always should use Disk cache with Rewrites, this is the fastest method for serving cache.', 'swift-performance'),
		                         ),
		                         array(
		                                'id'	      => 'memcached-host',
		                                'type'	=> 'text',
		                                'title'	=> esc_html__('Memcached Host', 'swift-performance'),
		                                'default'   => 'localhost',
		                                'required'  => array(
		                                      array('caching-mode', '=', 'memcached_php'),
		                                      array('enable-caching', '=', 1)
		                                ),
		                         ),
		                         array(
		                                'id'	      => 'memcached-port',
		                                'type'	=> 'text',
		                                'title'	=> esc_html__('Memcached Port', 'swift-performance'),
		                                'default'   => '11211',
		                                'required'  => array(
		                                      array('caching-mode', '=', 'memcached_php'),
		                                      array('enable-caching', '=', 1)
		                                ),
		                         ),
		                         array(
		                                'id'	=> 'early-load',
		                                'type'	=> 'switch',
		                                'title'	=> esc_html__('Early Loader', 'swift-performance'),
		                                'desc'	=> sprintf(esc_html__('Use %s Loader mu-plugin ', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                                            'info'    => sprintf(__('If %s have to serve the cache with PHP it will speed up the process. Please note that some requests will be served with PHP even if you choose the Disk with Rewrites caching mode.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
		                                'default'   => 1,
		                                'required'  => array(
		                                      array('enable-caching', '=', 1)
		                                ),
		                                'validate_callback' => 'swift_performance_muplugins_validate_callback',
		                         ),
		                         array(
		                                'id'	=> 'cache-path',
		                                'type'	=> 'text',
		                                'title'	=> esc_html__('Cache Path', 'swift-performance'),
		                                'default'   => WP_CONTENT_DIR . '/cache/',
		                                'required'  => array(
		                                      array('caching-mode', 'contains', 'disk_cache'),
		                                      array('enable-caching', '=', 1)
		                                ),
		                                'validate_callback' => 'swift_performance_cache_path_validate_callback',
		                         ),
		                         array(
		                              'id'         => 'cache-expiry-mode',
		                              'type'       => 'dropdown',
		                              'title'	     => esc_html__('Cache Expiry Mode', 'swift-performance'),
		                              'required'   => array('enable-caching', '=', 1),
		                              'options'    => array(
		                                    'timebased'   => esc_html__('Time based mode', 'swift-performance'),
		                                    'actionbased' => esc_html__('Action based mode', 'swift-performance'),
		                              ),
		                              'default'    => 'timebased',
                                          'info'      => sprintf(__('It is recommended to use Action based mode. %s will clear the cache if the content was modified (post update, new post, new comment, comment approved, stock changed, etc). However if the site is using nonce or any other thing what can expire, you should choose the Time based expiry mode.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
		                         ),
		                         array(
		                              'id'       => 'cache-expiry-time',
                                          'type'     => 'dropdown',
		                              'title'    => esc_html__('Cache Expiry Time', 'swift-performance'),
		                              'desc'     => esc_html__('Clear cached pages after specified time', 'swift-performance'),
		                              'options'  => array(
		                                      '1800'      => '30 mins',
		                                      '3600'      => '1 hour',
		                                      '7200'      => '2 hours',
		                                      '21600'     => '6 hours',
		                                      '28800'     => '8 hours',
		                                      '36000'     => '10 hours',
		                                      '43200'     => '12 hours',
		                                      '86400'     => '1 day',
		                                      '172800'    => '2 days'
		                              ),
		                              'default' => '43200',
		                              'required'  => array('cache-expiry-mode', '=', 'timebased')
		                        ),
		                        array(
		                                'id'	      => 'cache-garbage-collection-time',
		                                'type'	=> 'dropdown',
		                                'title'	=> esc_html__('Garbage Collection Interval', 'swift-performance'),
		                                'desc'  => esc_html__('How often should check the expired cached pages', 'swift-performance'),
		                                'options'   => array(
		                                      '600'       => '10 mins',
		                                      '1800'      => '30 mins',
		                                      '3600'      => '1 hour',
		                                      '7200'      => '2 hours',
		                                      '21600'     => '6 hours',
		                                      '43200'     => '12 hours',
		                                      '86400'     => '1 day',
		                                ),
		                                'default'   => '1800',
		                                'required'  => array('cache-expiry-mode', '=', 'timebased')
		                         ),
                                     array(
                                        'id'         => 'extend-nonce-life',
                                        'type'       => 'switch',
                                        'title'      => esc_html__('Extend Nonce Life', 'swift-performance'),
                                         'desc'       => esc_html__('Extend nonce life to cache expiry time.', 'swift-performance'),
                                         'info'       => __('You can extend the default lifespan of WordPress nonce to keep nonces valid during the cache lifespan.', 'swift-performance'),
                                         'default'    => 0,
                                         'class'	 => 'should-refresh',
                                         'feature'    => 'pro_only',
                                        'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('cache-expiry-mode', '=', 'timebased'),
                                               array('enable-caching', '=', 1)
                                         ),
                                    ),
                                     array(
                                        'id'         => 'bypass-nonce',
                                        'type'       => 'switch',
                                        'title'      => esc_html__('Bypass Nonce', 'swift-performance'),
                                         'desc'       => esc_html__('Bypass nonce for not logged in users.', 'swift-performance'),
                                         'info'       => __('Unfortunately a lot of plugins and themes are using nonces on the frontend for not logged in users without a valid reason. With this option you can bypass nonces for not logged in users.', 'swift-performance'),
                                         'default'    => 0,
                                         'feature'    => 'pro_only',
                                        'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('cache-expiry-mode', '=', 'actionbased'),
                                               array('enable-caching', '=', 1)
                                         ),
                                    ),
                                     array(
		                             'id'         => 'short-lifespan-pages',
		                             'type'       => 'dropdown',
		                             'multiple'      => true,
		                             'title'      => esc_html__('Short Lifespan Pages', 'swift-performance'),
                                         'desc'       => esc_html__('Select pages where cache should be cleared after 12 hours.', 'swift-performance'),
                                         'info'       => __('Some pages may contains nonces which can expire in 12 hours. You can specify these pages here, they will be cleared in every 12 hours, even if you are using Action Based mode.', 'swift-performance'),
		                             'options'    => $pages,
                                         'feature'    => 'pro_only',
		                             'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('cache-expiry-mode', '=', 'actionbased'),
                                               array('enable-caching', '=', 1)
                                         ),
		                         ),
		                         array(
		                             'id'         => 'clear-page-cache-after-post',
		                             'type'       => 'dropdown',
		                             'multiple'      => true,
		                             'title'      => esc_html__('Clear Cache on Update Post by Page', 'swift-performance'),
                                         'desc'       => esc_html__('Select pages where cache should be cleared after publish/update post.', 'swift-performance'),
                                         'info'       => __('It is useful if your site is using for example a WooCommerce shortcode to show products on homepage. Because it is a shortcode homepage cache won\'t be cleared automatically if a post/stock/comment was updated, however you can specify pages manually here.', 'swift-performance'),
		                             'options'    => $pages,
		                             'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('enable-caching', '=', 1)
                                         ),
		                         ),
		                         array(
		                             'id'         => 'clear-permalink-cache-after-post',
		                             'type'       => 'multi-text',
		                             'title'      => esc_html__('Clear Cache on Update Post by URL', 'swift-performance'),
		                             'desc'   => esc_html__('Set URLs where cache should be cleared after publish/update post.', 'swift-performance'),
                                         'info'       => __('It is useful if your site is using for example a WooCommerce shortcode to show products on homepage. Because it is a shortcode homepage cache won\'t be cleared automatically if a post/stock/comment was updated, however you can specify URLs manually here.', 'swift-performance'),
                                         'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('enable-caching', '=', 1)
                                         ),
		                         ),
                                     array(
		                             'id'         => 'clear-cache-updater',
		                             'type'       => 'switch',
		                             'title'      => esc_html__('Clear Cache After Update', 'swift-performance'),
                                         'desc'       => esc_html__('Clear all cache after core/plugin/theme has been updated.', 'swift-performance'),
                                         'info'       => sprintf(__('If this option is disabled %s will show a notice to clear cache.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                                         'default'    => 0,
		                             'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('enable-caching', '=', 1)
                                         ),
		                         ),
		                         array(
		                             'id'          => 'enable-caching-logged-in-users',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Enable Caching for logged in users', 'swift-performance'),
		                             'desc'    => esc_html__('This option can increase the total cache size, depending on the count of your users.', 'swift-performance'),
		                             'default'     => 0,
		                             'required'    => array('enable-caching', '=', 1),
		                         ),
		                         array(
		                             'id'          => 'shared-logged-in-cache',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Shared Logged in Cache', 'swift-performance'),
		                             'desc'    => esc_html__('If you enable this option logged in users won\'t have separate private cache, but they will get content from public cache', 'swift-performance'),
		                             'default'     => 0,
		                             'required'    => array(
		                                   array('enable-caching', '=', 1),
		                                   array('enable-caching-logged-in-users', '=', 1),
		                             ),
                                         'class'	 => 'should-clear-cache'
		                         ),
		                         array(
		                             'id'          => 'mobile-support',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Separate Mobile Device Cache', 'swift-performance'),
		                             'desc'    => esc_html__('You can create separate cache for mobile devices, it can be useful if your site not just responsive, but it has a separate mobile theme/layout (eg: AMP). ', 'swift-performance'),
		                             'default'     => 0,
		                             'required'    => array('enable-caching', '=', 1),
                                         'class'	 => 'should-clear-cache'
		                         ),
		                         array(
		                             'id'          => 'browser-cache',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Enable Browser Cache', 'swift-performance'),
		                             'desc'    => esc_html__('If you enable this option it will generate htacess/nginx rules for browser cache. (Expire headers should be configured on your server as well)', 'swift-performance'),
		                             'default'     => 1,
		                             'required'   => array('enable-caching', '=', 1),
		                         ),
		                         array(
		                             'id'          => 'enable-gzip',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Enable Gzip', 'swift-performance'),
		                             'desc'    => esc_html__('If you enable this option it will generate htacess/nginx rules for gzip compression. (Compression should be configured on your server as well)', 'swift-performance'),
		                             'default'     => 1,
		                             'required'   => array('enable-caching', '=', 1),
		                         ),
		                         array(
		                             'id'          => '304-header',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Send 304 Header', 'swift-performance'),
		                             'default'     => 0,
                                         'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('enable-caching', '=', 1)
                                         ),
		                         ),
		                         array(
		                             'id'          => 'cache-404',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Cache 404 pages', 'swift-performance'),
		                             'default'     => 0,
		                             'required'   => array('enable-caching', '=', 1),
		                         ),
                                     array(
                                         'id'          => 'cache-sitemap',
                                         'type'        => 'switch',
                                         'title'       => esc_html__('Cache Sitemap', 'swift-performance'),
                                         'default'     => 0,
                                         'required'   => array('enable-caching', '=', 1),
                                     ),
		                         array(
		                             'id'          => 'dynamic-caching',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Enable Dynamic Caching', 'swift-performance'),
		                             'desc'    => esc_html__('If you enable this option you can specify cacheable $_GET and $_POST requests', 'swift-performance'),
		                             'default'     => 0,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-clear-cache'
		                         ),
		                         array(
		                             'id'         => 'cacheable-dynamic-requests',
		                             'type'       => 'multi-text',
		                             'title'      => esc_html__('Cacheable Dynamic Requests', 'swift-performance'),
		                             'desc'   => esc_html__('Specify $_GET and/or $_POST keys what should be cached. Eg: "s" to cache search requests', 'swift-performance'),
		                             'required'   => array('dynamic-caching', '=', 1),
                                         'class'	 => 'should-clear-cache'
		                         ),
		                         array(
		                             'id'         => 'cacheable-ajax-actions',
		                             'type'       => 'multi-text',
		                             'title'      => esc_html__('Cacheable AJAX Actions', 'swift-performance'),
		                             'desc'   => esc_html__('With this option you can cache resource-intensive AJAX requests', 'swift-performance'),
		                             'required'   => array('enable-caching', '=', 1),
		                         ),
		                         array(
		                             'id'         => 'ajax-cache-expiry-time',
		                             'type'	    => 'number',
		                             'title'	    => esc_html__('AJAX Cache Expiry Time', 'swift-performance'),
		                             'desc'   => esc_html__('Cache expiry time for AJAX requests in seconds', 'swift-performance'),
		                             'default'    => '1440',
		                             'required'   => array('enable-caching', '=', 1),
		                        ),
		            	)
		            ),
                        'tweaks' => array(
		                  'title' => esc_html__('Tweaks', 'swift-performance'),
		                  'fields' => array(
                                    array(
                                      'id'            => 'proxy-cache',
                                      'type'          => 'switch',
                                      'title'         => esc_html__('Enable Proxy Cache', 'swift-performance'),
                                      'desc'          => esc_html__('Enable proxy cache for pages.', 'swift-performance'),
                                      'info'          => __('With this option you can add s-maxage header to force proxies (eg Cloudflare) to cache pages. <br><br><b>PLEASE NOTE:</b> On Cloudflare only Enterprise plan allows to bypass cache by cookies, so by default if you enable this option, logged in users will get cached pages as well (like when you enable <i>Shared Logged in Cache</i>).', 'swift-performance'),
                                      'default'       => 0,
                                      'feature'    => 'pro_only',
                                      'required'   => array(
                                           array('settings-mode', '=', 'advanced'),
                                      ),
                                    ),
                                    array(
                                      'id'            => 'proxy-cache-maxage',
                                      'type'          => 'number',
                                      'title'         => esc_html__('Proxy Cache Maxage', 'swift-performance'),
                                      'desc'          => esc_html__('Set max-age for proxies (in seconds)', 'swift-performance'),
                                      'default'       => 84600,
                                      'required'   => array(
                                           array('settings-mode', '=', 'advanced'),
                                           array('proxy-cache', '=', 1),
                                      ),
                                    ),
                                    array(
                                      'id'            => 'proxy-cache-only',
                                      'type'          => 'switch',
                                      'title'         => esc_html__('Proxy Cache Only', 'swift-performance'),
                                      'desc'          => esc_html__('Keep cached files only on proxy server', 'swift-performance'),
                                      'info'          => esc_html__('If you enable Proxy Cache Only mode cached content won\'t be stored on your server, but on proxy server. You can save storage with this option.', 'swift-performance'),
                                      'default'       => 0,
                                      'class'	 => 'should-clear-cache',
                                      'required'   => array(
                                           array('settings-mode', '=', 'advanced'),
                                           array('proxy-cache', '=', 1),
                                      ),
                                    ),
                                    array(
                                        'id'          => 'ignore-specific-parameters',
                                        'type'        => 'multi-text',
                                        'title'       => esc_html__('Ignore GET Params', 'swift-performance'),
                                        'desc'        => esc_html__('Ignore specific GET parameters for caching', 'swift-performance'),
                                        'info'        => __('You can specify GET parameters which should be ignored for caching. The following ones are ignored by default: utm_source, utm_campaign, utm_medium, utm_expid, utm_term, utm_content, fb_action_ids, fb_action_types, fb_source, fbclid, _ga, gclid, age-verified', 'swift-performance'),
                                        'class'	      => 'should-clear-cache',
                                        'required'   => array(
                                              array('settings-mode', '=', 'advanced'),
                                              array('enable-caching', '=', 1)
                                        ),
                                    ),
                                    array(
                                        'id'          => 'avoid-mixed-content',
                                        'type'        => 'switch',
                                        'title'       => esc_html__('Avoid Mixed Content', 'swift-performance'),
                                        'desc'        => esc_html__('Remove protocol from resource URLs to avoid mixed content errors', 'swift-performance'),
                                        'info'       => __('If your site can be loaded via HTTP and HTTPS as well it can cause mixed content errors. If you enable this option it will remove the protocol from all resources to avoid it. Use it only on HTTPS sites.', 'swift-performance'),
                                        'default'     => 1,
                                        'feature'    => 'avoid_mixed_content',
                                        'required'   => array('enable-caching', '=', 1),
                                        'class'	 => 'should-clear-cache'
                                    ),
                                    array(
                                        'id'          => 'keep-original-headers',
                                        'type'        => 'switch',
                                        'title'       => esc_html__('Keep Original Headers', 'swift-performance'),
                                        'desc'        => esc_html__('Send original headers for cached pages', 'swift-performance'),
                                        'info'       => __('If you are using a plugin which send custom headers you can keep them for the cached version as well.', 'swift-performance'),
                                        'default'     => 1,
                                        'feature'    => 'keep_original_headers',
                                        'required'    => array(
                                              array('enable-caching', '=', 1),
                                        ),
                                        'class'       => 'should-clear-cache'
                                    ),
                                    array(
                                        'id'          => 'exclude-original-headers',
                                        'type'        => 'multi-text',
                                        'title'       => esc_html__('Exclude Headers', 'swift-performance'),
                                        'desc'        => esc_html__('Don\'t keep these headers', 'swift-performance'),
                                        'info'        => __('You can specify headers which shouldn\'t be kept for the cached version', 'swift-performance'),
                                        'class'	      => 'should-clear-cache',
                                        'required'   => array(
                                              array('settings-mode', '=', 'advanced'),
                                              array('keep-original-headers', '=', 1)
                                        ),
                                    ),
                                    array(
                                        'id'          => 'cache-case-insensitive',
                                        'type'        => 'switch',
                                        'title'       => esc_html__('Case Insensitive URLs', 'swift-performance'),
                                        'desc'    => esc_html__('Convert URLs to lower case for caching', 'swift-performance'),
                                        'default'     => 0,
                                        'required'   => array(
                                              array('settings-mode', '=', 'advanced'),
                                              array('enable-caching', '=', 1)
                                        ),
                                    ),
                                    array(
                                          'id'         => 'caching-strict-host',
                                          'type'	  => 'switch',
                                          'title'      => esc_html__('Strict Host', 'swift-performance'),
                                          'desc'       => sprintf(esc_html__('Strict Host mode prevent generate cache for different hosts', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                                          'info'       => __('With Strict Host mode you can prevent www/non-www related issues, or generating cache for unused but served domains.', 'swift-performance'),
                                          'default'    => 1,
                                          'class'       => 'should-clear-cache',
                                          'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('enable-caching', '=', 1)
                                          ),
                                    ),
		                  )
		            ),
                        'ajaxify' => array(
                              'title'     => esc_html__('Ajaxify', 'swift-performance'),
                              'fields'    => array(
                                    array(
                                        'id'          => 'lazyload-shortcode',
                                        'type'        => 'multi-text',
                                        'title'       => esc_html__('Lazyload Shortcodes', 'swift-performance'),
                                        'desc'        => esc_html__('Specify shortcodes which you would like to lazyload', 'swift-performance'),
                                        'info'        => __('You can specify shortcodes (eg: featured_products) to lazyload elements on the page. These elements will be loaded via AJAX after the page loaded. It can be useful for elements which can\'t be cached and should be loaded dynamically, like related products, recently view products, most popular posts, recent comments etc.', 'swift-performance'),
                                        'class'	      => 'should-clear-cache',
                                        'feature'    => 'pro_only',
                                        'required'   => array(
                                              array('settings-mode', '=', 'advanced'),
                                              array('enable-caching', '=', 1)
                                        ),
                                    ),
                                    array(
                                        'id'          => 'lazyload-blocks',
                                        'type'        => 'switch',
                                        'title'       => esc_html__('Lazyload Blocks', 'swift-performance'),
                                        'desc'        => esc_html__('Enable to be able to set Gutenberg blocks to be lazyloaded', 'swift-performance'),
                                        'info'        => __('If you enable this option, every Gutenberg blocks will get a new option to lazyload them. These blocks will be loaded via AJAX after the page loaded. It can be useful for elements which can\'t be cached and should be loaded dynamically, like related products, recently view products, most popular posts, recent comments etc.', 'swift-performance'),
                                        'class'	      => 'should-clear-cache',
                                        'feature'    => 'pro_only',
                                        'required'   => array(
                                              array('settings-mode', '=', 'advanced'),
                                              array('enable-caching', '=', 1)
                                        ),
                                    ),
                                    array(
                                        'id'          => 'lazyload-widgets',
                                        'type'        => 'dropdown',
                                        'multiple'    => true,
                                        'title'       => esc_html__('Lazyload Widgets', 'swift-performance'),
                                        'desc'        => esc_html__('Select widgets which should be lazyloaded', 'swift-performance'),
                                        'options'     => array(),
                                        'class'	      => 'should-clear-cache',
                                        'feature'    => 'pro_only',
                                        'required'   => array(
                                              array('settings-mode', '=', 'advanced'),
                                              array('enable-caching', '=', 1)
                                        ),
                                    ),
                                    array(
							'id'         => 'ajaxify-preload-point',
							'type'       => 'number',
							'title'      => esc_html__('Preload Sensitivity', 'swift-performance'),
                                          'desc'       => esc_html__('Specify how many pixels before the viewport should be lazyloaded elements loaded', 'swift-performance'),
							'default'    => 50,
							'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('enable-caching', '=', 1)
                                          ),
                                          'class'	 => 'should-clear-cache'
						),
                                    array(
                                        'id'          => 'ajaxify-placeholder',
                                        'type'        => 'dropdown',
                                        'title'       => esc_html__('Ajaxify Placeholder', 'swift-performance'),
                                        'desc'        => esc_html__('Choose placeholder effect for lazyloaded elements.', 'swift-performance'),
                                        'info'        => __('You can choose how should look like lazyloaded elements before they will be loaded. You can hide them, blur them, or show the cached version until they load.', 'swift-performance'),
                                        'class'       => 'should-clear-cache',
                                        'options'     => array(
                                              'cached'      => esc_html__('Show cached', 'swift-performance'),
                                              'blur'        => esc_html__('Blurred', 'swift-performance'),
                                              'hidden'      => esc_html__('Hidden', 'swift-performance'),
                                        ),
                                        'default'     => 'blur',
                                        'required'    => array(
                                              array('settings-mode', '=', 'advanced'),
                                              array('enable-caching', '=', 1)
                                        ),
                                    ),
                                    array(
                                        'id'          => 'ajaxify',
                                        'type'        => 'multi-text',
                                        'title'       => esc_html__('Lazyload elements', 'swift-performance'),
                                        'desc'        => esc_html__('Specify CSS selectors (depricated)', 'swift-performance'),
                                        'info'        => __('You can specify CSS selectors (eg: #related-products or .last-comments) to lazyload elements on the page. These elements will be loaded via AJAX after the page loaded. It can be useful for elements which can\'t be cached and should be loaded dynamically, like related products, recently view products, most popular posts, recent comments etc.', 'swift-performance'),
                                        'class'	      => 'should-clear-cache',
                                        'feature'    => 'pro_only',
                                        'required'   => array(
                                              array('settings-mode', '=', 'advanced'),
                                              array('enable-caching', '=', 1)
                                        ),
                                    ),
                              )
                        ),
				'exceptions' => array(
		                   'title' => esc_html__('Exceptions', 'swift-performance'),
		                   'fields' => array(
		                         array(
		                             'id'         => 'exclude-post-types',
		                             'type'       => 'dropdown',
		                             'multiple'   => true,
		                             'title'      => esc_html__('Exclude Post Types', 'swift-performance'),
		                             'desc'       => esc_html__('Select post types which shouldn\'t be cached.', 'swift-performance'),
		                             'required'   => array('enable-caching', '=', 1),
		                             'options'    => $post_types,
                                         'class'	=> 'should-reset-warmup'
		                         ),
		                         array(
		                             'id'         => 'exclude-pages',
		                             'type'       => 'dropdown',
		                             'multiple'   => true,
		                             'title'      => esc_html__('Exclude Pages', 'swift-performance'),
		                             'desc'   => esc_html__('Select pages which shouldn\'t be cached.', 'swift-performance'),
		                             'required'   => array('enable-caching', '=', 1),
		                             'options'    => $pages,
                                         'class'	 => 'should-reset-warmup'
		                         ),
		                         array(
		                             'id'         => 'exclude-strings',
		                             'type'       => 'multi-text',
		                             'title'      => esc_html__('Exclude URLs', 'swift-performance'),
		                             'desc'   => esc_html__('URLs which contains that string won\'t be cached. Use leading/trailing # for regex', 'swift-performance'),
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-reset-warmup'
		                         ),
                                     array(
                                         'id'         => 'exclude-cookies',
                                         'type'       => 'multi-text',
                                         'title'      => esc_html__('Exclude Cookies', 'swift-performance'),
                                         'desc'   => esc_html__('Cache will be bypassed if the user has one of these cookies.', 'swift-performance'),
                                         'required'   => array('enable-caching', '=', 1),
                                     ),
		                         array(
		                             'id'         => 'exclude-content-parts',
		                             'type'       => 'multi-text',
		                             'title'      => esc_html__('Exclude Content Parts', 'swift-performance'),
		                             'desc'   => esc_html__('Pages which contains that string won\'t be cached. Use leading/trailing # for regex', 'swift-performance'),
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-reset-warmup'
		                         ),
		                         array(
		                             'id'         => 'exclude-useragents',
		                             'type'       => 'multi-text',
		                             'title'      => esc_html__('Exclude User Agents', 'swift-performance'),
		                             'desc'   => esc_html__('User agents which contains that string won\'t be cached. Use leading/trailing # for regex', 'swift-performance'),
		                             'required'   => array('enable-caching', '=', 1),
		                         ),
		                         array(
		                             'id'          => 'exclude-crawlers',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Exclude Crawlers', 'swift-performance'),
		                             'desc'    => esc_html__('Exclude known crawlers from cache', 'swift-performance'),
		                             'default'     => 0,
		                             'required'   => array('enable-caching', '=', 1),
		                         ),
		                         array(
		                             'id'          => 'exclude-author',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Exclude Author Pages', 'swift-performance'),
		                             'default'     => 1,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-reset-warmup'
		                         ),
		                         array(
		                             'id'          => 'exclude-archive',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Exclude Archive', 'swift-performance'),
		                             'default'     => 0,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-reset-warmup'
		                         ),
		                         array(
		                             'id'          => 'exclude-rest',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Exclude REST URLs', 'swift-performance'),
		                             'default'     => 1,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-reset-warmup'
		                         ),
		                         array(
		                             'id'          => 'exclude-feed',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Exclude Feed', 'swift-performance'),
		                             'default'     => 1,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-reset-warmup'
		                        ),
		                  )
		            ),
				'warmup' => array(
		                   'title' => esc_html__('Warmup', 'swift-performance'),
		                   'fields' => array(
		                         array(
		                             'id'          => 'automated_prebuild_cache',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Prebuild Cache Automatically', 'swift-performance'),
		                             'desc'    => esc_html__('This option will prebuild the cache after it was cleared', 'swift-performance'),
		                             'default'     => 0,
		                         ),
		                         array(
		                             'id'          => 'prebuild-speed',
		                             'type'        => 'dropdown',
		                             'title'       => esc_html__('Prebuild Speed', 'swift-performance'),
		                             'desc'    => esc_html__('You can limit prebuild speed. It is recommended to use on limited shared hosting.', 'swift-performance'),
		                             'default'     => 5,
		                             'options'     => array(
                                               -1 => __('Multi thread', 'swift-performance'),
		                                   0  => __('Unlimited', 'swift-performance'),
		                                   5  => __('Moderate', 'swift-performance'),
		                                   20 => __('Reduced', 'swift-performance'),
		                                   40 => __('Slow', 'swift-performance'),
		                             ),
		                             'required'   => array('automated_prebuild_cache', '=', 1),
		                         ),
		                         array(
		                             'id'          => 'discover-warmup',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Discover New Pages', 'swift-performance'),
		                             'desc'    => esc_html__('Let the plugin to discover new pages for warmup (eg: pagination, plugin-created pages, etc)', 'swift-performance'),
		                             'default'     => 0,
                                         'feature'    => 'discover',
                                         'required'   => array('settings-mode', '=', 'advanced')
		                         ),
                                     array(
		                             'id'          => 'warmup-table-source',
		                             'type'        => 'dropdown',
		                             'title'       => esc_html__('Warmup Table Source', 'swift-performance'),
		                             'desc'        => sprintf(__('You can use URL list, sitemap, define pages manually, or let %s to build Warmup Table', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                                         'options'       => array(
                                             'auto'      => esc_html__('Auto', 'swift-performance'),
                                             'url-list'  => esc_html__('URL list', 'swift-performance'),
                                             'sitemap'   => esc_html__('Sitemap', 'swift-performance'),
                                             'manual'    => esc_html__('Add pages manually', 'swift-performance'),
                                          ),
                                         'default'       => 'auto',
                                         'class'	 => 'should-reset-warmup',
                                         'required'   => array('settings-mode', '=', 'advanced')
		                         ),
                                     array(
 		                             'id'         => 'warmup-table-url-list',
 		                             'type'       => 'editor',
 		                             'title'	=> esc_html__('URL List', 'swift-performance'),
 		                             'desc'       => sprintf(esc_html__('Define Warmup Table by URL list. One URL per line', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
 		                             'mode'       => 'text',
 		                             'theme'      => 'monokai',
                                         'class'	=> 'should-reset-warmup',
                                         'required'   => array('warmup-table-source', '=', 'url-list')
 		                         ),
                                     array(
                                         'id'          => 'warmup-sitemap',
                                         'type'        => 'url',
                                         'title'       => esc_html__('Sitemap URL', 'swift-performance'),
                                         'default'     => '',
                                         'class'	 => 'should-reset-warmup',
                                         'required'   => array('warmup-table-source', '=', 'sitemap')
                                     ),
                                     array(
		                             'id'         => 'warmup-pages',
		                             'type'       => 'dropdown',
		                             'multiple'   => true,
		                             'title'      => esc_html__('Specify Pages Manually', 'swift-performance'),
		                             'desc'       => esc_html__('Select pages which should be added to Warmup Table.', 'swift-performance'),
                                         'class'	=> 'should-reset-warmup',
		                             'options'    => $pages,
                                         'required'   => array('warmup-table-source', '=', 'manual'),
		                         ),
                                     array(
                                        'id'         => 'warmup-per-page',
                                        'type'       => 'number',
                                        'title'      => esc_html__('URLs per page', 'swift-performance'),
                                        'desc'       => esc_html__('Set how many URLs should be show per page in Warmup Table.', 'swift-performance'),
                                        'default'    => 30,
                                        'required'   => array('settings-mode', '=', 'advanced'),
                                    ),
                                     array(
                                          'id'	      => 'warmup-priority-order',
                                          'type'       => 'sortable',
                                          'title'      => esc_html__('Warmup Priority', 'swift-performance'),
                                          'desc'       => esc_html__('Configure warmup priority', 'swift-performance'),
                                          'options'    => array(
                                                'home'          => esc_html__('Home', 'swift-performance'),
                                                'menu-items'    => esc_html__('Menu Items', 'swift-performance'),
                                                'archives'      => esc_html__('Archives', 'swift-performance'),
                                                'categories'    => esc_html__('Categories', 'swift-performance'),
                                                'tags'          => esc_html__('Terms', 'swift-performance'),
                                                'posts'         => esc_html__('Posts/Pages', 'swift-performance'),
                                          ),
                                          'multiple'   => true,
                                          'class'	 => 'should-reset-warmup',
                                          'default'    => array(
                                                'home', 'menu-items', 'archives', 'categories', 'posts', 'tags'
                                          ),
                                          'required'	=> array(
                                                array('warmup-table-source', '=', 'auto'),
                                                array('settings-mode', '=', 'advanced')
                                           ),
                                    ),
                                    array(
                                        'id'          => 'autoupdate-warmup-table',
                                        'type'        => 'switch',
                                        'title'       => esc_html__('Autoupdate Warmup Table', 'swift-performance'),
                                        'desc'       => esc_html__('Add new pages, and remove deleted URLs from Warmup Table', 'swift-performance'),
                                        'default'     => 1,
                                        'required'   => array(
                                              array('warmup-table-source', '!=', 'auto'),
                                              array('settings-mode', '=', 'advanced')
                                        ),
                                        'class'	 => 'should-clear-cache'
                                    ),
                                    array(
                                        'id'          => 'warmup-remove-redirect',
                                        'type'        => 'switch',
                                        'title'       => esc_html__('Remove redirects', 'swift-performance'),
                                        'desc'       => esc_html__('Remove redirected URLs from Warmup table', 'swift-performance'),
                                        'default'     => 0,
                                        'required'   => array(
                                              array('settings-mode', '=', 'advanced')
                                        ),
                                        'class'	 => 'should-clear-cache'
                                    ),
		                         array(
		                             'id'          => 'cache-author',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Prebuild Author Pages', 'swift-performance'),
		                             'default'     => 0,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-clear-cache'
		                         ),
		                         array(
		                             'id'          => 'cache-archive',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Prebuild Archive', 'swift-performance'),
		                             'default'     => 1,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-clear-cache'
		                         ),
                                     array(
		                             'id'          => 'cache-terms',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Prebuild Terms', 'swift-performance'),
		                             'default'     => 1,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-clear-cache'
		                         ),
		                         array(
		                             'id'          => 'cache-rest',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Prebuild REST URLs', 'swift-performance'),
		                             'default'     => 0,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-clear-cache'
		                         ),
		                         array(
		                             'id'          => 'cache-feed',
		                             'type'        => 'switch',
		                             'title'       => esc_html__('Prebuild Feed', 'swift-performance'),
		                             'default'     => 0,
		                             'required'   => array('enable-caching', '=', 1),
                                         'class'	 => 'should-clear-cache'
		                        ),
                                    array(
                                        'id'          => 'enable-remote-prebuild-cache',
                                        'type'        => 'switch',
                                        'title'       => esc_html__('Enable Remote Prebuild Cache', 'swift-performance'),
                                        'desc'   => esc_html__('Use API to prebuild cache.', 'swift-performance'),
                                        'info'       => __('It is a fallback option if loopbacks are disabled on the server. If you can use local prebuild it is recommended to leave this option unchecked.', 'swift-performance'),
                                        'default'     => 0,
                                        'required'   => array(
                                             array('settings-mode', '=', 'advanced'),
                                             array('purchase-key', 'NOT_EMPTY')
                                        ),
                                    ),
		                  )
		            ),
				'varnish' => array(
		                   'title' => esc_html__('Varnish', 'swift-performance'),
		                   'fields' => array(
		                         array(
		                               'id'         => 'varnish-auto-purge',
		                               'type'	  => 'switch',
		                               'title'      => esc_html__('Enable Auto Purge', 'swift-performance'),
		                               'default'    => 0,
		                         ),
		                         array(
		                            'id'		=> 'custom-varnish-host',
		                            'type'		=> 'text',
		                            'title'		=> esc_html__('Custom Host', 'swift-performance'),
		                            'desc'	=> esc_html__('If you are using proxy (eg: Cloudflare) you may need this option', 'swift-performance'),
		                            'default'	=> '',
		                            'required'	=> array(
		                                   array('varnish-auto-purge', '=', '1')
		                            )
		                         ),
		                  )
		            ),
			)
		),
            'plugins' => array(
                  'title' => esc_html__('Plugins', 'swift-performance'),
                  'icon' => 'fas fa-plug',
                  'subsections'	=> array(
                        'wpcf7' => array(
                              'title' => esc_html__('Contact Form 7', 'swift-performance'),
                              'fields' => array(
                                   array(
                                         'id'         => 'wpcf7-smart-load',
                                         'type'	 => 'switch',
                                         'title'      => esc_html__('Smart Enqueue Assets', 'swift-performance'),
                                         'desc'       => esc_html__('Load Contact Form 7 CSS and JS only, if current page contains a contact form.', 'swift-performance'),
                                         'default'    => 0,
                                         'class'	=> 'should-clear-cache',
                                         'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                         )
                                   ),
                             )
                        ),
                        'elementor' => array(
                              'title' => esc_html__('Elementor', 'swift-performance'),
                              'fields' => array(
                                   array(
                                         'id'         => 'elementor-lazyload-yt-background',
                                         'type'	      => 'switch',
                                         'title'      => esc_html__('Lazyload Youtube Background', 'swift-performance'),
                                         'desc'       => esc_html__('Use lazyload for Youtube background videos', 'swift-performance'),
                                         'default'    => 0,
                                         'class'	=> 'should-clear-cache',
                                         'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                         )
                                   ),
                                   array(
                                         'id'         => 'lazyload-elementor-widgets',
                                         'type'	      => 'switch',
                                         'title'      => esc_html__('Lazyload Elementor Widgets', 'swift-performance'),
                                         'desc'        => esc_html__('Enable to be able to set Elementor widgets to be lazyloaded', 'swift-performance'),
                                         'info'        => __('If you enable this option, every Elementor widgets will get a new option to lazyload them. These widgets will be loaded via AJAX after the page loaded.', 'swift-performance'),
                                         'default'    => 0,
                                         'class'	=> 'should-clear-cache',
                                         'feature'    => 'pro_only',
                                         'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                         )
                                   ),
                             )
                        ),
                        'woocommerce' => array(
                               'title' => esc_html__('WooCommerce', 'swift-performance'),
                               'fields' => array(
                                    array(
                                          'id'         => 'cache-empty-minicart',
                                          'type'	 => 'switch',
                                          'title'      => esc_html__('Cache Empty Minicart', 'swift-performance'),
                                          'desc'       => esc_html__('Cart Fragments (wc-ajax=get_refreshed_fragments) requests will be cached if the cart is empty', 'swift-performance'),
                                          'default'    => 0,
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('enable-caching', '=', 1)
                                          )
                                    ),
                                    array(
                                          'id'         => 'disable-cart-fragments',
                                          'type'	 => 'dropdown',
                                          'title'      => esc_html__('Disable Cart Fragments', 'swift-performance'),
                                          'options'    => array(
                                                 'none'             => __('Don\'t disable', 'swift-performance'),
                                                 'everywhere'       => __('Everywhere', 'swift-performance'),
                                                 'non-shop'         => __('Non-Shop Pages', 'swift-performance'),
                                                 'specified-pages'  => __('Specified Pages', 'swift-performance'),
                                                 'specified-urls'   => __('Specified URLs', 'swift-performance'),
                                          ),
                                          'default'    => 'none',
                                          'feature'    => 'dequeue_woocommerce_cart_fragments',
                                          'required'   => array('settings-mode', '=', 'advanced')
                                    ),
                                    array(
                                            'id'         => 'disable-cart-fragments-pages',
                                            'type'       => 'dropdown',
                                            'multiple'      => true,
                                            'title'      => esc_html__('Disable Cart Fragments on Specific Pages', 'swift-performance'),
                                            'options'    => $pages,
                                            'required'   => array('disable-cart-fragments', '=', 'specified-pages'),
                                            'class'	 => 'should-clear-cache'
                                    ),
                                    array(
                                            'id'         => 'disable-cart-fragments-urls',
                                            'type'       => 'multi-text',
                                            'title'      => esc_html__('Disable Cart Fragments on Specific URLs', 'swift-performance'),
                                            'desc'   => esc_html__('Disable cart fragments if one of these strings is found in the match.', 'swift-performance'),
                                            'required'   => array('disable-cart-fragments', '=', 'specified-urls'),
                                            'class'	 => 'should-clear-cache'
                                      ),
                                      array(
                                          'id'         => 'woocommerce-session-cache',
                                          'type'	 => 'switch',
                                          'title'      => esc_html__('WooCommerce Session Cache', 'swift-performance'),
                                          'desc'       => esc_html__('Speed up Cart and Checkout pages', 'swift-performance'),
                                          'info'       => esc_html__('You can preload and cache WooCommerce cart and checkout pages for each users separately', 'swift-performance'),
                                          'default'    => 0,
                                          'feature'    => 'woocommere_clear_session_cache',
                                          'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('enable-caching', '=', 1)
                                          )
                                      ),
                                      array(
                                          'id'         => 'optimize-woocommerce-session-cache',
                                          'type'	 => 'switch',
                                          'title'      => esc_html__('Optimize Session Cache', 'swift-performance'),
                                          'info'       => esc_html__('If you enable this option Swift Performance will optimize CSS/JS delivery for WooCommerce Session Cache pages', 'swift-performance'),
                                          'default'    => 0,
                                          'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('woocommerce-session-cache', '=', 1),
                                               array('enable-caching', '=', 1)
                                          )
                                      ),
                                      array(
                                          'id'         => 'woocommerce-geoip-support',
                                          'type'	      => 'switch',
                                          'title'      => esc_html__('GEO IP Support', 'swift-performance'),
                                          'default'    => 0,
                                          'feature'    => 'init_woocommerce',
                                          'required'   => array(
                                               array('settings-mode', '=', 'advanced'),
                                               array('caching-mode', 'contains', '_php')
                                          ),
                                          'class'	 => 'should-clear-cache'
                                      ),
                                      array(
                                       'id'         => 'woocommerce-geoip-allowed-countries',
                                       'type'       => 'dropdown',
                                       'title'      => esc_html__('Allowed Countries', 'swift-performance'),
                                       'desc'   => esc_html__('Select countries which should be cached separately. Leave it empty to allow separate cache for all countries.', 'swift-performance'),
                                       'options'    => $swift_countries,
                                       'multiple'      => true,
                                       'required'   => array('woocommerce-geoip-support', '=', 1),
                                       'class'	 => 'should-clear-cache'
                                      ),
                                      array(
                                           'id'         => 'woocommerce-price-ajaxify',
                                           'type'	 => 'switch',
                                           'title'      => esc_html__('Ajaxify Prices', 'swift-performance'),
                                           'desc'       => esc_html__('Load prices via AJAX', 'swift-performance'),
                                           'info'       => __('This option is using ajaxify feature to load prices. It can be useful if you sell items with different TAX rates, based on user\'s location.', 'swift-performance'),
                                           'default'    => 0,
                                           'feature'    => 'pro_only',
                                           'required'   => array(
                                                 array('settings-mode', '=', 'advanced'),
                                                 array('enable-caching', '=', 1)
                                           ),
                                           'class'	 => 'should-clear-cache'
                                     ),
                                     array(
                                          'id'         => 'woocommerce-ajaxify-checkout',
                                          'type'	 => 'switch',
                                          'title'      => esc_html__('Ajaxify Checkout', 'swift-performance'),
                                          'desc'       => esc_html__('Ajaxify Cart and Checkout pages', 'swift-performance'),
                                          'info'       => __('This option is using ajaxify feature to cart and checkout pages. It can speed up checkout process.', 'swift-performance'),
                                          'default'    => 0,
                                          'feature'    => 'pro_only',
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('enable-caching', '=', 1)
                                          ),
                                          'class'	 => 'should-reset-warmup'
                                    ),
                                     array(
                                          'id'         => 'prebuild-woocommerce-variations',
                                          'type'	 => 'switch',
                                          'title'      => esc_html__('Prebuild Variations', 'swift-performance'),
                                          'desc'       => esc_html__('Use dynamic caching to prebuild variations', 'swift-performance'),
                                          'default'    => 0,
                                          'required'   => array(
                                                array('settings-mode', '=', 'advanced'),
                                                array('enable-caching', '=', 1)
                                          ),
                                    ),
                                )
                        ),
                  )
            ),
            'cdn' => array(
                  'title' => esc_html__('CDN', 'swift-performance'),
                  'icon' => 'fas fa-tasks',
                  'subsections' => array(
                        'general' => array(
                             'title' => esc_html__('General', 'swift-performance'),
                             'fields' => array(
                                   array(
                                               'id'	=> 'enable-cdn',
                                               'type'	=> 'switch',
                                               'title' => esc_html__('Enable CDN', 'swift-performance'),
                                               'default' => 0,
                                               'class' => 'should-clear-cache'
                                   ),
                                   array(
                                               'id'	=> 'cdn-hostname-master',
                                               'type'	=> 'text',
                                               'title'	=> esc_html__('CDN Hostname', 'swift-performance'),
                                               'required' => array('enable-cdn', '=', 1),
                                               'class' => 'should-clear-cache'
                                   ),
                                   array(
                                               'id'	=> 'cdn-hostname-slot-1',
                                               'type'	=> 'text',
                                               'title' => esc_html__('CDN Hostname for Javascript ', 'swift-performance'),
                                               'required' => array('cdn-hostname-master', '!=', ''),
                                               'desc' => esc_html__('Use different hostname for javascript files', 'swift-performance'),
                                               'class' => 'should-clear-cache'
                                   ),
                                   array(
                                               'id'	=> 'cdn-hostname-slot-2',
                                               'type'	=> 'text',
                                               'title'	=> esc_html__('CDN Hostname for Media files', 'swift-performance'),
                                               'required' => array('cdn-hostname-slot-1', '!=', ''),
                                               'desc' => esc_html__('Use different hostname for media files', 'swift-performance'),
                                               'class' => 'should-clear-cache'
                                   ),
                                   array(
                                               'id'	=> 'enable-cdn-ssl',
                                               'type'	=> 'switch',
                                               'title'	=> esc_html__('Use Different Hostname for SSL', 'swift-performance'),
                                               'default' => 0,
                                               'desc' => esc_html__('You can specify different hostname(s) for SSL', 'swift-performance'),
                                               'required' => array('enable-cdn', '=', 1),
                                               'class' => 'should-clear-cache'
                                   ),
                                   array(
                                               'id'	=> 'cdn-hostname-master-ssl',
                                               'type'	=> 'text',
                                               'title'	=> esc_html__('SSL CDN Hostname', 'swift-performance'),
                                               'required' => array('enable-cdn-ssl', '=', 1),
                                               'class' => 'should-clear-cache'
                                   ),
                                   array(
                                               'id'	=> 'cdn-hostname-slot-1-ssl',
                                               'type'	=> 'text',
                                               'title'	=> esc_html__('CDN Hostname for Javascript ', 'swift-performance'),
                                               'required' => array('cdn-hostname-master-ssl', '!=', ''),
                                               'desc' => esc_html__('Use different hostname for javascript files', 'swift-performance'),
                                               'class' => 'should-clear-cache'
                                   ),
                                   array(
                                               'id'	=> 'cdn-hostname-slot-2-ssl',
                                               'type'	=> 'text',
                                               'title'	=> esc_html__('CDN Hostname for Media files', 'swift-performance'),
                                               'required' => array('cdn-hostname-slot-1-ssl', '!=', ''),
                                               'desc' => esc_html__('Use different hostname for media files', 'swift-performance'),
                                               'class' => 'should-clear-cache'
                                   ),
                                   array(
                                               'id'         => 'cdn-file-types',
                                               'type'       => 'multi-text',
                                               'title'	=> esc_html__('CDN Custom File Types', 'swift-performance'),
                                               'desc'       => esc_html__('Use CDN for custom file types. Specify file extensions, eg: pdf', 'swift-performance'),
                                               'required'   => array('enable-cdn', '=', 1),
                                               'class'      => 'should-clear-cache'
                                   ),
                                   array(
                                               'id'         => 'exclude-cdn-file-types',
                                               'type'       => 'multi-text',
                                               'title'	=> esc_html__('Exclude File Types from CDN', 'swift-performance'),
                                               'desc'       => esc_html__('Disable CDN for custom file types. Specify file extensions, eg: pdf', 'swift-performance'),
                                               'required'   => array('enable-cdn', '=', 1),
                                               'class'      => 'should-clear-cache'
                                   ),
                             )

                       ),
                       'cloudflare' => array(
                              'title' => esc_html__('Cloudflare', 'swift-performance'),
                              'fields' => array(
                                    array(
                                          'id'         => 'cloudflare-auto-purge',
                                          'type'	 => 'switch',
                                          'title'      => esc_html__('Enable Auto Purge', 'swift-performance'),
                                          'default'    => 0,
                                    ),
                                    array(
                                       'id'           => 'cloudflare-auth-method',
                                       'type'         => 'dropdown',
                                       'title'        => esc_html__('Cloudflare Auth Method', 'swift-performance'),
                                       'default'      => 'api-key',
                                       'options'      => array(
                                             'api-key'      => esc_html__('API key', 'swift-performance'),
                                             'token'        => esc_html__('Auth token', 'swift-performance'),
                                       ),
                                       'required'     => array(
                                              array('cloudflare-auto-purge', '=', '1')
                                       )
                                    ),
                                    array(
                                       'id'           => 'cloudflare-token',
                                       'type'         => 'license',
                                       'title'        => esc_html__('Cloudflare Auth Token', 'swift-performance'),
                                       'default'      => '',
                                       'required'     => array(
                                              array('cloudflare-auth-method', '=', 'token'),
                                              array('cloudflare-auto-purge', '=', '1')
                                       )
                                    ),
                                    array(
                                       'id'           => 'cloudflare-email',
                                       'type'         => 'text',
                                       'title'        => esc_html__('Cloudflare Account E-mail', 'swift-performance'),
                                       'default'      => '',
                                       'required'     => array(
                                              array('cloudflare-auth-method', '=', 'api-key'),
                                              array('cloudflare-auto-purge', '=', '1')
                                       )
                                    ),
                                    array(
                                      'id'            => 'cloudflare-api-key',
                                      'type'          => 'license',
                                      'title'         => esc_html__('Cloudflare API Key', 'swift-performance'),
                                      'default'       => '',
                                      'required'      => array(
                                              array('cloudflare-auth-method', '=', 'api-key'),
                                              array('cloudflare-auto-purge', '=', '1')
                                      )
                                    ),
                                    array(
                                      'id'            => 'cloudflare-host',
                                      'type'          => 'text',
                                      'title'         => esc_html__('Cloudflare Host', 'swift-performance'),
                                      'default'       => preg_replace('~^www\.~','', parse_url(Swift_Performance::home_url(), PHP_URL_HOST)),
                                      'required'      => array(
                                              array('cloudflare-auto-purge', '=', '1')
                                      )
                                    ),
                               )
                        ),
                        'cdn-maxcdn' => array(
                              'title' => esc_html__('MaxCDN (StackPath)', 'swift-performance'),
                              'fields' => array(
                                    array(
                                                 'id'   	=> 'maxcdn-alias',
                                                 'type' 	=> 'text',
                                                 'title'	=> esc_html__('MAXCDN Alias', 'swift-performance'),
                                                 'required'   => array(
                                                      array('settings-mode', '=', 'advanced'),
                                                      array('enable-cdn', '=', '1')
                                                 ),
                                    ),
                                    array(
                                                 'id'   	=> 'maxcdn-key',
                                                 'type' 	=> 'text',
                                                 'title'	=> esc_html__('MAXCDN Consumer Key', 'swift-performance'),
                                                 'required'   => array(
                                                      array('settings-mode', '=', 'advanced'),
                                                      array('enable-cdn', '=', '1')
                                                 ),
                                    ),
                                    array(
                                                 'id'   	=> 'maxcdn-secret',
                                                 'type' 	=> 'license',
                                                 'title'      => esc_html__('MAXCDN Consumer Secret', 'swift-performance'),
                                                 'required'   => array(
                                                      array('settings-mode', '=', 'advanced'),
                                                      array('enable-cdn', '=', '1')
                                                 ),
                                    ),
                              )
                        )
                  )
            ),
	)
));
?>