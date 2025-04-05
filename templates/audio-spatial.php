<?php
/**
 * Template: Audio Spatial
 *
 * Renders a spatial audio player with 3D effects and panning controls.
 *
 * @package BRMedia\Templates
 */

// Ensure media ID is set
if (!isset($media_id)) {
    echo '<p>Media ID not provided.</p>';
    return;
}

// Get media URL
$url = get_post_meta($media_id, '_brmedia_music_url', true);
if (empty($url)) {
    echo '<p>Audio URL not found.</p>';
    return;
}
?>

<div class="brmedia-player brmedia-audio-spatial" data-media-id="<?php echo esc_attr($media_id); ?>">
    <audio class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
    </audio>
    <input type="range" class="spatial-panner" min="-1" max="1" step="0.1" value="0">
</div>

<style>
    .brmedia-audio-spatial {
        perspective: 1000px;
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .brmedia-audio-spatial .plyr {
        transform-style: preserve-3d;
        transform: rotateY(15deg);
        transition: transform 0.3s ease;
    }
    .brmedia-audio-spatial:hover .plyr {
        transform: rotateY(0deg);
    }
    .spatial-panner {
        width: 100%;
        margin-top: 10px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const player = document.querySelector('.brmedia-audio-spatial .plyr');
    const panner = document.querySelector('.spatial-panner');
    const audioContext = new AudioContext();
    const source = audioContext.createMediaElementSource(player);
    const pannerNode = audioContext.createStereoPanner();
    source.connect(pannerNode);
    pannerNode.connect(audioContext.destination);

    panner.addEventListener('input', function() {
        pannerNode.pan.value = parseFloat(this.value);
    });
});
</script>