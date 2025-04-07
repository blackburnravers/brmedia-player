<?php
/**
 * Video Module Logic
 * Advanced management of video uploads, embeds, and metadata.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'templates.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

class BRMedia_Video {
    public function __construct() {
        add_action('init', [$this, 'register_video_post_type']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'add_video_menu']);
        add_action('init', [$this, 'register_video_db_table']);
        add_action('wp_ajax_brmedia_video_action', [$this, 'handle_video_action']);
        add_action('wp_ajax_nopriv_brmedia_video_action', [$this, 'handle_video_action']);
        add_action('save_post_brmedia_video', [$this, 'save_video_metadata'], 10, 2);
    }

    // Register custom post type for videos
    public function register_video_post_type() {
        register_post_type('brmedia_video', [
            'labels' => [
                'name' => 'Videos',
                'singular_name' => 'Video',
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'taxonomies' => ['category', 'post_tag'],
        ]);
    }

    // Register database table for video analytics
    public function register_video_db_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'brmedia_video_stats';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            video_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL,
            timestamp DATETIME NOT NULL,
            ip_address VARCHAR(100) NOT NULL,
            PRIMARY KEY (id),
            INDEX video_id (video_id),
            INDEX user_id (user_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    // Enqueue shared assets
    public function enqueue_assets() {
        // Assets managed in brmedia-player.php or frontend.js/css
    }

    // Add video submenu to ACP
    public function add_video_menu() {
        add_submenu_page(
            'brmedia',
            'Video Settings',
            'Video',
            'manage_options',
            'brmedia-video',
            [$this, 'video_settings_page']
        );
    }

    // Render video settings page
    public function video_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        ?>
        <div class="wrap">
            <h1>Video Settings</h1>
            <p>Manage video settings here.</p>
        </div>
        <?php
    }

    // Handle video actions (e.g., play, pause)
    public function handle_video_action() {
        check_ajax_referer('brmedia_video_nonce', 'nonce');
        $video_id = intval($_POST['video_id'] ?? 0);
        $action = sanitize_text_field($_POST['action_type'] ?? 'play');
        $user_id = get roofs_current_user_id();

        if (!$video_id || get_post_type($video_id) !== 'brmedia_video') {
            wp_send_json_error(['message' => 'Invalid video ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_video_stats';
        $wpdb->insert($table, [
            'video_id' => $video_id,
            'user_id' => $user_id,
            'action' => $action,
            'timestamp' => current_time('mysql'),
            'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'Unknown'),
        ], ['%d', '%d', '%s', '%s', '%s']);

        wp_send_json_success   wp_send_json_success(['message' => 'Action recorded']);
    }

    // Save video metadata on post save
    public function save_video_metadata($post_id, $post) {
        if (get_post_type($post_id) !== 'brmedia_video' || wp_is_post_revision($post_id)) {
            return;
        }

        $video_url = get_post_meta($post_id, 'brmedia_video_url', true);
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/i', $video_url, $matches)) {
            $youtube_id = $matches[1];
            $api_key = 'YOUR_YOUTUBE_API_KEY'; // Replace with your API key
            $response = wp_remote_get("https://www.googleapis.com/youtube/v3/videos?id={$youtube_id}&part=contentDetails&key={$api_key}");
            if (!is_wp_error($response)) {
                $data = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($data['items'][0]['contentDetails']['duration'])) {
                    $duration = $this->convert_youtube_duration($data['items'][0]['contentDetails']['duration']);
                    update_post_meta($post_id, 'brmedia_video_duration', $duration);
                }
            }
        }
    }

    // Convert YouTube duration (ISO 8601) to human-readable format
    private function convert_youtube_duration($iso_duration) {
        $interval = new DateInterval($iso_duration);
        $hours = $interval->h;
        $minutes = $interval->i;
        $seconds = $interval->s;
        return ($hours ? $hours . 'h ' : '') . ($minutes ? $minutes . 'm ' : '') . $seconds . 's';
    }
}

new BRMedia_Video();