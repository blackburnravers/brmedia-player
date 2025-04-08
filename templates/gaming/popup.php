<?php
/**
 * Popup Gaming Template
 *
 * Displays a button to open a Twitch live stream in a popup modal.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve gaming data
$post_id = get_the_ID();
$channel_name = get_post_meta($post_id, 'brmedia_twitch_channel', true);

// Validate required data
if (empty($channel_name)) {
    echo '<p>' . esc_html__('Twitch channel name is missing.', 'brmedia') . '</p>';
    return;
}

// Enqueue styles and scripts
wp_enqueue_style('brmedia-frontend');
wp_enqueue_script('brmedia-frontend');
?>

<button class="brmedia-gaming-popup-button" data-channel="<?php echo esc_attr($channel_name); ?>" aria-label="<?php echo esc_attr__('Open live stream popup', 'brmedia'); ?>">
    <?php esc_html_e('Watch Stream', 'brmedia'); ?>
</button>

<div id="brmedia-gaming-popup" class="brmedia-popup" style="display: none;" role="dialog" aria-hidden="true">
    <div class="popup-content">
        <iframe
            src="https://player.twitch.tv/?channel=<?php echo esc_attr($channel_name); ?>&parent=<?php echo esc_attr(wp_parse_url(home_url(), PHP_URL_HOST)); ?>"
            frameborder="0"
            allowfullscreen="true"
            scrolling="no"
            height="378"
            width="620"
            aria-label="<?php echo esc_attr__('Twitch live stream', 'brmedia'); ?>"
        ></iframe>
        <button class="close-popup" aria-label="<?php echo esc_attr__('Close popup', 'brmedia'); ?>">
            <?php esc_html_e('Close', 'brmedia'); ?>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const popupButton = document.querySelector('.brmedia-gaming-popup-button');
    const popup = document.getElementById('brmedia-gaming-popup');
    const closeButton = popup.querySelector('.close-popup');

    popupButton.addEventListener('click', function() {
        popup.style.display = 'flex';
        popup.setAttribute('aria-hidden', 'false');
    });

    closeButton.addEventListener('click', function() {
        popup.style.display = 'none';
        popup.setAttribute('aria-hidden', 'true');
    });

    // Close popup with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && popup.style.display === 'flex') {
            popup.style.display = 'none';
            popup.setAttribute('aria-hidden', 'true');
        }
    });
});
</script>