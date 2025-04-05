<?php
/**
 * Template: Audio Fullscreen Immersive
 *
 * Renders a fullscreen audio player with immersive visuals.
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

<div class="brmedia-player brmedia-audio-fullscreen-immersive" data-media-id="<?php echo esc_attr($media_id); ?>">
    <audio class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
    </audio>
    <canvas class="immersive-background" width="100%" height="100%"></canvas>
</div>

<style>
    .brmedia-audio-fullscreen-immersive {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: #000;
        z-index: 1000;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .immersive-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.querySelector('.brmedia-audio-fullscreen-immersive .immersive-background');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    function drawParticles() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (let i = 0; i < 100; i++) {
            const x = Math.random() * canvas.width;
            const y = Math.random() * canvas.height;
            const radius = Math.random() * 5;
            ctx.beginPath();
            ctx.arc(x, y, radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 255, 255, ${Math.random()})`;
            ctx.fill();
        }
        requestAnimationFrame(drawParticles);
    }
    drawParticles();
});
</script>