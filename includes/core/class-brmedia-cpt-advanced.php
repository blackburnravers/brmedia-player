<?php
/**
 * BRMedia CPT Advanced Class
 *
 * Manages advanced custom post types with taxonomies and metadata.
 *
 * @package BRMedia\Includes\Core
 */

namespace BRMedia\Includes\Core;

class BRMedia_CPT_Advanced {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_taxonomies']);
    }

    /**
     * Registers advanced custom post types
     */
    public function register_post_types() {
        register_post_type('brmusic', [
            'labels' => [
                'name' => 'Music Tracks',
                'singular_name' => 'Music Track',
            ],
            'public' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'menu_icon' => 'dashicons-format-audio',
            'taxonomies' => ['brmedia_genre'],
        ]);

        register_post_type('brvideo', [
            'labels' => [
                'name' => 'Videos',
                'singular_name' => 'Video',
            ],
            'public' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'menu_icon' => 'dashicons-video-alt3',
            'taxonomies' => ['brmedia_category'],
        ]);
    }

    /**
     * Registers custom taxonomies
     */
    public function register_taxonomies() {
        register_taxonomy('brmedia_genre', 'brmusic', [
            'labels' => [
                'name' => 'Genres',
                'singular_name' => 'Genre',
            ],
            'hierarchical' => true,
            'show_admin_column' => true,
        ]);

        register_taxonomy('brmedia_category', 'brvideo', [
            'labels' => [
                'name' => 'Categories',
                'singular_name' => 'Category',
            ],
            'hierarchical' => true,
            'show_admin_column' => true,
        ]);
    }
}