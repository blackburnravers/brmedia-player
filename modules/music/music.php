<?php
/**
 * Music Module Logic
 * Advanced management of audio tracks with playlists, metadata, and external syncing.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'templates.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

class BRMedia_Music {
    public function __construct() {
        add_action('init', [$this, 'register_music_post_type']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'add_music_menu']);
        add_action('init', [$this, 'register_music_db_tables']);
        add_action('wp_ajax_brmedia_music_action', [$this, 'handle_music_action']);
        add_action('wp_ajax_nopriv_brmedia_music_action', [$this, 'handle_music_action']);
        add_action('save_post_brmedia_music', [$this, 'save_music_metadata'], 10, 2);
    }

    // Register custom post type for music tracks
    public function register_music_post_type() {
        register_post_type('brmedia_music', [
            'labels' => [
                'name' => 'Music',
                'singular_name' => 'Track',
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'taxonomies' => ['category', 'post_tag'],
        ]);

        register_taxonomy('brmedia_playlist', 'brmedia_music', [
            'labels' => [
                'name' => 'Playlists',
                'singular_name' => 'Playlist',
            ],
            'public' => true,
            'hierarchical' => true,
        ]);
    }

    // Register database tables for music analytics and playlists
    public function register_music_db_tables() {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'brmedia_music_stats';
        $playlist_table = $wpdb->prefix . 'brmedia_music_playlist';
        $charset_collate = $wpdb->get_charset_collate();

        $sql_stats = "CREATE TABLE IF NOT EXISTS $stats_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            track_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL,
            timestamp DATETIME NOT NULL,
            ip_address VARCHAR(100) NOT NULL,
            PRIMARY KEY (id),
            INDEX track_id (track_id),
            INDEX user_id (user_id)
        ) $charset_collate;";

        $sql_playlist = "CREATE TABLE IF NOT EXISTS $playlist_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            playlist_id BIGINT(20) UNSIGNED NOT NULL,
            track_id BIGINT(20) UNSIGNED NOT NULL,
            position INT NOT NULL,
            PRIMARY KEY (id),
            INDEX playlist_id (playlist_id),
            INDEX track_id (track_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_stats);
        dbDelta($sql_playlist);
    }

    // Enqueue shared assets
    public function enqueue_assets() {
        wp_enqueue_script('plyr', 'https://cdn.plyr.io/3.6.8/plyr.min.js', [], '3.6.8', true);
        wp_enqueue_style('plyr', 'https://cdn.plyr.io/3.6.8/plyr.css', [], '3.6.8');
    }

    // Add music submenu to ACP
    public function add_music_menu() {
        add_submenu_page(
            'brmedia',
            'Music Settings',
            'Music',
            'manage_options',
            'brmedia-music',
            [$this, 'music_settings_page']
        );
        add_action('admin_init', [$this, 'register_settings']);
    }

    // Register settings for ACP
    public function register_settings() {
        register_setting('brmedia_music_settings', 'brmedia_music_soundcloud_key', ['sanitize_callback' => 'sanitize_text_field']);
        add_settings_section('brmedia_music_section', 'Music Integration', null, 'brmedia-music');
        add_settings_field('soundcloud_key', 'SoundCloud API Key', [$this, 'soundcloud_key_field'], 'brmedia-music', 'brmedia_music_section');
    }

    public function soundcloud_key_field() {
        $value = get_option('brmedia_music_soundcloud_key', '');
        echo '<input type="text" name="brmedia_music_soundcloud_key" value="' . esc_attr($value) . '" />';
    }

    // Render music settings page
    public function music_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        ?>
        <div class="wrap">
            <h1>Music Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('brmedia_music_settings');
                do_settings_sections('brmedia-music');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Handle music actions (e.g., play, pause)
    public function handle_music_action() {
        check_ajax_referer('brmedia_music_nonce', 'nonce');
        $track_id = intval($_POST['track_id'] ?? 0);
        $action = sanitize_text_field($_POST['action_type'] ?? 'play');
        $user_id = get_current_user_id();

        if (!$track_id || get_post_type($track_id) !== 'brmedia_music') {
            wp_send_json_error(['message' => 'Invalid track ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_music_stats';
        $wpdb->insert($table, [
            'track_id' => $track_id,
            'user_id' => $user_id,
            'action' => $action,
            'timestamp' => current_time('mysql'),
            'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'Unknown'),
        ], ['%d', '%d', '%s', '%s', '%s']);

        wp_send_json_success(['message' => 'Action recorded']);
    }

    // Save music metadata on post save
    public function save_music_metadata($post_id, $post) {
        if (get_post_type($post_id) !== 'brmedia_music' || wp_is_post_revision($post_id)) {
            return;
        }

        $audio_id = get_post_meta($post_id, 'brmedia_music_audio', true);
        if ($audio_id && ($audio_path = get_attached_file($audio_id))) {
            // Placeholder for metadata extraction (requires external libraries like getID3)
            update_post_meta($post_id, 'brmedia_music_duration', 'Unknown'); // Replace with actual duration extraction
        }

        $soundcloud_url = get_post_meta($post_id, 'brmedia_music_soundcloud', true);
        if ($soundcloud_url) {
            $api_key = get_option('brmedia_music_soundcloud_key');
            $response = wp_remote_get("https://api.soundcloud.com/resolve?url=" . urlencode($soundcloud_url) . "&client_id=" . $api_key);
            if (!is_wp_error($response)) {
                $data = json_decode(wp_remote_retrieve_body($response), true);
                if ($data['id']) {
                    update_post_meta($post_id, 'brmedia_music_soundcloud_id', $data['id']);
                    update_post_meta($post_id, 'brmedia_music_duration', gmdate('H:i:s', $data['duration'] / 1000));
                }
            }
        }
    }
}

new BRMedia_Music();