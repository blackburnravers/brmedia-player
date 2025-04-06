<?php
/**
 * Template Name: Popup Audio Player
 * Description: A popup audio player template for BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve audio data
$audio_file = get_post_meta( get_the_ID(), '_brmedia_audio_file', true );
$title = get_the_title();
$artist = get_post_meta( get_the_ID(), '_brmedia_artist', true );
$cover_image = get_the_post_thumbnail_url();
?>

<button class="brmedia-popup-trigger" data-post-id="<?php echo get_the_ID(); ?>">
    <i class="fas fa-play"></i> Play <?php echo esc_html( $title ); ?>
</button>

<div class="brmedia-popup-player" id="brmedia-popup-<?php echo get_the_ID(); ?>" style="display: none;">
    <div class="brmedia-popup-content">
        <button class="brmedia-close-popup" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <div class="brmedia-player-header">
            <?php if ( $cover_image ) : ?>
                <img src="<?php echo esc_url( $cover_image ); ?>" alt="<?php echo esc_attr( $title ); ?>">
            <?php endif; ?>
            <h3><?php echo esc_html( $title ); ?></h3>
            <p><?php echo esc_html( $artist ); ?></p>
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
        </div>
        <audio id="brmedia-audio-popup-<?php echo get_the_ID(); ?>" preload="metadata">
            <source src="<?php echo esc_url( $audio_file ); ?>" type="audio/mpeg">
        </audio>
        <div class="brmedia-waveform" id="waveform-popup-<?php echo get_the_ID(); ?>"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.querySelector('.brmedia-popup-trigger[data-post-id="<?php echo get_the_ID(); ?>"]');
        const popup = document.getElementById('brmedia-popup-<?php echo get_the_ID(); ?>');
        const closeButton = popup.querySelector('.brmedia-close-popup');

        trigger.addEventListener('click', function() {
            popup.style.display = 'block';
            if (!popup.classList.contains('initialized')) {
                const player = new Plyr('#brmedia-audio-popup-<?php echo get_the_ID(); ?>', {
                    controls: ['play', 'progress', 'current-time', 'duration', 'volume'],
                });
                const wavesurfer = WaveSurfer.create({
                    container: '#waveform-popup-<?php echo get_the_ID(); ?>',
                    waveColor: '#ddd',
                    progressColor: '#ff5500',
                    cursorColor: '#333',
                    barWidth: 2,
                    height: 100,
                    responsive: true,
                });
                wavesurfer.load('<?php echo esc_url( $audio_file ); ?>');
                wavesurfer.on('ready', function() {
                    player.on('play', function() { wavesurfer.play(); });
                    player.on('pause', function() { wavesurfer.pause(); });
                    player.on('seek', function() { wavesurfer.seekTo(player.currentTime / player.duration); });
                });
                popup.classList.add('initialized');
            }
        });

        closeButton.addEventListener('click', function() {
            popup.style.display = 'none';
        });
    });
</script>