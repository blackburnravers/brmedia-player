<?php
/**
 * Minimal Audio Player Template
 *
 * This template provides a minimalistic audio player with basic controls.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve post data
$post_id = $post->ID;
$audio_url = get_post_meta($post_id, 'brmedia_audio_url', true);
?>

<div class="brmedia-audio-player minimal" data-post-id="<?php echo esc_attr($post_id); ?>">
    <audio controls>
        <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>