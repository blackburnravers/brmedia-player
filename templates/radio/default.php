<?php
/**
 * Default Radio Template
 *
 * Embeds a live radio stream with real-time track information and schedule display.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve radio data
$post_id = get_the_ID();
$stream_url = get_post_meta($post_id, 'brmedia_radio_stream_url', true);
$track_api_url = get_option('brmedia_radio_track_api', '');

// Validate required data
if (empty($stream_url)) {
    echo '<p>' . esc_html__('Radio stream URL is missing.', 'brmedia') . '</p>';
    return;
}

// Enqueue styles and scripts
wp_enqueue_style('brmedia-frontend');
wp_enqueue_script('brmedia-frontend');
?>

<div class="brmedia-radio-default" data-stream="<?php echo esc_attr($stream_url); ?>" aria-label="<?php echo esc_attr__('Radio player', 'brmedia'); ?>">
    <!-- Audio Player -->
    <div class="player-container">
        <audio id="radio-player" controls preload="none">
            <source src="<?php echo esc_url($stream_url); ?>" type="audio/mpeg">
            <?php esc_html_e('Your browser does not support the audio element.', 'brmedia'); ?>
        </audio>
    </div>

    <!-- Track Information -->
    <div class="track-info">
        <h3><?php esc_html_e('Now Playing:', 'brmedia'); ?> <span id="current-track"><?php esc_html_e('Loading...', 'brmedia'); ?></span></h3>
    </div>

    <!-- Schedule Information -->
    <div class="schedule">
        <h4><?php esc_html_e('Schedule', 'brmedia'); ?></h4>
        <p><?php esc_html_e('Next show:', 'brmedia'); ?> <span id="next-show"><?php esc_html_e('Loading...', 'brmedia'); ?></span></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.brmedia-radio-default');
    const trackApiUrl = '<?php echo esc_js($track_api_url); ?>';
    const player = document.getElementById('radio-player');

    function fetchTrackInfo() {
        if (!trackApiUrl) {
            document.getElementById('current-track').textContent = '<?php echo esc_js(__('Track info unavailable', 'brmedia')); ?>';
            document.getElementById('next-show').textContent = '<?php echo esc_js(__('Schedule unavailable', 'brmedia')); ?>';
            return;
        }

        fetch(trackApiUrl, { method: 'GET', headers: { 'Accept': 'application/json' } })
            .then(response => {
                if (!response.ok) throw new Error('Track API request failed');
                return response.json();
            })
            .then(data => {
                document.getElementById('current-track').textContent = data.current_track || '<?php echo esc_js(__('Unknown', 'brmedia')); ?>';
                document.getElementById('next-show').textContent = data.next_show || '<?php echo esc_js(__('Unknown', 'brmedia')); ?>';
            })
            .catch(error => {
                console.error('Error fetching track info:', error);
                document.getElementById('current-track').textContent = '<?php echo esc_js(__('Failed to load', 'brmedia')); ?>';
            });
    }

    // Initial fetch and periodic updates (every 30 seconds)
    fetchTrackInfo();
    setInterval(fetchTrackInfo, 30000);

    // Playback event listeners
    player.addEventListener('play', function() {
        console.log('Radio is playing');
    });

    player.addEventListener('error', function() {
        console.error('Playback error occurred');
        document.getElementById('current-track').textContent = '<?php echo esc_js(__('Stream unavailable', 'brmedia')); ?>';
    });
});
</script>