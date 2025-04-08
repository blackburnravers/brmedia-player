<?php
/**
 * Footer Audio Player Template
 *
 * A persistent footer-based audio player using WaveSurfer.js.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve audio data (e.g., from plugin settings)
$audio_url = get_option('brmedia_footer_audio_url', '');
$audio_title = get_option('brmedia_footer_audio_title', 'Now Playing');

// Validate required data
if (empty($audio_url)) {
    return; // Don’t display if no audio is set
}
?>

<div class="brmedia-footer-player">
    <div class="player-controls">
        <button class="play-pause" aria-label="Play or pause audio">
            <i class="fas fa-play"></i>
        </button>
        <span class="current-time">00:00</span> / <span class="duration">00:00</span>
        <span class="title"><?php echo esc_html($audio_title); ?></span>
    </div>
    <div class="wavesurfer-container" data-audio-url="<?php echo esc_url($audio_url); ?>"></div>
</div>

<style>
.brmedia-footer-player {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #fff;
    border-top: 1px solid #ddd;
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 1000;
    box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
}
.player-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}
.play-pause {
    background: #0073aa;
    color: #fff;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 3px;
}
.wavesurfer-container {
    flex-grow: 1;
    height: 50px;
    min-width: 0;
}
</style>