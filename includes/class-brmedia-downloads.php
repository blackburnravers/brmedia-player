<?php
/**
 * BRMedia Player Downloads Class
 *
 * This class implements a secure download system with temporary tokens.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BRMedia_Downloads {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'handle_download'));
    }

    /**
     * Handle download requests
     */
    public function handle_download() {
        if (isset($_GET['brmedia_download']) && isset($_GET['token'])) {
            $attachment_id = intval($_GET['brmedia_download']);
            $token = sanitize_text_field($_GET['token']);

            // Verify token
            $stored_attachment_id = get_transient('brmedia_download_token_' . $token);
            if ($stored_attachment_id && $stored_attachment_id == $attachment_id) {
                $file_path = get_attached_file($attachment_id);
                if (file_exists($file_path)) {
                    // Increment download count
                    $download_count = get_post_meta($attachment_id, 'brmedia_download_count', true);
                    $download_count = $download_count ? intval($download_count) + 1 : 1;
                    update_post_meta($attachment_id, 'brmedia_download_count', $download_count);

                    // Serve the file
                    header('Content-Type: ' . mime_content_type($file_path));
                    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                    readfile($file_path);
                    delete_transient('brmedia_download_token_' . $token);
                    exit;
                }
            }

            wp_die('Invalid or expired download link.', 'Download Error', array('response' => 403));
        }
    }
}