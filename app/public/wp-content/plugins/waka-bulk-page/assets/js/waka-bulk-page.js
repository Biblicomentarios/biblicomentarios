;(function($){

    $(document).on('click', '#wbp-add-pages', function (e) {
        e.preventDefault();

        var $list = $('#wbp-new-pages');
        var pages = $list.val().split("\n");
        var parent = $("#wbp-parent").val() || '';
        var $template = $("#wbp-template");
        var $status = $("#wbp-status");
        var html = '';

        var idTemplate,idStatus;

        var itemParent = '';
        var currentDepth = 0;

        var tree = [];
        for (var i in pages) {
            if (pages.hasOwnProperty(i) && pages[i].length > 0) {

                // Counting number of "-" to get item depth
                itemDepth = (pages[i].match(/^-+/g) || []).toString().length;

                pageName = pages[i].replace(/^-+/g, '');
                id = uniqid();

                // Si 0 => on reconstruit l'arbo
                if(itemDepth == 0) {
                    tree = [];
                    tree[0] = [id,pageName];
                    itemParent = [parent,''];
                } else {
                    itemParent = tree[itemDepth - 1];
                    if(itemParent != undefined)
                    {
                        tree[itemDepth] = [id,pageName];
                    } else {
                        itemParent = [parent,''];
                    }
                }

                idTemplate = 'wbp-template-' + id;
                idStatus = 'wbp-status-' + id;



                // Children detected : parent = last fetch item
                html = '<li class="page_item_new page-item-' + id + '">' + pageName + '<a href="" class="wbp-remove">&times;</a>' +
                    '<input type="hidden" name="wbp-page[' + id + '][name]" value="' + pageName +'" />' +
                    '<input type="hidden" name="wbp-page[' + id + '][parent]" value="' + itemParent[0] +'" />' +
                    '<select name="wbp-page[' + id + '][status]" id="' + idStatus + '">' + $status.html() + '</select>' +
                    '<select name="wbp-page[' + id + '][template]" id="' + idTemplate + '">' + $template.html() + '</select>' +
                    '</li>';

                currentDepth = itemDepth;

                add_item(itemParent[0], html);

                // If a template was already selected
                $('#' + idTemplate).val( $template.val() );

                // If a status was already selected
                $('#' + idStatus).val( $status.val() );

                /*if(tpl[0])
                    $('#' + idTpl).val( tpl[0] );*/


            }
        }

        $list.val('');

        $("#wbp-add-pages").attr('disabled', 'disabled');

        checkBtnSubmit();

    });

    /**
     * Check if pages were created so we can activate the submit button
     */
    function checkBtnSubmit()
    {
        var $btnSubmit = jQuery(".wbp-btn-submit");

        $btnSubmit.attr('disabled', 'disabled');

        if(jQuery("#wbp-pages").find(".page_item_new").length > 0)
            $btnSubmit.removeAttr('disabled');
    }


    /* jQuery(document).on('change', '.wbp-template', function(e) {
        var that = $(this);
        if('new' == that.val())
        {
            that.after('<div id="add-new-tpl">' +
                '<input type="text" name="tpl-filename" value="" placeholder="template-name.php" /><br/>' +
                '<input type="text" name="tpl-name" value="" placeholder="Template name" /><input type="button" value="OK"/><br/>' +
                '* the file will automatically be created' +
                '</div>');
        }else {
            jQuery("#add-new-tpl").remove();
        }
    }); */

    function add_item(parent, html)
    {
        if (parent) {
            var $parent = $(".page-item-" + parent);

            if (!$parent.children('.children').length)
                $parent.append('<ul class="children"></ul>');

            $parent.children('.children').append(html);

        } else {
            $("#wbp-pages > ul").append(html);
        }
    }


    $(document).on('keyup', 'textarea#wbp-new-pages', function(e) {

        var $btnAdd = jQuery("#wbp-add-pages");

        if(jQuery(this).val().length >= 1)
            $btnAdd.removeAttr('disabled');
        else
            $btnAdd.attr('disabled', 'disabled');
    });

    $(document).on('change', '[name="create_menu"]', function(e){
        var val = $(this).val();

        var $menu_name = jQuery("#menu_name");
        if(val.length > 0)
            $menu_name.show();
        else
            $menu_name.hide();
    });

    $(document).on('click', '.wbp-remove', function(e){
        e.preventDefault();
        $(this).closest('.page_item_new').remove();
        checkBtnSubmit();
    });

    var uniqid = function() {
        return (new Date().getTime() + Math.floor((Math.random()*10000)+1)).toString(16);
    };

})(jQuery);