// assets/js/templates/video-default.js
class BRMediaVideoDefault {
    constructor(playerElement) {
        this.player = playerElement.querySelector('.plyr');
        this.plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
            fullscreen: { enabled: true, fallback: true, iosNative: true },
        });
        this.initEventListeners();
    }

    initEventListeners() {
        this.plyr.on('play', () => console.log('Video started playing'));
        this.plyr.on('pause', () => console.log('Video paused'));
        this.plyr.on('ended', () => console.log('Video ended'));
    }
}

// Usage
document.querySelectorAll('.brmedia-video-default').forEach(element => {
    new BRMediaVideoDefault(element);
});