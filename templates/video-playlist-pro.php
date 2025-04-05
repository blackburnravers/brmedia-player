<?php
/**
 * Template: Video Playlist Pro
 *
 * Renders an advanced video playlist with drag-and-drop functionality.
 *
 * @package BRMedia\Templates
 */

// Ensure playlist data is set
if (!isset($playlist) || !is_array($playlist)) {
    echo '<p>Playlist data not provided.</p>';
    return;
}
?>

<div class="brmedia-player brmedia-video-playlist-pro">
    <div class="playlist-items">
        <?php foreach ($playlist as $video): ?>
            <div class="video" data-video-id="<?php echo esc_attr($video['id']); ?>">
                <img src="<?php echo esc_url($video['thumbnail']); ?>" alt="Thumbnail">
                <div class="video-info">
                    <h3><?php echo esc_html($video['title']); ?></h3>
                    <p><?php echo esc_html($video['duration']); ?></p>
                </div>
                <button class="play-btn">Play</button>
            </div>
        <?php endforeach; ?>
    </div>
    <video class="plyr" controls>
        <source src="" type="video/mp4">
    </video>
</div>

<style>
    .brmedia-video-playlist-pro {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .playlist-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    .video {
        background: #fff;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        cursor: grab;
    }
    .video img {
        width: 100%;
        border-radius: 8px;
    }
    .video-info {
        margin-top: 10px;
    }
    .play-btn {
        margin-top: 10px;
        padding: 5px 10px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const playlist = document.querySelector('.brmedia-video-playlist-pro .playlist-items');
    Sortable.create(playlist, {
        animation: 150,
        ghostClass: 'sortable-ghost',
    });

    const plyr = new Plyr('.brmedia-video-playlist-pro .plyr');
    document.querySelectorAll('.play-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const videoId = this.parentElement.dataset.videoId;
            plyr.source = {
                type: 'video',
                sources: [{ src: `/videos/${videoId}.mp4`, type: 'video/mp4' }],
            };
            plyr.play();
        });
    });
});
</script>