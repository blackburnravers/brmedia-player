<?php
/**
 * Templates Panel Page
 * Advanced customization of all player and button templates with sub-options for all modules.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Templates panel class
class BRMedia_Templates_Panel {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_templates_panel_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_templates_assets']);
    }

    // Add templates panel submenu under BRMedia menu
    public function add_templates_panel_menu() {
        add_submenu_page(
            'brmedia',             // Parent slug
            'Templates Panel',     // Page title
            'Templates',           // Menu title
            'manage_options',      // Capability
            'brmedia-templates',   // Menu slug
            [$this, 'render_templates_panel'] // Callback
        );
    }

    // Enqueue styles and scripts
    public function enqueue_templates_assets($hook) {
        if ($hook !== 'brmedia_page_brmedia-templates') {
            return;
        }
        wp_enqueue_style('brmedia-templates-css', plugins_url('assets/css/templates.css', __FILE__), [], '1.1.0');
        wp_enqueue_script('brmedia-templates-js', plugins_url('assets/js/templates.js', __FILE__), ['jquery'], '1.1.0', true);
        wp_localize_script('brmedia-templates-js', 'brmediaTemplates', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('brmedia_templates_nonce'),
        ]);
    }

    // Render the templates panel page
    public function render_templates_panel() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Define template categories and options
        $template_categories = [
            'audio' => [
                'label' => 'Audio Player Templates',
                'templates' => ['default', 'compact', 'playlist', 'popup', 'fullscreen', 'minimal'],
                'settings' => [
                    'primary_color' => 'Primary Color',
                    'button_size' => 'Button Size (px)',
                    'waveform_enabled' => 'Enable Waveform',
                ],
            ],
            'video' => [
                'label' => 'Video Player Templates',
                'templates' => ['default', 'popup', 'embedded', 'cinematic'],
                'settings' => [
                    'primary_color' => 'Primary Color',
                    'player_width' => 'Player Width (px)',
                    'player_height' => 'Player Height (px)',
                ],
            ],
            'radio' => [
                'label' => 'Radio Player Templates',
                'templates' => ['default', 'minimal'],
                'settings' => [
                    'stream_url' => 'Default Stream URL',
                    'auto_play' => 'Auto Play',
                ],
            ],
            'gaming' => [
                'label' => 'Gaming Stream Templates',
                'templates' => ['twitch', 'youtube'],
                'settings' => [
                    'embed_width' => 'Embed Width (px)',
                    'embed_height' => 'Embed Height (px)',
                ],
            ],
            'downloads' => [
                'label' => 'Download Button Templates',
                'templates' => ['big', 'small', 'massive', 'icon-only', 'progress-bar'],
                'settings' => [
                    'button_color' => 'Button Color',
                    'button_text' => 'Button Text',
                    'gate_type' => 'Gate Type (e.g., email, none)',
                ],
            ],
            'podcasts' => [
                'label' => 'Podcast Player Templates',
                'templates' => ['default', 'episodic', 'compact'],
                'settings' => [
                    'primary_color' => 'Primary Color',
                    'episode_layout' => 'Episode Layout (list/grid)',
                ],
            ],
            'chat' => [
                'label' => 'Chat Interface Templates',
                'templates' => ['inline', 'popup'],
                'settings' => [
                    'chat_color' => 'Chat Background Color',
                    'message_style' => 'Message Style (modern/classic)',
                ],
            ],
        ];

        // Get current template settings
        $current_settings = get_option('brmedia_template_settings', []);

        // Handle form submission
        if (isset($_POST['brmedia_save_template_settings']) && check_admin_referer('brmedia_save_template_settings')) {
            $new_settings = $_POST['brmedia_template_settings'] ?? [];
            update_option('brmedia_template_settings', $new_settings);
            $message = '<div class="notice notice-success is-dismissible"><p>Template settings saved successfully.</p></div>';
        }

        ?>
        <div class="wrap">
            <h1>Templates Panel</h1>
            <?php if (isset($message)) echo $message; ?>
            <form method="post">
                <?php wp_nonce_field('brmedia_save_template_settings'); ?>
                <div class="brmedia-templates-panel">
                    <?php foreach ($template_categories as $category => $data): ?>
                        <section class="template-category">
                            <h2><?php echo esc_html($data['label']); ?></h2>
                            <div class="template-options">
                                <label>Default Template:
                                    <select name="brmedia_template_settings[<?php echo esc_attr($category); ?>][default_template]">
                                        <?php foreach ($data['templates'] as $template): ?>
                                            <option value="<?php echo esc_attr($template); ?>" 
                                                    <?php selected($current_settings[$category]['default_template'] ?? '', $template); ?>>
                                                <?php echo esc_html(ucfirst($template)); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <?php foreach ($data['settings'] as $setting_key => $setting_label): ?>
                                    <label>
                                        <?php echo esc_html($setting_label); ?>:
                                        <?php if (strpos($setting_key, 'color') !== false): ?>
                                            <input type="color" 
                                                   name="brmedia_template_settings[<?php echo esc_attr($category); ?>][<?php echo esc_attr($setting_key); ?>]" 
                                                   value="<?php echo esc_attr($current_settings[$category][$setting_key] ?? '#0073aa'); ?>">
                                        <?php elseif (strpos($setting_key, 'enabled') !== false || strpos($setting_key, 'auto_play') !== false): ?>
                                            <input type="checkbox" 
                                                   name="brmedia_template_settings[<?php echo esc_attr($category); ?>][<?php echo esc_attr($setting_key); ?>]" 
                                                   value="1" 
                                                   <?php checked($current_settings[$category][$setting_key] ?? false); ?>>
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="brmedia_template_settings[<?php echo esc_attr($category); ?>][<?php echo esc_attr($setting_key); ?>]" 
                                                   value="<?php echo esc_attr($current_settings[$category][$setting_key] ?? ''); ?>">
                                        <?php endif; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                    <?php submit_button('Save Template Settings', 'primary', 'brmedia_save_template_settings'); ?>
                </div>
            </form>
        </div>
        <?php
    }
}

// Instantiate the templates panel
new BRMedia_Templates_Panel();