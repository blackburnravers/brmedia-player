<?php
/**
 * Chat Shortcodes
 * Advanced shortcodes for chat rooms with customization and integration.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('brmedia_chat', 'brmedia_chat_shortcode');
function brmedia_chat_shortcode($atts) {
    $atts = shortcode_atts([
        'room' => 'default',
        'height' => '400px',
        'width' => '300px',
        'theme' => 'light', // Options: light, dark
    ], $atts, 'brmedia_chat');

    // Validate attributes
    $atts['height'] = preg_match('/^\d+(px|%)$/', $atts['height']) ? $atts['height'] : '400px';
    $atts['width'] = preg_match('/^\d+(px|%)$/', $atts['width']) ? $atts['width'] : '300px';

    return brmedia_chat_template($atts['room'], $atts['height'], $atts['width'], $atts['theme']);
}