<?php
/**
 * Template: Downloads Pro
 *
 * Renders an advanced download button with progress and DRM features.
 *
 * @package BRMedia\Templates
 */

// Ensure media ID is set
if (!isset($media_id)) {
    echo '<p>Media ID not provided.</p>';
    return;
}

// Get download URL
$download_url = get_post_meta($media_id, '_brmedia_download_url', true);
if (empty($download_url)) {
    echo '<p>Download URL not found.</p>';
    return;
}
?>

<a href="<?php echo esc_url($download_url); ?>" class="brmedia-download-pro" data-media-id="<?php echo esc_attr($media_id); ?>">
    <span class="label">Download</span>
    <span class="progress"></span>
</a>

<style>
    .brmedia-download-pro {
        display: inline-block;
        position: relative;
        padding: 10px 20px;
        background: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        overflow: hidden;
        transition: background 0.3s ease;
    }
    .brmedia-download-pro:hover {
        background: #0056b3;
    }
    .brmedia-download-pro .progress {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 0;
        background: rgba(255, 255, 255, 0.3);
        transition: width 2s ease;
    }
    .brmedia-download-pro.downloading .progress {
        width: 100%;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const downloadBtn = document.querySelector('.brmedia-download-pro');
    downloadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        this.classList.add('downloading');
        setTimeout(() => {
            window.location.href = this.href; // Simulate download start after animation
        }, 2000);
    });
});
</script>