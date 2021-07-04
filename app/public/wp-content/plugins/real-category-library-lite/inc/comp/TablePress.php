<?php

namespace DevOwl\RealCategoryLibrary\comp;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
/**
 * Compatibility with "TablePress".
 *
 * @see https://github.com/TobiasBg/TablePress
 */
class TablePress {
    use UtilsProvider;
    /**
     * C'tor.
     */
    private function __construct() {
        // Silence is golden.
    }
    /**
     * Check if the plugin is active.
     */
    public function isActive() {
        return \defined('TABLEPRESS_ABSPATH');
    }
    /**
     * Override the table checkbox name so we can drag & drop tables to categories.
     *
     * @param string $tableCheckboxName
     */
    public function tableCheckboxName($tableCheckboxName) {
        return $this->isPageActive() ? 'table[]' : $tableCheckboxName;
    }
    /**
     * Override the `global $typenow` so the category tree gets visible in the custom UI.
     *
     * @param string $typenow
     */
    public function typenow($typenow) {
        return empty($typenow) && $this->isPageActive() ? $this->getPostTypeName() : $typenow;
    }
    /**
     * Check if it is the current screen and force available.
     *
     * @param boolean $enabled
     * @param string $type The post type
     * @param object $taxonomy The taxonomy
     * @param boolean $checkScreen If `true`, the current screen will be checked, too (must be `edit`) (since 4.0.0)
     */
    public function available($enabled, $type, $taxonomy, $checkScreen) {
        if ($enabled === \false && $checkScreen && $type === $this->getPostTypeName()) {
            return $this->isPageActive();
        }
        return $enabled;
    }
    /**
     * For the post type to be respected by Real Category Management.
     *
     * @param string[] $post_types
     * @see https://git.io/JqnKe
     */
    public function forcePostType($post_types) {
        $post_types[] = $this->getPostTypeName();
        return $post_types;
    }
    /**
     * Get the registered post type name.
     */
    protected function getPostTypeName() {
        return apply_filters('tablepress_post_type', 'tablepress_table');
    }
    /**
     * Checks if the current requested page is the TablePress list.
     */
    protected function isPageActive() {
        return \function_exists('get_current_screen') &&
            \in_array(get_current_screen()->base, ['tablepress_list'], \true);
    }
    /**
     * New instance.
     */
    public static function instance() {
        return new \DevOwl\RealCategoryLibrary\comp\TablePress();
    }
}
