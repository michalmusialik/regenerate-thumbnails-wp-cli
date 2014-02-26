<?php

WP_CLI::add_command( 'example', 'Example_Command' );

/**
 * Implements example command.
 */
class Example_Command extends WP_CLI_Command {

    /**
     * Prints a greeting.
     * 
     * ## OPTIONS
     * 
     * <name>
     * : The name of the person to greet.
     * 
     * ## EXAMPLES
     * 
     *     wp example hello Newman
     *
     * @synopsis <name>
     */
    function hello( $args, $assoc_args ) {
        list( $name ) = $args;

        // Print a success message
        WP_CLI::success( "Hello, $name!" );
    }
}