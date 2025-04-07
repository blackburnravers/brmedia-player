<?php
/**
 * Music Management Page
 * Comprehensive interface for managing music with bulk actions, metadata, syncing, and playlists.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Music management class
class BRMedia_Music_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_music_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_music_assets']);
        add_action('wp_ajax_brmedia_sync_soundcloud', [$this, 'sync_soundcloud']);
    }

    // Add music submenu under BRMedia menu
    public function add_music_menu() {
        add_submenu_page(
            'brmedia',          // Parent slug
            'Music Management', // Page title
            'Music',            // Menu title
            'manage_options',   // Capability
            'brmedia-music',    // Menu slug
            [$this, 'render_music_page'] // Callback
        );
    }

    // Enqueue styles and scripts
    public function enqueue_music_assets($hook) {
        if ($hook !== 'brmedia_page_brmedia-music') {
            return;
        }
        wp_enqueue_style('brmedia-music-css', plugins_url('assets/css/music.css', __FILE__), [], '1.1.0');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        wp_enqueue_script('brmedia-music-js', plugins_url('assets/js/music.js', __FILE__), ['jquery'], '1.1.0', true);
        wp_localize_script('brmedia-music-js', 'brmediaMusicAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('brmedia_music_nonce'),
        ]);
    }

    // Handle SoundCloud sync via AJAX
    public function sync_soundcloud() {
        check_ajax_referer('brmedia_music_nonce', 'nonce');
        // Placeholder for SoundCloud API integration
        wp_send_json_success(['message' => 'SoundCloud sync completed.']);
    }

    // Render the music management page
    public function render_music_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Handle bulk actions
        if (isset($_POST['brmedia_music_bulk_action']) && check_admin_referer('brmedia_music_bulk_action')) {
            $action = sanitize_text_field($_POST['brmedia_bulk_action']);
            $track_ids = array_map('intval', $_POST['brmedia_track_ids'] ?? []);
            if ($action && !empty($track_ids)) {
                switch ($action) {
                    case 'delete':
                        foreach ($track_ids as $track_id) {
                            wp_delete_post($track_id, true);
                        }
                        $message = '<div class="notice notice-success is-dismissible"><p>Tracks deleted successfully.</p></div>';
                        break;
                    case 'sync':
                        $message = '<div class="notice notice-success is-dismissible"><p>Metadata sync initiated.</p></div>';
                        break;
                    case 'add_to_playlist':
                        $playlist_id = intval($_POST['brmedia_playlist_id']);
                        foreach ($track_ids as $track_id) {
                            // Placeholder for playlist addition logic
                        }
                        $message = '<div class="notice notice-success is-dismissible"><p>Tracks added to playlist.</p></div>';
                        break;
                }
            }
        }

        // Fetch music tracks with filters
        $args = [
            'post_type'      => 'brmusic',
            'posts_per_page' => 20,
            'paged'          => max(1, get_query_var('paged') ?: 1),
            'meta_query'     => [],
        ];
        if (isset($_GET['artist_filter'])) {
            $args['meta_query'][] = [
                'key'     => 'artist',
                'value'   => sanitize_text_field($_GET['artist_filter']),
                'compare' => 'LIKE',
            ];
        }
        $tracks_query = new WP_Query($args);
        $tracks = $tracks_query->posts;

        // Fetch playlists
        $playlists = get_posts(['post_type' => 'brplaylist', 'posts_per_page' => -1]);

        ?>
        <div class="wrap">
            <h1>Music Management</h1>
            <?php if (isset($message)) echo $message; ?>
            <div class="brmedia-music-management">
                <!-- Quick Actions -->
                <section class="brmedia-quick-actions">
                    <h2>Quick Actions</h2>
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=brmusic')); ?>" class="button button-primary">Add New Track</a>
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=brplaylist')); ?>" class="button">Create Playlist</a>
                </section>

                <!-- Filters -->
                <section class="brmedia-filters">
                    <h2>Filter Tracks</h2>
                    <form method="get">
                        <input type="hidden" name="page" value="brmedia-music">
                        <label>Artist: <input type="text" name="artist_filter" value="<?php echo esc_attr($_GET['artist_filter'] ?? ''); ?>"></label>
                        <input type="submit" value="Filter" class="button">
                    </form>
                </section>

                <!-- Manage Tracks -->
                <section class="brmedia-track-management">
                    <h2>Manage Tracks</h2>
                    <form method="post">
                        <?php wp_nonce_field('brmedia_music_bulk_action'); ?>
                        <div class="bulk-actions">
                            <select name="brmedia_bulk_action">
                                <option value="">Bulk Actions</option>
                                <option value="delete">Delete</option>
                                <option value="sync">Sync Metadata</option>
                                <option value="add_to_playlist">Add to Playlist</option>
                            </select>
                            <select name="brmedia_playlist_id" class="playlist-select" style="display: none;">
                                <?php foreach ($playlists as $playlist): ?>
                                    <option value="<?php echo esc_attr($playlist->ID); ?>"><?php echo esc_html($playlist->post_title); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="submit" name="brmedia_music_bulk_action" value="Apply" class="button">
                        </div>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="brmedia-select-all"></th>
                                    <th>Title</th>
                                    <th>Artist</th>
                                    <th>Album</th>
                                    <th>Genre</th>
                                    <th>Duration</th>
                                    <th>Plays</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tracks as $track): ?>
                                    <tr>
                                        <td><input type="checkbox" name="brmedia_track_ids[]" value="<?php echo esc_attr($track->ID); ?>"></td>
                                        <td><?php echo esc_html($track->post_title); ?></td>
                                        <td><?php echo esc_html(get_post_meta($track->ID, 'artist', true)); ?></td>
                                        <td><?php echo esc_html(get_post_meta($track->ID, 'album', true)); ?></td>
                                        <td><?php echo esc_html(get_post_meta($track->ID, 'genre', true)); ?></td>
                                        <td><?php echo esc_html(get_post_meta($track->ID, 'duration', true)); ?></td>
                                        <td><?php echo esc_html(get_post_meta($track->ID, 'plays', true) ?: 0); ?></td>
                                        <td>
                                            <a href="<?php echo esc_url(admin_url('post.php?post=' . $track->ID . '&action=edit')); ?>">Edit</a> |
                                            <a href="<?php echo esc_url(get_delete_post_link($track->ID)); ?>" onclick="return confirm('Are you sure?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php
                        echo paginate_links([
                            'total'   => $tracks_query->max_num_pages,
                            'current' => max(1, get_query_var('paged') ?: 1),
                            'format'  => '?paged=%#%',
                            'base'    => add_query_arg('page', 'brmedia-music', admin_url('admin.php')),
                        ]);
                        ?>
                    </form>
                </section>

                <!-- External Sync -->
                <section class="brmedia-sync">
                    <h2>Sync from External Platforms</h2>
                    <form id="soundcloud-sync-form" method="post">
                        <?php wp_nonce_field('brmedia_sync_soundcloud', 'sync_nonce'); ?>
                        <label>SoundCloud URL: <input type="url" name="soundcloud_url" placeholder="https://soundcloud.com/..."></label>
                        <input type="submit" value="Sync from SoundCloud" class="button button-primary">
                    </form>
                    <div id="sync-status"></div>
                </section>
            </div>
        </div>
        <?php
    }
}

// Instantiate the music admin
new BRMedia_Music_Admin();