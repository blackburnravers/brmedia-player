<?php
/**
 * BRMedia Hooks Class
 *
 * Centralizes WordPress hooks for the plugin.
 *
 * @package BRMedia\Includes\Core
 */

namespace BRMedia\Includes\Core;

use BRMedia\Includes\Admin\BRMedia_Admin_Core;
use BRMedia\Includes\Admin\BRMedia_Dashboard;
use BRMedia\Includes\Admin\BRMedia_Settings;

class BRMedia_Hooks {
    /** @var DIContainer Dependency injection container */
    private $di;

    /**
     * Constructor
     *
     * @param DIContainer $di Dependency injection container
     */
    public function __construct($di) {
        $this->di = $di;
        $this->register_hooks();
    }

    /**
     * Registers all plugin hooks
     */
    public function register_hooks() {
        // Admin Core Hooks
        $admin_core = $this->di->get('admin');
        add_action('admin_menu', [$admin_core, 'register_admin_menu']);

        // Dashboard Hooks
        $dashboard = new BRMedia_Dashboard();
        add_action('admin_menu', [$dashboard, 'register_dashboard']);

        // Settings Hooks
        $settings = new BRMedia_Settings();
        add_action('admin_init', [$settings, 'register_settings']);

        // Custom AJAX Hooks
        add_action('wp_ajax_brmedia_save_settings', [$this, 'save_settings']);
    }

    /**
     * AJAX handler to save settings
     */
    public function save_settings() {
        check_ajax_referer('brmedia_save_settings', 'nonce');
        $data = $_POST['data'] ?? [];
        update_option('brmedia_options', $data);
        wp_send_json_success(['message' => 'Settings saved']);
    }
}