<?php
/**
 * BRMedia Player Analytics Class
 *
 * This class handles analytics data collection and storage.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BRMedia_Analytics {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_brmedia_track_play', array($this, 'track_play'));
        add_action('wp_ajax_nopriv_brmedia_track_play', array($this, 'track_play'));
    }

    /**
     * Track a play event
     */
    public function track_play() {
        check_ajax_referer('brmedia_nonce', 'nonce');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        if (!$post_id) {
            wp_send_json_error('Invalid post ID');
        }

        // Increment play count
        $play_count = get_post_meta($post_id, 'brmedia_play_count', true);
        $play_count = $play_count ? intval($play_count) + 1 : 1;
        update_post_meta($post_id, 'brmedia_play_count', $play_count);

        // Log listener location (requires a geo-IP service)
        $ip = $_SERVER['REMOTE_ADDR'];
        // Example: Use a geo-IP API (not implemented here)
        $location = 'Unknown'; // Placeholder

        $analytics_data = array(
            'time' => current_time('mysql'),
            'ip' => $ip,
            'location' => $location,
        );
        add_post_meta($post_id, 'brmedia_analytics', $analytics_data);

        wp_send_json_success('Play tracked');
    }
}