<?php
/**
 * BRMedia Templates Pro Class
 *
 * Manages advanced template settings with live preview.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Templates_Pro {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_templates_page']);
    }

    /**
     * Registers templates management page
     */
    public function add_templates_page() {
        add_submenu_page(
            'brmedia-dashboard',           // Parent slug
            'Templates Manager',           // Page title
            'Templates Pro',               // Menu title
            'manage_options',              // Capability
            'brmedia-templates-pro',       // Menu slug
            [$this, 'render_templates_page'] // Callback
        );
    }

    /**
     * Renders templates management page
     */
    public function render_templates_page() {
        $templates = ['audio-reactive', 'video-cinematic'];
        ?>
        <div class="wrap">
            <h1>Templates Manager (Pro)</h1>
            <select id="template-select">
                <?php foreach ($templates as $template) : ?>
                    <option value="<?php echo esc_attr($template); ?>"><?php echo esc_html($template); ?></option>
                <?php endforeach; ?>
            </select>
            <div id="template-preview" class="brmedia-player" data-template="audio-reactive">
                <audio class="plyr"><source src="sample.mp3" type="audio/mp3"></audio>
            </div>
        </div>
        <?php
    }
}