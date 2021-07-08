<?php

namespace ILJ\Database;

/**
 * Termmeta wrapper for the inlink postmeta
 *
 * @package ILJ\Database
 * @since   1.0.1
 */
class Termmeta
{
    const ILJ_META_KEY_LINKDEFINITION = 'ilj_linkdefinition';

    /**
     * Returns all Linkdefinitions from termmeta table
     *
     * @since  1.0.1
     * @return array
     */
    public static function getAllLinkDefinitions()
    {
        global $wpdb;
        $meta_key = Postmeta::ILJ_META_KEY_LINKDEFINITION;
        $query    = "
            SELECT termmeta.*
            FROM $wpdb->termmeta termmeta
            WHERE termmeta.meta_key = '$meta_key'
        ";
        return $wpdb->get_results($query);
    }

    /**
     * Removes all link definitions from termmeta table
     *
     * @since  1.1.3
     * @return int
     */
    public static function removeAllLinkDefinitions()
    {
        global $wpdb;
        $meta_key = self::ILJ_META_KEY_LINKDEFINITION;
        return $wpdb->delete($wpdb->termmeta, array( 'meta_key' => $meta_key ));
    }

    
}
