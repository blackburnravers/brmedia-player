<?php
/**
 * Minimal Audio Player Template
 * An ultra-minimalistic audio player with only a play/pause button.
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
$button_color = $audio_settings['button_color'] ?? '#0073aa';
$button_size = $audio_settings['button_size'] ?? '40px';
$hover_effect = $audio_settings['hover_effect'] ?? 'scale'; // Options: scale, color, none

// Audio data
$audio_id = $atts['id'] ?? 0;
$audio_url = wp_get_attachment_url($audio_id);

// Validate audio URL
if (empty($audio_url)) {
    echo '<p>Error: No audio file found.</p>';
    return;
}

// Enqueue Font Awesome
wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

?>
<div class="brmedia-audio-player minimal-template" style="display: inline-block;">
    <button class="play-pause-btn" 
            style="background: none; border: none; color: <?php echo esc_attr($button_color); ?>; font-size: <?php echo esc_attr($button_size); ?>; cursor: pointer; transition: all 0.3s ease;"
            data-hover-effect="<?php echo esc_attr($hover_effect); ?>">
        <i class="fas fa-play"></i>
    </button>
    <audio id="audio-<?php echo esc_attr($audio_id); ?>" style="display: none;">
        <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const audio = document.getElementById('audio-<?php echo esc_attr($audio_id); ?>');
        const playPauseBtn = document.querySelector('.minimal-template .play-pause-btn');
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
    .minimal-template .play-pause-btn:hover {
        <?php if ($hover_effect === 'scale'): ?>
            transform: scale(1.1);
        <?php elseif ($hover_effect === 'color'): ?>
            color: #ccc;
        <?php endif; ?>
    }
</style>