<?php
/**
 * Template Name: Download Button Style 1
 * Description: A basic download button for BRMedia Player.
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
?>

<a href="<?php echo esc_url( $download_url ); ?>" class="brmedia-download-button style-1" download>
    <i class="fas <?php echo esc_attr( $icon ); ?>"></i> <?php echo esc_html( $label ); ?>
</a>