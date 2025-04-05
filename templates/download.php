<?php
/**
 * Template: Download
 *
 * Renders a basic download button.
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

<a href="<?php echo esc_url($download_url); ?>" class="brmedia-download" data-media-id="<?php echo esc_attr($media_id); ?>">
    Download
</a>

<style>
    .brmedia-download {
        display: inline-block;
        padding: 8px 16px;
        background: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background 0.3s ease;
    }
    .brmedia-download:hover {
        background: #0056b3;
    }
</style>