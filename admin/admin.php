<?php
/**
 * BRMedia Player Admin Menu
 *
 * This file sets up the admin menu and submenus for the plugin's Admin Control Panel (ACP).
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the top-level admin menu and its submenus
 */
function brmedia_add_admin_menu() {
    // Top-level menu
    add_menu_page(
        'BRMedia Player',           // Page title
        'BRMedia',                  // Menu title
        'manage_options',           // Capability required
        'brmedia',                  // Menu slug
        'brmedia_dashboard_page',   // Callback function
        'dashicons-media-audio',    // Icon
        20                          // Position
    );

    // Dashboard submenu (default page)
    add_submenu_page(
        'brmedia',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'brmedia',
        'brmedia_dashboard_page'
    );

    // Custom post type: Music
    add_submenu_page(
        'brmedia',
        'Music',
        'Music',
        'manage_options',
        'edit.php?post_type=brmusic'
    );

    // Custom post type: Video
    add_submenu_page(
        'brmedia',
        'Video',
        'Video',
        'manage_options',
        'edit.php?post_type=brvideo'
    );

    // Media management page
    add_submenu_page(
        'brmedia',
        'Media',
        'Media',
        'manage_options',
        'brmedia-media',
        'brmedia_media_page'
    );

    // Radio settings page
    add_submenu_page(
        'brmedia',
        'Radio',
        'Radio',
        'manage_options',
        'brmedia-radio',
        'brmedia_radio_page'
    );

    // Templates customization page
    add_submenu_page(
        'brmedia',
        'Templates Panel',
        'Templates Panel',
        'manage_options',
        'brmedia-templates',
        'brmedia_templates_page'
    );

    // Shortcodes manager page
    add_submenu_page(
        'brmedia',
        'Shortcodes Manager',
        'Shortcodes Manager',
        'manage_options',
        'brmedia-shortcodes',
        'brmedia_shortcodes_page'
    );

    // Statistics and analytics page
    add_submenu_page(
        'brmedia',
        'Statistics & Analytics',
        'Statistics & Analytics',
        'manage_options',
        'brmedia-analytics',
        'brmedia_analytics_page'
    );

    // Widgets and SEO settings page
    add_submenu_page(
        'brmedia',
        'Widgets & SEO',
        'Widgets & SEO',
        'manage_options',
        'brmedia-widgets-seo',
        'brmedia_widgets_seo_page'
    );

    // Social sharing settings page
    add_submenu_page(
        'brmedia',
        'Social Share',
        'Social Share',
        'manage_options',
        'brmedia-social-share',
        'brmedia_social_share_page'
    );
}
add_action('admin_menu', 'brmedia_add_admin_menu');

/**
 * Include all admin page files and register settings
 */
function brmedia_include_admin_pages() {
    // Include all admin page files
    require_once plugin_dir_path(__FILE__) . 'dashboard.php';
    require_once plugin_dir_path(__FILE__) . 'music-sync.php';
    require_once plugin_dir_path(__FILE__) . 'video-sync.php';
    require_once plugin_dir_path(__FILE__) . 'radio-settings.php';
    require_once plugin_dir_path(__FILE__) . 'templates-panel.php';
    require_once plugin_dir_path(__FILE__) . 'shortcodes-manager.php';
    require_once plugin_dir_path(__FILE__) . 'statistics-analytics.php';
    require_once plugin_dir_path(__FILE__) . 'widgets-seo.php';
    require_once plugin_dir_path(__FILE__) . 'social-share.php';

    // Register settings for form submissions
    register_setting('brmedia_module_settings', 'brmedia_music_enabled');
    register_setting('brmedia_module_settings', 'brmedia_video_enabled');
    register_setting('brmedia_module_settings', 'brmedia_radio_enabled');
    register_setting('brmedia_module_settings', 'brmedia_gaming_enabled');
    register_setting('brmedia_module_settings', 'brmedia_podcasts_enabled');
    register_setting('brmedia_module_settings', 'brmedia_chat_enabled');
    register_setting('brmedia_module_settings', 'brmedia_downloads_enabled');
}
add_action('admin_init', 'brmedia_include_admin_pages');

/**
 * Enqueue admin styles and scripts
 */
function brmedia_admin_enqueue_scripts($hook) {
    if (strpos($hook, 'brmedia') !== false) {
        wp_enqueue_style('brmedia-admin-css', plugin_dir_url(__FILE__) . 'css/admin.css', array(), '1.0.0');
        wp_enqueue_script('brmedia-admin-js', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), '1.0.0', true);
    }
}
add_action('admin_enqueue_scripts', 'brmedia_admin_enqueue_scripts');