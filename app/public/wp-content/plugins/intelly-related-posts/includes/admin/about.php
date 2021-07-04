<?php
function irp_ui_about() {
    global $irp;
    irp_ui_tracking(TRUE);
    ?>
    <div><?php $irp->Lang->P('AboutText')?></div>
    <style>
        ul li {
            padding:2px;
        }
    </style>
    <ul>
        <li>
            <img style="float:left; margin-right:10px;" src="<?php echo IRP_PLUGIN_IMAGES?>email.png" />
            <a href="mailto:aleste@intellywp.com">aleste@intellywp.com</a>
        </li>
        <li>
            <img style="float:left; margin-right:10px;" src="<?php echo IRP_PLUGIN_IMAGES?>twitter.png" />
            <?php $irp->Utils->twitter('intellywp')?>
        </li>
        <li>
            <img style="float:left; margin-right:10px;" src="<?php echo IRP_PLUGIN_IMAGES?>internet.png" />
            <a href="http://www.intellywp.com" target="_new">IntellyWP.com</a>
        </li>
    </ul>
    <?php
}