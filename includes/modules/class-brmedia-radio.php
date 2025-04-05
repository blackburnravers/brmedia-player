<?php
/**
 * BRMedia Radio Module Class
 *
 * Manages live radio streaming functionality.
 *
 * @package BRMedia\Includes\Modules
 */

namespace BRMedia\Includes\Modules;

class BRMedia_Radio {
    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('brmedia_radio', [$this, 'radio_shortcode']);
    }

    /**
     * Shortcode handler for embedding radio streams
     *
     * @param array $atts Shortcode attributes
     * @return string Rendered HTML
     */
    public function radio_shortcode($atts) {
        $atts = shortcode_atts([
            'url' => '',
            'name' => 'Live Radio',
        ], $atts, 'brmedia_radio');

        if (empty($atts['url'])) {
            return '<p>Radio stream URL required.</p>';
        }

        ob_start();
        ?>
        <div class="brmedia-radio">
            <h3><?php echo esc_html($atts['name']); ?></h3>
            <audio class="plyr" controls>
                <source src="<?php echo esc_url($atts['url']); ?>" type="audio/mpeg">
            </audio>
        </div>
        <?php
        return ob_get_clean();
    }
}