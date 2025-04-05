// assets/js/templates/video-compact.js
class BRMediaVideoCompact {
    constructor(playerElement) {
        this.player = playerElement.querySelector('.plyr');
        this.plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute'],
            clickToPlay: true,
        });
        this.resizeVideo();
        window.addEventListener('resize', () => this.resizeVideo());
    }

    resizeVideo() {
        const containerWidth = this.player.parentElement.offsetWidth;
        const video = this.player.querySelector('video');
        video.style.width = `${containerWidth}px`;
        video.style.height = `${(containerWidth / 16) * 9}px`; // 16:9 aspect ratio
    }
}

// Usage
document.querySelectorAll('.brmedia-video-compact').forEach(element => {
    new BRMediaVideoCompact(element);
});