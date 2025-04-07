<?php
/**
 * Dashboard Page
 * Advanced overview with large cards for module stats, detailed toggles, analytics, and utilities.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Dashboard class
class BRMedia_Dashboard {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_dashboard_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_dashboard_assets']);
        add_action('wp_ajax_brmedia_fetch_analytics', [$this, 'fetch_analytics']);
    }

    // Add dashboard submenu under BRMedia menu
    public function add_dashboard_menu() {
        add_submenu_page(
            'brmedia',           // Parent slug
            'Dashboard',         // Page title
            'Dashboard',         // Menu title
            'manage_options',    // Capability
            'brmedia-dashboard', // Menu slug
            [$this, 'render_dashboard'] // Callback
        );
    }

    // Enqueue styles and scripts
    public function enqueue_dashboard_assets($hook) {
        if ($hook !== 'brmedia_page_brmedia-dashboard') {
            return;
        }
        wp_enqueue_style('brmedia-dashboard-css', plugins_url('assets/css/dashboard.css', __FILE__), [], '1.1.0');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        wp_enqueue_script('brmedia-dashboard-js', plugins_url('assets/js/dashboard.js', __FILE__), ['jquery'], '1.1.0', true);
        wp_localize_script('brmedia-dashboard-js', 'brmediaAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('brmedia_analytics_nonce'),
        ]);
    }

    // Fetch real-time analytics via AJAX
    public function fetch_analytics() {
        check_ajax_referer('brmedia_analytics_nonce', 'nonce');
        $analytics = [
            'total_users'    => get_users(['fields' => 'count']),
            'active_streams' => 10, // Placeholder for actual logic
            'total_plays'    => 5000, // Placeholder
        ];
        wp_send_json_success($analytics);
    }

    // Render the dashboard page
    public function render_dashboard() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Module stats (replace with actual data retrieval)
        $stats = [
            'music'     => ['plays' => 2500, 'downloads' => 450, 'icon' => 'fa-music', 'active' => true],
            'video'     => ['plays' => 1800, 'views' => 2000, 'icon' => 'fa-video', 'active' => false],
            'radio'     => ['streams' => 15, 'listeners' => 300, 'icon' => 'fa-broadcast-tower', 'active' => false],
            'gaming'    => ['views' => 1200, 'players' => 150, 'icon' => 'fa-gamepad', 'active' => false],
            'podcasts'  => ['plays' => 900, 'subscribers' => 80, 'icon' => 'fa-podcast', 'active' => false],
            'chat'      => ['messages' => 3500, 'users' => 200, 'icon' => 'fa-comments', 'active' => false],
            'downloads' => ['downloads' => 700, 'size' => '1.2GB', 'icon' => 'fa-download', 'active' => false],
        ];

        // Get enabled modules and settings
        $enabled_modules = get_option('brmedia_enabled_modules', ['music' => true]);
        $module_settings = get_option('brmedia_module_settings', []);

        ?>
        <div class="wrap">
            <h1>BRMedia Dashboard</h1>
            <div class="brmedia-dashboard">
                <!-- Module Statistics Grid -->
                <section class="brmedia-stats-grid">
                    <h2>Module Overview</h2>
                    <div class="grid-container">
                        <?php foreach ($stats as $module => $data): ?>
                            <div class="brmedia-stat-card <?php echo $data['active'] ? 'active' : ''; ?>">
                                <i class="fas <?php echo esc_attr($data['icon']); ?> fa-2x"></i>
                                <h3><?php echo esc_html(ucfirst($module)); ?></h3>
                                <ul>
                                    <?php foreach ($data as $key => $value): 
                                        if ($key !== 'icon' && $key !== 'active'): ?>
                                            <li><strong><?php echo esc_html(ucfirst(str_replace('_', ' ', $key))); ?>:</strong> <?php echo esc_html($value); ?></li>
                                        <?php endif; 
                                    endforeach; ?>
                                </ul>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=brmedia-' . $module)); ?>" class="button">Manage</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Module Toggles and Settings -->
                <section class="brmedia-module-toggles">
                    <h2>Module Management</h2>
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('brmedia_dashboard_settings');
                        do_settings_sections('brmedia-dashboard');
                        ?>
                        <div class="brmedia-toggle-grid">
                            <?php
                            $modules = array_keys($stats);
                            foreach ($modules as $module): ?>
                                <div class="brmedia-toggle-card">
                                    <label class="toggle-label">
                                        <input type="checkbox" 
                                               name="brmedia_enabled_modules[<?php echo esc_attr($module); ?>]" 
                                               value="1" 
                                               <?php checked(isset($enabled_modules[$module]) && $enabled_modules[$module]); ?>
                                               <?php echo $module === 'music' ? 'disabled' : ''; ?>>
                                        <?php echo esc_html(ucfirst($module)); ?> Module
                                    </label>
                                    <?php if ($module !== 'music'): ?>
                                        <details>
                                            <summary>Advanced Settings</summary>
                                            <label>
                                                <input type="checkbox" 
                                                       name="brmedia_module_settings[<?php echo esc_attr($module); ?>][cache_enabled]" 
                                                       value="1" 
                                                       <?php checked(isset($module_settings[$module]['cache_enabled']) && $module_settings[$module]['cache_enabled']); ?>>
                                                Enable Caching
                                            </label><br>
                                            <label>
                                                Max Items: 
                                                <input type="number" 
                                                       name="brmedia_module_settings[<?php echo esc_attr($module); ?>][max_items]" 
                                                       value="<?php echo esc_attr($module_settings[$module]['max_items'] ?? 50); ?>" 
                                                       min="1" max="1000">
                                            </label>
                                        </details>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p><strong>Note:</strong> The Music module is always enabled.</p>
                        <?php submit_button('Save Settings', 'primary', 'submit', false); ?>
                    </form>
                </section>

                <!-- Performance Analytics -->
                <section class="brmedia-analytics">
                    <h2>Performance Analytics</h2>
                    <div id="analytics-data">
                        <p>Loading analytics...</p>
                    </div>
                    <button id="refresh-analytics" class="button">Refresh Analytics</button>
                </section>

                <!-- Quick Tools -->
                <section class="brmedia-quick-tools">
                    <h2>Quick Tools</h2>
                    <ul>
                        <li><a href="<?php echo esc_url(admin_url('tools.php?page=brmedia-cache')); ?>">Clear Cache</a></li>
                        <li><a href="https://example.com/documentation" target="_blank">Documentation</a></li>
                        <li><a href="https://example.com/support" target="_blank">Support</a></li>
                        <li><a href="#" id="export-settings">Export Settings</a></li>
                    </ul>
                </section>
            </div>
        </div>
        <?php
    }
}

// Register settings
function brmedia_register_dashboard_settings() {
    register_setting('brmedia_dashboard_settings', 'brmedia_enabled_modules', [
        'sanitize_callback' => 'brmedia_sanitize_module_toggles',
    ]);
    register_setting('brmedia_dashboard_settings', 'brmedia_module_settings', [
        'sanitize_callback' => 'brmedia_sanitize_module_settings',
    ]);
}
add_action('admin_init', 'brmedia_register_dashboard_settings');

// Sanitize module toggles
function brmedia_sanitize_module_toggles($input) {
    $sanitized = array_map('boolval', (array) $input);
    $sanitized['music'] = true; // Force Music to be enabled
    return $sanitized;
}

// Sanitize module settings
function brmedia_sanitize_module_settings($input) {
    $sanitized = [];
    foreach ((array) $input as $module => $settings) {
        $sanitized[$module] = [
            'cache_enabled' => boolval($settings['cache_enabled'] ?? false),
            'max_items'     => intval($settings['max_items'] ?? 50),
        ];
    }
    return $sanitized;
}

// Instantiate the dashboard
new BRMedia_Dashboard();