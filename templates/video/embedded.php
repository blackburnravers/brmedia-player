<?php
/**
 * Embedded Video Player Template
 * Displays an embedded video player inline with content, customizable via ACP.
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
$embed_width = $video_settings['embed_width'] ?? '560px';
$embed_height = $video_settings['embed_height'] ?? '315px';

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
<div class="brmedia-video-player embedded-template" 
     style="width: <?php echo esc_attr($embed_width); ?>; 
            height: <?php echo esc_attr($embed_height); ?>; 
            margin: 0 auto;">
    <video id="player-<?php echo esc_attr($video_id); ?>">
        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>
<h3><?php echo esc_html($title); ?></h3>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const player = new Plyr('#player-<?php echo esc_attr($video_id); ?>', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
            invertTime: false,
            toggleInvert: false,
        });

        // Customize player colors via ACP settings
        document.querySelector('.plyr').style.setProperty('--plyr-color-main', '<?php echo esc_attr($primary_color); ?>');
    });
</script>