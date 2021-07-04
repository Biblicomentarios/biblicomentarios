<?php

if ( ! current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'daext-autolinks-manager'));
}

?>

<!-- output -->

<div class="wrap">

    <h2><?php esc_html_e('Autolinks Manager - Help', 'daext-autolinks-manager'); ?></h2>

    <div id="daext-menu-wrapper">

        <p><?php esc_html_e('Visit the resources below to find your answers or to ask questions directly to the plugin developers.', 'daext-autolinks-manager'); ?></p>
        <ul>
            <li><a href="https://daext.com/doc/autolinks-manager/"><?php esc_html_e('Plugin Documentation', 'daext-autolinks-manager'); ?></a></li>
            <li><a href="https://daext.com/support/"><?php esc_html_e('Support Conditions', 'daext-autolinks-manager'); ?></li>
            <li><a href="https://daext.com"><?php esc_html_e('Developer Website', 'daext-autolinks-manager'); ?></a></li>
            <li><a href="https://daext.com/product/autolinks-manager-pro/"><?php esc_html_e('Pro Version', 'daext-autolinks-manager'); ?></a></li>
            <li><a href="https://wordpress.org/plugins/daext-autolinks-manager/"><?php esc_html_e('WordPress.org Plugin Page', 'daext-autolinks-manager'); ?></a></li>
            <li><a href="https://wordpress.org/support/plugin/daext-autolinks-manager/"><?php esc_html_e('WordPress.org Support Forum', 'daext-autolinks-manager'); ?></a></li>
        </ul>
        <p>

    </div>

