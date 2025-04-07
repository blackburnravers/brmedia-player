<?php
/**
 * Radio Module Logic
 * Advanced management of radio streaming with sources, auto-DJ, and chat integration.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'templates.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

class BRMedia_Radio {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'add_radio_menu']);
        add_action('init', [$this, 'register_radio_db_tables']);
        add_action('wp_ajax_brmedia_radio_status', [$this, 'handle_radio_status']);
        add_actionasc_action('wp_ajax_nopriv_brmedia_radio_status', [$this, 'handle_radio_status']);
    }

    // Register database tables for radio timetables and auto-DJ playlists
    public function register_radio_db_tables() {
        global $wpdb;
        $timetable_table = $wpdb->prefix . 'brmedia_radio_timetable';
        $playlist_table = $wpdb->prefix . 'brmedia_radio_playlist';
        $charset_collate = $wpdb->get_charset_collate();

        $sql_timetable = "CREATE TABLE IF NOT EXISTS $timetable_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            dj_name VARCHAR(100) NOT NULL,
            start_time DATETIME NOT NULL,
            end_time DATETIME NOT NULL,
            stream_url VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        $sql_playlist = "CREATE TABLE IF NOT EXISTS $playlist_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            track_id BIGINT(20) UNSIGNED NOT NULL,
            position INT NOT NULL,
            PRIMARY KEY (id),
            INDEX track_id (track_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_timetable);
        dbDelta($sql_playlist);
    }

    // Enqueue shared assets
    public function enqueue_assets() {
        // No new CSS/JS files; assets are managed in brmedia-player.php or existing frontend files
    }

    // Add radio submenu to ACP
    public function add_radio_menu() {
        add_submenu_page(
            'brmedia',
            'Radio Settings',
            'Radio',
            'manage_options',
            'brmedia-radio',
            [$this, 'radio_settings_page']
        );
        add_action('admin_init', [$this, 'register_settings']);
    }

    // Register settings for ACP
    public function register_settings() {
        register_setting('brmedia_radio_settings', 'brmedia_radio_sources', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('brmedia_radio_settings', 'brmedia_radio_default_source', ['sanitize_callback' => 'sanitize_text_field']);

        add_settings_section('brmedia_radio_section', 'Radio Configuration', null, 'brmedia-radio');
        add_settings_field('sources', 'Streaming Sources (comma-separated)', [$this, 'sources_field'], 'brmedia-radio', 'brmedia_radio_section');
        add_settings_field('default_source', 'Default Source', [$this, 'default_source_field'], 'brmedia-radio', 'brmedia_radio_section');
    }

    public function sources_field() {
        $value = get_option('brmedia_radio_sources', 'icecast,shoutcast,youtube');
        echo '<input type="text" name="brmedia_radio_sources" value="' . esc_attr($value) . '" />';
    }

    public function default_source_field() {
        $value = get_option('brmedia_radio_default_source', 'icecast');
        $sources = explode(',', get_option('brmedia_radio_sources', 'icecast,shoutcast,youtube'));
        echo '<select name="brmedia_radio_default_source">';
        foreach ($sources as $source) {
            echo '<option value="' . esc_attr($source) . '"' . selected($value, $source, false) . '>' . esc_html($source) . '</option>';
        }
        echo '</select>';
    }

    // Render radio settings page
    public function radio_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        ?>
        <div class="wrap">
            <h1>Radio Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('brmedia_radio_settings');
                do_settings_sections('brmedia-radio');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Handle radio status updates via AJAX
    public function handle_radio_status() {
        check_ajax_referer('brmedia_radio_nonce', 'nonce');
        $current_time = current_time('mysql');
        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_radio_timetable';
        $active_dj = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE start_time <= %s AND end_time >= %s LIMIT 1",
            $current_time, $current_time
        ));

        if ($active_dj) {
            wp_send_json_success([
                'dj_name' => $active_dj->dj_name,
                'stream_url' => $active_dj->stream_url,
            ]);
        } else {
            // Fallback to auto-DJ playlist
            $playlist_table = $wpdb->prefix . 'brmedia_radio_playlist';
            $next_track = $wpdb->get_row("SELECT track_id FROM $playlist_table ORDER BY position ASC LIMIT 1");
            if ($next_track) {
                $track_url = wp_get_attachment_url($next_track->track_id);
                wp_send_json_success(['dj_name' => 'Auto-DJ', 'stream_url' => $track_url]);
            } else {
                wp_send_json_error(['message' => 'No active DJ or playlist available']);
            }
        }
    }
}

new BRMedia_Radio();