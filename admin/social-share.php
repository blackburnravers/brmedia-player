<?php
/**
 * Social Share Settings Page
 * Advanced management of social sharing platforms, button styles, and behavior.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Social share class
class BRMedia_Social_Share {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_social_share_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_social_share_assets']);
    }

    // Add social share submenu under BRMedia menu
    public function add_social_share_menu() {
        add_submenu_page(
            'brmedia',             // Parent slug
            'Social Share Settings', // Page title
            'Social Share',        // Menu title
            'manage_options',      // Capability
            'brmedia-social-share',// Menu slug
            [$this, 'render_social_share_page'] // Callback
        );
    }

    // Enqueue styles and scripts
    public function enqueue_social_share_assets($hook) {
        if ($hook !== 'brmedia_page_brmedia-social-share') {
            return;
        }
        wp_enqueue_style('brmedia-social-share-css', plugins_url('assets/css/social-share.css', __FILE__), [], '1.1.0');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        wp_enqueue_script('brmedia-social-share-js', plugins_url('assets/js/social-share.js', __FILE__), ['jquery'], '1.1.0', true);
        wp_localize_script('brmedia-social-share-js', 'brmediaSocialShare', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('brmedia_social_share_nonce'),
        ]);
    }

    // Render the social share settings page
    public function render_social_share_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Available social platforms with icons
        $platforms = [
            'twitter'    => ['name' => 'Twitter', 'icon' => 'fab fa-twitter'],
            'facebook'   => ['name' => 'Facebook', 'icon' => 'fab fa-facebook'],
            'instagram'  => ['name' => 'Instagram', 'icon' => 'fab fa-instagram'],
            'linkedin'   => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin'],
            'pinterest'  => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest'],
            'reddit'     => ['name' => 'Reddit', 'icon' => 'fab fa-reddit'],
            'tumblr'     => ['name' => 'Tumblr', 'icon' => 'fab fa-tumblr'],
            'whatsapp'   => ['name' => 'WhatsApp', 'icon' => 'fab fa-whatsapp'],
        ];

        // Get current settings
        $enabled_platforms = get_option('brmedia_social_platforms', ['twitter', 'facebook']);
        $platform_order = get_option('brmedia_social_platform_order', array_keys($platforms));
        $button_style = get_option('brmedia_social_button_style', 'icon-text');
        $share_behavior = get_option('brmedia_social_share_behavior', 'popup');
        $analytics_enabled = get_option('brmedia_social_analytics_enabled', true);

        // Handle form submission
        if (isset($_POST['brmedia_save_social_settings']) && check_admin_referer('brmedia_save_social_settings')) {
            $enabled_platforms = array_filter($_POST['brmedia_social_platforms'] ?? [], 'sanitize_text_field');
            $platform_order = array_filter(explode(',', sanitize_text_field($_POST['brmedia_social_platform_order'] ?? '')), 'sanitize_text_field');
            $button_style = sanitize_text_field($_POST['brmedia_social_button_style'] ?? 'icon-text');
            $share_behavior = sanitize_text_field($_POST['brmedia_social_share_behavior'] ?? 'popup');
            $analytics_enabled = boolval($_POST['brmedia_social_analytics_enabled'] ?? false);

            update_option('brmedia_social_platforms', $enabled_platforms);
            update_option('brmedia_social_platform_order', $platform_order);
            update_option('brmedia_social_button_style', $button_style);
            update_option('brmedia_social_share_behavior', $share_behavior);
            update_option('brmedia_social_analytics_enabled', $analytics_enabled);

            $message = '<div class="notice notice-success is-dismissible"><p>Social share settings saved successfully.</p></div>';
        }

        ?>
        <div class="wrap">
            <h1>Social Share Settings</h1>
            <?php if (isset($message)) echo $message; ?>
            <form method="post">
                <?php wp_nonce_field('brmedia_save_social_settings'); ?>
                <div class="brmedia-social-settings">
                    <!-- Enabled Platforms -->
                    <section class="brmedia-platforms">
                        <h2>Enabled Platforms</h2>
                        <div class="platform-grid">
                            <?php foreach ($platforms as $platform => $data): ?>
                                <label class="platform-card">
                                    <input type="checkbox" 
                                           name="brmedia_social_platforms[]" 
                                           value="<?php echo esc_attr($platform); ?>" 
                                           <?php checked(in_array($platform, $enabled_platforms)); ?>>
                                    <i class="<?php echo esc_attr($data['icon']); ?>"></i> <?php echo esc_html($data['name']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Platform Order -->
                    <section class="brmedia-platform-order">
                        <h2>Platform Order</h2>
                        <input type="text" 
                               name="brmedia_social_platform_order" 
                               value="<?php echo esc_attr(implode(',', $platform_order)); ?>" 
                               placeholder="Comma-separated list of platforms" 
                               style="width: 400px;">
                        <p class="description">Enter the desired order of platforms (e.g., twitter,facebook,instagram).</p>
                    </section>

                    <!-- Button Style -->
                    <section class="brmedia-button-style">
                        <h2>Button Style</h2>
                        <select name="brmedia_social_button_style">
                            <option value="icon-only" <?php selected($button_style, 'icon-only'); ?>>Icon Only</option>
                            <option value="icon-text" <?php selected($button_style, 'icon-text'); ?>>Icon + Text</option>
                            <option value="text-only" <?php selected($button_style, 'text-only'); ?>>Text Only</option>
                        </select>
                    </section>

                    <!-- Share Behavior -->
                    <section class="brmedia-share-behavior">
                        <h2>Share Behavior</h2>
                        <select name="brmedia_social_share_behavior">
                            <option value="popup" <?php selected($share_behavior, 'popup'); ?>>Open in Popup</option>
                            <option value="new-tab" <?php selected($share_behavior, 'new-tab'); ?>>Open in New Tab</option>
                            <option value="same-tab" <?php selected($share_behavior, 'same-tab'); ?>>Open in Same Tab</option>
                        </select>
                    </section>

                    <!-- Analytics Integration -->
                    <section class="brmedia-analytics">
                        <h2>Analytics Integration</h2>
                        <label>
                            <input type="checkbox" 
                                   name="brmedia_social_analytics_enabled" 
                                   value="1" 
                                   <?php checked($analytics_enabled); ?>>
                            Enable Social Share Analytics
                        </label>
                        <p class="description">Track sharing actions in analytics.</p>
                    </section>

                    <?php submit_button('Save Social Share Settings', 'primary', 'brmedia_save_social_settings'); ?>
                </div>
            </form>
        </div>
        <?php
    }
}

// Instantiate the social share admin
new BRMedia_Social_Share();