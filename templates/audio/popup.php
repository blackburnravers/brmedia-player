<?php
/**
 * Popup Audio Player Template
 *
 * This template provides a button that opens the audio player in a popup window.
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

<button class="brmedia-popup-button" data-post-id="<?php echo esc_attr($post_id); ?>">Listen to <?php echo esc_html($title); ?></button>

<div id="brmedia-popup-player" style="display: none;">
    <div class="popup-content">
        <h3><?php echo esc_html($title); ?></h3>
        <div class="wavesurfer-container" data-audio-url="<?php echo esc_url($audio_url); ?>"></div>
        <div class="controls">
            <button class="play-pause"><i class="fas fa-play"></i></button>
            <span class="current-time">00:00</span> / <span class="duration">00:00</span>
        </div>
        <button class="close-popup">Close</button>
    </div>
</div>

<style>
    #brmedia-popup-player {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .popup-content {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        width: 80%;
        max-width: 600px;
    }
    .close-popup {
        margin-top: 10px;
        padding: 5px 10px;
        background: #0073aa;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }
</style>

<script>
    // Handle popup player
    document.addEventListener('DOMContentLoaded', function() {
        var popupButton = document.querySelector('.brmedia-popup-button');
        var popupPlayer = document.getElementById('brmedia-popup-player');
        var closeButton = popupPlayer.querySelector('.close-popup');

        popupButton.addEventListener('click', function() {
            popupPlayer.style.display = 'flex';
            var container = popupPlayer.querySelector('.wavesurfer-container');
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
            var playPauseButton = popupPlayer.querySelector('.play-pause');
            playPauseButton.addEventListener('click', function() {
                wavesurfer.playPause();
                this.querySelector('i').classList.toggle('fa-play');
                this.querySelector('i').classList.toggle('fa-pause');
            });

            // Close popup
            closeButton.addEventListener('click', function() {
                popupPlayer.style.display = 'none';
                wavesurfer.destroy();
            });
        });
    });
</script>