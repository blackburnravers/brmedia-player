// admin.js

jQuery(document).ready(function($) {
    var mediaUploader;

    $('.upload_button').on('click', function(e) {
        e.preventDefault();

        var button = $(this);
        var inputField = button.prev('input');

        // If the media uploader exists, reopen it.
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create a new media uploader.
        mediaUploader = wp.media({
            title: 'Select File',
            button: {
                text: 'Use this file'
            },
            multiple: false
        });

        // When a file is selected, grab the URL and set it as the value of the input field.
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url);
        });

        // Open the media uploader.
        mediaUploader.open();
    });
});