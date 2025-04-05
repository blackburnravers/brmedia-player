<?php
/**
 * Template: Audio Compact
 *
 * Renders a compact audio player with essential controls.
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

<div class="brmedia-player brmedia-audio-compact" data-media-id="<?php echo esc_attr($media_id); ?>">
    <audio class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
    </audio>
</div>

<style>
    .brmedia-audio-compact {
        width: 100%;
        max-width: 300px;
        background: #f0f0f0;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .brmedia-audio-compact .plyr__controls {
        display: flex;
        justify-content: space-between;
        padding: 5px;
    }
    .brmedia-audio-compact .plyr__control {
        margin: 0 5px;
    }
</style>