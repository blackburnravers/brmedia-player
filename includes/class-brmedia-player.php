<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BRMedia_Player {

    public function __construct() {
        // Load plugin text domain
        add_action( 'init', array( $this, 'load_textdomain' ) );

        // Register custom post types
        add_action( 'init', array( $this, 'register_post_types' ) );

        // Enqueue admin styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

        // Enqueue frontend styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

        // Include admin classes
        $this->include_admin_classes();

        // Include frontend classes
        $this->include_frontend_classes();

        // Initialize modules
        $this->initialize_modules();
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'brmedia-player', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    public function register_post_types() {
        // Register Music post type
        $labels = array(
            'name' => __( 'Music', 'brmedia-player' ),
            'singular_name' => __( 'Music', 'brmedia-player' ),
            'add_new' => __( 'Add New', 'brmedia-player' ),
            'add_new_item' => __( 'Add New Music', 'brmedia-player' ),
            'edit_item' => __( 'Edit Music', 'brmedia-player' ),
            'new_item' => __( 'New Music', 'brmedia-player' ),
            'all_items' => __( 'All Music', 'brmedia-player' ),
            'view_item' => __( 'View Music', 'brmedia-player' ),
            'search_items' => __( 'Search Music', 'brmedia-player' ),
            'not_found' => __( 'No music found', 'brmedia-player' ),
            'not_found_in_trash' => __( 'No music found in Trash', 'brmedia-player' ),
            'menu_name' => __( 'Music', 'brmedia-player' ),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_position' => 5,
            'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
            'rewrite' => array( 'slug' => 'music' ),
        );

        register_post_type( 'brmusic', $args );

        // Register other post types similarly...
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style( 'brmedia-admin', BRMEDIA_PLAYER_PLUGIN_URL . 'admin/admin.css', array(), BRMEDIA_PLAYER_VERSION );
        wp_enqueue_script( 'brmedia-admin', BRMEDIA_PLAYER_PLUGIN_URL . 'admin/admin.js', array('jquery'), BRMEDIA_PLAYER_VERSION, true );
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style( 'brmedia-frontend', BRMEDIA_PLAYER_PLUGIN_URL . 'assets/css/frontend.css', array(), BRMEDIA_PLAYER_VERSION );
        wp_enqueue_script( 'brmedia-frontend', BRMEDIA_PLAYER_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), BRMEDIA_PLAYER_VERSION, true );
    }

    private function include_admin_classes() {
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin-dashboard.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin-music.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin-video.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin-radio.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin-templates.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin-shortcodes.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin-statistics.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin-widgets.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/admin/class-brmedia-admin-social.php';
    }

    private function include_frontend_classes() {
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/frontend/class-brmedia-frontend.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/frontend/class-brmedia-frontend-player.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/frontend/class-brmedia-frontend-shortcodes.php';
    }

    private function initialize_modules() {
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/modules/class-brmedia-music.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/modules/class-brmedia-video.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/modules/class-brmedia-radio.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/modules/class-brmedia-gaming.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/modules/class-brmedia-podcasts.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/modules/class-brmedia-chat.php';
        require_once BRMEDIA_PLAYER_PLUGIN_DIR . 'includes/modules/class-brmedia-downloads.php';
    }
}