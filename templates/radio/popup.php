<?php
/**
 * Popup Radio Template
 *
 * Displays a button to open a radio player in a popup modal.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve radio data
$post_id = get_the_ID();
$stream_url = get_post_meta($post_id, 'brmedia_radio_stream_url', true);

// Validate required data
if (empty($stream_url)) {
    echo '<p>' . esc_html__('Radio stream URL is missing.', 'brmedia') . '</p>';
    return;
}

// Enqueue styles and scripts
wp_enqueue_style('brmedia-frontend');
wp_enqueue_script('brmedia-frontend');
?>

<button class="brmedia-radio-popup-button" aria-label="<?php echo esc_attr__('Open radio player popup', 'brmedia'); ?>">
    <?php esc_html_e('Listen to Radio', 'brmedia'); ?>
</button>

<div id="brmedia-radio-popup" class="brmedia-popup" style="display: none;" role="dialog" aria-hidden="true">
    <div class="popup-content">
        <audio id="radio-player-popup" controls preload="none">
            <source src="<?php echo esc_url($stream_url); ?>" type="audio/mpeg">
            <?php esc_html_e('Your browser does not support the audio element.', 'brmedia'); ?>
        </audio>
        <button class="close-popup" aria-label="<?php echo esc_attr__('Close popup', 'brmedia'); ?>">
            <?php esc_html_e('Close', 'brmedia'); ?>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const popupButton = document.querySelector('.brmedia-radio-popup-button');
    const popup = document.getElementById('brmedia-radio-popup');
    const closeButton = popup.querySelector('.close-popup');
    const player = document.getElementById('radio-player-popup');

    popupButton.addEventListener('click', function() {
        popup.style.display = 'flex';
        popup.setAttribute('aria-hidden', 'false');
        player.play().catch(error => console.error('Auto-play failed:', error));
    });

    closeButton.addEventListener('click', function() {
        popup.style.display = 'none';
        popup.setAttribute('aria-hidden', 'true');
        player.pause();
    });

    // Close popup with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && popup.style.display === 'flex') {
            popup.style.display = 'none';
            popup.setAttribute('aria-hidden', 'true');
            player.pause();
        }
    });
});
</script>