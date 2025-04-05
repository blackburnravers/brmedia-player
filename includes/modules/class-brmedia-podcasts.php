<?php
/**
 * BRMedia Podcasts Module Class
 *
 * Manages podcast syndication and embedding.
 *
 * @package BRMedia\Includes\Modules
 */

namespace BRMedia\Includes\Modules;

class BRMedia_Podcasts {
    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('brmedia_podcast', [$this, 'podcast_shortcode']);
    }

    /**
     * Shortcode handler for embedding podcast episodes
     *
     * @param array $atts Shortcode attributes
     * @return string Rendered HTML
     */
    public function podcast_shortcode($atts) {
        $atts = shortcode_atts([
            'url' => '',
            'title' => 'Podcast Episode',
        ], $atts, 'brmedia_podcast');

        if (empty($atts['url'])) {
            return '<p>Podcast episode URL required.</p>';
        }

        ob_start();
        ?>
        <div class="brmedia-podcast">
            <h3><?php echo esc_html($atts['title']); ?></h3>
            <audio class="plyr" controls>
                <source src="<?php echo esc_url($atts['url']); ?>" type="audio/mpeg">
            </audio>
        </div>
        <?php
        return ob_get_clean();
    }
}