// assets/js/analytics.js
class BRMediaAnalytics {
    constructor() {
        this.initEventListeners();
        this.loadHistoricalData();
    }

    initEventListeners() {
        document.querySelectorAll('.brmedia-player').forEach(player => {
            const plyr = player.querySelector('.plyr');
            plyr.addEventListener('play', () => this.trackEvent('play', player.dataset.mediaId));
            plyr.addEventListener('pause', () => this.trackEvent('pause', player.dataset.mediaId));
            plyr.addEventListener('ended', () => this.trackEvent('ended', player.dataset.mediaId));
        });

        document.querySelectorAll('.brmedia-download').forEach(downloadBtn => {
            downloadBtn.addEventListener('click', () => this.trackEvent('download', downloadBtn.dataset.mediaId));
        });
    }

    trackEvent(eventType, mediaId) {
        fetch('/wp-admin/admin-ajax.php?action=brmedia_track_event', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ eventType, mediaId })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                console.log(`Tracked ${eventType} for media ${mediaId}`);
            }
        })
        .catch(error => console.error('Error tracking event:', error));
    }

    loadHistoricalData() {
        fetch('/wp-admin/admin-ajax.php?action=brmedia_get_analytics')
            .then(response => response.json())
            .then(data => {
                const chartContainer = document.querySelector('.brmedia-analytics-chart');
                if (chartContainer) {
                    // Placeholder for chart rendering (e.g., with Chart.js)
                    chartContainer.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                }
            })
            .catch(error => console.error('Error loading analytics:', error));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new BRMediaAnalytics();
});