<?php
/**
 * Template: Audio Playlist Pro
 *
 * Renders an advanced audio playlist with drag-and-drop functionality.
 *
 * @package BRMedia\Templates
 */

// Ensure playlist data is set
if (!isset($playlist)) {
    echo '<p>Playlist data not provided.</p>';
    return;
}
?>

<div class="brmedia-player brmedia-audio-playlist-pro">
    <div class="playlist-items">
        <?php foreach ($playlist as $track): ?>
            <div class="track" data-track-id="<?php echo esc_attr($track['id']); ?>">
                <img src="<?php echo esc_url($track['thumbnail']); ?>" alt="Thumbnail">
                <div class="track-info">
                    <h3><?php echo esc_html($track['title']); ?></h3>
                    <p><?php echo esc_html($track['artist']); ?></p>
                </div>
                <button class="play-btn">Play</button>
            </div>
        <?php endforeach; ?>
    </div>
    <audio class="plyr" controls>
        <source src="" type="audio/mp3">
    </audio>
</div>

<style>
    .brmedia-audio-playlist-pro {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .playlist-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }
    .track {
        background: #fff;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        cursor: grab;
    }
    .track img {
        width: 100%;
        border-radius: 8px;
    }
    .track-info {
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
    const playlist = document.querySelector('.brmedia-audio-playlist-pro .playlist-items');
    Sortable.create(playlist, {
        animation: 150,
        ghostClass: 'sortable-ghost',
    });

    const plyr = new Plyr('.brmedia-audio-playlist-pro .plyr');
    document.querySelectorAll('.play-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const trackId = this.parentElement.dataset.trackId;
            plyr.source = {
                type: 'audio',
                sources: [{ src: `/audio/${trackId}.mp3`, type: 'audio/mp3' }],
            };
            plyr.play();
        });
    });
});
</script>