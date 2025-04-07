<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Admin_Widgets {

    public function __construct() {
        // Add hooks and filters as needed
    }

    public function render_widgets() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/widgets-seo.php';
    }
}

new BRMedia_Admin_Widgets();