<?php
namespace ILJ\Helper;

use ILJ\Core\Options as CoreOptions;
use ILJ\Posttypes\CustomLinks;
use ILJ\Helper\Blacklist;
use ILJ\Backend\Editor;

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
    const ILJ_FILTER_INDEX_ASSET = 'ilj_index_asset_title';


    /**
     * Returns all meta data to a specific asset from index
     *
     * @since  1.1.0
     * @param  int    $id   The id of the asset
     * @param  string $type The type of the asset (post, term or custom)
     * @return object
     */
    public static function getMeta($id, $type)
    {
        if (!\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            if ('post' != $type) {
                return null;
            }
        }

        if ('post' == $type) {
            $post = get_post($id);

            if (!$post) {
                return null;
            }

            $asset_title    = $post->post_title;
            $asset_url      = get_the_permalink($post->ID);
            $asset_url_edit = get_edit_post_link($post->ID);
        }

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            if ('post' != $type) {
                switch ($type) {
                case 'term':
                    $term = get_term($id);

                    if (!$term || is_wp_error($term)) {
                            return null;
                    }

                    $asset_title    = $term->name;
                    $asset_url      = get_term_link($term);
                    $asset_url_edit = get_edit_term_link($term->term_id, $term->taxonomy);

                    break;

                case 'custom':
                    $asset_title = get_the_title($id);
                    $asset_url   = get_post_meta(
                        $id,
                        \ILJ\Posttypes\CustomLinks::ILJ_FIELD_CUSTOM_LINKS_URL,
                        true
                    );
                    $asset_url_edit = get_edit_post_link($id);

                    break;
                default:
                    return null;
                }
            }
        }

        if (!isset($asset_title) || !isset($asset_url) || !isset($asset_url_edit) ) {
            return null;
        }

        $meta_data = (object) [
            'title'    => $asset_title,
            'url'      => $asset_url,
            'url_edit' => $asset_url_edit
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
        $meta_data = apply_filters(self::ILJ_FILTER_INDEX_ASSET, $meta_data, $type, $id);

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
        $whitelist = CoreOptions::getOption(\ILJ\Core\Options\Whitelist::getKey());

        if (!count($whitelist)) {
            return [];
        }

        $args = [
            'posts_per_page'   => -1,
            'post__not_in'     => Blacklist::getBlacklistedList("post"),
            'post_type'        => $whitelist,
            'post_status'      => ['publish'],
            'suppress_filters' => true
        ];

        if(CoreOptions::getOption(\ILJ\Core\Options\BlacklistChildPages::getKey())){
           
            $args['post_parent__not_in'] = Blacklist::getBlacklistedList("post");
        }

        $query       = new \WP_Query($args);
        $post_count = $query->post_count;

        return $query->posts;
    }

    /**
     * Returns all relevant terms for linking
     *
     * @since  1.2.0
     * @return array
     */
    public static function getTerms__premium_only()
    {
        $args = [
            'taxonomy' => CoreOptions::getOption(\ILJ\Core\Options\TaxonomyWhitelist::getKey()),
            'exclude'  => Blacklist::getBlacklistedList("term"),
            'hide_empty' => false
        ];

        $taxonomies = new \WP_Term_Query($args);

        return $taxonomies->terms;
    }

    /**
     * Returns all custom links for linking
     *
     * @since  1.2.0
     * @return array
     */
    public static function getCustomLinks__premium_only()
    {
        $args = [
            'posts_per_page'   => -1,
            'post_type'        => CustomLinks::ILJ_POSTTYPE_CUSTOM_LINKS_SLUG,
            'post_status'      => ['publish'],
            'suppress_filters' => true
        ];

        $query       = new \WP_Query($args);

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
    public static function getDetailedType($id, $type)
    {
        if ($type == 'post') {
            $detailed_type = get_post_type($id);
        }

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            switch($type) {
            case 'custom':
                $detailed_type = $type;
                break;
            case 'term':
                $term = get_term($id);
                if ($term) {
                    $detailed_type = $term->taxonomy;
                }
                break;
            }
        }

        return $detailed_type;
    }


    /**
     * Checks if two posts are in the same taxonomy
     *
     * @since 1.2.14
     * @param int    $link_from ID of post the link is coming from
     * @param int    $link_to   ID of post the link is going to
     * @param string $type      Generic type of asset
     *
     * @return bool
     */
    public static function postsInSameTaxonomy__premium_only($link_from, $link_to, $type)
    {
        if ($type != 'post') {
            return false;
        }

        $taxonomies = CoreOptions::getOption(\ILJ\Core\Options\LimitTaxonomyList::getKey());
        if(empty($taxonomies)) {
            return false;
        }

        foreach($taxonomies as $taxonomy){
            $has_tax_from = has_term('', $taxonomy, $link_from);
            $has_tax_to = has_term('', $taxonomy, $link_to);

            if($has_tax_from || $has_tax_to) {
                return true;
            }
        }

        return false;
    }


    
    /**
     * Checks if two posts share the same term
     *
     * @since 1.2.14
     * @param int    $link_from ID of post the link is coming from
     * @param int    $link_to   ID of post the link is going to
     * @param string $type      Generic type of asset
     * 
     * @return boolean
     */
    public static function postsInSameTerm__premium_only($link_from, $link_to, $type)
    {
        if ($type != 'post') {
            return false;
        }

        $post_from = get_post($link_from);
        $post_to = get_post($link_to);

        if (!$post_from || !$post_to) {
            return false;
        }

        if($post_from->post_type == "page" || $post_to->post_type == "page" ) {
            return true;
        }

        $taxonomies = CoreOptions::getOption(\ILJ\Core\Options\LimitTaxonomyList::getKey());

        if(empty($taxonomies)) {
            return false;
        }

        foreach($taxonomies as $taxonomy){
            $tax_from = wp_get_post_terms($post_from->ID, $taxonomy, array( 'fields' => 'ids' ));
            $tax_to = wp_get_post_terms($post_to->ID, $taxonomy, array( 'fields' => 'ids' ));

            $same_tax = array_intersect($tax_from, $tax_to);
            if(count($same_tax) > 0) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Get Limit Links meta and Max incoming links meta value
     *
     * @param  int $link_to post ID
     * @param  string $type
     * @return array
     */
    public static function getLimitLinksMetas__premium_only($link_to, $type) {

        $limit_metas = 0 ;
        if ($type == 'post' || $type == 'custom') {
            $limitlinks   = get_post_meta( $link_to,Editor::ILJ_META_KEY_LIMITINCOMINGLINKS,true );
            $maxlinks   = get_post_meta( $link_to,Editor::ILJ_META_KEY_MAXINCOMINGLINKS,true );
            $limit_metas = array(Editor::ILJ_META_KEY_LIMITINCOMINGLINKS => $limitlinks , Editor::ILJ_META_KEY_MAXINCOMINGLINKS => $maxlinks);
   
        }

        if ($type == 'term') {
            $limitlinks   = get_term_meta( $link_to,Editor::ILJ_META_KEY_LIMITINCOMINGLINKS,true );
            $maxlinks   = get_term_meta( $link_to,Editor::ILJ_META_KEY_MAXINCOMINGLINKS,true );
            $limit_metas = array(Editor::ILJ_META_KEY_LIMITINCOMINGLINKS => $limitlinks , Editor::ILJ_META_KEY_MAXINCOMINGLINKS => $maxlinks);
    
            
        }
        return $limit_metas;
    }
    
    /**
     * Get Incoming Links Count
     *
     * @param  int $id          Post/Tax ID
     * @param  string $type     Type
     * @return int
     */
    public static function getIncomingLinksCount($id, $type) {
        global $wpdb;
        $ilj_linkindex_table = $wpdb->prefix . "ilj_linkindex";
        $incoming_links = $wpdb->get_var("SELECT count(link_to) AS incoming_links FROM $ilj_linkindex_table WHERE (link_to = '".$id."' AND type_to = '". $type ."')");
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
    public static function checkIfBlacklistedKeyword($link_from, $phrase, $type){

        if ($type == 'post') {
            $keyword_blacklist = get_post_meta( $link_from, Editor::ILJ_META_KEY_BLACKLISTDEFINITION, true);
        }
        if ($type == 'term'){
            $keyword_blacklist = get_term_meta( $link_from, Editor::ILJ_META_KEY_BLACKLISTDEFINITION, true);
        }
        
        if(!empty($keyword_blacklist) || $keyword_blacklist != false){
            if (!\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
                $keyword_blacklist = array_slice($keyword_blacklist, 0, 2);
            }
            foreach($keyword_blacklist as $keyword){

                if(strtolower($phrase) == strtolower($keyword)){
                    return true;
                }
            }
    
        }

        return false;
    }
    
}