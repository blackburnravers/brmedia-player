<?php
/**
 * Radio Templates
 * Advanced rendering of radio players with live status and timetables.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Radio player template
function brmedia_radio_player_template($source = '') {
    $default_source = get_option('brmedia_radio_default_source', 'icecast');
    $source = !empty($source) && in_array($source, explode(',', get_option('brmedia_radio_sources', 'icecast,shoutcast,youtube'))) ? $source : $default_source;

    ob_start();
    ?>
    <div class="brmedia-radio-player" data-source="<?php echo esc_attr($source); ?>">
        <h3>Now Playing: <span class="brmedia-dj-name">Loading...</span></h3>
        <audio controls>
            <source src="" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
        <script>
            jQuery(document).ready(function($) {
                function updateRadioStatus() {
                    $.post(brmedia_radio.ajax_url, {
                        action: 'brmedia_radio_status',
                        nonce: brmedia_radio.nonce
                    }, function(response) {
                        if (response.success) {
                            $('.brmedia-dj-name').text(response.data.dj_name);
                            $('.brmedia-radio-player audio source').attr('src', response.data.stream_url);
                            $('.brmedia-radio-player audio')[0].load();
                        }
                    });
                }
                updateRadioStatus();
                setInterval(updateRadioStatus, 30000); // Update every 30 seconds
            });
        </script>
    </div>
    <?php
    return ob_get_clean();
}

// Radio timetable template
function brmedia_radio_timetable_template($show_future = true) {
    global $wpdb;
    $table = $wpdb->prefix . 'brmedia_radio_timetable';
    $current_time = current_time('mysql');
    $where = $show_future ? "WHERE end_time >= %s" : "WHERE start_time <= %s AND end_time >= %s";
    $query = $wpdb->prepare("SELECT * FROM $table $where ORDER BY start_time ASC", $show_future ? $current_time : [$current_time, $current_time]);
    $timetables = $wpdb->get_results($query, ARRAY_A);

    if (empty($timetables)) {
        return '<p>No scheduled DJs found.</p>';
    }

    ob_start();
    ?>
    <table class="brmedia-radio-timetable">
        <thead>
            <tr>
                <th>DJ Name</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timetables as $timetable) : ?>
                <tr>
                    <td><?php echo esc_html($timetable['dj_name']); ?></td>
                    <td><?php echo esc_html(date('Y-m-d H:i', strtotime($timetable['start_time']))); ?></td>
                    <td><?php echo esc_html(date('Y-m-d H:i', strtotime($timetable['end_time']))); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}