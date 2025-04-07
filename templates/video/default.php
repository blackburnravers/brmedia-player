<?php
/**
 * Default Video Player Template
 * A standard video player with customizable controls, colors, and responsive design.
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
$player_width = $video_settings['player_width'] ?? '100%';
$player_height = $video_settings['player_height'] ?? 'auto';
$aspect_ratio = $video_settings['aspect_ratio'] ?? '16:9'; // Default to 16:9

// Calculate padding for responsive aspect ratio
list($ratio_w, $ratio_h) = explode(':', $aspect_ratio);
$padding_bottom = ($ratio_h / $ratio_w) * 100 . '%';

// Video data
$video_id = $atts['id'] ?? 0;
$video_url = get_post_meta($video_id, 'brmedia_video_url', true);
$title = get_the_title($video_id);
$description = get_post_meta($video_id, 'brmedia_video_description', true) ?: '';

// Validate video URL
if (empty($video_url)) {
    echo '<p>Error: No video URL found.</p>';
    return;
}

// Enqueue Plyr.js and styles
wp_enqueue_script('plyr', 'https://cdn.plyr.io/3.6.8/plyr.min.js', [], '3.6.8', true);
wp_enqueue_style('plyr', 'https://cdn.plyr.io/3.6.8/plyr.css', [], '3.6.8');

?>
<div class="brmedia-video-player default-template" 
     style="width: <?php echo esc_attr($player_width); ?>; 
            max-width: 100%; 
            position: relative; 
            padding-bottom: <?php echo esc_attr($padding_bottom); ?>;">
    <video id="player-<?php echo esc_attr($video_id); ?>" 
           style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>
<h3><?php echo esc_html($title); ?></h3>
<p><?php echo esc_html($description); ?></p>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const player = new Plyr('#player-<?php echo esc_attr($video_id); ?>', {
            controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'],
            settings: ['captions', 'quality', 'speed', 'loop'],
            invertTime: false,
            toggleInvert: false,
            ratio: '<?php echo esc_attr($aspect_ratio); ?>',
        });

        // Customize player colors via ACP settings
        document.querySelector('.plyr').style.setProperty('--plyr-color-main', '<?php echo esc_attr($primary_color); ?>');
    });
</script>