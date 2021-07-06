<div class="swift-lite-reloaded-notice">
      <h2><?php echo sprintf(esc_html__('Swift Performance Lite %s - Reloaded%s', 'swift-performance'), '<span>', '</span>');?></h2>
      <?php echo sprintf(esc_html__('New Pro features are available in Swift Performance Lite %sfor free%s. Go to %sSwift Performance dashboard%s to activate them!', 'swift-performance'), '<strong>', '</strong>', '<a href="'.esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG), admin_url('tools.php'))).'">', '</a>');?>
</div>
<div class="swift-notice-buttonset">
      <a href="<?php echo esc_url(add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG), admin_url('tools.php')));?>" class="swift-btn swift-btn-green" data-swift-dismiss-notice><?php esc_html_e('Activate Features', 'swift-performance');?></a>
      <a href="#" class="swift-btn swift-btn-gray" data-swift-dismiss-notice><?php esc_html_e('Dismiss', 'swift-performance');?></a>
</div>