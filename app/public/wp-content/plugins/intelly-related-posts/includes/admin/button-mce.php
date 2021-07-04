<?php
add_action('admin_head', 'irp_add_mce_button');
function irp_add_mce_button() {
    global $typenow;

    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }

    if(!in_array($typenow, array('post', 'page'))) {
        return;
    }
    if (get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "irp_add_mce_plugin");
        add_filter('mce_buttons', 'irp_register_mce_button');
    }
}

function irp_add_mce_plugin($plugin_array) {
    $plugin_array['irp_mce_button']=IRP_PLUGIN_ASSETS.'js/button-mce.js';
    return $plugin_array;
}
function irp_register_mce_button($buttons) {
    array_push($buttons, "irp_mce_button");
    return $buttons;
}

function irp_ui_button_editor() {
    global $irp;

    $irp->Utils->printScriptCss();
    $irp->Form->prefix='Editor';
    $irp->Form->labels=FALSE;

    $args=array('class'=>'wp-admin wp-core-ui admin-bar'
        , 'style'=>'padding:10px; margin-left:auto; margin-right:auto;');
    $irp->Form->formStarts('post', '', $args);
    {
        ?>
        <p style="text-align:center;"><?php $irp->Lang->P('EditorSubtitle') ?></p>
        <?php
        $args = array('post_type' => 'post', 'all' => FALSE);
        $options = $irp->Utils->query(IRP_QUERY_POSTS_OF_TYPE, $args);
		$irp->Form->text('irpPostId', '', FALSE);
        ?>
        <div style="clear:both;"></div>
        <p style="text-align:right;">
            <input type="button" id="btnInsert" class="button button-primary irp-button irp-submit" value="<?php $irp->Lang->P('Insert')?>"/>
            <input type="button" id="btnClose" class="button irp-button" value="<?php $irp->Lang->P('Cancel')?>"/>
        </p>

        <script>
            jQuery(function () {
                jQuery('#btnInsert').click(function () {
                    var editor = top.tinymce.activeEditor;
                    var postId = parseInt(jQuery('#irpPostId').val());
                    var name=jQuery('#select2-chosen-1').text().replace('"', ' ');
                    if(name) {
                        name='name="'+name+'"';
                    }
                    if (postId > 0) {
                        var code = '[irp posts="' + postId + '" '+name+']';
                        editor.insertContent(code);
                    }
                    editor.windowManager.close();
                });
                jQuery('#btnClose').click(function () {
                    var editor = top.tinymce.activeEditor;
                    editor.windowManager.close();
                });
				/*
                jQuery("#irpPostId").select2({
                    placeholder: "Type here..."
                    , theme: "classic"
                    , width: '100%'
                    //, maximumSelectionLength: 3
                });
				*/
				var ajaxurl = 'admin-ajax.php';
				jQuery('#irpPostId').select2({
					
					ajax: {
						
						url: ajaxurl,
						dataType: 'json',
	
						delay: 250,
	
						data: function (term, page) {
	
							return {
								action: 'irp_list_posts',
								irp_post_type: '<?php echo esc_attr($_REQUEST['irp_post_type']);?>',
								q: term,
								page: page
							};
	
						},
	
						results: function (data, page) {
	
							page = page || 1;
					
							return {
								results: data.items,
								more: false, 
							};
						
						},
	
						cache: true
						
					},
					
					placeholder: "Type here to search an item...",
					minimumInputLength: 3,
					width: '100%'
				
				});
            });
        </script>

        <style>
            .select2-results {
                max-height: 100px;
                overflow-y: auto;
            }
        </style>
    <?php
    }
    $irp->Form->formEnds();
    exit;
}

