<?php
/**
 * Fullscreen Audio Player Template
 *
 * This template provides a fullscreen audio player with waveform and controls.
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

<div class="brmedia-audio-player fullscreen" data-post-id="<?php echo esc_attr($post_id); ?>">
    <button class="fullscreen-toggle">Enter Fullscreen</button>
    <div class="player-content" style="display: none;">
        <h3><?php echo esc_html($title); ?></h3>
        <div class="wavesurfer-container" data-audio-url="<?php echo esc_url($audio_url); ?>"></div>
        <div class="controls">
            <button class="play-pause"><i class="fas fa-play"></i></button>
            <span class="current-time">00:00</span> / <span class="duration">00:00</span>
        </div>
        <button class="exit-fullscreen">Exit Fullscreen</button>
    </div>
</div>

<style>
    .brmedia-audio-player.fullscreen .player-content {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        z-index: 9999;
        padding: 20px;
        box-sizing: border-box;
    }
</style>

<script>
    // Handle fullscreen player
    document.addEventListener('DOMContentLoaded', function() {
        var fullscreenButton = document.querySelector('.brmedia-audio-player.fullscreen .fullscreen-toggle');
        var playerContent = document.querySelector('.brmedia-audio-player.fullscreen .player-content');
        var exitButton = playerContent.querySelector('.exit-fullscreen');

        fullscreenButton.addEventListener('click', function() {
            playerContent.style.display = 'block';
            var container = playerContent.querySelector('.wavesurfer-container');
            var wavesurfer = WaveSurfer.create({
                container: container,
                waveColor: '#0073aa',
                progressColor: '#005a87',
                cursorColor: '#333',
                height: 200,
                responsive: true
            });
            wavesurfer.load(container.dataset.audioUrl);

            // Play/Pause button
            var playPauseButton = playerContent.querySelector('.play-pause');
            playPauseButton.addEventListener('click', function() {
                wavesurfer.playPause();
                this.querySelector('i').classList.toggle('fa-play');
                this.querySelector('i').classList.toggle('fa-pause');
            });

            // Exit fullscreen
            exitButton.addEventListener('click', function() {
                playerContent.style.display = 'none';
                wavesurfer.destroy();
            });
        });
    });
</script>