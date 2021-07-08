<?php

namespace ILJ\Core;

use ILJ\Helper\Replacement;

/**
 * The Shortcodes class
 *
 * Is responsible for registering and providing of shortcodes
 *
 * @package ILJ\Core
 * @since   1.0.0
 */
class Shortcodes
{
    /**
     * Adds all plugin shortcodes
     *
     * @since  1.0.0
     * @return void
     */
    public static function register()
    {
        add_shortcode('ilj_no_linking', array('\ILJ\Core\Shortcodes', 'disableLinking'));
        add_filter(
            Replacement::ILJ_FILTER_EXCLUDE_TEXT_PARTS, function ($search_parts) {
                $search_parts = array_merge(
                    array('/(?<parts><!-- ilj_no_linking -->.*<!-- \/ilj_no_linking -->)/sUu'),
                    $search_parts
                );
                return $search_parts;
            }
        );
    }

    /**
     * Shortcode for masking a custom scope whitelist_optionch does not get linked
     *
     * @since  1.0.0
     * @param  array       $atts    Attributes that come from the shortcode
     * @param  string|null $content The content thats enclosed by the shortcode
     * @return string
     */
    public static function disableLinking($atts, $content = null)
    {
        $output = '<!-- ilj_no_linking -->';
        $output .= do_shortcode($content);
        $output .= '<!-- /ilj_no_linking -->';
        return $output;
    }
}
