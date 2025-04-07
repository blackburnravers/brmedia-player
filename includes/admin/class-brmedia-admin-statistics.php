<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Admin_Statistics {

    public function __construct() {
        // Add hooks and filters as needed
    }

    public function render_statistics() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/statistics-analytics.php';
    }
}

new BRMedia_Admin_Statistics();