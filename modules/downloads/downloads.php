<?php
/**
 * BRMedia Player Downloads Module
 *
 * Registers the 'brdownloads' custom post type and defines the [brmedia_download] shortcode
 * for secure file downloads.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the 'brdownloads' custom post type
 */
function brmedia_register_downloads_post_type() {
    $labels = array(
        'name'               => 'Downloads',
        'singular_name'      => 'Download',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Download',
        'edit_item'          => 'Edit Download',
        'new_item'           => 'New Download',
        'view_item'          => 'View Download',
        'search_items'       => 'Search Downloads',
        'not_found'          => 'No downloads found',
        'not_found_in_trash' => 'No downloads found in Trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'taxonomies'         => array('brmedia_category', 'brmedia_tag'),
        'menu_icon'          => 'dashicons-download',
        'rewrite'            => array('slug' => 'downloads'),
    );

    register_post_type('brdownloads', $args);
}
add_action('init', 'brmedia_register_downloads_post_type');

/**
 * Define the [brmedia_download] shortcode
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output for the download button
 */
function brmedia_download_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id'       => 0,
        'template' => 'button',
    ), $atts);

    $post_id = intval($atts['id']);
    if (!$post_id || get_post_type($post_id) !== 'brdownloads') {
        return '<p>Invalid download ID.</p>';
    }

    $file_url = get_post_meta($post_id, 'brmedia_download_file_url', true);
    if (!$file_url) {
        return '<p>No file found for download.</p>';
    }

    $download_url = BRMedia_Helpers::generate_download_url($post_id);
    $title = get_the_title($post_id);

    $template_path = BRMedia_Helpers::get_template_path('download', $atts['template']);
    if (!file_exists($template_path)) {
        return '<p>Download template not found.</p>';
    }

    ob_start();
    include $template_path;
    return ob_get_clean();
}
add_shortcode('brmedia_download', 'brmedia_download_shortcode');

/**
 * Add metabox for download file
 */
function brmedia_downloads_metabox() {
    add_meta_box(
        'brmedia_download_file',
        'Download File Details',
        'brmedia_download_file_callback',
        'brdownloads',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'brmedia_downloads_metabox');

/**
 * Metabox callback
 *
 * @param WP_Post $post The current post object
 */
function brmedia_download_file_callback($post) {
    wp_nonce_field('brmedia_download_file_nonce', 'brmedia_download_file_nonce');
    $file_url = get_post_meta($post->ID, 'brmedia_download_file_url', true);
    ?>
    <p>
        <label for="brmedia_download_file_url">File URL:</label><br>
        <input type="url" name="brmedia_download_file_url" id="brmedia_download_file_url" value="<?php echo esc_attr($file_url); ?>" style="width: 100%;" />
    </p>
    <?php
}

/**
 * Save download metabox data
 *
 * @param int $post_id The post ID
 */
function brmedia_save_download_file($post_id) {
    if (!isset($_POST['brmedia_download_file_nonce']) || !wp_verify_nonce($_POST['brmedia_download_file_nonce'], 'brmedia_download_file_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['brmedia_download_file_url'])) {
        update_post_meta($post_id, 'brmedia_download_file_url', esc_url_raw($_POST['brmedia_download_file_url']));
    }
}
add_action('save_post', 'brmedia_save_download_file');