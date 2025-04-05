<?php
/**
 * BRMedia Gaming Module Class
 *
 * Integrates Twitch gaming content into the plugin.
 *
 * @package BRMedia\Includes\Modules
 */

namespace BRMedia\Includes\Modules;

use BRMedia\API\Integrations\BRMedia_Twitch;

class BRMedia_Gaming {
    /** @var BRMedia_Twitch Twitch integration instance */
    private $twitch;

    /**
     * Constructor
     *
     * @param BRMedia_Twitch $twitch Twitch integration instance
     */
    public function __construct(BRMedia_Twitch $twitch) {
        $this->twitch = $twitch;
        add_shortcode('brmedia_twitch_stream', [$this, 'twitch_stream_shortcode']);
    }

    /**
     * Shortcode handler for embedding Twitch streams
     *
     * @param array $atts Shortcode attributes
     * @return string Rendered HTML
     */
    public function twitch_stream_shortcode($atts) {
        $atts = shortcode_atts([
            'channel' => '',
            'width' => '100%',
            'height' => '400',
        ], $atts, 'brmedia_twitch_stream');

        if (empty($atts['channel'])) {
            return '<p>Twitch channel name required.</p>';
        }

        $embed_code = $this->twitch->get_embed_code($atts['channel'], 'live');
        return sprintf(
            '<div class="brmedia-twitch-stream" style="width:%s;height:%s;">%s</div>',
            esc_attr($atts['width']),
            esc_attr($atts['height']),
            $embed_code
        );
    }
}