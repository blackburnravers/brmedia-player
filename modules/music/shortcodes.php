<?php
/**
 * Music Shortcodes
 * Advanced shortcodes for music players and playlists.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('brmedia_music', 'brmedia_music_shortcode');
function brmedia_music_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 0,
        'template' => 'default', // Options: default, compact, playlist
    ], $atts, 'brmedia_music');

    $id = intval($atts['id']);
    if (!$id || get_post_type($id) !== 'brmedia_music') {
        return '<p>Error: Invalid track ID</p>';
    }

    return brmedia_music_template($id, $atts['template']);
}

add_shortcode('brmedia_music_playlist', 'brmedia_music_playlist_shortcode');
function brmedia_music_playlist_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 0,
    ], $atts, 'brmedia_music_playlist');

    $playlist_id = intval($atts['id']);
    if (!$playlist_id || !term_exists($playlist_id, 'brmedia_playlist')) {
        return '<p>Error: Invalid playlist ID</p>';
    }

    return brmedia_music_playlist_template($playlist_id);
}