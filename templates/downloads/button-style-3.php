<?php
/**
 * Template Name: Download Button Style 3
 * Description: A download button with progress bar for BRMedia Player.
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

<a href="<?php echo esc_url( $download_url ); ?>" class="brmedia-download-button style-3" download>
    <i class="fas <?php echo esc_attr( $icon ); ?>"></i> <?php echo esc_html( $label ); ?>
</a>
<div class="brmedia-download-progress" style="display: none;">
    <progress value="0" max="100"></progress>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.querySelector('.brmedia-download-button.style-3');
        const progressContainer = document.querySelector('.brmedia-download-progress');
        const progressBar = progressContainer.querySelector('progress');

        button.addEventListener('click', function(event) {
            event.preventDefault();
            progressContainer.style.display = 'block';
            const xhr = new XMLHttpRequest();
            xhr.open('GET', this.href, true);
            xhr.responseType = 'blob';
            xhr.onprogress = function(event) {
                if (event.lengthComputable) {
                    const percentComplete = (event.loaded / event.total) * 100;
                    progressBar.value = percentComplete;
                }
            };
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const blob = xhr.response;
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = '<?php echo esc_attr( basename( $download_url ) ); ?>';
                    link.click();
                    progressContainer.style.display = 'none';
                }
            };
            xhr.send();
        });
    });
</script>