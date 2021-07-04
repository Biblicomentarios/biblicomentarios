<?php

if ( ! current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'daext-autolinks-manager'));
}

?>

<!-- output -->

<div class="wrap">

    <h2><?php esc_html_e('Autolinks Manager - Pro Version', 'daext-autolinks-manager'); ?></h2>

    <div id="daext-menu-wrapper">

        <p><?php echo esc_html__('For professional users, we distribute a ', 'daext-autolinks-manager') . '<a href="https://daext.com/product/autolinks-manager-pro/">' . esc_attr__('Pro Version', 'daext-autolinks-manager') . '</a> ' . esc_attr__('of this plugin.', 'daext-autolinks-manager') . '</p>'; ?>
        <h2><?php esc_html_e('Additional Features Included in the Pro Version', 'daext-autolinks-manager'); ?></h2>
        <ul>
            <li><?php echo '<strong>' . esc_html__('Wizard', 'daext-autolinks-manager') . '</strong> ' . esc_html__('menu to bulk upload the autolinks with an embedded spreadsheet editor', 'daext-autolinks-manager'); ?></li>
            <li><?php echo '<strong>' . esc_html__('Tracking', 'daext-autolinks-manager') . '</strong> ' . esc_html__('menus to monitor all the clicks performed on the autolinks', 'daetxam'); ?></li>
            <li><?php echo '<strong>' . esc_html__('Import', 'daext-autolinks-manager') . '</strong> ' . esc_html__('and', 'daext-autolinks-manager') . ' <strong>' . esc_html__('Export', 'daext-autolinks-manager') . '</strong> ' . esc_html__('menus to store the plugin data (in XML format) or move the plugin data between different WordPress installations', 'daext-autolinks-manager'); ?></li>
            <li><?php echo '<strong>' . esc_html__('Maintenance', 'daext-autolinks-manager') . '</strong> ' . esc_html__('menu to perform bulk operations on the plugin data', 'daext-autolinks-manager'); ?></li>
            <li><?php echo esc_html__('Ability to sort the autolinks statistics with a widget included in the', 'daext-autolinks-manager') . ' <strong>' . esc_html__('Statistics', 'daext-autolinks-manager') . '</strong> ' . esc_html__('menu', 'daext-autolinks-manager'); ?></li>
            <li><?php echo esc_html__('Ability to export the autolinks statistics in a CSV format with a widget included in the', 'daext-autolinks-manager') . ' <strong>' . esc_html__('Statistics', 'daext-autolinks-manager') . '</strong> ' . esc_html__('menu', 'daext-autolinks-manager'); ?></li>
            <li><?php echo esc_html__('Ability to export the tracking statistics in a CSV format with a widget included in the', 'daext-autolinks-manager') . ' <strong>' . esc_html__('Tracking', 'daext-autolinks-manager') . '</strong> ' . esc_html__('menu', 'daext-autolinks-manager'); ?></li>
            <li><?php esc_html_e('Additional options to set custom menu capabilities for all the plugin menus', 'daext-autolinks-manager'); ?></li>
            <li><?php esc_html_e('Additional options to customize the pagination system of the plugin', 'daext-autolinks-manager'); ?></li>
        </ul>
        <h2><?php esc_html_e('Additional Benefits of the Pro Version', 'daext-autolinks-manager'); ?></h2>
        <ul>
            <li><?php esc_html_e('24 hours support provided 7 days a week', 'daext-autolinks-manager'); ?></li>
            <li><?php echo esc_html__('30 day money back guarantee (more information is available in the', 'daext-autolinks-manager') . ' <a href="https://daext.com/refund-policy/">' . esc_html__('Refund Policy', 'daext-autolinks-manager') . '</a> ' . esc_html__('page', 'daext-autolinks-manager') . ')'; ?></li>
        </ul>
        <h2><?php esc_html_e('Get Started', 'daext-autolinks-manager'); ?></h2>
        <p><?php echo esc_html__('Download the', 'daext-autolinks-manager') . ' <a href="https://daext.com/product/autolinks-manager-pro/">' . esc_html__('Pro Version', 'daext-autolinks-manager') . '</a> ' . esc_attr__('now by selecting one of the available plans.', 'daext-autolinks-manager'); ?></p>
        <a class="product-image-link" href="https://daext.com/product/autolinks-manager-pro/"><img src="<?php echo $this->shared->get('url') . 'admin/assets/img/autolinks-manager-pro.png'; ?>"></a>
    </div>

