<?php

WP_CLI::add_command( 'regenerate-thumbnails', 'Regenerate_Thumbnails_Command' );

/**
 * Regenerates thumbnails.
 */
class Regenerate_Thumbnails_Command extends WP_CLI_Command {

    /**
     * Regenerates all thumbnails.
     * 
     * ## EXAMPLES
     * 
     *     wp regenerate-thumbnails all
     *
     */
    function all( $args, $assoc_args ) {

        // Print a success message
        WP_CLI::success( "It works!" );
    }
}