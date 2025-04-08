<?php
/**
 * Plugin Name: BRMedia Player
 * Plugin URI: https://www.blackburnravers.co.uk
 * Description: A powerful, modular WordPress plugin for managing and presenting multimedia content, including audio, video, radio streams, gaming streams, podcasts, and downloads, tailored for the Blackburn Ravers community.
 * Version: 1.0.0
 * Author: Rhys Cole
 * Author URI: https://www.blackburnravers.co.uk
 * License: GPLv2 or later
 * Text Domain: brmedia
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('BRMEDIA_VERSION', '1.0.0');
define('BRMEDIA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BRMEDIA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BRMEDIA_TEMPLATES_DIR', BRMEDIA_PLUGIN_DIR . 'templates/');

// Include core files from /br/includes/
require_once BRMEDIA_PLUGIN_DIR . 'includes/class-brmedia-core.php';
require_once BRMEDIA_PLUGIN_DIR . 'includes/class-brmedia-helpers.php';
require_once BRMEDIA_PLUGIN_DIR . 'includes/class-brmedia-analytics.php';
require_once BRMEDIA_PLUGIN_DIR . 'includes/class-brmedia-comments.php';
require_once BRMEDIA_PLUGIN_DIR . 'includes/class-brmedia-downloads.php';
require_once BRMEDIA_PLUGIN_DIR . 'includes/taxonomies.php';

// Include module files from /br/modules/
require_once BRMEDIA_PLUGIN_DIR . 'modules/music/music.php';
require_once BRMEDIA_PLUGIN_DIR . 'modules/video/video.php';
require_once BRMEDIA_PLUGIN_DIR . 'modules/radio/radio.php';
require_once BRMEDIA_PLUGIN_DIR . 'modules/gaming/gaming.php';
require_once BRMEDIA_PLUGIN_DIR . 'modules/podcasts/podcasts.php';
require_once BRMEDIA_PLUGIN_DIR . 'modules/chat/chat.php';
require_once BRMEDIA_PLUGIN_DIR . 'modules/downloads/downloads.php';

// Include admin files from /br/admin/
require_once BRMEDIA_PLUGIN_DIR . 'admin/admin.php';
require_once BRMEDIA_PLUGIN_DIR . 'admin/dashboard.php';
require_once BRMEDIA_PLUGIN_DIR . 'admin/music-sync.php';
require_once BRMEDIA_PLUGIN_DIR . 'admin/video-sync.php';
require_once BRMEDIA_PLUGIN_DIR . 'admin/radio-settings.php';
require_once BRMEDIA_PLUGIN_DIR . 'admin/templates-panel.php';
require_once BRMEDIA_PLUGIN_DIR . 'admin/shortcodes-manager.php';
require_once BRMEDIA_PLUGIN_DIR . 'admin/statistics-analytics.php';
require_once BRMEDIA_PLUGIN_DIR . 'admin/widgets-seo.php';
require_once BRMEDIA_PLUGIN_DIR . 'admin/social-share.php';

// Include template-specific files from /br/templates/
require_once BRMEDIA_TEMPLATES_DIR . 'footer-player.php';

// Initialize the plugin
function brmedia_init() {
    $brmedia_core = new BRMedia_Core();
    do_action('brmedia_after_init', $brmedia_core);
}
add_action('plugins_loaded', 'brmedia_init');

// Activation and Deactivation Hooks
register_activation_hook(__FILE__, 'brmedia_activate');
function brmedia_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'brmedia_analytics';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) UNSIGNED NOT NULL,
        event_type VARCHAR(50) NOT NULL,
        event_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_ip VARCHAR(100),
        PRIMARY KEY (id),
        INDEX idx_post_id (post_id)
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('brmedia_version', BRMEDIA_VERSION);
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'brmedia_deactivate');
function brmedia_deactivate() {
    delete_transient('brmedia_cache');
    flush_rewrite_rules();
}

// Enqueue Frontend Assets
function brmedia_enqueue_frontend_assets() {
    wp_enqueue_script('wavesurfer-js', 'https://cdn.jsdelivr.net/npm/wavesurfer.js@6.0.0/dist/wavesurfer.min.js', [], '6.0.0', true);
    wp_enqueue_script('video-js', 'https://vjs.zencdn.net/7.20.3/video.min.js', [], '7.20.3', true);
    wp_enqueue_style('video-js-css', 'https://vjs.zencdn.net/7.20.3/video-js.min.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', [], '5.15.4');
    
    wp_enqueue_style('brmedia-frontend', BRMEDIA_PLUGIN_URL . 'assets/css/frontend.css', [], BRMEDIA_VERSION);
    wp_enqueue_script('brmedia-frontend', BRMEDIA_PLUGIN_URL . 'assets/js/frontend.js', ['jquery', 'wavesurfer-js', 'video-js'], BRMEDIA_VERSION, true);
    
    wp_localize_script('brmedia-frontend', 'brmedia_params', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('brmedia_nonce'),
        'twitchClientId' => get_option('brmedia_twitch_client_id', ''),
        'twitchOAuthToken' => get_option('brmedia_twitch_oauth_token', ''),
        'trackApiUrl' => get_option('brmedia_radio_track_api', ''),
        'siteUrl' => 'https://www.blackburnravers.co.uk'
    ]);
}
add_action('wp_enqueue_scripts', 'brmedia_enqueue_frontend_assets');

// Enqueue Admin Assets
function brmedia_enqueue_admin_assets($hook) {
    if (strpos($hook, 'brmedia') !== false) {
        wp_enqueue_style('brmedia-admin', BRMEDIA_PLUGIN_URL . 'assets/css/admin.css', [], BRMEDIA_VERSION);
        wp_enqueue_script('brmedia-admin', BRMEDIA_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], BRMEDIA_VERSION, true);
    }
}
add_action('admin_enqueue_scripts', 'brmedia_enqueue_admin_assets');

// Load Text Domain for Translations
function brmedia_load_textdomain() {
    load_plugin_textdomain('brmedia', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'brmedia_load_textdomain');

// Register Custom Post Types and Taxonomies
function brmedia_register_custom_types() {
    $post_types = [
        'brmusic' => ['name' => 'Music', 'singular_name' => 'Music'],
        'brvideo' => ['name' => 'Videos', 'singular_name' => 'Video'],
        'brradio' => ['name' => 'Radio', 'singular_name' => 'Radio'],
        'brgaming' => ['name' => 'Gaming', 'singular_name' => 'Gaming'],
        'brpodcasts' => ['name' => 'Podcasts', 'singular_name' => 'Podcast'],
        'brdownloads' => ['name' => 'Downloads', 'singular_name' => 'Download']
    ];
    
    foreach ($post_types as $type => $labels) {
        register_post_type($type, [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'comments'],
            'taxonomies' => ['brmedia_category', 'brmedia_tag'],
            'show_in_rest' => true
        ]);
    }
    
    register_taxonomy('brmedia_category', array_keys($post_types), [
        'labels' => ['name' => 'Categories', 'singular_name' => 'Category'],
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true
    ]);
    
    register_taxonomy('brmedia_tag', array_keys($post_types), [
        'labels' => ['name' => 'Tags', 'singular_name' => 'Tag'],
        'hierarchical' => false,
        'public' => true,
        'show_in_rest' => true
    ]);
}
add_action('init', 'brmedia_register_custom_types');

// Shortcodes for Multimedia Content
function brmedia_shortcode_handler($atts, $content, $tag) {
    $atts = shortcode_atts([
        'id' => 0,
        'template' => 'default',
        'type' => str_replace('brmedia_', '', $tag)
    ], $atts);
    
    $post_id = intval($atts['id']);
    $template = sanitize_text_field($atts['template']);
    $type = sanitize_key($atts['type']);
    
    $meta_key = "brmedia_{$type}_url";
    $media_url = get_post_meta($post_id, $meta_key, true);
    
    if (empty($media_url) || !file_exists(BRMEDIA_TEMPLATES_DIR . "{$type}/{$template}.php")) {
        return '<p>' . esc_html__(ucfirst($type) . ' not found.', 'brmedia') . '</p>';
    }
    
    ob_start();
    include BRMEDIA_TEMPLATES_DIR . "{$type}/{$template}.php";
    return ob_get_clean();
}

$shortcodes = ['brmedia_audio', 'brmedia_video', 'brmedia_radio', 'brmedia_gaming', 'brmedia_podcast', 'brmedia_download'];
foreach ($shortcodes as $shortcode) {
    add_shortcode($shortcode, 'brmedia_shortcode_handler');
}

// AJAX Handlers
function brmedia_track_play() {
    check_ajax_referer('brmedia_nonce', 'nonce');
    $post_id = intval($_POST['post_id']);
    $event_type = sanitize_text_field($_POST['event_type']);
    
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'brmedia_analytics',
        [
            'post_id' => $post_id,
            'event_type' => $event_type,
            'user_ip' => BRMedia_Helpers::get_user_ip()
        ],
        ['%d', '%s', '%s']
    );
    wp_send_json_success(['message' => 'Play tracked']);
}
add_action('wp_ajax_brmedia_track_play', 'brmedia_track_play');
add_action('wp_ajax_nopriv_brmedia_track_play', 'brmedia_track_play');

function brmedia_add_comment() {
    check_ajax_referer('brmedia_nonce', 'nonce');
    $comment_data = [
        'comment_post_ID' => intval($_POST['post_id']),
        'comment_content' => sanitize_textarea_field($_POST['comment']),
        'user_id' => get_current_user_id(),
        'comment_approved' => 1
    ];
    $comment_id = wp_insert_comment($comment_data);
    wp_send_json_success(['comment_id' => $comment_id]);
}
add_action('wp_ajax_brmedia_add_comment', 'brmedia_add_comment');
add_action('wp_ajax_nopriv_brmedia_add_comment', 'brmedia_add_comment');

// Footer Player Integration
function brmedia_footer_player() {
    include BRMEDIA_TEMPLATES_DIR . 'footer-player.php';
}
add_action('wp_footer', 'brmedia_footer_player');

// Add custom action links to the plugin row in the Plugins list
function brmedia_add_action_links($links) {
    $custom_links = array(
        '<a href="' . admin_url('edit.php?post_type=brmusic') . '">' . __('Music', 'brmedia') . '</a>',
        '<a href="' . admin_url('admin.php?page=brmedia-dashboard') . '">' . __('Settings', 'brmedia') . '</a>',
        '<a href="' . admin_url('admin.php?page=brmedia-templates') . '">' . __('Templates', 'brmedia') . '</a>',
    );
    return array_merge($custom_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'brmedia_add_action_links');

// Add Admin Menu
function brmedia_admin_menu() {
    add_menu_page(
        'BRMedia Dashboard',
        'BRMedia',
        'manage_options',
        'brmedia-dashboard',
        'brmedia_dashboard_page',
        'dashicons-playlist-audio',
        20
    );
    
    add_submenu_page(
        'brmedia-dashboard',
        'Music Sync',
        'Music Sync',
        'manage_options',
        'brmedia-music-sync',
        'brmedia_music_sync_page'
    );
    
    add_submenu_page(
        'brmedia-dashboard',
        'Video Sync',
        'Video Sync',
        'manage_options',
        'brmedia-video-sync',
        'brmedia_video_sync_page'
    );
    
    add_submenu_page(
        'brmedia-dashboard',
        'Radio Settings',
        'Radio Settings',
        'manage_options',
        'brmedia-radio-settings',
        'brmedia_radio_settings_page'
    );
}
add_action('admin_menu', 'brmedia_admin_menu');

// REST API Endpoints
function brmedia_register_rest_routes() {
    register_rest_route('brmedia/v1', '/analytics/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'brmedia_get_analytics',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ]);
}
add_action('rest_api_init', 'brmedia_register_rest_routes');

function brmedia_get_analytics($request) {
    $post_id = $request['id'];
    global $wpdb;
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT event_type, COUNT(*) as count FROM {$wpdb->prefix}brmedia_analytics WHERE post_id = %d GROUP BY event_type",
            $post_id
        )
    );
    return rest_ensure_response($results);
}

// Uninstall Hook
register_uninstall_hook(__FILE__, 'brmedia_uninstall');
function brmedia_uninstall() {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}brmedia_analytics");
    delete_option('brmedia_version');
}