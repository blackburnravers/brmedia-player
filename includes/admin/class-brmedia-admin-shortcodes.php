<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Admin_Shortcodes {

    public function __construct() {
        // Add hooks and filters as needed
    }

    public function render_shortcodes() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/shortcodes-manager.php';
    }
}

new BRMedia_Admin_Shortcodes();