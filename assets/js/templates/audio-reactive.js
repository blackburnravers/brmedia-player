// assets/js/templates/audio-reactive.js
class BRMediaAudioReactive {
    constructor(playerElement) {
        this.player = playerElement;
        this.canvas = document.createElement('canvas');
        this.canvas.className = 'reactive-background';
        this.player.insertBefore(this.canvas, this.player.firstChild);
        this.ctx = this.canvas.getContext('2d');
        this.audioContext = new AudioContext();
        this.analyser = this.audioContext.createAnalyser();
        this.analyser.fftSize = 512;
        this.dataArray = new Uint8Array(this.analyser.frequencyBinCount);
        this.initPlayer();
    }

    initPlayer() {
        const plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
        });

        plyr.on('ready', () => {
            const source = this.audioContext.createMediaElementSource(plyr.media);
            source.connect(this.analyser);
            this.analyser.connect(this.audioContext.destination);
            this.resizeCanvas();
            this.render();
        });
    }

    resizeCanvas() {
        this.canvas.width = this.player.offsetWidth;
        this.canvas.height = this.player.offsetHeight;
    }

    render() {
        requestAnimationFrame(() => this.render());
        this.analyser.getByteFrequencyData(this.dataArray);
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        const centerX = this.canvas.width / 2;
        const centerY = this.canvas.height / 2;
        const radius = 100;
        this.ctx.beginPath();
        this.dataArray.forEach((value, index) => {
            const angle = (index / this.dataArray.length) * 2 * Math.PI;
            const x = centerX + Math.cos(angle) * (radius + value / 2);
            const y = centerY + Math.sin(angle) * (radius + value / 2);
            if (index === 0) {
                this.ctx.moveTo(x, y);
            } else {
                this.ctx.lineTo(x, y);
            }
        });
        this.ctx.closePath();
        this.ctx.strokeStyle = '#fff';
        this.ctx.stroke();
    }
}

// Usage
document.querySelectorAll('.brmedia-audio-reactive').forEach(element => {
    new BRMediaAudioReactive(element);
});