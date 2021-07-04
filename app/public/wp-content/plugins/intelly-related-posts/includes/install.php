<?php
register_activation_hook(IRP_PLUGIN_FILE, 'irp_install');
function irp_install($networkwide=NULL) {
	global $wpdb, $irp;

    $time=$irp->Options->getPluginInstallDate();
    if($time==0) {
        $irp->Options->setPluginInstallDate(time());
    }
    $irp->Options->setPluginUpdateDate(time());
    $irp->Options->setPluginFirstInstall(TRUE);
    $irp->Options->setTrackingLastSend(0);
}

add_action('admin_init', 'irp_first_redirect');
function irp_first_redirect() {
    global $irp;
    if ($irp->Options->isPluginFirstInstall()) {
        $irp->Options->setPluginFirstInstall(FALSE);
        if (!$irp->Options->isPluginFirstInstall()) {
            $irp->Options->setShowActivationNotice(TRUE);
            $irp->Options->setShowWhatsNew(TRUE);
            $irp->Utils->redirect(IRP_PAGE_SETTINGS);
        }
    }
}



