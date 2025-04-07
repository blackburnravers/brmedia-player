<?php
/*
Template Name: Fullscreen Music Player Template
Description: A fullscreen template for displaying the music player.
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$options = get_option('brmedia_player_settings');

// Ensure $post_id is provided
if (empty($post_id)) {
    return '<p>' . __('No music ID provided', 'brmedia-player') . '</p>';
}

$post = get_post($post_id);

if (!$post || $post->post_type !== 'brmusic') {
    return '<p>' . __('Invalid music ID', 'brmedia-player') . '</p>';
}

$music_file = get_post_meta($post_id, '_brmusic_file', true);
$music_url = get_post_meta($post_id, '_brmusic_url', true);
$audio_source = $music_file ? $music_file : $music_url;

?>
<div class="brmedia-music-player fullscreen" id="waveform-<?php echo esc_attr($post_id); ?>" data-audio-source="<?php echo esc_url($audio_source); ?>" style="background-color: <?php echo esc_attr($options['bg_color']); ?>; padding: <?php echo esc_attr($options['padding']); ?>; margin: <?php echo esc_attr($options['margin']); ?>; border: <?php echo esc_attr($options['border']); ?>;">
    <div class="player-controls" style="color: <?php echo esc_attr($options['text_color']); ?>;">
        <button id="play-pause-<?php echo esc_attr($post_id); ?>" class="control-button"><i class="<?php echo esc_attr($options['icon_play']); ?>"></i></button>
        <button id="mute-unmute-<?php echo esc_attr($post_id); ?>" class="control-button"><i class="<?php echo esc_attr($options['icon_mute']); ?>"></i></button>
        <button id="fullscreen-exit-<?php echo esc_attr($post_id); ?>" class="control-button"><i class="fas fa-compress"></i></button>
    </div>
    <div class="player-info">
        <h2 style="color: <?php echo esc_attr($options['text_color']); ?>;"><?php echo esc_html(get_the_title($post_id)); ?></h2>
        <p style="color: <?php echo esc_attr($options['text_color']); ?>;"><?php echo esc_html(get_post_meta($post_id, '_brmusic_artist', true)); ?></p>
    </div>
    <div id="waveform-wrapper-<?php echo esc_attr($post_id); ?>" class="waveform-wrapper"></div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var wavesurfer = WaveSurfer.create({
        container: "#waveform-wrapper-<?php echo esc_attr($post_id); ?>",
        waveColor: "<?php echo esc_attr($options['wave_color']); ?>",
        progressColor: "<?php echo esc_attr($options['progress_color']); ?>"
    });

    wavesurfer.load("<?php echo esc_url($audio_source); ?>");

    document.getElementById("play-pause-<?php echo esc_attr($post_id); ?>").addEventListener("click", function() {
        if (wavesurfer.isPlaying()) {
            wavesurfer.pause();
            this.innerHTML = '<i class="<?php echo esc_attr($options['icon_play']); ?>"></i>';
        } else {
            wavesurfer.play();
            this.innerHTML = '<i class="<?php echo esc_attr($options['icon_pause']); ?>"></i>';
        }
    });

    document.getElementById("mute-unmute-<?php echo esc_attr($post_id); ?>").addEventListener("click", function() {
        if (wavesurfer.getMute()) {
            wavesurfer.setMute(false);
            this.innerHTML = '<i class="<?php echo esc_attr($options['icon_mute']); ?>"></i>';
        } else {
            wavesurfer.setMute(true);
            this.innerHTML = '<i class="<?php echo esc_attr($options['icon_unmute']); ?>"></i>';
        }
    });

    document.getElementById("fullscreen-exit-<?php echo esc_attr($post_id); ?>").addEventListener("click", function() {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            document.documentElement.requestFullscreen();
        }
    });
});
</script>
<style>
.brmedia-music-player.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.player-controls {
    position: absolute;
    top: 10px;
    right: 10px;
}
.player-controls .control-button {
    background: none;
    border: none;
    font-size: 24px;
    margin: 0 5px;
    cursor: pointer;
}
.player-info {
    position: absolute;
    bottom: 10px;
    text-align: center;
}
.waveform-wrapper {
    width: 80%;
    height: 100px;
}
</style>