<?php
/**
 * Filters for theme-related WordPress features. These filters are for handling adding or modifying the
 * output of common WordPress template tags. Many of the filters are simply for adding HTML5 microdata.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/* Add extra support for post types. */
add_action( 'init', 'hoot_add_post_type_support', 15 );
add_action( 'edit_form_after_title', 'hoot_add_page_for_posts_editor', 0 );

/* Filters the archive title and description. */
add_filter( 'get_the_archive_title_prefix', '__return_false' ); // Replaces the earlier filter fn for get_the_archive_title hook
add_filter( 'get_the_archive_title',        'hoot_archive_title_filter',       5  );
add_filter( 'get_the_archive_description',  'hoot_archive_description_filter', 10 );

/* Don't strip tags on single post titles. */
remove_filter( 'single_post_title', 'strip_tags' );

/* Filters the title for untitled posts. */
add_filter( 'the_title', 'hoot_untitled_post' );

/* Default excerpt more. */
add_filter( 'the_content_more_link', 'hoot_readmore', 5 );
if ( apply_filters( 'hoot_force_excerpt_readmore', true ) ) {
	add_filter( 'excerpt_more', 'hoot_readmore_quicktag', 6 );
	add_filter( 'wp_trim_excerpt', 'hoot_readmore_quicktag_replace', 6, 2 );
} else {
	add_filter( 'excerpt_more', 'hoot_readmore', 5 );
}

/* Modifies the arguments and output of wp_link_pages(). */
add_filter( 'wp_link_pages_args', 'hoot_link_pages_args', 5 );
add_filter( 'wp_link_pages_link', 'hoot_link_pages_link', 5 );

/* Filters to add microdata support to common template tags. */
add_filter( 'the_author_posts_link',          'hoot_the_author_posts_link',          5 );
add_filter( 'get_comment_author_link',        'hoot_get_comment_author_link',        5 );
add_filter( 'get_comment_author_url_link',    'hoot_get_comment_author_url_link',    5 );
add_filter( 'get_avatar',                     'hoot_get_avatar',                     5 );
add_filter( 'post_thumbnail_html',            'hoot_post_thumbnail_html',            5 );
add_filter( 'comments_popup_link_attributes', 'hoot_comments_popup_link_attributes', 5 );

/* Adds custom CSS classes to nav menu items. */
add_filter( 'nav_menu_css_class', 'hoot_nav_menu_css_class', 5, 2 );

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type. Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but
 * they're not registered.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
if ( !function_exists( 'hoot_add_post_type_support' ) ):
function hoot_add_post_type_support() {

	// Add support for excerpts to the 'page' post type.
	add_post_type_support( 'page', array( 'excerpt' ) );

	// Add thumbnail support for audio and video attachments.
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );

}
endif;

/**
 * This function enables Editor for 'Posts Page'.
 * This content can thus be used as description by hoot_archive_description_filter()
 * hooked into 'get_the_archive_description' filter
 *
 * @since 3.0.0
 * @access public
 * @param object $post
 * @return void
 */
if ( !function_exists( 'hoot_add_page_for_posts_editor' ) ):
function hoot_add_page_for_posts_editor( $post ) {

	// Check if this is 'page_for_posts' (blog)
	if ( get_option( 'page_for_posts' ) != $post->ID )
		return;

	remove_action( 'edit_form_after_title', '_wp_posts_page_notice' );
	add_post_type_support( $post->post_type, 'editor' );

}
endif;

/**
 * Filters 'get_the_archive_title' to add better archive titles than core.
 *
 * @since 3.0.0
 * @access public
 * @param string $title
 * @return string
 */
if ( !function_exists( 'hoot_archive_title_filter' ) ):
function hoot_archive_title_filter( $title ) {

	if ( is_home() && ! is_front_page() )
		$title = get_post_field( 'post_title', get_queried_object_id() );

	elseif ( is_search() )
		/* Translators: %s is search query */
		$title = sprintf( esc_html__( 'Search results for: %s', 'magazine-news-byte' ), get_search_query() );

	elseif ( function_exists( 'bbp_is_single_user' ) && bbp_is_single_user() )
		$title = __( 'User Profile ', 'magazine-news-byte' );

	/* If the current page is a paged page. */
	if ( ( ( $page = get_query_var( 'paged' ) ) || ( $page = get_query_var( 'page' ) ) ) && $page > 1 ) {
		$paged = number_format_i18n( absint( $page ) );
		/* Translators: %s is page number */
		$suffix = ' <span class="loop-title-suffix loop-title-paged">' . sprintf( __( '(Page %s)', 'magazine-news-byte' ), $paged ) . '</span>';
		$suffix = apply_filters( 'hoot_archive_title_paged', $suffix, $paged );
		$title .= $suffix;
	}

	return $title;

}
endif;

/**
 * Filters 'get_the_archve_description' to add better archive descriptions than core.
 *
 * @since 3.0.0
 * @access public
 * @param string $desc
 * @return string
 */
if ( !function_exists( 'hoot_archive_description_filter' ) ):
function hoot_archive_description_filter( $desc ) {

	if ( is_home() && ! is_front_page() )
		$desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );
	elseif ( function_exists( 'bbp_is_forum_archive' ) && bbp_is_forum_archive() )
		$desc = '';

	return ( ( !empty( $desc ) ) ? do_shortcode( wp_kses_post( wpautop( $desc ) ) ) : '' );
}
endif;

/**
 * The WordPress.org theme review requires that a link be provided to the single post page for untitled
 * posts. This is a filter on 'the_title' so that an '(Untitled)' title appears in that scenario, allowing
 * for the normal method to work.
 *
 * @since 3.0.0
 * @access public
 * @param string $title
 * @return string
 */
if ( !function_exists( 'hoot_untitled_post' ) ):
function hoot_untitled_post( $title ) {
	// Translators: Used as a placeholder for untitled posts on non-singular views.
	if ( ! $title && ! is_singular() && in_the_loop() && ! is_admin() )
		$title = esc_html__( '(Untitled)', 'magazine-news-byte' );

	return $title;
}
endif;

/**
 * Filters the excerpt more output with internationalized text and a link to the post.
 *
 * @since 3.0.0
 * @access public
 * @param string $more
 * @return string
 */
if ( !function_exists( 'hoot_readmore' ) ):
function hoot_readmore( $more = '[&hellip;]' ) {
	if ( is_admin() )
		return $more;
	elseif ( is_feed() )
		return apply_filters( 'hoot_rssreadmoretext', '' );

	$read_more = apply_filters( 'hoot_readmoretext', $more );
	global $post;
	$read_more = '<span class="more-link"><a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( $read_more ) . '</a></span>';

	return wp_kses_post( apply_filters( 'hoot_readmore', $read_more ) );
}
endif;

/**
 * Always display the 'Read More' link in Excerpts (even when excerpt is actually smaller than excerpt length)
 * Insert quicktag to be replaced later in 'wp_trim_excerpt()'
 *
 * @since 3.0.0
 * @access public
 * @param string $more
 * @return string
 */
if ( !function_exists( 'hoot_readmore_quicktag' ) ):
function hoot_readmore_quicktag( $more = '' ) {
	if ( is_admin() )
		return $more;
	return '<!--hoot-read-more-quicktag-->';
}
endif;

/**
 * Always display the 'Read More' link in Excerpts (even when excerpt is actually smaller than excerpt length)
 * Replace quicktag with read more link
 *
 * @since 3.0.0
 * @access public
 * @param string $text
 * @param string $raw_excerpt
 * @return string
 */
if ( !function_exists( 'hoot_readmore_quicktag_replace' ) ):
function hoot_readmore_quicktag_replace( $text, $raw_excerpt ) {
	if ( is_admin() )
		return $text;
	$read_more = hoot_readmore();
	$text = str_replace( '<!--hoot-read-more-quicktag-->', '', $text );
	return $text . $read_more;

}
endif;

/**
 * Wraps the output of 'wp_link_pages()' with '<p class="page-links">' if it's simply wrapped in a
 * '<p>' tag.
 *
 * @since 3.0.0
 * @access public
 * @param array $args
 * @return array
 */
if ( !function_exists( 'hoot_link_pages_args' ) ):
function hoot_link_pages_args( $args ) {
	$args['before'] = str_replace( '<p>', '<p class="page-links">', $args['before'] );
	return $args;
}
endif;

/**
 * Wraps page "links" that aren't actually links (just text) with '<span class="page-numbers">' so that they
 * can also be styled. This makes 'wp_link_pages()' consistent with the output of 'paginate_links()'.
 *
 * @since 3.0.0
 * @access public
 * @param string $link
 * @return string
 */
if ( !function_exists( 'hoot_link_pages_link' ) ):
function hoot_link_pages_link( $link ) {
	return 0 !== strpos( $link, '<a' ) ? "<span class='page-numbers'>{$link}</span>" : $link;
}
endif;

/**
 * Adds microdata to the author posts link.
 *
 * @since 3.0.0
 * @access public
 * @param string $link
 * @return string
 */
if ( !function_exists( 'hoot_the_author_posts_link' ) ):
function hoot_the_author_posts_link( $link ) {

	$pattern = array(
		"/(<a.*?)(>)/i",
		'/(<a.*?>)(.*?)(<\/a>)/i'
	);

	$replace = array(
		'$1 class="url fn n" itemprop="url"$2',
		'$1<span itemprop="name">$2</span>$3'
	);

	return preg_replace( $pattern, $replace, $link );
}
endif;

/**
 * Adds microdata to the comment author link.
 *
 * @since 3.0.0
 * @access public
 * @param string $link
 * @return string
 */
if ( !function_exists( 'hoot_get_comment_author_link' ) ):
function hoot_get_comment_author_link( $link ) {

	$pattern = array(
		'/(class=[\'"])(.+?)([\'"])/i',
		"/(<a.*?)(>)/i",
		'/(<a.*?>)(.*?)(<\/a>)/i'
	);

	$replace = array(
		'$1$2 fn n$3',
		'$1 itemprop="url"$2',
		'$1<span itemprop="name">$2</span>$3'
	);

	return preg_replace( $pattern, $replace, $link );
}
endif;

/**
 * Adds microdata to the comment author URL link.
 *
 * @since 3.0.0
 * @access public
 * @param string $link
 * @return string
 */
if ( !function_exists( 'hoot_get_comment_author_url_link' ) ):
function hoot_get_comment_author_url_link( $link ) {

	$pattern = array(
		'/(class=[\'"])(.+?)([\'"])/i',
		"/(<a.*?)(>)/i"
	);
	$replace = array(
		'$1$2 fn n$3',
		'$1 itemprop="url"$2'
	);

	return preg_replace( $pattern, $replace, $link );
}
endif;

/**
 * Adds microdata to avatars.
 *
 * @since 3.0.0
 * @access public
 * @param string $avatar
 * @return string
 */
if ( !function_exists( 'hoot_get_avatar' ) ):
function hoot_get_avatar( $avatar ) {
	return preg_replace( '/(<img.*?)(\/>)/i', '$1itemprop="image" $2', $avatar );
}
endif;

/**
 * Adds microdata to the post thumbnail HTML.
 *
 * @since 3.0.0
 * @access public
 * @param string $html
 * @return string
 */
if ( !function_exists( 'hoot_post_thumbnail_html' ) ):
function hoot_post_thumbnail_html( $html ) {
	return function_exists( 'get_the_image' ) ? $html : preg_replace( '/(<img.*?)(\/>)/i', '$1itemprop="image" $2', $html );
}
endif;

/**
 * Adds microdata to the comments popup link.
 *
 * @since 3.0.0
 * @access public
 * @param string $attr
 * @return string
 */
if ( !function_exists( 'hoot_comments_popup_link_attributes' ) ):
function hoot_comments_popup_link_attributes( $attr ) {
	return 'itemprop="discussionURL"';
}
endif;

/**
 * Adds a custom class to nav menu items that correspond to a post type archive.  The
 * 'menu-item-parent-archive' class is shown when viewing a single post of that belongs
 * to the given post type.
 *
 * @since 3.0.0
 * @access public
 * @param array $classes
 * @param object $item
 * @return array
 */
if ( !function_exists( 'hoot_nav_menu_css_class' ) ):
function hoot_nav_menu_css_class( $classes, $item ) {
	if ( 'post_type' === $item->type && is_singular( $item->object ) )
		$classes[] = 'menu-item-parent-archive';
	return $classes;
}
endif;