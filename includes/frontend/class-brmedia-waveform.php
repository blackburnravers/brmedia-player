<?php
/**
 * BRMedia Waveform Class
 *
 * Manages basic waveform rendering.
 *
 * @package BRMedia\Includes\Frontend
 */

namespace BRMedia\Includes\Frontend;

class BRMedia_Waveform {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Enqueues scripts for waveform rendering
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'wavesurfer',
            'https://unpkg.com/wavesurfer.js',
            [],
            null,
            true
        );
    }

    /**
     * Renders the waveform for a media ID
     *
     * @param int $media_id Media post ID
     * @return string Rendered HTML
     */
    public function render_waveform($media_id) {
        $url = get_post_meta($media_id, '_brmedia_music_url', true);
        $waveform_id = 'waveform-' . uniqid();
        ob_start();
        ?>
        <div id="<?php echo esc_attr($waveform_id); ?>" class="brmedia-waveform"></div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var wavesurfer = WaveSurfer.create({
                container: '#<?php echo esc_js($waveform_id); ?>',
                waveColor: 'grey',
                progressColor: 'blue',
            });
            wavesurfer.load('<?php echo esc_url($url); ?>');
        });
        </script>
        <?php
        return ob_get_clean();
    }
}