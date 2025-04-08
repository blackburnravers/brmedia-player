<?php
/**
 * BRMedia Player Radio Module
 *
 * Manages radio streaming configurations, DJ timetables, and the [brmedia_radio] shortcode.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register radio settings in the admin
 */
function brmedia_register_radio_settings() {
    register_setting('brmedia_radio_settings', 'brmedia_radio_stream_url', 'esc_url_raw');
    register_setting('brmedia_radio_settings', 'brmedia_radio_stream_type', 'sanitize_text_field');
    register_setting('brmedia_radio_settings', 'brmedia_radio_dj_timetable', 'brmedia_sanitize_timetable');
}
add_action('admin_init', 'brmedia_register_radio_settings');

/**
 * Sanitize DJ timetable array
 *
 * @param array $input The input data
 * @return array Sanitized timetable
 */
function brmedia_sanitize_timetable($input) {
    $sanitized = array();
    if (is_array($input)) {
        foreach ($input as $slot) {
            $sanitized[] = array(
                'dj_name'    => sanitize_text_field($slot['dj_name']),
                'day'        => sanitize_text_field($slot['day']),
                'start_time' => sanitize_text_field($slot['start_time']),
                'end_time'   => sanitize_text_field($slot['end_time']),
            );
        }
    }
    return $sanitized;
}

/**
 * Define the [brmedia_radio] shortcode
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output for the radio player
 */
function brmedia_radio_shortcode($atts) {
    $atts = shortcode_atts(array(
        'template' => 'default',
    ), $atts);

    $stream_url = get_option('brmedia_radio_stream_url', '');
    $stream_type = get_option('brmedia_radio_stream_type', 'shoutcast');

    if (!$stream_url) {
        return '<p>No radio stream configured.</p>';
    }

    $template_path = BRMedia_Helpers::get_template_path('radio', $atts['template']);
    if (!file_exists($template_path)) {
        return '<p>Radio template not found.</p>';
    }

    ob_start();
    include $template_path;
    return ob_get_clean();
}
add_shortcode('brmedia_radio', 'brmedia_radio_shortcode');

/**
 * Display DJ timetable
 *
 * @return string HTML output for the timetable
 */
function brmedia_display_dj_timetable() {
    $timetable = get_option('brmedia_radio_dj_timetable', array());
    if (empty($timetable)) {
        return '<p>No DJ timetable set.</p>';
    }

    $html = '<table class="brmedia-dj-timetable">';
    $html .= '<thead><tr><th>DJ Name</th><th>Day</th><th>Time</th></tr></thead>';
    $html .= '<tbody>';
    foreach ($timetable as $slot) {
        $html .= '<tr>';
        $html .= '<td>' . esc_html($slot['dj_name']) . '</td>';
        $html .= '<td>' . esc_html($slot['day']) . '</td>';
        $html .= '<td>' . esc_html($slot['start_time']) . ' - ' . esc_html($slot['end_time']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

/**
 * Shortcode to display DJ timetable
 *
 * @return string HTML output
 */
function brmedia_dj_timetable_shortcode() {
    return brmedia_display_dj_timetable();
}
add_shortcode('brmedia_dj_timetable', 'brmedia_dj_timetable_shortcode');