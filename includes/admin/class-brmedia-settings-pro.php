<?php
/**
 * BRMedia Settings Pro Class
 *
 * Handles advanced plugin settings with multisite support.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Settings_Pro {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'add_settings_page']);
    }

    /**
     * Registers settings page under BRMedia menu
     */
    public function add_settings_page() {
        add_submenu_page(
            'brmedia-dashboard',           // Parent slug
            'Pro Settings',                // Page title
            'Pro Settings',                // Menu title
            'manage_options',              // Capability
            'brmedia-settings-pro',        // Menu slug
            [$this, 'render_settings_page'] // Callback
        );
    }

    /**
     * Registers settings with multisite support
     */
    public function register_settings() {
        register_setting('brmedia_settings_pro', 'brmedia_pro_options', [$this, 'sanitize_options']);
        add_settings_section(
            'brmedia_pro_section',      // ID
            'Advanced Settings',        // Title
            null,                       // Callback
            'brmedia-settings-pro'      // Page
        );
        add_settings_field(
            'brmedia_autoplay',         // ID
            'Enable Autoplay',          // Title
            [$this, 'autoplay_callback'], // Callback
            'brmedia-settings-pro',     // Page
            'brmedia_pro_section'       // Section
        );
        add_settings_field(
            'brmedia_network_sync',     // ID
            'Network Sync',             // Title
            [$this, 'network_sync_callback'], // Callback
            'brmedia-settings-pro',     // Page
            'brmedia_pro_section'       // Section
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
        $sanitized['autoplay'] = isset($input['autoplay']) ? 1 : 0;
        $sanitized['network_sync'] = isset($input['network_sync']) ? 1 : 0;
        return $sanitized;
    }

    /**
     * Callback for autoplay setting
     */
    public function autoplay_callback() {
        $options = get_option('brmedia_pro_options', []);
        $checked = isset($options['autoplay']) && $options['autoplay'] ? 'checked' : '';
        echo "<input type='checkbox' name='brmedia_pro_options[autoplay]' value='1' $checked>";
    }

    /**
     * Callback for network sync setting (multisite)
     */
    public function network_sync_callback() {
        $options = get_option('brmedia_pro_options', []);
        $checked = isset($options['network_sync']) && $options['network_sync'] ? 'checked' : '';
        echo "<input type='checkbox' name='brmedia_pro_options[network_sync]' value='1' $checked> <span>Sync settings across network</span>";
    }

    /**
     * Renders the settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap brmedia-settings">
            <h1>BRMedia Pro Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('brmedia_settings_pro');
                do_settings_sections('brmedia-settings-pro');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}