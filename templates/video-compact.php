<?php
/**
 * Template: Video Compact
 *
 * Renders a compact video player for small spaces.
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

<div class="brmedia-player brmedia-video-compact" data-media-id="<?php echo esc_attr($media_id); ?>">
    <video class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="video/mp4">
    </video>
</div>

<style>
    .brmedia-video-compact {
        width: 100%;
        max-width: 400px;
        background: #000;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .brmedia-video-compact .plyr {
        width: 100%;
    }
    .brmedia-video-compact .plyr__controls {
        padding: 5px;
        background: #333;
        display: flex;
        justify-content: space-between;
    }
    .brmedia-video-compact .plyr__control {
        margin: 0 5px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const player = document.querySelector('.brmedia-video-compact .plyr');
    player.addEventListener('loadedmetadata', function() {
        this.style.height = `${(this.offsetWidth / 16) * 9}px`; // Maintain 16:9 aspect ratio
    });
});
</script>