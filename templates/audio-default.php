<?php
/**
 * Template: Audio Default
 *
 * Renders the default audio player with standard features.
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

<div class="brmedia-player brmedia-audio-default" data-media-id="<?php echo esc_attr($media_id); ?>">
    <audio class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
    </audio>
    <div class="brmedia-social-sharing">
        <a href="#" class="share-facebook">Facebook</a>
        <a href="#" class="share-twitter">Twitter</a>
    </div>
</div>

<style>
    .brmedia-audio-default {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .brmedia-social-sharing {
        margin-top: 10px;
        text-align: center;
    }
    .brmedia-social-sharing a {
        margin: 0 5px;
        color: #007bff;
        text-decoration: none;
    }
</style>