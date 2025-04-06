<?php
/**
 * Template Name: Podcast - Boxed Series
 * Description: A boxed layout template for podcast series in BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve podcast series data from post meta or shortcode attributes
$series_title = get_the_title();
$artwork = get_the_post_thumbnail_url();
$host_bio = get_post_meta( get_the_ID(), '_brmedia_podcast_host_bio', true );
$episodes = get_post_meta( get_the_ID(), '_brmedia_podcast_episodes', true ); // Array of episode data
?>

<div class="brmedia-podcast-series boxed">
    <div class="brmedia-series-header">
        <?php if ( $artwork ) : ?>
            <img src="<?php echo esc_url( $artwork ); ?>" alt="<?php echo esc_attr( $series_title ); ?>" class="brmedia-artwork">
        <?php endif; ?>
        <h2><?php echo esc_html( $series_title ); ?></h2>
    </div>
    <div class="brmedia-host-bio">
        <h3>About the Host</h3>
        <p><?php echo esc_html( $host_bio ); ?></p>
    </div>
    <div class="brmedia-episode-list">
        <h3>Episodes</h3>
        <ul>
            <?php foreach ( $episodes as $episode ) : ?>
                <li>
                    <a href="<?php echo esc_url( $episode['link'] ); ?>"><?php echo esc_html( $episode['title'] ); ?></a>
                    <p><?php echo esc_html( $episode['description'] ); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="brmedia-subscription-buttons">
        <a href="#" class="brmedia-subscribe spotify"><i class="fab fa-spotify"></i> Spotify</a>
        <a href="#" class="brmedia-subscribe apple"><i class="fab fa-apple"></i> Apple Podcasts</a>
        <a href="#" class="brmedia-subscribe google"><i class="fab fa-google"></i> Google Podcasts</a>
    </div>
</div>