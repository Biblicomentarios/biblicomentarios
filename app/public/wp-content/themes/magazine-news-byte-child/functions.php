<?php
function updatePages()
{
    $args = array();
    $pages = get_pages();

    foreach ($pages as $page) {
        if ($page->ID > 118568) {
            update_post_meta($page->ID, '_wp_page_template', 'page-evento.php');
            wp_set_post_categories($page->ID, array(2095));
        }
    }
}

// add_action('init','updatePages');
function pageEventoTitle()
{
    global $post;
    $contenido = $post->post_content_filtered;

    if (is_page_template('page-evento.php')) {
        $contenido = $post->post_content;
        return $contenido;
    }
    return $contenido;
}
 add_filter('the_content', 'pageEventoTitle');

function carga()
{
  //actualizarPaginas();
    if( null == get_page_by_title( 'Test Post 5', OBJECT, 'post' ) ) {
        wp_insert_post( 
            array(
            'post_title'    => 'Test Post 5',
            'post_name'     => 'test-post-5',
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_author'   => 1
            )
        );
    }
}
 // add_filter('init', 'carga');


