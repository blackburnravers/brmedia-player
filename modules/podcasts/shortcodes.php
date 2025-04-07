<?php
/**
 * Podcasts Shortcodes
 * Advanced shortcodes for podcast players and episode lists.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('brmedia_podcast_player', 'brmedia_podcast_player_shortcode');
function brmedia_podcast_player_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 0,
        'waveform' => 'true', // Enable/disable waveform
    ], $atts, 'brmedia_podcast_player');

    $episode_id = intval($atts['id']);
    if (!$episode_id || get_post_type($episode_id) !== 'brmedia_podcast') {
        return '<p>Error: Invalid podcast episode ID</p>';
    }

    $show_waveform = filter_var($atts['waveform'], FILTER_VALIDATE_BOOLEAN);
    return brmedia_podcast_player_template($episode_id, $show_waveform);
}

add_shortcode('brmedia_podcast_list', 'brmedia_podcast_list_shortcode');
function brmedia_podcast_list_shortcode($atts) {
    $atts = shortcode_atts([
        'series_id' => 0,
        'limit' => 10,
    ], $atts, 'brmedia_podcast_list');

    $series_id = intval($atts['series_id']);
    $limit = intval($atts['limit']);
    if (!$series_id || !term_exists($series_id, 'brmedia_podcast_series')) {
        return '<p>Error: Invalid series ID</p>';
    }

    return brmedia_podcast_list_template($series_id, $limit);
}