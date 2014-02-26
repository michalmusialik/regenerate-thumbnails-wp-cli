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

  	// Get access to the database
  	global $wpdb;

    // Query the database directly to get all images
    $images = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY ID DESC");

    // Check if query returned any images.
    if ( $images ) {
    	// Echo image count
    	$count = count($images);
    	WP_CLI::success( "$count images found." );	
    } else {
    	// Echo error and abort
    	WP_CLI::error( "No images found in database." );
    	return;
    }

  }
}