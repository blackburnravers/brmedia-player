<?php
/**
 * Gaming Shortcodes
 * Advanced shortcodes for gaming content.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('brmedia_gaming', 'brmedia_gaming_shortcode');
function brmedia_gaming_shortcode($atts) {
    $atts = shortcode_atts([
        'type' => 'twitch', // Options: twitch, youtube
        'id' => '',
        'width' => '600',
        'height' => '400',
    ], $atts, 'brmedia_gaming');

    return brmedia_gaming_template($atts['type'], $atts['id'], $atts['width'], $atts['height']);
}