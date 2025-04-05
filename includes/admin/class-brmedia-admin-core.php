<?php
/**
 * BRMedia Admin Core Class
 *
 * This class handles the core admin functionality, including menu registration and settings initialization.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

use BRMedia\Includes\Core\DIContainer;

class BRMedia_Admin_Core {
    /** @var DIContainer Dependency injection container */
    private $di;

    /**
     * Constructor
     *
     * @param DIContainer $di Dependency injection container
     */
    public function __construct(DIContainer $di) {
        $this->di = $di;
        $this->register_admin_menu();
        $this->register_settings();
    }

    /**
     * Registers the admin menu and submenus
     */
    private function register_admin_menu() {
        add_action('admin_menu', function() {
            add_menu_page(
                'BRMedia Dashboard',          // Page title
                'BRMedia',                    // Menu title
                'manage_options',             // Capability
                'brmedia-dashboard',          // Menu slug
                [$this, 'render_dashboard'],  // Callback function
                'dashicons-media-audio',      // Icon
                20                            // Position
            );
            add_submenu_page(
                'brmedia-dashboard',          // Parent slug
                'Settings',                   // Page title
                'Settings',                   // Menu title
                'manage_options',             // Capability
                'brmedia-settings',           // Menu slug
                [$this, 'render_settings']    // Callback function
            );
        });
    }

    /**
     * Renders the dashboard page
     */
    public function render_dashboard() {
        echo '<div class="wrap"><h1>BRMedia Dashboard</h1></div>';
        // Add additional dashboard content here as needed
    }

    /**
     * Renders the settings page
     */
    public function render_settings() {
        echo '<div class="wrap"><h1>BRMedia Settings</h1></div>';
        // Add settings form here as needed
    }

    /**
     * Registers plugin settings
     */
    private function register_settings() {
        add_action('admin_init', function() {
            register_setting('brmedia_settings', 'brmedia_options');
            add_settings_section(
                'brmedia_main_section',    // ID
                'Main Settings',           // Title
                null,                      // Callback
                'brmedia-settings'         // Page
            );
            add_settings_field(
                'brmedia_api_key',         // ID
                'API Key',                 // Title
                [$this, 'api_key_callback'], // Callback
                'brmedia-settings',        // Page
                'brmedia_main_section'     // Section
            );
        });
    }

    /**
     * Callback for the API key settings field
     */
    public function api_key_callback() {
        $options = get_option('brmedia_options');
        $value = isset($options['api_key']) ? esc_attr($options['api_key']) : '';
        echo "<input type='text' name='brmedia_options[api_key]' value='$value'>";
    }
}