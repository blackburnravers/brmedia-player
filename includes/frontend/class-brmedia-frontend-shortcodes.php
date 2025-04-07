<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Frontend_Shortcodes {

    public function __construct() {
        // Register shortcodes
        add_shortcode( 'brmedia_music_list', array( $this, 'music_list_shortcode' ) );
        add_shortcode( 'brmedia_music_player', array( $this, 'music_player_shortcode' ) );
    }

    // Shortcode to display a list of music posts
    public function music_list_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'count' => 5,
        ), $atts, 'brmedia_music_list' );

        $query_args = array(
            'post_type' => 'brmusic',
            'posts_per_page' => intval( $atts['count'] ),
        );

        $query = new WP_Query( $query_args );

        if ( $query->have_posts() ) {
            ob_start();

            echo '<ul class="brmedia-music-list">';
            while ( $query->have_posts() ) {
                $query->the_post();
                echo '<li>';
                echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                echo '</li>';
            }
            echo '</ul>';

            wp_reset_postdata();

            return ob_get_clean();
        } else {
            return '<p>' . __( 'No music found', 'brmedia-player' ) . '</p>';
        }
    }

    // Shortcode to display a music player for a specific music post
    public function music_player_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => '',
        ), $atts, 'brmedia_music_player' );

        if ( empty( $atts['id'] ) ) {
            return '<p>' . __( 'No music ID provided', 'brmedia-player' ) . '</p>';
        }

        $post_id = intval( $atts['id'] );
        $post = get_post( $post_id );

        if ( ! $post || $post->post_type !== 'brmusic' ) {
            return '<p>' . __( 'Invalid music ID', 'brmedia-player' ) . '</p>';
        }

        ob_start();

        $music_file = get_post_meta( $post_id, '_brmusic_file', true );
        $music_url = get_post_meta( $post_id, '_brmusic_url', true );
        $audio_source = $music_file ? $music_file : $music_url;

        // Display player and information
        echo '<div class="brmedia-music-player" id="waveform-' . esc_attr( $post_id ) . '" data-audio-source="' . esc_url( $audio_source ) . '"></div>';
        echo '<button id="play-pause-' . esc_attr( $post_id ) . '"><i class="fas fa-play"></i></button>';

        echo '<h2>' . esc_html( get_the_title( $post_id ) ) . '</h2>';
        echo '<p>' . esc_html( get_post_meta( $post_id, '_brmusic_artist', true ) ) . '</p>';
        echo '<p>' . esc_html( get_post_meta( $post_id, '_brmusic_album', true ) ) . '</p>';
        echo '<p>' . esc_html( get_post_meta( $post_id, '_brmusic_year', true ) ) . '</p>';

        // Display cover image (featured image)
        if ( has_post_thumbnail( $post_id ) ) {
            echo get_the_post_thumbnail( $post_id, 'medium' );
        }

        // Display tracklist
        $tracklist_file = get_post_meta( $post_id, '_brmusic_tracklist', true );
        $tracklist_url = get_post_meta( $post_id, '_brmusic_tracklist_url', true );
        if ( $tracklist_file || $tracklist_url ) {
            $tracklist_source = $tracklist_file ? $tracklist_file : $tracklist_url;
            $tracklist_content = file_get_contents( $tracklist_source );

            if ( $tracklist_content ) {
                echo '<h3>' . __( 'Tracklist', 'brmedia-player' ) . '</h3>';
                echo '<pre>' . esc_html( $tracklist_content ) . '</pre>';
            }
        }

        echo '</div>';

        return ob_get_clean();
    }
}

new BRMedia_Frontend_Shortcodes();