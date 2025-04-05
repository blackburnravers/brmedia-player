<?php
/**
 * Template: Audio Playlist
 *
 * Renders a basic audio playlist with sequential playback.
 *
 * @package BRMedia\Templates
 */

// Ensure playlist data is set
if (!isset($playlist)) {
    echo '<p>Playlist data not provided.</p>';
    return;
}
?>

<div class="brmedia-player brmedia-audio-playlist">
    <ul class="playlist-items">
        <?php foreach ($playlist as $track): ?>
            <li data-track-id="<?php echo esc_attr($track['id']); ?>">
                <?php echo esc_html($track['title']); ?> - <?php echo esc_html($track['artist']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <audio class="plyr" controls>
        <source src="" type="audio/mp3">
    </audio>
</div>

<style>
    .brmedia-audio-playlist {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .playlist-items {
        list-style: none;
        padding: 0;
    }
    .playlist-items li {
        cursor: pointer;
        padding: 5px;
        background: #f0f0f0;
        border-radius: 5px;
    }
    .playlist-items li:hover {
        background: #e0e0e0;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const plyr = new Plyr('.brmedia-audio-playlist .plyr');
    document.querySelectorAll('.playlist-items li').forEach(item => {
        item.addEventListener('click', function() {
            const trackId = this.dataset.trackId;
            plyr.source = {
                type: 'audio',
                sources: [{ src: `/audio/${trackId}.mp3`, type: 'audio/mp3' }],
            };
            plyr.play();
        });
    });
});
</script>