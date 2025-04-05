<?php
/**
 * BRMedia Templates Class
 *
 * Manages basic template settings and admin interface.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Templates {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_templates_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Registers the templates management page
     */
    public function add_templates_page() {
        add_submenu_page(
            'brmedia-dashboard',           // Parent slug
            'Templates',                   // Page title
            'Templates',                   // Menu title
            'manage_options',              // Capability
            'brmedia-templates',           // Menu slug
            [$this, 'render_templates_page'] // Callback
        );
    }

    /**
     * Registers template settings
     */
    public function register_settings() {
        register_setting('brmedia_templates', 'brmedia_template_options', [$this, 'sanitize_options']);
        add_settings_section(
            'brmedia_template_section',    // ID
            'Template Settings',           // Title
            null,                          // Callback
            'brmedia-templates'            // Page
        );
        add_settings_field(
            'brmedia_audio_template',      // ID
            'Default Audio Template',      // Title
            [$this, 'audio_template_callback'], // Callback
            'brmedia-templates',           // Page
            'brmedia_template_section'     // Section
        );
    }

    /**
     * Sanitizes template options
     *
     * @param array $input Input data
     * @return array Sanitized data
     */
    public function sanitize_options($input) {
        $sanitized = [];
        $sanitized['audio_template'] = sanitize_text_field($input['audio_template']);
        return $sanitized;
    }

    /**
     * Callback for audio template setting
     */
    public function audio_template_callback() {
        $options = get_option('brmedia_template_options', []);
        $value = isset($options['audio_template']) ? $options['audio_template'] : 'audio-default';
        ?>
        <select name="brmedia_template_options[audio_template]">
            <option value="audio-default" <?php selected($value, 'audio-default'); ?>>Default</option>
            <option value="audio-compact" <?php selected($value, 'audio-compact'); ?>>Compact</option>
        </select>
        <?php
    }

    /**
     * Renders the templates management page
     */
    public function render_templates_page() {
        ?>
        <div class="wrap brmedia-settings">
            <h1>Template Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('brmedia_templates');
                do_settings_sections('brmedia-templates');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}