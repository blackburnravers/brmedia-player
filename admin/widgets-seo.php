<?php
/**
 * Widgets & SEO Settings Page
 * Advanced interface for managing a wide range of widgets and comprehensive SEO settings.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Widgets & SEO class
class BRMedia_Widgets_SEO {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_widgets_seo_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_widgets_seo_assets']);
        add_action('wp_ajax_brmedia_save_widgets_seo', [$this, 'save_widgets_seo_settings']);
        add_action('init', [$this, 'register_widgets']);
    }

    // Register custom widgets
    public function register_widgets() {
        register_widget('BRMedia_Player_Widget');
        register_widget('BRMedia_Tracklist_Widget');
        register_widget('BRMedia_Download_Widget');
        register_widget('BRMedia_Social_Share_Widget');
        register_widget('BRMedia_Live_Stream_Widget');
        register_widget('BRMedia_Podcast_Episode_Widget');
    }

    // Add widgets & SEO submenu under BRMedia menu
    public function add_widgets_seo_menu() {
        add_submenu_page(
            'brmedia',
            'Widgets & SEO Settings',
            'Widgets & SEO',
            'manage_options',
            'brmedia-widgets-seo',
            [$this, 'render_widgets_seo_page']
        );
    }

    // Enqueue styles and scripts
    public function enqueue_widgets_seo_assets($hook) {
        if ($hook !== 'brmedia_page_brmedia-widgets-seo') {
            return;
        }
        wp_enqueue_style('brmedia-widgets-seo-css', plugins_url('assets/css/widgets-seo.css', __FILE__), [], '1.2.0');
        wp_enqueue_script('brmedia-widgets-seo-js', plugins_url('assets/js/widgets-seo.js', __FILE__), ['jquery'], '1.2.0', true);
        wp_localize_script('brmedia-widgets-seo-js', 'brmediaWidgetsSEO', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('brmedia_widgets_seo_nonce'),
        ]);
    }

    // AJAX handler to save settings
    public function save_widgets_seo_settings() {
        check_ajax_referer('brmedia_widgets_seo_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $settings = wp_unslash($_POST['settings'] ?? []);
        $sanitized_settings = $this->sanitize_settings($settings);

        update_option('brmedia_widgets_seo_settings', $sanitized_settings);
        wp_send_json_success(['message' => 'Settings saved successfully.']);
    }

    // Sanitize settings
    private function sanitize_settings($settings) {
        $sanitized = [];
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize_settings($value);
            } else {
                $sanitized[$key] = in_array($key, ['enabled', 'og_enabled', 'tc_enabled', 'schema_enabled']) ? boolval($value) : sanitize_text_field($value);
            }
        }
        return $sanitized;
    }

    // Render the widgets & SEO page
    public function render_widgets_seo_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        $settings = get_option('brmedia_widgets_seo_settings', [
            'widgets' => [
                'player' => ['enabled' => true, 'title' => 'Media Player', 'template' => 'default', 'color' => '#0073aa'],
                'tracklist' => ['enabled' => false, 'title' => 'Tracklist', 'items' => 5, 'show_timestamps' => true],
                'download' => ['enabled' => true, 'title' => 'Download Button', 'template' => 'big', 'button_text' => 'Download'],
                'social_share' => ['enabled' => false, 'title' => 'Social Share', 'platforms' => ['twitter', 'facebook'], 'style' => 'icon-text'],
                'live_stream' => ['enabled' => false, 'title' => 'Live Stream', 'source' => 'radio'],
                'podcast_episode' => ['enabled' => false, 'title' => 'Podcast Episode', 'episode_id' => '', 'layout' => 'compact'],
            ],
            'seo' => [
                'og_enabled' => true,
                'tc_enabled' => true,
                'schema_enabled' => false,
                'default_title' => 'BRMedia Content',
                'default_description' => 'Explore multimedia content with BRMedia.',
                'default_image' => '',
                'integration' => 'none', // none, yoast, rankmath
                'custom_fields' => [],
            ],
        ]);

        ?>
        <div class="wrap">
            <h1>Widgets & SEO Settings</h1>
            <div class="brmedia-widgets-seo">
                <form id="widgets-seo-form">
                    <!-- Widgets Section -->
                    <section class="brmedia-widgets">
                        <h2>Widgets Configuration</h2>
                        <div class="widgets-grid">
                            <!-- Player Widget -->
                            <div class="widget-card">
                                <h3>Player Widget</h3>
                                <label>
                                    <input type="checkbox" name="settings[widgets][player][enabled]" value="1" <?php checked($settings['widgets']['player']['enabled']); ?>>
                                    Enable
                                </label>
                                <label>Title: <input type="text" name="settings[widgets][player][title]" value="<?php echo esc_attr($settings['widgets']['player']['title']); ?>"></label>
                                <label>Template:
                                    <select name="settings[widgets][player][template]">
                                        <option value="default" <?php selected($settings['widgets']['player']['template'], 'default'); ?>>Default</option>
                                        <option value="compact" <?php selected($settings['widgets']['player']['template'], 'compact'); ?>>Compact</option>
                                    </select>
                                </label>
                                <label>Color: <input type="color" name="settings[widgets][player][color]" value="<?php echo esc_attr($settings['widgets']['player']['color']); ?>"></label>
                            </div>

                            <!-- Tracklist Widget -->
                            <div class="widget-card">
                                <h3>Tracklist Widget</h3>
                                <label>
                                    <input type="checkbox" name="settings[widgets][tracklist][enabled]" value="1" <?php checked($settings['widgets']['tracklist']['enabled']); ?>>
                                    Enable
                                </label>
                                <label>Title: <input type="text" name="settings[widgets][tracklist][title]" value="<?php echo esc_attr($settings['widgets']['tracklist']['title']); ?>"></label>
                                <label>Max Items: <input type="number" name="settings[widgets][tracklist][items]" value="<?php echo esc_attr($settings['widgets']['tracklist']['items']); ?>" min="1" max="50"></label>
                                <label>
                                    <input type="checkbox" name="settings[widgets][tracklist][show_timestamps]" value="1" <?php checked($settings['widgets']['tracklist']['show_timestamps']); ?>>
                                    Show Timestamps
                                </label>
                            </div>

                            <!-- Download Widget -->
                            <div class="widget-card">
                                <h3>Download Widget</h3>
                                <label>
                                    <input type="checkbox" name="settings[widgets][download][enabled]" value="1" <?php checked($settings['widgets']['download']['enabled']); ?>>
                                    Enable
                                </label>
                                <label>Title: <input type="text" name="settings[widgets][download][title]" value="<?php echo esc_attr($settings['widgets']['download']['title']); ?>"></label>
                                <label>Template:
                                    <select name="settings[widgets][download][template]">
                                        <option value="big" <?php selected($settings['widgets']['download']['template'], 'big'); ?>>Big</option>
                                        <option value="small" <?php selected($settings['widgets']['download']['template'], 'small'); ?>>Small</option>
                                        <option value="progress-bar" <?php selected($settings['widgets']['download']['template'], 'progress-bar'); ?>>Progress Bar</option>
                                    </select>
                                </label>
                                <label>Button Text: <input type="text" name="settings[widgets][download][button_text]" value="<?php echo esc_attr($settings['widgets']['download']['button_text']); ?>"></label>
                            </div>

                            <!-- Social Share Widget -->
                            <div class="widget-card">
                                <h3>Social Share Widget</h3>
                                <label>
                                    <input type="checkbox" name="settings[widgets][social_share][enabled]" value="1" <?php checked($settings['widgets']['social_share']['enabled']); ?>>
                                    Enable
                                </label>
                                <label>Title: <input type="text" name="settings[widgets][social_share][title]" value="<?php echo esc_attr($settings['widgets']['social_share']['title']); ?>"></label>
                                <label>Platforms:
                                    <select multiple name="settings[widgets][social_share][platforms][]">
                                        <?php
                                        $platforms = ['twitter', 'facebook', 'instagram', 'linkedin', 'pinterest'];
                                        foreach ($platforms as $platform):
                                        ?>
                                            <option value="<?php echo esc_attr($platform); ?>" <?php echo in_array($platform, $settings['widgets']['social_share']['platforms']) ? 'selected' : ''; ?>>
                                                <?php echo esc_html(ucfirst($platform)); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label>Style:
                                    <select name="settings[widgets][social_share][style]">
                                        <option value="icon-text" <?php selected($settings['widgets']['social_share']['style'], 'icon-text'); ?>>Icon + Text</option>
                                        <option value="icon-only" <?php selected($settings['widgets']['social_share']['style'], 'icon-only'); ?>>Icon Only</option>
                                    </select>
                                </label>
                            </div>

                            <!-- Live Stream Widget -->
                            <div class="widget-card">
                                <h3>Live Stream Widget</h3>
                                <label>
                                    <input type="checkbox" name="settings[widgets][live_stream][enabled]" value="1" <?php checked($settings['widgets']['live_stream']['enabled']); ?>>
                                    Enable
                                </label>
                                <label>Title: <input type="text" name="settings[widgets][live_stream][title]" value="<?php echo esc_attr($settings['widgets']['live_stream']['title']); ?>"></label>
                                <label>Source:
                                    <select name="settings[widgets][live_stream][source]">
                                        <option value="radio" <?php selected($settings['widgets']['live_stream']['source'], 'radio'); ?>>Radio</option>
                                        <option value="gaming" <?php selected($settings['widgets']['live_stream']['source'], 'gaming'); ?>>Gaming</option>
                                    </select>
                                </label>
                            </div>

                            <!-- Podcast Episode Widget -->
                            <div class="widget-card">
                                <h3>Podcast Episode Widget</h3>
                                <label>
                                    <input type="checkbox" name="settings[widgets][podcast_episode][enabled]" value="1" <?php checked($settings['widgets']['podcast_episode']['enabled']); ?>>
                                    Enable
                                </label>
                                <label>Title: <input type="text" name="settings[widgets][podcast_episode][title]" value="<?php echo esc_attr($settings['widgets']['podcast_episode']['title']); ?>"></label>
                                <label>Episode ID: <input type="number" name="settings[widgets][podcast_episode][episode_id]" value="<?php echo esc_attr($settings['widgets']['podcast_episode']['episode_id']); ?>" placeholder="Enter post ID"></label>
                                <label>Layout:
                                    <select name="settings[widgets][podcast_episode][layout]">
                                        <option value="compact" <?php selected($settings['widgets']['podcast_episode']['layout'], 'compact'); ?>>Compact</option>
                                        <option value="full" <?php selected($settings['widgets']['podcast_episode']['layout'], 'full'); ?>>Full</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                    </section>

                    <!-- SEO Settings Section -->
                    <section class="brmedia-seo">
                        <h2>SEO Settings</h2>
                        <div class="seo-options">
                            <!-- Open Graph -->
                            <h3>Open Graph</h3>
                            <label>
                                <input type="checkbox" name="settings[seo][og_enabled]" value="1" <?php checked($settings['seo']['og_enabled']); ?>>
                                Enable Open Graph Metadata
                            </label>
                            <label>Default Title: <input type="text" name="settings[seo][default_title]" value="<?php echo esc_attr($settings['seo']['default_title']); ?>"></label>
                            <label>Default Description: <textarea name="settings[seo][default_description]"><?php echo esc_textarea($settings['seo']['default_description']); ?></textarea></label>
                            <label>Default Image URL: <input type="text" name="settings[seo][default_image]" value="<?php echo esc_attr($settings['seo']['default_image']); ?>" placeholder="https://example.com/image.jpg"></label>

                            <!-- Twitter Cards -->
                            <h3>Twitter Cards</h3>
                            <label>
                                <input type="checkbox" name="settings[seo][tc_enabled]" value="1" <?php checked($settings['seo']['tc_enabled']); ?>>
                                Enable Twitter Card Metadata
                            </label>
                            <label>Twitter Site Handle: <input type="text" name="settings[seo][tc_site]" value="<?php echo esc_attr($settings['seo']['tc_site'] ?? '@BRMedia'); ?>" placeholder="@username"></label>

                            <!-- Schema Markup -->
                            <h3>Schema Markup</h3>
                            <label>
                                <input type="checkbox" name="settings[seo][schema_enabled]" value="1" <?php checked($settings['seo']['schema_enabled']); ?>>
                                Enable Schema Markup
                            </label>
                            <label>Content Type:
                                <select name="settings[seo][schema_type]">
                                    <option value="VideoObject" <?php selected($settings['seo']['schema_type'] ?? '', 'VideoObject'); ?>>VideoObject</option>
                                    <option value="AudioObject" <?php selected($settings['seo']['schema_type'] ?? '', 'AudioObject'); ?>>AudioObject</option>
                                    <option value="PodcastEpisode" <?php selected($settings['seo']['schema_type'] ?? '', 'PodcastEpisode'); ?>>PodcastEpisode</option>
                                </select>
                            </label>

                            <!-- SEO Integration -->
                            <h3>SEO Plugin Integration</h3>
                            <label>Integrate with:
                                <select name="settings[seo][integration]">
                                    <option value="none" <?php selected($settings['seo']['integration'], 'none'); ?>>None</option>
                                    <option value="yoast" <?php selected($settings['seo']['integration'], 'yoast'); ?>>Yoast SEO</option>
                                    <option value="rankmath" <?php selected($settings['seo']['integration'], 'rankmath'); ?>>Rank Math</option>
                                </select>
                            </label>
                            <p class="description">Select a plugin to sync metadata with (requires the plugin to be installed).</p>

                            <!-- Custom Fields -->
                            <h3>Custom Metadata Fields</h3>
                            <div id="custom-fields">
                                <?php
                                $custom_fields = $settings['seo']['custom_fields'] ?? [];
                                foreach ($custom_fields as $key => $value):
                                ?>
                                    <div class="custom-field-row">
                                        <input type="text" name="settings[seo][custom_fields][<?php echo esc_attr($key); ?>][key]" value="<?php echo esc_attr($key); ?>" placeholder="Key">
                                        <input type="text" name="settings[seo][custom_fields][<?php echo esc_attr($key); ?>][value]" value="<?php echo esc_attr($value); ?>" placeholder="Value">
                                        <button type="button" class="remove-field button">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="add-custom-field" class="button">Add Custom Field</button>
                        </div>
                    </section>

                    <button type="button" id="save-widgets-seo" class="button button-primary">Save Settings</button>
                </form>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                // Add custom field
                $('#add-custom-field').on('click', function() {
                    const index = $('#custom-fields .custom-field-row').length;
                    $('#custom-fields').append(`
                        <div class="custom-field-row">
                            <input type="text" name="settings[seo][custom_fields][${index}][key]" placeholder="Key">
                            <input type="text" name="settings[seo][custom_fields][${index}][value]" placeholder="Value">
                            <button type="button" class="remove-field button">Remove</button>
                        </div>
                    `);
                });

                // Remove custom field
                $(document).on('click', '.remove-field', function() {
                    $(this).closest('.custom-field-row').remove();
                });

                // Save settings
                $('#save-widgets-seo').on('click', function() {
                    const settings = $('#widgets-seo-form').serializeArray().reduce((acc, item) => {
                        const matches = item.name.match(/settings\[(.+?)\](?:\[(.+?)\])?(?:\[(.+?)\])?(?:\[(.+?)\])?/);
                        if (matches) {
                            let obj = acc;
                            for (let i = 1; i < matches.length && matches[i]; i++) {
                                if (i === matches.length - 1 || !matches[i + 1]) {
                                    obj[matches[i]] = item.value;
                                } else {
                                    obj[matches[i]] = obj[matches[i]] || {};
                                    obj = obj[matches[i]];
                                }
                            }
                        }
                        return acc;
                    }, {});
                    $.post(brmediaWidgetsSEO.ajax_url, {
                        action: 'brmedia_save_widgets_seo',
                        settings: settings,
                        nonce: brmediaWidgetsSEO.nonce
                    }, function(response) {
                        alert(response.success ? response.data.message : response.data.message);
                    });
                });
            });
        </script>
        <?php
    }
}

// Widget classes (simplified examples)
class BRMedia_Player_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('brmedia_player_widget', 'BRMedia Player', ['description' => 'Displays a media player.']);
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo '<h3>' . esc_html($instance['title']) . '</h3>';
        // Placeholder for player output
        echo '<div class="brmedia-player">Player Placeholder</div>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Media Player';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
}

class BRMedia_Tracklist_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('brmedia_tracklist_widget', 'BRMedia Tracklist', ['description' => 'Displays a tracklist.']);
    }

    public function widget($args, $instance) {
        // Placeholder implementation
    }

    public function form($instance) {
        // Placeholder form
    }
}

class BRMedia_Download_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('brmedia_download_widget', 'BRMedia Download', ['description' => 'Displays a download button.']);
    }

    public function widget($args, $instance) {
        // Placeholder implementation
    }

    public function form($instance) {
        // Placeholder form
    }
}

class BRMedia_Social_Share_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('brmedia_social_share_widget', 'BRMedia Social Share', ['description' => 'Displays social share buttons.']);
    }

    public function widget($args, $instance) {
        // Placeholder implementation
    }

    public function form($instance) {
        // Placeholder form
    }
}

class BRMedia_Live_Stream_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('brmedia_live_stream_widget', 'BRMedia Live Stream', ['description' => 'Displays a live stream player.']);
    }

    public function widget($args, $instance) {
        // Placeholder implementation
    }

    public function form($instance) {
        // Placeholder form
    }
}

class BRMedia_Podcast_Episode_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('brmedia_podcast_episode_widget', 'BRMedia Podcast Episode', ['description' => 'Displays a podcast episode.']);
    }

    public function widget($args, $instance) {
        // Placeholder implementation
    }

    public function form($instance) {
        // Placeholder form
    }
}

// Instantiate the widgets & SEO admin
new BRMedia_Widgets_SEO();