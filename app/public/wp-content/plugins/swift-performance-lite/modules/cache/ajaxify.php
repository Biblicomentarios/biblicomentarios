<script>
      function swift_ajaxify(){
            if (typeof jQuery == 'function' && jQuery("<?php echo implode(',', $ajaxify);?>").length > 0){
                  jQuery(function(){
                        jQuery.get(document.location.href.replace(/#(.*)$/,'') + (document.location.href.match(/\?/) ? '&' : '?') + 'nocache=' + Math.random(), function(response){
                              var nodelist = jQuery.parseHTML(response);
                              <?php foreach ($ajaxify as $item) :?>
                                    jQuery("<?php echo $item?>").each(function(){
                                          var element = jQuery(this);
                                          jQuery(nodelist).each(function(i, node){
                                                if (jQuery(node).is("<?php echo $item?>")){
                                                      jQuery(node).addClass('swift-lazyloaded');
                                                      jQuery(element).replaceWith(node);
                                                      delete(nodelist[i]);
                                                }
                                                else if (jQuery(node).find("<?php echo $item?>").length > 0){
                                                      jQuery(node).find("<?php echo $item?>").each(function(i, node_in){
                                                            jQuery(node_in).addClass('swift-lazyloaded');
                                                            jQuery(element).replaceWith(node_in);
                                                      });
                                                }
                                          });
                                    });
                                    jQuery("<?php echo $item?>").trigger('swift-performance-ajaxify-item-done');
                              <?php endforeach; ?>
                              jQuery('body').trigger('swift-performance-ajaxify-done');
                        });
                  });
                  swift_ajaxify = false;
            }
      }
      swift_ajaxify();
</script>