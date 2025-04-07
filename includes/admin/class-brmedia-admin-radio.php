<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Admin_Radio {

    public function __construct() {
        // Add hooks and filters as needed
    }

    public function render_radio() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/radio.php';
    }
}

new BRMedia_Admin_Radio();