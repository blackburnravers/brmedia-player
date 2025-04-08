<?php
/**
 * BRMedia Player Shortcodes Manager Page
 *
 * This file lists all available shortcodes with previews and copy functionality.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Shortcodes Manager page callback
 */
function brmedia_shortcodes_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    ?>
    <div class="wrap">
        <h1>Shortcodes Manager</h1>
        <div class="brmedia-shortcode" style="margin-bottom: 20px;">
            <h2>Audio Player</h2>
            <code id="shortcode-audio">[brmedia_audio id="123"]</code>
            <button class="button" onclick="navigator.clipboard.writeText('[brmedia_audio id=123]')">Copy</button>
            <div class="brmedia-preview" style="margin-top: 10px;">
                <?php echo do_shortcode('[brmedia_audio id="123"]'); ?>
            </div>
        </div>
        <!-- Add more shortcodes as needed -->
    </div>
    <?php
}