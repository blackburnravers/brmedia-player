// assets/js/admin.js
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard widgets
    const widgets = document.querySelectorAll('.brmedia-dashboard .widget');
    widgets.forEach(widget => {
        widget.addEventListener('click', function() {
            const content = this.querySelector('.widget-content');
            if (content) {
                content.classList.toggle('hidden');
            }
        });
    });

    // Handle settings form submission
    const settingsForm = document.querySelector('.brmedia-settings form');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const data = new FormData(this);
            fetch('/wp-admin/admin-ajax.php?action=brmedia_save_settings', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Error saving settings.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Metabox interactions for CPTs (brmusic, brvideo)
    const metaboxes = document.querySelectorAll('.brmedia-metabox');
    metaboxes.forEach(metabox => {
        const inputs = metabox.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                const postId = metabox.dataset.postId;
                const field = this.name;
                const value = this.value;
                fetch('/wp-admin/admin-ajax.php?action=brmedia_save_metabox', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ postId, field, value })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        console.log(`Metabox saved for post ${postId}`);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
});