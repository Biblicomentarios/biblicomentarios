<?php
function scratch_register_styles()
{
    wp_enqueue_style('scratch-style', get_template_directory_uri() . '/style.css', array('scratch-bootstrap'), '1.0.0.0', 'all');
    wp_enqueue_style('scratch-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), '4.3.1', 'all');
    wp_enqueue_style('scratch-fontawesome', 'https://pro.fontawesome.com/releases/v5.10.0/css/all.css', array(), '10.0', 'all');
    wp_enqueue_style('gfonts-anton', 'https://fonts.googleapis.com/css2?family=Nunito&family=Montserrat&family=Prompt&family=Staatliches&display=swap', array(), '1.0', 'all');
}

add_action('wp_enqueue_scripts', 'scratch_register_styles');


function scratch_register_scripts()
{
    wp_enqueue_script('scratch-jquery', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', array(), '3.3.1', 'all', true);
    wp_enqueue_script('scratch-popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.14.7', 'all', true);
    wp_enqueue_script('scratch-bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '4.3.1', 'all', true);
}

add_action('wp_enqueue_scripts', 'scratch_register_scripts');

function scratch_supports()
{
    add_theme_support('title-tag');
    add_theme_support('custom-logo', array(
        'height' => 480,
        'width'  => 720,
    ));

    add_theme_support('post-thumbnails');

    post_type_supports('post', 'title');
}

add_action('after_setup_theme', 'scratch_supports');

function bc_image_sizes()
{
    add_image_size('archive-thumbnail', 200, 120, true);
}

add_action('init', 'bc_image_sizes');

function scratch_register_menus()
{
    register_nav_menus(
        array(
            'top-menu' => __('Top Menu'),
            'at-menu' => __('Antiguo Testamento'),
            'nt-menu' => __('Nuevo Testamento'),
            'lm-menu' => __('Libro de Mormón'),
            'dc-menu' => __('Doctrina y Convenios'),
            'pgp-menu' => __('Perla de Gran Precio')
        )
    );
}
add_action('init', 'scratch_register_menus');

function register_navwalker()
{
    require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
}
add_action('after_setup_theme', 'register_navwalker');

function bootstrap_pagination(\WP_Query $wp_query = null, $echo = true, $params = [])
{
    if (null === $wp_query) {
        global $wp_query;
    }

    $add_args = [];

    //add query (GET) parameters to generated page URLs
    /*if (isset($_GET[ 'sort' ])) {
        $add_args[ 'sort' ] = (string)$_GET[ 'sort' ];
    }*/

    $pages = paginate_links(
        array_merge([
            'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format'       => '?paged=%#%',
            'current'      => max(1, get_query_var('paged')),
            'total'        => $wp_query->max_num_pages,
            'type'         => 'array',
            'show_all'     => false,
            'end_size'     => 3,
            'mid_size'     => 1,
            'prev_next'    => true,
            'prev_text'    => __('« Anterior'),
            'next_text'    => __('Siguiente »'),
            'add_args'     => $add_args,
            'add_fragment' => ''
        ], $params)
    );

    if (is_array($pages)) {
        //$current_page = ( get_query_var( 'paged' ) == 0 ) ? 1 : get_query_var( 'paged' );
        $pagination = '<div class="pagination"><ul class="pagination">';

        foreach ($pages as $page) {
            $pagination .= '<li class="page-item' . (strpos($page, 'current') !== false ? ' active' : '') . '"> ' . str_replace('page-numbers', 'page-link', $page) . '</li>';
        }

        $pagination .= '</ul></div>';

        if ($echo) {
            echo $pagination;
        } else {
            return $pagination;
        }
    }

    return null;
}

// Define path and URL to the ACF plugin.
define('MY_ACF_PATH', get_stylesheet_directory() . '/includes/acf/');
define('MY_ACF_URL', get_stylesheet_directory_uri() . '/includes/acf/');

// Include the ACF plugin.
include_once(MY_ACF_PATH . 'acf.php');

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'my_acf_settings_url');
function my_acf_settings_url($url)
{
    return MY_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', 'my_acf_settings_show_admin');
function my_acf_settings_show_admin($show_admin)
{
    return true;
}

function scratch_widgets_init()
{
    register_sidebar(array(
        'name'          => __('Primary Sidebar', 'scratch'),
        'id'            => 'sidebar-primary',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}

add_action('widgets_init', 'scratch_widgets_init');

/**
 * Echo or return a formatted list of breadcrumbs.
 *
 * @param  array  $args An array of arguments to controll the output of the 
 *                      function.
 * @return string       The breadcrumb list.
 */
function get_breadcrumbs($args = array())
{
    global $post;

    if (is_string($args)) {
        parse_str($args, $args);
    }

    // Set up defaults.
    $defaults = array(
        'separator'   => ' &gt; ',
        'linkFinal'   => false,
        'echo'        => true,
        'printOnHome' => true,
        'before'      => '',
        'after'       => '',
    );

    // Merge the defaults with the parameters.
    $options = array_merge($defaults, (array)$args);

    // Initialise the trail with the current post.
    $trail = array($post);

    // Initialise the output.
    $output = '';

    $currentCategory = 0;

    if (is_front_page() == true && $options['printOnHome'] == false) {
        /**
         * If this is the front page and the option to prevent priting on the
         * home page is disabled then echo or return the empty string depending
         * on the echo option.
         */
        if ($options['echo'] == true) {
            echo $output;
        }
        return $output;
    }

    if (is_page()) {
        // If the current page is a page.
        $parent = $post;
        while ($parent->post_parent) {
            $parent = get_post($parent->post_parent);
            array_unshift($trail, $parent);
        }
    } elseif (is_category()) {
        // The current page is a category page.
        $trail = array();
        $currentCategory = get_query_var('cat');
        $category        = get_category($currentCategory);
        while ($category->category_parent > 0) {
            array_unshift($trail, $category);
            $category = get_category($category->category_parent);
        }
        // Add the final category to the trail.
        array_unshift($trail, $category);
    } else {
        // The current page will be a post or set of posts.
        $path = explode('/', $_SERVER['REQUEST_URI']);
        $path = array_filter($path);
        while (count($path) > 0) {
            $page = get_page_by_path(implode('/', $path));
            if ($page != NULL) {
                array_unshift($trail, $page);
            }
            array_pop($path);
        }

        if (count($trail) == 1) {
            // No pages found in path, try using categories.
            $category = get_the_category();
            $category = $category[0];
            while ($category->category_parent > 0) {
                array_unshift($trail, $category);
                $category = get_category($category->category_parent);
            }
            array_unshift($trail, $category);
        }
    }

    $show_on_front = get_option('show_on_front');
    if ('posts' == $show_on_front) {
        // The home page is a list of posts so just call it Home.
        $output .= '<li class="breadcrumb-item" id="breadcrumb-0"><a href="' . get_bloginfo('home') . '" title="Home">Inicio</a></li>' . "\n"; // home page link
    } else {
        // Otherwise the front page is a page so get the page name.
        $page_on_front = get_option('page_on_front');
        $home_page = get_post($page_on_front);
        $output .= '<li class="breadcrumb-item" id="breadcrumb-' . $home_page->ID . '"><a href="' . get_bloginfo('home') . '" title="' . $home_page->post_title . '">' . $home_page->post_title . '</a></li>' . "\n"; // home page link
        if ($trail[0]->ID == $page_on_front) {
            array_shift($trail);
        }
    }

    if (is_front_page() == false) {
        // If we aren't on the home page then construct the output. 
        foreach ($trail as $key => $page) {
            // Every item in the trail will be either a post/page object or a category.
            if (count($trail) - 1 == $key && $options['linkFinal'] == false) {
                // If we are on the last page and the option to link the final link is true.
                if (isset($page->post_title)) {
                    $output .= '<li class="breadcrumb-item" id="breadcrumb-' . $page->ID . '">' . $options['separator'] . ' ' . $page->post_title . '</li>' . "\n";
                } elseif (isset($page->cat_name)) {
                    $output .= '<li class="breadcrumb-item" id="breadcrumb-cat-' . $page->term_id . '">' . $options['separator'] . ' ' . $page->cat_name . '</li>' . "\n";
                }
            } else {
                // Create the link to the page or category
                if (isset($page->post_title)) {
                    $output .= '<li class="breadcrumb-item" id="breadcrumb-' . $page->ID . '">' . $options['separator'] . '<a href="' . get_page_link($page->ID) . '" title="' . $page->post_title . '">' . $page->post_title . '</a></li>' . "\n";
                } elseif (isset($page->cat_name)) {
                    $output .= '<li class="breadcrumb-item" id="breadcrumb-cat-' . $page->term_id . '">' . $options['separator'] . '<a href="' . get_category_link($page->term_id) . '" title="' . $page->cat_name . '">' . $page->cat_name . '</a></li>' . "\n";
                }
            }
        }
    }

    // Finish off the html of the ul
    $output = "<ul class='breadcrumb'>\n" . $output . "</ul>\n";

    // Add other elements
    $output = $options['before'] . $output .  $options['after'];

    if ($options['echo'] == true) {
        // Print out the $output variable.
        echo $output;
    }
    // Return 
    return $output;
}
