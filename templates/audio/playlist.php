<?php
/**
 * Template Name: Playlist Audio Player
 * Description: A playlist audio player template for BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve playlist data from post meta (array of tracks)
$playlist = get_post_meta( get_the_ID(), '_brmedia_playlist', true );
if ( ! is_array( $playlist ) ) {
    $playlist = array();
}
?>

<div class="brmedia-audio-player brmedia-playlist-player">
    <div class="brmedia-player-controls">
        <button class="brmedia-play-pause" aria-label="Play/Pause">
            <i class="fas fa-play"></i>
            <i class="fas fa-pause" style="display: none;"></i>
        </button>
        <div class="brmedia-progress">
            <input type="range" class="brmedia-seekbar" value="0" min="0" max="100">
        </div>
        <div class="brmedia-time">
            <span class="brmedia-current-time">00:00</span> / <span class="brmedia-duration">00:00</span>
        </div>
        <button class="brmedia-volume" aria-label="Volume">
            <i class="fas fa-volume-up"></i>
        </button>
        <button class="brmedia-next" aria-label="Next Track">
            <i class="fas fa-forward"></i>
        </button>
        <button class="brmedia-prev" aria-label="Previous Track">
            <i class="fas fa-backward"></i>
        </button>
    </div>
    <div class="brmedia-playlist">
        <ul>
            <?php foreach ( $playlist as $index => $track ) : 
                $track_id = $track['id'];
                $track_title = $track['title'];
                $track_artist = $track['artist'];
                $track_file = $track['file'];
            ?>
                <li data-index="<?php echo $index; ?>" data-file="<?php echo esc_url( $track_file ); ?>">
                    <span class="brmedia-track-title"><?php echo esc_html( $track_title ); ?></span>
                    <span class="brmedia-track-artist"><?php echo esc_html( $track_artist ); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <audio id="brmedia-audio-playlist" preload="metadata">
        <source src="" type="audio/mpeg">
    </audio>
    <div class="brmedia-waveform" id="waveform-playlist"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const playlistItems = document.querySelectorAll('.brmedia-playlist li');
        let currentTrack = 0;

        // Initialize Plyr
        const player = new Plyr('#brmedia-audio-playlist', {
            controls: ['play', 'progress', 'current-time', 'duration', 'volume'],
            invertTime: false,
        });

        // Initialize Wavesurfer
        const wavesurfer = WaveSurfer.create({
            container: '#waveform-playlist',
            waveColor: '#ddd',
            progressColor: '#ff5500',
            cursorColor: '#333',
            barWidth: 2,
            height: 100,
            responsive: true,
        });

        // Load and play a track
        function loadTrack(index) {
            const track = playlistItems[index];
            const file = track.getAttribute('data-file');
            player.source = {
                type: 'audio',
                sources: [{ src: file, type: 'audio/mpeg' }],
            };
            wavesurfer.load(file);
            player.play();
        }

        // Load initial track
        loadTrack(currentTrack);

        // Track selection
        playlistItems.forEach(item => {
            item.addEventListener('click', function() {
                currentTrack = parseInt(this.getAttribute('data-index'));
                loadTrack(currentTrack);
            });
        });

        // Next/Previous controls
        document.querySelector('.brmedia-next').addEventListener('click', function() {
            currentTrack = (currentTrack + 1) % playlistItems.length;
            loadTrack(currentTrack);
        });
        document.querySelector('.brmedia-prev').addEventListener('click', function() {
            currentTrack = (currentTrack - 1 + playlistItems.length) % playlistItems.length;
            loadTrack(currentTrack);
        });

        // Auto-play next track
        player.on('ended', function() {
            currentTrack = (currentTrack + 1) % playlistItems.length;
            loadTrack(currentTrack);
        });
    });
</script>