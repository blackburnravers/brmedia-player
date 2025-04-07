<?php
/**
 * Podcasts Templates
 * Advanced rendering of podcast players with waveforms and episode lists.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Podcast player template with waveform
function brmedia_podcast_player_template($episode_id, $show_waveform = true) {
    $audio_url = get_post_meta($episode_id, 'brmedia_podcast_audio', true);
    $title = get_the_title($episode_id);
    $duration = get_post_meta($episode_id, 'brmedia_podcast_duration', true) ?: 'Unknown';

    if (!$audio_url || get_post_type($episode_id) !== 'brmedia_podcast') {
        return '<p>Error: Invalid podcast episode</p>';
    }

    ob_start();
    ?>
    <div class="brmedia-podcast-player" data-episode-id="<?php echo esc_attr($episode_id); ?>">
        <h3><?php echo esc_html($title); ?></h3>
        <p>Duration: <?php echo esc_html($duration); ?></p>
        <?php if ($show_waveform) : ?>
            <div id="waveform-<?php echo esc_attr($episode_id); ?>" class="brmedia-waveform"></div>
            <button class="brmedia-play-btn" data-episode-id="<?php echo esc_attr($episode_id); ?>">
                <i class="fas fa-play"></i> Play
            </button>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const wavesurfer = WaveSurfer.create({
                        container: '#waveform-<?php echo esc_attr($episode_id); ?>',
                        waveColor: '#0073aa',
                        progressColor: '#005d82',
                        height: 100,
                        barWidth: 2,
                        responsive: true
                    });
                    wavesurfer.load('<?php echo esc_url($audio_url); ?>');
                    document.querySelector('.brmedia-play-btn[data-episode-id="<?php echo esc_attr($episode_id); ?>"]').addEventListener('click', function() {
                        wavesurfer.playPause();
                        const isPlaying = wavesurfer.isPlaying();
                        this.innerHTML = isPlaying ? '<i class="fas fa-pause"></i> Pause' : '<i class="fas fa-play"></i> Play';
                        jQuery.post(brmedia_podcasts.ajax_url, {
                            action: 'brmedia_podcast_action',
                            episode_id: <?php echo esc_attr($episode_id); ?>,
                            action_type: isPlaying ? 'play' : 'pause',
                            nonce: brmedia_podcasts.nonce
                        });
                    });
                });
            </script>
        <?php else : ?>
            <audio controls>
                <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Podcast episode list template
function brmedia_podcast_list_template($series_id, $limit = 10) {
    $args = [
        'post_type' => 'brmedia_podcast',
        'tax_query' => [
            [
                'taxonomy' => 'brmedia_podcast_series',
                'field' => 'term_id',
                'terms' => $series_id,
            ],
        ],
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
    ];
    $episodes = new WP_Query($args);

    if (!$episodes->have_posts()) {
        return '<p>No episodes found for this series.</p>';
    }

    ob_start();
    ?>
    <div class="brmedia-podcast-list">
        <h2><?php echo esc_html(get_term($series_id, 'brmedia_podcast_series')->name); ?></h2>
        <ul>
            <?php while ($episodes->have_posts()) : $episodes->the_post(); ?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    <p><?php the_excerpt(); ?></p>
                    <span>Duration: <?php echo esc_html(get_post_meta(get_the_ID(), 'brmedia_podcast_duration', true) ?: 'Unknown'); ?></span>
                </li>
            <?php endwhile; wp_reset_postdata(); ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}