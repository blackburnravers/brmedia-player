<?php
/**
 * Template: Video Popup
 *
 * Renders a basic popup video player.
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

<button class="brmedia-popup-trigger" data-target="#popup-<?php echo esc_attr($media_id); ?>">Open Video</button>
<div id="popup-<?php echo esc_attr($media_id); ?>" class="brmedia-player brmedia-video-popup">
    <video class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="video/mp4">
    </video>
    <button class="close-btn">Close</button>
</div>

<style>
    .brmedia-video-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
        max-width: 600px;
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
    }
    .brmedia-video-popup.active {
        display: block;
    }
    .close-btn {
        margin-top: 10px;
        padding: 5px 10px;
        background: #ff4d4d;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.querySelector('.brmedia-popup-trigger');
    const popup = document.querySelector('.brmedia-video-popup');
    const closeBtn = popup.querySelector('.close-btn');

    trigger.addEventListener('click', function() {
        popup.classList.add('active');
    });

    closeBtn.addEventListener('click', function() {
        popup.classList.remove('active');
    });
});
</script>