<?php
/**
 * Video Shortcodes
 * Advanced shortcodes for video players.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('brmedia_video', 'brmedia_video_shortcode');
function brmedia_video_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 0,
        'template' => 'default', // Options: 'default', 'popup'
    ], $atts, 'brmedia_video');

    $video_id = intval($atts['id']);
    if (!$video_id || get_post_type($video_id) !== 'brmedia_video') {
        return '<p>Error: Invalid video ID</p>';
    }

    return brmedia_video_player_template($video_id, $atts['template']);
}