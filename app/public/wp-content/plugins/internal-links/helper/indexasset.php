<?php

namespace ILJ\Helper;

use  ILJ\Core\Options as CoreOptions ;
use  ILJ\Posttypes\CustomLinks ;
use  ILJ\Helper\Blacklist ;
use  ILJ\Backend\Editor ;
/**
 * Toolset for linkindex assets
 *
 * Methods for handling linkindex data
 *
 * @package ILJ\Helper
 * @since   1.1.0
 */
class IndexAsset
{
    const  ILJ_FILTER_INDEX_ASSET = 'ilj_index_asset_title' ;
    /**
     * Returns all meta data to a specific asset from index
     *
     * @since  1.1.0
     * @param  int    $id   The id of the asset
     * @param  string $type The type of the asset (post, term or custom)
     * @return object
     */
    public static function getMeta( $id, $type )
    {
        if ( 'post' != $type ) {
            return null;
        }
        
        if ( 'post' == $type ) {
            $post = get_post( $id );
            if ( !$post ) {
                return null;
            }
            $asset_title = $post->post_title;
            $asset_url = get_the_permalink( $post->ID );
            $asset_url_edit = get_edit_post_link( $post->ID );
        }
        
        if ( !isset( $asset_title ) || !isset( $asset_url ) || !isset( $asset_url_edit ) ) {
            return null;
        }
        $meta_data = (object) [
            'title'    => $asset_title,
            'url'      => $asset_url,
            'url_edit' => $asset_url_edit,
        ];
        /**
         * Filters the index asset
         *
         * @since 1.6.0
         *
         * @param object $meta_data The index asset
         * @param string $type The asset type
         * @param int $id The asset id
         */
        $meta_data = apply_filters(
            self::ILJ_FILTER_INDEX_ASSET,
            $meta_data,
            $type,
            $id
        );
        return $meta_data;
    }
    
    /**
     * Returns all relevant posts for linking
     *
     * @since  1.2.0
     * @return array
     */
    public static function getPosts()
    {
        $whitelist = CoreOptions::getOption( \ILJ\Core\Options\Whitelist::getKey() );
        if ( !count( $whitelist ) ) {
            return [];
        }
        $args = [
            'posts_per_page'   => -1,
            'post__not_in'     => Blacklist::getBlacklistedList( "post" ),
            'post_type'        => $whitelist,
            'post_status'      => [ 'publish' ],
            'suppress_filters' => true,
        ];
        if ( CoreOptions::getOption( \ILJ\Core\Options\BlacklistChildPages::getKey() ) ) {
            $args['post_parent__not_in'] = Blacklist::getBlacklistedList( "post" );
        }
        $query = new \WP_Query( $args );
        $post_count = $query->post_count;
        return $query->posts;
    }
    
    /**
     * Gets the concrete type of an asset
     *
     * @since 1.2.5
     * @param string $id   ID of asset
     * @param string $type Generic type of asset
     *
     * @return string
     */
    public static function getDetailedType( $id, $type )
    {
        if ( $type == 'post' ) {
            $detailed_type = get_post_type( $id );
        }
        return $detailed_type;
    }
    
    /**
     * Get Incoming Links Count
     *
     * @param  int $id          Post/Tax ID
     * @param  string $type     Type
     * @return int
     */
    public static function getIncomingLinksCount( $id, $type )
    {
        global  $wpdb ;
        $ilj_linkindex_table = $wpdb->prefix . "ilj_linkindex";
        $incoming_links = $wpdb->get_var( "SELECT count(link_to) AS incoming_links FROM {$ilj_linkindex_table} WHERE (link_to = '" . $id . "' AND type_to = '" . $type . "')" );
        return $incoming_links;
    }
    
    /**
     * Checks if the phrase is included in the blacklist of keywords
     *
     * @param  int    $link_from     post/term ID
     * @param  string $phrase      string to check for 
     * @param  string $type        could be term/post
     * @return bool
     */
    public static function checkIfBlacklistedKeyword( $link_from, $phrase, $type )
    {
        if ( $type == 'post' ) {
            $keyword_blacklist = get_post_meta( $link_from, Editor::ILJ_META_KEY_BLACKLISTDEFINITION, true );
        }
        if ( $type == 'term' ) {
            $keyword_blacklist = get_term_meta( $link_from, Editor::ILJ_META_KEY_BLACKLISTDEFINITION, true );
        }
        
        if ( !empty($keyword_blacklist) || $keyword_blacklist != false ) {
            $keyword_blacklist = array_slice( $keyword_blacklist, 0, 2 );
            foreach ( $keyword_blacklist as $keyword ) {
                if ( strtolower( $phrase ) == strtolower( $keyword ) ) {
                    return true;
                }
            }
        }
        
        return false;
    }

}