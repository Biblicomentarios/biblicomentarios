<?php

/**
 * Class StockpackCli
 *
 * Handle Cli commands for stockpack
 */
class StockpackCLI {
    public function __construct() {

        // example constructor called when plugin loads

    }

    public function update_token( $args, $assoc_args ) {
        global $stockpack;

        list( $token ) = $args;
        $stockpack->admin->set_api_key($token);

        WP_CLI::success( $token.__(' set as token for the StockPack plugin!','stockpack') );

    }
}
