<?php
/**
 * Helper functions. Functions which don't have a natural home in other files.
 * Functions defined here are generally used across the entire theme to make various tasks faster.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Enhanced current_theme_supports to check for arguments
 * @NU
 *
 * @since 3.0.0
 * @access public
 * @param string $feature
 * @param string $arg
 */
if ( !function_exists( 'hoot_current_theme_supports' ) ):
function hoot_current_theme_supports( $feature, $arg = '' ) {
	if ( !empty( $arg ) ) {
		$support = get_theme_support( $feature );
		return ( isset( $support[0] ) && is_array( $support[0] ) && in_array( $arg, $support[0] ) );
	} else {
		return current_theme_supports( $feature );
	}
}
endif;

/**
 * Trim a string like PHP trim function
 * It additionally trims the <br> tags and breaking and non breaking spaces as well
 * @NU
 *
 * @since 3.0.0
 * @access public
 * @param string $content
 * @return string
 */
if ( !function_exists( 'hoot_trim' ) ):
function hoot_trim( $content ) {
	$content = trim( $content, " \t\n\r\0\x0B\xC2\xA0" ); // trim non breaking spaces as well
	$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);
	$content = preg_replace('/(?:<br\s*\/?>\s*)+$/', '', $content);
	$content = trim( $content, " \t\n\r\0\x0B\xC2\xA0" ); // trim non breaking spaces as well
	return $content;
}
endif;

/**
 * Trim a string to defined length
 * JNES@HK
 *
 * @since 3.0.0
 * @access public
 * @param string $content
 * @param int $words
 * @return string
 */
if ( !function_exists( 'hoot_trim_content' ) ):
function hoot_trim_content( $raw, $words ) {
	$text = $raw;
	$text = strip_shortcodes( $text );
	// $text = apply_filters( 'the_content', $text );
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = wp_trim_words( $text, $words, '' );
	return apply_filters( 'wp_trim_excerpt', $text, $raw );
}
endif;

/**
 * Insert into associative array at a specific location
 * @NU
 *
 * @since 3.0.0
 * @access public
 * @param array $insert
 * @param array $target
 * @param int|string $location 0 based position, or key in $target
 * @param string $order 'before' or 'after'
 * @return array
 */
if ( !function_exists( 'hoot_array_insert' ) ):
function hoot_array_insert( $insert, $target, $location, $order = 'before' ) {

	if ( !is_array( $insert ) || !is_array( $target ) )
		return $target;

	if ( is_int( $location ) ) {

		if ( $order == 'after' )
			$location++;
		$target = array_slice( $target, 0, $location, true ) +
					$insert +
					array_slice( $target, $location, count( $target ) - 1, true );
		return $target;

	} elseif ( is_string( $location ) ) {

		$count = ( $order == 'after' ) ? 1 : 0;
		foreach ( $target as $key => $value ) {
			if ( $key === $location ) {
				$target = array_slice( $target, 0, $count, true ) +
							$insert +
							array_slice( $target, $count, count( $target ) - 1, true );
				return $target;
			}
			$count++;
		}
		// $location not found. So lets just return a simple array merge
		return array_merge( $target, $insert );

	}

	// Just for brevity
	return $target;
}
endif;

/**
 * Helper function for getting the minified script uri if available.
 *
 * @since 3.0.0
 * @access public
 * @param string $location
 * @param string $return uri or path
 * @return string
 */
if ( !function_exists( 'hoot_locate_script' ) ):
function hoot_locate_script( $location, $return = 'uri' ) {
	$location = preg_replace( array(
		'/\.min\.css$/',
		'/\.css$/',
		), '', $location );
	return hoot_locate_uri( $location, 'js', $return );
}
endif;

/**
 * Helper function for getting the minified style uri if available.
 *
 * @since 3.0.0
 * @access public
 * @param string $location
 * @param string $return uri or path
 * @return string
 */
if ( !function_exists( 'hoot_locate_style' ) ):
function hoot_locate_style( $location, $return = 'uri' ) {
	$location = preg_replace( array(
		'/\.min\.js$/',
		'/\.js$/',
		), '', $location );
	return hoot_locate_uri( $location, 'css', $return );
}
endif;

/**
 * Helper function for getting the minified script/style uri if available.
 *
 * @since 3.0.0
 * @access public
 * @param string $location absolute or relative path
 * @param string $type
 * @param string $return uri or path
 * @return string
 */
if ( !function_exists( 'hoot_locate_uri' ) ):
function hoot_locate_uri( $location, $type, $return = 'uri' ) {

	$location = str_replace( array(
		hoot_data()->template_uri . 'premium/',
		hoot_data()->template_dir . 'premium/',
		hoot_data()->template_uri,
		hoot_data()->template_dir,
		), '', $location );

	$pattern = apply_filters( 'hoot_locate_uri_extension_pattern', array( '/\.min\.' . $type . '$/', '/\.' . $type . '$/' ) );
	$location = preg_replace( $pattern, '', $location );

	if ( is_admin() )
		$loadminified = true;
	elseif ( defined( 'HOOT_DEBUG' ) )
		$loadminified = ( HOOT_DEBUG ) ? false : true;
	else
		$loadminified = hoot_get_mod( 'load_minified', 0 );

	/** Prepare Locations **/

	$locations = array();
	if ( is_child_theme() ) {

		if ( $loadminified )
			$locations['child-default-min'] = array(
				'path' => hoot_data()->child_dir . $location . '.min.' . $type,
				'uri'  => hoot_data()->child_uri . $location . '.min.' . $type,
				);

		$locations['child-default'] = array(
			'path' => hoot_data()->child_dir . $location . '.' . $type,
			'uri'  => hoot_data()->child_uri . $location . '.' . $type,
			);

	}

	if ( $loadminified )
		$locations['default-min'] = array(
			'path' => hoot_data()->template_dir . $location . '.min.' . $type,
			'uri'  => hoot_data()->template_uri . $location . '.min.' . $type,
			);

	$locations['default'] = array(
		'path' => hoot_data()->template_dir . $location . '.' . $type,
		'uri'  => hoot_data()->template_uri . $location . '.' . $type,
		);

	$locations = apply_filters( 'hoot_locate_uri', $locations, $location, $type, $loadminified );

	/** Locate the file **/

	$located = array( 'path' => '', 'uri' => '' );
	foreach ( $locations as $locate ) {
		if ( file_exists( $locate['path'] ) ) {
			$located = $locate;
			break;
		}
	}

	if ( $return == 'path' )
		return $located['path'];
	else
		return $located['uri'];

}
endif;

/**
 * A class of helper functions to cache and build options
 * 
 * @since 3.0.0
 */
if ( !class_exists( 'Hoot_List' ) ):
class Hoot_List {

	/**
	 * List length
	 *
	 * @since 3.0.0
	 * @return int
	 */
	static function listlength(){
		return apply_filters( 'hoot_admin_list_item_count', 75 );
	}

	/**
	 * Utility functions for processing list count
	 *
	 * @since 3.0.0
	 * @return int
	 */
	static function countval( $number ){

		if ( $number===false)
			return self::listlength();

		$number = absint( $number );
		if ( empty( $number ) || $number < 0 )
			return 0;

		return $number;
	}

	/**
	 * Get pages array
	 *
	 * @since 3.0.0
	 * @param int $number
	 * @param string $post_type for custom post types
	 * @return array
	 */
	static function get_pages( $number = 0, $post_type = 'page' ){
		$number = ( !absint( $number ) ) ? -1 : absint( $number ); // get_pages() doesnt allow -1 as number
		$pages = array();
		$the_query = new WP_Query( array( 'post_type' => $post_type, 'posts_per_page' => $number, 'orderby' => 'post_title', 'order' => 'ASC', 'post_status' => 'publish' ) );
		// Prietable plugin (wpalchemy) bug compatibility: We cannot run a custom loop (with
		// $the_query->the_post() ) since this will set global $post (initially empty before looping
		// through custom query). Even wp_reset_postdata() doesnt set global $post back to empty
		// wpalchemy uses global $post->ID, and hence gets the ID of last page instead of empty (at
		// a later hook, it would have got its easy table's post ID)
		// All this happens in Metabox.php file in easy-pricing-tables (hooked to 'admin_init' at 10)
		// if ( $the_query->have_posts() ) :
		// 	while ( $the_query->have_posts() ) : $the_query->the_post();
		// 		$pages[ get_the_ID() ] = get_the_title();
		// 	endwhile;
		// 	wp_reset_postdata();
		// endif;
		if ( !empty( $the_query->posts ) )
			foreach ( $the_query->posts as $post ) if( !empty( $post->ID ) )
				$pages[ $post->ID ] = ( empty( $post->post_title ) ) ? '' : apply_filters( 'the_title', $post->post_title, $post->ID );
		return $pages;
	}

	/**
	 * Get posts array
	 *
	 * @since 3.0.0
	 * @param int $number
	 * @return array
	 */
	static function get_posts( $number = 0 ){
		$number = ( absint( $number ) ) ? absint( $number ) : 0;
		$posts = array();
		$object = get_posts("numberposts=$number");
		foreach ( $object as $post ) {
			$posts[ $post->ID ] = $post->post_title;
		}
		return $posts;
	}

	/**
	 * Get terms array
	 *
	 * @since 3.0.0
	 * @param int $number
	 * @param string $taxonomy
	 * @return array
	 */
	static function get_terms( $number = 0, $taxonomy = 'category' ){
		$number = ( absint( $number ) ) ? absint( $number ) : 0;
		$terms = array();
		$object = (array) get_terms( array( 'taxonomy' => $taxonomy, 'number' => $number ) );
		foreach ( $object as $term )
			$terms[$term->term_id] = $term->name;
		return $terms;
	}

	/**
	 * Pull all the categories into an array
	 *
	 * @since 3.0.0
	 * @param int $number false for default list length, empty or -1 for all
	 * @return array
	 */
	static function categories( $number = false ){
		$number = self::countval( $number );

		if ( $number == self::listlength() ) {
			static $options_categories_default = array();
			if ( empty( $options_categories_default ) )
				$options_categories_default = self::get_terms( $number, 'category' );
			return $options_categories_default;
		}

		elseif ( empty( $number ) ) {
			static $options_categories = array();
			if ( empty( $options_categories ) )
				$options_categories = self::get_terms( $number, 'category' );
			return $options_categories;
		}

		else
			return self::get_terms( $number, 'category' );

	}

	/**
	 * Pull all the tags into an array
	 *
	 * @since 3.0.0
	 * @param int $number false for default list length, empty or -1 for all
	 * @return array
	 */
	static function tags( $number = false ){
		$number = self::countval( $number );

		if ( $number == self::listlength() ) {
			static $options_tags_default = array();
			if ( empty( $options_tags_default ) )
				$options_tags_default = self::get_terms( $number, 'post_tag' );
			return $options_tags_default;
		}

		elseif ( empty( $number ) ) {
			static $options_tags = array();
			if ( empty( $options_tags ) )
				$options_tags = self::get_terms( $number, 'post_tag' );
			return $options_tags;
		}

		else
			return self::get_terms( $number, 'post_tag' );

	}

	/**
	 * Pull all the pages into an array
	 *
	 * @since 3.0.0
	 * @param int $number false for default list length, empty or -1 for all
	 * @return array
	 */
	static function pages( $number = false ){
		$number = self::countval( $number );

		if ( $number == self::listlength() ) {
			static $options_pages_default = array();
			if ( empty( $options_pages_default ) )
				$options_pages_default = self::get_pages( $number, 'page' );
			return $options_pages_default;
		}

		elseif ( empty( $number ) ) {
			static $options_pages = array();
			if ( empty( $options_pages ) )
				$options_pages = self::get_pages( $number, 'page' );
			return $options_pages;
		}

		else
			return self::get_pages( $number, 'page' );

	}

	/**
	 * Pull all the posts into an array
	 *
	 * @since 3.0.0
	 * @param int $number false for default list length, empty or -1 for all
	 * @return array
	 */
	static function posts( $number = false ){
		$number = self::countval( $number );

		if ( $number == self::listlength() ) {
			static $options_posts_default = array();
			if ( empty( $options_posts_default ) )
				$options_posts_default = self::get_posts( $number );
			return $options_posts_default;
		}

		elseif ( empty( $number ) ) {
			static $options_posts = array();
			if ( empty( $options_posts ) )
				$options_posts = self::get_posts( $number );
			return $options_posts;
		}

		else
			return self::get_posts( $number );

	}

	/**
	 * Pull all the cpt posts into an array
	 *
	 * @since 3.0.0
	 * @param string $post_type for custom post types
	 * @param int $number false for default list length, empty or -1 for all
	 * @return array
	 */
	static function cpt( $post_type = 'page', $number = false ){
		$number = self::countval( $number );

		if ( $number == self::listlength() ) {
			static $cpt_default = array();
			if ( empty( $cpt_default[ $post_type ] ) )
				$cpt_default[ $post_type ] = self::get_pages( $number, $post_type );
			$return = $cpt_default[ $post_type ];
		}

		elseif ( empty( $number ) ) {
			static $cpt = array();
			if ( empty( $cpt[ $post_type ] ) )
				$cpt[ $post_type ] = self::get_pages( $number, $post_type );
			$return = $cpt[ $post_type ];
		}

		else
			$return = self::get_pages( $number, $post_type );

		return $return;

	}

}
endif;

/**
 * Options builder helper product functions
 *
 * (array)Hoot_List::get_terms( 0, 'product_cat' ) => doesnt work in register widget class, most likely due to action heirarchy (i.e. 'widgets_init').
 * First action this is available in is 'wp_default_styles', hence can actually be used in action 'wp_loaded'
 * Hence this would have worked in form(), however doesnt work when we use it in __construct()
 */
if ( !function_exists( 'hoot_list_products_category' ) ):
function hoot_list_products_category(){
	// $product_cats = get_categories( array( 'taxonomy' => 'product_cat', 'orderby' => 'name', 'hierarchical' => 1, 'hide_empty' => 0, ) );
	// $product_cat = array(); foreach ( $product_cats as $pcat ) $product_cat[ $pcat->term_id ] = $pcat->name;
	return (array)Hoot_List::get_terms( 0, 'product_cat' );
}
endif;