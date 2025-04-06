<?php
/**
 * Template Name: Gaming - Fullscreen
 * Description: A fullscreen template for gaming content in BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve gaming data from post meta or shortcode attributes
$video_url = get_post_meta( get_the_ID(), '_brmedia_game_video', true );
?>

<div class="brmedia-gaming-player fullscreen">
    <div class="brmedia-fullscreen-video">
        <video id="brmedia-video-fullscreen-<?php echo get_the_ID(); ?>" class="brmedia-video-player" controls>
            <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
        </video>
    </div>
    <button class="brmedia-exit-fullscreen" aria-label="Exit Fullscreen">
        <i class="fas fa-compress"></i>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const player = new Plyr('#brmedia-video-fullscreen-<?php echo get_the_ID(); ?>', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
            fullscreen: { enabled: true, fallback: true },
        });

        // Fullscreen toggle logic
        const fullscreenButton = document.querySelector('.brmedia-exit-fullscreen');
        fullscreenButton.addEventListener('click', function() {
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                document.querySelector('.brmedia-fullscreen-video').requestFullscreen();
            }
        });
    });
</script>