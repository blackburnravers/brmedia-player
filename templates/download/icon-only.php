<?php
/**
 * Icon-Only Download Button Template
 * A minimalist download button with just an icon, customizable via ACP.
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
$icon_color = $download_settings['icon_color'] ?? '#0073aa';
$icon_size = $download_settings['icon_size'] ?? '24px';
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
   class="brmedia-download-btn icon-only-template" 
   style="color: <?php echo esc_attr($icon_color); ?>; 
          font-size: <?php echo esc_attr($icon_size); ?>; 
          display: inline-block; 
          transition: all 0.3s ease;"
   data-hover-effect="<?php echo esc_attr($hover_effect); ?>">
    <i class="fas fa-download"></i>
</a>

<style>
    .brmedia-download-btn.icon-only-template:hover {
        <?php if ($hover_effect === 'scale'): ?>
            transform: scale(1.2);
        <?php elseif ($hover_effect === 'color'): ?>
            color: <?php echo esc_attr($download_settings['hover_color'] ?? '#005d82'); ?>;
        <?php endif; ?>
    }
</style>