<?php
function irp_ui_metabox($post) {
    global $irp;

    wp_nonce_field('irp_meta_box', 'irp_meta_box_nonce');
    $array=$irp->Options->getExcludedPostsIds();
    $exclude=FALSE;
    if(in_array($post->ID, $array)) {
        $exclude=TRUE;
    }
    ?>
    <input type="hidden" name="irp_previous" value="<?php echo ($exclude ? 1 : 0)?>" />
    <input type="checkbox" class="irp-checkbox" name="irp_exclude" value="1" <?php echo ($exclude ? ' CHECKED' : '')?> />
    <?php $irp->Lang->P('Post without related posts')?>
    <?php
}

add_action('add_meta_boxes', 'irp_add_meta_box');
function irp_add_meta_box() {
    global $irp;

    if($irp->Plugin->isActive(IRP_PLUGINS_INTELLY_RELATED_POSTS_PRO)) {
        return;
    }

    $options=$irp->Options->getMetaboxPostTypes();
    $screens=array();
    foreach($options as $k=>$v) {
        if(intval($v)>0) {
            $screens[]=$k;
        }
    }
    if(count($screens)>0) {
        foreach ($screens as $screen) {
            add_meta_box(
                'irp_sectionid'
                , $irp->Lang->L('Related Posts by IntellyWP')
                , 'irp_ui_metabox'
                , $screen
                , 'side'
            );
        }
    }
}
//si aggancia a quando un post viene salvato per salvare anche gli altri dati del metabox
add_action('save_post', 'irp_save_meta_box_data');
function irp_save_meta_box_data($postId) {
    global $irp;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!isset($_POST['irp_meta_box_nonce']) || !isset($_POST['post_type'])) {
        return;
    }
    if (!wp_verify_nonce( $_POST['irp_meta_box_nonce'], 'irp_meta_box')) {
        return;
    }

    $exclude=$irp->Utils->qs('irp_exclude', 0);
    $previous=$irp->Utils->qs('irp_previous', 0);
    if($exclude!=$previous) {
        $array=$irp->Options->getExcludedPostsIds();
        $array=array_diff($array, array($postId));
        if($exclude) {
            array_push($array, $postId);
        }
        $irp->Options->setExcludedPostsIds($array);
    }
}
