<?php
/**
 * Template Name: Video Player - Compact
 * Description: A compact video player template for BRMedia Player.
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
?>

<div class="brmedia-video-player compact">
    <video id="brmedia-video-compact-<?php echo get_the_ID(); ?>" playsinline controls data-poster="<?php echo esc_url( $poster_image ); ?>">
        <source src="<?php echo esc_url( $video_file ); ?>" type="video/mp4">
    </video>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const player = new Plyr('#brmedia-video-compact-<?php echo get_the_ID(); ?>', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
            invertTime: false,
            hideControls: true,
        });
    });
</script>