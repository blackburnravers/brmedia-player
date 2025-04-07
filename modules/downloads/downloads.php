<?php
/**
 * Downloads Module Logic
 * Advanced management of downloadable files with gating, bulk downloads, and analytics.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'templates.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

class BRMedia_Downloads {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'add_downloads_menu']);
        add_action('init', [$this, 'register_downloads_db_tables']);
        add_action('wp_ajax_brmedia_download', [$this, 'handle_download']);
        add_action('wp_ajax_nopriv_brmedia_download', [$this, 'handle_download']);
        add_action('wp_ajax_brmedia_download_bulk', [$this, 'handle_bulk_download']);
        add_action('wp_ajax_nopriv_brmedia_download_bulk', [$this, 'handle_bulk_download']);
    }

    // Register database tables for download stats and gating
    public function register_downloads_db_tables() {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'brmedia_download_stats';
        $gates_table = $wpdb->prefix . 'brmedia_download_gates';
        $charset_collate = $wpdb->get_charset_collate();

        $sql_stats = "CREATE TABLE IF NOT EXISTS $stats_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            file_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            timestamp DATETIME NOT NULL,
            ip_address VARCHAR(100) NOT NULL,
            user_agent TEXT NOT NULL,
            PRIMARY KEY (id),
            INDEX file_id (file_id),
            INDEX user_id (user_id)
        ) $charset_collate;";

        $sql_gates = "CREATE TABLE IF NOT EXISTS $gates_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            file_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            gate_type VARCHAR(50) NOT NULL,
            gate_value TEXT NOT NULL,
            timestamp DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX file_id (file_id),
            INDEX user_id (user_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_stats);
        dbDelta($sql_gates);
    }

    // Enqueue shared assets (no new CSS/JS files)
    public function enqueue_assets() {
        // Assets are managed in brmedia-player.php or frontend.js/css
    }

    // Add downloads submenu to ACP
    public function add_downloads_menu() {
        add_submenu_page(
            'brmedia',
            'Downloads Settings',
            'Downloads',
            'manage_options',
            'brmedia-downloads',
            [$this, 'downloads_settings_page']
        );
        add_action('admin_init', [$this, 'register_settings']);
    }

    // Register settings for ACP
    public function register_settings() {
        register_setting('brmedia_downloads_settings', 'brmedia_download_gate_options', ['sanitize_callback' => 'sanitize_text_field']);
        add_settings_section('brmedia_downloads_section', 'Download Gate Settings', null, 'brmedia-downloads');
        add_settings_field('gate_options', 'Gate Options (comma-separated)', [$this, 'gate_options_field'], 'brmedia-downloads', 'brmedia_downloads_section');
    }

    public function gate_options_field() {
        $value = get_option('brmedia_download_gate_options', 'email,social,login');
        echo '<input type="text" name="brmedia_download_gate_options" value="' . esc_attr($value) . '" />';
    }

    // Render downloads settings page
    public function downloads_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        ?>
        <div class="wrap">
            <h1>Downloads Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('brmedia_downloads_settings');
                do_settings_sections('brmedia-downloads');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Handle single file download
    public function handle_download() {
        check_ajax_referer('brmedia_downloads_nonce', 'nonce');
        $file_id = intval($_POST['file_id'] ?? 0);
        $gate_type = sanitize_text_field($_POST['gate_type'] ?? '');
        $gate_value = sanitize_text_field($_POST['gate_value'] ?? '');
        $user_id = get_current_user_id();

        if (!$file_id || !($file_path = get_attached_file($file_id))) {
            wp_send_json_error(['message' => 'Invalid file ID']);
        }

        if ($gate_type && in_array($gate_type, explode(',', get_option('brmedia_download_gate_options', 'email,social,login')))) {
            if (!$this->check_gate($file_id, $user_id, $gate_type, $gate_value)) {
                wp_send_json_error(['message' => 'Gate requirement not met']);
            }
        }

        $this->log_download($file_id, $user_id);
        $this->serve_file($file_path);
    }

    // Handle bulk download
    public function handle_bulk_download() {
        check_ajax_referer('brmedia_downloads_nonce', 'nonce');
        $file_ids = array_map('intval', explode(',', $_POST['file_ids'] ?? ''));
        $user_id = get_current_user_id();

        if (empty($file_ids)) {
            wp_send_json_error(['message' => 'No files specified']);
        }

        $zip = new ZipArchive();
        $zip_name = 'brmedia_bulk_download_' . time() . '.zip';
        $zip_path = wp_upload_dir()['path'] . '/' . $zip_name;

        if ($zip->open($zip_path, ZipArchive::CREATE) !== true) {
            wp_send_json_error(['message' => 'Failed to create archive']);
        }

        foreach ($file_ids as $file_id) {
            if ($file_path = get_attached_file($file_id)) {
                $zip->addFile($file_path, basename($file_path));
                $this->log_download($file_id, $user_id);
            }
        }
        $zip->close();

        $this->serve_file($zip_path, true);
        @unlink($zip_path); // Clean up after serving
    }

    // Check gating condition
    private function check_gate($file_id, $user_id, $gate_type, $gate_value) {
        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_download_gates';

        if ($gate_type === 'email' && !is_email($gate_value)) {
            return false;
        } elseif ($gate_type === 'login' && !$user_id) {
            return false;
        }

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE file_id = %d AND user_id = %d AND gate_type = %s",
            $file_id, $user_id, $gate_type
        ));

        if (!$existing) {
            $wpdb->insert($table, [
                'file_id' => $file_id,
                'user_id' => $user_id,
                'gate_type' => $gate_type,
                'gate_value' => $gate_value,
                'timestamp' => current_time('mysql'),
            ], ['%d', '%d', '%s', '%s', '%s']);
        }

        return true;
    }

    // Log download action
    private function log_download($file_id, $user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_download_stats';
        $wpdb->insert($table, [
            'file_id' => $file_id,
            'user_id' => $user_id,
            'timestamp' => current_time('mysql'),
            'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'Unknown'),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'),
        ], ['%d', '%d', '%s', '%s', '%s']);
    }

    // Serve file securely
    private function serve_file($file_path, $is_temp = false) {
        $file_name = basename($file_path);
        $mime_type = mime_content_type($file_path) ?: 'application/octet-stream';

        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        if (!$is_temp) {
            exit;
        }
    }
}

new BRMedia_Downloads();