<?php
namespace ILJ\Database;

/**
 * Postmeta wrapper for the inlink postmeta
 *
 * @package ILJ\Database
 * @since   1.0.0
 */
class Postmeta
{
    const ILJ_META_KEY_LINKDEFINITION = 'ilj_linkdefinition';

    /**
     * Returns all Linkdefinitions from postmeta table
     *
     * @since  1.0.0
     * @return array
     */
    public static function getAllLinkDefinitions()
    {
        global $wpdb;

        $meta_key = self::ILJ_META_KEY_LINKDEFINITION;

        $public_post_types = array_keys(get_post_types(['public' => true ]));
        $public_post_types = array_map('esc_sql', $public_post_types);

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            $public_post_types = array_merge($public_post_types, [\ILJ\Posttypes\CustomLinks::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG ]);
        }

        $public_post_types_list = "'" . implode("','", $public_post_types) . "'";

        $query    = "
            SELECT postmeta.*
            FROM $wpdb->postmeta postmeta
            LEFT JOIN $wpdb->posts posts ON postmeta.post_id = posts.ID
            WHERE postmeta.meta_key = '$meta_key'
            AND posts.post_status = 'publish'
            AND posts.post_type IN ($public_post_types_list)
        ";

        return $wpdb->get_results($query);
    }

    /**
     * Removes all link definitions from postmeta table
     *
     * @since  1.1.3
     * @return int
     */
    public static function removeAllLinkDefinitions()
    {
        global $wpdb;
        $meta_key = self::ILJ_META_KEY_LINKDEFINITION;
        return $wpdb->delete($wpdb->postmeta, array( 'meta_key' => $meta_key ));
    }

    
    
}
