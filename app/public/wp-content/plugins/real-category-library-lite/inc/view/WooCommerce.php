<?php

namespace DevOwl\RealCategoryLibrary\view;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\lite\view\WooCommerce as ViewWooCommerce;
use DevOwl\RealCategoryLibrary\overrides\interfce\view\IOverrideWooCommerce;
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
/**
 * Add compatibility with WooCommerce 3.x.
 */
class WooCommerce implements \DevOwl\RealCategoryLibrary\overrides\interfce\view\IOverrideWooCommerce {
    use UtilsProvider;
    use ViewWooCommerce;
    /**
     * Add woocommerce product options.
     *
     * @param array $settings
     */
    public function woocommerce_products_general_settings($settings) {
        $this->maybeMigrateToFixedSorting();
        return $settings;
    }
    /**
     * This is a migration process which needs to be triggered by customers itself.
     * Unfortunately we can not provide any button to the customer as is it is support-dependent.
     * If a customer reports, after the update the sorting is lost, this needs to be done.
     *
     * @see https://app.clickup.com/t/5pp9b
     */
    public function maybeMigrateToFixedSorting() {
        global $wpdb;
        if (isset($_GET['rcl_migrate_to_wc']) && current_user_can('manage_options')) {
            $wpdb->query(
                "UPDATE {$wpdb->termmeta} wptm\n                INNER JOIN {$wpdb->term_taxonomy} wptt ON wptt.term_id = wptm.term_id\n                INNER JOIN {$wpdb->terms} wpt ON wpt.term_id = wptm.term_id\n                AND wptm.meta_key = 'order'\n                AND wpt.term_order > 0\n                SET wptm.meta_value = wpt.term_order\n                WHERE wptt.taxonomy = 'product_cat'"
            );
        }
    }
}
