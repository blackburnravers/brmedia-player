<?php
/**
 * BRMedia Player Dashboard Page
 *
 * This file renders the dashboard page with module stats and controls.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Dashboard page callback
 */
function brmedia_dashboard_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    ?>
    <div class="wrap">
        <h1>BRMedia Player Dashboard</h1>
        <div class="brmedia-dashboard" style="display: flex; flex-wrap: wrap; gap: 20px;">
            <!-- Module Stats -->
            <div class="brmedia-module-stats" style="flex: 1; min-width: 300px;">
                <h2>Module Stats</h2>
                <?php
                $modules = array('music', 'video', 'radio', 'gaming', 'podcasts', 'chat', 'downloads');
                foreach ($modules as $module) {
                    $enabled = get_option("brmedia_{$module}_enabled", true);
                    $stats = brmedia_get_module_stats($module);
                    ?>
                    <div class="brmedia-module-stat" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
                        <h3><?php echo ucfirst($module); ?></h3>
                        <p>Status: <span style="color: <?php echo $enabled ? 'green' : 'red'; ?>;"><?php echo $enabled ? 'Enabled' : 'Disabled'; ?></span></p>
                        <p>Items: <?php echo esc_html($stats['items']); ?></p>
                        <p>Plays: <?php echo esc_html($stats['plays']); ?></p>
                        <p>Downloads: <?php echo esc_html($stats['downloads']); ?></p>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- Module Controls -->
            <div class="brmedia-module-controls" style="flex: 1; min-width: 300px;">
                <h2>Enable/Disable Modules</h2>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('brmedia_module_settings');
                    do_settings_sections('brmedia_module_settings');
                    foreach ($modules as $module) {
                        if ($module !== 'music') { // Music module is always enabled
                            $enabled = get_option("brmedia_{$module}_enabled", true);
                            ?>
                            <label style="display: block; margin-bottom: 10px;">
                                <input type="checkbox" name="brmedia_<?php echo esc_attr($module); ?>_enabled" value="1" <?php checked($enabled, true); ?> />
                                <?php echo esc_html(ucfirst($module)); ?>
                            </label>
                            <?php
                        }
                    }
                    submit_button('Save Changes', 'primary', 'submit', false);
                    ?>
                </form>
            </div>

            <!-- Quick Links -->
            <div class="brmedia-quick-links" style="flex: 1; min-width: 300px;">
                <h2>Quick Links</h2>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="<?php echo esc_url(admin_url('edit.php?post_type=brmusic')); ?>">Manage Music</a></li>
                    <li><a href="<?php echo esc_url(admin_url('edit.php?post_type=brvideo')); ?>">Manage Videos</a></li>
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=brmedia-templates')); ?>">Customize Templates</a></li>
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=brmedia-shortcodes')); ?>">Shortcodes Manager</a></li>
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=brmedia-analytics')); ?>">View Analytics</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Retrieve statistics for a specific module
 *
 * @param string $module Module name
 * @return array Associative array of stats
 */
function brmedia_get_module_stats($module) {
    $post_type = 'br' . $module;
    $items = wp_count_posts($post_type)->publish;
    // Placeholder for plays and downloads; replace with actual data retrieval logic
    $plays = get_option("brmedia_{$module}_plays", 0);
    $downloads = get_option("brmedia_{$module}_downloads", 0);
    return array(
        'items' => $items ? $items : 0,
        'plays' => $plays,
        'downloads' => $downloads,
    );
}