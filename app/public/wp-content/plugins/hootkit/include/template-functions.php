<?php
/**
 * Miscellaneous template tags and utilit helper functions
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Array Walk/Map function
 * 
 *
 * @since 1.0.13
 * @access public
 * @param string $s
 * @return array
 */
if ( !function_exists( 'hootkit_append_negative' ) ):
function hootkit_append_negative( $s ) {
	if ( is_string( $s ) ) return '-' . $s;
	else return '';
}
endif;

/**
 * Utility function to extract border class for widget based on user option.
 *
 * @since 1.0.0
 * @access public
 * @param string $val string value separated by spaces
 * @param int $index index for value to extract from $val
 * @prefix string $prefix prefixer for css class to return
 * @return  void
 */
if ( !function_exists( 'hootkit_widget_borderclass' ) ):
function hootkit_widget_borderclass( $val, $index=0, $prefix='' ) {
	$val = explode( " ", trim( $val ) );
	if ( isset( $val[ $index ] ) )
		return $prefix . trim( $val[ $index ] );
	else
		return '';
}
endif;

/**
 * Utility function to create style string attribute.
 *
 * @since 1.0.0
 * @access public
 * @param string $mt margin top
 * @param string $mb margin bottom
 * @return string
 */
if ( !function_exists( 'hootkit_widget_marginstyle' ) ):
function hootkit_widget_marginstyle( $mt='', $mb='' ) {
	$return = '';
	if ( $mt===0 || $mt==='0' ) {
		$return .= " margin-top:0px;";
	} else {
		$margin = intval( $mt );
		if ( !empty( $margin ) ) $return .= " margin-top:{$margin}px;";
	}
	if ( $mb===0 || $mb==='0' ) {
		$return .= " margin-bottom:0px;";
	} else {
		$margin = intval( $mb );
		if ( !empty( $margin ) ) $return .= " margin-bottom:{$margin}px;";
	}
	if ( !empty( $return ) ) $return = ' style="'.$return.'" ';
	return $return;
}
endif;

/**
 * Add custom widget css class and styles
 *
 * @since 1.0.0
 * @access public
 * @param array $string
 * @param array $instance
 * @return string
 */
if ( !function_exists( 'hootkit_add_widgetstyle' ) ):
function hootkit_add_widgetstyle( $string, $instance ) {

	if ( !empty( $instance ) && !empty( $instance['customcss'] ) ) {
		$customcss = $instance['customcss'];
		$newstring = 'class="widget';
		$newstring .= ( !empty( $customcss['class'] ) ) ? ' ' . hoot_sanitize_html_classes( $customcss['class'] ) . ' ' : '';
		$mt = ( !isset( $customcss['mt'] ) ) ? '' : $customcss['mt'];
		$mb = ( !isset( $customcss['mb'] ) ) ? '' : $customcss['mb'];
		$newstring = hootkit_widget_marginstyle( $mt, $mb ) . $newstring;
		return str_replace( 'class="widget', $newstring, $string );
	}

	return $string;
}
endif;
add_filter( 'hootkit_before_widget', 'hootkit_add_widgetstyle', 10, 2 );

/**
 * Return Skype contact button code
 * Ref: https://www.skype.com/en/developer/create-contactme-buttons/
 *
 * @since 1.0.0
 * @access public
 * @param string $username Skype Username to create the Skype button
 * @return void
 */
if ( !function_exists( 'hootkit_get_skype_button' ) ) :
function hootkit_get_skype_button( $username ) {
	static $script = false;
	static $id = 1;
	$code = '';
	$action = apply_filters( 'hootkit_skype_button_action', 'call' );

	if ( !$script )
		$code .= '<script type="text/javascript"' .
				 ' src="' . esc_url('https://secure.skypeassets.com/i/scom/js/skype-uri.js') . '"'.
				 '></script>';

	$code .= '<div id="SkypeButton_Call_' . esc_attr( $username ) . '_' . $id . '" class="hoot-skype-call-button">';
	$code .= '<script type="text/javascript">';
	$code .=  'Skype.ui({'
			. '"name": "' . esc_attr( $action ) . '",' // dropdown (doesnt work well), call, chat
			. '"element": "SkypeButton_Call_' . esc_attr( $username ) . '_' . $id . '",'
			. '"participants": ["' . esc_attr( $username ) . '"],'
			//. '"imageColor": "white",' // omit for blue
			. '"imageSize": 24' // 10, 12, 14, 16 (omit), 24, 32
			. '});';
	$code .= '</script>';
	$code .= '</div>';

	$code = apply_filters( 'hootkit_get_skype_button', $code, $script, $id, $action );
	$script = true;
	$id++;
	return $code;
}
endif;

/**
 * Predict the optimum image size based on column span
 *
 * @since 1.0.0
 * @access public
 * @param string $size span or column size or actual image size name. Default is content width span.
 * @param bool $crop true|false|null Using null will return closest matched image irrespective of its crop setting
 * @param string $default Image size if hoot theme function does not exist
 * @return string
 */
if ( !function_exists( 'hootkit_thumbnail_size' ) ):
function hootkit_thumbnail_size( $size = '', $crop = NULL, $default = 'full' ) {

	// Hoot Framework >=v3.0.1
	if ( function_exists( 'hoot_thumbnail_size' ) )
		return hoot_thumbnail_size( $size, $crop );

	// Hoot Framework v3.0.0
	// JNES@deprecated <= Unos v2.8.0 @8.19
	if ( function_exists( 'hoot_theme_thumbnail_size' ) )
		return hoot_theme_thumbnail_size( $size, $crop );

	// Non Hoot Framework themes
	$registered = get_intermediate_image_sizes();
	if ( in_array( $size, $registered ) )
		return $size;

	// @todo `hoot_get_image_sizes` logic => use best guess in templates using `hootkit_thumbnail_size`
	// if ( is_numeric( $default ) ) {
	// 	$size = absint( $default );
	// }

	return esc_attr( apply_filters( 'hootkit_thumbnail_size', $default, $size, $crop ) );
}
endif;

/**
 * Display the post thumbnail image
 *
 * @since 1.0.0
 * @access public
 * @param string $classes additional classes
 * @param string $size span or column size or actual image size name. Default is content width span.
 * @param bool $miscrodata true|false Add microdata or not
 * @param string $link image link url
 * @param bool $crop true|false|null Using null will return closest matched image irrespective of its crop setting
 * @param string $default Image size if hoot theme function does not exist
 * @return void
 */
if ( !function_exists( 'hootkit_post_thumbnail' ) ):
function hootkit_post_thumbnail( $classes = '', $size = '', $microdata = false, $link = '', $crop = NULL, $default = 'full' ) {

	// Hoot Framework >=v3.0.1
	if ( function_exists( 'hoot_post_thumbnail' ) ) {
		hoot_post_thumbnail( $classes, $size, $microdata, $link, $crop );
	}
	// Hoot Framework v3.0.0
	// JNES@deprecated <= Unos v2.8.0 @8.19
	elseif ( function_exists( 'hoot_theme_post_thumbnail' ) ) {
		hoot_theme_post_thumbnail( $classes, $size, $microdata, $link, $crop );
	}
	// Non Hoot Framework themes
	elseif ( has_post_thumbnail() ) {
		$thumbnail_size = hootkit_thumbnail_size( $size, NULL, $default );
		$custom_class = ( !empty( $classes ) ) ? hoot_sanitize_html_classes( $classes ) : '';
		echo '<div class="entry-featured-img-wrap">';
			if ( !empty( $link ) ) echo '<a href="' . esc_url( $link ) . '" ' . hoot_get_attr( 'entry-featured-img-link' ) . '>';
			the_post_thumbnail( $thumbnail_size, array( 'class' => "attachment-$thumbnail_size $custom_class", 'itemscope' => '' ) );
			if ( !empty( $link ) ) echo '</a>';
		echo '</div>';
	}
}
endif;

/**
 * Display the meta information HTML for single post/page
 *
 * @since 1.0.0
 * @access public
 * @param array $args
 * @return void
 */
if ( !function_exists( 'hootkit_display_meta_info' ) ):
function hootkit_display_meta_info( $args = array() ) {

	$args = wp_parse_args( apply_filters( 'hootkit_display_meta_info_args', $args ), array(
		'display'       => array( 'author', 'date', 'cats', 'tags', 'comments' ),
		'context'       => '',
		'editlink'      => true,
		'wrapper'       => '',
		'wrapper_id'    => '',
		'wrapper_class' => '',
		'empty'         => '<div class="entry-byline empty"></div>',
	) );
	extract( $args, EXTR_SKIP );

	$wrapper = preg_replace( '/[^a-z]/i', '', $wrapper );
	if ( !empty( $wrapper ) ) {
		$wrapperend = "</{$wrapper}>";
		$wrapper_id = ( !empty( $wrapper_id ) ) ? ' id="' . hoot_sanitize_html_classes( $wrapper_id ) . '"' : '';
		$wrapper_class = ( !empty( $wrapper_class ) ) ? ' class="' . hoot_sanitize_html_classes( $wrapper_class ) . '"' : '';
		$wrapper = "<{$wrapper}{$wrapper_id}{$wrapper_class}>";
	}

	// Hoot Framework >=v3.0.1
	if ( function_exists( 'hoot_display_meta_info' ) ) {
		if ( hoot_meta_info( $display, $context, true ) ) {
			echo $wrapper;
			hoot_display_meta_info( $display, $context, $editlink );
			echo $wrapperend;
		}
	}

	// Hoot Framework v3.0.0
	// JNES@deprecated <= Unos v2.8.0 @8.19
	elseif ( function_exists( 'hoot_theme_display_meta_info' ) ) {
		if ( hoot_theme_meta_info( $display, $context, true ) ) {
			echo $wrapper;
			hoot_theme_display_meta_info( $display, $context, $editlink );
			echo $wrapperend;
		}
	}

	// Non Hoot Framework themes
	elseif ( empty( $display ) ) {
		echo ( ( $empty ) ? $wrapper . wp_kses_post( $empty ) . $wrapperend : '' );
	} else {

		$display = array(
			'author'   => in_array( 'author', $display ),
			'date'     => in_array( 'date', $display ),
			'cats'     => in_array( 'cats', $display ),
			'tags'     => in_array( 'tags', $display ),
			'comments' => in_array( 'comments', $display ),
		);

		echo $wrapper;
		/** Begin @HootKit **/

	$blocks = array();

	if ( !empty( $display['author'] ) ) :
		$blocks['author']['label'] = __( 'By:', 'hootkit' );
		ob_start();
		the_author_posts_link();
		$blocks['author']['content'] = '<span ' . hoot_get_attr( 'entry-author' ) . '>' . ob_get_clean() . '</span>';
	endif;

	if ( !empty( $display['date'] ) ) :
		$blocks['date']['label'] = __( 'On:', 'hootkit' );
		$blocks['date']['content'] = '<time ' . hoot_get_attr( 'entry-published' ) . '>' . get_the_date() . '</time>';
	endif;

	if ( !empty( $display['cats'] ) ) :
		$category_list = get_the_category_list(', ');
		if ( !empty( $category_list ) ) :
			$blocks['cats']['label'] = __( 'En:', 'hootkit' );
			$blocks['cats']['content'] = $category_list;
		endif;
	endif;

	if ( !empty( $display['tags'] ) && get_the_tags() ) :
		$blocks['tags']['label'] = __( 'Etiquetas:', 'hootkit' );
		$blocks['tags']['content'] = ( ! get_the_tags() ) ? __( 'No Tags', 'hootkit' ) : get_the_tag_list( '', ', ', '' );
	endif;

	if ( !empty( $display['comments'] ) && comments_open() ) :
		$blocks['comments']['label'] = __( 'With:', 'hootkit' );
		ob_start();
		comments_popup_link(__( '0 Comments', 'hootkit' ),
							__( '1 Comment', 'hootkit' ),
							__( '% Comments', 'hootkit' ), 'comments-link', '' );
		$blocks['comments']['content'] = ob_get_clean();
	endif;

	if ( $editlink && $edit_link = get_edit_post_link() ) :
		$blocks['editlink']['label'] = '';
		$blocks['editlink']['content'] = '<a href="' . $edit_link . '">' . __( 'Edit This', 'hootkit' ) . '</a>';
	endif;

	$blocks = apply_filters( 'hootkit_display_meta_info', $blocks, $args ); // @HootKit

	if ( !empty( $blocks ) )
		echo '<div class="entry-byline">';

	foreach ( $blocks as $key => $block ) {
		if ( !empty( $block['content'] ) ) {
			echo ' <div class="entry-byline-block entry-byline-' . sanitize_html_class( $key ) . '">';
				if ( !empty( $block['label'] ) )
					echo ' <span class="entry-byline-label">' . esc_html( $block['label'] ) . '</span> ';
				echo wp_kses( $block['content'], hoot_data( 'hootallowedtags' ) );
			echo ' </div>';
		}
	}

	// if ( !empty( $display['publisher'] ) ) {} // @HootKit

	if ( !empty( $blocks ) )
		echo '</div><!-- .entry-byline -->';

		/** End @HootKit **/
		echo $wrapperend;

	}

}
endif;

/**
 * Social Icons Widget - Icons
 *
 * @since 1.0.0
 * @access public
 * @param array $attr
 * @param string $context
 * @return array
 */
if ( !function_exists( 'hootkit_attr_social_icons_icon' ) ):
function hootkit_attr_social_icons_icon( $attr, $context ) {
	$attr['class'] = ( empty( $attr['class'] ) ) ? '' : $attr['class'];

	$attr['class'] .= ' social-icons-icon';
	if ( $context != 'fa-envelope' )
		$attr['target'] = '_blank';

	return $attr;
}
endif;
add_filter( 'hoot_attr_social-icons-icon', 'hootkit_attr_social_icons_icon', 10, 2 );

/**
 * Skip slider image from Jetpack's Lazy Load
 * Alternately, we can also add css class 'skip-lazy' to the images
 *
 * @since 1.0.0
 * @access public
 * @param array $attr
 * @param string $context
 * @return array
 */
if ( !function_exists( 'hootkit_jetpack_lazy_load_exclude' ) ):
function hootkit_jetpack_lazy_load_exclude( $classes ) {
	if ( !is_array( $classes ) ) $classes = array();
	$classes[] = 'hootkitslide-img';
	$classes[] = 'hootkitcarousel-img';
	// $classes[] = 'content-block-img'; // use 'skip-lazy' class for content-block-5 images instead of all content block styles
	return $classes;
}
endif;
// 'skip-lazy' class works for a3lazyload as well => Hence using that method instead
// add_filter( 'jetpack_lazy_images_blacklisted_classes', 'hootkit_jetpack_lazy_load_exclude' );

/**
 * Common template function to display view all link in widgets
 *
 * @since 1.1.0
 * @access public
 * @return string
 */
if ( !function_exists( 'hootkit_get_viewall' ) ):
function hootkit_get_viewall( $echo = false, $post_type = 'post' ) {
	global $hoot_data;
	$html = '';
	if ( !empty( $hoot_data->currentwidget['instance'] ) )
		extract( $hoot_data->currentwidget['instance'], EXTR_SKIP );
	if ( !empty( $viewall ) ) {
		switch ( $post_type ) {
			case 'product':
				$base_url = '';
				if ( !empty( $category ) && is_array( $category ) && count( $category ) == 1 ) { // If more than 1 cat selected, show shop url
					$category[0] = (int)$category[0]; // convert string to integer else get_term_link gives error
					$base_url = get_term_link( $category[0], 'product_cat' );
					$base_url = ( !is_wp_error( $base_url ) ) ? esc_url( $base_url ) : '';
				}
				if ( empty( $base_url ) ) {
					$base_url = ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ) ? // returns -1 when no shop page has been set yet
								esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) :
								esc_url( home_url('/') );
				}
				break;
			default:
				if ( !empty( $category ) && is_array( $category ) && count( $category ) == 1 ) { // If more than 1 cat selected, show blog url
					$base_url = esc_url( get_category_link( $category[0] ) );
				} elseif ( !empty( $category ) && !is_array( $category ) ) { // Pre 1.0.10 compatibility with 'select' type
					$base_url = esc_url( get_category_link( $category ) );
				} else {
					$base_url = ( get_option( 'page_for_posts' ) ) ?
								esc_url( get_permalink( get_option( 'page_for_posts' ) ) ) :
								esc_url( home_url('/') );
				}
				break;
		}
		$class = sanitize_html_class( 'viewall-' . $viewall );
		$html = apply_filters( 'hootkit_get_viewall', '<div class="viewall ' . $class . '"><a href="' . $base_url . '">' . __( 'View All', 'hootkit' ) . '</a></div>', $viewall, $class, $base_url );
	}
	if ( $echo ) echo $html;
	else return $html;
}
endif;