// assets/js/frontend.js
class BRMediaFrontend {
    constructor() {
        this.initPlayers();
        this.setupEventListeners();
        this.loadDynamicTemplates();
    }

    initPlayers() {
        const players = document.querySelectorAll('.brmedia-player');
        players.forEach(player => {
            const template = player.dataset.template;
            const plyr = new Plyr(player.querySelector('.plyr'), {
                controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
                settings: ['speed', 'quality'],
            });

            // Load template-specific JS if needed
            if (template) {
                const script = document.createElement('script');
                script.src = `/wp-content/plugins/brmedia-player/assets/js/templates/${template}.js`;
                document.body.appendChild(script);
            }
        });
    }

    setupEventListeners() {
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

    loadDynamicTemplates() {
        const dynamicElements = document.querySelectorAll('[data-dynamic-template]');
        dynamicElements.forEach(element => {
            const template = element.dataset.dynamicTemplate;
            const script = document.createElement('script');
            script.src = `/wp-content/plugins/brmedia-player/assets/js/templates/${template}.js`;
            document.body.appendChild(script);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new BRMediaFrontend();
});