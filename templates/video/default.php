<?php
/**
 * Template Name: Video Player - Default
 * Description: A default video player template for BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve video data from post meta or shortcode attributes
$video_file = get_post_meta( get_the_ID(), '_brmedia_video_file', true );
$poster_image = get_the_post_thumbnail_url();
$captions_file = get_post_meta( get_the_ID(), '_brmedia_captions_file', true );
?>

<div class="brmedia-video-player default">
    <video id="brmedia-video-<?php echo get_the_ID(); ?>" playsinline controls data-poster="<?php echo esc_url( $poster_image ); ?>">
        <source src="<?php echo esc_url( $video_file ); ?>" type="video/mp4">
        <?php if ( $captions_file ) : ?>
            <track kind="captions" label="English" srclang="en" src="<?php echo esc_url( $captions_file ); ?>" default>
        <?php endif; ?>
    </video>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const player = new Plyr('#brmedia-video-<?php echo get_the_ID(); ?>', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'fullscreen'],
            captions: { active: true, language: 'en', update: true },
        });
    });
</script>