<?php
/*
Plugin Name: BC-Reference
*/
function bc_reference_register_block() {
 
    // automatically load dependencies and version
    $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
 
    wp_register_script(
        'bc-reference-esnext',
        plugins_url( 'build/index.js', __FILE__ ),
        $asset_file['dependencies'],
        $asset_file['version']
    );
 
    register_block_type( 'bc/example-01-basic-esnext', array(
        'api_version' => 2,
        'editor_script' => 'gutenberg-examples-01-esnext',
    ) );
}
add_action( 'init', 'bc_reference_register_block' );