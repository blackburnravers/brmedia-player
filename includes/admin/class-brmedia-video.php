<?php
/**
 * BRMedia Video Class
 *
 * Manages the brvideo CPT with basic features.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Video {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_brvideo', [$this, 'save_metadata']);
    }

    /**
     * Registers the brvideo custom post type
     */
    public function register_post_type() {
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

    /**
     * Adds meta boxes for brvideo CPT
     */
    public function add_meta_boxes() {
        add_meta_box(
            'brmedia_video',           // ID
            'Video Settings',          // Title
            [$this, 'render_meta_box'],// Callback
            'brvideo',                 // Post type
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
        wp_nonce_field('brmedia_video_nonce', 'brmedia_video_nonce');
        $url = get_post_meta($post->ID, '_brmedia_video_url', true);
        ?>
        <p><label>Video URL: <input type="text" name="brmedia_video_url" value="<?php echo esc_attr($url); ?>"></label></p>
        <?php
    }

    /**
     * Saves metadata
     *
     * @param int $post_id Post ID
     */
    public function save_metadata($post_id) {
        if (!isset($_POST['brmedia_video_nonce']) || !wp_verify_nonce($_POST['brmedia_video_nonce'], 'brmedia_video_nonce')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        update_post_meta($post_id, '_brmedia_video_url', esc_url_raw($_POST['brmedia_video_url']));
    }
}