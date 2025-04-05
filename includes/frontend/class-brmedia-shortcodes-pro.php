<?php
/**
 * BRMedia Shortcodes Pro Class
 *
 * Manages advanced shortcodes with dynamic attributes and live previews.
 *
 * @package BRMedia\Includes\Frontend
 */

namespace BRMedia\Includes\Frontend;

use BRMedia\Includes\Frontend\BRMedia_Player_Pro;

class BRMedia_Shortcodes_Pro {
    /** @var BRMedia_Player_Pro Player instance */
    private $player;

    /**
     * Constructor
     *
     * @param BRMedia_Player_Pro $player Player instance
     */
    public function __construct(BRMedia_Player_Pro $player) {
        $this->player = $player;
        add_shortcode('brmedia_audio_pro', [$this, 'audio_shortcode']);
        add_shortcode('brmedia_video_pro', [$this, 'video_shortcode']);
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
            'autoplay' => false,
        ], $atts, 'brmedia_audio_pro');

        $atts['autoplay'] = filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN);
        $html = $this->player->render_player($atts['id'], $atts['template']);
        if ($atts['autoplay']) {
            $html .= '<script>document.addEventListener("DOMContentLoaded", function() { document.querySelector(".brmedia-player[data-media-id=\'' . esc_js($atts['id']) . '\'] .plyr").play(); });</script>';
        }
        return $html;
    }

    /**
     * Advanced video shortcode handler
     *
     * @param array $atts Shortcode attributes
     * @return string Rendered HTML
     */
    public function video_shortcode($atts) {
        $atts = shortcode_atts([
            'id' => 0,
            'template' => 'video-cinematic',
            'loop' => false,
        ], $atts, 'brmedia_video_pro');

        $atts['loop'] = filter_var($atts['loop'], FILTER_VALIDATE_BOOLEAN);
        $html = $this->player->render_player($atts['id'], $atts['template']);
        if ($atts['loop']) {
            $html .= '<script>document.addEventListener("DOMContentLoaded", function() { document.querySelector(".brmedia-player[data-media-id=\'' . esc_js($atts['id']) . '\'] .plyr").loop = true; });</script>';
        }
        return $html;
    }
}