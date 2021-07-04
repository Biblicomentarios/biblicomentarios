jQuery(
    function() {
        var $parent = jQuery('.notice-iworks-rate');
        jQuery('.iworks-rate-button, .notice-dismiss', $parent).on('click', function(e) {
            var data = {
                action: 'iworks_rate_button',
                plugin_id: $parent.data('id'),
                button: jQuery(this).data('action')
            };
            if ('get-help' === jQuery(this).data('action')) {
                return true;
            }
            jQuery.post(
                $parent.data('ajax-url'),
                data,
                function(response) {
                    $parent.detach();
                }
            );
            return true;
        });
    }
);