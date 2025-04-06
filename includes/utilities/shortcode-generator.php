<?php
/**
 * Utility: Shortcode Generator
 * Description: Dynamically generates shortcodes for BRMedia Player elements.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generates a shortcode for the specified media type and attributes.
 *
 * @param string $type The media type (e.g., 'audio', 'video', 'tracklist').
 * @param array $atts Optional. An array of attributes for the shortcode.
 * @return string The generated shortcode.
 */
function brmedia_generate_shortcode( $type, $atts = array() ) {
    $shortcode = '[' . $type;

    // Add attributes
    foreach ( $atts as $key => $value ) {
        $shortcode .= ' ' . $key . '="' . esc_attr( $value ) . '"';
    }

    $shortcode .= ']';

    return $shortcode;
}

/**
 * Example usage:
 * echo brmedia_generate_shortcode( 'brmedia_audio', array( 'template' => 'default', 'post_id' => get_the_ID() ) );
 * // Output: [brmedia_audio template="default" post_id="123"]
 */