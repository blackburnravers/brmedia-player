<?php
/**
 * Plugin Name: BRMedia Player
 * Plugin URI: https://www.blackburnravers.co.uk/brmedia-player
 * Description: An advanced, modular WordPress plugin for managing and showcasing music and video content with AI, streaming, and API integrations.
 * Version: 1.0.0
 * Author: Rhys Cole
 * Author URI: https://www.blackburnravers.co.uk
 * License: GPL-2.0+
 * Text Domain: brmedia-player
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('BRMEDIA_VERSION', '1.0.0');
define('BRMEDIA_DIR', plugin_dir_path(__FILE__));
define('BRMEDIA_URL', plugin_dir_url(__FILE__));
define('BRMEDIA_BASENAME', plugin_basename(__FILE__));
define('BRMEDIA_SLUG', 'brmedia-player');

// Include Composer autoloader (for external dependencies)
if (file_exists(BRMEDIA_DIR . 'vendor/autoload.php')) {
    require_once BRMEDIA_DIR . 'vendor/autoload.php';
}

// Namespace imports
use BRMedia\Core\DIContainer;
use BRMedia\Core\Hooks;
use BRMedia\Core\EnqueuePro;
use BRMedia\Core\CPTAdvanced;
use BRMedia\Admin\AdminCore;
use BRMedia\Frontend\PlayerPro;

/**
 * Main BRMedia Player class
 */
class BRMediaPlayer {
    /**
     * Dependency Injection Container
     * @var DIContainer
     */
    private $di;

    /**
     * Singleton instance
     * @var BRMediaPlayer
     */
    private static $instance;

    /**
     * Constructor
     */
    private function __construct() {
        $this->di = new DIContainer();
        $this->register_services();
        $this->init();
    }

    /**
     * Get singleton instance
     * @return BRMediaPlayer
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register services with the DI container
     */
    private function register_services() {
        // Core services
        $this->di->register('enqueue', EnqueuePro::class);
        $this->di->register('cpt', CPTAdvanced::class);

        // Admin services
        $this->di->register('admin', AdminCore::class);

        // Frontend services
        $this->di->register('player', PlayerPro::class);

        // Allow extensions to register additional services
        do_action('brmedia_register_services', $this->di);
    }

    /**
     * Initialize the plugin
     */
    private function init() {
        // Load text domain for translations
        load_plugin_textdomain('brmedia-player', false, BRMEDIA_DIR . 'languages/');

        // Initialize hooks
        $hooks = new Hooks($this->di);
        $hooks->load();

        // Activation and deactivation hooks
        register_activation_hook(BRMEDIA_BASENAME, [$this, 'activate']);
        register_deactivation_hook(BRMEDIA_BASENAME, [$this, 'deactivate']);
    }

    /**
     * Activation hook
     */
    public function activate() {
        // Flush rewrite rules for custom post types
        $cpt = $this->di->get('cpt');
        $cpt->register_post_types();
        flush_rewrite_rules();

        // Set default options
        if (!get_option('brmedia_settings')) {
            update_option('brmedia_settings', [
                'default_audio_template' => 'audio-reactive',
                'default_video_template' => 'video-cinematic',
                'enable_analytics' => true,
            ]);
        }
    }

    /**
     * Deactivation hook
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Get the DI container
     * @return DIContainer
     */
    public function get_di() {
        return $this->di;
    }
}

/**
 * Bootstrap the plugin
 */
function brmedia_player() {
    return BRMediaPlayer::get_instance();
}

// Kick off the plugin
brmedia_player();

// Cleanup on uninstall (handled in uninstall.php)
register_uninstall_hook(BRMEDIA_BASENAME, ['BRMediaPlayer', 'uninstall']);

/**
 * Uninstall callback (static method)
 */
function uninstall() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    // Delete options and custom tables if multisite-aware
    delete_option('brmedia_settings');
    if (is_multisite()) {
        global $wpdb;
        $blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}");
        foreach ($blogs as $blog) {
            switch_to_blog($blog->blog_id);
            delete_option('brmedia_settings');
            restore_current_blog();
        }
    }
}