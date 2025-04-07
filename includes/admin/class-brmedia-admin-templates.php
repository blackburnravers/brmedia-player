<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Admin_Templates {

    public function __construct() {
        // Add hooks and filters as needed
    }

    public function render_templates() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/templates-panel.php';
    }
}

new BRMedia_Admin_Templates();