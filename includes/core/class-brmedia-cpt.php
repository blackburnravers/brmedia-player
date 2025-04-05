<?php
/**
 * BRMedia CPT Class
 *
 * Manages basic custom post types.
 *
 * @package BRMedia\Includes\Core
 */

namespace BRMedia\Includes\Core;

class BRMedia_CPT {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register_post_types']);
    }

    /**
     * Registers basic custom post types
     */
    public function register_post_types() {
        register_post_type('brmusic', [
            'labels' => [
                'name' => 'Music Tracks',
                'singular_name' => 'Music Track',
            ],
            'public' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_icon' => 'dashicons-format-audio',
        ]);

        register_post_type('brvideo', [
            'labels' => [
                'name' => 'Videos',
                'singular_name' => 'Video',
            ],
            'public' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_icon' => 'dashicons-video-alt3',
        ]);
    }
}