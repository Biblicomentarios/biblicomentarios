<div class="<?php echo implode(' ', $classes);?>" id="<?php echo esc_attr($prefix . $field['id']);?>-container"<?php echo (!empty($default) ? ' data-default="' . $default . '"' : '');?> data-type="<?php echo $field['type'];?>">
      <div class="luv-framework-field-title">
            <a href="#" class="luv-framework-reset-single-field"><?php esc_html_e('RESET TO DEFAULT', 'swift-performance')?></a>

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
      <div class="luv-framework-field-inner">
     	 <?php if (isset($field['options'])): ?>
                  <ul class="luv-framework-sortable-container">
	            <?php foreach (array_replace(array_flip($field['value']), $field['options']) as $key => $option):?>
	                  <li data-value="<?php echo esc_attr($key);?>">
	                        <input type="checkbox" name="<?php echo esc_attr($name);?>" value="<?php echo esc_attr($key);?>" checked>
                              <?php echo esc_html($option);?>
	                  </li>
	            <?php endforeach;?>
                  </ul>
	      <?php endif; ?>
      </div>
</div>
