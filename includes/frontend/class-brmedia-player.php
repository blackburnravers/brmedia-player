<?php
/**
 * BRMedia Player Class
 *
 * Manages a basic media player.
 *
 * @package BRMedia\Includes\Frontend
 */

namespace BRMedia\Includes\Frontend;

class BRMedia_Player {
    /**
     * Renders the player with a given media ID
     *
     * @param int $media_id Media post ID
     * @return string Rendered HTML
     */
    public function render_player($media_id) {
        $url = get_post_meta($media_id, '_brmedia_music_url', true) ?: get_post_meta($media_id, '_brmedia_video_url', true);
        ob_start();
        ?>
        <div class="brmedia-player" data-media-id="<?php echo esc_attr($media_id); ?>">
            <?php if (wp_check_filetype($url)['type'] === 'video/mp4') : ?>
                <video class="plyr">
                    <source src="<?php echo esc_url($url); ?>" type="video/mp4">
                </video>
            <?php else : ?>
                <audio class="plyr">
                    <source src="<?php echo esc_url($url); ?>" type="audio/mp3">
                </audio>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}