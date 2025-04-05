<?php
/**
 * Template: Video Popup 3D
 *
 * Renders a 3D popup video player with advanced effects.
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
<div id="popup-<?php echo esc_attr($media_id); ?>" class="brmedia-player brmedia-video-popup-3d">
    <video class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="video/mp4">
    </video>
    <button class="close-btn">Close</button>
</div>

<style>
    .brmedia-video-popup-3d {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotateY(10deg);
        width: 80%;
        max-width: 900px;
        background: #fff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        display: none;
        transition: transform 0.3s ease;
    }
    .brmedia-video-popup-3d.active {
        display: block;
        transform: translate(-50%, -50%) rotateY(0deg);
    }
    .brmedia-video-popup-3d:hover {
        transform: translate(-50%, -50%) rotateY(-5deg);
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
    const popup = document.querySelector('.brmedia-video-popup-3d');
    const closeBtn = popup.querySelector('.close-btn');

    trigger.addEventListener('click', function() {
        popup.classList.add('active');
    });

    closeBtn.addEventListener('click', function() {
        popup.classList.remove('active');
    });
});
</script>