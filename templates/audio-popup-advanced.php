<?php
/**
 * Template: Audio Popup Advanced
 *
 * Renders an advanced popup audio player with animations and visualizations.
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

<button class="brmedia-popup-trigger" data-target="#popup-<?php echo esc_attr($media_id); ?>">Open Player</button>
<div id="popup-<?php echo esc_attr($media_id); ?>" class="brmedia-player brmedia-audio-popup-advanced">
    <audio class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
    </audio>
    <canvas class="popup-visualization" width="400" height="100"></canvas>
    <button class="close-btn">Close</button>
</div>

<style>
    .brmedia-audio-popup-advanced {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .brmedia-audio-popup-advanced.active {
        display: block;
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    .popup-visualization {
        margin-top: 10px;
        width: 100%;
        height: 100px;
        background: #f0f0f0;
    }
    .close-btn {
        margin-top: 10px;
        padding: 5px 10px;
        background: #ff4d4d;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.querySelector('.brmedia-popup-trigger');
    const popup = document.querySelector('.brmedia-audio-popup-advanced');
    const closeBtn = popup.querySelector('.close-btn');

    trigger.addEventListener('click', function() {
        popup.classList.add('active');
    });

    closeBtn.addEventListener('click', function() {
        popup.classList.remove('active');
    });

    const canvas = popup.querySelector('.popup-visualization');
    const ctx = canvas.getContext('2d');
    const audioContext = new AudioContext();
    const analyser = audioContext.createAnalyser();
    analyser.fftSize = 128;
    const bufferLength = analyser.frequencyBinCount;
    const dataArray = new Uint8Array(bufferLength);

    const source = audioContext.createMediaElementSource(popup.querySelector('.plyr'));
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
            ctx.fillStyle = `rgb(0, 123, 255)`;
            ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
            x += barWidth + 1;
        }
    }
    draw();
});
</script>