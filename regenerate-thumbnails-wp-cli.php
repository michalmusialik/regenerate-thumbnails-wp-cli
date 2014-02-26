<?php
/*
Plugin Name:  Regenerate thumbnails WP-CLI
Plugin URI:   
Description:  This plugin adds a regenerate-thumbnails command to wp-cli. Based on Force Regenerate Thumbnails by Pedro Elsner.
Version:      0.0.1
Author:       Michal Musialik
Author URI:   http://www.michalmusialik.com/
 */

/*
 * Add the command to WP_CLI
 */
if ( defined('WP_CLI') && WP_CLI ) {
    include plugin_dir_path( __FILE__ ) . '/regenerate-thumbnails-command.php';
}
