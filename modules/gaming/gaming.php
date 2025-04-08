<?php
/**
 * BRMedia Player Gaming Module
 *
 * Registers the 'brgaming' custom post type and defines the [brmedia_gaming] shortcode.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the 'brgaming' custom post type
 */
function brmedia_register_gaming_post_type() {
    $labels = array(
        'name'               => 'Gaming',
        'singular_name'      => 'Gaming',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Gaming Content',
        'edit_item'          => 'Edit Gaming Content',
        'new_item'           => 'New Gaming Content',
        'view_item'          => 'View Gaming Content',
        'search_items'       => 'Search Gaming Content',
        'not_found'          => 'No gaming content found',
        'not_found_in_trash' => 'No gaming content found in Trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'taxonomies'         => array('brmedia_category', 'brmedia_tag'),
        'menu_icon'          => 'dashicons-games',
        'rewrite'            => array('slug' => 'gaming'),
    );

    register_post_type('brgaming', $args);
}
add_action('init', 'brmedia_register_gaming_post_type');

/**
 * Define the [brmedia_gaming] shortcode
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output for gaming content
 */
function brmedia_gaming_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id'       => 0,
        'template' => 'default',
    ), $atts);

    $post_id = intval($atts['id']);
    if (!$post_id || get_post_type($post_id) !== 'brgaming') {
        return '<p>Invalid gaming ID.</p>';
    }

    $gaming_content = get_post_meta($post_id, 'brmedia_gaming_content', true);
    if (!$gaming_content) {
        return '<p>No gaming content found.</p>';
    }

    $title = get_the_title($post_id);
    $template_path = BRMedia_Helpers::get_template_path('gaming', $atts['template']);
    if (!file_exists($template_path)) {
        return '<p>Gaming template not found.</p>';
    }

    ob_start();
    include $template_path;
    return ob_get_clean();
}
add_shortcode('brmedia_gaming', 'brmedia_gaming_shortcode');

/**
 * Add metabox for gaming content
 */
function brmedia_gaming_metabox() {
    add_meta_box(
        'brmedia_gaming_content',
        'Gaming Content',
        'brmedia_gaming_content_callback',
        'brgaming',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'brmedia_gaming_metabox');

/**
 * Metabox callback
 *
 * @param WP_Post $post The current post object
 */
function brmedia_gaming_content_callback($post) {
    wp_nonce_field('brmedia_gaming_content_nonce', 'brmedia_gaming_content_nonce');
    $gaming_content = get_post_meta($post->ID, 'brmedia_gaming_content', true);
    ?>
    <p>
        <label for="brmedia_gaming_content">Gaming Content (e.g., Stream URL or Embed Code):</label><br>
        <textarea name="brmedia_gaming_content" id="brmedia_gaming_content" style="width: 100%; height: 100px;"><?php echo esc_textarea($gaming_content); ?></textarea>
    </p>
    <?php
}

/**
 * Save gaming metabox data
 *
 * @param int $post_id The post ID
 */
function brmedia_save_gaming_content($post_id) {
    if (!isset($_POST['brmedia_gaming_content_nonce']) || !wp_verify_nonce($_POST['brmedia_gaming_content_nonce'], 'brmedia_gaming_content_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['brmedia_gaming_content'])) {
        update_post_meta($post_id, 'brmedia_gaming_content', wp_kses_post($_POST['brmedia_gaming_content']));
    }
}
add_action('save_post', 'brmedia_save_gaming_content');