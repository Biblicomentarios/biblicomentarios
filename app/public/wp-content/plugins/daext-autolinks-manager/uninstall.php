<?php

//exit if this file is called outside wordpress
if ( ! defined('WP_UNINSTALL_PLUGIN')) {
    die();
}

require_once(plugin_dir_path(__FILE__) . 'shared/class-daextam-shared.php');
require_once(plugin_dir_path(__FILE__) . 'admin/class-daextam-admin.php');

//delete options and tables
daextam_Admin::un_delete();
