<?php
/**
 * BRMedia Player Video Module
 *
 * Registers the 'brvideo' custom post type and defines the [brmedia_video] shortcode.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the 'brvideo' custom post type
 */
function brmedia_register_video_post_type() {
    $labels = array(
        'name'               => 'Videos',
        'singular_name'      => 'Video',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Video',
        'edit_item'          => 'Edit Video',
        'new_item'           => 'New Video',
        'view_item'          => 'View Video',
        'search_items'       => 'Search Videos',
        'not_found'          => 'No videos found',
        'not_found_in_trash' => 'No videos found in Trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'taxonomies'         => array('brmedia_category', 'brmedia_tag'),
        'menu_icon'          => 'dashicons-video-alt3',
        'rewrite'            => array('slug' => 'videos'),
    );

    register_post_type('brvideo', $args);
}
add_action('init', 'brmedia_register_video_post_type');

/**
 * Define the [brmedia_video] shortcode
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output for the video player
 */
function brmedia_video_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id'       => 0,
        'template' => 'default',
        'width'    => '640',
        'height'   => '360',
    ), $atts);

    $post_id = intval($atts['id']);
    if (!$post_id || get_post_type($post_id) !== 'brvideo') {
        return '<p>Invalid video ID.</p>';
    }

    $video_url = get_post_meta($post_id, 'brmedia_video_url', true);
    if (!$video_url) {
        return '<p>No video file found.</p>';
    }

    $title = get_the_title($post_id);
    $width = intval($atts['width']);
    $height = intval($atts['height']);

    $template_path = BRMedia_Helpers::get_template_path('video', $atts['template']);
    if (!file_exists($template_path)) {
        return '<p>Video template not found.</p>';
    }

    ob_start();
    include $template_path;
    return ob_get_clean();
}
add_shortcode('brmedia_video', 'brmedia_video_shortcode');

/**
 * Add metabox for video URL
 */
function brmedia_video_metabox() {
    add_meta_box(
        'brmedia_video_url',
        'Video File Details',
        'brmedia_video_url_callback',
        'brvideo',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'brmedia_video_metabox');

/**
 * Metabox callback
 *
 * @param WP_Post $post The current post object
 */
function brmedia_video_url_callback($post) {
    wp_nonce_field('brmedia_video_url_nonce', 'brmedia_video_url_nonce');
    $video_url = get_post_meta($post->ID, 'brmedia_video_url', true);
    ?>
    <p>
        <label for="brmedia_video_url">Video URL:</label><br>
        <input type="url" name="brmedia_video_url" id="brmedia_video_url" value="<?php echo esc_attr($video_url); ?>" style="width: 100%;" />
    </p>
    <?php
}

/**
 * Save video metabox data
 *
 * @param int $post_id The post ID
 */
function brmedia_save_video_url($post_id) {
    if (!isset($_POST['brmedia_video_url_nonce']) || !wp_verify_nonce($_POST['brmedia_video_url_nonce'], 'brmedia_video_url_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['brmedia_video_url'])) {
        update_post_meta($post_id, 'brmedia_video_url', esc_url_raw($_POST['brmedia_video_url']));
    }
}
add_action('save_post', 'brmedia_save_video_url');