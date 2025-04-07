<?php
/**
 * Radio Shortcodes
 * Advanced shortcodes for radio players and timetables.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('brmedia_radio_player', 'brmedia_radio_player_shortcode');
function brmedia_radio_player_shortcode($atts) {
    $atts = shortcode_atts([
        'source' => '',
    ], $atts, 'brmedia_radio_player');

    return brmedia_radio_player_template($atts['source']);
}

add_shortcode('brmedia_radio_timetable', 'brmedia_radio_timetable_shortcode');
function brmedia_radio_timetable_shortcode($atts) {
    $atts = shortcode_atts([
        'show_future' => 'true',
    ], $atts, 'brmedia_radio_timetable');

    $show_future = filter_var($atts['show_future'], FILTER_VALIDATE_BOOLEAN);
    return brmedia_radio_timetable_template($show_future);
}