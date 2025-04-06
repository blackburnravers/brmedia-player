<?php
/**
 * Uninstall script for BRMedia Player plugin.
 *
 * This file is executed when the plugin is uninstalled via the WordPress admin.
 * It removes all data associated with the plugin, including custom posts, 
 * their attachments, options, and custom database tables.
 *
 * Note: This script assumes a single-site WordPress installation. For multisite
 * support, additional logic would be required to loop through all sites.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    // Prevent direct access to this file
    exit;
}

// Ensure WordPress database object is available
global $wpdb;

/**
 * Delete Custom Posts and Attachments
 * 
 * Removes all posts of type 'brmusic' and 'brvideo', along with their associated
 * media attachments (if any).
 */

// Delete all 'brmusic' posts and their attachments
$music_posts = get_posts( array(
    'post_type'   => 'brmusic',         // Custom post type for music
    'numberposts' => -1,                // Retrieve all posts
    'post_status' => 'any',             // Include all statuses (publish, draft, trash, etc.)
) );

foreach ( $music_posts as $post ) {
    // Check for an audio attachment linked via post meta
    $attachment_id = get_post_meta( $post->ID, '_brmedia_audio_file', true );
    if ( $attachment_id ) {
        // Permanently delete the attachment
        wp_delete_attachment( $attachment_id, true );
    }
    // Permanently delete the post (bypasses trash)
    wp_delete_post( $post->ID, true );
}

// Delete all 'brvideo' posts and their attachments
$video_posts = get_posts( array(
    'post_type'   => 'brvideo',         // Custom post type for videos
    'numberposts' => -1,                // Retrieve all posts
    'post_status' => 'any',             // Include all statuses
) );

foreach ( $video_posts as $post ) {
    // Check for a video attachment linked via post meta
    $attachment_id = get_post_meta( $post->ID, '_brmedia_video_file', true );
    if ( $attachment_id ) {
        // Permanently delete the attachment
        wp_delete_attachment( $attachment_id, true );
    }
    // Permanently delete the post
    wp_delete_post( $post->ID, true );
}

/**
 * Delete Plugin Options
 * 
 * Removes all settings stored in the wp_options table related to the plugin.
 */
delete_option( 'brmedia_player_settings' ); // Main settings option
// Add additional options if the plugin uses them, e.g.:
// delete_option( 'brmedia_api_keys' );

/**
 * Drop Custom Database Tables
 * 
 * Deletes any custom tables created by the plugin (e.g., for analytics).
 * Uses DROP TABLE IF EXISTS to avoid errors if the table doesn't exist.
 */
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}brmedia_analytics" );
// Add additional table drops if the plugin creates other tables, e.g.:
// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}brmedia_other_table" );

/**
 * Optional Cleanup (Commented Out)
 * 
 * These sections can be uncommented and customized if the plugin uses transients,
 * user meta, or other data types.
 */

// Delete transients (temporary cached data)
// delete_transient( 'brmedia_transient_name' );

// Delete user meta (if the plugin stores user-specific data)
// $users = get_users();
// foreach ( $users as $user ) {
//     delete_user_meta( $user->ID, 'brmedia_user_meta_key' );
// }