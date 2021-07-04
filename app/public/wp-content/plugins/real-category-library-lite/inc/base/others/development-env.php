<?php
/**
 * If you see this, you can completely ignore this file because
 * it is never loaded on your website!
 */

// internal
function rcl_cptui_register_my_cpts() {
    /**
     * Post Type: Movies.
     */

    $labels = [
        'name' => 'Movies',
        'singular_name' => 'Movie'
    ];

    $args = [
        'label' => 'Movies',
        'labels' => $labels,
        'description' => '',
        'public' => true,
        'publicly_queryable' => false,
        'show_ui' => true,
        'delete_with_user' => false,
        'show_in_rest' => false,
        'rest_base' => '',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'has_archive' => false,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'exclude_from_search' => false,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => true,
        'rewrite' => ['slug' => 'movie', 'with_front' => true],
        'query_var' => true,
        'supports' => ['title', 'editor', 'page-attributes']
    ];

    register_post_type('movie', $args);
}
add_action('init', 'rcl_cptui_register_my_cpts');

// internal
function rcl_cptui_register_my_taxes() {
    /**
     * Taxonomy: Genre.
     */

    $labels = [
        'name' => 'Genre',
        'singular_name' => 'Genre'
    ];

    $args = [
        'label' => 'Genre',
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => false,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'genre', 'with_front' => true],
        'show_admin_column' => false,
        'show_in_rest' => false,
        'rest_base' => 'genre',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit' => true
    ];
    register_taxonomy('genre', ['movie'], $args);

    /**
     * Taxonomy: Scenarios.
     */

    $labels = [
        'name' => 'Scenarios',
        'singular_name' => 'Scenario'
    ];

    $args = [
        'label' => 'Scenarios',
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => false,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'scenario', 'with_front' => true],
        'show_admin_column' => false,
        'show_in_rest' => false,
        'rest_base' => 'scenario',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit' => true
    ];
    register_taxonomy('scenario', ['movie'], $args);
}
add_action('init', 'rcl_cptui_register_my_taxes');
