<?php
/**
 * Video Templates
 * Advanced rendering of video players with metadata and embeds.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Video player template
function brmedia_video_player_template($video_id, $template = 'default') {
    $valid_templates = ['default', 'popup'];
    $template = in_array($template, $valid_templates) ? $template : 'default';

    $video_url = get_post_meta($video_id, 'brmedia_video_url', true);
    $title = get_the_title($video_id);
    $duration = get_post_meta($video_id, 'brmedia_video_duration', true) ?: 'Unknown';

    if (!$video_url || get_post_type($video_id) !== 'brmedia_video') {
        return '<p>Error: Invalid video</p>';
    }

    ob_start();
    ?>
    <div class="brmedia-video-player brmedia-template-<?php echo esc_attr($template); ?>" data-video-id="<?php echo esc_attr($video_id); ?>">
        <h3><?php echo esc_html($title); ?></h3>
        <p>Duration: <?php echo esc_html($duration); ?></p>
        <?php if (preg_match('/youtube\.com\/watch\?v=([^&]+)/i', $video_url, $matches)) : ?>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo esc_attr($matches[1]); ?>" frameborder="0" allowfullscreen></iframe>
        <?php else : ?>
            <video id="player-<?php echo esc_attr($video_id); ?>" controls>
                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const player = new Plyr('#player-<?php echo esc_attr($video_id); ?>', {
                        controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
                    });
                    player.on('play', function() {
                        jQuery.post(brmedia_video.ajax_url, {
                            action: 'brmedia_video_action',
                            video_id: <?php echo esc_attr($video_id); ?>,
                            action_type: 'play',
                            nonce: brmedia_video.nonce
                        });
                    });
                });
            </script>
        <?php endif; ?>
        <?php if ($template === 'popup') : ?>
            <button class="brmedia-popup-btn" onclick="jQuery(this).next('.brmedia-popup').show();">Open in Popup</button>
            <div class="brmedia-popup" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; z-index:1000;">
                <?php echo $video_url; ?>
                <button onclick="jQuery(this).parent().hide();">Close</button>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}