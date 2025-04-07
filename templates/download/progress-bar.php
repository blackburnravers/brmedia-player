<?php
/**
 * Progress Bar Download Button Template
 * A download button with a progress bar for visual feedback.
 *
 * @package BRMediaPlayer
 * @subpackage Templates
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Retrieve template settings from ACP
$settings = get_option('brmedia_template_settings', []);
$download_settings = $settings['downloads'] ?? [];
$button_color = $download_settings['button_color'] ?? '#0073aa';
$button_text = $download_settings['button_text'] ?? 'Download';
$progress_color = $download_settings['progress_color'] ?? '#28a745';

// File data
$file_id = $atts['id'] ?? 0;
$file_url = wp_get_attachment_url($file_id);
$file_name = basename($file_url);

// Validate file URL
if (empty($file_url)) {
    echo '<p>Error: No file found.</p>';
    return;
}

?>
<div class="brmedia-download-progress" style="margin-top: 10px;">
    <button class="brmedia-download-btn progress-bar-template" 
            style="background-color: <?php echo esc_attr($button_color); ?>; 
                   color: #fff; 
                   padding: 10px 20px; 
                   border: none; 
                   cursor: pointer; 
                   border-radius: 5px; 
                   font-size: 16px; 
                   transition: background-color 0.3s ease;"
            data-file-id="<?php echo esc_attr($file_id); ?>">
        <i class="fas fa-download"></i> <?php echo esc_html($button_text); ?>
    </button>
    <div class="progress-container" style="display: none; margin-top: 10px;">
        <progress value="0" max="100" style="width: 100%; height: 20px;"></progress>
        <span class="progress-text" style="display: block; text-align: center; margin-top: 5px;">0%</span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.querySelector('.brmedia-download-btn[data-file-id="<?php echo esc_attr($file_id); ?>"]');
        const progressContainer = btn.nextElementSibling;
        const progressBar = progressContainer.querySelector('progress');
        const progressText = progressContainer.querySelector('.progress-text');

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            progressContainer.style.display = 'block';
            btn.disabled = true;

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '<?php echo esc_url($file_url); ?>', true);
            xhr.responseType = 'blob';

            xhr.onprogress = function(event) {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    progressBar.value = percent;
                    progressText.textContent = percent + '%';
                }
            };

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const blob = new Blob([xhr.response], { type: xhr.response.type });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = '<?php echo esc_js($file_name); ?>';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    progressContainer.style.display = 'none';
                    btn.disabled = false;
                } else {
                    alert('Download failed. Please try again.');
                    progressContainer.style.display = 'none';
                    btn.disabled = false;
                }
            };

            xhr.send();
        });
    });
</script>

<style>
    .progress-container progress {
        border: none;
        background: #f3f3f3;
        border-radius: 5px;
    }
    .progress-container progress::-webkit-progress-bar {
        background: #f3f3f3;
        border-radius: 5px;
    }
    .progress-container progress::-webkit-progress-value {
        background: <?php echo esc_attr($progress_color); ?>;
        border-radius: 5px;
    }
</style>