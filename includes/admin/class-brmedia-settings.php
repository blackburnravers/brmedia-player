<?php
/**
 * BRMedia Settings Class
 *
 * Handles basic plugin settings.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Settings {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Registers basic settings
     */
    public function register_settings() {
        register_setting('brmedia_settings', 'brmedia_options', [$this, 'sanitize_options']);
        add_settings_section(
            'brmedia_main_section',     // ID
            'General Settings',         // Title
            null,                       // Callback
            'brmedia-settings'          // Page
        );
        add_settings_field(
            'brmedia_default_template', // ID
            'Default Template',         // Title
            [$this, 'default_template_callback'], // Callback
            'brmedia-settings',         // Page
            'brmedia_main_section'      // Section
        );
    }

    /**
     * Sanitizes settings input
     *
     * @param array $input Input data
     * @return array Sanitized data
     */
    public function sanitize_options($input) {
        $sanitized = [];
        $sanitized['default_template'] = sanitize_text_field($input['default_template']);
        return $sanitized;
    }

    /**
     * Callback for default template setting
     */
    public function default_template_callback() {
        $options = get_option('brmedia_options', []);
        $value = isset($options['default_template']) ? $options['default_template'] : 'audio-default';
        ?>
        <select name="brmedia_options[default_template]">
            <option value="audio-default" <?php selected($value, 'audio-default'); ?>>Default Audio</option>
            <option value="video-default" <?php selected($value, 'video-default'); ?>>Default Video</option>
        </select>
        <?php
    }
}