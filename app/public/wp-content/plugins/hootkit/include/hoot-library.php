<?php
/**
 * Helper functions for HootKit and Hoot Themes
 * used all over the plugin and display templates / views
 * All functions are wrapped in 'fuction_exist' check
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}


/* == Lib/Sanitization == */


/**
 * sanitize_html_class works just fine for a single class
 * This is an extension to sanitize_html_class for sanitizing more than one class (with spaces or in an array)
 *
 * @param mixed string/array
 * @param mixed $fallback
 * @return mixed string / $fallback
 */
if ( !function_exists( 'hoot_sanitize_html_classes' ) ):
function hoot_sanitize_html_classes( $class, $fallback = null ) {
	if ( is_string( $class ) ) {
		$class = explode( " ", $class );
	} 
	if ( is_array( $class ) && count( $class ) > 0 ) {
		$class = array_map( "sanitize_html_class", $class );
		return implode( " ", $class );
	}
	else { 
		return sanitize_html_class( $class, $fallback );
	}
}
endif;


/* == Lib/Template == */


/**
 * Outputs an HTML element's attributes.
 *
 * @since 3.0.0
 * @access public
 * @param string $slug The slug/ID of the element (e.g., 'sidebar').
 * @param string $context A specific context (e.g., 'primary').
 * @param string|array $attr Addisitonal css classes to add / Array of attributes to pass in (overwrites filters).
 * @return void
 */
if ( !function_exists( 'hoot_attr' ) ):
function hoot_attr( $slug, $context = '', $attr = '' ) {
	echo hoot_get_attr( $slug, $context, $attr );
}
endif;

/**
 * Gets an HTML element's attributes. This function is actually meant so plugins and child themes can easily filter data.
 * The purpose is to allow modify, remove, or add any attributes without having to edit every template file in the theme.
 * So, one could support microformats instead of microdata, if desired.
 *
 * @since 3.0.0
 * @access public
 * @param string $slug The slug/ID of the element (e.g., 'sidebar').
 * @param string $context A specific context (e.g., 'primary').
 * @param string|array $attr Addisitonal css classes to add / Array of attributes to pass in (overwrites filters).
 * @return string
 */
if ( !function_exists( 'hoot_get_attr' ) ):
function hoot_get_attr( $slug, $context = '', $attr = '' ) {

	/* Define variables */
	$out             = '';
	$classes         = ( is_string( $attr ) ) ? $attr : '';
	$attr            = ( is_array( $attr ) ) ? $attr : array();
	$attr['classes'] = ( !empty( $classes ) ) ? $classes : '';

	/* Build attrs */
	// $slugger = str_replace( "-", "_", $slug );
	$attr = apply_filters( "hoot_attr_{$slug}", $attr, $context );
	if ( !isset( $attr['class'] ) )
		$attr['class'] = $slug;

	/* Add custom Classes if any */
	if ( !empty( $attr['classes'] ) && is_string( $attr['classes'] ) )
		$attr['class'] .= ' ' . $attr['classes'];
	unset( $attr['classes'] );

	/* Create attributes */

	// 1. Get ID and class first
	foreach ( array( 'id', 'class' ) as $key ) {
		if ( !empty( $attr[ $key ] ) ) {
			$out .= ' ' . esc_attr( $key ) . '="' . hoot_sanitize_html_classes( $attr[ $key ] ) . '"';
			unset( $attr[ $key ] );
		}
	}

	// 2. Remaining attributes
	foreach ( $attr as $name => $value ) {
		if ( $value !== false ) {
			$out .= ( !empty( $value ) ) ?
					' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"' :
					' ' . esc_attr( $name );
		}
	}

	return trim( $out );
}
endif;

/**
 * Get excerpt with Custom Length
 * This function must be used within loop
 *
 * @since 3.0.0
 * @access public
 * @param int $words
 * @return string
 */
if ( !function_exists( 'hoot_get_excerpt' ) ):
function hoot_get_excerpt( $words ) {
	if ( empty( $words ) ) {
		return apply_filters( 'the_excerpt', get_the_excerpt() );
	} else {
		hoot_set_data( 'excerpt_customlength', $words );
		add_filter( 'excerpt_length', 'hoot_getexcerpt_customlength', 99999 );
		$return = apply_filters( 'the_excerpt', get_the_excerpt() );
		remove_filter( 'excerpt_length', 'hoot_getexcerpt_customlength', 99999 );
		hoot_unset_data( 'excerpt_customlength' );
		return $return;
	}
}
endif;

/**
 * Custom Excerpt Length if set
 *
 * @since 3.0.0
 * @access public
 * @param int $length
 * @return int
 */
if ( !function_exists( 'hoot_getexcerpt_customlength' ) ):
function hoot_getexcerpt_customlength( $length ){
	$excerpt_customlength = hoot_data( 'excerpt_customlength' );
	if ( !empty( $excerpt_customlength ) )
		return $excerpt_customlength;
	else
		return $length;
}
endif;


/* == Lib/Init == */


/* Create an empty object for storing hoot data */
global $hoot_data;
if ( !isset( $hoot_data ) || !is_object( $hoot_data ) )
	$hoot_data = new stdClass();

/**
 * This function is useful for quickly grabbing data
 *
 * @since 3.0.0
 * @access public
 * @param string
 * @return mixed
 */
if ( !function_exists( 'hoot_data' ) ) :
function hoot_data( $key = '', $subkey = '' ) {
	global $hoot_data;

	// Return entire data object if no key provided
	if ( ! $key ) {
		return $hoot_data;
	}

	// Return data value
	elseif ( $key && is_string( $key ) ) {
		if ( isset( $hoot_data->$key ) ) {

			if ( !$subkey || ( !is_string( $subkey ) && !is_integer( $subkey ) ) )
				return $hoot_data->$key;

			if ( is_object( $hoot_data->$key ) )
				return ( isset( $hoot_data->$key->$subkey ) ) ? $hoot_data->$key->$subkey : null;

			if ( is_array( $hoot_data->$key ) ) {
				$arr = $hoot_data->$key;
				return ( isset( $arr[ $subkey ] ) ) ? $arr[ $subkey ] : null;
			}

		} else {

			// $key has not been set in $hoot_data
			return null;

		}
	}

	// $key provided but isn't a string - Nothing!

}
endif;
/* Declare 'hoot_get_data' for brevity */
if ( !function_exists( 'hoot_get_data' ) ) :
function hoot_get_data( $key = '', $subkey = '' ) {
	return hoot_data( $key, $subkey );
}
endif;

/**
 * Sets properties of the hoot_data class. This function is useful for quickly setting data
 *
 * @since 3.0.0
 * @access public
 * @param string
 * @param mixed
 * @param bool $override
 * @return void
 */
if ( !function_exists( 'hoot_set_data' ) ) :
function hoot_set_data( $key, $value, $override = true ) {
	global $hoot_data;
	if ( !isset( $hoot_data->$key ) || $override )
		$hoot_data->$key = $value;
}
endif;

/**
 * Unsets properties of the hoot_data class. This function is useful for quickly setting data
 *
 * @since 3.0.0
 * @access public
 * @param string
 * @return void
 */
if ( !function_exists( 'hoot_unset_data' ) ) :
function hoot_unset_data( $key ) {
	global $hoot_data;
	if ( isset( $hoot_data->$key ) )
		unset( $hoot_data->$key );
}
endif;

/**
 * Set theme detail constants if not set already
 */
$hootkit_theme = hoot_data( 'theme' );
if ( empty( $hootkit_theme ) ) {
	hoot_set_data( 'theme', wp_get_theme() );
	if ( is_child_theme() ) {
		hoot_set_data( 'childtheme_version',  hoot_data( 'theme' )->get( 'Version' ) );
		hoot_set_data( 'childtheme_name',     hoot_data( 'theme' )->get( 'Name' ) );
		hoot_set_data( 'childtheme_author_uri',hoot_data( 'theme' )->get( 'AuthorURI' ) );
		if ( !empty( hoot_data( 'theme' )->parent() ) ) {
			hoot_set_data( 'template_version',    hoot_data( 'theme' )->parent()->get( 'Version' ) );
			hoot_set_data( 'template_name',       hoot_data( 'theme' )->parent()->get( 'Name' ) );
			hoot_set_data( 'template_author',     hoot_data( 'theme' )->parent()->get( 'Author' ) );
			hoot_set_data( 'template_author_uri', hoot_data( 'theme' )->parent()->get( 'AuthorURI' ) );
		};
	} else {
		hoot_set_data( 'template_version',    hoot_data( 'theme' )->get( 'Version' ) );
		hoot_set_data( 'template_name',       hoot_data( 'theme' )->get( 'Name' ) );
		hoot_set_data( 'template_author',     hoot_data( 'theme' )->get( 'Author' ) );
		hoot_set_data( 'template_author_uri', hoot_data( 'theme' )->get( 'AuthorURI' ) );
	}
}

/**
 * Custom allowed tags to accomodate microdata schema to be used in wp_kses()
 */
global $allowedposttags;
$hootallowedtags = $allowedposttags;
$hootallowedtags[ 'time' ] = array( 'id' => 1, 'class' => 1, 'datetime' => 1, 'title' => 1 );
$hootallowedtags[ 'meta' ] = array( 'content' => 1 );
foreach ( $hootallowedtags as $key => $value ) {
	if ( !empty( $value ) ) $hootallowedtags[ $key ]['itemprop'] = $hootallowedtags[ $key ]['itemscope'] = $hootallowedtags[ $key ]['itemtype'] = 1;
}
hoot_set_data( 'hootallowedtags', $hootallowedtags );


/* == Lib/Locations == */


/**
 * A function for loading a custom widget template. This works similar to the WordPress `get_*()` template functions. 
 * It's purpose is for loading a widget display template in a theme/child-theme (primarily by Hootkit plugin).
 * This function looks for widget templates within the 'widget' sub-folder or the root theme folder.
 * The templates are saved in static variable, so each template is only located once if it is needed.
 *
 * @since 3.0.0
 * @access public
 * @param string $name
 * @param bool|string $load
 * @return void
 */
if ( !function_exists( 'hoot_get_widget' ) ) :
function hoot_get_widget( $name, $load = true ) {

	/* Store template locations */
	static $widget_templates = array();

	/* Create an array of template files to look for. */
	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "widget-{$name}.php"; // Not recommended in theme to allow easy child theme customization
		$templates[] = "hootkit/widget-{$name}.php";
		$templates[] = "template-parts/widget-{$name}.php";
	}

	$templates[] = 'widget.php';         // Not recommended in theme to allow easy child theme customization
	$templates[] = 'hootkit/widget.php';
	$templates[] = 'template-parts/widget.php';

	// Allow devs to filter the template hierarchy.
	$templates = apply_filters( 'hoot_widget_template_hierarchy', $templates, $name );

	/* Return array */
	if ( $load === 'array' )
		return $templates;

	/* Check if a template has been provided for the specific widget.  If not, get the template. */
	if ( ! isset( $widget_templates[ $name ] ) ) {

		// Locate the widget template.
		$customtemplate = apply_filters( 'hoot_widget_template', false, $name );
		$template = ( is_string( $customtemplate ) ) ? $customtemplate : locate_template( $templates );

		// Set the template location
		$widget_templates[ $name ] = $template;

	}

	/* If a template was found, load/return the template. */
	if ( ! empty( $widget_templates[ $name ] ) ) {
		if ( $load ) {
			require( $widget_templates[ $name ] );
		} else {
			return $widget_templates[ $name ];
		}
	}

}
endif;


/* == Lib/Helpers == */


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
		return apply_filters( 'hoot_admin_list_item_count', 999 );
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