<?php
/**
 * Template Name: Download Button Style 4
 * Description: A download button with email collection for BRMedia Player.
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

<button class="brmedia-download-button style-4" data-download-url="<?php echo esc_url( $download_url ); ?>">
    <i class="fas <?php echo esc_attr( $icon ); ?>"></i> <?php echo esc_html( $label ); ?>
</button>

<div class="brmedia-email-modal" style="display: none;">
    <div class="brmedia-modal-content">
        <h3>Enter Your Email to Download</h3>
        <input type="email" class="brmedia-email-input" placeholder="Your Email">
        <button class="brmedia-submit-email">Submit</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.querySelector('.brmedia-download-button.style-4');
        const modal = document.querySelector('.brmedia-email-modal');
        const emailInput = modal.querySelector('.brmedia-email-input');
        const submitButton = modal.querySelector('.brmedia-submit-email');

        button.addEventListener('click', function() {
            modal.style.display = 'block';
        });

        submitButton.addEventListener('click', function() {
            const email = emailInput.value;
            if (email) {
                // Send email to server (e.g., via AJAX)
                console.log('Email collected: ' + email);
                // Proceed to download
                window.location.href = button.getAttribute('data-download-url');
                modal.style.display = 'none';
            } else {
                alert('Please enter a valid email.');
            }
        });
    });
</script>