<?php
/**
 * Default Audio Player Template
 * A standard audio player with full controls, progress bar, and volume adjustment.
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
$audio_settings = $settings['audio'] ?? [];
$primary_color = $audio_settings['primary_color'] ?? '#0073aa';
$secondary_color = $audio_settings['secondary_color'] ?? '#ffffff';
$player_width = $audio_settings['player_width'] ?? '100%';
$player_height = $audio_settings['player_height'] ?? '100px';
$font_family = $audio_settings['font_family'] ?? 'Arial, sans-serif';
$border_radius = $audio_settings['border_radius'] ?? '5px';
$show_title = $audio_settings['show_title'] ?? true;

// Audio data
$audio_id = $atts['id'] ?? 0;
$audio_url = wp_get_attachment_url($audio_id);
$title = get_the_title($audio_id);

// Validate audio URL
if (empty($audio_url)) {
    echo '<p>Error: No audio file found.</p>';
    return;
}

// Enqueue external resources
wp_enqueue_script('plyr', 'https://cdn.plyr.io/3.6.8/plyr.min.js', [], '3.6.8', true);
wp_enqueue_style('plyr', 'https://cdn.plyr.io/3.6.8/plyr.css', [], '3.6.8');
wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

?>
<div class="brmedia-audio-player default-template" 
     style="width: <?php echo esc_attr($player_width); ?>; 
            height: <?php echo esc_attr($player_height); ?>; 
            background-color: <?php echo esc_attr($primary_color); ?>; 
            color: <?php echo esc_attr($secondary_color); ?>; 
            font-family: <?php echo esc_attr($font_family); ?>; 
            padding: 10px; 
            border-radius: <?php echo esc_attr($border_radius); ?>;">
    <?php if ($show_title) : ?>
        <h3 style="margin: 0 0 10px 0;"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    <div class="audio-controls" style="display: flex; align-items: center;">
        <button class="play-pause-btn" 
                style="background: none; border: none; color: <?php echo esc_attr($secondary_color); ?>; font-size: 24px; cursor: pointer;">
            <i class="fas fa-play"></i>
        </button>
        <div class="progress-bar" style="flex-grow: 1; margin: 0 10px;">
            <input type="range" min="0" max="100" value="0" class="progress-slider" style="width: 100%;">
        </div>
        <div class="volume-control" style="display: flex; align-items: center; margin-left: 10px;">
            <i class="fas fa-volume-up" style="font-size: 18px; margin-right: 5px;"></i>
            <input type="range" min="0" max="1" step="0.1" value="0.8" class="volume-slider" style="width: 80px;">
        </div>
    </div>
    <audio id="audio-<?php echo esc_attr($audio_id); ?>" style="display: none;">
        <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const audio = document.getElementById('audio-<?php echo esc_attr($audio_id); ?>');
        const playPauseBtn = document.querySelector('.default-template .play-pause-btn');
        const progressSlider = document.querySelector('.default-template .progress-slider');
        const volumeSlider = document.querySelector('.default-template .volume-slider');
        let isPlaying = false;

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

        audio.addEventListener('timeupdate', function() {
            const progress = (audio.currentTime / audio.duration) * 100;
            progressSlider.value = isNaN(progress) ? 0 : progress;
        });

        progressSlider.addEventListener('input', function() {
            const time = (this.value / 100) * audio.duration;
            audio.currentTime = isNaN(time) ? 0 : time;
        });

        volumeSlider.addEventListener('input', function() {
            audio.volume = this.value;
        });
    });
</script>

<style>
    .default-template {
        transition: box-shadow 0.3s ease;
    }
    .default-template:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .play-pause-btn {
        transition: color 0.2s ease, transform 0.2s ease;
    }
    .play-pause-btn:hover {
        color: #ccc;
        transform: scale(1.1);
    }
    .progress-slider, .volume-slider {
        -webkit-appearance: none;
        background: <?php echo esc_attr($secondary_color); ?>;
        height: 5px;
        border-radius: 5px;
        outline: none;
        transition: background 0.2s ease;
    }
    .progress-slider::-webkit-slider-thumb, .volume-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 15px;
        height: 15px;
        background: <?php echo esc_attr($primary_color); ?>;
        border-radius: 50%;
        cursor: pointer;
    }
    .progress-slider:hover, .volume-slider:hover {
        background: #ccc;
    }
</style>