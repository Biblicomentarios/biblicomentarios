<?php
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

if (!function_exists('rcl_skip_already_admin_notice')) {
    /**
     * Show an admin notice to administrators when the plugin is already active.
     */
    function rcl_skip_already_admin_notice() {
        if (current_user_can('activate_plugins')) {
            echo '<div class=\'notice notice-error\'>
				<p>Currently multiple versions of the plugin <strong>Real Category Management</strong> are active. Please deactivate all versions except the one you want to use.</p>' .
                '</div>';
        }
    }
}
add_action('admin_notices', 'rcl_skip_already_admin_notice');

if (!function_exists('rcl_skip_already_deactivate_lite')) {
    /**
     * Automatically deactivate lite version when we try to activate the PRO version.
     */
    function rcl_skip_already_deactivate_lite() {
        // Avoid doing this in local stack as we do not have pro and lite difference in slug
        if (defined('DEVOWL_WP_DEV') && constant('DEVOWL_WP_DEV') && $_SERVER['SERVER_PORT'] === strval(10000)) {
            return;
        }

        deactivate_plugins(RCL_SLUG_LITE . '/index.php');
    }
}
register_activation_hook(
    constant('WP_PLUGIN_DIR') . DIRECTORY_SEPARATOR . RCL_SLUG_PRO . DIRECTORY_SEPARATOR . 'index.php',
    'rcl_skip_already_deactivate_lite'
);
