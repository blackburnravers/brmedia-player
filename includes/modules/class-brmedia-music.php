<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Music {

    public function __construct() {
        // Register hooks
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_custom_meta' ) );
    }

    // Register Music custom post type
    public function register_post_type() {
        $labels = array(
            'name' => __( 'Music', 'brmedia-player' ),
            'singular_name' => __( 'Music', 'brmedia-player' ),
            'add_new' => __( 'Add New', 'brmedia-player' ),
            'add_new_item' => __( 'Add New Music', 'brmedia-player' ),
            'edit_item' => __( 'Edit Music', 'brmedia-player' ),
            'new_item' => __( 'New Music', 'brmedia-player' ),
            'all_items' => __( 'All Music', 'brmedia-player' ),
            'view_item' => __( 'View Music', 'brmedia-player' ),
            'search_items' => __( 'Search Music', 'brmedia-player' ),
            'not_found' => __( 'No music found', 'brmedia-player' ),
            'not_found_in_trash' => __( 'No music found in Trash', 'brmedia-player' ),
            'menu_name' => __( 'Music', 'brmedia-player' ),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_position' => 5,
            'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
            'rewrite' => array( 'slug' => 'music' ),
            'show_in_rest' => true,
        );

        register_post_type( 'brmusic', $args );
    }

    // Add custom meta boxes
    public function add_custom_meta_boxes() {
        add_meta_box(
            'brmusic_meta_box',
            __( 'Music Details', 'brmedia-player' ),
            array( $this, 'render_meta_box' ),
            'brmusic',
            'normal',
            'high'
        );
    }

    // Render the custom meta box
    public function render_meta_box( $post ) {
        // Add nonce for security
        wp_nonce_field( 'brmusic_meta_box_nonce', 'meta_box_nonce' );

        // Retrieve current data
        $artist = get_post_meta( $post->ID, '_brmusic_artist', true );
        $album = get_post_meta( $post->ID, '_brmusic_album', true );
        $year = get_post_meta( $post->ID, '_brmusic_year', true );
        $music_file = get_post_meta( $post->ID, '_brmusic_file', true );
        $tracklist_file = get_post_meta( $post->ID, '_brmusic_tracklist', true );
        $music_url = get_post_meta( $post->ID, '_brmusic_url', true );
        $tracklist_url = get_post_meta( $post->ID, '_brmusic_tracklist_url', true );

        // Display form fields
        echo '<label for="brmusic_artist">' . __( 'Artist', 'brmedia-player' ) . '</label>';
        echo '<input type="text" name="brmusic_artist" id="brmusic_artist" value="' . esc_attr( $artist ) . '" size="25" />';
        echo '<br><br>';

        echo '<label for="brmusic_album">' . __( 'Album', 'brmedia-player' ) . '</label>';
        echo '<input type="text" name="brmusic_album" id="brmusic_album" value="' . esc_attr( $album ) . '" size="25" />';
        echo '<br><br>';

        echo '<label for="brmusic_year">' . __( 'Year', 'brmedia-player' ) . '</label>';
        echo '<input type="text" name="brmusic_year" id="brmusic_year" value="' . esc_attr( $year ) . '" size="4" />';
        echo '<br><br>';

        echo '<label for="brmusic_file">' . __( 'Music File', 'brmedia-player' ) . '</label>';
        echo '<input type="text" name="brmusic_file" id="brmusic_file" value="' . esc_attr( $music_file ) . '" size="25" />';
        echo '<button class="upload_button button">' . __( 'Upload', 'brmedia-player' ) . '</button>';
        echo '<br><br>';

        echo '<label for="brmusic_tracklist">' . __( 'Tracklist File', 'brmedia-player' ) . '</label>';
        echo '<input type="text" name="brmusic_tracklist" id="brmusic_tracklist" value="' . esc_attr( $tracklist_file ) . '" size="25" />';
        echo '<button class="upload_button button">' . __( 'Upload', 'brmedia-player' ) . '</button>';
        echo '<br><br>';

        echo '<label for="brmusic_url">' . __( 'Music URL', 'brmedia-player' ) . '</label>';
        echo '<input type="text" name="brmusic_url" id="brmusic_url" value="' . esc_attr( $music_url ) . '" size="25" />';
        echo '<br><br>';

        echo '<label for="brmusic_tracklist_url">' . __( 'Tracklist URL', 'brmedia-player' ) . '</label>';
        echo '<input type="text" name="brmusic_tracklist_url" id="brmusic_tracklist_url" value="' . esc_attr( $tracklist_url ) . '" size="25" />';
    }

    // Save custom meta box data
    public function save_custom_meta( $post_id ) {
        // Check nonce
        if ( ! isset( $_POST['meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['meta_box_nonce'], 'brmusic_meta_box_nonce' ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save data
        if ( isset( $_POST['brmusic_artist'] ) ) {
            update_post_meta( $post_id, '_brmusic_artist', sanitize_text_field( $_POST['brmusic_artist'] ) );
        }

        if ( isset( $_POST['brmusic_album'] ) ) {
            update_post_meta( $post_id, '_brmusic_album', sanitize_text_field( $_POST['brmusic_album'] ) );
        }

        if ( isset( $_POST['brmusic_year'] ) ) {
            update_post_meta( $post_id, '_brmusic_year', sanitize_text_field( $_POST['brmusic_year'] ) );
        }

        if ( isset( $_POST['brmusic_file'] ) ) {
            update_post_meta( $post_id, '_brmusic_file', sanitize_text_field( $_POST['brmusic_file'] ) );
        }

        if ( isset( $_POST['brmusic_tracklist'] ) ) {
            update_post_meta( $post_id, '_brmusic_tracklist', sanitize_text_field( $_POST['brmusic_tracklist'] ) );
        }

        if ( isset( $_POST['brmusic_url'] ) ) {
            update_post_meta( $post_id, '_brmusic_url', esc_url_raw( $_POST['brmusic_url'] ) );
        }

        if ( isset( $_POST['brmusic_tracklist_url'] ) ) {
            update_post_meta( $post_id, '_brmusic_tracklist_url', esc_url_raw( $_POST['brmusic_tracklist_url'] ) );
        }
    }
}

new BRMedia_Music();