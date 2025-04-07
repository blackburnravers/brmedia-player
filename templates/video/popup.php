<?php
/**
 * Popup Video Player Template
 * Displays video in a popup modal with customizable styling and behavior.
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
$video_settings = $settings['video'] ?? [];
$primary_color = $video_settings['primary_color'] ?? '#0073aa';
$popup_width = $video_settings['popup_width'] ?? '80%';
$popup_height = $video_settings['popup_height'] ?? '80%';
$aspect_ratio = $video_settings['aspect_ratio'] ?? '16:9';

// Video data
$video_id = $atts['id'] ?? 0;
$video_url = get_post_meta($video_id, 'brmedia_video_url', true);
$title = get_the_title($video_id);

// Validate video URL
if (empty($video_url)) {
    echo '<p>Error: No video URL found.</p>';
    return;
}

// Enqueue Plyr.js and styles
wp_enqueue_script('plyr', 'https://cdn.plyr.io/3.6.8/plyr.min.js', [], '3.6.8', true);
wp_enqueue_style('plyr', 'https://cdn.plyr.io/3.6.8/plyr.css', [], '3.6.8');

?>
<button class="brmedia-popup-btn" 
        data-video-id="<?php echo esc_attr($video_id); ?>" 
        style="background-color: <?php echo esc_attr($primary_color); ?>; color: #fff; padding: 10px 20px; border: none; cursor: pointer;">
    <i class="fas fa-play"></i> Watch Video
</button>

<div id="popup-<?php echo esc_attr($video_id); ?>" 
     class="brmedia-popup" 
     style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: <?php echo esc_attr($popup_width); ?>; height: <?php echo esc_attr($popup_height); ?>; background: #fff; z-index: 9999; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
    <div style="position: relative; width: 100%; height: 100%;">
        <video id="player-<?php echo esc_attr($video_id); ?>" 
               style="width: 100%; height: 100%;">
            <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <button class="brmedia-close-popup" 
                style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 24px; cursor: pointer;">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.querySelector('.brmedia-popup-btn[data-video-id="<?php echo esc_attr($video_id); ?>"]');
        const popup = document.getElementById('popup-<?php echo esc_attr($video_id); ?>');
        const closeBtn = popup.querySelector('.brmedia-close-popup');

        btn.addEventListener('click', function() {
            popup.style.display = 'block';
            const player = new Plyr('#player-<?php echo esc_attr($video_id); ?>', {
                controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'],
                ratio: '<?php echo esc_attr($aspect_ratio); ?>',
            });
            player.play();
        });

        closeBtn.addEventListener('click', function() {
            popup.style.display = 'none';
            const player = Plyr.get('#player-<?php echo esc_attr($video_id); ?>');
            if (player) player.pause();
        });

        // Customize player colors via ACP settings
        document.querySelector('.plyr').style.setProperty('--plyr-color-main', '<?php echo esc_attr($primary_color); ?>');
    });
</script>