<?php
/*
Plugin Name: BRMedia Player
Plugin URI: https://www.blackburnravers.co.uk/brmedia-player
Description: An advanced media player plugin for WordPress with integrations for SoundCloud, YouTube, Twitch, Spotify, and more.
Version: 1.0.0
Author: Rhys Cole
Author URI: https://www.blackburnravers.co.uk
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: brmedia-player
*/

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define plugin constants
 */
define( 'BRMEDIA_PLAYER_VERSION', '1.0.0' );
define( 'BRMEDIA_PLAYER_DIR', plugin_dir_path( __FILE__ ) );
define( 'BRMEDIA_PLAYER_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin text domain for translations
 */
function brmedia_player_load_textdomain() {
    load_plugin_textdomain( 'brmedia-player', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'brmedia_player_load_textdomain' );

/**
 * Include necessary plugin files
 */
require_once BRMEDIA_PLAYER_DIR . 'includes/class-brmedia-player.php';
require_once BRMEDIA_PLAYER_DIR . 'includes/class-audio-player.php';
require_once BRMEDIA_PLAYER_DIR . 'includes/class-video-player.php';
require_once BRMEDIA_PLAYER_DIR . 'includes/class-radio-player.php';
require_once BRMEDIA_PLAYER_DIR . 'includes/class-podcast-player.php';
require_once BRMEDIA_PLAYER_DIR . 'includes/class-footer-player.php';
require_once BRMEDIA_PLAYER_DIR . 'includes/class-download-buttons.php';

/**
 * Enqueue frontend scripts and styles from CDNs and local assets
 */
function brmedia_player_enqueue_scripts() {
    // Font Awesome
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), '6.0.0' );

    // Plyr (video/audio player)
    wp_enqueue_script( 'plyr', 'https://cdn.plyr.io/3.6.8/plyr.js', array(), '3.6.8', true );
    wp_enqueue_style( 'plyr', 'https://cdn.plyr.io/3.6.8/plyr.css', array(), '3.6.8' );

    // Wavesurfer (audio waveform visualization)
    wp_enqueue_script( 'wavesurfer', 'https://unpkg.com/wavesurfer.js@5.2.0/dist/wavesurfer.min.js', array(), '5.2.0', true );

    // Chart.js (for visualizations or analytics if needed)
    wp_enqueue_script( 'chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js', array(), '3.7.1', true );

    // Custom frontend scripts and styles
    wp_enqueue_script( 'brmedia-frontend', BRMEDIA_PLAYER_URL . 'assets/js/frontend.js', array( 'jquery', 'plyr', 'wavesurfer', 'chartjs' ), BRMEDIA_PLAYER_VERSION, true );
    wp_enqueue_style( 'brmedia-frontend', BRMEDIA_PLAYER_URL . 'assets/css/frontend.css', array(), BRMEDIA_PLAYER_VERSION );
}
add_action( 'wp_enqueue_scripts', 'brmedia_player_enqueue_scripts' );

/**
 * Enqueue admin scripts and styles
 */
function brmedia_player_enqueue_admin_scripts() {
    wp_enqueue_style( 'brmedia-admin', BRMEDIA_PLAYER_URL . 'assets/css/admin.css', array(), BRMEDIA_PLAYER_VERSION );
    wp_enqueue_script( 'brmedia-admin', BRMEDIA_PLAYER_URL . 'assets/js/admin.js', array( 'jquery' ), BRMEDIA_PLAYER_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'brmedia_player_enqueue_admin_scripts' );

/**
 * Add quick links to the plugin page in WordPress admin
 */
function brmedia_player_action_links( $links ) {
    $settings_link = '<a href="' . admin_url( 'admin.php?page=brmedia-settings' ) . '">' . __( 'Settings', 'brmedia-player' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'brmedia_player_action_links' );

/**
 * Initialize the plugin
 */
if ( class_exists( 'BRMedia_Player' ) ) {
    $brmedia_player = new BRMedia_Player();
}

/**
 * Activation hook
 */
function brmedia_player_activate() {
    // Ensure the main class is available
    if ( class_exists( 'BRMedia_Player' ) ) {
        BRMedia_Player::activate();
    }
}
register_activation_hook( __FILE__, 'brmedia_player_activate' );

/**
 * Deactivation hook
 */
function brmedia_player_deactivate() {
    // Ensure the main class is available
    if ( class_exists( 'BRMedia_Player' ) ) {
        BRMedia_Player::deactivate();
    }
}
register_deactivation_hook( __FILE__, 'brmedia_player_deactivate' );