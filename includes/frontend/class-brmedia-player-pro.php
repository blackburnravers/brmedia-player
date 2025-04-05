<?php
/**
 * BRMedia Player Pro Class
 *
 * Manages an advanced media player with HLS/DASH streaming.
 *
 * @package BRMedia\Includes\Frontend
 */

namespace BRMedia\Includes\Frontend;

class BRMedia_Player_Pro {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Enqueues scripts for the advanced player
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'hls',
            'https://cdn.jsdelivr.net/npm/hls.js@latest',
            [],
            null,
            true
        );
    }

    /**
     * Renders the player with a given media ID and template
     *
     * @param int $media_id Media post ID
     * @param string $template Template name
     * @return string Rendered HTML
     */
    public function render_player($media_id, $template = 'audio-reactive') {
        $url = get_post_meta($media_id, '_brmedia_music_url', true) ?: get_post_meta($media_id, '_brmedia_video_url', true);
        ob_start();
        ?>
        <div class="brmedia-player brmedia-<?php echo esc_attr($template); ?>" data-media-id="<?php echo esc_attr($media_id); ?>">
            <?php if (strpos($url, '.m3u8') !== false) : ?>
                <video class="plyr" data-type="hls">
                    <source src="<?php echo esc_url($url); ?>" type="application/x-mpegURL">
                </video>
            <?php else : ?>
                <audio class="plyr">
                    <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
                </audio>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}