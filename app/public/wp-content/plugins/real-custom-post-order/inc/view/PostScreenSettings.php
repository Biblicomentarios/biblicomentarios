<?php

namespace DevOwl\RealCustomPostOrder\view;

use DevOwl\RealCustomPostOrder\base\UtilsProvider;
use DevOwl\RealCustomPostOrder\sortable\PostSortable;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Screen settings for every post type.
 */
class PostScreenSettings {
    use UtilsProvider;
    /**
     * C'tor.
     *
     * @codeCoverageIgnore C'tor
     */
    private function __construct() {
        // Silence is golden.
    }
    /**
     * Modify screen settings with sortable feature.
     *
     * @param string $settings
     */
    public function screen_settings($settings) {
        if (
            \DevOwl\RealCustomPostOrder\sortable\PostSortable::get()->isAvailable(\false) &&
            current_user_can('manage_options')
        ) {
            $post_type = get_query_var('post_type', 'post');
            $option = self::getOptionName($post_type);
            $settings .=
                '<fieldset class="metabox-prefs">
        		<legend>' .
                __('Custom order for this list table', RCPO_TD) .
                '</legend>
        		<label><input class="hide-column-tog" name="' .
                $option .
                '" type="checkbox" id="' .
                $option .
                '" value="1" ' .
                checked(self::isActive($post_type), 1, \false) .
                '>' .
                __('Enabled', RCPO_TD) .
                '</label>
    		</fieldset>';
        }
        return $settings;
    }
    /**
     * Save the screen options over the nonce checker.
     * The nonce name is "screen-options-nonce".
     *
     * @param string $action
     * @param false|int $result
     */
    public function check_admin_referer($action, $result) {
        if (current_user_can('manage_options') && $action === 'screen-options-nonce' && $result) {
            $post_type = get_query_var('post_type', isset($_GET['post_type']) ? $_GET['post_type'] : 'post');
            $option = self::getOptionName($post_type);
            update_option($option, isset($_POST[$option]) && \boolval($_POST[$option]) ? '1' : '0');
        }
    }
    /**
     * Get option name for a given post type.
     *
     * @param string $post_type
     */
    public static function getOptionName($post_type) {
        return RCPO_OPT_PREFIX . '-post-active-' . $post_type;
    }
    /**
     * Checks if a given post type is active.
     *
     * @param string $post_type
     */
    public static function isActive($post_type) {
        $default = \in_array($post_type, ['post', 'page'], \true);
        $optionName = self::getOptionName($post_type);
        add_option($optionName, $default);
        return \boolval(get_option($optionName, $default));
    }
    /**
     * New instance.
     *
     * @codeCoverageIgnore
     */
    public static function instance() {
        return new \DevOwl\RealCustomPostOrder\view\PostScreenSettings();
    }
}
