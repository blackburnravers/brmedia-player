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

// Enqueue the admin.js script in your main plugin file or admin class
function brmedia_admin_enqueue_scripts() {
    wp_enqueue_media();
    wp_enqueue_script( 'brmedia-admin-js', plugin_dir_url( __FILE__ ) . 'admin/admin.js', array( 'jquery' ), '1.0', true );
}

add_action( 'admin_enqueue_scripts', 'brmedia_admin_enqueue_scripts' );

// Include the main class
require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/class-brmedia-player.php';

add_action( 'admin_enqueue_scripts', 'brmedia_admin_enqueue_scripts' );

function brmedia_enqueue_scripts() {
    // Enqueue Wavesurfer.js
    wp_enqueue_script( 'wavesurfer-js', 'https://unpkg.com/wavesurfer.js', array(), '1.0.0', true );
    // Enqueue your custom script to initialize Wavesurfer
    wp_enqueue_script( 'brmedia-custom-js', plugin_dir_url( __FILE__ ) . 'assets/js/custom.js', array('wavesurfer-js'), '1.0.0', true );
    // Enqueue Font Awesome
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css' );
    // Enqueue your custom CSS for the player
    wp_enqueue_style( 'brmedia-custom-css', plugin_dir_url( __FILE__ ) . 'assets/css/custom.css', array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'brmedia_enqueue_scripts' );

// Initialize the plugin
function brmedia_player_init() {
    $brmedia_player = new BRMedia_Player();
}
add_action( 'plugins_loaded', 'brmedia_player_init' );