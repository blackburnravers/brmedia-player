<?php
/**
 * Utility: Tracklist Parser
 * Description: Parses tracklists from text files or strings, extracting timestamps and track names.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Parses a tracklist string into an array of tracks with timestamps.
 *
 * @param string $tracklist The tracklist string or file path.
 * @param bool $is_file Optional. Whether the input is a file path. Default: false.
 * @return array An array of tracks, each with 'time' and 'title'.
 */
function brmedia_parse_tracklist( $tracklist, $is_file = false ) {
    if ( $is_file ) {
        // Read the file content
        $tracklist = file_get_contents( $tracklist );
        if ( false === $tracklist ) {
            return array();
        }
    }

    // Split the tracklist into lines
    $lines = explode( "\n", $tracklist );
    $tracks = array();

    foreach ( $lines as $line ) {
        // Match lines with timestamp and title (e.g., "00:00 - Track Name")
        if ( preg_match( '/^(\d{2}:\d{2}(:\d{2})?)\s*[-–—:]\s*(.+)$/', trim( $line ), $matches ) ) {
            $time = $matches[1];
            $title = trim( $matches[3]);
            $tracks[] = array(
                'time' => $time,
                'title' => $title,
            );
        }
    }

    return $tracks;
}

/**
 * Converts a timestamp string (hh:mm:ss or mm:ss) to seconds.
 *
 * @param string $time The timestamp string.
 * @return int The time in seconds.
 */
function brmedia_timestamp_to_seconds( $time ) {
    $parts = explode( ':', $time );
    $seconds = 0;

    if ( count( $parts ) === 3 ) { // hh:mm:ss
        $seconds += (int) $parts[0] * 3600;
        $seconds += (int) $parts[1] * 60;
        $seconds += (int) $parts[2];
    } elseif ( count( $parts ) === 2 ) { // mm:ss
        $seconds += (int) $parts[0] * 60;
        $seconds += (int) $parts[1];
    } elseif ( count( $parts ) === 1 ) { // ss
        $seconds += (int) $parts[0];
    }

    return $seconds;
}