<?php
/**
 * Playlist Audio Player Template
 * An audio player with a playlist, allowing track selection and playback.
 *
 * @package BRMediaPlayer
 * @subpackage Templates
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Retrieve template settings from ACP
$settings = get_option('brmedia_template_settings', []);
$audio_settings = $settings['audio'] ?? [];
$primary_color = $audio_settings['primary_color'] ?? '#0073aa';
$secondary_color = $audio_settings['secondary_color'] ?? '#ffffff';
$player_width = $audio_settings['player_width'] ?? '100%';
$player_height = $audio_settings['player_height'] ?? '300px';
$font_family = $audio_settings['font_family'] ?? 'Arial, sans-serif';
$border_radius = $audio_settings['border_radius'] ?? '5px';
$show_track_info = $audio_settings['show_track_info'] ?? true;

// Playlist data (example; replace with dynamic data)
$playlist = [
    ['id' => 1, 'title' => 'Track 1', 'url' => 'https://example.com/track1.mp3', 'duration' => '3:45'],
    ['id' => 2, 'title' => 'Track 2', 'url' => 'https://example.com/track2.mp3', 'duration' => '4:20'],
    ['id' => 3, 'title' => 'Track 3', 'url' => 'https://example.com/track3.mp3', 'duration' => '2:55'],
];

// Enqueue external resources
wp_enqueue_script('plyr', 'https://cdn.plyr.io/3.6.8/plyr.min.js', [], '3.6.8', true);
wp_enqueue_style('plyr', 'https://cdn.plyr.io/3.6.8/plyr.css', [], '3.6.8');
wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

?>
<div class="brmedia-audio-player playlist-template" 
     style="width: <?php echo esc_attr($player_width); ?>; 
            height: <?php echo esc_attr($player_height); ?>; 
            background-color: <?php echo esc_attr($primary_color); ?>; 
            color: <?php echo esc_attr($secondary_color); ?>; 
            font-family: <?php echo esc_attr($font_family); ?>; 
            padding: 20px; 
            border-radius: <?php echo esc_attr($border_radius); ?>; 
            overflow-y: auto;">
    <div class="playlist-controls" style="margin-bottom: 20px;">
        <button class="play-pause-btn" 
                style="background: none; border: none; color: <?php echo esc_attr($secondary_color); ?>; font-size: 24px; cursor: pointer;">
            <i class="fas fa-play"></i>
        </button>
        <button class="next-btn" 
                style="background: none; border: none; color: <?php echo esc_attr($secondary_color); ?>; font-size: 24px; cursor: pointer; margin-left: 10px;">
            <i class="fas fa-forward"></i>
        </button>
    </div>
    <div class="playlist-tracks">
        <?php foreach ($playlist as $track): ?>
            <div class="track" data-url="<?php echo esc_url($track['url']); ?>" style="display: flex; align-items: center; margin-bottom: 10px; cursor: pointer;">
                <button class="track-play-btn" 
                        style="background: none; border: none; color: <?php echo esc_attr($secondary_color); ?>; font-size: 18px; cursor: pointer;">
                    <i class="fas fa-play"></i>
                </button>
                <span class="track-title" style="margin-left: 10px;"><?php echo esc_html($track['title']); ?></span>
                <?php if ($show_track_info): ?>
                    <span class="track-duration" style="margin-left: auto;"><?php echo esc_html($track['duration']); ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <audio id="audio-player" style="display: none;">
        <source src="" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const audio = document.getElementById('audio-player');
        const playPauseBtn = document.querySelector('.playlist-template .play-pause-btn');
        const nextBtn = document.querySelector('.playlist-template .next-btn');
        const tracks = document.querySelectorAll('.playlist-template .track');
        let currentTrack = 0;
        let isPlaying = false;

        function loadTrack(index) {
            const track = tracks[index];
            audio.src = track.dataset.url;
            audio.load();
            if (isPlaying) audio.play();
            highlightTrack(index);
        }

        function highlightTrack(index) {
            tracks.forEach((t, i) => {
                t.classList.toggle('active', i === index);
            });
        }

        playPauseBtn.addEventListener('click', function() {
            if (isPlaying) {
                audio.pause();
                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            } else {
                audio.play();
                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            }
            isPlaying = !isPlaying;
        });

        nextBtn.addEventListener('click', function() {
            currentTrack = (currentTrack + 1) % tracks.length;
            loadTrack(currentTrack);
        });

        tracks.forEach((track, index) => {
            track.addEventListener('click', function() {
                currentTrack = index;
                loadTrack(currentTrack);
            });
        });

        // Initial load
        loadTrack(currentTrack);
    });
</script>

<style>
    .playlist-template {
        transition: box-shadow 0.3s ease;
    }
    .playlist-template:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .track:hover {
        background-color: rgba(255,255,255,0.1);
    }
    .track.active {
        background-color: rgba(255,255,255,0.2);
    }
    .playlist-controls button {
        transition: color 0.2s ease, transform 0.2s ease;
    }
    .playlist-controls button:hover {
        color: #ccc;
        transform: scale(1.1);
    }
</style>