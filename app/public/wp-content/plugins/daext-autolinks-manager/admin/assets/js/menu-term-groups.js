(function($) {

  'use strict';

  $(document).ready(function() {

    'use strict';

    //Handle Post Type Change ------------------------------------------------------------------------------------------
    $('.post-type').on('change', function() {

      //get the post type
      var postType = $(this).val();

      //get the id
      var id = parseInt($(this).attr('data-id'), 10);

      //delete the options of taxonomy with the same id
      $('#taxonomy-' + id + ' option:not(.default)').remove();

      //delete the options of terms with the same id
      $('#term-' + id + ' option:not(.default)').remove();

      //prepare ajax request
      var data = {
        'action': 'daextam_get_taxonomies',
        'security': window.daextamNonce,
        'post_type': postType,
      };

      //send ajax request
      $.post(window.daextamAjaxUrl, data, function(data) {

        var isJson = true,
            taxonomies = null;

        try {
          taxonomies = $.parseJSON(data);
        } catch (e) {
          isJson = false;
        }

        if (isJson) {

          //add the taxonomies
          $.each(taxonomies, function(index, taxonomy) {
            $('#taxonomy-' + id).append('<option value="' + taxonomy.name + '">' + taxonomy.label + '</option>');
          });

        }

        updateChosenField('#taxonomy-' + id, '0');

        updateChosenField('#term-' + id, '0');

      });

    });

    //Handle Taxonomy Change -------------------------------------------------------------------------------------------

    $('.taxonomy').on('change', function() {

      //get the taxonomy
      var taxonomy = $(this).val();

      //get the id
      var id = parseInt($(this).attr('data-id'), 10);

      //delete the options of terms with the same id
      $('#term-' + id + ' option:not(.default)').remove();

      //prepare ajax request
      var data = {
        'action': 'daextam_get_terms',
        'security': window.daextamNonce,
        'taxonomy': taxonomy,
      };

      //send ajax request
      $.post(window.daextamAjaxUrl, data, function(data) {

        var isJson = true,
            terms = null;

        try {
          terms = $.parseJSON(data);
        } catch (e) {
          isJson = false;
        }

        if (parseInt(data, 10) !== 0 && isJson) {

          //add the taxonomies
          $.each(terms, function(index, termObj) {
            $('#term-' + id).append('<option value="' + termObj.term_id + '">' + termObj.name + '</option>');
          });

        }

        updateChosenField('#term-' + id, '0');

      });

    });

    //Dialog Confirm ---------------------------------------------------------------------------------------------------
    window.DAEXTAM = {};
    $('.menu-icon.delete').click(function(event) {
      event.preventDefault();
      window.DAEXTAM.autolinkToDelete = $(this).prev().val();
      $('#dialog-confirm').dialog('open');
    });

  });

  /**
   * Update the selection of a specific chosen field (field_selector) based on the provided value (selected_value)
   *
   * @param field_selector
   * @param selected_value
   */
  function updateChosenField(field_selector, selected_value) {

    $(field_selector + ' option').removeAttr('selected');
    $(field_selector + ' option[value=' + selected_value + ']').attr('selected', 'selected');
    $(field_selector).trigger('chosen:updated');

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