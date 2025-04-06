<?php
/**
 * Template Name: Footer Player - Track Playing
 * Description: Footer player template when a track is playing.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve track data from post meta or shortcode attributes
$track_title = get_the_title();
$track_artist = get_post_meta( get_the_ID(), '_brmedia_artist', true );
$cover_image = get_the_post_thumbnail_url();
?>

<div class="brmedia-footer-player track-playing">
    <div class="brmedia-track-info">
        <?php if ( $cover_image ) : ?>
            <img src="<?php echo esc_url( $cover_image ); ?>" alt="<?php echo esc_attr( $track_title ); ?>" class="brmedia-cover-image">
        <?php endif; ?>
        <div class="brmedia-track-details">
            <h3><?php echo esc_html( $track_title ); ?></h3>
            <p><?php echo esc_html( $track_artist ); ?></p>
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
    </div>
</div>