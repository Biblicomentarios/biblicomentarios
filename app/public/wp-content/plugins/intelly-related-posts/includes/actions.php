<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init', 'irp_do_action');
add_action('wp_ajax_do_action', 'irp_do_action');
function irp_do_action() {
    global $irp;

    load_plugin_textdomain(IRP_PLUGIN_SLUG, false, dirname( plugin_basename(__FILE__ ), 2) . '/languages');
    
    $action = $irp->Utils->qs('irp_action');
    $irp->Log->info('[actions::irp_do_action] Action: %s', $action);
    switch($action) {
        case 'ui_button_editor':
            call_irp_ui_button_editor($irp);
            break;
        case 'ui_box_preview':
            call_irp_ui_box_preview($irp);
            break;
        case 'manager_trackingOn':
            call_irp_manager_trackingOn($irp);
            break;
        case 'manager_trackingOff':
            call_irp_manager_trackingOff($irp);
            break;
        case '':
            break; // blank strings are okay. We just want to ignore them.
        default:
            $irp->Log->fatal('Attempting to execute unknown function %s', $action);
            break;
    }
}

function call_irp_ui_button_editor($irp)
{
    if (current_user_can('edit_posts')
        || current_user_can('edit_pages')
        || current_user_can('edit_published_posts')
        || current_user_can('edit_published_pages')
        || current_user_can('edit_others_posts')
        || current_user_can('edit_others_pages')
        || current_user_can('edit_private_posts')
        || current_user_can('edit_private_pages')
        ) {
        irp_ui_button_editor();
    }
}

function call_irp_ui_box_preview($irp)
{
    if (current_user_can('activate_plugins')) {
        irp_ui_box_preview();
    }
}

function call_irp_manager_trackingOn($irp)
{
    if (current_user_can('activate_plugins')) {
        $irp->Tracking->enableTracking();
    }
}

function call_irp_manager_trackingOff($irp)
{
    if (current_user_can('activate_plugins')) {
        $irp->Tracking->disableTracking();
    }
}
