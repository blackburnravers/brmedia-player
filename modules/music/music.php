<?php
/**
 * BRMedia Player Music Module
 *
 * Registers the 'brmusic' custom post type, defines the [brmedia_audio] shortcode,
 * and provides metaboxes for audio file management.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the 'brmusic' custom post type
 */
function brmedia_register_music_post_type() {
    $labels = array(
        'name'               => 'Music',
        'singular_name'      => 'Music',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Music',
        'edit_item'          => 'Edit Music',
        'new_item'           => 'New Music',
        'view_item'          => 'View Music',
        'search_items'       => 'Search Music',
        'not_found'          => 'No music found',
        'not_found_in_trash' => 'No music found in Trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'taxonomies'         => array('brmedia_category', 'brmedia_tag'),
        'menu_icon'          => 'dashicons-format-audio',
        'rewrite'            => array('slug' => 'music'),
    );

    register_post_type('brmusic', $args);
}
add_action('init', 'brmedia_register_music_post_type');

/**
 * Define the [brmedia_audio] shortcode
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output for the audio player
 */
function brmedia_audio_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id'       => 0,
        'template' => 'default',
        'autoplay' => 'no',
    ), $atts);

    $post_id = intval($atts['id']);
    if (!$post_id || get_post_type($post_id) !== 'brmusic') {
        return '<p>Invalid music ID.</p>';
    }

    $audio_url = get_post_meta($post_id, 'brmedia_audio_url', true);
    if (!$audio_url) {
        return '<p>No audio file found.</p>';
    }

    $title = get_the_title($post_id);
    $autoplay = ($atts['autoplay'] === 'yes') ? 'autoplay' : '';

    // Get template path (assumes a helper class exists)
    $template_path = BRMedia_Helpers::get_template_path('audio', $atts['template']);
    if (!file_exists($template_path)) {
        return '<p>Audio template not found.</p>';
    }

    ob_start();
    include $template_path;
    return ob_get_clean();
}
add_shortcode('brmedia_audio', 'brmedia_audio_shortcode');

/**
 * Add metabox for audio file URL
 */
function brmedia_music_metabox() {
    add_meta_box(
        'brmedia_music_audio',
        'Audio File Details',
        'brmedia_music_audio_callback',
        'brmusic',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'brmedia_music_metabox');

/**
 * Metabox callback to display input fields
 *
 * @param WP_Post $post The current post object
 */
function brmedia_music_audio_callback($post) {
    wp_nonce_field('brmedia_music_audio_nonce', 'brmedia_music_audio_nonce');
    $audio_url = get_post_meta($post->ID, 'brmedia_audio_url', true);
    $duration = get_post_meta($post->ID, 'brmedia_audio_duration', true);
    ?>
    <p>
        <label for="brmedia_audio_url">Audio URL:</label><br>
        <input type="url" name="brmedia_audio_url" id="brmedia_audio_url" value="<?php echo esc_attr($audio_url); ?>" style="width: 100%;" />
    </p>
    <p>
        <label for="brmedia_audio_duration">Duration (e.g., 03:45):</label><br>
        <input type="text" name="brmedia_audio_duration" id="brmedia_audio_duration" value="<?php echo esc_attr($duration); ?>" style="width: 100%;" />
    </p>
    <?php
}

/**
 * Save metabox data
 *
 * @param int $post_id The post ID
 */
function brmedia_save_music_audio($post_id) {
    if (!isset($_POST['brmedia_music_audio_nonce']) || !wp_verify_nonce($_POST['brmedia_music_audio_nonce'], 'brmedia_music_audio_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['brmedia_audio_url'])) {
        update_post_meta($post_id, 'brmedia_audio_url', esc_url_raw($_POST['brmedia_audio_url']));
    }
    if (isset($_POST['brmedia_audio_duration'])) {
        update_post_meta($post_id, 'brmedia_audio_duration', sanitize_text_field($_POST['brmedia_audio_duration']));
    }
}
add_action('save_post', 'brmedia_save_music_audio');