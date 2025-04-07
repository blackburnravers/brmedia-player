<?php
/**
 * Small Download Button Template
 * A compact, customizable download button for tight spaces.
 *
 * @package BRMediaPlayer
 * @subpackage Templates
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Retrieve template settings from ACP
$settings = get_option('brmedia_template_settings', []);
$download_settings = $settings['downloads'] ?? [];
$button_color = $download_settings['button_color'] ?? '#0073aa';
$button_text = $download_settings['button_text'] ?? 'Download';
$hover_effect = $download_settings['hover_effect'] ?? 'color'; // Default to color change

// File data
$file_id = $atts['id'] ?? 0;
$file_url = wp_get_attachment_url($file_id);
$file_name = basename($file_url);

// Validate file URL
if (empty($file_url)) {
    echo '<p>Error: No file found.</p>';
    return;
}

?>
<a href="<?php echo esc_url($file_url); ?>" 
   class="brmedia-download-btn small-template" 
   style="background-color: <?php echo esc_attr($button_color); ?>; 
          color: #fff; 
          padding: 8px 16px; 
          text-decoration: none; 
          border-radius: 3px; 
          font-size: 14px; 
          display: inline-block; 
          transition: all 0.3s ease;"
   data-hover-effect="<?php echo esc_attr($hover_effect); ?>">
    <i class="fas fa-download"></i> <?php echo esc_html($button_text); ?>
</a>

<style>
    .brmedia-download-btn.small-template:hover {
        <?php if ($hover_effect === 'scale'): ?>
            transform: scale(1.05);
        <?php elseif ($hover_effect === 'color'): ?>
            filter: brightness(1.1);
        <?php endif; ?>
    }
</style>