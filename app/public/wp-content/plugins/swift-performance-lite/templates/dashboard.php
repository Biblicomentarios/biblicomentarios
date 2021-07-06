<?php defined('ABSPATH') or die("KEEP CALM AND CARRY ON");?>
<?php
      $license                = Swift_Performance::license_type();
	$check_api			= Swift_Performance::check_api();
	$update_available       = false;

	if ($license == 'pro' && !$check_api){
		$license = 'expired';
	}

	$caching_on			= Swift_Performance::check_option('enable-caching', 1);
	$assets_caching_on	= (Swift_Performance::check_option('merge-scripts', 1) || Swift_Performance::check_option('merge-styles', 1));

	$ajax_caching_on		= $caching_on && (count(array_filter((array)Swift_Performance::get_option('cacheable-ajax-actions'))) > 0);
	$dynamic_caching_on	= $caching_on && (Swift_Performance::check_option('dynamic-caching', 1) || Swift_Performance::check_option('woocommerce-session-cache',1));

	$prebuild_pid		= get_transient('swift_performance_prebuild_cache_pid');
	$prebuild_running		= !(empty($prebuild_pid) || $prebuild_pid == 'stop');

	if ($caching_on){
		include_once SWIFT_PERFORMANCE_DIR . 'includes/cache-status/cache-status.php';

	      $cache_status_table = new Swift_Performance_Cache_Status_Table();
	      $cache_status_table->prepare_items();
	}

	if ($dynamic_caching_on){
		include_once SWIFT_PERFORMANCE_DIR . 'includes/cache-status/dynamic-cache-status.php';

	      $dynamic_cache_status_table = new Swift_Performance_Dynamic_Cache_Status_Table();
	      $dynamic_cache_status_table->prepare_items();
	}

	if ($ajax_caching_on){
		include_once SWIFT_PERFORMANCE_DIR . 'includes/cache-status/ajax-cache-status.php';

	      $ajax_cache_status_table = new Swift_Performance_Ajax_Cache_Status_Table();
	      $ajax_cache_status_table->prepare_items();
	}
?>

<div class="swift-message swift-hidden"></div>
<div class="swift-dashboard two-columns license-<?php echo $license;?>">
	<div class="swift-dashboard-item">
		<h3><?php esc_html_e('Actions', 'swift-performance');?></h3>
		<ul class="swift-dashboard-actions">
      	<?php if(get_option('swift_performance_rewrites') != ''):?>
            <li><a href="#" id="swift-performance-show-rewrite" class="swift-performance-control clear-response-container"><?php esc_html_e('Show Rewrite Rules', 'swift-performance');?></a></li>
      	<?php endif;?>
		<li><a href="<?php echo esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'setup'), admin_url('tools.php')));?>"><?php esc_html_e('Setup Wizard', 'swift-performance');?></a></li>
		<li><a href="#" id="swift-performance-log" class="swift-performance-control clear-response-container"><?php esc_html_e('Show Log', 'swift-performance');?></a> | <a href="#" id="swift-performance-clear-logs" class="swift-performance-control"><?php esc_html_e('Clear Log', 'swift-performance');?></a></li>
            <?php if(in_array($license, array('lite','pro','expired')) && !$check_api):?>
            <li><a href="#" id="swift-performance-debug-api"><?php esc_html_e('Debug API Connection', 'swift-performance');?></a></li>
            <?php endif;?>
		<?php if ($caching_on && (!defined('SWIFT_PERFORMANCE_DISABLE_CACHE') || !SWIFT_PERFORMANCE_DISABLE_CACHE)):?>
		<li>
			<a href="#" id="swift-performance-toggle-developer-mode" class="swift-performance-control clear-response-container">
				<span<?php echo (Swift_Performance::is_developer_mode_active() ? ' class="swift-hidden"' : '');?>><?php esc_html_e('Enable Developer Mode', 'swift-performance');?></span>
				<span<?php echo (!Swift_Performance::is_developer_mode_active() ? ' class="swift-hidden"' : '');?>><?php esc_html_e('Disable Developer Mode', 'swift-performance');?></span>
			</a>
		</li>
		<li>
			<a href="#" id="swift-performance-custom-purge-toggle" class="swift-performance-control clear-response-container"><?php esc_html_e('Custom Purge', 'swift-performance')?></a>
			<div class="swift-performance-custom-purge-container swift-hidden">
				<input type="url" id="swift-performance-custom-purge-url">
				<a id="swift-performance-custom-purge-trigger" class="swift-btn swift-btn-gray"><?php esc_html_e('Purge', 'swift-performance');?></a>
				<i><?php echo esc_html__('Use full or partial URL. Use # for regex, eg: #products-(.*)#');?></i>
			</div>
		</li>
		<?php endif;?>
		</ul>
            <div class="swift-cache-actions">
                  <div class="swift-button-container">
                        <?php if ($caching_on):?>
                        <a href="#" class="swift-performance-clear-cache swift-performance-control swift-btn swift-btn-gray" data-type="all"><?php esc_html_e('Clear All Cache', 'swift-performance');?></a>
                        <?php endif;?>
                        <?php if ($ajax_caching_on):?>
                        <a href="#" class="swift-performance-clear-cache swift-performance-control swift-btn swift-btn-gray" data-type="ajax"><?php esc_html_e('Clear Ajax Cache', 'swift-performance');?></a>
                        <?php endif;?>
                        <?php if ($dynamic_caching_on):?>
                        <a href="#" class="swift-performance-clear-cache swift-performance-control swift-btn swift-btn-gray" data-type="dynamic"><?php esc_html_e('Clear Dynamic Cache', 'swift-performance');?></a>
                        <?php endif;?>
                        <?php if (($caching_on || $ajax_caching_on || $dynamic_caching_on) && (defined('SWIFT_PERFORMANCE_DISABLE_CACHE') && SWIFT_PERFORMANCE_DISABLE_CACHE)):?>
                              <br>
                        <i><?php esc_html_e('3rd party caching was detected, these options may won\'t work properly.', 'swift-performance');?></i>
                        <?php endif;?>
                  </div>
            </div>
	</div>
	<div class="swift-dashboard-item">
		<?php include_once apply_filters('swift_performance_template_dir', SWIFT_PERFORMANCE_DIR . 'templates/') . 'license-box.php'; ?>
	</div>
</div>
<div class="swift-preformatted-box swift-box swift-hidden">
	<h3><span class="title"></span><span class="swift-box-close"><span class="dashicons dashicons-no-alt"></span></h3>
	<div class="swift-box-inner">
		<pre class="response-container"></pre>
	</div>
</div>

<?php if ($caching_on && (!defined('SWIFT_PERFORMANCE_DISABLE_CACHE') || !SWIFT_PERFORMANCE_DISABLE_CACHE)):?>
<input type="hidden" id="swift-performance-warmup-table-url" value="<?php echo set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );?>">

<div id="swift-cache-status-box" class="swift-box">
	<h3><?php esc_html_e('Cache Status', 'swift-performance');?></h3>
	<div class="swift-box-inner">
            <ul>
                  <li>
				<i class="far fa-copy"></i>
            		<label><?php esc_html_e('Known Pages', 'swift-performance')?></label>
                        <span class="cache-status-count warmup-pages-count">-</span>
                  </li>
                  <li>
				<i class="fas fa-copy"></i>
                        <label><?php esc_html_e('Cached Pages', 'swift-performance')?></label>
                        <span class="cache-status-count cached-pages-count">-</span>
                  </li>
                  <li>
				<i class="fas fa-weight fa-dashboard"></i>
                        <label><?php esc_html_e('Cache size', 'swift-performance')?></label>
                        <span class="cache-status-count cache-size-count">-</span>
                  </li>
                  <li>
				<i class="fas fa-microchip"></i>
                        <label><?php esc_html_e('Threads', 'swift-performance')?></label>
                        <span class="cache-status-count">
					<span class="thread-count">-</span>
					<?php if (Swift_Performance::check_option('limit-threads', 1)):?>
					<span class="thread-numbers">
						<span class="thread-plus change-thread-limit"><span class="fas fa-plus"></span></span>
						<span class="thread-minus change-thread-limit"><span class="fas fa-minus"></span></span>
					</span>
					<?php endif;?>
				</span>
                  </li>
            </ul>
		<?php if ($ajax_caching_on || $dynamic_caching_on):?>
		<ul class="secondary">
			<?php if ($ajax_caching_on):?>
                  <li>
				<i class="fas fa-tasks"></i>
            		<label><?php esc_html_e('AJAX Objects', 'swift-performance')?></label>
                        <span class="cache-status-count ajax-object-count">-</span>
                  </li>
			<li>
				<i class="fas fa-weight"></i>
            		<label><?php esc_html_e('AJAX Cache Size', 'swift-performance')?></label>
                        <span class="cache-status-count ajax-size-count">-</span>
                  </li>
			<?php endif;?>
			<?php if ($dynamic_caching_on):?>
                  <li>
				<i class="fas fa-sync-alt"></i>
                        <label><?php esc_html_e('Dynamic Pages', 'swift-performance')?></label>
                        <span class="cache-status-count cached-dynamic-pages-count">-</span>
                  </li>
			<li>
				<i class="fas fa-weight"></i>
                        <label><?php esc_html_e('Dynamic Cache Size', 'swift-performance')?></label>
                        <span class="cache-status-count cached-dynamic-size-count">-</span>
                  </li>
			<?php endif;?>
            </ul>
		<?php endif;?>
            <div class="prebuild-status"></div>
	</div>
</div>
<?php if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON):?>
	<div class="swift-error swift-on-white"><?php echo sprintf(esc_html__('WP Cron is disabled. This can cause issues with cache prebuild. If you would like to limit WP Cron, please re-enable it and use built in %sLimit Cron%s features instead.', 'swift-performance'), '<a target="_blank" href="https://docs.swiftperformance.io/knowledgebase/cronjobs/">', '</a>');?></div>
<?php endif;?>
<div id="swift-performance-list-table-container">
	<div id="swift-warmup-box" class="swift-box">
		<h3>
		<a href="#" data-table="#warmup-table-container" class="swift-cache-table-switch active"><?php esc_html_e('Warmup Table', 'swift-performance');?></a>
		<?php if ($dynamic_caching_on):?>
		<a href="#" data-table="#dynamic-cache-table-container" class="swift-cache-table-switch"><?php esc_html_e('Dynamic Cache', 'swift-performance');?></a>
		<?php endif;?>
		<?php if ($ajax_caching_on):?>
		<a href="#" data-table="#ajax-cache-table-container" class="swift-cache-table-switch"><?php esc_html_e('AJAX Cache', 'swift-performance');?></a>
		<?php endif;?>
		</h3>
		<div id="warmup-table-container" class="swift-cache-table-container">
		      <div class="swift-actions-container">
		            <div class="swift-button-container">
		                  <a href="#" id="swift-performance-refresh-list-table" class="swift-btn swift-btn-black swift-btn-thin"><i class="fas fa-sync-alt"></i></a>
					<a href="#" id="swift-performance-add-warmup-link" class="swift-btn swift-btn-black swift-btn-thin"><i class="fas fa-plus"></i></a>
		                  <a href="#" id="swift-performance-prebuild-cache" class="swift-btn swift-btn-blue<?php echo ($prebuild_running ? ' swift-hidden' : '')?>"><?php esc_html_e('Start Prebuild Cache', 'swift-performance');?></a>
					<a href="#" id="swift-performance-stop-prebuild-cache" class="swift-btn swift-btn-brand<?php echo (!$prebuild_running ? ' swift-hidden' : '')?>"><?php esc_html_e('Stop Prebuild Cache', 'swift-performance');?></a>
					<a href="#" id="swift-performance-reset-warmup" class="swift-btn swift-btn-brand"><?php esc_html_e('Reset Warmup Table', 'swift-performance');?></a>
		            </div>
				<div class="swift-add-warmup-link-container swift-hidden">
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e('URL', 'swift-performance');?></th>
								<th><?php esc_html_e('Prebuild priority', 'swift-performance');?></th>
								<th><?php esc_html_e('Actions', 'swift-performance');?></th>
							</tr>
						</thead>

						<tbody class="field-container">
							<tr>
								<td>
									<input type="url" class="swift-input" name="url" placeholder="<?php echo esc_attr(home_url());?>" >
								</td>
								<td>
									<input type="number" class="swift-input" name="priority" value="<?php echo Swift_Performance::get_default_warmup_priority();?>" >
								</td>
								<td>
									<button id="swift-save-warmup-link" class="swift-btn swift-btn-brand">Save</button>
									<button id="swift-performance-cancel-add-warmup-link" class="swift-btn swift-btn-gray">Cancel</button>
								</td>
							</tr>
					  </tbody>
					</table>
				</div>
		      </div>
		      <?php
		            $cache_status_table->display();
		      ?>
		</div>
		<div id="dynamic-cache-table-container" class="swift-cache-table-container swift-hidden">
			<?php
				if ($dynamic_caching_on){
			 		$dynamic_cache_status_table->display();
				}
			?>
		</div>
		<div id="ajax-cache-table-container" class="swift-cache-table-container swift-hidden">
			<?php
				if ($ajax_caching_on){
			 		$ajax_cache_status_table->display();
				}
			?>
		</div>
	</div>
</div>
<?php else: ?>
	<div id="swift-cache-status-box" class="swift-box">
		<h3><?php esc_html_e('Cache Status', 'swift-performance');?></h3>
		<div class="swift-box-inner">
			<?php esc_html_e('Swift Performance caching is disabled', 'swift-performance');?>
		</div>
	</div>
	<br>
<?php endif;?>