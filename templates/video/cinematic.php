<?php
/**
 * Cinematic Video Player Template
 * Displays a full-screen, immersive video player with minimal controls.
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
$background_color = $video_settings['background_color'] ?? '#000';

// Video data
$video_id = $atts['id'] ?? 0;
$video_url = get_post_meta($video_id, 'brmedia_video_url', true);

// Validate video URL
if (empty($video_url)) {
    echo '<p>Error: No video URL found.</p>';
    return;
}

// Enqueue Plyr.js and styles
wp_enqueue_script('plyr', 'https://cdn.plyr.io/3.6.8/plyr.min.js', [], '3.6.8', true);
wp_enqueue_style('plyr', 'https://cdn.plyr.io/3.6.8/plyr.css', [], '3.6.8');

?>
<div class="brmedia-video-player cinematic-template" 
     style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: <?php echo esc_attr($background_color); ?>; z-index: 9999;">
    <video id="player-<?php echo esc_attr($video_id); ?>" 
           style="width: 100%; height: 100%;" 
           autoplay>
        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <button class="brmedia-exit-cinematic" 
            style="position: absolute; top: 20px; right: 20px; background: none; border: none; color: #fff; font-size: 24px; cursor: pointer;">
        <i class="fas fa-times"></i>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const player = new Plyr('#player-<?php echo esc_attr($video_id); ?>', {
            controls: ['play', 'progress', 'fullscreen'],
            hideControls: true,
            clickToPlay: false,
        });

        // Customize player colors via ACP settings
        document.querySelector('.plyr').style.setProperty('--plyr-color-main', '<?php echo esc_attr($primary_color); ?>');

        // Exit cinematic mode
        document.querySelector('.brmedia-exit-cinematic').addEventListener('click', function() {
            player.pause();
            document.querySelector('.cinematic-template').style.display = 'none';
        });
    });
</script>