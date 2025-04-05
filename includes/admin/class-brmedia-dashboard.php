<?php
/**
 * BRMedia Dashboard Class
 *
 * Manages the admin dashboard with media statistics and quick access links.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Dashboard {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'register_dashboard']);
    }

    /**
     * Registers the dashboard page
     */
    public function register_dashboard() {
        add_submenu_page(
            'brmedia-dashboard',           // Parent slug
            'Dashboard Overview',          // Page title
            'Overview',                    // Menu title
            'manage_options',              // Capability
            'brmedia-dashboard',           // Menu slug (matches parent to override)
            [$this, 'render_dashboard']    // Callback function
        );
    }

    /**
     * Renders the dashboard page
     */
    public function render_dashboard() {
        $stats = $this->get_media_stats();
        ?>
        <div class="wrap brmedia-dashboard">
            <h1>BRMedia Dashboard</h1>
            <div class="widget">
                <h2>Total Tracks</h2>
                <p><?php echo esc_html($stats['tracks']); ?></p>
            </div>
            <div class="widget">
                <h2>Total Plays</h2>
                <p><?php echo esc_html($stats['plays']); ?></p>
            </div>
            <div class="widget">
                <h2>Total Downloads</h2>
                <p><?php echo esc_html($stats['downloads']); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Retrieves media statistics
     *
     * @return array Media stats
     */
    private function get_media_stats() {
        $tracks = wp_count_posts('brmusic')->publish + wp_count_posts('brvideo')->publish;
        $plays = get_option('brmedia_total_plays', 0); // Placeholder
        $downloads = get_option('brmedia_total_downloads', 0); // Placeholder
        return [
            'tracks' => $tracks,
            'plays' => $plays,
            'downloads' => $downloads,
        ];
    }
}