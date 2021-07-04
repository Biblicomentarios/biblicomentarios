<?php
/*
Plugin Name: AB Carga Eventos
Description: Carga de eventos.
Author: JPMarichal
Version: 1.0.0
Author URI: https://biblicomentarios.com/
Last Updated: 22 May 2021
License: Private
Text Domain: biblicomentarios
*/
/* ============================================================= */


function pluginprefix_activate()
{
    agregarPagina(119757,10,'El faraón ordena a las parteras matar a los niños hebreos varones ');
    agregarPagina(119757,20,'Dios dota a las parteras de familias ');
    agregarPagina(119757,30,'El faraón ordena a los egipcios arrojar a los niños varones al Nilo');
    agregarPagina(119758,10,'Moisés es escondido durante tres meses ');
    agregarPagina(119758,20,'Moisés es escondido en una cesta ');
    agregarPagina(119758,30,'La hija del faraón encuentra a Moisés ');
    agregarPagina(119760,10,'Moisés huye a Madián ');
    agregarPagina(119760,20,'Moisés abreva el rebaño del sacerdote de Madián ');
    agregarPagina(119760,30,'Moisés se establece en Madián ');    

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'pluginprefix_activate');

function actualizarPaginas()
{
    for ($i = 118581; $i < 119179; $i++) {
        $tituloPagina = get_the_title($i);
        $postActual = get_post($i);
        $post_type = $postActual->post_type;
        $post_status = $postActual->post_status;
        $post_id = $postActual->ID;
        if ($post_type = 'post' && $post_status = 'publish' && $tituloPagina != '') {
            echo $tituloPagina . '|' . $i . '<br/>';
            $result = wp_update_post(
                array(
                    'ID' => $post_id,
                    //'comment_status' => 'open',
                    // 'ping_status'    => 'open',
                    // 'post_author'    => 1,
                    // 'post_name'      => strtolower(str_replace(' ', '-', trim($tituloPagina))),
                    'post_status'    => 'publish',
                    // 'page_template'  => 'page-evento.php'
                ),
                true,
                true
            );

            if (is_wp_error($result)) {
                wp_die('Post not saved');
            }
        }
        //if ($postActual->post_type = 'page') {            
        //     echo $tituloPagina.'|'.$i.'<br/>';
        // $result = wp_update_post(
        //     array(
        //         'ID' => $i,
        //         //'comment_status' => 'open',
        //         // 'ping_status'    => 'open',
        //         // 'post_author'    => 1,
        //         // 'post_name'      => strtolower(str_replace(' ', '-', trim($tituloPagina))),
        //          'post_status'    => 'publish',
        //         // 'page_template'  => 'page-evento.php'
        //     )
        //     ,true
        //     ,true
        // );

        // if (is_wp_error($result)){
        //     wp_die('Post not saved');
        // }
        //    }else{
        //        echo $tituloPagina.' No es una página - '.$postActual->post_type.'<br/>';
        //    }
    }
    // $page_id = wp_update_post(
    //     array(
    //         'comment_status' => 'open',
    //         'ping_status'    => 'open',
    //         'post_author'    => 1,
    //         'post_name'      => strtolower(str_replace(' ', '-', trim($tituloPagina))),
    //         'post_status'    => 'publish',
    //         'post_type'      => 'page',
    //         'page_template'  => 'page-evento.php'
    //     )
    //     ,false,false
    // );

    // wp_set_post_terms($page_id, array(2095), 'category', true);
}

function agregarPagina($idParent, $orden, $tituloPagina)
{
    $check_page_exist = get_page_by_title($tituloPagina, 'OBJECT', 'page');
    // Check if the page already exists
    if (empty($check_page_exist)) {
        $page_id = wp_insert_post(
            array(
                'comment_status' => 'open',
                'ping_status'    => 'open',
                'post_author'    => 1,
                'post_title'     => $tituloPagina,
                'post_name'      => strtolower(str_replace(' ', '-', trim($tituloPagina))),
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_parent'    => $idParent,
                'menu_order'    => $orden,
                'page_template'  => 'page-evento.php'
            )
        );

        wp_set_post_terms($page_id, array(2095), 'category', true);
    }
}
