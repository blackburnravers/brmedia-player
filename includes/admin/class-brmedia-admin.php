<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_admin_menu() {
        add_menu_page(
            __( 'BRMedia Player', 'brmedia-player' ),
            __( 'BRMedia', 'brmedia-player' ),
            'manage_options',
            'brmedia-player',
            array( $this, 'admin_dashboard' ),
            'dashicons-format-audio',
            6
        );

        add_submenu_page(
            'brmedia-player',
            __( 'Dashboard', 'brmedia-player' ),
            __( 'Dashboard', 'brmedia-player' ),
            'manage_options',
            'brmedia-player',
            array( $this, 'admin_dashboard' )
        );

        add_submenu_page(
            'brmedia-player',
            __( 'Music', 'brmedia-player' ),
            __( 'Music', 'brmedia-player' ),
            'manage_options',
            'brmedia-music',
            array( $this, 'admin_music' )
        );

        add_submenu_page(
            'brmedia-player',
            __( 'Video', 'brmedia-player' ),
            __( 'Video', 'brmedia-player' ),
            'manage_options',
            'brmedia-video',
            array( $this, 'admin_video' )
        );

        add_submenu_page(
            'brmedia-player',
            __( 'Radio', 'brmedia-player' ),
            __( 'Radio', 'brmedia-player' ),
            'manage_options',
            'brmedia-radio',
            array( $this, 'admin_radio' )
        );

        add_submenu_page(
            'brmedia-player',
            __( 'Templates Panel', 'brmedia-player' ),
            __( 'Templates Panel', 'brmedia-player' ),
            'manage_options',
            'brmedia-templates',
            array( $this, 'admin_templates' )
        );

        add_submenu_page(
            'brmedia-player',
            __( 'Shortcodes Manager', 'brmedia-player' ),
            __( 'Shortcodes Manager', 'brmedia-player' ),
            'manage_options',
            'brmedia-shortcodes',
            array( $this, 'admin_shortcodes' )
        );

        add_submenu_page(
            'brmedia-player',
            __( 'Statistics & Analytics', 'brmedia-player' ),
            __( 'Statistics & Analytics', 'brmedia-player' ),
            'manage_options',
            'brmedia-statistics',
            array( $this, 'admin_statistics' )
        );

        add_submenu_page(
            'brmedia-player',
            __( 'Widgets & SEO', 'brmedia-player' ),
            __( 'Widgets & SEO', 'brmedia-player' ),
            'manage_options',
            'brmedia-widgets',
            array( $this, 'admin_widgets' )
        );

        add_submenu_page(
            'brmedia-player',
            __( 'Social Share', 'brmedia-player' ),
            __( 'Social Share', 'brmedia-player' ),
            'manage_options',
            'brmedia-social',
            array( $this, 'admin_social' )
        );
    }

    public function register_settings() {
        // Register settings for each section as needed
    }

    public function admin_dashboard() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public function admin_music() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/music.php';
    }

    public function admin_video() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/video.php';
    }

    public function admin_radio() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/radio.php';
    }

    public function admin_templates() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/templates-panel.php';
    }

    public function admin_shortcodes() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/shortcodes-manager.php';
    }

    public function admin_statistics() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/statistics-analytics.php';
    }

    public function admin_widgets() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/widgets-seo.php';
    }

    public function admin_social() {
        include BRMEDIA_PLAYER_PLUGIN_DIR . 'admin/views/social-share.php';
    }
}

new BRMedia_Admin();