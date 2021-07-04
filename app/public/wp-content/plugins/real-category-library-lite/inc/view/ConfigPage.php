<?php

namespace DevOwl\RealCategoryLibrary\view;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
use DevOwl\RealCategoryLibrary\Core;
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
/**
 * Add an option page to "Settings".
 */
class ConfigPage {
    use UtilsProvider;
    const COMPONENT_ID = RCL_SLUG . '-component';
    /**
     * C'tor.
     */
    private function __construct() {
        // Silence is golden.
    }
    /**
     * Add new menu page.
     */
    public function admin_menu() {
        $pluginName = $this->getCore()->getPluginData()['Name'];
        add_submenu_page(
            'options-general.php',
            $pluginName,
            __('Category Management', RCL_TD),
            \DevOwl\RealCategoryLibrary\Core::MANAGE_MIN_CAPABILITY,
            self::COMPONENT_ID,
            [$this, 'render_component_library']
        );
    }
    /**
     * Show a "Settings" link in plugins list.
     *
     * @param string[] $actions
     * @return string[]
     */
    public function plugin_action_links($actions) {
        $actions[] = \sprintf('<a href="%s">%s</a>', $this->getUrl(), __('Settings'));
        return $actions;
    }
    /**
     * Render the content of the menu page.
     */
    public function render_component_library() {
        echo '<div id="' . self::COMPONENT_ID . '" class="wrap"></div>';
    }
    /**
     * Check if a given page string is this config page or from the current page `pagenow`.
     *
     * @param string $hook_suffix The current admin page (admin_enqueue_scripts)
     */
    public function isVisible($hook_suffix = \false) {
        return $hook_suffix === 'settings_page_' . self::COMPONENT_ID ||
            (isset($_GET['page'], $GLOBALS['pagenow']) &&
                $GLOBALS['pagenow'] === 'options-general.php' &&
                $_GET['page'] === self::COMPONENT_ID);
    }
    /**
     * Get the URL of this page.
     */
    public function getUrl() {
        return admin_url('options-general.php?page=' . self::COMPONENT_ID);
    }
    /**
     * New instance.
     */
    public static function instance() {
        return new \DevOwl\RealCategoryLibrary\view\ConfigPage();
    }
}
