<?php
/**
 * Template: Cover Pro
 *
 * Renders an advanced cover art display with hover effects.
 *
 * @package BRMedia\Templates
 */

// Ensure media ID is set
if (!isset($media_id)) {
    echo '<p>Media ID not provided.</p>';
    return;
}

// Get cover image and metadata
$cover_url = get_the_post_thumbnail_url($media_id, 'medium') ?: 'default-cover.jpg';
$title = get_the_title($media_id);
$artist = get_post_meta($media_id, '_brmedia_artist', true);
?>

<div class="brmedia-cover-pro" data-media-id="<?php echo esc_attr($media_id); ?>">
    <img src="<?php echo esc_url($cover_url); ?>" alt="<?php echo esc_attr($title); ?>">
    <div class="overlay">
        <h3><?php echo esc_html($title); ?></h3>
        <p><?php echo esc_html($artist); ?></p>
    </div>
</div>

<style>
    .brmedia-cover-pro {
        position: relative;
        width: 300px;
        height: 300px;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    .brmedia-cover-pro img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .brmedia-cover-pro:hover img {
        transform: scale(1.1);
    }
    .brmedia-cover-pro .overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 10px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    .brmedia-cover-pro:hover .overlay {
        transform: translateY(0);
    }
</style>