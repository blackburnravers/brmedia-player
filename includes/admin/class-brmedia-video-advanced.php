<?php
/**
 * BRMedia Video Advanced Class
 *
 * Manages advanced features for the brvideo CPT, including auto-transcoding.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Video_Advanced extends BRMedia_Video {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        add_action('add_meta_boxes', [$this, 'add_advanced_meta_boxes']);
        add_action('save_post_brvideo', [$this, 'save_advanced_metadata']);
    }

    /**
     * Adds advanced meta boxes for brvideo CPT
     */
    public function add_advanced_meta_boxes() {
        add_meta_box(
            'brmedia_video_advanced',      // ID
            'Advanced Video Settings',     // Title
            [$this, 'render_advanced_meta_box'], // Callback
            'brvideo',                     // Post type
            'normal',                      // Context
            'high'                         // Priority
        );
    }

    /**
     * Renders the advanced meta box content
     *
     * @param WP_Post $post Post object
     */
    public function render_advanced_meta_box($post) {
        wp_nonce_field('brmedia_video_advanced_nonce', 'brmedia_video_advanced_nonce');
        $duration = get_post_meta($post->ID, '_brmedia_video_duration', true);
        $transcode = get_post_meta($post->ID, '_brmedia_auto_transcode', true);
        ?>
        <p><label>Duration (seconds): <input type="number" name="brmedia_video_duration" value="<?php echo esc_attr($duration); ?>"></label></p>
        <p><label>Auto-Transcode: <input type="checkbox" name="brmedia_auto_transcode" value="1" <?php checked($transcode, 1); ?>></label></p>
        <?php
    }

    /**
     * Saves advanced metadata and triggers auto-transcoding (placeholder)
     *
     * @param int $post_id Post ID
     */
    public function save_advanced_metadata($post_id) {
        if (!isset($_POST['brmedia_video_advanced_nonce']) || !wp_verify_nonce($_POST['brmedia_video_advanced_nonce'], 'brmedia_video_advanced_nonce')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        update_post_meta($post_id, '_brmedia_video_duration', sanitize_text_field($_POST['brmedia_video_duration']));
        $transcode = isset($_POST['brmedia_auto_transcode']) ? 1 : 0;
        update_post_meta($post_id, '_brmedia_auto_transcode', $transcode);
        if ($transcode) {
            // Placeholder for auto-transcoding logic (e.g., FFmpeg integration)
            $this->trigger_transcoding($post_id);
        }
    }

    /**
     * Triggers video transcoding (placeholder)
     *
     * @param int $post_id Post ID
     */
    private function trigger_transcoding($post_id) {
        // Placeholder for actual transcoding logic
        error_log("Transcoding triggered for video ID: $post_id");
    }
}