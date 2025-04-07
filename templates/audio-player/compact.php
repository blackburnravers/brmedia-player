<?php
/*
Template Name: Compact Music Player Template
Description: A compact template for displaying the music player.
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
<div class="brmedia-music-player compact" id="waveform-<?php echo esc_attr($post_id); ?>" data-audio-source="<?php echo esc_url($audio_source); ?>" style="background-color: <?php echo esc_attr($options['bg_color']); ?>; padding: <?php echo esc_attr($options['padding']); ?>; margin: <?php echo esc_attr($options['margin']); ?>; border: <?php echo esc_attr($options['border']); ?>;">
    <button id="play-pause-<?php echo esc_attr($post_id); ?>" style="color: <?php echo esc_attr($options['text_color']); ?>;"><i class="<?php echo esc_attr($options['icon_play']); ?>"></i></button>
    <h2 style="color: <?php echo esc_attr($options['text_color']); ?>;"><?php echo esc_html(get_the_title($post_id)); ?></h2>
    <p style="color: <?php echo esc_attr($options['text_color']); ?>;"><?php echo esc_html(get_post_meta($post_id, '_brmusic_artist', true)); ?></p>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var wavesurfer = WaveSurfer.create({
        container: "#waveform-<?php echo esc_attr($post_id); ?>",
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
});
</script>