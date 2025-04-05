<?php
/**
 * Template: Audio AI-Enhanced
 *
 * Renders an AI-enhanced audio player with dynamic visualizations.
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

<div class="brmedia-player brmedia-audio-ai-enhanced" data-media-id="<?php echo esc_attr($media_id); ?>">
    <audio class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
    </audio>
    <canvas class="ai-visualization" width="800" height="200"></canvas>
</div>

<style>
    .brmedia-audio-ai-enhanced {
        position: relative;
        background: #000;
        border-radius: 10px;
        overflow: hidden;
    }
    .ai-visualization {
        width: 100%;
        height: 200px;
        background: #111;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const player = document.querySelector('.brmedia-audio-ai-enhanced .plyr');
    const canvas = document.querySelector('.brmedia-audio-ai-enhanced .ai-visualization');
    const ctx = canvas.getContext('2d');
    const audioContext = new AudioContext();
    const analyser = audioContext.createAnalyser();
    analyser.fftSize = 256;
    const bufferLength = analyser.frequencyBinCount;
    const dataArray = new Uint8Array(bufferLength);

    const source = audioContext.createMediaElementSource(player);
    source.connect(analyser);
    analyser.connect(audioContext.destination);

    function draw() {
        requestAnimationFrame(draw);
        analyser.getByteFrequencyData(dataArray);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const barWidth = (canvas.width / bufferLength) * 2.5;
        let x = 0;
        for (let i = 0; i < bufferLength; i++) {
            const barHeight = (dataArray[i] / 255) * canvas.height;
            ctx.fillStyle = `rgb(${barHeight + 100}, 50, 50)`;
            ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
            x += barWidth + 1;
        }
    }

    draw();
});
</script>