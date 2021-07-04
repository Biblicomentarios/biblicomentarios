(function($) {

  'use strict';

  $(document).ready(function() {

    'use strict';

    removeBorderLastTableTr();

    $('.group-trigger').click(function() {

      //open and close the various sections of the tables area
      var target = $(this).attr('data-trigger-target');
      $('.' + target).toggle();

      $(this).find('.expand-icon').toggleClass('arrow-down');

      removeBorderLastTableTr();

      /**
       * Prevent a bug that causes the "All" text (used in the chosen multiple when there are no items selected) to be
       * hidden.
       */
      $('.chosen-container-multi .chosen-choices .search-field input').each(function() {
        $(this).css('width', 'auto');
      });

    });

    $('#cancel').click(function(event) {

      //reload the Autolinks menu
      event.preventDefault();
      window.location.replace(window.daextamAdminUrl + 'admin.php?page=daextam-autolinks');

    });

    //Initialize an object wrapper in the global context
    window.DAEXTAM = {};

    $('.menu-icon.delete').click(function(event) {
      event.preventDefault();
      window.DAEXTAM.autolinkToDelete = $(this).prev().val();
      $('#dialog-confirm').dialog('open');
    });

    //Submit the filter form when the select box changes
    $('#daext-filter-form select').on('change', function() {
      $('#daext-filter-form').submit();
    });

  });

  /*
   Remove the bottom border on the last visible tr included in the form
   */
  function removeBorderLastTableTr() {
    $('table.daext-form-table tr > *').css('border-bottom-width', '1px');
    $('table.daext-form-table tr:visible:last > *').css('border-bottom-width', '0');
  }

  /**
   * Original Version (not compatible with pre-ES5 browser)
   */
  // $(function() {
  //   $('#dialog-confirm').dialog({
  //     autoOpen: false,
  //     resizable: false,
  //     height: 'auto',
  //     width: 340,
  //     modal: true,
  //     buttons: {
  //       [objectL10n.deleteText]: function() {
  //         $('#form-delete-' + window.DAEXTAM.autolinkToDelete).submit();
  //       },
  //       [objectL10n.cancelText]: function() {
  //         $(this).dialog('close');
  //       },
  //     },
  //   });
  // });

  /**
   *
   * Compiled Version (compatible with pre-ES5 browser and ES5 browsers)
   *
   * Version compiled with Babel (https://babeljs.io).
   *
   * The reason is that dynamic property names (in this case [objectL10n.deleteText] and [objectL10n.cancelText] are not
   * supported with pre-ES5 JavaScript engines.
   */
  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {value: value, enumerable: true, configurable: true, writable: true});
    } else {
      obj[key] = value;
    }
    return obj;
  }

  $(function() {
    var _buttons;

    $('#dialog-confirm').dialog({
      autoOpen: false,
      resizable: false,
      height: 'auto',
      width: 340,
      modal: true,
      buttons: (_buttons = {}, _defineProperty(_buttons, window.objectL10n.deleteText, function() {
        $('#form-delete-' + window.DAEXTAM.autolinkToDelete).submit();
      }), _defineProperty(_buttons, window.objectL10n.cancelText, function() {
        $(this).dialog('close');
      }), _buttons),
    });
  });

}(window.jQuery));

