<?php
/**
 * Popup Audio Player Template
 * Displays an audio player in a popup modal with customizable styling and behavior.
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
$popup_width = $audio_settings['popup_width'] ?? '400px';
$popup_height = $audio_settings['popup_height'] ?? '200px';
$font_family = $audio_settings['font_family'] ?? 'Arial, sans-serif';
$border_radius = $audio_settings['border_radius'] ?? '5px';

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
<button class="brmedia-popup-btn" 
        style="background-color: <?php echo esc_attr($primary_color); ?>; 
               color: <?php echo esc_attr($secondary_color); ?>; 
               padding: 10px 20px; 
               border: none; 
               cursor: pointer; 
               border-radius: <?php echo esc_attr($border_radius); ?>; 
               font-family: <?php echo esc_attr($font_family); ?>;">
    <i class="fas fa-play"></i> Play Audio
</button>

<div id="popup-<?php echo esc_attr($audio_id); ?>" 
     class="brmedia-popup" 
     style="display: none; 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            width: <?php echo esc_attr($popup_width); ?>; 
            height: <?php echo esc_attr($popup_height); ?>; 
            background-color: <?php echo esc_attr($primary_color); ?>; 
            color: <?php echo esc_attr($secondary_color); ?>; 
            font-family: <?php echo esc_attr($font_family); ?>; 
            padding: 20px; 
            border-radius: <?php echo esc_attr($border_radius); ?>; 
            z-index: 9999; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
    <button class="brmedia-close-popup" 
            style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: <?php echo esc_attr($secondary_color); ?>; font-size: 24px; cursor: pointer;">
        <i class="fas fa-times"></i>
    </button>
    <h3><?php echo esc_html($title); ?></h3>
    <audio id="audio-<?php echo esc_attr($audio_id); ?>" controls>
        <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.querySelector('.brmedia-popup-btn');
        const popup = document.getElementById('popup-<?php echo esc_attr($audio_id); ?>');
        const closeBtn = popup.querySelector('.brmedia-close-popup');
        const audio = document.getElementById('audio-<?php echo esc_attr($audio_id); ?>');

        btn.addEventListener('click', function() {
            popup.style.display = 'block';
            audio.play();
        });

        closeBtn.addEventListener('click', function() {
            popup.style.display = 'none';
            audio.pause();
        });
    });
</script>

<style>
    .brmedia-popup {
        transition: opacity 0.3s ease;
    }
    .brmedia-popup:hover {
        opacity: 0.95;
    }
    .brmedia-close-popup {
        transition: color 0.2s ease, transform 0.2s ease;
    }
    .brmedia-close-popup:hover {
        color: #ccc;
        transform: scale(1.1);
    }
</style>