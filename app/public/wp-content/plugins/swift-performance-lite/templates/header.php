<?php defined('ABSPATH') or die("KEEP CALM AND CARRY ON");?>
<?php if (Swift_Performance::is_developer_mode_active()):?>
      <div class="update-nag">
      <?php echo sprintf(__('%s Developer Mode is active until <strong>%s</strong>'), SWIFT_PERFORMANCE_PLUGIN_NAME, get_date_from_gmt( date( 'Y-m-d H:i:s', get_option('swift-performance-developer-mode') ), get_option('date_format') . ' ' .get_option('time_format') ));?>
      </div>
<?php endif;?>

<div id="swift-performance-wrapper">
      <div class="swift-performance-header">
            <img class="swift-performance-logo" src="<?php echo SWIFT_PERFORMANCE_LOGO_URI;?>">
            <div class="swift-performance-slogan">
                  <?php esc_html_e('Speed up WordPress', 'swift-performance');?>
                  <small><?php esc_html_e('is not rocket science anymore', 'swift-performance');?></small>
            </div>
      </div>
      <div id="swift-performance-wrapper-inner">
      <ul class="swift-menu">
            <?php foreach(Swift_Performance::get_menu() as $element):?>
                  <li<?php echo((isset($_GET['subpage']) && $_GET['subpage'] == $element['slug']) || (!isset($_GET['subpage']) && $element['slug'] == 'dashboard') ? ' class="active"' : '');?>>
                        <a href="<?php echo esc_url(add_query_arg('subpage', $element['slug'], menu_page_url(SWIFT_PERFORMANCE_SLUG, false)));?>">
                              <?php if(isset($element['icon'])):?>
                                    <i class="<?php echo esc_attr($element['icon']);?>"></i>
                              <?php endif;?>
                              <?php echo esc_html($element['name']);?>
                        </a>
                  </li>
            <?php endforeach;?>
      </ul>