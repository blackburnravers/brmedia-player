// assets/js/templates/video-cinematic.js
class BRMediaVideoCinematic {
    constructor(playerElement) {
        this.player = playerElement.querySelector('.plyr');
        this.plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
            fullscreen: { enabled: true, fallback: true, iosNative: true },
        });
        this.initLightingEffects();
    }

    initLightingEffects() {
        const video = this.player.querySelector('video');
        const background = document.querySelector('.brmedia-video-cinematic');
        video.addEventListener('playing', () => {
            this.updateLighting(background);
        });
    }

    updateLighting(background) {
        const video = this.player.querySelector('video');
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;
        let r = 0, g = 0, b = 0;
        for (let i = 0; i < data.length; i += 4) {
            r += data[i];
            g += data[i + 1];
            b += data[i + 2];
        }
        const pixels = data.length / 4;
        r = Math.floor(r / pixels);
        g = Math.floor(g / pixels);
        b = Math.floor(b / pixels);
        background.style.background = `radial-gradient(circle, rgb(${r}, ${g}, ${b}), #000)`;
        requestAnimationFrame(() => this.updateLighting(background));
    }
}

// Usage
document.querySelectorAll('.brmedia-video-cinematic').forEach(element => {
    new BRMediaVideoCinematic(element);
});