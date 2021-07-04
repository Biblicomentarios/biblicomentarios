(function($) {

  'use strict';

  $(document).ready(function() {

    'use strict';

    $('#daext-sort-form select').on('change', function() {
      $('#daext-sort-form').submit();
    });

    $('#update-archive').click(function() {

      //if another request is processed right now do not proceed with another ajax request
      if ($('#ajax-request-status').val() == 'processing') {
        return;
      }

      //prepare ajax request
      var data = {
        'action': 'daextam_generate_statistics',
        'security': window.daextamNonce,
      };

      //show the ajax loader
      $('#ajax-loader').show();

      //set the ajax request status
      $('#ajax-request-status').val('processing');

      //send ajax request
      $.post(window.daextamAjaxUrl, data, function() {

        //reload the dashboard menu ----------------------------------------
        window.location.replace(window.daextamAdminUrl + 'admin.php?page=daextam-statistics');

      });

    });

  });

}(window.jQuery));