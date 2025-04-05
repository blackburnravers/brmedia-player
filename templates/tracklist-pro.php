<?php
/**
 * Template: Tracklist Pro
 *
 * Renders an advanced tracklist with timestamped cues.
 *
 * @package BRMedia\Templates
 */

// Ensure tracklist data is set
if (!isset($tracklist) || !is_array($tracklist)) {
    echo '<p>Tracklist data not provided.</p>';
    return;
}
?>

<div class="brmedia-tracklist-pro" data-media-id="<?php echo esc_attr($media_id ?? ''); ?>">
    <ul>
        <?php foreach ($tracklist as $track): ?>
            <li data-timestamp="<?php echo esc_attr($track['timestamp']); ?>">
                <span class="timestamp"><?php echo esc_html($track['timestamp']); ?></span>
                <span class="title"><?php echo esc_html($track['title']); ?></span>
                <span class="artist"><?php echo esc_html($track['artist']); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<style>
    .brmedia-tracklist-pro ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .brmedia-tracklist-pro li {
        display: flex;
        align-items: center;
        padding: 10px;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        transition: background 0.3s ease;
        cursor: pointer;
    }
    .brmedia-tracklist-pro li:hover {
        background: #e9ecef;
    }
    .timestamp {
        width: 60px;
        font-weight: bold;
        color: #007bff;
    }
    .title {
        flex: 1;
        margin-left: 10px;
    }
    .artist {
        color: #666;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tracklist = document.querySelector('.brmedia-tracklist-pro');
    const player = document.querySelector('.brmedia-player[data-media-id="' + tracklist.dataset.mediaId + '"] .plyr');
    if (player) {
        const plyr = Plyr.setup(player)[0];
        tracklist.querySelectorAll('li').forEach(item => {
            item.addEventListener('click', function() {
                const timestamp = this.dataset.timestamp.split(':');
                const seconds = parseInt(timestamp[0]) * 60 + parseInt(timestamp[1]);
                plyr.currentTime = seconds;
                plyr.play();
            });
        });
    }
});
</script>