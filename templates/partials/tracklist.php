<?php
/**
 * Partial Template: Tracklist
 * Description: Displays a clickable tracklist with timestamps for seeking.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Default values
$tracklist = $args['tracklist'] ?? array();
$player_id = $args['player_id'] ?? 'player-' . uniqid();
?>

<div class="brmedia-tracklist">
    <ul>
        <?php foreach ( $tracklist as $track ) : 
            // Convert timestamp to seconds if necessary
            $time_in_seconds = isset( $track['time_seconds'] ) ? $track['time_seconds'] : convert_to_seconds( $track['time'] );
        ?>
            <li data-time="<?php echo esc_attr( $time_in_seconds ); ?>">
                <span class="brmedia-track-time"><?php echo esc_html( $track['time'] ); ?></span>
                <span class="brmedia-track-title"><?php echo esc_html( $track['title'] ); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const player = document.querySelector('#<?php echo esc_js( $player_id ); ?>');
    if (player && player.plyr) {
        const plyrPlayer = player.plyr;
        const tracklistItems = document.querySelectorAll('.brmedia-tracklist li');

        tracklistItems.forEach(function(item) {
            item.addEventListener('click', function() {
                const time = parseFloat(this.getAttribute('data-time'));
                plyrPlayer.currentTime = time;
                plyrPlayer.play();
            });
        });
    }
});

// Helper function to convert hh:mm:ss to seconds
function convert_to_seconds(time) {
    const parts = time.split(':');
    let seconds = 0;
    if (parts.length === 3) {
        seconds += parseInt(parts[0]) * 3600;
        seconds += parseInt(parts[1]) * 60;
        seconds += parseInt(parts[2]);
    } else if (parts.length === 2) {
        seconds += parseInt(parts[0]) * 60;
        seconds += parseInt(parts[1]);
    } else if (parts.length === 1) {
        seconds += parseInt(parts[0]);
    }
    return seconds;
}
</script>