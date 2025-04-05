<?php
/**
 * Template: Video Cinematic
 *
 * Renders a cinematic video player with theater-like effects.
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

<div class="brmedia-player brmedia-video-cinematic" data-media-id="<?php echo esc_attr($media_id); ?>">
    <video class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="video/mp4">
    </video>
</div>

<style>
    .brmedia-video-cinematic {
        position: relative;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        background: #000;
        aspect-ratio: 16 / 9;
        overflow: hidden;
    }
    .brmedia-video-cinematic::before,
    .brmedia-video-cinematic::after {
        content: '';
        position: absolute;
        left: 0;
        width: 100%;
        height: 15%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1;
    }
    .brmedia-video-cinematic::before {
        top: 0;
    }
    .brmedia-video-cinematic::after {
        bottom: 0;
    }
    .brmedia-video-cinematic .plyr {
        width: 100%;
        height: 100%;
    }
    .brmedia-video-cinematic .plyr__controls {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
        padding: 20px;
    }
</style>