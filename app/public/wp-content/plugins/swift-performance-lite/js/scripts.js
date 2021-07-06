jQuery(function(){
      var _interval, cache_status_interval;

      jQuery(window).on('blur', function(){
		clearTimeout(cache_status_interval);
	});

      // Cache status
	jQuery(window).on('focus', function(){
            // Cache status
            if (jQuery('#swift-cache-status-box').length > 0){
                  clearTimeout(cache_status_interval);
                  cache_status();
            }
	});

      if (jQuery('#swift-cache-status-box').length > 0){
            clearTimeout(cache_status_interval);
            cache_status();
      }

      // Show pointers if any
      jQuery(window).on('load', pointers);

      // Fire cron if WP cron is disabled
      if (swift_performance.cron.length > 0){
            jQuery.get(swift_performance.cron);
            setInterval(function(){
                  jQuery.get(swift_performance.cron);
            },60000);
      }

      // Clear messages and container if any buttons was clicked
      jQuery(document).on('click', '.swift-performance-control', function(){
            if (jQuery(this).hasClass('clear-response-container')){
                  jQuery('.swift-preformatted-box .response-container').empty();
            }
            clear_messages();
      });

      jQuery(document).on('click', '.swift-box-close', function(){
            jQuery(this).closest('.swift-box').addClass('swift-hidden');
      });

      // Initialize charts and counters
      jQuery(window).on('load', function(){
            // Pie chart
            if (jQuery('.swift-pie-chart').length > 0){
                  jQuery('.swift-pie-chart').each(function(index, element) {
                        var that = jQuery(this);
                        var num = +(jQuery(that).attr('data-value'));
                        jQuery(that).html('<svg class="swift-pie" viewBox="0 0 32 32"><circle class="swift-pie-fill" r="16" cx="16" cy="16" style="stroke-dasharray: 0 100" /></svg>');
                        setTimeout(function(){
                              jQuery(that).find('.swift-pie-fill').css('stroke-dasharray', num + ' 100');
                        },1);
                  });

            }

            // Bar chart
            jQuery(".swift-bar-chart-bar .swift-bar-chart-bar-outer").each(function() {
                  var that = jQuery(this);
                  if (!jQuery(that).hasClass("swift-animated")) {
                    jQuery(that).addClass("swift-animated");
                    jQuery(that).animate({ width: jQuery(that).attr("data-value") + "%" }, {duration: 2000, easing:'swing'});
                  }
            });

            // Run counters
            jQuery('.swift-counter').each(function() {
                  jQuery(this).swiftCount();
            });
      });

      // Increase/decrease threads
      jQuery(document).on('click', '#swift-cache-status-box .change-thread-limit', function(){
            clearInterval(cache_status_interval);
            jQuery('body').addClass('swift-loading');
            var limit = (jQuery(this).hasClass('thread-plus') ? 1 : -1);
            jQuery.post(ajaxurl, {action: 'swift_performance_change_thread_limit', '_wpnonce' : swift_performance.nonce, 'limit' : limit}, function(){
                  cache_status(function(){
                        jQuery('body').removeClass('swift-loading');
                  });
            });
      });

      // Paginate list table
      jQuery(document).on('click', '#swift-performance-list-table-container .pagination-links a, .swift-performance-list-table thead th a', function(e){
            e.preventDefault();
            jQuery('body').addClass('swift-loading');
            jQuery('#swift-performance-list-table-url').val(jQuery(this).attr('href'));
            history.pushState(null, null, jQuery(this).attr('href'));
            jQuery.get(jQuery(this).attr('href'), function(source){
                  var html = jQuery.parseHTML(source);
                  jQuery('.swift-cache-table-container, .swift-images-table-container').each(function(){
                        var table         = jQuery(html).find('#' + jQuery(this).attr('id'));
                        if (jQuery(this).hasClass('swift-hidden')){
                              jQuery(table).addClass('swift-hidden')
                        }
                        else {
                              jQuery(table).removeClass('swift-hidden');
                        }
                        jQuery(this).replaceWith(table);

                  });
                  jQuery('body').trigger('swift-list-table-paginated');
                  jQuery('body').removeClass('swift-loading');
            });
      });

      // Filter list table
      jQuery(document).on('submit', '.swift-list-table-filter', function(e){
            e.preventDefault();
            jQuery('body').addClass('swift-loading');
            history.pushState(null, null, jQuery(this).attr('action') + '?' + jQuery(this).serialize());
            jQuery.get(jQuery(this).attr('action'), jQuery(this).serialize(), function(source){
                  var html = jQuery.parseHTML(source);
                  jQuery('.swift-cache-table-container, .swift-images-table-container').each(function(){
                        jQuery(this).replaceWith(jQuery(html).find('#' + jQuery(this).attr('id')));
                  });
                  jQuery('body').removeClass('swift-loading');
            });
      });

      // Refresh warmup
      jQuery(document).on('click', '#swift-performance-refresh-list-table', function(e){
            e.preventDefault();
            jQuery('body').addClass('swift-loading');
            jQuery.get(document.location.href, function(source){
                  var html = jQuery.parseHTML(source);
                  jQuery('#swift-performance-list-table-container').replaceWith(jQuery(html).find('#swift-performance-list-table-container'));
                  jQuery('body').removeClass('swift-loading');
            });
      });

      // Reset warmup
      jQuery(document).on('click', '#swift-performance-reset-warmup', function(e){
            e.preventDefault();
            if (confirm(__('Do you want to reset prebuild links?'))){
                  jQuery('body').addClass('swift-loading');
                  jQuery.post(ajaxurl, {action: 'swift_performance_reset_warmup', '_wpnonce' : swift_performance.nonce}, function(){
                        jQuery('#swift-performance-refresh-list-table').trigger('click');
                  });
            }
      });

      // Clear cache
      jQuery(document).on('click', '.swift-performance-clear-cache', function(e){
            jQuery('body').addClass('swift-loading');
            var type = jQuery(this).attr('data-type');
            jQuery.post(ajaxurl, {action: 'swift_performance_clear_cache', '_wpnonce' : swift_performance.nonce, 'type': type}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery('body').removeClass('swift-loading');
                  show_message(response);
                  jQuery('#swift-performance-refresh-list-table').trigger('click');
            });
            e.preventDefault();
      });

      // Clear assets cache
      jQuery(document).on('click', '#swift-performance-clear-assets-cache', function(e){
            jQuery('body').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_clear_assets_cache', '_wpnonce' : swift_performance.nonce}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery('body').removeClass('swift-loading');
                  show_message(response);
            });
            e.preventDefault();
      });

      // Custom Purge Toggle
      jQuery(document).on('click', '#swift-performance-custom-purge-toggle', function(e){
            e.preventDefault();
            jQuery('.swift-performance-custom-purge-container').toggleClass('swift-hidden');
      });

      // Custom Purge Trigger
      jQuery(document).on('click', '#swift-performance-custom-purge-trigger', function(e){
            e.preventDefault();
            jQuery('body').addClass('swift-loading');

            jQuery.post(ajaxurl, {action: 'swift_performance_custom_purge', '_wpnonce' : swift_performance.nonce, 'rule': jQuery('#swift-performance-custom-purge-url').val()}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery('#swift-performance-custom-purge-url').val('');
                  jQuery('body').removeClass('swift-loading');
                  show_message(response);
            });
      });

      jQuery(document).on('click', '.swift-cache-table-switch', function(e){
            e.preventDefault();
            jQuery('.swift-cache-table-switch').removeClass('active');
            jQuery(this).addClass('active');

            jQuery('.swift-cache-table-container, .swift-images-table-container').addClass('swift-hidden');
            jQuery(jQuery(this).attr('data-table')).removeClass('swift-hidden');
      });

      // Start prebuild cache
      jQuery(document).on('click', '#swift-performance-prebuild-cache', function(e){
            jQuery('#swift-performance-prebuild-cache').addClass('swift-hidden');
            jQuery('#swift-performance-stop-prebuild-cache').removeClass('swift-hidden');
            jQuery('body').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_prebuild_cache', '_wpnonce' : swift_performance.nonce}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery('body').removeClass('swift-loading');
                  show_message(response);
            });
            e.preventDefault();
      });

      // Stop prebuild cache
      jQuery(document).on('click', '#swift-performance-stop-prebuild-cache', function(e){
            jQuery('#swift-performance-stop-prebuild-cache').addClass('swift-hidden');
            jQuery('#swift-performance-prebuild-cache').removeClass('swift-hidden');
            jQuery('body').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_stop_prebuild_cache', '_wpnonce' : swift_performance.nonce}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery('body').removeClass('swift-loading');
                  show_message(response);
            });
            e.preventDefault();
      });

      // Change prebuild priority
      jQuery(document).on('submit', '.swift-priority-update', function(e){
            e.preventDefault();
            var form = jQuery(this);
            var data = jQuery(form).serialize();
            jQuery(form).closest('td').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_update_prebuild_priority', '_wpnonce' : swift_performance.nonce, 'data' : data}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery(form).closest('td').removeClass('swift-loading');
                  show_message(response);
            });
      });

      // Single prebuild
      jQuery(document).on('click', '.swift-performance-list-table .do-cache', function(e){
            e.preventDefault();
            var button = jQuery(this);
            var row = jQuery(button).closest('tr');
            jQuery(button).closest('td').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_single_prebuild', '_wpnonce' : swift_performance.nonce, 'url' : jQuery(button).attr('data-url')}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery(button).closest('td').removeClass('swift-loading');
                  show_message(response);
                  update_warmup_row(row, response);
            });
      });

      // Clear single
      jQuery(document).on('click', '.swift-performance-list-table .clear-cache', function(e){
            e.preventDefault();
            var button = jQuery(this);
            var row = jQuery(button).closest('tr');
            jQuery(button).closest('td').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_single_clear_cache', '_wpnonce' : swift_performance.nonce, 'url' : jQuery(button).attr('data-url')}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery(button).closest('td').removeClass('swift-loading');
                  show_message(response);

                  // Remove if 404 cleared
                  if (jQuery(button).attr('data-status') == '404'){
                        jQuery(row).remove();
                  }
                  else {
                        update_warmup_row(row, response);
                  }
            });
      });

      // Clear single dynamic
      jQuery(document).on('click', '.swift-performance-list-table .clear-single-dynamic-url', function(e){
            e.preventDefault();
            var button = jQuery(this);
            var row = jQuery(button).closest('tr');
            jQuery(button).closest('td').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_single_dynamic_clear_cache', '_wpnonce' : swift_performance.nonce, 'url' : jQuery(row).attr('data-id')}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery(button).closest('td').removeClass('swift-loading');
                  show_message(response);

                  jQuery(row).remove();

            });
      });

      // Clear single ajax
      jQuery(document).on('click', '.swift-performance-list-table .clear-single-ajax-url', function(e){
            e.preventDefault();
            var button = jQuery(this);
            var row = jQuery(button).closest('tr');
            jQuery(button).closest('td').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_single_ajax_clear_cache', '_wpnonce' : swift_performance.nonce, 'url' : jQuery(row).attr('data-id')}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery(button).closest('td').removeClass('swift-loading');
                  show_message(response);

                  jQuery(row).remove();

            });
      });

      // Remove warmup URL
      jQuery(document).on('click', '.remove-warmup-url', function(e){
            e.preventDefault();
            var button = jQuery(this);
            var row = jQuery(button).closest('tr');
            jQuery(button).closest('td').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_remove_warmup_url', '_wpnonce' : swift_performance.nonce, 'url' : jQuery(button).attr('data-url')}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery(button).closest('td').removeClass('swift-loading');
                  show_message(response);

                  // Remove if 404 cleared
                  if (response.type == 'success'){
                        jQuery(row).remove();
                  }
                  else {
                        update_warmup_row(row, response);
                  }
            });
      });

      // Add warmup link
      jQuery(document).on('click', '#swift-performance-add-warmup-link', function(e){
            e.preventDefault();
            jQuery('.swift-add-warmup-link-container').removeClass('swift-hidden');
      });

      jQuery(document).on('click', '#swift-performance-cancel-add-warmup-link', function(e){
            e.preventDefault();
            jQuery('.swift-add-warmup-link-container').addClass('swift-hidden');
      });

      jQuery(document).on('click', '#swift-save-warmup-link', function(e){
            e.preventDefault();
            var form = jQuery(this).closest('.field-container');
            jQuery.post(ajaxurl, {action: 'swift_performance_add_warmup_url', '_wpnonce' : swift_performance.nonce, 'url' : jQuery(form).find('[name="url"]').val(), 'priority' : jQuery(form).find('[name="priority"]').val()}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);
                  show_message(response)

                  // Link was successfully added
                  if (response.type == 'success'){
                        jQuery(form).find('input').val('');
                        jQuery('#swift-performance-refresh-list-table').trigger('click');
                  }
            });
      });

      // Show Rewrite Rules
      jQuery(document).on('click', '#swift-performance-show-rewrite', function(e){
            clearInterval(_interval);

            jQuery('body').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_show_rewrites', '_wpnonce' : swift_performance.nonce}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);
                  show_message(response);

                  jQuery('.swift-preformatted-box').removeClass('swift-hidden');
                  jQuery('.swift-preformatted-box h3 .title').text(response.title);
                  jQuery('.swift-preformatted-box pre.response-container').text(response.rewrites);
                  jQuery('body').removeClass('swift-loading');
            });
            e.preventDefault();
      });

      // Show Log
      jQuery(document).on('click', '#swift-performance-log', function(e){
            clearInterval(_interval);

            jQuery('body').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_show_log', '_wpnonce' : swift_performance.nonce}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);
                  show_message(response);

                  jQuery('.swift-preformatted-box').removeClass('swift-hidden');
                  jQuery('.swift-preformatted-box h3 .title').text(response.title);
                  jQuery('.swift-preformatted-box pre.response-container').text(response.status);
                  jQuery('body').removeClass('swift-loading');
            });
            _interval = setInterval(function(){
                  var scroll_top = jQuery('.swift-preformatted-box .response-container').scrollTop();
                  jQuery.post(ajaxurl, {action: 'swift_performance_show_log', '_wpnonce' : swift_performance.nonce}, function(response){
                        response = (typeof response === 'string' ? JSON.parse(response) : response);

                        jQuery('.swift-preformatted-box pre.response-container').text(response.status);
                        jQuery('.swift-preformatted-box .response-container').scrollTop(scroll_top);
                  });
            }, 5000);
            e.preventDefault();
      });

      // Clear logs
      jQuery(document).on('click', '#swift-performance-clear-logs', function(e){
            e.preventDefault();
            if (confirm(__('Do you want to clear all logs'))){
                  jQuery('body').addClass('swift-loading');
                  jQuery.post(ajaxurl, {action: 'swift_performance_clear_logs', '_wpnonce' : swift_performance.nonce}, function(){
                        jQuery('#swift-performance-log').trigger('click');
                  });
            }
      });

      // Debug connection
      jQuery(document).on('click', '#swift-performance-debug-api', function(e){
            e.preventDefault();
            jQuery('body').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_debug_api', '_wpnonce' : swift_performance.nonce}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);
                  show_message(response);

                  jQuery('.swift-preformatted-box').removeClass('swift-hidden');
                  jQuery('.swift-preformatted-box h3 .title').text(response.title);
                  jQuery('.swift-preformatted-box pre.response-container').text(response.status);
                  jQuery('body').removeClass('swift-loading');
            });
      });

      // Developer Mode
      jQuery(document).on('click', '#swift-performance-toggle-developer-mode', function(e){
            e.preventDefault();
            jQuery('body').addClass('swift-loading');
            jQuery.post(ajaxurl, {action: 'swift_performance_toggle_dev_mode', '_wpnonce' : swift_performance.nonce}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  jQuery('body').removeClass('swift-loading');
                  jQuery('#swift-performance-toggle-developer-mode > span').toggleClass('swift-hidden');
                  show_message(response);
            });
      });

      /*
       * DB Optimizer
       */

      // Backup confirmation
      jQuery(document).on('click','.swift-confirm-backup', function(e){
            e.preventDefault();
            jQuery('.swift-dashboard').removeClass('content-blurred');
            jQuery(this).parent().remove();
      });

      // Ajax actions
      jQuery(document).on('click', '.swift-db-optimizer-action', function(e){
            e.preventDefault();
            var action  = jQuery(this).attr('id');
            var count   = jQuery(this).closest('ul').find('.count');
            jQuery(count).html('<span class="dashicons dashicons-update swift-spin"></span>');
            jQuery.post(ajaxurl, {'action': 'swift_performance_db_optimizer', 'swift-action': action, '_wpnonce' : swift_performance.nonce}, function(response){
                  jQuery(count).html(response);
            });
      });

      // Toggle schedule form
      jQuery(document).on('click', '.swift-toggle-scheduled-dbo', function(e){
            e.preventDefault();
            jQuery('#schedule-' + jQuery(this).attr('data-option')).toggleClass('swift-hidden');
      });

      // Change status for scheduled event
      jQuery(document).on('click', '.swift-scheduled-dbo-change', function(e){
            e.preventDefault();
            var option  = jQuery(this).closest('form').find('[name="option"]').val();
            var data    = jQuery(this).closest('form').serialize();
            var action  = jQuery(this).attr('data-action');
            data        += '&action=swift_performance_db_optimizer&swift-action=' + action + '&_wpnonce=' + swift_performance.nonce;

            jQuery('body').addClass('swift-loading');

            jQuery.post(ajaxurl, data, function(response){
                  if (response.match('is-scheduled')){
                        jQuery('#trigger-' + option).addClass('is-scheduled');
                  }
                  else {
                        jQuery('#trigger-' + option).removeClass('is-scheduled');
                        jQuery('#schedule-' + option + ' input').removeProp('checked');
                  }

                  jQuery('body').removeClass('swift-loading');
                  jQuery('#schedule-' + option).addClass('swift-hidden');
            });
      });


      // Edit Plugin Rule
      jQuery(document).on('click', '.swift-performance-edit-plugin-rule', function(e){
            e.preventDefault();
            var summary       = '';
            var container     = jQuery(this).closest('.swift-box-inner');
            if (jQuery(this).closest('.plugin-rule').find('select').length > 0){
                  jQuery(this).closest('.plugin-rule').find('select option:selected').each(function(){
                        summary += jQuery(this).text() + ', ';
                  });
                  summary = summary.replace(/,\s$/, '');
            }
            else {
                  summary = jQuery(this).closest('.plugin-rule').find('input').val();
            }

            if (summary == ''){
                summary = __('Not set');
            }

            if (jQuery(this).closest('.plugin-rule').hasClass('is-editing')){
                  save_plugin_organizer_rule();
            }

            jQuery(this).closest('.plugin-rule').toggleClass('is-editing');
            jQuery(container).toggleClass('disabled');
            jQuery(this).closest('.plugin-rule').find('.rule-summary').empty().text(summary);
      });

      // Cancel editing
      jQuery(document).on('click', '.cancel-editing', function(e){
            e.preventDefault();
            jQuery(this).closest('.plugin-rule').removeClass('is-editing');
            jQuery(this).closest('.swift-box-inner').removeClass('disabled');
      });

      // Remove Plugin Rule
      jQuery(document).on('click', '.swift-performance-remove-plugin-rule', function(e){
           e.preventDefault();
           jQuery(this).closest('.swift-box-inner').removeClass('disabled');
           jQuery(this).closest('.plugin-rule').remove();
           save_plugin_organizer_rule();
      });

      // Show rule help
      jQuery(document).on('change', '.rule-mode-selector', function(){
            var mode = jQuery(this).val();
            if (mode !== ''){
                  jQuery(this).closest('.swift-box-inner').find('.swift-plugin-rule-help').addClass('swift-hidden');
                  jQuery(this).closest('.swift-box-inner').find('.swift-plugin-rule-help.swift-help-' + mode).removeClass('swift-hidden');
            }
      });
      jQuery('.rule-mode-selector').trigger('change');

      // Add Disable Plugin Rule
      jQuery(document).on('click', '.swift-add-plugin-rule', function(e){
            e.preventDefault();
            var container     = jQuery(this).closest('.swift-box-inner');
            var type          = jQuery(container).find('.rule-mode-selector option:selected').attr('data-type');
            var mode          = jQuery(container).find('.rule-mode-selector').val();
            var slug          = jQuery(container).attr('data-plugin');
            var clone         = jQuery('#swift-plugin-rule-samples').find('.' + mode + '-sample').clone();

            var randid        = parseInt(Math.random()*1000000000);

            if (mode !== ''){
                  if (jQuery(clone).hasClass('editable')){
                        jQuery(clone).addClass('is-editing');
                        jQuery(container).addClass('disabled');
                  }
                  jQuery(clone).find('input, select').each(function(){
                        jQuery(this).attr('name', jQuery(this).attr('name').replace('%SLUG%', slug));
                        jQuery(this).attr('name', jQuery(this).attr('name').replace('%TYPE%', type));
                        jQuery(this).attr('name', jQuery(this).attr('name').replace('%RANDID%', randid));
                        if (type == 'exception'){
                              jQuery(clone).find('i.fa-ban').attr('class', 'fas fa-check');
                        }
                  });

                  jQuery(clone).appendTo(jQuery(container).find('ul.rule-container'));
                  if (!jQuery(clone).hasClass('is-editing')) {
                        save_plugin_organizer_rule();
                  }
            }
      });

      /**
       * Save plugin organizer rule
       */
       function save_plugin_organizer_rule(){
            jQuery('body').addClass('swift-loading');
            jQuery.post(document.location.href, jQuery('#plugin-organizer').serialize(), function(){
                  jQuery('body').removeClass('swift-loading');
            });
       }

      /**
       * Show Cache Status
       */
      function cache_status(callback){
            var ids=[];
            var dids=[];
            jQuery('#warmup-table-container tr').each(function(){
              if (jQuery(this).attr('data-id')){
                        if (jQuery(this).attr('data-id').match('_')){
                              dids.push(jQuery(this).attr('data-id'));
                        }
                        else {
                              ids.push(jQuery(this).attr('data-id'));
                        }
            	}
            });

            jQuery.post(ajaxurl, {action: 'swift_performance_cache_status', '_wpnonce' : swift_performance.nonce, 'ids' : ids, 'dids': dids}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);

                  if (typeof response.type !== 'undefined' && response.type == 'critical'){
                        jQuery(window).unbind('focus');
                        clearTimeout(cache_status_interval);
                        show_message(response, true);
                  }

                  jQuery('#swift-cache-status-box .prebuild-status').text(response.prebuild);
                  if (response.prebuild == ''){
                        jQuery('#swift-performance-stop-prebuild-cache').addClass('swift-hidden');
                        jQuery('#swift-performance-prebuild-cache').removeClass('swift-hidden');
                  }
                  else {
                        jQuery('#swift-performance-prebuild-cache').addClass('swift-hidden');
                        jQuery('#swift-performance-stop-prebuild-cache').removeClass('swift-hidden');
                  }

                  jQuery('#swift-cache-status-box .warmup-pages-count').text(response.all_pages);
                  jQuery('#swift-cache-status-box .cached-pages-count').text(response.cached_pages);

                  jQuery('#swift-cache-status-box .cache-size-count').text(response.size);
                  jQuery('#swift-cache-status-box .thread-count').html(response.threads);

                  jQuery('#swift-cache-status-box .ajax-object-count').text(response.ajax_objects);
                  jQuery('#swift-cache-status-box .ajax-size-count').text(response.ajax_size);

                  jQuery('#swift-cache-status-box .cached-dynamic-pages-count').html(response.dynamic_pages);
                  jQuery('#swift-cache-status-box .cached-dynamic-size-count').html(response.dynamic_size);

                  // Update status list
                  if (response.status_list){
                        for (i in response.status_list){
                              update_warmup_row(jQuery("[data-id=" + response.status_list[i]['id'] + "]"), {'status': response.status_list[i]['type'], 'date': response.status_list[i]['date']});
                        }
                  }

                  if (response.dynamic_table !== ''){
                        jQuery('#dynamic-cache-table-container').empty().append(jQuery(response.dynamic_table));
                  }

                  if (response.ajax_table !== ''){
                        jQuery('#ajax-cache-table-container').empty().append(jQuery(response.ajax_table));
                  }

                  if (typeof callback === 'function'){
                        callback();
                  }

                  // Update credits
                  if (!empty(response.credit)){
                        jQuery('.swift-performance-credit-bar-inner').each(function(){
                              var type = jQuery(this).attr('data-type');
                              jQuery(this).attr('data-credit', response.credit[type]);
                        });
                        update_credit();
                  }

                  clearTimeout(cache_status_interval);
                  cache_status_interval = setTimeout(cache_status, 5000);

            });
      }

      /**
       * Update warmup table row
       * @param Object row
       * @param Object response
       */
      function update_warmup_row(row, response){
            // Status
            if (typeof response.status !== 'undefined'){
                  jQuery(row).find('.column-status .dashicons').addClass('swift-hidden');
                  if (response.status == 'html' || response.status == 'json' || response.status == 'xml' || response.status == 'proxy'){
                        jQuery(row).find('.view-cached').removeClass('swift-hidden');
                        jQuery(row).find('.do-cache').addClass('swift-hidden');
                        jQuery(row).find('.clear-cache').removeClass('swift-hidden');

                        if (response.status != 'proxy'){
                              jQuery(row).find('.column-status .dashicons-yes').removeClass('swift-hidden');
                        }
                        else {
                              jQuery(row).find('.column-status .dashicons-cloud').removeClass('swift-hidden');
                        }
                  }
                  else if (response.status == 'error') {
                        jQuery(row).find('.column-status .dashicons-editor-strikethrough').removeClass('swift-hidden');
                  }
                  else if (response.status == 'redirect') {
                        jQuery(row).find('.column-status .dashicons-redo').removeClass('swift-hidden');
                  }
                  else if (response.status == '404') {
                        jQuery(row).find('.column-status .dashicons-warning').removeClass('swift-hidden');
                  }
                  else {
                        jQuery(row).find('.view-cached').addClass('swift-hidden');
                        jQuery(row).find('.column-status .dashicons-no').removeClass('swift-hidden');
                        jQuery(row).find('.do-cache').removeClass('swift-hidden');
                        jQuery(row).find('.clear-cache').addClass('swift-hidden');
                  }
            }

            // Date
            if (typeof response.date !== 'undefined'){
                  jQuery(row).find('.column-date').empty().text(response.date);
            }
      }

      /**
       * Show message if any
       * @param object response
       */
      function show_message(response, permanent){
            var permanent = permanent || false;
            if (typeof response.text !== 'undefined' && response.text.length > 0){
                  if (permanent){
                        jQuery('.swift-message').removeClass('success warning critical swift-hidden').addClass(response.type).text(response.text);
                        var message_top = jQuery('.swift-message').offset().top - 42;
                        if (jQuery("html").scrollTop() > message_top || jQuery("body").scrollTop() > message_top){
                              jQuery("html, body").animate({scrollTop: message_top});
                        }
                  }
                  else {
                        var result = (response.type == 'success' ? 'luv-success' : 'luv-error');
                        var notice = jQuery('<div>', {
                              'class': 'luv-framework-notice ' + result,
                        }).append(jQuery('<span>', {
                              'class': 'luv-framework-notice-inner',
                              'text': response.text
                        }));

                        jQuery('body').append(notice);
                        setTimeout(function(){
                              jQuery(notice).find('.luv-framework-notice-inner').css('max-width', '100%');
                        }, 100);
                        setTimeout(function(){
                              jQuery(notice).remove();
                        }, 5000);
                  }
            }
      }

      /**
      * Clear messages
      */
      function clear_messages(){
           jQuery('.swift-message').empty();
           jQuery('.swift-message').attr('class', 'swift-message swift-hidden');
      }

      /**
       * Add new meta box row
       */
      jQuery(document).on('click', '.swift-meta-box-group .add-new-row', function(e){
            e.preventDefault();
            var row = jQuery(this).closest('.swift-meta-box-group').find('.sample').clone().removeClass('swift-hidden sample');
            jQuery(row).insertBefore(this);
      });

       /**
        * Delete meta box row
        */
      jQuery(document).on('click', '.swift-meta-box-group .remove-row', function(e){
            e.preventDefault();
            jQuery(this).closest('.swift-meta-box-row').remove();
      });

      /**
       * Show tooltips
       */
      function pointers(){
            var item = jQuery('[data-swift-pointer]:first');
            if (jQuery(item).length > 0){
                  jQuery(item).pointer({
                        content: jQuery(item).attr('data-swift-pointer-content'),
                        position: jQuery(item).attr('data-swift-pointer-position'),
                        buttons: function( event, t ) {
      				var close  = swift_performance.i18n['Dismiss'],
      					button = jQuery('<a class="close" href="#">' + close + '</a>');

      				return button.bind( 'click.pointer', function(e) {
      					e.preventDefault();
      					t.element.pointer('close');
      				});
      			},
                        hide: function( event, t ) {
      				t.pointer.hide();
      				t.closed();
                              jQuery.post(ajaxurl, {'action': 'swift_performance_dismiss_pointer', 'id': jQuery(item).attr('data-swift-pointer'), '_wpnonce': swift_performance.nonce})
      			},
                  }).pointer('open');
            }
      }

      /* FRAMEWORK CUSTOMIZATIONS */

      // Hide info popups on tab chane
      jQuery('.luv-framework-tab').on('luv-tab-changed', function(){
            jQuery('.wp-pointer').css('display', 'none');
      });

      // Settings mode switch
      jQuery(document).on('change', '.swift-settings-mode input', function(){
            jQuery('[name="_luv_settings-mode"]').val(jQuery('.swift-settings-mode input:checked').val()).trigger('change');
      });

      //Image Optimizer Preset buttons
      jQuery(document).on('change', '.swift-performance-io-preset', function(){
            jQuery('[name="_luv_jpeg-quality"]').val(jQuery(this).attr('data-jpeg')).trigger('change');
            jQuery('[name="_luv_png-quality"]').val(jQuery(this).attr('data-png')).trigger('change');
            if (jQuery(this).attr('data-jpeg') * 1 < 100){
                  jQuery('[name="_luv_resize-large-images"]').attr('checked', true).trigger('change');
                  jQuery('[name="_luv_maximum-image-width"]').val('1920').trigger('change');
            }
            else {
                  jQuery('[name="_luv_resize-large-images"]').removeAttr('checked').trigger('change');
            }
      });

      // Clear cache after change settings
      jQuery(document).on('change', '.should-clear-cache input, .should-clear-cache select, .should-clear-cache textarea', function(){
            jQuery('.luv-framework-container.swift-performance-settings').attr('data-clear-cache', 'true');
            jQuery('.swift-performance-ajax-preview').addClass('swift-visible');
      });

      jQuery(document).on('change', '.should-refresh input, .should-refresh select, .should-refresh textarea', function(){
            jQuery('.luv-framework-container.swift-performance-settings').attr('data-refresh', 'true');
      });

      jQuery(document).on('luv-saved', '.luv-framework-container.swift-performance-settings', function(){
            if (jQuery(this).attr('data-clear-cache')){
                  jQuery('.luv-modal').empty().append(jQuery('.swift-confirm-clear-cache').clone().removeClass('luv-hidden')).removeClass('luv-modal-hide').show();
            }
            else if (jQuery(this).attr('data-refresh')){
                  document.location.reload();
            }
            jQuery('.swift-performance-ajax-preview').removeClass('swift-visible');
            jQuery(this).removeAttr('data-clear-cache');
      });

      // Reset Image Optimizer Presets
      jQuery(document).on('luv-reset', '.luv-framework-tab', function(){
            if (jQuery(this).find('#io-preset-lossless').length > 0){
                  jQuery(this).find('#io-preset-lossless').trigger('click');
            }
      });

      // Preview
      jQuery(document).on('click', '.swift-performance-ajax-preview', function(e){
            e.preventDefault();
            jQuery('body').addClass('swift-loading');

            jQuery.post(luv_framework_fields.ajax_url, _serialize(jQuery(this).attr('data-fieldset')) + '&action=' + 'swift_performance_preview&_wpnonce=' + swift_performance.nonce, function(response){
                  jQuery('.luv-framework-section-header .has-issues').each(function(){
                        jQuery(this).removeClass('has-issues').removeClass('has-error').removeClass('has-warning');
                  });
                  window.open(response.url);
                  jQuery('body').removeClass('swift-loading');
            });
      });

      // Clear cache
      jQuery(document).on('click', '[data-swift-clear-cache]', function(e){
		e.preventDefault();
            if (jQuery(this).closest('.luv-modal').length > 0){
                  jQuery(this).closest('.luv-modal').addClass('luv-modal-hide');
            }
            else if (jQuery(this).closest('[data-message-id]').length > 0){
                  jQuery.post(ajaxurl, {action: 'swift_performance_dismiss_notice', '_wpnonce' : swift_performance.nonce, 'id': jQuery(this).closest('[data-message-id]').attr('data-message-id')});
                  jQuery(this).closest('[data-message-id]').fadeOut();
            }

            var type = (window.swift_performance.clear_home_only ? 'homepage' : 'all');

            console.log(type);

            jQuery.post(ajaxurl, {action: 'swift_performance_clear_cache', '_wpnonce' : swift_performance.nonce, 'type': type}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);
                  show_message(response);
                  if (jQuery('.luv-framework-container.swift-performance-settings').attr('data-refresh')){
                        document.location.reload();
                  }
            });
	});

      // Should clear cache after change
      jQuery(document).on('change', '.should-clear-cache input, .should-clear-cache select, .should-clear-cache textarea', function(){
		jQuery('.luv-framework-container.swift-performance-settings').attr('data-clear-cache', 'true');
            jQuery('.swift-performance-ajax-preview').addClass('swift-visible');
	});

      // Should reset warmup after change
      jQuery(document).on('change', '.should-reset-warmup input, .should-reset-warmup select, .should-reset-warmup textarea', function(){
            jQuery('.luv-framework-container.swift-performance-settings').attr('data-reset-warmup', 'true');
      });

      // Reset Warmup
      jQuery(document).on('click', '[data-swift-reset-warmup]', function(e){
		e.preventDefault();
            if (jQuery(this).closest('.luv-modal').length > 0){
                  jQuery(this).closest('.luv-modal').addClass('luv-modal-hide');
            }
            else if (jQuery(this).closest('[data-message-id]').length > 0){
                  jQuery.post(ajaxurl, {action: 'swift_performance_dismiss_notice', '_wpnonce' : swift_performance.nonce, 'id': jQuery(this).closest('[data-message-id]').attr('data-message-id')});
                  jQuery(this).closest('[data-message-id]').fadeOut();
            }

            jQuery.post(ajaxurl, {action: 'swift_performance_reset_warmup', '_wpnonce' : swift_performance.nonce}, function(response){
                  response = (typeof response === 'string' ? JSON.parse(response) : response);
                  show_message(response);
            });
	});

	jQuery(document).on('luv-saved', '.luv-framework-container.swift-performance-settings', function(){
		if (jQuery(this).attr('data-clear-cache')){
			jQuery('.luv-modal').empty().append(jQuery('.swift-confirm-clear-cache').clone().removeClass('luv-hidden')).removeClass('luv-modal-hide').show();
		}

            if (jQuery(this).attr('data-reset-warmup')){
                  jQuery('.luv-modal').empty().append(jQuery('.swift-confirm-reset-warmup').clone().removeClass('luv-hidden')).removeClass('luv-modal-hide').show();
            }

            jQuery('.swift-performance-ajax-preview').removeClass('swift-visible');
		jQuery(this).removeAttr('data-clear-cache');
            jQuery(this).removeAttr('data-reset-warmup');
	});

      // Reset Image Optimizer
      jQuery(document).on('luv-reset', '.luv-framework-tab', function(){
            if (jQuery(this).find('#io-preset-lossless').length > 0){
                  jQuery(this).find('#io-preset-lossless').trigger('click');
            }
      });

      // Dismiss notice
      jQuery(document).on('click', '[data-swift-dismiss-notice]', function(){
            jQuery.post(ajaxurl, {action: 'swift_performance_dismiss_notice', '_wpnonce' : swift_performance.nonce, 'id': jQuery(this).closest('[data-message-id]').attr('data-message-id')});
            jQuery(this).closest('[data-message-id]').fadeOut();
      });

      /* Lite upgrade */

      // Show register form
      jQuery(document).on('click', '#swift-performance-lite-activate', function(e){
            e.preventDefault();
            jQuery('.swift-performance-lite-license-form, #swift-performance-lite-purchase-key h4').addClass('swift-hidden');
            jQuery('#swift-performance-license-form').removeClass('swift-hidden');
      });

      // Already have license key
      jQuery(document).on('click', '.swift-performance-already-have-license', function(e){
            e.preventDefault();
            jQuery('#swift-performance-license-form').addClass('swift-hidden');
            jQuery('#swift-performance-lite-purchase-key').removeClass('swift-hidden');
      });

      // Send license key
      jQuery(document).on('submit', '#swift-performance-license-form', function(e){
            e.preventDefault();
            var form = jQuery(this);
            var data = parse_str(jQuery(form).serialize());

            jQuery(form).find('.swift-error').addClass('swift-hidden');
            jQuery(form).find('.error-container').text(jQuery(form).find('.error-container').attr('data-default'));

            if (empty(data['name']) || empty(data['email'])){
                  jQuery(form).find('.error-missing-field').removeClass('swift-hidden');
            }
            else if (empty(data['terms'])){
                  jQuery(form).find('.error-accept-terms').removeClass('swift-hidden');
            }
            else {
                  data['action']    = 'swift_performance_send_license_key';
                  data['_wpnonce']  = swift_performance.nonce;
                  jQuery('.swift-performance-lite-license-form').addClass('swift-loading');
                  jQuery.post(ajaxurl, data, function(response){
                        jQuery('.swift-performance-lite-license-form').removeClass('swift-loading');
                        if (!empty(response.result) && response.result == 'success'){
                              jQuery(form).addClass('swift-hidden');
                              jQuery('#swift-performance-lite-purchase-key h4').removeClass('swift-hidden');
                              jQuery('#swift-performance-lite-purchase-key').removeClass('swift-hidden');
                        }
                        else {
                              jQuery(form).find('.error-container').removeClass('swift-hidden');
                              if (!empty(response.message)){
                                    jQuery(form).find('.error-container').text(response.message);
                              }
                        }
                  });
            }
      });

      // Resend license key
      jQuery(document).on('click', '#swift-resend-license-key', function(e){
            e.preventDefault();
            jQuery('#swift-performance-license-form').submit();
      });

      // Activate license
      jQuery(document).on('submit', '#swift-performance-lite-purchase-key', function(e){
            e.preventDefault();

            var form = jQuery(this);
            var data = parse_str(jQuery(form).serialize());

            jQuery(form).find('.swift-error').addClass('swift-hidden');
            jQuery(form).find('.error-container').text(jQuery(form).find('.error-container').attr('data-default'));

            if (empty(data['license-key'])){
                  jQuery(form).find('.error-missing-field').removeClass('swift-hidden');
            }
            else {
                  data['action']    = 'swift_performance_activate';
                  data['_wpnonce']  = swift_performance.nonce;
                  jQuery('.swift-performance-lite-license-form').addClass('swift-loading');
                  jQuery.post(ajaxurl, data, function(response){
                        if (!empty(response.result) && response.result == 'success'){
                              document.location.reload();
                        }
                        else {
                              jQuery('.swift-performance-lite-license-form').removeClass('swift-loading');
                              jQuery(form).find('.error-container').removeClass('swift-hidden');
                              if (!empty(response.message)){
                                    jQuery(form).find('.error-container').text(response.message);
                              }
                        }
                  });
            }
      });

      function update_credit(){
            jQuery('.swift-performance-credit-container').each(function(item){
                  var count   = jQuery(this).find('span');
                  var bar     = jQuery(this).find('.swift-performance-credit-bar-inner');
                  var total   = jQuery(bar).attr('data-total');
                  var credit  = jQuery(bar).attr('data-credit');

                  jQuery(count).text(jQuery(count).text().replace(/(\d+)/, credit));
                  jQuery(bar).css('width', (credit/total*100) + '%');

            });
      }
      update_credit();

      /* Helper Functions */

      /**
       * Check if a value is empty
       * @param value
       * @return boolean
       */
      function empty(value){
            return (value === 0 || value === '' || value === null || value === undefined || value === false);
      }

      /**
       * Parse query string and return an array
       * @param string
       * @return array
       */
      function parse_str(str){
            var parsed = {};
            str.replace(
                  new RegExp(
                        "([^?=&]+)(=([^&#]*))?", "g"),
                        function($0, $1, $2, $3) { parsed[$1] = decodeURIComponent($3); }
                  );
            return parsed;
      }

      /**
       * Serialize fieldset
       * @param string s
       * @return string
       */
      function _serialize(s){
            var params = '';
            jQuery(s).find('input:not([type="checkbox"]):not([type="radio"]), input[type="checkbox"]:checked, input[type="radio"]:checked , option:selected, textarea').each(function(){
                  var name = (jQuery(this).is('option') ? jQuery(this).closest('select').attr('name') : jQuery(this).attr('name'));
                  params += name + '=' + encodeURIComponent(jQuery(this).val()) + '&';
            });
            return _trim(params, '&');
      }

      /**
       * Trim characters from string
       * @param string s
       * @param string c
       * @return string
       */
      function _trim (s, c) {
       if (c === "]") c = "\\]";
       if (c === "\\") c = "\\\\";
       return s.replace(new RegExp(
          "^[" + c + "]+|[" + c + "]+$", "g"
       ), "");
      }

      /**
       * Localize strings
       * @param string text
       * @return string
       */
      function __(text){
            if (typeof swift_performance.i18n[text] === 'string'){
                  return swift_performance.i18n[text]
            }
            else {
                  return text;
            }
      }
});


// Shortcuts
(function(){
	var keypressed = [];

	document.addEventListener('keydown', function(e){
            keypressed.push(e.keyCode);

            // Save
		if (keypressed.indexOf(224) !== -1 && keypressed.indexOf(83) !== -1 && keypressed.length == 2){
                  e.preventDefault();
			jQuery('.luv-framework-ajax-save').click();
                  return false;
		}

            // Clear only homepage cache
            if (keypressed.indexOf(16) !== -1 && keypressed.length == 1){
                  e.preventDefault();
			window.swift_performance.clear_home_only = true;
                  return false;
		}
            else {
                  window.swift_performance.clear_home_only = false;
            }

	});

	document.addEventListener('keyup', function(e){
		keypressed = [];
            window.swift_performance.clear_home_only = false;
	});

})();


/**
 * Counter effect
 * @param int countTo
 */
jQuery.fn.swiftCount = function(countTo, duration, easing){
      var that    = jQuery(this),
      unit        = n(countTo)[1] || n(jQuery(that).attr('data-count'))[1],
      countTo     = n(countTo)[0] || n(jQuery(that).attr('data-count'))[0],
      size        = countTo.split(".")[1] ? countTo.split(".")[1].length : 0,
      duration    = duration || 2000,
      easing      = easing || 'swing';

      jQuery({countNum: n(jQuery(that).text())[0]}).animate({
          countNum: countTo
      },
      {
          duration: duration,
          easing: easing,
          step: function(now) {
            jQuery(that).text(parseFloat(now).toFixed(size) + unit);
          }
      });

      /**
       * Return numeric and unit part of a string
       * @param string text
       * @return array
       */
      function n(text){
            if (typeof text !== 'undefined'){
                  text = text.toString();
                  var v = u = '';
                  for (var i in text){
                        if (u == '' && text[i].match(/[\d\.]/)){
                              v += text[i];
                        }
                        else if (typeof text[i] !== 'undefined'){
                              u += text[i];
                        }
                  }
                  return [v,u];
            }
            return [0,''];
      }
}
