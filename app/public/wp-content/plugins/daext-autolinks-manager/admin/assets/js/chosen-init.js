(function($) {

  'use strict';

  $(document).ready(function() {

    'use strict';

    //initialize chosen on all the select elements
    var chosenElements = [];

    //Statistics Menu -------------------------------------------------------------------------------------------------
    addToChosen('sb');
    addToChosen('or');

    //Autolinks Menu ---------------------------------------------------------------------------------------------------
    addToChosen('cf');
    addToChosen('category-id');
    addToChosen('open-new-tab');
    addToChosen('use-nofollow');
    addToChosen('case-sensitive-search');
    addToChosen('left-boundary');
    addToChosen('right-boundary');
    addToChosen('post-types');
    addToChosen('categories');
    addToChosen('tags');
    addToChosen('term-group-id');

    //Terms Menu
    for (var i = 0; i <= 100; i++) {
      addToChosen('post-type-' + i);
      addToChosen('taxonomy-' + i);
      addToChosen('term-' + i);
    }

    //Options Menu -----------------------------------------------------------------------------------------------------

    //Defaults
    addToChosen('daextam-defaults-open-new-tab');
    addToChosen('daextam-defaults-use-nofollow');
    addToChosen('daextam-defaults-case-sensitive-search');
    addToChosen('daextam-defaults-left-boundary');
    addToChosen('daextam-defaults-right-boundary');
    addToChosen('daextam-defaults-categories');
    addToChosen('daextam-defaults-tags');
    addToChosen('daextam-defaults-term-group-id');

    //Analysis
    addToChosen('daextam-analysis-set-max-execution-time');
    addToChosen('daextam-analysis-set-memory-limit');
    addToChosen('daextam-analysis-categories');
    addToChosen('daextam-analysis-tags');

    //Advanced
    addToChosen('daextam-advanced-enable-autolinks');
    addToChosen('daextam-advanced-enable-test-mode');
    addToChosen('daextam-advanced-random-prioritization');
    addToChosen('daextam-advanced-ignore-self-autolinks');
    addToChosen('daextam-advanced-categories-and-tags-verification');
    addToChosen('daextam-advanced-general-limit-mode');
    addToChosen('daextam-advanced-protected-tags');
    addToChosen('daextam-advanced-protected-gutenberg-blocks');
    addToChosen('daextam-advanced-protected-gutenberg-embeds');

    //Post Editor
    addToChosen('daextam-enable-autolinks');

    $(chosenElements.join(',')).chosen({
      placeholder_text_multiple: window.objectL10n.chooseAnOptionText,
    });

    function addToChosen(elementId) {

      if ($('#' + elementId).length && chosenElements.indexOf($('#' + elementId)) === -1) {
        chosenElements.push('#' + elementId);
      }

    }

  });

})(window.jQuery);