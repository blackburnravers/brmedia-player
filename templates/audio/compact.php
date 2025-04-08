<?php
/**
 * Compact Audio Player Template
 *
 * This template provides a compact audio player with basic controls and minimal metadata.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve post data
$post_id = $post->ID;
$audio_url = get_post_meta($post_id, 'brmedia_audio_url', true);
$title = get_the_title($post_id);
?>

<div class="brmedia-audio-player compact" data-post-id="<?php echo esc_attr($post_id); ?>">
    <div class="wavesurfer-container" data-audio-url="<?php echo esc_url($audio_url); ?>" style="height: 50px;"></div>
    <div class="controls">
        <button class="play-pause"><i class="fas fa-play"></i></button>
        <span class="title"><?php echo esc_html($title); ?></span>
    </div>
</div>

<script>
    // Initialize WaveSurfer.js for compact player
    document.addEventListener('DOMContentLoaded', function() {
        var container = document.querySelector('.brmedia-audio-player.compact .wavesurfer-container');
        var wavesurfer = WaveSurfer.create({
            container: container,
            waveColor: '#0073aa',
            progressColor: '#005a87',
            cursorColor: '#333',
            height: 50,
            responsive: true
        });
        wavesurfer.load(container.dataset.audioUrl);

        // Play/Pause button
        var playPauseButton = container.nextElementSibling.querySelector('.play-pause');
        playPauseButton.addEventListener('click', function() {
            wavesurfer.playPause();
            this.querySelector('i').classList.toggle('fa-play');
            this.querySelector('i').classList.toggle('fa-pause');
        });
    });
</script>