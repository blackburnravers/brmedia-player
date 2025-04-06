<?php
/**
 * Template Name: Default Audio Player
 * Description: The default audio player template for BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve audio data from post meta or shortcode attributes
$audio_file = get_post_meta( get_the_ID(), '_brmedia_audio_file', true );
$artist = get_post_meta( get_the_ID(), '_brmedia_artist', true );
$title = get_the_title();
$cover_image = get_the_post_thumbnail_url();
$waveform = get_post_meta( get_the_ID(), '_brmedia_waveform', true ); // Optional waveform data

?>

<div class="brmedia-audio-player brmedia-default-player">
    <div class="brmedia-player-header">
        <?php if ( $cover_image ) : ?>
            <img src="<?php echo esc_url( $cover_image ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="brmedia-cover-image">
        <?php endif; ?>
        <div class="brmedia-track-info">
            <h3><?php echo esc_html( $title ); ?></h3>
            <p><?php echo esc_html( $artist ); ?></p>
        </div>
    </div>
    <div class="brmedia-player-controls">
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
        <button class="brmedia-fullscreen" aria-label="Fullscreen">
            <i class="fas fa-expand"></i>
        </button>
    </div>
    <audio id="brmedia-audio-<?php echo get_the_ID(); ?>" preload="metadata">
        <source src="<?php echo esc_url( $audio_file ); ?>" type="audio/mpeg">
    </audio>
    <?php if ( $waveform ) : ?>
        <div class="brmedia-waveform" id="waveform-<?php echo get_the_ID(); ?>"></div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Plyr for default player
        const player = new Plyr('#brmedia-audio-<?php echo get_the_ID(); ?>', {
            controls: ['play', 'progress', 'current-time', 'duration', 'volume', 'fullscreen'],
            invertTime: false,
        });

        // Initialize Wavesurfer if waveform data is available
        <?php if ( $waveform ) : ?>
            const wavesurfer = WaveSurfer.create({
                container: '#waveform-<?php echo get_the_ID(); ?>',
                waveColor: '#ddd',
                progressColor: '#ff5500',
                cursorColor: '#333',
                barWidth: 2,
                height: 100,
                responsive: true,
            });
            wavesurfer.load('<?php echo esc_url( $audio_file ); ?>');
            wavesurfer.on('ready', function() {
                player.on('play', function() {
                    wavesurfer.play();
                });
                player.on('pause', function() {
                    wavesurfer.pause();
                });
                player.on('seek', function() {
                    wavesurfer.seekTo(player.currentTime / player.duration);
                });
            });
        <?php endif; ?>
    });
</script>