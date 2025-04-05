<?php
/**
 * BRMedia Downloads Class
 *
 * Manages basic download functionality and tracking.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Downloads {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_brmedia_track_download', [$this, 'track_download']);
    }

    /**
     * Generates a download button
     *
     * @param int $media_id Media post ID
     * @return string HTML button
     */
    public function generate_download_button($media_id) {
        $url = get_post_meta($media_id, '_brmedia_download_url', true);
        return sprintf(
            '<a href="%s" class="brmedia-download" data-media-id="%d">Download</a>',
            esc_url($url),
            esc_attr($media_id)
        );
    }

    /**
     * Tracks download events via AJAX
     */
    public function track_download() {
        $media_id = intval($_POST['mediaId']);
        $downloads = get_option('brmedia_total_downloads', 0);
        update_option('brmedia_total_downloads', $downloads + 1);
        wp_send_json_success();
    }
}