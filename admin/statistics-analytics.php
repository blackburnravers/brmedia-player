<?php
/**
 * BRMedia Player Statistics & Analytics Page
 *
 * This file displays analytics data such as play counts and downloads.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Statistics & Analytics page callback
 */
function brmedia_analytics_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
    ?>
    <div class="wrap">
        <h1>Statistics & Analytics</h1>
        <div class="brmedia-analytics">
            <h2>Play Stats</h2>
            <canvas id="playChart" width="400" height="200"></canvas>
            <script>
                const ctx = document.getElementById('playChart').getContext('2d');
                const playChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Music', 'Video', 'Radio'],
                        datasets: [{
                            label: 'Plays',
                            data: [<?php echo get_option('brmedia_music_plays', 0); ?>, <?php echo get_option('brmedia_video_plays', 0); ?>, <?php echo get_option('brmedia_radio_plays', 0); ?>],
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: { scales: { y: { beginAtZero: true } } }
                });
            </script>
        </div>
    </div>
    <?php
}