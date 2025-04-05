<?php
/**
 * BRMedia Shortcodes Pro Class
 *
 * Handles advanced shortcode generation with live previews.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Shortcodes_Pro {
    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('brmedia_audio_pro', [$this, 'audio_shortcode']);
        add_action('admin_menu', [$this, 'add_shortcode_page']);
    }

    /**
     * Registers shortcode generator page
     */
    public function add_shortcode_page() {
        add_submenu_page(
            'brmedia-dashboard',           // Parent slug
            'Shortcode Generator',         // Page title
            'Shortcodes Pro',              // Menu title
            'manage_options',              // Capability
            'brmedia-shortcodes-pro',      // Menu slug
            [$this, 'render_shortcode_page'] // Callback
        );
    }

    /**
     * Renders shortcode generator page
     */
    public function render_shortcode_page() {
        ?>
        <div class="wrap">
            <h1>Shortcode Generator (Pro)</h1>
            <input type="text" id="shortcode-preview" value="[brmedia_audio_pro id='1' template='audio-reactive']">
            <div id="live-preview"></div>
        </div>
        <?php
    }

    /**
     * Advanced audio shortcode handler
     *
     * @param array $atts Shortcode attributes
     * @return string Rendered HTML
     */
    public function audio_shortcode($atts) {
        $atts = shortcode_atts([
            'id' => 0,
            'template' => 'audio-reactive',
        ], $atts, 'brmedia_audio_pro');

        ob_start();
        ?>
        <div class="brmedia-player" data-template="<?php echo esc_attr($atts['template']); ?>" data-media-id="<?php echo esc_attr($atts['id']); ?>">
            <audio class="plyr">
                <source src="<?php echo esc_url(get_post_meta($atts['id'], '_brmedia_music_url', true)); ?>" type="audio/mp3">
            </audio>
        </div>
        <?php
        return ob_get_clean();
    }
}