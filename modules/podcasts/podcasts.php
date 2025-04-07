<?php
/**
 * Podcasts Module Logic
 * Advanced management of podcast episodes with RSS feeds, waveforms, and analytics.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'templates.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

class BRMedia_Podcasts {
    public function __construct() {
        add_action('init', [$this, 'register_podcast_post_type']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'add_podcasts_menu']);
        add_action('init', [$this, 'register_podcasts_db_table']);
        add_action('wp_ajax_brmedia_podcast_action', [$this, 'handle_podcast_action']);
        add_action('wp_ajax_nopriv_brmedia_podcast_action', [$this, 'handle_podcast_action']);
        add_action('wp_head', [$this, 'add_rss_feed_link']);
        add_filter('query_vars', [$this, 'add_rss_query_var']);
        add_action('template_redirect', [$this, 'generate_rss_feed']);
    }

    // Register custom post type for podcast episodes
    public function register_podcast_post_type() {
        register_post_type('brmedia_podcast', [
            'labels' => [
                'name' => 'Podcasts',
                'singular_name' => 'Podcast Episode',
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'taxonomies' => ['category', 'post_tag'],
        ]);

        register_taxonomy('brmedia_podcast_series', 'brmedia_podcast', [
            'labels' => [
                'name' => 'Series',
                'singular_name' => 'Series',
            ],
            'public' => true,
            'hierarchical' => true,
        ]);
    }

    // Register database table for podcast analytics
    public function register_podcasts_db_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'brmedia_podcast_stats';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            episode_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL,
            timestamp DATETIME NOT NULL,
            ip_address VARCHAR(100) NOT NULL,
            PRIMARY KEY (id),
            INDEX episode_id (episode_id),
            INDEX user_id (user_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    // Enqueue shared assets (no new files added)
    public function enqueue_assets() {
        wp_enqueue_script('wavesurfer', 'https://cdn.jsdelivr.net/npm/wavesurfer.js@6.0.0/dist/wavesurfer.min.js', [], '6.0.0', true);
    }

    // Add podcasts submenu to ACP
    public function add_podcasts_menu() {
        add_submenu_page(
            'brmedia',
            'Podcasts Settings',
            'Podcasts',
            'manage_options',
            'brmedia-podcasts',
            [$this, 'podcasts_settings_page']
        );
        add_action('admin_init', [$this, 'register_settings']);
    }

    // Register settings for ACP
    public function register_settings() {
        register_setting('brmedia_podcasts_settings', 'brmedia_podcast_feed_title', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('brmedia_podcasts_settings', 'brmedia_podcast_feed_description', ['sanitize_callback' => 'sanitize_text_field']);

        add_settings_section('brmedia_podcast_section', 'Podcast Feed Settings', null, 'brmedia-podcasts');
        add_settings_field('feed_title', 'Feed Title', [$this, 'feed_title_field'], 'brmedia-podcasts', 'brmedia_podcast_section');
        add_settings_field('feed_description', 'Feed Description', [$this, 'feed_description_field'], 'brmedia-podcasts', 'brmedia_podcast_section');
    }

    public function feed_title_field() {
        $value = get_option('brmedia_podcast_feed_title', 'BRMedia Podcasts');
        echo '<input type="text" name="brmedia_podcast_feed_title" value="' . esc_attr($value) . '" />';
    }

    public function feed_description_field() {
        $value = get_option('brmedia_podcast_feed_description', 'A collection of podcasts from BRMedia.');
        echo '<textarea name="brmedia_podcast_feed_description">' . esc_textarea($value) . '</textarea>';
    }

    // Render podcasts settings page
    public function podcasts_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        ?>
        <div class="wrap">
            <h1>Podcasts Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('brmedia_podcasts_settings');
                do_settings_sections('brmedia-podcasts');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Handle podcast actions (e.g., play, pause)
    public function handle_podcast_action() {
        check_ajax_referer('brmedia_podcasts_nonce', 'nonce');
        $episode_id = intval($_POST['episode_id'] ?? 0);
        $action = sanitize_text_field($_POST['action_type'] ?? 'play');
        $user_id = get_current_user_id();

        if (!$episode_id || get_post_type($episode_id) !== 'brmedia_podcast') {
            wp_send_json_error(['message' => 'Invalid episode ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'brmedia_podcast_stats';
        $wpdb->insert($table, [
            'episode_id' => $episode_id,
            'user_id' => $user_id,
            'action' => $action,
            'timestamp' => current_time('mysql'),
            'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'Unknown'),
        ], ['%d', '%d', '%s', '%s', '%s']);

        wp_send_json_success(['message' => 'Action recorded']);
    }

    // Add RSS feed link to site header
    public function add_rss_feed_link() {
        echo '<link rel="alternate" type="application/rss+xml" title="' . esc_attr(get_option('brmedia_podcast_feed_title', 'BRMedia Podcasts')) . '" href="' . esc_url(site_url('/?brmedia_podcast_feed=1')) . '" />';
    }

    // Add custom query var for RSS feed
    public function add_rss_query_var($vars) {
        $vars[] = 'brmedia_podcast_feed';
        return $vars;
    }

    // Generate RSS feed
    public function generate_rss_feed() {
        if (get_query_var('brmedia_podcast_feed')) {
            header('Content-Type: application/rss+xml; charset=' . get_option('blog_charset'), true);
            echo '<?xml version="1.0" encoding="' . esc_attr(get_option('blog_charset')) . '"?>';
            ?>
            <rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
                <channel>
                    <title><?php echo esc_html(get_option('brmedia_podcast_feed_title', 'BRMedia Podcasts')); ?></title>
                    <description><?php echo esc_html(get_option('brmedia_podcast_feed_description', 'A collection of podcasts from BRMedia.')); ?></description>
                    <link><?php echo esc_url(home_url('/')); ?></link>
                    <?php
                    $episodes = new WP_Query(['post_type' => 'brmedia_podcast', 'posts_per_page' => 100]);
                    while ($episodes->have_posts()) {
                        $episodes->the_post();
                        $audio_url = get_post_meta(get_the_ID(), 'brmedia_podcast_audio', true);
                        if ($audio_url) {
                            $file_size = @filesize(get_attached_file(get_post_meta(get_the_ID(), '_wp_attached_file', true))) ?: 0;
                            ?>
                            <item>
                                <title><?php the_title_rss(); ?></title>
                                <description><?php the_excerpt_rss(); ?></description>
                                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                                <enclosure url="<?php echo esc_url($audio_url); ?>" length="<?php echo esc_attr($file_size); ?>" type="audio/mpeg" />
                                <guid><?php the_guid(); ?></guid>
                                <itunes:duration><?php echo esc_attr(get_post_meta(get_the_ID(), 'brmedia_podcast_duration', true)); ?></itunes:duration>
                            </item>
                            <?php
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </channel>
            </rss>
            <?php
            exit;
        }
    }
}

new BRMedia_Podcasts();