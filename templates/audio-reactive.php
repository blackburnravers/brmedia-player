<?php
/**
 * Template: Audio Reactive
 *
 * Renders an audio player with reactive waveform visualization.
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

<div class="brmedia-player brmedia-audio-reactive" data-media-id="<?php echo esc_attr($media_id); ?>">
    <audio class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
    </audio>
    <canvas class="reactive-waveform" width="600" height="100"></canvas>
</div>

<style>
    .brmedia-audio-reactive {
        position: relative;
        background: #1a1a1a;
        border-radius: 10px;
        padding: 20px;
        overflow: hidden;
    }
    .reactive-waveform {
        width: 100%;
        height: 100px;
        background: #222;
        margin-top: 10px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const player = document.querySelector('.brmedia-audio-reactive .plyr');
    const canvas = document.querySelector('.reactive-waveform');
    const ctx = canvas.getContext('2d');
    const audioContext = new AudioContext();
    const analyser = audioContext.createAnalyser();
    analyser.fftSize = 256;
    const bufferLength = analyser.frequencyBinCount;
    const dataArray = new Uint8Array(bufferLength);

    const source = audioContext.createMediaElementSource(player);
    source.connect(analyser);
    analyser.connect(audioContext.destination);

    function drawWaveform() {
        requestAnimationFrame(drawWaveform);
        analyser.getByteFrequencyData(dataArray);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const barWidth = (canvas.width / bufferLength) * 2.5;
        let x = 0;
        for (let i = 0; i < bufferLength; i++) {
            const barHeight = (dataArray[i] / 255) * canvas.height;
            ctx.fillStyle = `hsl(${i * 2}, 100%, 50%)`;
            ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
            x += barWidth + 1;
        }
    }
    drawWaveform();
});
</script>