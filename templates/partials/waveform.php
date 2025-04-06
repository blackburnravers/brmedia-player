<?php
/**
 * Partial Template: Waveform
 * Description: Renders a waveform visualization for audio tracks using Wavesurfer.js.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Default values
$audio_file = $args['audio_file'] ?? '';
$wave_color = $args['wave_color'] ?? '#ddd';
$progress_color = $args['progress_color'] ?? '#ff5500';
$player_id = $args['player_id'] ?? 'player-' . uniqid();
?>

<div class="brmedia-waveform" id="waveform-<?php echo esc_attr( $player_id ); ?>"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wavesurfer = WaveSurfer.create({
        container: '#waveform-<?php echo esc_js( $player_id ); ?>',
        waveColor: '<?php echo esc_js( $wave_color ); ?>',
        progressColor: '<?php echo esc_js( $progress_color ); ?>',
        cursorColor: '#333',
        barWidth: 2,
        height: 100,
        responsive: true,
    });
    wavesurfer.load('<?php echo esc_url( $audio_file ); ?>');

    // Sync with Plyr player
    const player = document.querySelector('#<?php echo esc_js( $player_id ); ?>');
    if (player && player.plyr) {
        const plyrPlayer = player.plyr;
        wavesurfer.on('ready', function() {
            plyrPlayer.on('play', function() { wavesurfer.play(); });
            plyrPlayer.on('pause', function() { wavesurfer.pause(); });
            plyrPlayer.on('seeked', function() { wavesurfer.seekTo(plyrPlayer.currentTime / plyrPlayer.duration); });
        });
        wavesurfer.on('seek', function(progress) {
            plyrPlayer.currentTime = progress * plyrPlayer.duration;
        });
    }
});
</script>