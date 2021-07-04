<?php 
/**
 * Plugin Name: Create And Assign Categories For Pages
 * Plugin URI: http://jobdeoz.com/
 * Description: This plugin will help you to create the new category for your wordpress pages. You can assign those categories to your pages. With that, you can get your wordpress pages with the help of specific category. This will save your time from custom coding.
 * Version: 1.2
 * Author: Sandeep Singh
 * Author URI: https://profiles.wordpress.org/sandeepsinghhdp
 * License: GPLv2+
 * Text Domain: create-and-assign-categories-for-pages
 */

 

// starting of the Plugin

 
 function san_add_taxonomies_to_pages() {
      register_taxonomy_for_object_type( 'post_tag', 'page' );
      register_taxonomy_for_object_type( 'category', 'page' );
  } 

 add_action( 'init', 'san_add_taxonomies_to_pages' ); //hook


 if ( ! is_admin() ) {
 add_action( 'pre_get_posts', 'san_category_and_tag_archives' );  //hook
    
   }

// Add Page as post_type in the tag.php and archive.php files. 

function san_category_and_tag_archives( $wp_query ) {

	$my_san_post_array = array('post','page');
	
	if ( $wp_query->get( 'category_name' ) || $wp_query->get( 'cat' ) )
      $wp_query->set( 'post_type', $my_san_post_array );
	
    if ( $wp_query->get( 'tag' ) )
      $wp_query->set( 'post_type', $my_san_post_array );

  }

  
// end of the Plugin
  
?>