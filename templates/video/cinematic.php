<?php
/**
 * Cinematic Video Player Template
 *
 * A fullscreen video player with a cinematic feel.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve video data
$post_id = get_the_ID();
$video_url = get_post_meta($post_id, 'brmedia_video_url', true);
$poster_url = get_the_post_thumbnail_url($post_id, 'full');

// Validate required data
if (empty($video_url)) {
    echo '<p>' . esc_html__('Video URL is missing.', 'brmedia') . '</p>';
    return;
}
?>

<div class="brmedia-video-cinematic" data-post-id="<?php echo esc_attr($post_id); ?>">
    <video id="video-cinematic-<?php echo esc_attr($post_id); ?>" class="video-js" controls preload="auto" poster="<?php echo esc_url($poster_url); ?>" data-setup='{"fluid": true}'>
        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
        <p class="vjs-no-js">
            <?php esc_html_e('To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video.', 'brmedia'); ?>
        </p>
    </video>
</div>

<style>
.brmedia-video-cinematic {
    position: relative;
    width: 100%;
    height: 100vh;
    background: #000;
}
.brmedia-video-cinematic .video-js {
    width: 100%;
    height: 100%;
}
</style>