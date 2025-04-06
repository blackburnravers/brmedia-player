<?php
/**
 * Template Name: Download Button Style 5
 * Description: A customizable download button for BRMedia Player.
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
$bg_color = get_post_meta( get_the_ID(), '_brmedia_download_bg_color', true ) ?: '#0073aa';
$text_color = get_post_meta( get_the_ID(), '_brmedia_download_text_color', true ) ?: '#ffffff';
?>

<a href="<?php echo esc_url( $download_url ); ?>" class="brmedia-download-button style-5" style="background-color: <?php echo esc_attr( $bg_color ); ?>; color: <?php echo esc_attr( $text_color ); ?>;" download>
    <i class="fas <?php echo esc_attr( $icon ); ?>"></i> <?php echo esc_html( $label ); ?>
</a>

<style>
    .brmedia-download-button.style-5 {
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: opacity 0.3s ease;
    }
    .brmedia-download-button.style-5:hover {
        opacity: 0.8;
    }
</style>