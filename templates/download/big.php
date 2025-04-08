<?php
/**
 * Big Download Button Template
 *
 * A larger download button with enhanced visibility and hover effects.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve download data
$post_id = $post->ID;
$download_url = BRMedia_Helpers::generate_download_url($post_id);
$title = get_the_title($post_id);
?>

<a href="<?php echo esc_url($download_url); ?>" 
   class="brmedia-download-button big" 
   data-post-id="<?php echo esc_attr($post_id); ?>" 
   title="Download <?php echo esc_attr($title); ?>">
    <i class="fas fa-download"></i> Download <?php echo esc_html($title); ?>
</a>