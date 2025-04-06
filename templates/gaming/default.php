<?php
/**
 * Template Name: Gaming - Default
 * Description: A default template for gaming content in BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve gaming data from post meta or shortcode attributes
$game_title = get_the_title();
$cover_image = get_the_post_thumbnail_url();
$video_url = get_post_meta( get_the_ID(), '_brmedia_game_video', true );
$stats = get_post_meta( get_the_ID(), '_brmedia_game_stats', true );
?>

<div class="brmedia-gaming-player default">
    <div class="brmedia-game-header">
        <?php if ( $cover_image ) : ?>
            <img src="<?php echo esc_url( $cover_image ); ?>" alt="<?php echo esc_attr( $game_title ); ?>" class="brmedia-cover-image">
        <?php endif; ?>
        <h2><?php echo esc_html( $game_title ); ?></h2>
    </div>
    <div class="brmedia-game-video">
        <video id="brmedia-video-<?php echo get_the_ID(); ?>" class="brmedia-video-player" controls>
            <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
        </video>
    </div>
    <div class="brmedia-game-stats">
        <h3>Game Stats</h3>
        <p><?php echo esc_html( $stats ); ?></p>
    </div>
    <div class="brmedia-game-comments">
        <?php comments_template(); ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const player = new Plyr('#brmedia-video-<?php echo get_the_ID(); ?>', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
        });
    });
</script>