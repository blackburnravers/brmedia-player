<?php
/**
 * Gaming Module Logic
 * Advanced management of gaming content with Twitch/YouTube integration and analytics.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'templates.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

class BRMedia_Gaming {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'add_gaming_menu']);
        add_action('init', [$this, 'register_gaming_db_table']);
        add_action('wp_ajax_brmedia_gaming_status', [$this, 'handle_gaming_status']);
        add_action('wp_ajax_nopriv_brmedia_gaming_status', [$this, 'handle_gaming_status']);
    }

    // Register database table for gaming analytics
    public function register_gaming_db_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'brmedia_gaming_stats';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            content_id VARCHAR(100) NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL,
            timestamp DATETIME NOT NULL,
            ip_address VARCHAR(100) NOT NULL,
            PRIMARY KEY (id),
            INDEX content_id (content_id),
            INDEX user_id (user_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    // Enqueue shared assets
    public function enqueue_assets() {
        // Assets managed in brmedia-player.php or frontend.js/css
    }

    // Add gaming submenu to ACP
    public function add_gaming_menu() {
        add_submenu_page(
            'brmedia',
            'Gaming Settings',
            'Gaming',
            'manage_options',
            'brmedia-gaming',
            [$this, 'gaming_settings_page']
        );
        add_action('admin_init', [$this, 'register_settings']);
    }

    // Register settings for ACP
    public function register_settings() {
        register_setting('brmedia_gaming_settings', 'brmedia_gaming_twitch_key', ['sanitize_callback' => 'sanitize_text_field']);
        add_settings_section('brmedia_gaming_section', 'Gaming Integration', null, 'brmedia-gaming');
        add_settings_field('twitch_key', 'Twitch API Key', [$this, 'twitch_key_field'], 'brmedia-gaming', 'brmedia_gaming_section');
    }

    public function twitch_key_field() {
        $value = get_option('brmedia_gaming_twitch_key', '');
        echo '<input type="text" name="brmedia_gaming_twitch_key" value="' . esc_attr($value) . '" />';
    }

    // Render gaming settings page
    public function gaming_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        ?>
        <div class="wrap">
            <h1>Gaming Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('brmedia_gaming_settings');
                do_settings_sections('brmedia-gaming');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Handle gaming status (e.g., Twitch stream status)
    public function handle_gaming_status() {
        check_ajax_referer('brmedia_gaming_nonce', 'nonce');
        $type = sanitize_text_field($_POST['type'] ?? 'twitch');
        $id = sanitize_text_field($_POST['id'] ?? '');
        $user_id = get_current_user_id();

        if ($type === 'twitch') {
            $twitch_key = get_option('brmedia_gaming_twitch_key');
            $response = wp_remote_get("https://api.twitch.tv/helix/streams?user_login=$id", [
                'headers' => ['Client-ID' => $twitch_key, 'Authorization' => 'Bearer YOUR_OAUTH_TOKEN'], // Replace with OAuth token
            ]);
            if (!is_wp_error($response)) {
                $data = json_decode(wp_remote_retrieve_body($response), true);
                $is_live = !empty($data['data']);
                $this->log_gaming_action($id, $user_id, $is_live ? 'live' : 'offline');
                wp_send_json_success(['is_live' => $is_live]);
            }
        }

        wp_send_json_error(['message' => 'Status check failed']);
    }

    // Log gaming action
    private function log_gaming_action($content_id, $user_id, $action) {
        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_gaming_stats';
        $wpdb->insert($table, [
            'content_id' => $content_id,
            'user_id' => $user_id,
            'action' => $action,
            'timestamp' => current_time('mysql'),
            'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'Unknown'),
        ], ['%s', '%d', '%s', '%s', '%s']);
    }
}

new BRMedia_Gaming();