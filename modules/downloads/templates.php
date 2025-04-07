<?php
/**
 * Downloads Templates
 * Advanced rendering of download buttons with gating and bulk options.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Single download button template
function brmedia_download_template($id, $template = 'big', $label = 'Download', $gate_type = '') {
    $valid_templates = ['big', 'small', 'massive', 'icon-only', 'progress-bar'];
    $template = in_array($template, $valid_templates) ? $template : 'big';
    $gate_options = explode(',', get_option('brmedia_download_gate_options', 'email,social,login'));

    ob_start();
    ?>
    <div class="brmedia-download-container" data-file-id="<?php echo esc_attr($id); ?>">
        <button class="brmedia-download-btn brmedia-template-<?php echo esc_attr($template); ?>" 
                data-file-id="<?php echo esc_attr($id); ?>" 
                <?php echo $gate_type ? 'data-gate="' . esc_attr($gate_type) . '"' : ''; ?>>
            <i class="fas fa-download"></i> <?php echo esc_html($label); ?>
        </button>
        <?php if ($gate_type && in_array($gate_type, $gate_options)) : ?>
            <div class="brmedia-gate-form" style="display:none;">
                <?php if ($gate_type === 'email') : ?>
                    <input type="email" placeholder="Enter your email" class="brmedia-gate-input">
                <?php elseif ($gate_type === 'social') : ?>
                    <button class="brmedia-social-gate">Share on Twitter</button>
                <?php endif; ?>
                <button class="brmedia-gate-submit">Submit</button>
            </div>
        <?php endif; ?>
        <?php if ($template === 'progress-bar') : ?>
            <div class="brmedia-progress-bar" style="display:none;">
                <progress value="0" max="100"></progress>
                <span class="progress-text">0%</span>
            </div>
        <?php endif; ?>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('.brmedia-download-btn[data-file-id="<?php echo esc_attr($id); ?>"]').on('click', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const gateType = $btn.data('gate');
                const $form = $btn.next('.brmedia-gate-form');
                const $progress = $btn.nextAll('.brmedia-progress-bar');

                if (gateType && $form.length) {
                    $form.show();
                    $form.find('.brmedia-gate-submit').on('click', function() {
                        const gateValue = $form.find('.brmedia-gate-input').val() || 'shared';
                        downloadFile(<?php echo esc_attr($id); ?>, gateType, gateValue, $btn, $progress);
                        $form.hide();
                    });
                } else {
                    downloadFile(<?php echo esc_attr($id); ?>, '', '', $btn, $progress);
                }
            });

            function downloadFile(fileId, gateType, gateValue, $btn, $progress) {
                $.ajax({
                    url: brmedia_downloads.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'brmedia_download',
                        file_id: fileId,
                        gate_type: gateType,
                        gate_value: gateValue,
                        nonce: brmedia_downloads.nonce
                    },
                    beforeSend: function() {
                        if ($progress.length) $progress.show();
                        $btn.prop('disabled', true);
                    },
                    success: function(response) {
                        if ($progress.length) $progress.hide();
                        $btn.prop('disabled', false);
                        const blob = new Blob([response], { type: response.type });
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = '<?php echo esc_attr(basename(get_attached_file($id))); ?>';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr) {
                        if ($progress.length) $progress.hide();
                        $btn.prop('disabled', false);
                        alert('Error: ' + (xhr.responseJSON?.data?.message || 'Download failed'));
                    }
                });
            }
        });
    </script>
    <?php
    return ob_get_clean();
}

// Bulk download template
function brmedia_download_bulk_template($file_ids) {
    $file_ids = array_filter(array_map('intval', explode(',', $file_ids)));
    if (empty($file_ids)) {
        return '<p>Error: No files specified</p>';
    }

    ob_start();
    ?>
    <div class="brmedia-bulk-download">
        <button class="brmedia-bulk-download-btn" data-file-ids="<?php echo esc_attr(implode(',', $file_ids)); ?>">
            <i class="fas fa-download"></i> Download All (<?php echo count($file_ids); ?> files)
        </button>
        <div class="brmedia-progress-bar" style="display:none;">
            <progress value="0" max="100"></progress>
            <span class="progress-text">0%</span>
        </div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('.brmedia-bulk download-btn[data-file-ids="<?php echo esc_attr(implode(',', $file_ids)); ?>"]').on('click', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const fileIds = $btn.data('file-ids');
                const $progress = $btn.next('.brmedia-progress-bar');

                $.ajax({
                    url: brmedia_downloads.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'brmedia_download_bulk',
                        file_ids: fileIds,
                        nonce: brmedia_downloads.nonce
                    },
                    beforeSend: function() {
                        $progress.show();
                        $btn.prop('disabled', true);
                    },
                    success: function(response) {
                        $progress.hide();
                        $btn.prop('disabled', false);
                        const blob = new Blob([response], { type: 'application/zip' });
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'bulk_download.zip';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr) {
                        $progress.hide();
                        $btn.prop('disabled', false);
                        alert('Error: ' + (xhr.responseJSON?.data?.message || 'Bulk download failed'));
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}