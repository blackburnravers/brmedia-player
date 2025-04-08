<?php
/**
 * Embedded Video Player Template
 *
 * A simple embedded video player for inline content.
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

// Validate required data
if (empty($video_url)) {
    echo '<p>' . esc_html__('Video URL is missing.', 'brmedia') . '</p>';
    return;
}
?>

<div class="brmedia-video-embedded" data-post-id="<?php echo esc_attr($post_id); ?>">
    <video id="video-embedded-<?php echo esc_attr($post_id); ?>" class="video-js" controls preload="auto" data-setup="{}">
        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
        <p class="vjs-no-js">
            <?php esc_html_e('To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video.', 'brmedia'); ?>
        </p>
    </video>
</div>

<style>
.brmedia-video-embedded {
    max-width: 100%;
    margin: 0 auto;
}
</style>