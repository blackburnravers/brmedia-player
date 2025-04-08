<?php
/**
 * BRMedia Player Widgets & SEO Page
 *
 * This file manages widget settings and SEO metadata.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Widgets & SEO page callback
 */
function brmedia_widgets_seo_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    ?>
    <div class="wrap">
        <h1>Widgets & SEO</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('brmedia_widgets_seo_settings');
            do_settings_sections('brmedia_widgets_seo_settings');
            ?>
            <h2>Widget Settings</h2>
            <label><input type="checkbox" name="brmedia_widget_tracklist" <?php checked(get_option('brmedia_widget_tracklist', true)); ?> /> Enable Tracklist Widget</label>
            <h2>SEO Metadata</h2>
            <label><input type="checkbox" name="brmedia_seo_opengraph" <?php checked(get_option('brmedia_seo_opengraph', true)); ?> /> Enable Open Graph Tags</label>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

/**
 * Register widgets and SEO settings
 */
function brmedia_register_widgets_seo_settings() {
    register_setting('brmedia_widgets_seo_settings', 'brmedia_widget_tracklist');
    register_setting('brmedia_widgets_seo_settings', 'brmedia_seo_opengraph');
}
add_action('admin_init', 'brmedia_register_widgets_seo_settings');