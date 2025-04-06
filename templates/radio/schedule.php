<?php
/**
 * Template Name: Radio Schedule
 * Description: Displays the radio schedule.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve schedule data
$schedule = $args['schedule'] ?? array();
?>

<div class="brmedia-radio-schedule">
    <h2>Radio Schedule</h2>
    <ul>
        <?php foreach ( $schedule as $show ) : ?>
            <li>
                <strong><?php echo esc_html( $show['time'] ); ?></strong> - <?php echo esc_html( $show['title'] ); ?>
                <p><?php echo esc_html( $show['description'] ); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</div>