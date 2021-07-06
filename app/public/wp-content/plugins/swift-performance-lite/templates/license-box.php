<?php if (in_array($license, array('offline', 'expired', 'nulled'))):?>
      <div class="swift-performance-reloaded-col">
            <?php if ($license == 'offline'):?>
            <h4><?php echo sprintf(esc_html__('Swift Performance %sLite%s reloaded'), '<small>', '</small>')?></h4>
            <?php elseif ($license == 'expired'):?>
                  <?php if (Swift_Performance::check_option('purchase-key', '')):?>
                        <h4><?php echo sprintf(esc_html__('Activate your %slicense%s', 'swift-performance'), '<small>', '</small>');?></h4>
                  <?php else:?>
                        <h4><?php echo sprintf(esc_html__('Your license has %sexpired%s', 'swift-performance'), '<small>', '</small>');?></h4>
                  <?php endif;?>
            <?php elseif ($license == 'nulled'):?>
            <h4><?php echo sprintf(esc_html__('You are using %snulled%s version', 'swift-performance'), '<small>', '</small>');?></h4>
            <?php endif;?>
      </div>
      <div class="swift-performance-reloaded-col<?php echo (in_array($license, array('nulled', 'expired')) ? ' invalid' : '' );?>">
            <div class="swift-performance-reloaded-title">
                  <?php if ($license == 'offline'):?>
                  <?php echo sprintf(esc_html__('Now more %sPRO features are available%s for Lite users', 'swift-performance'), '<strong>', '</strong>')?><sup>*</sup>
                  <?php elseif ($license == 'expired'):?>
                  <?php echo sprintf(esc_html__('Following %sfeatures are NOT available%s for you', 'swift-performance'), '<strong>', '</strong>')?>
                  <?php elseif ($license == 'nulled'):?>
                  <?php echo sprintf(esc_html__('Following %sfeatures are NOT available%s in nulled version', 'swift-performance'), '<strong>', '</strong>')?>
                  <?php endif;?>
            </div>
            <ul>
                  <li><?php esc_html_e('Image Optimizer', 'swift-performance');?></li>
                  <li><?php esc_html_e('Better Critical CSS', 'swift-performance');?></li>
                  <li class="swift-show-on-invalid"><?php esc_html_e('Advanced Cron', 'swift-performance');?></li>
                  <li class="swift-hide-on-invalid"><?php esc_html_e('Font optimization', 'swift-performance');?></li>
                  <li><?php esc_html_e('Better JS minify', 'swift-performance');?></li>
                  <li class="swift-hide-on-invalid"><?php esc_html_e('Advanced JS features', 'swift-performance');?></li>
                  <li class="swift-hide-on-invalid"><?php esc_html_e('Smart Render HTML', 'swift-performance');?></li>
                  <li class="swift-show-on-invalid"><?php esc_html_e('Remote Prebuild', 'swift-performance');?></li>
                  <li class="swift-hide-on-invalid"><?php esc_html_e('and more...', 'swift-performance');?></li>
            </ul>
      </div>
      <div class="swift-performance-register-container">
            <?php if ($license == 'offline'):?>
            <a href="#" class="swift-btn swift-btn-green" id="swift-performance-lite-activate"><?php esc_html_e('Activate Features', 'swift-performance');?></a>
            <small>
                  <div><?php esc_html_e('* 1000 free API credit / month', 'swift-performance');?></div>
                  <div><?php esc_html_e('500 free Image Optimizer credit / month', 'swift-performance');?></div>
            </small>
            <?php elseif ($license == 'expired'):?>
                  <?php if (Swift_Performance::check_option('purchase-key', '')):?>
                        <a href="#" class="swift-btn swift-btn-green swift-performance-already-have-license" id="swift-performance-lite-activate"><?php esc_html_e('Activate Features', 'swift-performance');?></a>
                  <?php else:?>
                        <a target="_blank" href="https://swiftperformance.io/my-account/" class="swift-btn swift-btn-green"><?php esc_html_e('Reactivate License', 'swift-performance');?></a>
                  <?php endif;?>
            <?php elseif ($license == 'nulled'):?>
            <a href="#" class="swift-btn swift-btn-green" id="swift-performance-lite-activate"><?php esc_html_e('Activate Features for FREE', 'swift-performance');?></a>
            <?php endif;?>
      </div>

      <form class="swift-performance-lite-license-form swift-hidden" id="swift-performance-license-form">
            <div class="swift-error swift-hidden error-missing-field"><?php esc_html_e('Please fill all require fields', 'swift-performance');?></div>
            <div class="swift-error swift-hidden error-accept-terms"><?php esc_html_e('Please accept terms', 'swift-performance');?></div>
            <div class="swift-error swift-hidden error-container" data-default="<?php esc_html_e('Error. Please refresh page and try again.', 'swift-performance');?>"></div>
            <a href="#" class="swift-performance-already-have-license"><?php esc_html_e('I already have a license key', 'swift-performance');?></a>
            <div class="swift-row two-columns">
                  <div class="swift-col">
                        <label class="swift-block swift-uppercase"><?php esc_html_e('Name', 'swift-performance');?> <sup>*</sup></label>
                        <input type="text" placeholder="your name" name="name">
                  </div>
                  <div class="swift-col">
                        <label class="swift-block swift-uppercase"><?php esc_html_e('E-mail', 'swift-performance');?> <sup>*</sup></label>
                        <input type="email" placeholder="E-mail address" name="email">
                  </div>
            </div>
            <br>
            <div class="swift-row three-columns">
                  <div class="swift-col swift-v-centered">
                        <label><input type="checkbox" name="anonym-stats" value="accepted"> <?php esc_html_e('Collect anonymized data', 'swift-performance')?></label>
                  </div>
                  <div class="swift-col swift-v-centered">
                        <label><input type="checkbox" name="terms" value="accepted"> <?php echo sprintf(esc_html__('Accept %sTerms & Conditions%s', 'swift-performance'), '<a target="_blank" href="https://swiftperformance.io/terms-and-conditions/">', '</a>')?> <sup>*</sup></label>
                  </div>
                  <div class="swift-col">
                        <button class="swift-btn swift-btn-green swift-send-license-key"><?php esc_html_e('Send license key', 'swift-performance');?></button>
                  </div>
            </div>
      </form>
      <form class="swift-performance-lite-license-form swift-hidden" id="swift-performance-lite-purchase-key">
            <h4 class="swift-hidden"><?php esc_html_e('Your license key has been sent to your e-mail address', 'swift-performance');?></h4>
            <div class="swift-error error-container swift-hidden" data-default="<?php esc_html_e('General error. Please try again.', 'swift-performance');?>"></div>
            <div class="swift-error error-missing-field swift-hidden"><?php esc_html_e('License key is empty.', 'swift-performance');?></div>
            <div class="swift-row two-columns">
                  <div class="swift-col">
                        <label class="swift-block swift-uppercase"><?php esc_html_e('License Key', 'swift-performance');?></label>
                        <input class="swift-centered swift-fullwidth" type="text" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" name="license-key">
                  </div>
                  <div class="swift-col swift-v-bottom">
                        <button class="swift-btn swift-btn-green"><?php esc_html_e('Activate', 'swift-performance');?></button>
                        <?php if ($license != 'expired'):?>
                        <a href="#" class="swift-btn swift-btn-yellow" id="swift-resend-license-key"><?php esc_html_e('Resend license key', 'swift-performance');?></a>
                        <?php endif;?>
                  </div>
            </div>
      </form>
<?php elseif ($license == 'lite'):?>
      <?php
            $plugin_updates   = get_site_transient('update_plugins');
            foreach ($plugin_updates->response as $object) {
                  if ($object->slug == 'swift-performance-lite'){
                        $latest = $object->new_version;
                        $update_available = version_compare(SWIFT_PERFORMANCE_VER, $latest, '<');
                        break;
                  }
            }
            $credit = Swift_Performance::get_credit();
      ?>
      <h3><?php esc_html_e('Informations', 'swift-performance');?></h3>
      <ul>
            <li>
                  <ul>
                        <li><strong><?php esc_html_e('API Connection', 'swift-performance');?></strong></li>
                        <?php if ($check_api):?>
                              <li class="text-right text-green"><?php esc_html_e('OK', 'swift-performance');?></li>
                        <?php else:?>
                              <li class="text-right text-red"><?php esc_html_e('FAIL', 'swift-performance');?> | <a href="#" id="swift-performance-debug-api"><?php esc_html_e('Debug', 'swift-performance');?></a></li>
                        <?php endif;?>
                  </ul>
                  <ul>
                        <li><strong><?php esc_html_e('Current Version', 'swift-performance');?></strong></li>
                        <li class="text-right text-<?php echo ($update_available ? 'red' : 'green');?>"><?php echo SWIFT_PERFORMANCE_VER;?></li>
                  </ul>
                  <?php if ($update_available):?>
                  <ul>
                        <li><strong><?php esc_html_e('Latest Version', 'swift-performance');?></strong></li>
                        <li class="text-right text-green"><?php echo esc_html($latest);?></li>
                  </ul>
                  <?php endif;?>
                  <ul>
                        <li><strong><?php esc_html_e('Timeout', 'swift-performance');?></strong></li>
                        <?php if (SWIFT_PERFORMANCE_PREBUILD_TIMEOUT > 30):?>
                              <li class="text-right text-green"><?php echo esc_html(sprintf(__("%ss", 'swift-perforance'), SWIFT_PERFORMANCE_PREBUILD_TIMEOUT));?></li>
                        <?php else:?>
                              <li class="text-right text-red"><?php echo esc_html(sprintf(__("%ss", 'swift-perforance'), SWIFT_PERFORMANCE_PREBUILD_TIMEOUT));?></li>
                        <?php endif;?>
                  </ul>
                  <?php if (!Swift_Performance::is_feature_available('__construct')):?>
                  <ul>
                        <li class="swift-error swift-centered">
                              <?php esc_html_e('Some features are not available yet.', 'swift-performance');?><br>
                              <strong><?php echo sprintf(esc_html__('Install %s to activate all features', 'swift-performance'), '<a target="_blank" href="'.SWIFT_PERFORMANCE_API_URL . 'extra/download/' .'">Swift Performance Extra</a>');?></strong>
                        </li>
                  </ul>
                  <?php endif;?>
            </li>
      </ul>
      <small class="swift-performance-monthly-reset"><?php echo sprintf(esc_html__('Your monthly qouta will be reset in %d days'), round((strtotime('first day of next month')-time())/86400))?></small>

      <div class="swift-performance-credit-container">
            <label><?php esc_html_e('Page optimization', 'swift-performance');?> <span><?php echo sprintf(esc_html__('%d credits', 'swift-performance'), $credit['compute']);?></span></label>
            <div class="swift-performance-credit-bar"><div class="swift-performance-credit-bar-inner brand"  data-type="compute" data-credit="<?php echo esc_attr($credit['compute']);?>" data-total="1000"></div></div>
      </div>
      <div class="swift-performance-credit-container">
            <label><?php esc_html_e('Image Optimization', 'swift-performance');?> <span><?php echo sprintf(esc_html__('%d credits', 'swift-performance'), $credit['io']);?></span></label>
            <div class="swift-performance-credit-bar"><div class="swift-performance-credit-bar-inner" data-type="io" data-credit="<?php echo esc_attr($credit['io']);?>" data-total="500"></div></div>
      </div>
      <div class="swift-performance-credit-container">
            <a target="_blank" class="swift-performance-upgrade-pro" href="<?php echo Swift_Performance::get_upgrade_url('dashboard');?>"><?php esc_html_e('Upgrade to unlimited', 'swift-performace');?></a>
      </div>
<?php elseif ($license == 'pro'):?>
      <?php
            $update_info 		= Swift_Performance::update_info();
            $latest			= (isset($update_info['version']) ? $update_info['version'] : 0);
            $update_available 	= version_compare(SWIFT_PERFORMANCE_VER, $latest, '<');
      ?>
<h3><?php esc_html_e('Informations', 'swift-performance');?></h3>
<ul>
      <li>
            <ul>
                  <li><strong><?php esc_html_e('API Connection', 'swift-performance');?></strong></li>
                  <?php if ($check_api):?>
                        <li class="text-right text-green"><?php esc_html_e('OK', 'swift-performance');?></li>
                  <?php else:?>
                        <li class="text-right text-red"><?php esc_html_e('FAIL', 'swift-performance');?> | <a href="#" id="swift-performance-debug-api"><?php esc_html_e('Debug', 'swift-performance');?></a></li>
                  <?php endif;?>
            </ul>
            <ul>
                  <li><strong><?php esc_html_e('Current Version', 'swift-performance');?></strong></li>
                  <li class="text-right text-<?php echo ($update_available ? 'red' : 'green');?>"><?php echo SWIFT_PERFORMANCE_VER;?></li>
            </ul>
            <?php if ($update_available):?>
            <ul>
                  <li><strong><?php esc_html_e('Latest Version', 'swift-performance');?></strong></li>
                  <li class="text-right text-green"><?php echo esc_html($latest);?></li>
            </ul>
            <?php endif;?>
            <ul>
                  <li><strong><?php esc_html_e('Timeout', 'swift-performance');?></strong></li>
                  <?php if (SWIFT_PERFORMANCE_PREBUILD_TIMEOUT > 30):?>
                        <li class="text-right text-green"><?php echo esc_html(sprintf(__("%ss", 'swift-perforance'), SWIFT_PERFORMANCE_PREBUILD_TIMEOUT));?></li>
                  <?php else:?>
                        <li class="text-right text-red"><?php echo esc_html(sprintf(__("%ss", 'swift-perforance'), SWIFT_PERFORMANCE_PREBUILD_TIMEOUT));?></li>
                  <?php endif;?>
            </ul>
            <?php if (!Swift_Performance::is_feature_available('__construct')):?>
            <ul>
                  <li class="swift-error swift-centered">
                        <?php esc_html_e('Some features are not available.', 'swift-performance');?><br>
                        <strong><?php echo sprintf(esc_html__('Install and activate %s or %s', 'swift-performance'), '<a target="_blank" href="https://swiftpeformance.io/my-account/">Swift Performance Pro</a>', '<a target="_blank" href="'.SWIFT_PERFORMANCE_API_URL . 'extra/download/' .'">Swift Performance Extra</a>');?></strong>
                  </li>
            </ul>
            <?php endif;?>
      </li>
</ul>
<?php endif;?>
<?php if ($update_available):?>
      <a href="<?php echo esc_url(admin_url('plugins.php'));?>" class="swift-btn swift-btn-green"><?php esc_html_e('Update Now', 'swift-performance')?></a>
<?php endif;?>