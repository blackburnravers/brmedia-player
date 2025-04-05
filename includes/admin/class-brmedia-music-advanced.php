<?php
/**
 * BRMedia Music Advanced Class
 *
 * Manages advanced features for the brmusic CPT.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Music_Advanced {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_brmusic', [$this, 'save_metadata']);
    }

    /**
     * Adds meta boxes for brmusic CPT
     */
    public function add_meta_boxes() {
        add_meta_box(
            'brmedia_music_advanced',      // ID
            'Advanced Music Settings',     // Title
            [$this, 'render_meta_box'],    // Callback
            'brmusic',                     // Post type
            'normal',                      // Context
            'high'                         // Priority
        );
    }

    /**
     * Renders the meta box content
     *
     * @param WP_Post $post Post object
     */
    public function render_meta_box($post) {
        wp_nonce_field('brmedia_music_advanced_nonce', 'brmedia_music_advanced_nonce');
        $bpm = get_post_meta($post->ID, '_brmedia_bpm', true);
        $artist = get_post_meta($post->ID, '_brmedia_artist', true);
        ?>
        <p><label>BPM: <input type="number" name="brmedia_bpm" value="<?php echo esc_attr($bpm); ?>"></label></p>
        <p><label>Artist: <input type="text" name="brmedia_artist" value="<?php echo esc_attr($artist); ?>"></label></p>
        <?php
    }

    /**
     * Saves metadata and applies AI-driven BPM detection (placeholder)
     *
     * @param int $post_id Post ID
     */
    public function save_metadata($post_id) {
        if (!isset($_POST['brmedia_music_advanced_nonce']) || !wp_verify_nonce($_POST['brmedia_music_advanced_nonce'], 'brmedia_music_advanced_nonce')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        update_post_meta($post_id, '_brmedia_bpm', sanitize_text_field($_POST['brmedia_bpm']));
        update_post_meta($post_id, '_brmedia_artist', sanitize_text_field($_POST['brmedia_artist']));
        // Placeholder for AI BPM detection
    }
}