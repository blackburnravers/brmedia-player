<?php
/**
 * Default Radio Player Template
 * A highly customizable radio player with streaming controls, live DJ info, progress bar, and multi-service streaming support.
 *
 * @package BRMediaPlayer
 * @subpackage Templates
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Retrieve template settings from ACP
$settings = get_option('brmedia_template_settings', []);
$radio_settings = $settings['radio'] ?? [];
$primary_color = $radio_settings['primary_color'] ?? '#0073aa';
$secondary_color = $radio_settings['secondary_color'] ?? '#ffffff';
$player_width = $radio_settings['player_width'] ?? '100%';
$player_height = $radio_settings['player_height'] ?? 'auto';
$font_family = $radio_settings['font_family'] ?? 'Arial, sans-serif';
$stream_url = $radio_settings['stream_url'] ?? '';
$auto_play = $radio_settings['auto_play'] ?? false;
$volume_level = $radio_settings['volume_level'] ?? 0.8; // Default volume (0.0 to 1.0)

// Streaming services supported
$streaming_services = [
    'Icecast' => 'icecast',
    'Shoutcast' => 'shoutcast',
    'Winamp' => 'winamp',
    'Windows Media Encoder' => 'windows-media',
    'Facebook Live' => 'facebook-live',
    'YouTube Live' => 'youtube-live',
    'Flash Stream' => 'flash-stream',
];
$selected_service = $radio_settings['selected_service'] ?? 'Icecast';

// DJ timetable (example; replace with dynamic data from database or API)
$dj_name = 'DJ Example';
$dj_start_time = '12:00 PM';
$dj_end_time = '2:00 PM';

// Enqueue Font Awesome
wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

?>
<div class="brmedia-radio-player default-template" 
     style="width: <?php echo esc_attr($player_width); ?>; 
            max-width: 100%; 
            height: <?php echo esc_attr($player_height); ?>; 
            background-color: <?php echo esc_attr($primary_color); ?>; 
            color: <?php echo esc_attr($secondary_color); ?>; 
            font-family: <?php echo esc_attr($font_family); ?>; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
    <h2>Live Radio Stream</h2>
    <div class="radio-status">
        <span class="status-indicator"><i class="fas fa-signal"></i> Live</span>
    </div>
    <div class="radio-controls">
        <button class="play-pause-btn" 
                style="background: none; border: none; color: <?php echo esc_attr($secondary_color); ?>; font-size: 24px; cursor: pointer;">
            <i class="fas fa-play"></i>
        </button>
        <button class="stop-btn" 
                style="background: none; border: none; color: <?php echo esc_attr($secondary_color); ?>; font-size: 24px; cursor: pointer; margin-left: 10px;">
            <i class="fas fa-stop"></i>
        </button>
        <div class="volume-control" style="margin-left: 20px;">
            <i class="fas fa-volume-up" style="margin-right: 5px;"></i>
            <input type="range" min="0" max="1" step="0.1" value="<?php echo esc_attr($volume_level); ?>" class="volume-slider" 
                   style="vertical-align: middle;">
        </div>
    </div>
    <div class="radio-progress">
        <progress value="0" max="100" id="stream-progress" style="width: 100%; margin-top: 10px;"></progress>
    </div>
    <div class="radio-info">
        <p><strong>Streaming Service:</strong> <?php echo esc_html($selected_service); ?></p>
        <p><strong>Current DJ:</strong> <?php echo esc_html($dj_name); ?> (<?php echo esc_html($dj_start_time); ?> - <?php echo esc_html($dj_end_time); ?>)</p>
    </div>
    <audio id="radio-player" style="display: none;">
        <source src="<?php echo esc_url($stream_url); ?>" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const audio = document.getElementById('radio-player');
        const playPauseBtn = document.querySelector('.play-pause-btn');
        const stopBtn = document.querySelector('.stop-btn');
        const volumeSlider = document.querySelector('.volume-slider');
        const progress = document.querySelector('#stream-progress');
        let isPlaying = false;

        // Set initial volume
        audio.volume = <?php echo esc_js($volume_level); ?>;

        // Play/Pause functionality
        playPauseBtn.addEventListener('click', function() {
            if (isPlaying) {
                audio.pause();
                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            } else {
                audio.play();
                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            }
            isPlaying = !isPlaying;
        });

        // Stop functionality
        stopBtn.addEventListener('click', function() {
            audio.pause();
            audio.currentTime = 0;
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            isPlaying = false;
            progress.value = 0;
        });

        // Volume control
        volumeSlider.addEventListener('input', function() {
            audio.volume = this.value;
        });

        // Progress simulation (since live streams don’t have duration, this is a placeholder)
        audio.addEventListener('timeupdate', function() {
            if (isPlaying) {
                let progressValue = progress.value + 1;
                if (progressValue > 100) progressValue = 0;
                progress.value = progressValue;
            }
        });

        // Auto-play if enabled
        <?php if ($auto_play): ?>
            audio.play();
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            isPlaying = true;
        <?php endif; ?>
    });
</script>

<style>
    .brmedia-radio-player.default-template {
        transition: all 0.3s ease;
    }
    .brmedia-radio-player.default-template:hover {
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }
    .radio-controls button:hover {
        color: #ccc;
        transform: scale(1.1);
    }
    .volume-slider {
        -webkit-appearance: none;
        background: <?php echo esc_attr($secondary_color); ?>;
        height: 5px;
        border-radius: 5px;
    }
    .volume-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 15px;
        height: 15px;
        background: <?php echo esc_attr($primary_color); ?>;
        border-radius: 50%;
        cursor: pointer;
    }
    .status-indicator {
        font-size: 14px;
        padding: 5px 10px;
        background: rgba(255,255,255,0.2);
        border-radius: 15px;
    }
    progress {
        width: 100%;
        height: 10px;
        background: #fff;
        border-radius: 5px;
    }
    progress::-webkit-progress-value {
        background: <?php echo esc_attr($secondary_color); ?>;
        border-radius: 5px;
    }
</style>