<?php
/**
 * Template Name: Video Player - Popup
 * Description: A popup video player template for BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve video data
$video_file = get_post_meta( get_the_ID(), '_brmedia_video_file', true );
$poster_image = get_the_post_thumbnail_url();
$title = get_the_title();
?>

<button class="brmedia-popup-trigger" data-post-id="<?php echo get_the_ID(); ?>">
    <i class="fas fa-play"></i> Watch <?php echo esc_html( $title ); ?>
</button>

<div class="brmedia-popup-player" id="brmedia-popup-<?php echo get_the_ID(); ?>" style="display: none;">
    <div class="brmedia-popup-content">
        <button class="brmedia-close-popup" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <video id="brmedia-video-popup-<?php echo get_the_ID(); ?>" playsinline controls data-poster="<?php echo esc_url( $poster_image ); ?>">
            <source src="<?php echo esc_url( $video_file ); ?>" type="video/mp4">
        </video>
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
                const player = new Plyr('#brmedia-video-popup-<?php echo get_the_ID(); ?>', {
                    controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
                });
                popup.classList.add('initialized');
            }
        });

        closeButton.addEventListener('click', function() {
            popup.style.display = 'none';
        });
    });
</script>