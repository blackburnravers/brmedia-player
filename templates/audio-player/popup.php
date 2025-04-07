<?php
/*
Template Name: Popup Music Player Template
Description: An advanced template for displaying a music player with a playlist in a styled popup.
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$options = get_option('brmedia_player_settings');

// Fetch all music posts for the playlist
$query = new WP_Query(array(
    'post_type' => 'brmusic',
    'posts_per_page' => -1
));

?>
<div class="brmedia-popup-overlay" id="brmedia-popup-overlay" style="background-color: <?php echo esc_attr($options['popup_overlay_bg']); ?>;">
    <div class="brmedia-popup" id="brmedia-popup" style="background-color: <?php echo esc_attr($options['popup_bg']); ?>; padding: <?php echo esc_attr($options['popup_padding']); ?>; margin: <?php echo esc_attr($options['popup_margin']); ?>; border: <?php echo esc_attr($options['popup_border']); ?>;">
        <button id="brmedia-popup-close" class="popup-close-button"><img src="<?php echo esc_url($options['popup_close_img']); ?>" alt="Close"></button>
        <div class="brmedia-music-player advanced-playlist" style="background-color: <?php echo esc_attr($options['bg_color']); ?>; padding: <?php echo esc_attr($options['padding']); ?>; margin: <?php echo esc_attr($options['margin']); ?>; border: <?php echo esc_attr($options['border']); ?>;">
            <div class="player-controls" style="color: <?php echo esc_attr($options['text_color']); ?>;">
                <button id="play-pause" class="control-button"><i class="<?php echo esc_attr($options['icon_play']); ?>"></i></button>
                <button id="mute-unmute" class="control-button"><i class="<?php echo esc_attr($options['icon_mute']); ?>"></i></button>
                <button id="prev-track" class="control-button"><i class="fas fa-backward"></i></button>
                <button id="next-track" class="control-button"><i class="fas fa-forward"></i></button>
            </div>
            <div id="waveform-wrapper" class="waveform-wrapper"></div>
            <div class="playlist">
                <ul id="playlist">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php
                        $post_id = get_the_ID();
                        $music_file = get_post_meta($post_id, '_brmusic_file', true);
                        $music_url = get_post_meta($post_id, '_brmusic_url', true);
                        $audio_source = $music_file ? $music_file : $music_url;
                        ?>
                        <li data-audio-source="<?php echo esc_url($audio_source); ?>" class="playlist-item">
                            <h3 style="color: <?php echo esc_attr($options['text_color']); ?>;"><?php the_title(); ?></h3>
                            <p style="color: <?php echo esc_attr($options['text_color']); ?>;"><?php echo esc_html(get_post_meta($post_id, '_brmusic_artist', true)); ?></p>
                        </li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var currentTrack = 0;
    var playlistItems = document.querySelectorAll('.playlist-item');
    var wavesurfer = WaveSurfer.create({
        container: "#waveform-wrapper",
        waveColor: "<?php echo esc_attr($options['wave_color']); ?>",
        progressColor: "<?php echo esc_attr($options['progress_color']); ?>"
    });

    function loadTrack(index) {
        var source = playlistItems[index].getAttribute('data-audio-source');
        wavesurfer.load(source);
        document.querySelectorAll('.playlist-item').forEach(function(item) {
            item.classList.remove('active');
        });
        playlistItems[index].classList.add('active');
    }

    document.getElementById("play-pause").addEventListener("click", function() {
        if (wavesurfer.isPlaying()) {
            wavesurfer.pause();
            this.innerHTML = '<i class="<?php echo esc_attr($options['icon_play']); ?>"></i>';
        } else {
            wavesurfer.play();
            this.innerHTML = '<i class="<?php echo esc_attr($options['icon_pause']); ?>"></i>';
        }
    });

    document.getElementById("mute-unmute").addEventListener("click", function() {
        if (wavesurfer.getMute()) {
            wavesurfer.setMute(false);
            this.innerHTML = '<i class="<?php echo esc_attr($options['icon_mute']); ?>"></i>';
        } else {
            wavesurfer.setMute(true);
            this.innerHTML = '<i class="<?php echo esc_attr($options['icon_unmute']); ?>"></i>';
        }
    });

    document.getElementById("prev-track").addEventListener("click", function() {
        if (currentTrack > 0) {
            currentTrack--;
            loadTrack(currentTrack);
        } else {
            currentTrack = playlistItems.length - 1;
            loadTrack(currentTrack);
        }
    });

    document.getElementById("next-track").addEventListener("click", function() {
        if (currentTrack < playlistItems.length - 1) {
            currentTrack++;
            loadTrack(currentTrack);
        } else {
            currentTrack = 0;
            loadTrack(currentTrack);
        }
    });

    playlistItems.forEach(function(item, index) {
        item.addEventListener("click", function() {
            currentTrack = index;
            loadTrack(currentTrack);
        });
    });

    loadTrack(currentTrack);

    document.getElementById("brmedia-popup-close").addEventListener("click", function() {
        document.getElementById("brmedia-popup-overlay").style.display = "none";
    });

    // Open the popup
    document.getElementById("brmedia-popup-overlay").style.display = "flex";
});
</script>
<style>
.brmedia-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: none;
    align-items: center;
    justify-content: center;
}

.brmedia-popup {
    position: relative;
    width: 90%;
    max-width: 800px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
}

.popup-close-button {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    cursor: pointer;
}

.brmedia-music-player.advanced-playlist {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.player-controls {
    margin-bottom: 10px;
}

.player-controls .control-button {
    background: none;
    border: none;
    font-size: 24px;
    margin: 0 5px;
    cursor: pointer;
}

.waveform-wrapper {
    width: 80%;
    height: 100px;
    margin-bottom: 20px;
}

.playlist {
    width: 100%;
    max-width: 600px;
}

.playlist ul {
    list-style: none;
    padding: 0;
}

.playlist-item {
    padding: 10px;
    border-bottom: 1px solid <?php echo esc_attr($options['border_color']); ?>;
    cursor: pointer;
}

.playlist-item.active {
    background-color: <?php echo esc_attr($options['active_bg_color']); ?>;
}
</style>