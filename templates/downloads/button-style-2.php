<?php
/**
 * Template Name: Download Button Style 2
 * Description: A styled download button with hover effect for BRMedia Player.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve download data from post meta or shortcode attributes
$download_url = get_post_meta( get_the_ID(), '_brmedia_download_url', true );
$label = get_post_meta( get_the_ID(), '_brmedia_download_label', true ) ?: 'Download';
$icon = get_post_meta( get_the_ID(), '_brmedia_download_icon', true ) ?: 'fa-download';
$color = get_post_meta( get_the_ID(), '_brmedia_download_color', true ) ?: '#0073aa';
?>

<a href="<?php echo esc_url( $download_url ); ?>" class="brmedia-download-button style-2" style="background-color: <?php echo esc_attr( $color ); ?>;" download>
    <i class="fas <?php echo esc_attr( $icon ); ?>"></i> <?php echo esc_html( $label ); ?>
</a>

<style>
    .brmedia-download-button.style-2 {
        padding: 10px 20px;
        border-radius: 5px;
        color: #fff;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    .brmedia-download-button.style-2:hover {
        background-color: #005a87;
    }
</style>