<?php
/**
 * Template Name: Footer Player - Default
 * Description: Default state for the footer player when no track or radio is active.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="brmedia-footer-player default">
    <div class="brmedia-player-controls">
        <button class="brmedia-play-pause" aria-label="Play/Pause" disabled>
            <i class="fas fa-play"></i>
        </button>
        <div class="brmedia-progress">
            <input type="range" class="brmedia-seekbar" value="0" min="0" max="100" disabled>
        </div>
        <div class="brmedia-time">
            <span class="brmedia-current-time">00:00</span> / <span class="brmedia-duration">00:00</span>
        </div>
        <button class="brmedia-volume" aria-label="Volume" disabled>
            <i class="fas fa-volume-up"></i>
        </button>
    </div>
    <div class="brmedia-track-info">
        <p>No track playing</p>
    </div>
</div>