<?php
/**
 * Template: Audio Popup
 *
 * Renders a basic popup audio player.
 *
 * @package BRMedia\Templates
 */

// Ensure media ID is set
if (!isset($media_id)) {
    echo '<p>Media ID not provided.</p>';
    return;
}

// Get media URL
$url = get_post_meta($media_id, '_brmedia_music_url', true);
if (empty($url)) {
    echo '<p>Audio URL not found.</p>';
    return;
}
?>

<button class="brmedia-popup-trigger" data-target="#popup-<?php echo esc_attr($media_id); ?>">Open Player</button>
<div id="popup-<?php echo esc_attr($media_id); ?>" class="brmedia-player brmedia-audio-popup">
    <audio class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
    </audio>
    <button class="close-btn">Close</button>
</div>

<style>
    .brmedia-audio-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
    }
    .brmedia-audio-popup.active {
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
    const popup = document.querySelector('.brmedia-audio-popup');
    const closeBtn = popup.querySelector('.close-btn');

    trigger.addEventListener('click', function() {
        popup.classList.add('active');
    });

    closeBtn.addEventListener('click', function() {
        popup.classList.remove('active');
    });
});
</script>