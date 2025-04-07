<?php
/**
 * Video Management Page
 * Advanced interface for managing video uploads, metadata, external syncing, and playlists.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Video management class
class BRMedia_Video_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_video_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_video_assets']);
        add_action('wp_ajax_brmedia_sync_youtube', [$this, 'sync_youtube']);
        add_action('init', [$this, 'register_custom_post_types']);
    }

    // Register custom post types for videos and playlists
    public function register_custom_post_types() {
        register_post_type('brvideo', [
            'labels' => ['name' => 'Videos', 'singular_name' => 'Video'],
            'public' => false,
            'show_ui' => true,
            'supports' => ['title', 'thumbnail'],
            'capability_type' => 'post',
            'menu_icon' => 'dashicons-video-alt3',
        ]);

        register_post_type('brvideoplaylist', [
            'labels' => ['name' => 'Video Playlists', 'singular_name' => 'Playlist'],
            'public' => false,
            'show_ui' => true,
            'supports' => ['title'],
            'capability_type' => 'post',
            'menu_icon' => 'dashicons-playlist-video',
        ]);
    }

    // Add video submenu under BRMedia menu
    public function add_video_menu() {
        add_submenu_page(
            'brmedia',
            'Video Management',
            'Video',
            'manage_options',
            'brmedia-video',
            [$this, 'render_video_page']
        );
    }

    // Enqueue styles and scripts
    public function enqueue_video_assets($hook) {
        if ($hook !== 'brmedia_page_brmedia-video') {
            return;
        }
        wp_enqueue_style('brmedia-video-css', plugins_url('assets/css/video.css', __FILE__), [], '1.2.0');
        wp_enqueue_script('brmedia-video-js', plugins_url('assets/js/video.js', __FILE__), ['jquery'], '1.2.0', true);
        wp_localize_script('brmedia-video-js', 'brmediaVideo', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('brmedia_video_nonce'),
        ]);
    }

    // AJAX handler for YouTube syncing
    public function sync_youtube() {
        check_ajax_referer('brmedia_video_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $youtube_url = esc_url_raw($_POST['youtube_url'] ?? '');
        if (empty($youtube_url) || !preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/', $youtube_url)) {
            wp_send_json_error(['message' => 'Invalid YouTube URL.']);
        }

        // Placeholder for YouTube API integration
        $video_id = wp_insert_post([
            'post_title' => 'Synced Video from YouTube',
            'post_type' => 'brvideo',
            'post_status' => 'publish',
        ]);

        if ($video_id && !is_wp_error($video_id)) {
            update_post_meta($video_id, 'source_url', $youtube_url);
            update_post_meta($video_id, 'duration', '00:00'); // Placeholder
            update_post_meta($video_id, 'views', 0);
            wp_send_json_success(['message' => 'Video synced successfully.', 'id' => $video_id]);
        } else {
            wp_send_json_error(['message' => 'Failed to sync video.']);
        }
    }

    // Render the video management page
    public function render_video_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Handle bulk actions
        $message = '';
        if (isset($_POST['brmedia_video_bulk_action']) && check_admin_referer('brmedia_video_bulk_action')) {
            $action = sanitize_text_field($_POST['brmedia_bulk_action']);
            $video_ids = array_map('intval', $_POST['brmedia_video_ids'] ?? []);
            $playlist_id = intval($_POST['brmedia_playlist_id'] ?? 0);

            if ($action && !empty($video_ids)) {
                switch ($action) {
                    case 'delete':
                        foreach ($video_ids as $id) {
                            wp_delete_post($id, true);
                        }
                        $message = '<div class="notice notice-success is-dismissible"><p>Videos deleted successfully.</p></div>';
                        break;
                    case 'add_to_playlist':
                        if ($playlist_id) {
                            foreach ($video_ids as $id) {
                                $current = get_post_meta($playlist_id, 'video_ids', true) ?: [];
                                if (!in_array($id, $current)) {
                                    $current[] = $id;
                                    update_post_meta($playlist_id, 'video_ids', $current);
                                }
                            }
                            $message = '<div class="notice notice-success is-dismissible"><p>Videos added to playlist.</p></div>';
                        }
                        break;
                }
            }
        }

        // Fetch videos
        $args = [
            'post_type'      => 'brvideo',
            'posts_per_page' => 20,
            'paged'          => max(1, get_query_var('paged') ?: 1),
        ];
        $videos_query = new WP_Query($args);
        $videos = $videos_query->posts;

        // Fetch playlists
        $playlists = get_posts(['post_type' => 'brvideoplaylist', 'posts_per_page' => -1]);

        ?>
        <div class="wrap">
            <h1>Video Management</h1>
            <?php if ($message) echo $message; ?>
            <div class="brmedia-video-management">
                <!-- Quick Actions -->
                <section class="brmedia-quick-actions">
                    <h2>Quick Actions</h2>
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=brvideo')); ?>" class="button button-primary">Upload New Video</a>
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=brvideoplaylist')); ?>" class="button">Create Playlist</a>
                </section>

                <!-- Manage Videos -->
                <section class="brmedia-video-management">
                    <h2>Manage Videos</h2>
                    <form method="post">
                        <?php wp_nonce_field('brmedia_video_bulk_action'); ?>
                        <div class="bulk-actions">
                            <select name="brmedia_bulk_action">
                                <option value="">Bulk Actions</option>
                                <option value="delete">Delete</option>
                                <option value="add_to_playlist">Add to Playlist</option>
                            </select>
                            <select name="brmedia_playlist_id" class="playlist-select" style="display: none;">
                                <option value="">Select Playlist</option>
                                <?php foreach ($playlists as $playlist): ?>
                                    <option value="<?php echo esc_attr($playlist->ID); ?>"><?php echo esc_html($playlist->post_title); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="submit" name="brmedia_video_bulk_action" value="Apply" class="button">
                        </div>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="brmedia-select-all"></th>
                                    <th>Title</th>
                                    <th>Duration</th>
                                    <th>Views</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($videos as $video): ?>
                                    <tr>
                                        <td><input type="checkbox" name="brmedia_video_ids[]" value="<?php echo esc_attr($video->ID); ?>"></td>
                                        <td><?php echo esc_html($video->post_title); ?></td>
                                        <td><?php echo esc_html(get_post_meta($video->ID, 'duration', true) ?: 'N/A'); ?></td>
                                        <td><?php echo esc_html(get_post_meta($video->ID, 'views', true) ?: 0); ?></td>
                                        <td>
                                            <a href="<?php echo esc_url(admin_url('post.php?post=' . $video->ID . '&action=edit')); ?>">Edit</a> |
                                            <a href="<?php echo esc_url(get_delete_post_link($video->ID)); ?>" onclick="return confirm('Are you sure?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($videos)): ?>
                                    <tr><td colspan="5">No videos found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php
                        echo paginate_links([
                            'total'   => $videos_query->max_num_pages,
                            'current' => max(1, get_query_var('paged') ?: 1),
                            'format'  => '?paged=%#%',
                            'base'    => add_query_arg('page', 'brmedia-video', admin_url('admin.php')),
                        ]);
                        ?>
                    </form>
                </section>

                <!-- External Sync -->
                <section class="brmedia-sync">
                    <h2>Sync from External Platforms</h2>
                    <form id="youtube-sync-form">
                        <label>YouTube URL: <input type="url" name="youtube_url" placeholder="https://youtube.com/..." required></label>
                        <button type="submit" class="button button-primary">Sync from YouTube</button>
                    </form>
                    <div id="sync-status"></div>
                </section>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                // Select all checkboxes
                $('#brmedia-select-all').on('change', function() {
                    $('input[name="brmedia_video_ids[]"]').prop('checked', this.checked);
                });

                // Bulk action visibility
                $('select[name="brmedia_bulk_action"]').on('change', function() {
                    $('.playlist-select').toggle(this.value === 'add_to_playlist');
                });

                // YouTube sync
                $('#youtube-sync-form').on('submit', function(e) {
                    e.preventDefault();
                    const url = $('input[name="youtube_url"]').val();
                    $.post(brmediaVideo.ajax_url, {
                        action: 'brmedia_sync_youtube',
                        youtube_url: url,
                        nonce: brmediaVideo.nonce
                    }, function(response) {
                        if (response.success) {
                            $('#sync-status').html('<p class="success">' + response.data.message + '</p>');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            $('#sync-status').html('<p class="error">' + response.data.message + '</p>');
                        }
                    });
                });
            });
        </script>
        <?php
    }
}

new BRMedia_Video_Admin();