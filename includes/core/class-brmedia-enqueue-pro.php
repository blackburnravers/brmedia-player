<?php
/**
 * BRMedia Enqueue Pro Class
 *
 * Handles advanced script and style enqueuing with CDN and lazy loading.
 *
 * @package BRMedia\Includes\Core
 */

namespace BRMedia\Includes\Core;

class BRMedia_Enqueue_Pro {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
    }

    /**
     * Enqueues frontend scripts and styles
     */
    public function enqueue_frontend() {
        // CDN Libraries with SRI
        wp_enqueue_script(
            'plyr',
            'https://cdn.plyr.io/3.6.8/plyr.js',
            [],
            '3.6.8',
            true,
            ['integrity' => 'sha256-...'] // Replace with actual SRI hash
        );
        wp_enqueue_script(
            'wavesurfer',
            'https://unpkg.com/wavesurfer.js',
            [],
            null,
            true
        );

        // Local scripts with lazy loading
        wp_enqueue_script(
            'brmedia-frontend',
            BRMEDIA_URL . 'assets/js/frontend.js',
            ['plyr'],
            BRMEDIA_VERSION,
            true
        );
        wp_enqueue_style(
            'brmedia-frontend',
            BRMEDIA_URL . 'assets/css/frontend.min.css',
            [],
            BRMEDIA_VERSION
        );

        // Lazy load template-specific JS
        global $post;
        if ($post && has_shortcode($post->post_content, 'brmedia_audio')) {
            $this->enqueue_template_scripts('audio-default');
        }
    }

    /**
     * Enqueues admin scripts and styles
     */
    public function enqueue_admin() {
        wp_enqueue_script(
            'brmedia-admin',
            BRMEDIA_URL . 'assets/js/admin.bundle.js',
            [],
            BRMEDIA_VERSION,
            true
        );
        wp_enqueue_style(
            'brmedia-admin',
            BRMEDIA_URL . 'assets/css/admin.min.css',
            [],
            BRMEDIA_VERSION
        );
    }

    /**
     * Enqueues template-specific scripts
     *
     * @param string $template Template name
     */
    private function enqueue_template_scripts($template) {
        $script_map = [
            'audio-default' => 'audio-default.js',
            'video-cinematic' => 'video-cinematic.js',
        ];
        if (isset($script_map[$template])) {
            wp_enqueue_script(
                "brmedia-$template",
                BRMEDIA_URL . "assets/js/templates/{$script_map[$template]}",
                ['plyr'],
                BRMEDIA_VERSION,
                true
            );
        }
    }
}