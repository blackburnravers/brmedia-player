<?php
/**
 * BRMedia Footer Class
 *
 * Manages a basic footer player.
 *
 * @package BRMedia\Includes\Frontend
 */

namespace BRMedia\Includes\Frontend;

class BRMedia_Footer {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_footer', [$this, 'render_footer_player']);
    }

    /**
     * Renders the footer player
     */
    public function render_footer_player() {
        ?>
        <div class="brmedia-footer">
            <div class="plyr">
                <audio controls>
                    <source src="" type="audio/mp3">
                </audio>
            </div>
        </div>
        <?php
    }
}