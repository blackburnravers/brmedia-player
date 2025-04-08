<?php
/**
 * Default Audio Player Template
 *
 * This template provides a full-featured audio player with waveform visualization,
 * playback controls, and metadata display.
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
$artist = get_post_meta($post_id, 'brmedia_artist', true);
$album = get_post_meta($post_id, 'brmedia_album', true);
$duration = get_post_meta($post_id, 'brmedia_audio_duration', true);
?>

<div class="brmedia-audio-player default" data-post-id="<?php echo esc_attr($post_id); ?>">
    <h3><?php echo esc_html($title); ?></h3>
    <div class="wavesurfer-container" data-audio-url="<?php echo esc_url($audio_url); ?>"></div>
    <div class="controls">
        <button class="play-pause"><i class="fas fa-play"></i></button>
        <span class="current-time">00:00</span> / <span class="duration"><?php echo esc_html($duration); ?></span>
    </div>
    <div class="metadata">
        <p>Artist: <?php echo esc_html($artist); ?></p>
        <p>Album: <?php echo esc_html($album); ?></p>
    </div>
</div>

<script>
    // Initialize WaveSurfer.js for this player
    document.addEventListener('DOMContentLoaded', function() {
        var container = document.querySelector('.brmedia-audio-player.default .wavesurfer-container');
        var wavesurfer = WaveSurfer.create({
            container: container,
            waveColor: '#0073aa',
            progressColor: '#005a87',
            cursorColor: '#333',
            height: 100,
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

        // Update current time
        wavesurfer.on('audioprocess', function() {
            var currentTime = wavesurfer.getCurrentTime();
            container.nextElementSibling.querySelector('.current-time').textContent = formatTime(currentTime);
        });

        // Update duration
        wavesurfer.on('ready', function() {
            var duration = wavesurfer.getDuration();
            container.nextElementSibling.querySelector('.duration').textContent = formatTime(duration);
        });

        // Helper function to format time
        function formatTime(time) {
            var minutes = Math.floor(time / 60);
            var seconds = Math.floor(time % 60);
            return minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
        }
    });
</script>