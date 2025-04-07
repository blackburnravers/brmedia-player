<?php
/**
 * Music Templates
 * Advanced rendering of music players with playlists and metadata.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Single track player template
function brmedia_music_template($id, $template = 'default') {
    $valid_templates = ['default', 'compact', 'playlist'];
    $template = in_array($template, $valid_templates) ? $template : 'default';

    $audio_url = get_post_meta($id, 'brmedia_music_audio', true) ?: wp_get_attachment_url($id);
    $title = get_the_title($id);
    $duration = get_post_meta($id, 'brmedia_music_duration', true) ?: 'Unknown';

    if (!$audio_url || get_post_type($id) !== 'brmedia_music') {
        return '<p>Error: Invalid track</p>';
    }

    ob_start();
    ?>
    <div class="brmedia-music-player brmedia-template-<?php echo esc_attr($template); ?>" data-track-id="<?php echo esc_attr($id); ?>">
        <h3><?php echo esc_html($title); ?></h3>
        <p>Duration: <?php echo esc_html($duration); ?></p>
        <audio id="player-<?php echo esc_attr($id); ?>" controls>
            <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const player = new Plyr('#player-<?php echo esc_attr($id); ?>', {
                    controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
                    <?php if ($template === 'compact') : ?>controlSpacing: 5, fontSize: 12<?php endif; ?>
                });
                player.on('play', function() {
                    jQuery.post(brmedia_music.ajax_url, {
                        action: 'brmedia_music_action',
                        track_id: <?php echo esc_attr($id); ?>,
                        action_type: 'play',
                        nonce: brmedia_music.nonce
                    });
                });
            });
        </script>
    </div>
    <?php
    return ob_get_clean();
}

// Playlist template
function brmedia_music_playlist_template($playlist_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'brmedia_music_playlist';
    $tracks = $wpdb->get_results($wpdb->prepare(
        "SELECT track_id FROM $table WHERE playlist_id = %d ORDER BY position ASC",
        $playlist_id
    ), ARRAY_A);

    if (empty($tracks)) {
        return '<p>No tracks in this playlist.</p>';
    }

    ob_start();
    ?>
    <div class="brmedia-music-playlist" data-playlist-id="<?php echo esc_attr($playlist_id); ?>">
        <h2><?php echo esc_html(get_term($playlist_id, 'brmedia_playlist')->name); ?></h2>
        <div class="brmedia-track-list">
            <?php foreach ($tracks as $track) : ?>
                <?php echo brmedia_music_template($track['track_id'], 'compact'); ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}