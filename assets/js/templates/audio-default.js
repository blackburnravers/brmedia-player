// assets/js/templates/audio-default.js
class BRMediaAudioDefault {
    constructor(playerElement) {
        this.player = playerElement.querySelector('.plyr');
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.source = null;
        this.analyser = null;
        this.initPlayer();
    }

    initPlayer() {
        const plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
        });

        plyr.on('ready', () => {
            this.setupWaveform();
        });

        plyr.on('playing', () => this.renderWaveform());
    }

    setupWaveform() {
        this.source = this.audioContext.createMediaElementSource(this.player);
        this.analyser = this.audioContext.createAnalyser();
        this.analyser.fftSize = 128;
        this.source.connect(this.analyser);
        this.analyser.connect(this.audioContext.destination);

        this.dataArray = new Uint8Array(this.analyser.frequencyBinCount);
        const waveElement = document.createElement('div');
        waveElement.className = 'waveform';
        this.player.parentElement.appendChild(waveElement);
    }

    renderWaveform() {
        requestAnimationFrame(() => this.renderWaveform());
        this.analyser.getByteTimeDomainData(this.dataArray);

        const waveElement = this.player.parentElement.querySelector('.waveform');
        waveElement.innerHTML = '';
        const barWidth = waveElement.offsetWidth / this.dataArray.length;
        this.dataArray.forEach((value, index) => {
            const bar = document.createElement('div');
            bar.className = 'bar';
            bar.style.width = `${barWidth}px`;
            bar.style.height = `${(value / 255) * 50}px`;
            bar.style.left = `${index * barWidth}px`;
            waveElement.appendChild(bar);
        });
    }
}

document.querySelectorAll('.brmedia-audio-default').forEach(element => {
    new BRMediaAudioDefault(element);
});