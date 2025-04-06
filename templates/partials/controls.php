<?php
/**
 * Partial Template: Controls
 * Description: Custom media controls for BRMedia Player using Font Awesome icons.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Default values
$player_id = $args['player_id'] ?? 'player-' . uniqid();
?>

<div class="brmedia-controls">
    <button class="brmedia-play-pause" aria-label="Play/Pause">
        <i class="fas fa-play"></i>
        <i class="fas fa-pause" style="display: none;"></i>
    </button>
    <div class="brmedia-progress">
        <input type="range" class="brmedia-seekbar" value="0" min="0" max="100">
    </div>
    <div class="brmedia-time">
        <span class="brmedia-current-time">00:00</span> / <span class="brmedia-duration">00:00</span>
    </div>
    <button class="brmedia-volume" aria-label="Volume">
        <i class="fas fa-volume-up"></i>
    </button>
    <!-- Additional controls (e.g., speed, fullscreen) can be added here -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const player = document.querySelector('#<?php echo esc_js( $player_id ); ?>');
    if (player && player.plyr) {
        const plyrPlayer = player.plyr;

        // Play/Pause button
        const playPauseBtn = document.querySelector('.brmedia-play-pause');
        playPauseBtn.addEventListener('click', function() {
            if (plyrPlayer.paused) {
                plyrPlayer.play();
            } else {
                plyrPlayer.pause();
            }
        });

        // Update play/pause icon
        plyrPlayer.on('play', function() {
            playPauseBtn.querySelector('.fa-play').style.display = 'none';
            playPauseBtn.querySelector('.fa-pause').style.display = 'inline';
        });
        plyrPlayer.on('pause', function() {
            playPauseBtn.querySelector('.fa-play').style.display = 'inline';
            playPauseBtn.querySelector('.fa-pause').style.display = 'none';
        });

        // Progress bar
        const seekbar = document.querySelector('.brmedia-seekbar');
        seekbar.addEventListener('input', function() {
            const percent = seekbar.value / 100;
            plyrPlayer.currentTime = percent * plyrPlayer.duration;
        });

        // Update progress bar and time
        plyrPlayer.on('timeupdate', function() {
            const percent = (plyrPlayer.currentTime / plyrPlayer.duration) * 100;
            seekbar.value = percent;
            document.querySelector('.brmedia-current-time').textContent = formatTime(plyrPlayer.currentTime);
            document.querySelector('.brmedia-duration').textContent = formatTime(plyrPlayer.duration);
        });

        // Volume control (toggle mute)
        const volumeBtn = document.querySelector('.brmedia-volume');
        volumeBtn.addEventListener('click', function() {
            plyrPlayer.toggleMute();
        });
    }
});

// Helper function to format time
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}
</script>