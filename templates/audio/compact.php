<?php
/**
 * Compact Audio Player Template
 * A minimalistic, space-efficient audio player with essential controls.
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
$player_height = $audio_settings['player_height'] ?? '50px';
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
<div class="brmedia-audio-player compact-template" 
     style="width: <?php echo esc_attr($player_width); ?>; 
            height: <?php echo esc_attr($player_height); ?>; 
            background-color: <?php echo esc_attr($primary_color); ?>; 
            color: <?php echo esc_attr($secondary_color); ?>; 
            font-family: <?php echo esc_attr($font_family); ?>; 
            display: flex; 
            align-items: center; 
            padding: 0 10px; 
            border-radius: <?php echo esc_attr($border_radius); ?>;">
    <button class="play-pause-btn" 
            style="background: none; border: none; color: <?php echo esc_attr($secondary_color); ?>; font-size: 18px; cursor: pointer;">
        <i class="fas fa-play"></i>
    </button>
    <?php if ($show_title) : ?>
    <div class="audio-info" style="flex-grow: 1; margin-left: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
        <span class="audio-title"><?php echo esc_html($title); ?></span>
    </div>
    <?php endif; ?>
    <audio id="audio-<?php echo esc_attr($audio_id); ?>" style="display: none;">
        <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const audio = document.getElementById('audio-<?php echo esc_attr($audio_id); ?>');
        const playPauseBtn = document.querySelector('.compact-template .play-pause-btn');
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
    });
</script>

<style>
    .compact-template {
        transition: box-shadow 0.3s ease;
    }
    .compact-template:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .play-pause-btn {
        transition: color 0.2s ease, transform 0.2s ease;
    }
    .play-pause-btn:hover {
        color: #ccc;
        transform: scale(1.1);
    }
</style>