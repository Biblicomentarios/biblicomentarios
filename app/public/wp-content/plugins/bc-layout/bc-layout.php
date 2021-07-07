<?php
/*
Plugin Name: BC Layout
Description: Modificaciones de layout para Biblicomentarios.
Author: JPMarichal
Version: 1.0.0
Author URI: https://biblicomentarios.com/
Last Updated: 22 May 2021
License: Private
Text Domain: biblicomentarios
*/
/* ============================================================= */
// Breadcrumbs en categor?a
if (is_category() && is_object_in_taxonomy(get_post_type(), 'category')) {
    echo 'Es categor?a';
    $cats = wp_get_object_terms(
        get_the_ID(),
        'category',
        array('fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC',)
    );
    $cats = wp_get_object_terms(
        get_the_ID(),
        'category'
    );
    $parent_id = array_shift($cats);
    $parent = get_category($parent_id);

    $grandparents = get_category_parents($parent_id, false);
    $grandparentArray = explode("/", $grandparents);
    $grandparent_name = strip_tags(array_slice($grandparentArray, -3)[0]);
    $grandparent_id = get_cat_ID($grandparent_name);

    echo '<h3 class="widget-title title-customstyle has-custom-style"><span class="accent-typo">Navega ' . $grandparent_name . '</span></h3>';
    wp_list_categories(array(
        'title_li' => '',
        'taxonomy'   => 'category',
        'show_count' => 0,
        'child_of'   => $grandparent_id,
        'style'      => 'list',
        'hide_empty' => 0, // include empty categories
        'depth'      => 1, // and up to 3 levels depth
    ));
}

// on category page
elseif (is_category()) {
    echo 'Es categor?a';
}


// Widget Class ==============================
class WPSE154979_Widget extends WP_Widget
{
    function WPSE154979_Widget()
    {
        $widget_ops = array(
            'classname'     => 'WPSE154979_custom_widget',
            'description'   => __('Post Category Children\'s or Parent Categories')
        );
        $control_ops = array('width' => 200, 'height' => 400);
        $this->WP_Widget('WPSE154979_custom', 'Custom Categories', $widget_ops, $control_ops);
        $this->alt_option_name = 'WPSE154979_custom';
    }


    function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

        echo $before_widget;
        if ($title)
            echo $before_title . $title . $after_title;

        // on single post page
        if (is_single() && is_object_in_taxonomy(get_post_type(), 'category')) {
            /*
            $cats = wp_get_object_terms( 
                get_the_ID(), 
                'category', 
                array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC',  ) 
            );
            */
            $cats = wp_get_object_terms(
                get_the_ID(),
                'category',
                array('fields' => 'ids', 'orderby' => 'meta_value_num', 'order' => 'DESC',)
            );
            $parent_id = array_shift($cats);
            $parent = get_category($parent_id);

            $grandparents = get_category_parents($parent_id, false);
            $grandparentArray = explode("/", $grandparents);
            $grandparent_name = strip_tags(array_slice($grandparentArray, -3)[0]);
            $grandparent_id = get_cat_ID($grandparent_name);

            echo '<h3 class="widget-title title-customstyle has-custom-style"><span class="accent-typo">Navega ' . $grandparent_name . '</span></h3>';
            wp_list_categories(array(
                'title_li' => '',
                'taxonomy'   => 'category',
                'show_count' => 0,
                'child_of'   => $grandparent_id,
                'style'      => 'list',
                'hide_empty' => 0, // include empty categories
                'depth'      => 1, // and up to 3 levels depth
            ));

            $temas = get_the_tags(get_the_ID());
            if ($temas) {
                echo '<h3 class="widget-title title-customstyle has-custom-style"><span class="accent-typo">Temas asociados</span></h3>';
                echo '<ul>';
                foreach ($temas as $tema) {
                    echo '<li> <a href="/tag/' . $tema->slug . '">' . $tema->name . '</a></li> ';
                }
                echo '</ul>';
            }
        }

        // on category page
        elseif (is_category()) {
            $parent_id = (int) get_query_var('cat');
            $parent = get_category($parent_id);
            $parent_name = $parent->name;
            echo '<h3 class="widget-title title-customstyle has-custom-style"><span class="accent-typo">Navega '
                . $parent_name
                . '</span></h3>';
            $grandparents = get_category_parents($parent_id, false);
            $grandparentArray = explode("/", $grandparents);
            $grandparent_name = strip_tags(array_slice($grandparentArray, -3)[0]);
            $grandparent_id = get_cat_ID($grandparent_name);
            $grandparent = get_category($grandparent_id);
            $grandparent_link = get_category_link($grandparent_id);

            // ?Cu?ntas categor?as hija se obtienen?
            $childCategories = wp_list_categories('echo=0&title_li=&show_option_none=&hide_empty=0&parent=' . $parent_id);

            if ($grandparent_name != $parent_name) {
                if ($childCategories != '') {
                    echo '<b>' . $parent_name . '</b> es parte de <a href="' . $grandparent_link . '">' . $grandparent_name . '</a> y se compone de: <hr>';
                    wp_list_categories('title_li=&show_option_none=&hide_empty=0&parent=' . $parent_id);
                } else {
                    echo '<b>' . $parent_name . '</b> es parte de <a href="' . $grandparent_link . '">' . $grandparent_name . '</a>';
                }
            } else {
                if ($childCategories != '') {
                    echo '<b>' . $parent_name . '</b> se compone de: <hr>';
                    wp_list_categories('title_li=&show_option_none=&hide_empty=0&parent=' . $parent_id);
                } else {
                    echo '<b>' . $parent_name . '</b> es parte de <a href="' . $grandparent_link . '">' . $grandparent_name . '</a>';
                }
            }
        }

        // on others page
        else {
            $parent_id = 0;
            wp_list_categories('title_li=&show_option_none=&hide_empty=0&parent=' . $parent_id);
        }

        echo $after_widget;
    }
    function update($new_instance, $old_instance)
    {
        $instance                   = $old_instance;
        $instance['title']          = strip_tags($new_instance['title']);
        return $instance;
    }
    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : ''; ?><p>
            <strong><?php _e('Title:'); ?></strong>
            <br /><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p><?php
        }
    }


    // Register Widget ==============================
    add_action('widgets_init', 'WPSE154979_Widget_Init');
    function WPSE154979_Widget_Init()
    {
        register_widget('WPSE154979_Widget');
    }

    add_filter('get_terms_orderby', function ($orderby, $qv, $taxonomy) {
        // Only target the category taxonomy
        if ('category' !== $taxonomy)
            return $orderby;

        // Support orderby term_order
        if (isset($qv['orderby']) && 'term_order' === $qv['orderby'])
            $orderby = 't.term_order';

        return $orderby;
    }, 10, 3);

    //Registramos el tama?o
    function bc_half_thumbnail()
    {
        add_image_size('half-thumbnail', 100, 100, true);
    }
    add_action('after_setup_theme', 'bc_half_thumbnail');

    //Agregamos el tama?o a las opciones de las im?genes
    function bp_body_size_choose($sizes)
    {
        return array_merge($sizes, array(
            'half-thumbnail' => 'Thumbnail medio'
        ));
    }
    add_filter('image_size_names_choose', 'bp_body_size_choose');

    // Registra los blocks personalizados
    add_action('acf/init', 'my_acf_init_block_types');
    function my_acf_init_block_types()
    {

        // Check function exists.
        if (function_exists('acf_register_block_type')) {

            // Registra BCQuote
            acf_register_block_type(array(
                'name'              => 'bcquote',
                'title'             => __('BC Cita'),
                'description'       => __('Cita personalizada.'),
                'render_template'   => 'template-parts/blocks/bcquote/bcquote.php',
                'category'          => 'formatting',
                'icon'              => 'admin-comments',
                'keywords'          => array('bcquote', 'quote', 'cita'),
                'enqueue_style' => get_template_directory_uri() . '/template-parts/blocks/bcquote/bcquote.css',
            ));

            // Registra BCPasaje
            acf_register_block_type(array(
                'name'              => 'bcpasaje',
                'title'             => __('BC Pasaje'),
                'description'       => __('Pasaje de las escrituras.'),
                'render_template'   => 'template-parts/blocks/bcpasaje/bcpasaje.php',
                'category'          => 'formatting',
                'icon'              => 'book-alt',
                'keywords'          => array('bcpasaje', 'pasaje', 'referencia', 'quote', 'cita'),
                'enqueue_style' => get_template_directory_uri() . '/template-parts/blocks/bcpasaje/bcpasaje.css',
            ));

            // Registra BCHarmonySingle
            acf_register_block_type(array(
                'name'              => 'bcharmonysingle',
                'title'             => __('BC Armon?a simple'),
                'description'       => __('Armon?a de los evangelios simple.'),
                'render_template'   => 'template-parts/blocks/bcharmonysingle/bcharmonysingle.php',
                'category'          => 'formatting',
                'icon'              => 'book-alt',
                'keywords'          => array('bcharmonysingle', 'pasaje', 'referencia', 'quote', 'cita', 'armon?a de los evangelios'),
                'enqueue_style' => get_template_directory_uri() . '/template-parts/blocks/bcharmonysingle/bcharmonysingle.css',
            ));

            // Registra BCLadoALado
            acf_register_block_type(array(
                'name'              => 'bcladoalado',
                'title'             => __('BC Lado a lado'),
                'description'       => __('Comparaci?n de textos lado a lado.'),
                'render_template'   => 'template-parts/blocks/bcladoalado/bcladoalado.php',
                'category'          => 'formatting',
                'icon'              => 'book-alt',
                'keywords'          => array('bcladoalado', 'pasaje', 'referencia', 'quote', 'cita', 'armon?a de los evangelios'),
                'enqueue_style' => get_template_directory_uri() . '/template-parts/blocks/bcladoalado/bcladoalado.css',
            ));

            // Registra BCDiagrama
            acf_register_block_type(array(
                'name'              => 'bcdiagrama',
                'title'             => __('BC Diagrama'),
                'description'       => __('Diagrama o ilustraci?n.'),
                'render_template'   => 'template-parts/blocks/bcdiagrama/bcdiagrama.php',
                'category'          => 'formatting',
                'icon'              => 'book-alt',
                'keywords'          => array('bcdiagrama', 'pasaje', 'referencia', 'quote', 'cita', 'armon?a de los evangelios'),
                'enqueue_style' => get_template_directory_uri() . '/template-parts/blocks/bcdiagrama/bcdiagrama.css',
            ));

            // Registra BCListadoReferencias
            $blockNickName = 'bcreferencias';
            acf_register_block_type(array(
                'name'              => $blockNickName,
                'title'             => __('BC Referencias'),
                'description'       => __('Listado de referencias.'),
                'render_template'   => "template-parts/blocks/$blockNickName/$blockNickName.php",
                'category'          => 'formatting',
                'icon'              => 'book-alt',
                'keywords'          => array($blockNickName, 'pasaje', 'referencia'),
                'enqueue_style' => get_template_directory_uri() . "/template-parts/blocks/$blockNickName/$blockNickName.css",
            ));
        }
    }

    function bootstrapstarter_enqueue_styles()
    {
        wp_register_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
        $dependencies = array('bootstrap');
        wp_enqueue_style('bootstrapstarter-style', get_stylesheet_uri(), $dependencies);
    }

    function bootstrapstarter_enqueue_scripts()
    {
        // $dependencies = array('jquery');
        // wp_enqueue_script('bootstrap', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', $dependencies, '3.3.1', true);
        // wp_enqueue_script('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', $dependencies, '1.14.7', true);
        // wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', $dependencies, '4.3.1', true);
    }

    add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_styles');
    add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_scripts');

    // Shortcode to output custom PHP in Elementor
    function bc_shortcode_categoria_antes($atts)
    {
        $term = get_queried_object();
        $url = get_field('urlanterior', $term);
        if ($url) {
            echo "<a href='" . $url . "'><i class='fas fa-angle-double-left'></i> Anterior</a>";
        }
    }
    add_shortcode('bc_category_nav_previous', 'bc_shortcode_categoria_antes');

    function bc_shortcode_categoria_despues($atts)
    {
        $term = get_queried_object();
        $url = get_field('urlsiguiente', $term);
        if ($url) {
            echo "<a href='" . $url . "'>Siguiente <i class='fas fa-angle-double-right'></i></a>";
        }
    }
    add_shortcode('bc_category_nav_after', 'bc_shortcode_categoria_despues');

    /* Desactivar Heartbeat para el backend */
    // Desactiva el heartbeat para todo el dashboard
    add_action('init', 'stop_heartbeat', 1);
    function stop_heartbeat()
    {
        global $pagenow;
        if ($pagenow != 'post.php' && $pagenow != 'post-new.php')
            wp_deregister_script('heartbeat');
    }

    // Limita el heartbeat a 60 segundos
    function limit_heart( $settings ) {
        $settings['interval'] = 60; //Entre 15 y 60 segundos
        return $settings;
    }
    add_filter( 'heartbeat_settings', 'limit_heart' );
