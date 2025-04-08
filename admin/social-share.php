<?php
/**
 * BRMedia Player Social Share Page
 *
 * This file configures social sharing options.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Social Share page callback
 */
function brmedia_social_share_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    ?>
    <div class="wrap">
        <h1>Social Share</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('brmedia_social_share_settings');
            do_settings_sections('brmedia_social_share_settings');
            ?>
            <h2>Platform Settings</h2>
            <label style="display: block; margin-bottom: 10px;">
                <input type="checkbox" name="brmedia_share_twitter" <?php checked(get_option('brmedia_share_twitter', true)); ?> />
                Twitter
            </label>
            <label style="display: block; margin-bottom: 10px;">
                <input type="checkbox" name="brmedia_share_facebook" <?php checked(get_option('brmedia_share_facebook', true)); ?> />
                Facebook
            </label>
            <?php submit_button('Save Social Settings'); ?>
        </form>
    </div>
    <?php
}

/**
 * Register social share settings
 */
function brmedia_register_social_share_settings() {
    register_setting('brmedia_social_share_settings', 'brmedia_share_twitter');
    register_setting('brmedia_social_share_settings', 'brmedia_share_facebook');
}
add_action('admin_init', 'brmedia_register_social_share_settings');