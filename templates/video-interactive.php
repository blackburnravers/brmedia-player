<?php
/**
 * Template: Video Interactive
 *
 * Renders an interactive video player with hotspots.
 *
 * @package BRMedia\Templates
 */

// Ensure media ID is set
if (!isset($media_id)) {
    echo '<p>Media ID not provided.</p>';
    return;
}

// Get video URL
$url = get_post_meta($media_id, '_brmedia_video_url', true);
if (empty($url)) {
    echo '<p>Video URL not found.</p>';
    return;
}

// Example hotspots (could be stored as post meta or passed as params)
$hotspots = [
    ['x' => 20, 'y' => 30, 'text' => 'Hotspot 1', 'action' => 'alert("Hotspot 1 clicked");'],
    ['x' => 50, 'y' => 50, 'text' => 'Hotspot 2', 'action' => 'alert("Hotspot 2 clicked");'],
];
?>

<div class="brmedia-player brmedia-video-interactive" data-media-id="<?php echo esc_attr($media_id); ?>">
    <video class="plyr" controls>
        <source src="<?php echo esc_url($url); ?>" type="video/mp4">
    </video>
    <?php foreach ($hotspots as $hotspot): ?>
        <div class="hotspot" style="left: <?php echo esc_attr($hotspot['x']); ?>%; top: <?php echo esc_attr($hotspot['y']); ?>%;" data-tooltip="<?php echo esc_attr($hotspot['text']); ?>" onclick="<?php echo esc_js($hotspot['action']); ?>"></div>
    <?php endforeach; ?>
</div>

<style>
    .brmedia-video-interactive {
        position: relative;
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        background: #000;
        overflow: hidden;
    }
    .hotspot {
        position: absolute;
        width: 20px;
        height: 20px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .hotspot:hover {
        transform: scale(1.2);
    }
    .hotspot::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 25px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    .hotspot:hover::after {
        opacity: 1;
    }
</style>