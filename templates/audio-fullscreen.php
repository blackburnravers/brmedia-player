<?php
/**
 * Template: Audio Fullscreen
 *
 * Renders a fullscreen audio player with a minimalistic design.
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

<div class="brmedia-player brmedia-audio-fullscreen" data-media-id="<?php echo esc_attr($media_id); ?>">
    <audio class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
    </audio>
</div>

<style>
    .brmedia-audio-fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: #000;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    .brmedia-audio-fullscreen .plyr {
        width: 80%;
        max-width: 800px;
    }
</style>