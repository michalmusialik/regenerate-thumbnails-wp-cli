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
    $images = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY ID ASC");

    // Check if query returned any images.
    if ( !$images ) {
      WP_CLI::error( "No images found in database." );
    }

    // Handle all images
    $count = count($images);
    $current = 1;
    foreach ( $images as $image ) {
      WP_CLI::line( "\nHandling image with id: $image->ID ($current out of $count)" );
      $this->handle_image( $image );
      $current++;
    }

  }

  /*
   * Handles a single image
   */
  private function handle_image( $image ) {

    // Get the image file path
    $image_path = get_attached_file( $image->ID );

    // Check if file exists
    if ( !file_exists( $image_path ) ) {
      WP_CLI::line( " Image file doesn't exist." );
      return;
    }

    /*
     * Delete all thumbnail files
     */

    // Get the filename
    $file_info = pathinfo( $image_path );

    WP_CLI::line( " Filename: " . $file_info['filename'] );

    $thumbnails = $this->find_thumbnails( $image_path );

    // If no thumbnails found
    if ( !$thumbnails ) {
      WP_CLI::line( " No thumbnails found." );
    }

    // Delete the thumbnails
    $deleted = "";
    $failed = "";
    foreach ( $thumbnails as $thumbnail ) {
      // Get the full thumbnail file path
      $thumbnail_path = $file_info['dirname'] . '/' . $thumbnail;

      // Try to delete the thumbnail file
      if ( unlink( $thumbnail_path ) ) {
      // if ( true ) {
        $deleted .= " " . $thumbnail;
      } else {
        $failed .= " " . $thumbnail;
      }
    }
    
    // If we managed to delete some thumbnails
    if ( "" !== $deleted ) {
      WP_CLI::line( " Deleted:" . $deleted );
    }

    // If we failed to delete some thumbnails
    if ( "" !== $failed ) {
      WP_CLI::line( " Failed to delete:" . $failed );
    }

    /*
     * Regenerate thumbnails for this image
     */

    // Start timer
    $timer_start = microtime(true);

    $image_meta = wp_generate_attachment_metadata( $image->ID, $image_path );
    if ( is_wp_error( $image_meta ) ) {
      WP_CLI::line( " " . $image_meta->get_error_message() );
    }
    if ( empty( $image_meta ) ) {
      WP_CLI::line( " Unknown error when regenerating thumbnails" );
    }
    wp_update_attachment_metadata( $image->ID, $image_meta );

    $time_taken = microtime(true) - $timer_start;

    /* 
     * Verify that thumbnails are regenerated
     */
    $thumbnails = $this->find_thumbnails( $image_path );

    $created = "";
    foreach ( $thumbnails as $thumbnail ) {
      // Get the full thumbnail file path
      $thumbnail_path = $file_info['dirname'] . '/' . $thumbnail;
      $created .= $thumbnail;
    }

    if ( "" !== $created ) {
      WP_CLI::line( " Created: $created in " . number_format( $time_taken, 3 ) . " seconds" );
    } else {
      WP_CLI::line( " Thumbnails were not created for unknonw reason." );
    }

  }

  /*
   * Scans a directory for thumbnails of an image
   * @param image_path - full path of the image file
   * @return array - array of string filenames
   */
  private function find_thumbnails( $image_path ) {

    $file_info = pathinfo( $image_path );

    // Open the directory
    $dir_handle = opendir( $file_info['dirname'] );
    if ( !$dir_handle ) {
      WP_CLI::line( " Couldn't open the directory." );
      return;
    }

    // Look for thumbnails of the image
    $thumbnails = array();
    while ( false !== ( $entry = readdir( $dir_handle ) ) ) {
      if ( $entry != "." && $entry != ".." ) {
        // Check if entry is a thumbnail of this image
        $pattern = '(' . $file_info['filename'] . ')(-)(\\d+)(x)(\\d+)';
        if ( preg_match ( "/^".$pattern."/is" , $entry ) === 1 ) {
          $thumbnails[] = $entry;
        }
      }
    }
    closedir( $dir_handle );

    return $thumbnails;
  }
}