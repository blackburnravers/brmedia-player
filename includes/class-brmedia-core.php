<?php
/**
 * BRMedia Player Core Class
 *
 * This class initializes the plugin, loads modules, and sets up global hooks.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BRMedia_Core {
    /**
     * Instance of the class (Singleton pattern)
     *
     * @var BRMedia_Core
     */
    private static $instance = null;

    /**
     * Get the singleton instance of the class
     *
     * @return BRMedia_Core
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Load includes
        $this->load_includes();

        // Load modules
        $this->load_modules();

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Initialize admin
        add_action('admin_menu', array($this, 'initialize_admin'));
    }

    /**
     * Load other include files
     */
    private function load_includes() {
        require_once plugin_dir_path(__FILE__) . 'class-brmedia-helpers.php';
        require_once plugin_dir_path(__FILE__) . 'class-brmedia-analytics.php';
        require_once plugin_dir_path(__FILE__) . 'class-brmedia-comments.php';
        require_once plugin_dir_path(__FILE__) . 'class-brmedia-downloads.php';
        require_once plugin_dir_path(__FILE__) . 'taxonomies.php';
    }

    /**
     * Load plugin modules
     */
    private function load_modules() {
        $modules_dir = plugin_dir_path(__DIR__) . 'modules/';
        $modules = array(
            'music' => 'music.php',
            'video' => 'video.php',
            'radio' => 'radio.php',
            'gaming' => 'gaming.php',
            'podcasts' => 'podcasts.php',
            'chat' => 'chat.php',
            'downloads' => 'downloads.php',
        );

        foreach ($modules as $module => $file) {
            $module_enabled = get_option("brmedia_{$module}_enabled", true);
            if ($module === 'music' || $module_enabled) {
                require_once $modules_dir . $module . '/' . $file;
            }
        }
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue WaveSurfer.js for audio
        wp_enqueue_script('wavesurfer-js', 'https://cdn.jsdelivr.net/npm/wavesurfer.js', array(), '2.0.0', true);

        // Enqueue Video.js for video
        wp_enqueue_script('video-js', 'https://vjs.zencdn.net/8.0.0/video.min.js', array(), '8.0.0', true);
        wp_enqueue_style('video-js-css', 'https://vjs.zencdn.net/8.0.0/video-js.css');

        // Enqueue Font Awesome for icons
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css', array(), '6.0.0');

        // Enqueue Chart.js for analytics
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.7.1', true);

        // Enqueue custom frontend scripts and styles
        wp_enqueue_style('brmedia-frontend', plugin_dir_url(__DIR__) . 'assets/css/frontend.css', array(), '1.0.0');
        wp_enqueue_script('brmedia-frontend', plugin_dir_url(__DIR__) . 'assets/js/frontend.js', array('jquery', 'wavesurfer-js', 'video-js'), '1.0.0', true);

        // Localize script for AJAX and other data
        wp_localize_script('brmedia-frontend', 'brmedia_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('brmedia_nonce'),
        ));
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style('brmedia-admin', plugin_dir_url(__DIR__) . 'assets/css/admin.css', array(), '1.0.0');
        wp_enqueue_script('brmedia-admin', plugin_dir_url(__DIR__) . 'assets/js/admin.js', array('jquery'), '1.0.0', true);
    }

    /**
     * Initialize admin menu
     */
    public function initialize_admin() {
        require_once plugin_dir_path(__DIR__) . 'admin/admin.php';
    }
}

// Initialize the plugin
BRMedia_Core::get_instance();