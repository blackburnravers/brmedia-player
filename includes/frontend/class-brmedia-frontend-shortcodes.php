<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Frontend_Shortcodes {

    public function __construct() {
        // Register shortcodes
        add_shortcode( 'brmedia_audio', array( $this, 'audio_shortcode' ) );
        add_shortcode( 'brmedia_video', array( $this, 'video_shortcode' ) );
        // Add other shortcodes as needed
    }

    public function audio_shortcode( $atts ) {
        // Audio shortcode logic goes here
    }

    public function video_shortcode( $atts ) {
        // Video shortcode logic goes here
    }
}

new BRMedia_Frontend_Shortcodes();