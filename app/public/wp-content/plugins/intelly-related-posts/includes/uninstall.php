<?php
register_deactivation_hook(IRP_PLUGIN_FILE, 'irp_uninstall');
function irp_uninstall($networkwide=NULL) {
	global $wpdb, $irp;
    $irp->Options->setActive(FALSE);
}
?>