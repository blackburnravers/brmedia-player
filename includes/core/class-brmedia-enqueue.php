<?php
/**
 * BRMedia Enqueue Class
 *
 * Manages basic script and style enqueuing.
 *
 * @package BRMedia\Includes\Core
 */

namespace BRMedia\Includes\Core;

class BRMedia_Enqueue {
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
        wp_enqueue_script(
            'plyr',
            'https://cdn.plyr.io/3.6.8/plyr.js',
            [],
            '3.6.8',
            true
        );
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
    }

    /**
     * Enqueues admin scripts and styles
     */
    public function enqueue_admin() {
        wp_enqueue_script(
            'brmedia-admin',
            BRMEDIA_URL . 'assets/js/admin.js',
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
}