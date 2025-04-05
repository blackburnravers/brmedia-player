// assets/js/templates/video-interactive.js
class BRMediaVideoInteractive {
    constructor(playerElement, hotspots) {
        this.player = playerElement.querySelector('.plyr');
        this.plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
        });
        this.hotspots = hotspots;
        this.initHotspots();
    }

    initHotspots() {
        const video = this.player.querySelector('video');
        this.hotspots.forEach(hotspot => {
            const hotspotElement = document.createElement('div');
            hotspotElement.className = 'hotspot';
            hotspotElement.style.left = `${hotspot.x}%`;
            hotspotElement.style.top = `${hotspot.y}%`;
            hotspotElement.addEventListener('click', hotspot.action);
            this.player.appendChild(hotspotElement);
        });
    }
}

// Usage
const hotspots = [
    { x: 20, y: 30, action: () => alert('Hotspot 1 clicked') },
    { x: 50, y: 50, action: () => alert('Hotspot 2 clicked') },
];
document.querySelectorAll('.brmedia-video-interactive').forEach(element => {
    new BRMediaVideoInteractive(element, hotspots);
});