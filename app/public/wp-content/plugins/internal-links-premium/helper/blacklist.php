<?php
namespace ILJ\Helper;

use ILJ\Core\Options;


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
    public static function getBlacklistedList($type){

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            if($type == "term"){
                $blacklistedList = Options::getOption(\ILJ\Core\Options\TermBlacklist::getKey());
                return $blacklistedList;
            }
        }

        if($type == "post"){
            $blacklistedList = Options::getOption(\ILJ\Core\Options\Blacklist::getKey());

            if(Options::getOption(\ILJ\Core\Options\BlacklistChildPages::getKey())){
                $blacklistedList = self::getBlacklistedChilds__premium_only($blacklistedList);
            }
            return $blacklistedList;
        }
       
    }
    
    
    /**
     * Get the All the Children of the List of of IDs
     *
     * @param  array $blacklistedList List of Blacklisted IDs
     * @return array $blacklistedList Returns the Blacklisted list with all the childs included
     */
    protected static function getBlacklistedChilds__premium_only($blacklistedList){

        if(!empty($blacklistedList)){
            
            $post_types = self::getHierarchicalPostTypes();

            $all_pages = [];
            foreach($post_types as $types) {
                $args = array(
                    'post_type'    =>  $types, 
                    'post_status'    => 'publish',
                );
                
                $all_pages = array_merge($all_pages, get_pages( $args ));
            }
            $all_child = [];

            foreach($blacklistedList as $post){           
                $children = get_page_children( $post ,  $all_pages);
                foreach($children as $child) {
                    array_push($all_child, $child->ID);
                }
            }
            $blacklistedList = array_merge($blacklistedList , $all_child);
        }

        return $blacklistedList;
    }

    
    /**
     * Get All Post Types that are Hierarchical , including custom post types
     *
     * @return array $post_types List of Hierarchical Post Types
     */
    protected static function getHierarchicalPostTypes(){
        $args = array(
            'public'   => true,
            'hierarchical' => true,
        );
        
        $post_types = array_keys(get_post_types( $args, 'names'));

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
    public static function checkIfBlacklisted($type, $id, $list){
        if($type == "post"){
            if(!empty($list)){
                if(in_array($id , $list)){
                    if(!empty($list)){
                        return true;
                    }
                
                }
            }
        }

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            if($type == "term"){
                if(!empty($list)){
                    if(in_array($id , $list)){
                        return true;
                    }
                }
                
            }
        }

        return false;
    }
   

}
