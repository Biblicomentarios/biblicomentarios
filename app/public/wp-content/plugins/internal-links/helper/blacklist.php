<?php

namespace ILJ\Helper;

use  ILJ\Core\Options ;
/**
 * Toolset for Blacklisting
 *
 * Methods for Blacklisting 
 *
 * @package ILJ\Helper
 * @since   1.2.15
 */
class Blacklist
{
    /**
     * Get Blacklisted list from option
     *
     * @param  string   $type               Check if Post/Term
     * @return array    $blacklistedList    List of Blacklisted Post/Terms
     */
    public static function getBlacklistedList( $type )
    {
        
        if ( $type == "post" ) {
            $blacklistedList = Options::getOption( \ILJ\Core\Options\Blacklist::getKey() );
            if ( Options::getOption( \ILJ\Core\Options\BlacklistChildPages::getKey() ) ) {
                $blacklistedList = self::getBlacklistedChilds__premium_only( $blacklistedList );
            }
            return $blacklistedList;
        }
    
    }
    
    /**
     * Get All Post Types that are Hierarchical , including custom post types
     *
     * @return array $post_types List of Hierarchical Post Types
     */
    protected static function getHierarchicalPostTypes()
    {
        $args = array(
            'public'       => true,
            'hierarchical' => true,
        );
        $post_types = array_keys( get_post_types( $args, 'names' ) );
        return $post_types;
    }
    
    /**
     * Check if ID is Blacklisted
     *
     * @param  string   $type     Checks if Post/Term
     * @param  int      $id       ID to check
     * @param  array    $list     Array list to find the $id
     * @return bool               Returns True if blacklisted  
     */
    public static function checkIfBlacklisted( $type, $id, $list )
    {
        if ( $type == "post" ) {
            if ( !empty($list) ) {
                if ( in_array( $id, $list ) ) {
                    if ( !empty($list) ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

}