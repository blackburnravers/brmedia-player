<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Admin_Video {

    public function __construct() {
        // Add hooks and filters as needed
    }

    public function render_video() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/video.php';
    }
}

new BRMedia_Admin_Video();