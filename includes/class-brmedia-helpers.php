<?php
/**
 * BRMedia Player Helper Functions
 *
 * This class provides utility functions used throughout the plugin.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BRMedia_Helpers {
    /**
     * Format a timestamp (seconds) into hh:mm:ss
     *
     * @param int $seconds Timestamp in seconds
     * @return string Formatted time (e.g., 01:23:45)
     */
    public static function format_timestamp($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Generate a secure download URL with a temporary token
     *
     * @param int $attachment_id Attachment ID
     * @return string Secure download URL
     */
    public static function generate_download_url($attachment_id) {
        $token = wp_generate_uuid4();
        set_transient('brmedia_download_token_' . $token, $attachment_id, HOUR_IN_SECONDS);
        return add_query_arg(array(
            'brmedia_download' => $attachment_id,
            'token' => $token,
        ), home_url());
    }

    /**
     * Get the URL of a template file
     *
     * @param string $type Template type (e.g., audio, video)
     * @param string $template Template name (e.g., default, popup)
     * @return string Template file path
     */
    public static function get_template_path($type, $template) {
        $path = plugin_dir_path(__DIR__) . "templates/{$type}/{$template}.php";
        if (file_exists($path)) {
            return $path;
        }
        return plugin_dir_path(__DIR__) . "templates/{$type}/default.php";
    }
}