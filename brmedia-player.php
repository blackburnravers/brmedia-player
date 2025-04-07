<?php
/*
Plugin Name: BRMedia Player
Plugin URI: https://www.blackburnravers.co.uk
Description: A powerful, modular WordPress plugin for managing and presenting multimedia content.
Version: 1.0.0
Author: Rhys Cole
Author URI: https://www.blackburnravers.co.uk
Text Domain: brmedia-player
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define constants
define( 'BRMEDIA_PLAYER_VERSION', '1.0.0' );
define( 'BRMEDIA_PLAYER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BRMEDIA_PLAYER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include the main class
require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/class-brmedia-player.php';

// Initialize the plugin
function brmedia_player_init() {
    $brmedia_player = new BRMedia_Player();
}
add_action( 'plugins_loaded', 'brmedia_player_init' );