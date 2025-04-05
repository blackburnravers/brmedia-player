<?php
/**
 * Template: Video Default
 *
 * Renders a default video player with standard features.
 *
 * @package BRMedia\Templates
 */

// Ensure media ID is set
if (!isset($media_id)) {
    echo '<p>Media ID not provided.</p>';
    return;
}

// Get video URL
$url = get_post_meta($media_id, '_brmedia_video_url', true);
if (empty($url)) {
    echo '<p>Video URL not found.</p>';
    return;
}
?>

<div class="brmedia-player brmedia-video-default" data-media-id="<?php echo esc_attr($media_id); ?>">
    <video class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="video/mp4">
    </video>
</div>

<style>
    .brmedia-video-default {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        background: #000;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    .brmedia-video-default .plyr {
        width: 100%;
    }
    .brmedia-video-default .plyr__controls {
        padding: 10px;
        background: #333;
    }
</style>