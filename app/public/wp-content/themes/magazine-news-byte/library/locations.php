<?php
/**
 * Functions for loading template parts. These functions are helper functions more flexible 
 * than what core WordPress currently offers with template part loading.
 *
 * @credit https://github.com/justintadlock/hybrid-core/blob/master/inc/template.php 
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Loads a post content template based off the post type and/or the post format. This functionality is 
 * not feasible with the WordPress get_template_part() function, so we have to rely on some custom logic 
 * and locate_template().
 *
 * Note that using this function assumes that you're creating a content template to handle attachments. 
 * This filter must be removed since we're bypassing the WP template hierarchy and focusing on templates 
 * specific to the content.
 *
 * @since 3.0.0
 * @access public
 * @param deprecated
 * @param bool|string $load
 * @return string
 */

if ( !function_exists( 'hoot_get_content_template' ) ) :
function hoot_get_content_template( $deprecated = '', $load = true ) {

	/* Set up an empty array and get the post type. */
	$templates = array();
	$post_type = get_post_type();

	/* Assume the theme is creating an attachment template. */
	if ( 'attachment' === $post_type ) {
		remove_filter( 'the_content', 'prepend_attachment' );

		$mime_type = get_post_mime_type();

		list( $type, $subtype ) = false !== strpos( $mime_type, '/' ) ? explode( '/', $mime_type ) : array( $mime_type, '' );

		$templates[] = "content-attachment-{$type}.php";
		$templates[] = "template-parts/content-attachment-{$type}.php";
		$templates[] = "content-attachment.php";
		$templates[] = "template-parts/content-attachment.php";
	}

	/* If the post type supports 'post-formats', get the template based on the format. */
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		/* Get the post format. */
		$post_format = get_post_format() ? get_post_format() : 'standard';

		/* Template based off post type and post format. */
		$templates[] = "content-{$post_type}-{$post_format}.php";
		$templates[] = "template-parts/content-{$post_type}-{$post_format}.php";

		/* Template based off the post format. */
		$templates[] = "content-{$post_format}.php";
		$templates[] = "template-parts/content-{$post_format}.php";
	}

	/* Template based off the post type. */
	$templates[] = "content-{$post_type}.php";
	$templates[] = "template-parts/content-{$post_type}.php";

	/* Fallback 'content.php' template. */
	$templates[] = 'content.php';
	$templates[] = 'template-parts/content.php';

	/* Allow devs to filter the content template hierarchy. */
	$templates = apply_filters( 'hoot_content_template_hierarchy', $templates, $post_type );

	/* Return array */
	if ( $load === 'array' )
		return $templates;

	/* Locate the content template. */
	$customtemplate = apply_filters( 'hoot_content_template', false, $post_type );
	$template = ( is_string( $customtemplate ) ) ? $customtemplate : locate_template( $templates );

	/* Load / Return the template */
	if ( $load )
		require( $template );
	else
		return $template;
}
endif;

/**
 * A function for loading a archive post template. This works similar to the WordPress 'get_*()' template functions. 
 * It's purpose is for loading a archive post template part.
 * The templates are saved in static variable, so each template is only located once if it is needed.
 *
 * @since 3.0.0
 * @access public
 * @param string $name
 * @param bool|string $load
 * @return void
 */
if ( !function_exists( 'hoot_get_archive_content' ) ) :
function hoot_get_archive_content( $name = null, $load = true ) {

	/* Get default values */
	if ( empty( $name ) )
		$name = hoot_get_mod( 'archive_type' );
	$nameid = ( empty( $name ) ) ? 'default' : $name;
	$post_type = get_post_type();

	/* Store template locations */
	static $archive_templates = array();

	/* Create an array of template files to look for. */
	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "archive-{$post_type}-{$name}.php";                // Not recommended in theme to allow easy child theme customization
		$templates[] = "template-parts/archive-{$post_type}-{$name}.php";
		$templates[] = "archive-{$name}.php";                             // Not recommended in theme to allow easy child theme customization
		$templates[] = "template-parts/archive-{$name}.php";
	}

	$templates[] = "archive-{$post_type}.php";                            // Not recommended in theme to allow easy child theme customization
	$templates[] = "template-parts/archive-{$post_type}.php";
	// $templates[] = 'archive.php';                                      // conflict
	// $templates[] = 'template-parts/archive.php';                       // for brevity

	// Allow devs to filter the template hierarchy.
	$templates = apply_filters( 'hoot_archive_post_template_hierarchy', $templates, $name, $post_type );

	/* Return array */
	if ( $load === 'array' )
		return $templates;

	/* Check if a template has been provided for the specific archive type.  If not, get the template. */
	if ( ! isset( $archive_templates[ $post_type ][ $nameid ] ) ) {

		// Locate the archive template.
		$customtemplate = apply_filters( 'hoot_archive_template', false, $name, $post_type );
		$template = ( is_string( $customtemplate ) ) ? $customtemplate : locate_template( $templates );

		// Set the template location
		$archive_templates[ $post_type ][ $nameid ] = $template;

	}

	/* If a template was found, load/return the template. */
	if ( ! empty( $archive_templates[ $post_type ][ $nameid ] ) ) {
		if ( $load ) {
			require( $archive_templates[ $post_type ][ $nameid ] );
		} else {
			return $archive_templates[ $post_type ][ $nameid ];
		}
	}

}
endif;

/**
 * A function for loading a menu template. This works similar to the WordPress 'get_*()' template functions. 
 * It's purpose is for loading a menu template part.
 *
 * @since 3.0.0
 * @access public
 * @param string $name
 * @param bool|string $load
 * @return void
 */
if ( !function_exists( 'hoot_get_menu' ) ) :
function hoot_get_menu( $name = '', $load = true ) {

	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "menu-{$name}.php";
		$templates[] = "template-parts/menu-{$name}.php";
	}

	$templates[] = 'menu.php';
	$templates[] = 'template-parts/menu.php';

	$templates = apply_filters( 'hoot_menu_template_hierarchy', $templates, $name );

	/* Return array */
	if ( $load === 'array' )
		return $templates;

	/* Load / Return the template */
	if ( $load )
		locate_template( $templates, true, false );
	else
		return locate_template( $templates );
}
endif;

/**
 * This is a replacement function for the WordPress 'get_header()' function. The reason for this function 
 * over the core function is because the core function does not provide the functionality needed to properly 
 * implement what's needed, particularly the ability to add header templates to a sub-directory. 
 * Technically, there's a workaround for that using the 'get_header' hook, but it requires keeping a 
 * an empty 'header.php' template in the theme's root, which will get loaded every time a header template 
 * gets loaded. That's kind of nasty hack, which leaves us with this function. This is the **only** 
 * clean solution currently possible.
 *
 * This function maintains compatibility with the core 'get_header()' function. It does so in two ways: 
 * 1) The 'get_header' hook is properly fired and 2) The core naming convention of header templates 
 * ('header-$name.php' and 'header.php') is preserved and given a higher priority than custom templates.
 *
 * @link http://core.trac.wordpress.org/ticket/15086
 * @link http://core.trac.wordpress.org/ticket/18676
 *
 * @since 3.0.0
 * @access public
 * @param string $name
 * @param bool|string $load
 * @return void
 */
if ( !function_exists( 'hoot_get_header' ) ) :
function hoot_get_header( $name = null, $load = true ) {

	do_action( 'get_header', $name ); // Core WordPress hook

	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "header-{$name}.php";
		$templates[] = "template-parts/header-{$name}.php";
	}

	$templates[] = 'header.php';
	$templates[] = 'template-parts/header.php';

	$templates = apply_filters( 'hoot_header_template_hierarchy', $templates, $name );

	/* Return array */
	if ( $load === 'array' )
		return $templates;

	/* Load / Return the template */
	if ( $load )
		locate_template( $templates, true, false );
	else
		return locate_template( $templates );
}
endif;

/**
 * A function for loading a comment template. This works similar to the WordPress 'get_*()' template functions. 
 * It's purpose is for loading a comment template part.
 * The templates are saved in static variable, so each template is only located once if it is needed.
 *
 * @since 3.0.0
 * @access public
 * @param string
 * @param bool|string $load
 * @return array
 */
if ( !function_exists( 'hoot_get_comment' ) ) :
function hoot_get_comment( $comment_type, $load = true ) {

	/* Store template locations */
	static $comment_templates = array();

	/* Create an array of template files to look for. */
	$templates = array(
		"comment-{$comment_type}.php",
		"template-parts/comment-{$comment_type}.php",
	);
	// If the comment type is a 'pingback' or 'trackback', allow the use of 'comment-ping.php'.
	if ( 'pingback' == $comment_type || 'trackback' == $comment_type ) {
		$templates[] = 'comment-ping.php';
		$templates[] = 'template-parts/comment-ping.php';
	}
	// Add the fallback 'comment.php' template.
	$templates[] = 'comment.php';
	$templates[] = 'template-parts/comment.php';
	// Allow devs to filter the template hierarchy.
	$templates = apply_filters( 'hoot_comment_template_hierarchy', $templates, $comment_type );

	/* Return array */
	if ( $load === 'array' )
		return $templates;

	/* Check if a template has been provided for the specific comment type.  If not, get the template. */
	if ( ! isset( $comment_templates[ $comment_type ] ) ) {

		// Locate the comment template.
		$template = locate_template( $templates );

		// Set the template location
		$comment_templates[ $comment_type ] = $template;

	}

	/* If a template was found, load/return the template. */
	if ( ! empty( $comment_templates[ $comment_type ] ) ) {
		if ( $load ) {
			require( $comment_templates[ $comment_type ] );
		} else {
			return $comment_templates[ $comment_type ];
		}
	}

}
endif;

/**
 * This is a replacement function for the WordPress 'get_sidebar()' function. The reason for this function 
 * over the core function is because the core function does not provide the functionality needed to properly 
 * implement what's needed, particularly the ability to add sidebar templates to a sub-directory. 
 * Technically, there's a workaround for that using the 'get_sidebar' hook, but it requires keeping a 
 * an empty 'sidebar.php' template in the theme's root, which will get loaded every time a sidebar template 
 * gets loaded. That's kind of nasty hack, which leaves us with this function. This is the **only** 
 * clean solution currently possible.
 *
 * This function maintains compatibility with the core 'get_sidebar()' function. It does so in two ways: 
 * 1) The 'get_sidebar' hook is properly fired and 2) The core naming convention of sidebar templates 
 * ('sidebar-$name.php' and 'sidebar.php') is preserved and given a higher priority than custom templates.
 *
 * @link http://core.trac.wordpress.org/ticket/15086
 * @link http://core.trac.wordpress.org/ticket/18676
 *
 * @since 3.0.0
 * @access public
 * @param string $name
 * @param bool|string $load
 * @return void
 */
if ( !function_exists( 'hoot_get_sidebar' ) ) :
function hoot_get_sidebar( $name = null, $load = true ) {

	do_action( 'get_sidebar', $name ); // Core WordPress hook

	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "sidebar-{$name}.php";
		$templates[] = "template-parts/sidebar-{$name}.php";
	}

	$templates[] = 'sidebar.php';
	$templates[] = 'template-parts/sidebar.php';

	$templates = apply_filters( 'hoot_sidebar_template_hierarchy', $templates, $name );

	/* Return array */
	if ( $load === 'array' )
		return $templates;

	/* Load / Return the template */
	if ( $load )
		locate_template( $templates, true, false );
	else
		return locate_template( $templates );

}
endif;

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

/**
 * This is a replacement function for the WordPress 'get_footer()' function. The reason for this function 
 * over the core function is because the core function does not provide the functionality needed to properly 
 * implement what's needed, particularly the ability to add footer templates to a sub-directory. 
 * Technically, there's a workaround for that using the 'get_footer' hook, but it requires keeping a 
 * an empty 'footer.php' template in the theme's root, which will get loaded every time a footer template 
 * gets loaded. That's kind of nasty hack, which leaves us with this function. This is the **only** 
 * clean solution currently possible.
 *
 * This function maintains compatibility with the core 'get_footer()' function. It does so in two ways: 
 * 1) The 'get_footer' hook is properly fired and 2) The core naming convention of footer templates 
 * ('footer-$name.php' and 'footer.php') is preserved and given a higher priority than custom templates.
 *
 * @link http://core.trac.wordpress.org/ticket/15086
 * @link http://core.trac.wordpress.org/ticket/18676
 *
 * @since 3.0.0
 * @access public
 * @param string $name
 * @param bool|string $load
 * @return void
 */
if ( !function_exists( 'hoot_get_footer' ) ) :
function hoot_get_footer( $name = null, $load = true ) {

	do_action( 'get_footer', $name ); // Core WordPress hook

	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "footer-{$name}.php";
		$templates[] = "template-parts/footer-{$name}.php";
	}

	$templates[] = 'footer.php';
	$templates[] = 'template-parts/footer.php';

	$templates = apply_filters( 'hoot_footer_template_hierarchy', $templates, $name );

	/* Return array */
	if ( $load === 'array' )
		return $templates;

	/* Load / Return the template */
	if ( $load )
		locate_template( $templates, true, false );
	else
		return locate_template( $templates );
}
endif;

/**
 * This function is an extension of 'get_template_part' in a way that it allows $load option to
 * be passed to 'locate_template' (something that WordPress doesnt support currently)
 *
 * @link https://core.trac.wordpress.org/browser/tags/4.9/src/wp-includes/general-template.php#L134
 *
 * @since 3.0.0
 * @access public
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 * @param bool|string $load
 * @return void
 */
if ( !function_exists( 'hoot_get_template_part' ) ) :
function hoot_get_template_part( $slug, $name = null, $load = true ) {

	do_action( "get_template_part_{$slug}", $slug, $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "{$slug}-{$name}.php";
		$templates[] = "template-parts/{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";
	$templates[] = "template-parts/{$slug}.php";

	$templates = apply_filters( 'hoot_get_template_hierarchy', $slug, $name );

	/* Return array */
	if ( $load === 'array' )
		return $templates;

	/* Load / Return the template */
	if ( $load )
		locate_template( $templates, true, false );
	else
		return locate_template( $templates );
}
endif;