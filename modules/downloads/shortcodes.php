<?php
/**
 * Downloads Shortcodes
 * Advanced shortcodes for single and bulk downloads.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('brmedia_download', 'brmedia_download_shortcode');
function brmedia_download_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 0,
        'template' => 'big', // Options: big, small, massive, icon-only, progress-bar
        'label' => 'Download',
        'gated' => '', // Options: email, social, login
    ], $atts, 'brmedia_download');

    $id = intval($atts['id']);
    if (!$id || !get_attached_file($id)) {
        return '<p>Error: Invalid file ID</p>';
    }

    return brmedia_download_template($id, $atts['template'], $atts['label'], $atts['gated']);
}

add_shortcode('brmedia_download_bulk', 'brmedia_download_bulk_shortcode');
function brmedia_download_bulk_shortcode($atts) {
    $atts = shortcode_atts([
        'file_ids' => '',
    ], $atts, 'brmedia_download_bulk');

    return brmedia_download_bulk_template($atts['file_ids']);
}