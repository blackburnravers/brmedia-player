<?php
/**
 * Gaming Templates
 * Advanced rendering of gaming content with live status and metadata.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Gaming content template
function brmedia_gaming_template($type = 'twitch', $id, $width = '600', $height = '400') {
    $valid_types = ['twitch', 'youtube'];
    $type = in_array($type, $valid_types) ? $type : 'twitch';

    if (empty($id)) {
        return '<p>Error: Missing content ID</p>';
    }

    ob_start();
    ?>
    <div class="brmedia-gaming-container" data-type="<?php echo esc_attr($type); ?>" data-id="<?php echo esc_attr($id); ?>">
        <h3><?php echo esc_html($type === 'twitch' ? 'Twitch Stream' : 'YouTube Video'); ?>: <span class="brmedia-gaming-title"><?php echo esc_html($id); ?></span></h3>
        <span class="brmedia-gaming-status">Checking status...</span>
        <?php if ($type === 'twitch') : ?>
            <iframe src="https://player.twitch.tv/?channel=<?php echo esc_attr($id); ?>&parent=<?php echo esc_attr($_SERVER['HTTP_HOST']); ?>"
                    width="<?php echo esc_attr($width); ?>"
                    height="<?php echo esc_attr($height); ?>"
                    frameborder="0"
                    scrolling="no"
                    allowfullscreen></iframe>
        <?php else : ?>
            <iframe width="<?php echo esc_attr($width); ?>"
                    height="<?php echo esc_attr($height); ?>"
                    src="https://www.youtube.com/embed/<?php echo esc_attr($id); ?>"
                    frameborder="0"
                    allowfullscreen></iframe>
        <?php endif; ?>
        <script>
            jQuery(document).ready(function($) {
                const $container = $('.brmedia-gaming-container[data-id="<?php echo esc_attr($id); ?>"]');
                const type = $container.data('type');
                const id = $container.data('id');

                $.post(brmedia_gaming.ajax_url, {
                    action: 'brmedia_gaming_status',
                    type: type,
                    id: id,
                    nonce: brmedia_gaming.nonce
                }, function(response) {
                    if (response.success) {
                        $container.find('.brmedia-gaming-status').text(response.data.is_live ? 'Live' : 'Offline');
                    } else {
                        $container.find('.brmedia-gaming-status').text('Status unavailable');
                    }
                });
            });
        </script>
    </div>
    <?php
    return ob_get_clean();
}