// BRMedia Player Admin JavaScript

(function($) {
    $(document).ready(function() {
        // Handle module enable/disable toggles
        $('.brmedia-module-controls input[type="checkbox"]').on('change', function() {
            var module = $(this).attr('name').replace('brmedia_', '').replace('_enabled', '');
            var enabled = $(this).is(':checked') ? 'Enabled' : 'Disabled';
            $(this).closest('.brmedia-module-stat').find('.status').text(enabled);
        });

        // Copy shortcode to clipboard
        $('.brmedia-shortcode button').on('click', function() {
            var shortcode = $(this).prev('code').text();
            navigator.clipboard.writeText(shortcode).then(function() {
                alert('Shortcode copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        });

        // Initialize analytics charts (if needed)
        if ($('#playChart').length) {
            var ctx = document.getElementById('playChart').getContext('2d');
            var playChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Music', 'Video', 'Radio'],
                    datasets: [{
                        label: 'Plays',
                        data: [/* Data fetched via PHP */],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
})(jQuery);