<?php
// dashboard.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<div class="wrap">
    <h1><?php _e( 'BRMedia Player Dashboard', 'brmedia-player' ); ?></h1>
    <div class="dashboard">
        <!-- Module stats and quick links go here -->
        <div class="module-card">
            <h2><?php _e( 'Music', 'brmedia-player' ); ?></h2>
            <p><?php _e( 'Manage your music library and playlists.', 'brmedia-player' ); ?></p>
        </div>
        <div class="module-card">
            <h2><?php _e( 'Video', 'brmedia-player' ); ?></h2>
            <p><?php _e( 'Manage your video content.', 'brmedia-player' ); ?></p>
        </div>
        <!-- Add more modules as needed -->
    </div>
</div>