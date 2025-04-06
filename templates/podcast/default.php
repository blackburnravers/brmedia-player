<?php
/**
 * Template Name: Podcast - Default
 * Description: A default template for podcast episodes in BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve podcast data from post meta or shortcode attributes
$audio_file = get_post_meta( get_the_ID(), '_brmedia_podcast_audio', true );
$title = get_the_title();
$description = get_the_excerpt();
$host = get_post_meta( get_the_ID(), '_brmedia_podcast_host', true );
$transcription = get_post_meta( get_the_ID(), '_brmedia_podcast_transcription', true );
?>

<div class="brmedia-podcast-player default">
    <div class="brmedia-podcast-header">
        <h2><?php echo esc_html( $title ); ?></h2>
        <p>Hosted by <?php echo esc_html( $host ); ?></p>
    </div>
    <div class="brmedia-podcast-audio">
        <audio id="brmedia-audio-<?php echo get_the_ID(); ?>" controls>
            <source src="<?php echo esc_url( $audio_file ); ?>" type="audio/mpeg">
        </audio>
    </div>
    <div class="brmedia-podcast-description">
        <p><?php echo esc_html( $description ); ?></p>
    </div>
    <?php if ( $transcription ) : ?>
        <div class="brmedia-podcast-transcription">
            <h3>Transcription</h3>
            <p><?php echo esc_html( $transcription ); ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const player = new Plyr('#brmedia-audio-<?php echo get_the_ID(); ?>', {
            controls: ['play', 'progress', 'current-time', 'duration', 'volume'],
        });
    });
</script>