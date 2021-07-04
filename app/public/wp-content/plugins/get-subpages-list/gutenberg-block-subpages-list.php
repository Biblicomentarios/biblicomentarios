<?php
/**
 * Plugin Name: Get Subpages List
 * Description: Gutenberg block gets child pages list of specific page
 * Author: Umang Bhanvadia
 * Version: 1.0.1
 * License: GPL2+
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the block's assets for the editor.
 *
 * wp-blocks:  The registerBlockType() function to register blocks.
 * wp-element: The wp.element.createElement() function to create elements.
 * wp-i18n:    The __() function for internationalization.
 *
 * @since 1.0.0
 */


function gutenberg_dynamic_render_callback( $attributes ){
    $attributes['pageID'] = 1;
    if ( 0 !== intval($attributes['pageID']) ) {

        $post_parent = isset( $attributes['pageID'] ) ? $attributes['pageID'] : '';

       // $the_query = new WP_Query( 'post_type=page&posts_per_page=-1&orderby=menu_order&order=0&post_parent='.$post_parent );
        $the_query = new WP_Query(
            array(
                'post_parent' => get_the_ID(),
                'post_type'=> 'page',
                'posts_per_page' => -1,
                'orderby'=> 'menu_order',
                'order' => 'ASC'
            )
        );
        ob_start(); ?>
        <div id="blog-page-main">
            <ul>
                <?php
                if ( $the_query->have_posts() ) :
                while ( $the_query->have_posts() ) : $the_query->the_post();
                    ?>
                    <li>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </li>
                <?php
                endwhile;
                wp_reset_postdata(); ?>
            </ul>
            <?php
            else:
                ?>
                <div class="blog-content">
                    <h3>No Child Pages</h3>
                </div>
            <?php
            endif; ?>
        </div>
        <?php
        $html = ob_get_clean();
        return $html;
    }else{
        ob_start(); ?>
        <div id="blog-page-main">
            <h3>Please Select Page</h3>
        </div>
        <?php
        $html = ob_get_clean();
        return $html;
    }

}

function subpages_block_jsx_example_backend_enqueue() {
	wp_enqueue_script(
		'dynamic-block-subpages', // Unique handle.
		plugins_url( 'js/block.build.js', __FILE__ ), // block.js: We register the block here.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-core-data' ) // Dependencies, defined above.
	);
    wp_enqueue_style(
        'dnm-block-css', // Unique handle.
        plugins_url( 'css/styles.css', __FILE__ ), // block.js: We register the block here.
        array() // Dependencies, defined above.
    );


    register_block_type('mdlr/dynamic-block-subpages',
        array(
            'attributes' => array(
                'pageID' => array(
                    'type'    => 'number',
                    'default' => '0'
                ),
            ),
            'render_callback' => 'gutenberg_dynamic_render_callback',
        )
    );
}
add_action( 'init', 'subpages_block_jsx_example_backend_enqueue' );