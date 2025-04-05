<?php
/**
 * Template: Tracklist
 *
 * Renders a basic tracklist.
 *
 * @package BRMedia\Templates
 */

// Ensure tracklist data is set
if (!isset($tracklist) || !is_array($tracklist)) {
    echo '<p>Tracklist data not provided.</p>';
    return;
}
?>

<div class="brmedia-tracklist">
    <ul>
        <?php foreach ($tracklist as $track): ?>
            <li>
                <?php echo esc_html($track['title']); ?> - <?php echo esc_html($track['artist']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<style>
    .brmedia-tracklist ul {
        list-style: none;
        padding: 0;
    }
    .brmedia-tracklist li {
        padding: 5px 0;
        border-bottom: 1px solid #ddd;
    }
</style>