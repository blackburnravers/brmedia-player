<?php
/**
 * Shortcodes Manager Page
 * Advanced interface for listing, previewing, and copying shortcodes.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Shortcodes manager class
class BRMedia_Shortcodes_Manager {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_shortcodes_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_shortcodes_assets']);
    }

    // Add shortcodes submenu under BRMedia menu
    public function add_shortcodes_menu() {
        add_submenu_page(
            'brmedia',             // Parent slug
            'Shortcodes Manager',  // Page title
            'Shortcodes',          // Menu title
            'manage_options',      // Capability
            'brmedia-shortcodes',  // Menu slug
            [$this, 'render_shortcodes_page'] // Callback
        );
    }

    // Enqueue styles and scripts
    public function enqueue_shortcodes_assets($hook) {
        if ($hook !== 'brmedia_page_brmedia-shortcodes') {
            return;
        }
        wp_enqueue_style('brmedia-shortcodes-css', plugins_url('assets/css/shortcodes.css', __FILE__), [], '1.1.0');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        wp_enqueue_script('brmedia-shortcodes-js', plugins_url('assets/js/shortcodes.js', __FILE__), ['jquery'], '1.1.0', true);
        wp_localize_script('brmedia-shortcodes-js', 'brmediaShortcodes', [
            'preview_nonce' => wp_create_nonce('brmedia_preview_nonce'),
            'ajax_url'      => admin_url('admin-ajax.php'),
        ]);
    }

    // Render the shortcodes manager page
    public function render_shortcodes_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Define shortcodes with descriptions, attributes, and examples
        $shortcodes = [
            'brmedia_audio' => [
                'description' => 'Embeds an audio player with customizable options.',
                'attributes'  => [
                    'id'        => 'The ID of the audio track (required).',
                    'template'  => 'Player template (default, compact, playlist, popup, fullscreen, minimal).',
                    'autoplay'  => 'Autoplay the audio (true/false).',
                    'loop'      => 'Loop the audio (true/false).',
                ],
                'example'     => '[brmedia_audio id="123" template="default" autoplay="false"]',
                'preview'     => '<audio controls><source src="#" type="audio/mpeg"></audio>',
            ],
            'brmedia_video' => [
                'description' => 'Embeds a video player with various layout options.',
                'attributes'  => [
                    'id'        => 'The ID of the video (required).',
                    'template'  => 'Video template (default, popup, embedded, cinematic).',
                    'width'     => 'Width of the video player.',
                    'height'    => 'Height of the video player.',
                ],
                'example'     => '[brmedia_video id="456" template="default" width="600" height="400"]',
                'preview'     => '<video controls><source src="#" type="video/mp4"></video>',
            ],
            'brmedia_tracklist' => [
                'description' => 'Displays a tracklist with timestamps for a given audio track.',
                'attributes'  => [
                    'id'        => 'The ID of the tracklist (required).',
                    'format'    => 'Timestamp format (hh:mm:ss).',
                ],
                'example'     => '[brmedia_tracklist id="789" format="hh:mm:ss"]',
                'preview'     => '<ul><li>00:00 - Intro</li><li>03:45 - Main Track</li></ul>',
            ],
            'brmedia_cover' => [
                'description' => 'Displays cover art for a track or album.',
                'attributes'  => [
                    'id'        => 'The ID of the cover art (required).',
                    'size'      => 'Image size (thumbnail, medium, large).',
                ],
                'example'     => '[brmedia_cover id="101" size="medium"]',
                'preview'     => '<img src="#" alt="Cover Art" style="width:200px;height:200px;">',
            ],
            'brmedia_download' => [
                'description' => 'Provides a customizable download button for files.',
                'attributes'  => [
                    'id'        => 'The ID of the file (required).',
                    'template'  => 'Button template (big, small, massive, icon-only, progress-bar).',
                    'label'     => 'Button text (e.g., "Download Now").',
                    'gated'     => 'Gating type (email, social, login).',
                ],
                'example'     => '[brmedia_download id="202" template="big" label="Download Now" gated="email"]',
                'preview'     => '<button><i class="fas fa-download"></i> Download Now</button>',
            ],
        ];

        ?>
        <div class="wrap">
            <h1>Shortcodes Manager</h1>
            <div class="brmedia-shortcodes-manager">
                <p>Below is a list of all available shortcodes with their attributes, examples, and live previews. Use the copy button to easily add them to your posts or pages.</p>
                <div class="shortcode-grid">
                    <?php foreach ($shortcodes as $shortcode => $data): ?>
                        <div class="shortcode-card">
                            <h3><?php echo esc_html($shortcode); ?></h3>
                            <p><?php echo esc_html($data['description']); ?></p>
                            <details>
                                <summary>Attributes</summary>
                                <ul>
                                    <?php foreach ($data['attributes'] as $attr => $desc): ?>
                                        <li><strong><?php echo esc_html($attr); ?>:</strong> <?php echo esc_html($desc); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </details>
                            <p><strong>Example:</strong> <code><?php echo esc_html($data['example']); ?></code></p>
                            <div class="shortcode-preview">
                                <strong>Preview:</strong>
                                <div><?php echo $data['preview']; ?></div>
                            </div>
                            <button class="button button-primary copy-shortcode" 
                                    data-shortcode="<?php echo esc_attr($data['example']); ?>">
                                Copy Shortcode
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                $('.copy-shortcode').on('click', function() {
                    const shortcode = $(this).data('shortcode');
                    navigator.clipboard.writeText(shortcode).then(() => {
                        alert('Shortcode copied to clipboard!');
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                    });
                });
            });
        </script>
        <?php
    }
}

// Instantiate the shortcodes manager
new BRMedia_Shortcodes_Manager();