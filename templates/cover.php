<?php
/**
 * Template: Cover
 *
 * Renders a basic cover art display.
 *
 * @package BRMedia\Templates
 */

// Ensure media ID is set
if (!isset($media_id)) {
    echo '<p>Media ID not provided.</p>';
    return;
}

// Get cover image
$cover_url = get_the_post_thumbnail_url($media_id, 'medium') ?: 'default-cover.jpg';
?>

<div class="brmedia-cover" data-media-id="<?php echo esc_attr($media_id); ?>">
    <img src="<?php echo esc_url($cover_url); ?>" alt="Cover Art">
</div>

<style>
    .brmedia-cover {
        width: 200px;
        height: 200px;
        border-radius: 5px;
        overflow: hidden;
    }
    .brmedia-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>