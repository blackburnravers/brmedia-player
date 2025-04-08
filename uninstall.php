<?php
/**
 * BRMedia Player Uninstall Script
 *
 * This script runs when the plugin is uninstalled. It removes all plugin data,
 * including options, custom post types, and transients.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Exit if accessed directly
}

// Delete plugin options
delete_option('brmedia_version');
delete_option('brmedia_twitch_client_id');
delete_option('brmedia_twitch_oauth_token');
delete_option('brmedia_radio_track_api');
delete_option('brmedia_footer_audio_url');
delete_option('brmedia_footer_audio_title');

// Delete transients
delete_transient('brmedia_download_token_');

// Delete custom post types and associated data
$custom_post_types = array('brmusic', 'brvideo', 'brradio', 'brgaming', 'brpodcasts', 'brchat', 'brdownloads');
foreach ($custom_post_types as $post_type) {
    $posts = get_posts(array(
        'post_type' => $post_type,
        'numberposts' => -1,
        'post_status' => 'any'
    ));
    foreach ($posts as $post) {
        wp_delete_post($post->ID, true); // Force delete without moving to trash
    }
}

// Delete taxonomies
$taxonomies = array('brmedia_category', 'brmedia_tag');
foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false
    ));
    foreach ($terms as $term) {
        wp_delete_term($term->term_id, $taxonomy);
    }
}

// Remove any plugin-specific database tables (if applicable)
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}brmedia_analytics");