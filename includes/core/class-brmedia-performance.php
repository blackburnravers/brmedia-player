<?php
/**
 * BRMedia Performance Class
 *
 * Provides performance optimization utilities.
 *
 * @package BRMedia\Includes\Core
 */

namespace BRMedia\Includes\Core;

class BRMedia_Performance {
    /**
     * Constructor
     */
    public function __construct() {
        add_filter('script_loader_tag', [$this, 'add_lazy_load'], 10, 3);
        add_action('wp_loaded', [$this, 'init_cache']);
    }

    /**
     * Adds lazy loading to scripts
     *
     * @param string $tag Script tag
     * @param string $handle Script handle
     * @param string $src Script source
     * @return string Modified script tag
     */
    public function add_lazy_load($tag, $handle, $src) {
        if (strpos($handle, 'brmedia') === 0) {
            $tag = str_replace('src=', 'data-src=', $tag);
            $tag .= '<script>document.addEventListener("DOMContentLoaded", function(){var s = document.createElement("script");s.src="' . $src . '";document.body.appendChild(s);});</script>';
        }
        return $tag;
    }

    /**
     * Initializes caching (e.g., transients)
     */
    public function init_cache() {
        if (!get_transient('brmedia_cache_players')) {
            $players = $this->generate_player_cache();
            set_transient('brmedia_cache_players', $players, DAY_IN_SECONDS);
        }
    }

    /**
     * Generates player cache data (placeholder)
     *
     * @return array Cached player data
     */
    private function generate_player_cache() {
        // Placeholder: Fetch and cache player data
        return ['example' => 'data'];
    }
}