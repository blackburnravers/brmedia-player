<?php
/**
 * Template Name: SoundCloud-Style Audio Player
 * Description: A SoundCloud-style audio player template for BRMedia Player.
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

<div class="brmedia-audio-player brmedia-soundcloud-style-player">
    <div class="brmedia-player-header">
        <img src="<?php echo esc_url( $cover_image ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="brmedia-cover-image">
        <div class="brmedia-track-info">
            <h3><?php echo esc_html( $title ); ?></h3>
            <p><?php echo esc_html( $artist ); ?></p>
        </div>
    </div>
    <div class="brmedia-waveform" id="waveform-soundcloud-<?php echo get_the_ID(); ?>"></div>
    <div class="brmedia-player-controls">
        <button class="brmedia-play-pause" aria-label="Play/Pause">
            <i class="fas fa-play"></i>
            <i class="fas fa-pause" style="display: none;"></i>
        </button>
        <div class="brmedia-time">
            <span class="brmedia-current-time">00:00</span> / <span class="brmedia-duration">00:00</span>
        </div>
        <button class="brmedia-volume" aria-label="Volume">
            <i class="fas fa-volume-up"></i>
        </button>
        <button class="brmedia-share" aria-label="Share">
            <i class="fas fa-share-alt"></i>
        </button>
    </div>
    <audio id="brmedia-audio-soundcloud-<?php echo get_the_ID(); ?>" preload="metadata">
        <source src="<?php echo esc_url( $audio_file ); ?>" type="audio/mpeg">
    </audio>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const player = new Plyr('#brmedia-audio-soundcloud-<?php echo get_the_ID(); ?>', {
            controls: ['play', 'current-time', 'duration', 'volume'],
            invertTime: false,
        });

        const wavesurfer = WaveSurfer.create({
            container: '#waveform-soundcloud-<?php echo get_the_ID(); ?>',
            waveColor: '#ff5500',
            progressColor: '#333',
            cursorColor: '#333',
            barWidth: 3,
            height: 150,
            responsive: true,
            interact: true,
        });
        wavesurfer.load('<?php echo esc_url( $audio_file ); ?>');
        wavesurfer.on('ready', function() {
            player.on('play', function() { wavesurfer.play(); });
            player.on('pause', function() { wavesurfer.pause(); });
            player.on('seek', function() { wavesurfer.seekTo(player.currentTime / player.duration); });
            wavesurfer.on('seek', function(progress) { player.currentTime = progress * player.duration; });
        });
    });
</script>