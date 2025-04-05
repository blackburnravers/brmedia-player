<?php
/**
 * Template: Video Playlist
 *
 * Renders a basic video playlist with clickable items.
 *
 * @package BRMedia\Templates
 */

// Ensure playlist data is set
if (!isset($playlist) || !is_array($playlist)) {
    echo '<p>Playlist data not provided.</p>';
    return;
}
?>

<div class="brmedia-player brmedia-video-playlist">
    <ul class="playlist-items">
        <?php foreach ($playlist as $video): ?>
            <li data-video-id="<?php echo esc_attr($video['id']); ?>">
                <?php echo esc_html($video['title']); ?> (<?php echo esc_html($video['duration']); ?>)
            </li>
        <?php endforeach; ?>
    </ul>
    <video class="plyr" controls>
        <source src="" type="video/mp4">
    </video>
</div>

<style>
    .brmedia-video-playlist {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .playlist-items {
        list-style: none;
        padding: 0;
    }
    .playlist-items li {
        padding: 5px;
        background: #f0f0f0;
        border-radius: 5px;
        cursor: pointer;
    }
    .playlist-items li:hover {
        background: #e0e0e0;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const plyr = new Plyr('.brmedia-video-playlist .plyr');
    document.querySelectorAll('.playlist-items li').forEach(item => {
        item.addEventListener('click', function() {
            const videoId = this.dataset.videoId;
            plyr.source = {
                type: 'video',
                sources: [{ src: `/videos/${videoId}.mp4`, type: 'video/mp4' }],
            };
            plyr.play();
        });
    });
});
</script>