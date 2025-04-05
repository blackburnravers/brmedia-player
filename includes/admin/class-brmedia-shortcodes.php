<?php
/**
 * BRMedia Shortcodes Class
 *
 * Manages basic shortcode generation.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Shortcodes {
    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('brmedia_audio', [$this, 'audio_shortcode']);
    }

    /**
     * Basic audio shortcode handler
     *
     * @param array $atts Shortcode attributes
     * @return string Rendered HTML
     */
    public function audio_shortcode($atts) {
        $atts = shortcode_atts([
            'id' => 0,
        ], $atts, 'brmedia_audio');

        ob_start();
        ?>
        <div class="brmedia-player" data-media-id="<?php echo esc_attr($atts['id']); ?>">
            <audio class="plyr">
                <source src="<?php echo esc_url(get_post_meta($atts['id'], '_brmedia_music_url', true)); ?>" type="audio/mp3">
            </audio>
        </div>
        <?php
        return ob_get_clean();
    }
}