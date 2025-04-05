<?php
/**
 * BRMedia Music Class
 *
 * Manages the brmusic CPT with basic features.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Music {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_brmusic', [$this, 'save_metadata']);
    }

    /**
     * Registers the brmusic custom post type
     */
    public function register_post_type() {
        register_post_type('brmusic', [
            'labels' => [
                'name' => 'Music Tracks',
                'singular_name' => 'Music Track',
            ],
            'public' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_icon' => 'dashicons-format-audio',
        ]);
    }

    /**
     * Adds meta boxes for brmusic CPT
     */
    public function add_meta_boxes() {
        add_meta_box(
            'brmedia_music',           // ID
            'Music Settings',          // Title
            [$this, 'render_meta_box'],// Callback
            'brmusic',                 // Post type
            'normal',                  // Context
            'default'                  // Priority
        );
    }

    /**
     * Renders the meta box content
     *
     * @param WP_Post $post Post object
     */
    public function render_meta_box($post) {
        wp_nonce_field('brmedia_music_nonce', 'brmedia_music_nonce');
        $url = get_post_meta($post->ID, '_brmedia_music_url', true);
        ?>
        <p><label>Audio URL: <input type="text" name="brmedia_music_url" value="<?php echo esc_attr($url); ?>"></label></p>
        <?php
    }

    /**
     * Saves metadata
     *
     * @param int $post_id Post ID
     */
    public function save_metadata($post_id) {
        if (!isset($_POST['brmedia_music_nonce']) || !wp_verify_nonce($_POST['brmedia_music_nonce'], 'brmedia_music_nonce')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        update_post_meta($post_id, '_brmedia_music_url', esc_url_raw($_POST['brmedia_music_url']));
    }
}