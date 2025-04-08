<?php
/**
 * Progress Bar Download Button Template
 *
 * A download button with an animated progress bar for feedback.
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

<div class="brmedia-download-progress" data-post-id="<?php echo esc_attr($post_id); ?>">
    <a href="<?php echo esc_url($download_url); ?>" 
       class="progress-button" 
       title="Download <?php echo esc_attr($title); ?>">
        <i class="fas fa-download"></i> Download <?php echo esc_html($title); ?>
    </a>
    <div class="progress-bar">
        <div class="progress" style="width: 0%;"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressButtons = document.querySelectorAll('.brmedia-download-progress .progress-button');
    progressButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const progressBar = this.nextElementSibling.querySelector('.progress');
            let width = 0;
            const interval = setInterval(function() {
                if (width >= 100) {
                    clearInterval(interval);
                    window.location.href = button.href;
                } else {
                    width += 10;
                    progressBar.style.width = width + '%';
                }
            }, 200);
        });
    });
});
</script>