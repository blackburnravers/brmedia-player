<?php
/**
 * Chat Module Logic
 * Advanced real-time chat with moderation, multi-room support, and integration.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'templates.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

class BRMedia_Chat {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'add_chat_menu']);
        add_action('init', [$this, 'register_chat_db_tables']);
        add_action('wp_ajax_brmedia_chat_message', [$this, 'handle_chat_message']);
        add_action('wp_ajax_nopriv_brmedia_chat_message', [$this, 'handle_chat_message']);
        add_action('wp_ajax_brmedia_chat_moderate', [$this, 'handle_chat_moderation']);
        add_action('wp_ajax_brmedia_chat_fetch', [$this, 'fetch_chat_messages']);
        add_action('wp_ajax_nopriv_brmedia_chat_fetch', [$this, 'fetch_chat_messages']);
    }

    // Register database tables for chat messages and moderation logs
    public function register_chat_db_tables() {
        global $wpdb;
        $messages_table = $wpdb->prefix . 'brmedia_chat_messages';
        $moderation_table = $wpdb->prefix . 'brmedia_chat_moderation';
        $charset_collate = $wpdb->get_charset_collate();

        $sql_messages = "CREATE TABLE IF NOT EXISTS $messages_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            message TEXT NOT NULL,
            room_id VARCHAR(100) NOT NULL,
            timestamp DATETIME NOT NULL,
            is_moderated TINYINT(1) DEFAULT 0,
            PRIMARY KEY (id),
            INDEX room_id (room_id),
            INDEX user_id (user_id)
        ) $charset_collate;";

        $sql_moderation = "CREATE TABLE IF NOT EXISTS $moderation_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL, /* e.g., mute, ban, timeout */
            target_user_id BIGINT(20) UNSIGNED NOT NULL,
            room_id VARCHAR(100) NOT NULL,
            duration INT DEFAULT 0, /* Duration in seconds, 0 for permanent */
            timestamp DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX user_id (user_id),
            INDEX target_user_id (target_user_id),
            INDEX room_id (room_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_messages);
        dbDelta($sql_moderation);
    }

    // Enqueue shared assets (no new CSS/JS files)
    public function enqueue_assets() {
        // Assets are managed in brmedia-player.php or frontend.js/css
    }

    // Add chat submenu to ACP
    public function add_chat_menu() {
        add_submenu_page(
            'brmedia',
            'Chat Settings',
            'Chat',
            'manage_options',
            'brmedia-chat',
            [$this, 'chat_settings_page']
        );
        add_action('admin_init', [$this, 'register_settings']);
    }

    // Register settings for ACP
    public function register_settings() {
        register_setting('brmedia_chat_settings', 'brmedia_chat_rooms', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('brmedia_chat_settings', 'brmedia_chat_poll_interval', ['sanitize_callback' => 'intval']);
        add_settings_section('brmedia_chat_section', 'Chat Configuration', null, 'brmedia-chat');
        add_settings_field('rooms', 'Chat Rooms (comma-separated)', [$this, 'rooms_field'], 'brmedia-chat', 'brmedia_chat_section');
        add_settings_field('poll_interval', 'Poll Interval (seconds)', [$this, 'poll_interval_field'], 'brmedia-chat', 'brmedia_chat_section');
    }

    public function rooms_field() {
        $value = get_option('brmedia_chat_rooms', 'default,radio,gaming');
        echo '<input type="text" name="brmedia_chat_rooms" value="' . esc_attr($value) . '" />';
    }

    public function poll_interval_field() {
        $value = get_option('brmedia_chat_poll_interval', 5);
        echo '<input type="number" name="brmedia_chat_poll_interval" value="' . esc_attr($value) . '" min="1" />';
    }

    // Render chat settings page
    public function chat_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        ?>
        <div class="wrap">
            <h1>Chat Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('brmedia_chat_settings');
                do_settings_sections('brmedia-chat');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Handle sending chat messages
    public function handle_chat_message() {
        check_ajax_referer('brmedia_chat_nonce', 'nonce');
        $message = sanitize_text_field($_POST['message'] ?? '');
        $room_id = sanitize_text_field($_POST['room'] ?? 'default');
        $user_id = get_current_user_id();
        $rooms = explode(',', get_option('brmedia_chat_rooms', 'default,radio,gaming'));

        if (empty($message) || !in_array($room_id, $rooms)) {
            wp_send_json_error(['message' => 'Invalid input or room']);
        }

        if ($this->is_user_moderated($user_id, $room_id)) {
            wp_send_json_error(['message' => 'You are currently moderated in this room']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_chat_messages';
        $wpdb->insert($table, [
            'user_id' => $user_id ? $user_id : 0, // 0 for guests
            'message' => $message,
            'room_id' => $room_id,
            'timestamp' => current_time('mysql'),
        ], ['%d', '%s', '%s', '%s']);

        $this->broadcast_message($room_id, $message, $user_id);
        wp_send_json_success([
            'message' => $message,
            'user' => $user_id ? get_userdata($user_id)->display_name : 'Guest',
            'timestamp' => current_time('mysql'),
        ]);
    }

    // Handle moderation actions (mute, ban, timeout)
    public function handle_chat_moderation() {
        check_ajax_referer('brmedia_chat_nonce', 'nonce');
        if (!current_user_can('moderate_comments')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }

        $action = sanitize_text_field($_POST['action_type'] ?? '');
        $target_user_id = intval($_POST['target_user_id'] ?? 0);
        $room_id = sanitize_text_field($_POST['room'] ?? 'default');
        $duration = intval($_POST['duration'] ?? 0);
        $user_id = get_current_user_id();

        if (!in_array($action, ['mute', 'ban', 'timeout']) || !$target_user_id) {
            wp_send_json_error(['message' => 'Invalid moderation action']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_chat_moderation';
        $wpdb->insert($table, [
            'user_id' => $user_id,
            'action' => $action,
            'target_user_id' => $target_user_id,
            'room_id' => $room_id,
            'duration' => $duration,
            'timestamp' => current_time('mysql'),
        ], ['%d', '%s', '%d', '%s', '%d', '%s']);

        wp_send_json_success(['message' => "User $target_user_id moderated ($action) in room $room_id"]);
    }

    // Fetch chat messages for a room
    public function fetch_chat_messages() {
        check_ajax_referer('brmedia_chat_nonce', 'nonce');
        $room_id = sanitize_text_field($_POST['room'] ?? 'default');
        $last_id = intval($_POST['last_id'] ?? 0);

        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_chat_messages';
        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT id, user_id, message, timestamp FROM $table WHERE room_id = %s AND id > %d AND is_moderated = 0 ORDER BY timestamp ASC LIMIT 50",
            $room_id, $last_id
        ), ARRAY_A);

        $formatted_messages = array_map(function($msg) {
            return [
                'id' => $msg['id'],
                'user' => $msg['user_id'] ? get_userdata($msg['user_id'])->display_name : 'Guest',
                'message' => $msg['message'],
                'timestamp' => $msg['timestamp'],
            ];
        }, $messages);

        wp_send_json_success(['messages' => $formatted_messages]);
    }

    // Check if a user is moderated in a room
    private function is_user_moderated($user_id, $room_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_chat_moderation';
        $current_time = current_time('mysql');

        $moderation = $wpdb->get_row($wpdb->prepare(
            "SELECT action, duration, timestamp FROM $table WHERE target_user_id = %d AND room_id = %s AND (duration = 0 OR TIMESTAMPADD(SECOND, duration, timestamp) > %s) ORDER BY timestamp DESC LIMIT 1",
            $user_id, $room_id, $current_time
        ));

        return $moderation && in_array($moderation->action, ['mute', 'ban']);
    }

    // Simulate broadcasting (for AJAX polling; replace with WebSockets for production)
    private function broadcast_message($room_id, $message, $user_id) {
        // In a real-time system, this would push to connected clients via WebSockets
        // For now, it’s handled by client-side polling in fetch_chat_messages
    }
}

new BRMedia_Chat();