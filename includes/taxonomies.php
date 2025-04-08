<?php
/**
 * BRMedia Player Taxonomies
 *
 * Registers shared taxonomies for custom post types.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register taxonomies
 */
function brmedia_register_taxonomies() {
    $post_types = array('brmusic', 'brvideo', 'brdownloads', 'brgaming');

    // Register Category Taxonomy
    $category_args = array(
        'hierarchical' => true,
        'labels' => array(
            'name' => __('Categories', 'brmedia-player'),
            'singular_name' => __('Category', 'brmedia-player'),
        ),
        'show_in_rest' => true,
        'public' => true,
    );
    register_taxonomy('brmedia_category', $post_types, $category_args);

    // Register Tag Taxonomy
    $tag_args = array(
        'hierarchical' => false,
        'labels' => array(
            'name' => __('Tags', 'brmedia-player'),
            'singular_name' => __('Tag', 'brmedia-player'),
        ),
        'show_in_rest' => true,
        'public' => true,
    );
    register_taxonomy('brmedia_tag', $post_types, $tag_args);
}
add_action('init', 'brmedia_register_taxonomies');