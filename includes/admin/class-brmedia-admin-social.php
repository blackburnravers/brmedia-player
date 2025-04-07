<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Admin_Social {

    public function __construct() {
        // Add hooks and filters as needed
    }

    public function render_social() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/social-share.php';
    }
}

new BRMedia_Admin_Social();