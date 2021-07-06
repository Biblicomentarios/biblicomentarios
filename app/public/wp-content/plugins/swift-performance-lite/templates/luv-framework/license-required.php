
<div class="<?php echo implode(' ', $classes);?>" id="<?php echo esc_attr($prefix . $field['id']);?>-container"<?php echo (!empty($default) ? ' data-default="' . $default . '"' : '');?> data-type="<?php echo $field['type'];?>">
      <div class="luv-framework-field-title">
            <strong><?php echo esc_html($label);?></strong>
            <?php if (!empty($info)):?>
                  <a href="#" class="luv-framework-show-info">?</a>
                  <div class="luv-framework-info">
                        <?php echo luv_framework_kses($info);?>
                  </div>
            <?php endif;?>
            <?php if (!empty($description)):?>
                  <div class="luv-framework-field-description">
                        <?php echo esc_html($description); ?>
                  </div>
            <?php endif;?>
      </div>
      <div class="luv-framework-field-inner swift-error swift-centered">
      	<?php if (Swift_Performance::license_type() == 'offline'):?>
                  <?php echo sprintf(esc_html__('This feature requires %sSwift Performance Extra%s plugin or %sPro version%s', 'swift-performance'), '<strong><a target="_blank" href="https://docs.swiftperformance.io/knowledgebase/swift-performance-extra/">', '</a></strong>', '<strong><a target="_blank" href="https://swiftperformance.io/upgrade-pro/">', '</a></strong>');?>
            <?php else:?>
                  <?php esc_html_e('This feature requires a valid license', 'swift-performance');?>
            <?php endif;?>
      </div>
</div>
