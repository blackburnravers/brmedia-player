<?php
/**
 * Playlist Audio Player Template
 *
 * This template provides a playlist-style audio player with multiple tracks.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve playlist data (assuming a meta field for playlist tracks)
$playlist = get_post_meta($post->ID, 'brmedia_playlist', true);
if (!is_array($playlist)) {
    $playlist = array();
}
?>

<div class="brmedia-audio-player playlist">
    <div class="wavesurfer-container" data-playlist='<?php echo json_encode($playlist); ?>'></div>
    <div class="controls">
        <button class="play-pause"><i class="fas fa-play"></i></button>
        <button class="next"><i class="fas fa-forward"></i></button>
        <button class="prev"><i class="fas fa-backward"></i></button>
    </div>
    <div class="playlist-tracks">
        <ul>
            <?php foreach ($playlist as $index => $track) : ?>
                <li data-index="<?php echo $index; ?>"><?php echo esc_html($track['title']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    // Initialize WaveSurfer.js for playlist player
    document.addEventListener('DOMContentLoaded', function() {
        var container = document.querySelector('.brmedia-audio-player.playlist .wavesurfer-container');
        var playlist = JSON.parse(container.dataset.playlist);
        var currentIndex = 0;

        var wavesurfer = WaveSurfer.create({
            container: container,
            waveColor: '#0073aa',
            progressColor: '#005a87',
            cursorColor: '#333',
            height: 100,
            responsive: true
        });

        function loadTrack(index) {
            wavesurfer.load(playlist[index].url);
            wavesurfer.on('ready', function() {
                wavesurfer.play();
            });
        }

        loadTrack(currentIndex);

        // Play/Pause button
        var playPauseButton = container.nextElementSibling.querySelector('.play-pause');
        playPauseButton.addEventListener('click', function() {
            wavesurfer.playPause();
            this.querySelector('i').classList.toggle('fa-play');
            this.querySelector('i').classList.toggle('fa-pause');
        });

        // Next and Previous buttons
        container.nextElementSibling.querySelector('.next').addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % playlist.length;
            loadTrack(currentIndex);
        });
        container.nextElementSibling.querySelector('.prev').addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + playlist.length) % playlist.length;
            loadTrack(currentIndex);
        });

        // Playlist track selection
        document.querySelectorAll('.playlist-tracks li').forEach(function(li) {
            li.addEventListener('click', function() {
                currentIndex = parseInt(this.dataset.index);
                loadTrack(currentIndex);
            });
        });
    });
</script>