<?php
/**
 * General template functions. These functions are for use throughout the theme's various template files.
 * Their main purpose is to handle many of the template tags that are currently lacking in core WordPress.
 *
 * @credit https://github.com/justintadlock/hybrid-core/blob/master/inc/template-comments.php
 * @credit https://github.com/justintadlock/hybrid-core/blob/master/inc/template-general.php
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

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
	$classes         = ( is_array( $attr ) && !empty( $attr['classes'] ) && is_string( $attr['classes'] ) ) ? $classes . ' ' . $attr['classes'] : $classes;
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
 * Callback function for 'wp_list_comments' to display individual comments
 *
 * @since 3.0.0
 * @access public
 * @param array
 * @param string
 * @return array
 */
if ( !function_exists( 'hoot_comments_callback' ) ) :
function hoot_comments_callback( $comment ) {

	// Get the comment type of the current comment.
	$comment_type = get_comment_type( $comment->comment_ID );

	// Locate the template and include it
	$template = hoot_get_comment( $comment_type, 'array' );
	if ( !empty( $template ) && is_array( $template ) )
		locate_template( $template, true, false );

}
endif;

/**
 * Callback function for 'wp_list_comments' to display end of individual comments
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
if ( !function_exists( 'hoot_comments_end_callback' ) ) :
function hoot_comments_end_callback() {
	echo '</li><!-- .comment -->';
}
endif;

/**
 * Outputs the comment reply link.  Note that WP's 'comment_reply_link()' doesn't work outside of
 * 'wp_list_comments()' without passing in the proper arguments (it isn't meant to).  This function
 * is just a wrapper for 'get_comment_reply_link()', which adds in the arguments automatically.
 *
 * @since 3.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
if ( !function_exists( 'hoot_comment_reply_link' ) ) :
function hoot_comment_reply_link( $args = array() ) {

	if ( ! get_option( 'thread_comments' ) || in_array( get_comment_type(), array( 'pingback', 'trackback' ) ) )
		return '';

	$args = wp_parse_args(
		$args,
		array(
			'depth'     => intval( $GLOBALS['comment_depth'] ),
			'max_depth' => get_option( 'thread_comments_depth' ),
		)
	);
	echo get_comment_reply_link( $args );

}
endif;

/**
 * Returns a link back to the site.
 *
 * @since 3.0.0
 * @access public
 * @return string
 */

if ( !function_exists( 'hoot_get_site_link' ) ) :
function hoot_get_site_link() {
	return sprintf( '<a class="site-link" href="%s" rel="home">%s</a>', esc_url( home_url() ), get_bloginfo( 'name' ) );
}
endif;

/**
 * Returns a link to WordPress.org.
 *
 * @since 3.0.0
 * @access public
 * @return string
 */
if ( !function_exists( 'hoot_get_wp_link' ) ) :
function hoot_get_wp_link() {
	return sprintf( '<a class="wp-link" href="%s">%s</a>', esc_url( __( 'https://wordpress.org', 'magazine-news-byte' ) ), esc_html__( 'WordPress', 'magazine-news-byte' ) );
}
endif;

/**
 * Returns a link to theme on WordPress.org.
 *
 * @since 3.0.0
 * @access public
 * @param string $link
 * @param string $anchor
 * @return string
 */
if ( !function_exists( 'hoot_get_wp_theme_link' ) ) :
function hoot_get_wp_theme_link( $link = '', $anchor = '' ) {
	$slug   = preg_replace( "/[\s-]+/", " ", strtolower( hoot_data()->template_name ) );
	$slug   = str_replace( " ", "-", $slug );
	$link   = ( empty( $link ) ) ? 'https://wordpress.org/themes/' . $slug : $link;
	$anchor = ( empty( $anchor ) ) ? hoot_data()->template_name : $anchor;
	/* Translators: %s is the Template Name */
	$title  = sprintf( __( '%s WordPress Theme', 'magazine-news-byte' ), hoot_data()->template_name );

	return sprintf( '<a class="wp-theme-link" href="%s" title="%s">%s</a>', esc_url( $link ), esc_attr( $title ), esc_html( $anchor ) );
}
endif;

/**
 * Returns a link to the theme URI.
 *
 * @since 3.0.0
 * @access public
 * @param string $link
 * @param string $anchor
 * @return string
 */
if ( !function_exists( 'hoot_get_theme_link' ) ) :
function hoot_get_theme_link( $link = '', $anchor = '' ) {
	$link   = ( empty( $link ) ) ? ( ( is_child_theme() && is_object( hoot_data( 'theme' )->parent() ) ) ? hoot_data( 'theme' )->parent()->get( 'ThemeURI' ) : hoot_data( 'theme' )->get( 'ThemeURI' ) ) : $link;
	$anchor = ( empty( $anchor ) ) ? hoot_data()->template_name : $anchor;
	/* Translators: %s is the Template Name */
	$title  = sprintf( __( '%s WordPress Theme', 'magazine-news-byte' ), hoot_data()->template_name );

	return sprintf( '<a class="theme-link" href="%s" title="%s">%s</a>', esc_url( $link ), esc_attr( $title ), esc_html( $anchor ) );
}
endif;

/**
 * Get excerpt with Custom Length
 * This function must be used within loop
 * @NU
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
 * @NU
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

/**
 * Temporarily remove read more links from excerpts
 * Used with 'hoot_reinstate_readmore_link'
 * @NU
 *
 * @since 3.0.0
 * @access public
 */
if ( !function_exists( 'hoot_remove_readmore_link' ) ):
function hoot_remove_readmore_link() {
	add_filter( 'hoot_readmore', 'hoot_readmore_empty_string' );
}
endif;
if ( !function_exists( 'hoot_readmore_empty_string' ) ):
function hoot_readmore_empty_string() {
	return '';
}
endif;

/**
 * Reinstate read more links to excerpts
 * Used with 'hoot_remove_readmore_link'
 * @NU
 *
 * @since 3.0.0
 * @access public
 */
if ( !function_exists( 'hoot_reinstate_readmore_link' ) ):
function hoot_reinstate_readmore_link() {
	remove_filter( 'hoot_readmore', 'hoot_readmore_empty_string' );
}
endif;

/**
 * Return the display array of meta blocks to show
 *
 * @since 3.0.1
 * @access public
 * @param array|string $args    (comma delimited) information to display
 * @param string       $context context in which meta blocks are being displayed
 * @param bool         $bool    Return bool value
 * @return array|bool
 */
if ( !function_exists( 'hoot_meta_info' ) ):
function hoot_meta_info( $args = '', $context = '', $bool = false ) {

	if ( !is_array( $args ) )
		$args = array_map( 'trim', explode( ',', $args ) );

	$display = array();
	foreach ( array( 'author', 'date', 'cats', 'tags', 'comments' ) as $key ) {
		if ( in_array( $key, $args ) )
			$display[ $key ] = true;
	}

	// if ( is_page() ) { : returns true in post loop when frontpage set as static page
	if ( get_post_type() == ' post' ) {
		if ( isset( $display['cats'] ) ) unset( $display['cats'] );
		if ( isset( $display['tags'] ) ) unset( $display['tags'] );
	}

	if ( !empty( $display['author'] ) )
		$display['publisher'] = true;

	$display = apply_filters( 'hoot_meta_info', $display, $context );

	if ( $bool ) {
		if ( empty( $display ) ) return false; else return true;
	} else {
		return $display;
	}

}
endif;

/**
 * Display the meta information HTML for single post/page
 *
 * @since 3.0.1
 * @access public
 * @param array|string $args     (comma delimited) information to display
 * @param string       $context  context in which meta blocks are being displayed
 * @param bool         $editlink display Edit link
 * @return void
 */
if ( !function_exists( 'hoot_display_meta_info' ) ):
function hoot_display_meta_info( $args = '', $context = '', $editlink = true ) {

	if ( !is_array( $args ) )
		$args = array_map( 'trim', explode( ',', $args ) );

	$display = hoot_meta_info( $args, $context );

	if ( empty( $display ) ) {
		echo '<div class="entry-byline empty"></div>';
		return;
	}

	$blocks = array();

	if ( !empty( $display['author'] ) ) :
		$blocks['author']['label'] = __( 'By:', 'magazine-news-byte' );
		ob_start();
		the_author_posts_link();
		$blocks['author']['content'] = '<span ' . hoot_get_attr( 'entry-author' ) . '>' . ob_get_clean() . '</span>';
	endif;

	if ( !empty( $display['date'] ) ) :
		$blocks['date']['label'] = __( 'On:', 'magazine-news-byte' );
		$blocks['date']['content'] = '<time ' . hoot_get_attr( 'entry-published' ) . '>' . get_the_date() . '</time>';
	endif;

	if ( !empty( $display['cats'] ) ) :
		$category_list = get_the_category_list(', ');
		if ( !empty( $category_list ) ) :
			$blocks['cats']['label'] = __( 'In:', 'magazine-news-byte' );
			$blocks['cats']['content'] = $category_list;
		endif;
	endif;

	if ( !empty( $display['tags'] ) && get_the_tags() ) :
		$blocks['tags']['label'] = __( 'Tagged:', 'magazine-news-byte' );
		$blocks['tags']['content'] = ( ! get_the_tags() ) ? __( 'No Tags', 'magazine-news-byte' ) : get_the_tag_list( '', ', ', '' );
	endif;

	if ( !empty( $display['comments'] ) && comments_open() ) :
		$blocks['comments']['label'] = __( 'With:', 'magazine-news-byte' );
		ob_start();
		comments_popup_link(__( '0 Comments', 'magazine-news-byte' ),
							__( '1 Comment', 'magazine-news-byte' ),
							__( '% Comments', 'magazine-news-byte' ), 'comments-link', '' );
		$blocks['comments']['content'] = ob_get_clean();
	endif;

	if ( $editlink && $edit_link = get_edit_post_link() ) :
		$blocks['editlink']['label'] = '';
		$blocks['editlink']['content'] = '<a href="' . $edit_link . '">' . __( 'Edit This', 'magazine-news-byte' ) . '</a>';
	endif;

	$blocks = apply_filters( 'hoot_display_meta_info', $blocks, $context, $display, $editlink );

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

	if ( !empty( $display['publisher'] ) ) {
		static $microdatapublisher;
		if ( empty( $microdatapublisher ) ) {
			$pname = get_bloginfo();
			$iid = intval( get_theme_mod( 'custom_logo' ) );
			if ( !empty( $iid ) ) {
				$isrc = wp_get_attachment_image_src( $iid, 'full' );
				if( empty( $isrc ) ) $isrc = wp_get_attachment_image_src( $iid, 'full', true );
				if ( !empty( $isrc[0] ) ) {
					$ilogo = $isrc[0];
					$iwidth = ( empty( $isrc[1] ) ) ? '' : $isrc[1];
					$iheight = ( empty( $isrc[2] ) ) ? '' : $isrc[2];
				}
			}
			if ( empty( $ilogo ) )
				$ilogo = $iwidth = $iheight = '';
			$microdatapublisher = wp_kses( apply_filters( 'hoot_entry_byline_publisher',
				'<span class="entry-publisher" itemprop="publisher" itemscope="itemscope" itemtype="https://schema.org/Organization">' .
					'<meta itemprop="name" content="' . $pname . '">' .
					'<span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">' .
						'<meta itemprop="url" content="' . $ilogo . '">' .
						'<meta itemprop="width" content="' . $iwidth . '">' .
						'<meta itemprop="height" content="' . $iheight . '">' .
					'</span>' .
				'</span>' ), hoot_data( 'hootallowedtags' ) );
		}
		echo $microdatapublisher;
	}

	if ( !empty( $blocks ) )
		echo '</div><!-- .entry-byline -->';

}
endif;

/**
 * Add debug info if HOOT_DEBUG is true
 *
 * @since 3.0.0
 * @access public
 */
if ( !function_exists( 'hoot_debug_info' ) ):
function hoot_debug_info( $msg, $return = false ) {
	static $string = '';
	if ( !empty( $msg ) )
		$string = $string . $msg;
	if ( $return )
		return $string;
}
endif;

/**
 * Add debug info if HOOT_DEBUG is true
 *
 * @since 3.0.0
 * @access public
 */
if ( !function_exists( 'hoot_add_debug_info' ) ):
function hoot_add_debug_info() {
	if ( current_user_can('manage_options') ) {
		echo "\n<!-- HOOT DEBUG INFO-->\n" ;
		if ( function_exists( 'hoot_developer_data' ) )
			echo "\n<!-- " . esc_html( hoot_developer_data() ) . "-->\n" ;
		$info = ( defined( 'HOOT_DEBUG' ) && true === HOOT_DEBUG ) ? hoot_debug_info( '', true ) : '';
		if ( $info )
			echo "<!--\n" . esc_html( $info ) . "\n-->" ;
	}
}
endif;
add_action( 'wp_footer', 'hoot_add_debug_info' );
add_action( 'admin_footer', 'hoot_add_debug_info' );

/**
 * Display Site Performance Data
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
if ( !function_exists( 'hoot_developer_data' ) ):
function hoot_developer_data( $commented = true ) {
	ob_start();
	echo intval( get_num_queries() ) . ' ' . esc_attr__( 'queries.', 'magazine-news-byte' ) . ' ';
	timer_stop(1);
	echo esc_html( __( ' seconds. ', 'magazine-news-byte' ) . ( memory_get_peak_usage(1) / 1024 ) / 1024 . 'MB' );
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}
endif;