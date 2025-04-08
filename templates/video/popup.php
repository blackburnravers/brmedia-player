<?php
/**
 * Popup Video Player Template
 *
 * Displays a button to open a video player in a popup modal.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve video data
$post_id = get_the_ID();
$video_url = get_post_meta($post_id, 'brmedia_video_url', true);
$poster_url = get_the_post_thumbnail_url($post_id, 'full');

// Validate required data
if (empty($video_url)) {
    echo '<p>' . esc_html__('Video URL is missing.', 'brmedia') . '</p>';
    return;
}
?>

<button class="brmedia-video-popup-button" data-video-id="<?php echo esc_attr($post_id); ?>" aria-label="<?php echo esc_attr__('Open video popup', 'brmedia'); ?>">
    <?php esc_html_e('Watch Video', 'brmedia'); ?>
</button>

<div id="brmedia-video-popup-<?php echo esc_attr($post_id); ?>" class="brmedia-popup" style="display: none;" role="dialog" aria-hidden="true">
    <div class="popup-content">
        <video id="video-popup-<?php echo esc_attr($post_id); ?>" class="video-js" controls preload="auto" poster="<?php echo esc_url($poster_url); ?>" data-setup="{}">
            <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
            <p class="vjs-no-js">
                <?php esc_html_e('To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video.', 'brmedia'); ?>
            </p>
        </video>
        <button class="close-popup" aria-label="<?php echo esc_attr__('Close popup', 'brmedia'); ?>">
            <?php esc_html_e('Close', 'brmedia'); ?>
        </button>
    </div>
</div>

<style>
.brmedia-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.popup-content {
    position: relative;
    width: 80%;
    max-width: 800px;
    background: #fff;
    padding: 20px;
}
.close-popup {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff0000;
    color: #fff;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}
</style>