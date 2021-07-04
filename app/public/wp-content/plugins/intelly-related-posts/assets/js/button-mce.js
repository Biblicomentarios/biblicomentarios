(function() {
    tinymce.PluginManager.add('irp_mce_button', function(editor, url) {
        editor.addButton('irp_mce_button', {
            title: 'Inline Related Posts'
            , type: 'menubutton'
            , icon: 'icon irp-own-icon'
            , image : url + '/../images/repeat.png'
            , menu: [
                {
                    text: 'Inline Related Post'
                    , onclick: function() {
                        var code='[irp]';
                        editor.insertContent(code);
                    }
                }
                , {
                    text: 'Custom Related Post'
                    , onclick: function() {
                        editor.windowManager.open({
                            title: 'Choose a post'
                            , width: 350
                            , height: 250
                            , file: ajaxurl+'?action=do_action&irp_action=ui_button_editor&irp_post_type=post'
                            , inline: 1
                            , resizable: false
                        });
                    }
                }
                , {
                    text: 'Custom Related Page'
                    , onclick: function() {
                        editor.windowManager.open({
                            title: 'Choose a page'
                            , width: 350
                            , height: 250
                            , file: ajaxurl+'?action=do_action&irp_action=ui_button_editor&irp_post_type=page'
                            , inline: 1
                            , resizable: false
                        });
                    }
                }
           ]
        });
    });
})();