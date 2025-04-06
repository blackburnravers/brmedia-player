<?php
/**
 * Template Name: Radio Player
 * Description: Displays the radio player with live streaming, now playing information, and visualizer.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve stream URL, logo, and other settings
$stream_url = $args['stream_url'] ?? '';
$logo_url = $args['logo_url'] ?? '';
$player_id = $args['player_id'] ?? 'radio-player';
?>

<div class="brmedia-radio-player" id="<?php echo esc_attr( $player_id ); ?>">
    <?php if ( $logo_url ) : ?>
        <img src="<?php echo esc_url( $logo_url ); ?>" alt="Station Logo" class="brmedia-station-logo">
    <?php endif; ?>
    <audio id="brmedia-audio-<?php echo esc_attr( $player_id ); ?>">
        <source src="<?php echo esc_url( $stream_url ); ?>" type="audio/mpeg">
    </audio>
    <div class="brmedia-now-playing">
        <p>Now Playing: <span id="now-playing-text">Loading...</span></p>
    </div>
    <canvas id="visualizer" width="300" height="100"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const player = new Plyr('#brmedia-audio-<?php echo esc_js( $player_id ); ?>', {
        controls: ['play', 'volume'],
        live: true,
    });

    // Visualizer setup
    const audio = document.getElementById('brmedia-audio-<?php echo esc_js( $player_id ); ?>');
    const canvas = document.getElementById('visualizer');
    const ctx = canvas.getContext('2d');
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const analyser = audioCtx.createAnalyser();
    const source = audioCtx.createMediaElementSource(audio);
    source.connect(analyser);
    analyser.connect(audioCtx.destination);
    analyser.fftSize = 256;
    const bufferLength = analyser.frequencyBinCount;
    const dataArray = new Uint8Array(bufferLength);

    function draw() {
        requestAnimationFrame(draw);
        analyser.getByteFrequencyData(dataArray);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const barWidth = (canvas.width / bufferLength) * 2.5;
        let x = 0;
        for (let i = 0; i < bufferLength; i++) {
            const barHeight = dataArray[i] / 2;
            ctx.fillStyle = 'rgb(' + (barHeight + 100) + ',50,50)';
            ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
            x += barWidth + 1;
        }
    }
    draw();

    // Fetch now playing information every 10 seconds
    setInterval(function() {
        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>?action=brmedia_now_playing')
            .then(response => response.json())
            .then(data => {
                document.getElementById('now-playing-text').textContent = data.now_playing;
            });
    }, 10000);
});
</script>