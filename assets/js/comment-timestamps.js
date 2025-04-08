// BRMedia Player Comment Timestamps JavaScript

(function($) {
    $(document).ready(function() {
        // Assuming WaveSurfer.js is initialized
        var wavesurfer = WaveSurfer.instances[0]; // Adjust based on actual initialization

        // Add click event to waveform
        wavesurfer.on('click', function(e) {
            var time = wavesurfer.getCurrentTime();
            var comment = prompt('Enter your comment for time ' + time.toFixed(2) + 's:');
            if (comment) {
                $.post(brmedia_params.ajax_url, {
                    action: 'brmedia_add_comment',
                    post_id: wavesurfer.container.dataset.postId,
                    timestamp: time,
                    comment: comment,
                    nonce: brmedia_params.nonce
                }, function(response) {
                    if (response.success) {
                        // Add a marker to the waveform
                        wavesurfer.addRegion({
                            start: time,
                            end: time + 0.1,
                            color: 'rgba(0, 123, 255, 0.3)',
                            drag: false,
                            resize: false
                        });
                    } else {
                        alert('Failed to add comment.');
                    }
                });
            }
        });

        // Load existing comments
        $.get(brmedia_params.ajax_url, {
            action: 'brmedia_get_comments',
            post_id: wavesurfer.container.dataset.postId,
            nonce: brmedia_params.nonce
        }, function(response) {
            if (response.success) {
                response.data.forEach(function(comment) {
                    wavesurfer.addRegion({
                        start: comment.timestamp,
                        end: comment.timestamp + 0.1,
                        color: 'rgba(0, 123, 255, 0.3)',
                        drag: false,
                        resize: false,
                        attributes: {
                            label: comment.content
                        }
                    });
                });
            }
        });
    });
})(jQuery);