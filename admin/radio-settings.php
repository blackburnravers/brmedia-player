<?php
/**
 * BRMedia Player Radio Settings Page
 *
 * This file manages radio streaming configurations and DJ timetables.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Radio page callback
 */
function brmedia_radio_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    ?>
    <div class="wrap">
        <h1>Radio Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('brmedia_radio_settings');
            do_settings_sections('brmedia_radio_settings');
            $radio_source = get_option('brmedia_radio_source', 'shoutcast');
            ?>
            <h2>Streaming Sources</h2>
            <select name="brmedia_radio_source">
                <option value="shoutcast" <?php selected($radio_source, 'shoutcast'); ?>>Shoutcast</option>
                <option value="icecast" <?php selected($radio_source, 'icecast'); ?>>Icecast</option>
                <option value="winamp" <?php selected($radio_source, 'winamp'); ?>>Winamp</option>
                <option value="windows" <?php selected($radio_source, 'windows'); ?>>Windows Media Encoder</option>
                <option value="facebook" <?php selected($radio_source, 'facebook'); ?>>Facebook Live</option>
                <option value="youtube" <?php selected($radio_source, 'youtube'); ?>>YouTube Live</option>
            </select>

            <h2>DJ Timetables</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>DJ Name</th>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Placeholder for timetable rows -->
                    <tr>
                        <td><input type="text" name="brmedia_dj_name[]" /></td>
                        <td><select name="brmedia_dj_day[]">
                            <?php foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
                                echo "<option value='$day'>$day</option>";
                            } ?>
                        </select></td>
                        <td><input type="time" name="brmedia_dj_start[]" /></td>
                        <td><input type="time" name="brmedia_dj_end[]" /></td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button('Save Radio Settings'); ?>
        </form>
    </div>
    <?php
}

/**
 * Register radio settings
 */
function brmedia_register_radio_settings() {
    register_setting('brmedia_radio_settings', 'brmedia_radio_source');
    // Add settings for DJ timetables if needed
}
add_action('admin_init', 'brmedia_register_radio_settings');