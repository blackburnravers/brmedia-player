<?php
/**
 * Template Name: Video Player - Playlist
 * Description: A playlist video player template for BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve playlist data from post meta (array of videos)
$playlist = get_post_meta( get_the_ID(), '_brmedia_video_playlist', true );
if ( ! is_array( $playlist ) ) {
    $playlist = array();
}
?>

<div class="brmedia-video-player playlist">
    <video id="brmedia-video-playlist" playsinline controls></video>
    <div class="brmedia-playlist">
        <ul>
            <?php foreach ( $playlist as $index => $video ) : 
                $video_id = $video['id'];
                $video_title = $video['title'];
                $video_file = $video['file'];
            ?>
                <li data-index="<?php echo $index; ?>" data-file="<?php echo esc_url( $video_file ); ?>">
                    <span class="brmedia-video-title"><?php echo esc_html( $video_title ); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const playlistItems = document.querySelectorAll('.brmedia-playlist li');
        let currentVideo = 0;

        // Initialize Plyr
        const player = new Plyr('#brmedia-video-playlist', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
        });

        // Load and play a video
        function loadVideo(index) {
            const video = playlistItems[index];
            const file = video.getAttribute('data-file');
            player.source = {
                type: 'video',
                sources: [{ src: file, type: 'video/mp4' }],
            };
            player.play();
        }

        // Load initial video
        loadVideo(currentVideo);

        // Video selection
        playlistItems.forEach(item => {
            item.addEventListener('click', function() {
                currentVideo = parseInt(this.getAttribute('data-index'));
                loadVideo(currentVideo);
            });
        });

        // Auto-play next video
        player.on('ended', function() {
            currentVideo = (currentVideo + 1) % playlistItems.length;
            loadVideo(currentVideo);
        });
    });
</script>