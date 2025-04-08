<?php
/**
 * Default Gaming Template
 *
 * Embeds a Twitch live stream with real-time status, viewer count, and chat integration.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve gaming data from post meta and plugin settings
$post_id = get_the_ID();
$channel_name = get_post_meta($post_id, 'brmedia_twitch_channel', true);
$client_id = get_option('brmedia_twitch_client_id', '');
$oauth_token = get_option('brmedia_twitch_oauth_token', '');

// Validate required data
if (empty($channel_name) || empty($client_id) || empty($oauth_token)) {
    echo '<p>' . esc_html__('Twitch configuration is incomplete. Please check your settings.', 'brmedia') . '</p>';
    return;
}

// Enqueue styles and scripts
wp_enqueue_style('brmedia-frontend');
wp_enqueue_script('brmedia-frontend');
?>

<div class="brmedia-gaming-default" data-channel="<?php echo esc_attr($channel_name); ?>" aria-label="<?php echo esc_attr(sprintf(__('Live stream for %s', 'brmedia'), $channel_name)); ?>">
    <!-- Stream Container -->
    <div class="stream-container">
        <iframe
            src="https://player.twitch.tv/?channel=<?php echo esc_attr($channel_name); ?>&parent=<?php echo esc_attr(wp_parse_url(home_url(), PHP_URL_HOST)); ?>"
            frameborder="0"
            allowfullscreen="true"
            scrolling="no"
            height="378"
            width="620"
            aria-label="<?php echo esc_attr__('Twitch live stream', 'brmedia'); ?>"
        ></iframe>
    </div>

    <!-- Stream Information -->
    <div class="stream-info">
        <h3 id="stream-title"><?php esc_html_e('Loading stream data...', 'brmedia'); ?></h3>
        <p id="stream-status"><?php esc_html_e('Status:', 'brmedia'); ?> <span><?php esc_html_e('Unknown', 'brmedia'); ?></span></p>
        <p id="viewer-count"><?php esc_html_e('Viewers:', 'brmedia'); ?> <span>0</span></p>
        <button class="follow-button" data-channel="<?php echo esc_attr($channel_name); ?>" aria-label="<?php echo esc_attr__('Follow this channel', 'brmedia'); ?>">
            <?php esc_html_e('Follow', 'brmedia'); ?>
        </button>
    </div>

    <!-- Chat Container -->
    <div class="chat-container">
        <iframe
            frameborder="0"
            scrolling="no"
            id="chat_embed"
            src="https://www.twitch.tv/embed/<?php echo esc_attr($channel_name); ?>/chat?parent=<?php echo esc_attr(wp_parse_url(home_url(), PHP_URL_HOST)); ?>"
            height="500"
            width="350"
            aria-label="<?php echo esc_attr__('Twitch chat', 'brmedia'); ?>"
        ></iframe>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.brmedia-gaming-default');
    const channel = container.dataset.channel;
    const clientId = '<?php echo esc_js($client_id); ?>';
    const oauthToken = '<?php echo esc_js($oauth_token); ?>';

    function fetchStreamData() {
        fetch(`https://api.twitch.tv/helix/streams?user_login=${channel}`, {
            headers: {
                'Client-ID': clientId,
                'Authorization': `Bearer ${oauthToken}`
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('API request failed');
            return response.json();
        })
        .then(data => {
            const stream = data.data[0];
            const titleElement = document.getElementById('stream-title');
            const statusElement = document.getElementById('stream-status').querySelector('span');
            const viewersElement = document.getElementById('viewer-count').querySelector('span');

            if (stream) {
                titleElement.textContent = stream.title;
                statusElement.textContent = '<?php echo esc_js(__('Live', 'brmedia')); ?>';
                viewersElement.textContent = stream.viewer_count;
            } else {
                titleElement.textContent = '<?php echo esc_js(__('Stream is offline', 'brmedia')); ?>';
                statusElement.textContent = '<?php echo esc_js(__('Offline', 'brmedia')); ?>';
                viewersElement.textContent = '0';
            }
        })
        .catch(error => {
            console.error('Error fetching stream data:', error);
            document.getElementById('stream-title').textContent = '<?php echo esc_js(__('Failed to load stream data', 'brmedia')); ?>';
        });
    }

    // Initial fetch and periodic updates (every 5 minutes)
    fetchStreamData();
    setInterval(fetchStreamData, 300000);

    // Follow button functionality (placeholder for Twitch follow action)
    const followButton = container.querySelector('.follow-button');
    followButton.addEventListener('click', function() {
        alert(`Follow feature for ${channel} is not fully implemented in this demo.`);
        // In a real implementation, use Twitch API to follow the channel
    });
});
</script>