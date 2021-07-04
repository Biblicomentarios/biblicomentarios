<?php

namespace DevOwl\RealCategoryLibrary;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\Vendor\MatthiasWeb\Utils\Activator as UtilsActivator;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * The activator class handles the plugin relevant activation hooks: Uninstall, activation,
 * deactivation and installation. The "installation" means installing needed database tables.
 */
class Activator {
    use UtilsProvider;
    use UtilsActivator;
    /**
     * Method gets fired when the user activates the plugin.
     */
    public function activate() {
        // Your implementation...
    }
    /**
     * Method gets fired when the user deactivates the plugin.
     */
    public function deactivate() {
        // Your implementation...
    }
    /**
     * Install tables, stored procedures or whatever in the database.
     * This method is always called when the version bumps up or for
     * the first initial activation.
     *
     * @param boolean $errorlevel If true throw errors
     */
    public function dbDelta($errorlevel) {
        global $wpdb;
        // Check if column already exists
        $row = $wpdb->get_results(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$wpdb->terms}' AND column_name = 'term_order';"
        );
        if (empty($row)) {
            $wpdb->query("ALTER TABLE {$wpdb->terms} ADD term_order INT(11) NOT NULL DEFAULT 0");
        }
    }
}
