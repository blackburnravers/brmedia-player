<?php
/**
 * Uninstall script for BRMedia Player
 * Cleans up all plugin data on uninstallation
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Exit if accessed directly
}

// Ensure proper permissions
if (!current_user_can('activate_plugins')) {
    return;
}

global $wpdb;

// Define plugin constants (mirrored from main file for standalone use)
define('BRMEDIA_SLUG', 'brmedia-player');
define('BRMEDIA_VERSION', '1.0.0');

// 1. Delete plugin options
$options = [
    'brmedia_settings',
    'brmedia_analytics_data',
    'brmedia_template_settings',
    'brmedia_api_keys',
];
foreach ($options as $option) {
    delete_option($option);
    delete_site_option($option); // For multisite
}

// 2. Delete transients
$transients = [
    'brmedia_cache_players',
    'brmedia_api_cache_soundcloud',
    'brmedia_api_cache_spotify',
];
foreach ($transients as $transient) {
    delete_transient($transient);
    delete_site_transient($transient); // For multisite
}

// 3. Delete custom post types and their metadata
$custom_post_types = ['brmusic', 'brvideo'];
foreach ($custom_post_types as $cpt) {
    $posts = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s", $cpt));
    foreach ($posts as $post_id) {
        wp_delete_post($post_id, true); // Force delete
        $wpdb->delete($wpdb->postmeta, ['post_id' => $post_id]);
    }
}

// 4. Drop custom database tables (e.g., for analytics)
$tables = [
    $wpdb->prefix . 'brmedia_analytics',
    $wpdb->prefix . 'brmedia_downloads',
];
foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");
}

// 5. Multisite cleanup
if (is_multisite()) {
    $blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}");
    foreach ($blogs as $blog) {
        switch_to_blog($blog->blog_id);
        
        // Repeat cleanup for each site
        foreach ($options as $option) {
            delete_option($option);
        }
        foreach ($transients as $transient) {
            delete_transient($transient);
        }
        foreach ($custom_post_types as $cpt) {
            $posts = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s", $cpt));
            foreach ($posts as $post_id) {
                wp_delete_post($post_id, true);
                $wpdb->delete($wpdb->postmeta, ['post_id' => $post_id]);
            }
        }
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
        
        restore_current_blog();
    }
}

// 6. Flush rewrite rules
flush_rewrite_rules();

// 7. Fire a custom action for extensions to hook into
do_action('brmedia_uninstall_cleanup');