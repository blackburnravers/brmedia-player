<?php
/**
 * Statistics & Analytics Page
 * Advanced analytics dashboard with real-time data, charts, filters, and export options.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Analytics class
class BRMedia_Analytics {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_analytics_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_analytics_assets']);
        add_action('wp_ajax_brmedia_get_analytics_data', [$this, 'get_analytics_data']);
    }

    // Add analytics submenu under BRMedia menu
    public function add_analytics_menu() {
        add_submenu_page(
            'brmedia',             // Parent slug
            'Statistics & Analytics', // Page title
            'Analytics',           // Menu title
            'manage_options',      // Capability
            'brmedia-analytics',   // Menu slug
            [$this, 'render_analytics_page'] // Callback
        );
    }

    // Enqueue styles and scripts (including Chart.js for charts)
    public function enqueue_analytics_assets($hook) {
        if ($hook !== 'brmedia_page_brmedia-analytics') {
            return;
        }
        wp_enqueue_style('brmedia-analytics-css', plugins_url('assets/css/analytics.css', __FILE__), [], '1.1.0');
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], '3.7.0', true);
        wp_enqueue_script('brmedia-analytics-js', plugins_url('assets/js/analytics.js', __FILE__), ['jquery', 'chart-js'], '1.1.0', true);
        wp_localize_script('brmedia-analytics-js', 'brmediaAnalytics', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('brmedia_analytics_nonce'),
        ]);
    }

    // AJAX handler to fetch analytics data
    public function get_analytics_data() {
        check_ajax_referer('brmedia_analytics_nonce', 'nonce');
        // Placeholder for actual database queries (e.g., from a custom table)
        $data = [
            'plays' => [
                'total' => 5000,
                'by_module' => ['music' => 2500, 'video' => 1800, 'podcasts' => 700],
            ],
            'downloads' => [
                'total' => 1200,
                'by_file_type' => ['mp3' => 800, 'mp4' => 400],
            ],
            'active_streams' => 10,
            'listener_locations' => ['US' => 40, 'UK' => 20, 'DE' => 15, 'FR' => 10, 'Other' => 15],
            'device_breakdown' => ['Desktop' => 60, 'Mobile' => 30, 'Tablet' => 10],
            'browser_breakdown' => ['Chrome' => 50, 'Firefox' => 20, 'Safari' => 15, 'Edge' => 10, 'Other' => 5],
        ];
        wp_send_json_success($data);
    }

    // Render the analytics page
    public function render_analytics_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        ?>
        <div class="wrap">
            <h1>Statistics & Analytics</h1>
            <div class="brmedia-analytics">
                <!-- Filters -->
                <section class="brmedia-filters">
                    <h2>Filters</h2>
                    <form id="analytics-filters">
                        <label>Date Range:
                            <input type="date" name="start_date" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                            to
                            <input type="date" name="end_date" value="<?php echo date('Y-m-d'); ?>">
                        </label>
                        <label>Module:
                            <select name="module">
                                <option value="all">All</option>
                                <option value="music">Music</option>
                                <option value="video">Video</option>
                                <option value="radio">Radio</option>
                                <option value="gaming">Gaming</option>
                                <option value="podcasts">Podcasts</option>
                                <option value="chat">Chat</option>
                                <option value="downloads">Downloads</option>
                            </select>
                        </label>
                        <button type="button" id="apply-filters" class="button">Apply Filters</button>
                    </form>
                </section>

                <!-- Summary Stats -->
                <section class="brmedia-summary-stats">
                    <h2>Summary</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Total Plays</h3>
                            <p id="total-plays">0</p>
                        </div>
                        <div class="stat-card">
                            <h3>Total Downloads</h3>
                            <p id="total-downloads">0</p>
                        </div>
                        <div class="stat-card">
                            <h3>Active Streams</h3>
                            <p id="active-streams">0</p>
                        </div>
                    </div>
                </section>

                <!-- Charts -->
                <section class="brmedia-charts">
                    <h2>Analytics Charts</h2>
                    <div class="chart-container">
                        <canvas id="plays-chart"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="downloads-chart"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="locations-chart"></canvas>
                    </div>
                </section>

                <!-- Data Tables -->
                <section class="brmedia-data-tables">
                    <h2>Detailed Breakdowns</h2>
                    <div class="table-container">
                        <h3>Listener Locations</h3>
                        <table class="wp-list-table widefat fixed striped" id="locations-table">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="table-container">
                        <h3>Device Breakdown</h3>
                        <table class="wp-list-table widefat fixed striped" id="devices-table">
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="table-container">
                        <h3>Browser Breakdown</h3>
                        <table class="wp-list-table widefat fixed striped" id="browsers-table">
                            <thead>
                                <tr>
                                    <th>Browser</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </section>

                <!-- Export Options -->
                <section class="brmedia-export">
                    <h2>Export Data</h2>
                    <button id="export-csv" class="button">Export as CSV</button>
                    <button id="export-pdf" class="button">Export as PDF</button>
                </section>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                // Fetch and display analytics data
                function fetchAnalytics() {
                    const filters = $('#analytics-filters').serialize();
                    $.post(brmediaAnalytics.ajax_url, {
                        action: 'brmedia_get_analytics_data',
                        nonce: brmediaAnalytics.nonce,
                        filters: filters
                    }, function(response) {
                        if (response.success) {
                            const data = response.data;
                            $('#total-plays').text(data.plays.total);
                            $('#total-downloads').text(data.downloads.total);
                            $('#active-streams').text(data.active_streams);

                            // Plays chart
                            const playsCtx = document.getElementById('plays-chart').getContext('2d');
                            new Chart(playsCtx, {
                                type: 'bar',
                                data: {
                                    labels: Object.keys(data.plays.by_module),
                                    datasets: [{
                                        label: 'Plays by Module',
                                        data: Object.values(data.plays.by_module),
                                        backgroundColor: '#0073aa',
                                    }]
                                },
                                options: { scales: { y: { beginAtZero: true } } }
                            });

                            // Downloads chart
                            const downloadsCtx = document.getElementById('downloads-chart').getContext('2d');
                            new Chart(downloadsCtx, {
                                type: 'pie',
                                data: {
                                    labels: Object.keys(data.downloads.by_file_type),
                                    datasets: [{
                                        label: 'Downloads by File Type',
                                        data: Object.values(data.downloads.by_file_type),
                                        backgroundColor: ['#0073aa', '#00a0d2'],
                                    }]
                                }
                            });

                            // Locations chart
                            const locationsCtx = document.getElementById('locations-chart').getContext('2d');
                            new Chart(locationsCtx, {
                                type: 'doughnut',
                                data: {
                                    labels: Object.keys(data.listener_locations),
                                    datasets: [{
                                        label: 'Listener Locations',
                                        data: Object.values(data.listener_locations),
                                        backgroundColor: ['#0073aa', '#00a0d2', '#1e73be', '#2ea2cc', '#ccc'],
                                    }]
                                }
                            });

                            // Update tables
                            const locationsTable = $('#locations-table tbody');
                            locationsTable.empty();
                            for (const [country, percentage] of Object.entries(data.listener_locations)) {
                                locationsTable.append(`<tr><td>${country}</td><td>${percentage}%</td></tr>`);
                            }
                            const devicesTable = $('#devices-table tbody');
                            devicesTable.empty();
                            for (const [device, percentage] of Object.entries(data.device_breakdown)) {
                                devicesTable.append(`<tr><td>${device}</td><td>${percentage}%</td></tr>`);
                            }
                            const browsersTable = $('#browsers-table tbody');
                            browsersTable.empty();
                            for (const [browser, percentage] of Object.entries(data.browser_breakdown)) {
                                browsersTable.append(`<tr><td>${browser}</td><td>${percentage}%</td></tr>`);
                            }
                        }
                    });
                }

                // Initial fetch
                fetchAnalytics();

                // Apply filters button
                $('#apply-filters').on('click', fetchAnalytics);

                // Export buttons (placeholders)
                $('#export-csv').on('click', function() {
                    alert('CSV export functionality coming soon.');
                });
                $('#export-pdf').on('click', function() {
                    alert('PDF export functionality coming soon.');
                });
            });
        </script>
        <?php
    }
}

// Instantiate the analytics admin
new BRMedia_Analytics();