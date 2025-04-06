<?php
/**
 * Template Name: Footer Player - Radio Active
 * Description: Footer player template when a radio stream is active.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve radio data (assumed functions)
$radio_info = get_radio_info(); // Returns array with 'show' and 'dj'
?>

<div class="brmedia-footer-player radio-active" style="background-color: green;">
    <div class="brmedia-player-controls">
        <button class="brmedia-play-pause" aria-label="Play/Pause">
            <i class="fas fa-play" style="color: white;"></i>
        </button>
        <div class="brmedia-progress">
            <input type="range" class="brmedia-seekbar" value="0" min="0" max="100" disabled>
        </div>
        <div class="brmedia-time">
            <span class="brmedia-current-time">Live</span>
        </div>
        <button class="brmedia-volume" aria-label="Volume">
            <i class="fas fa-volume-up" style="color: white;"></i>
        </button>
    </div>
    <div class="brmedia-track-info">
        <p>Radio: <?php echo esc_html( $radio_info['show'] ); ?> with <?php echo esc_html( $radio_info['dj'] ); ?></p>
    </div>
</div>