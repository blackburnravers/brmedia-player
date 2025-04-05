// assets/js/templates/audio-fullscreen.js
class BRMediaAudioFullscreen {
    constructor(playerElement) {
        this.player = playerElement.querySelector('.plyr');
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.analyser = null;
        this.source = null;
        this.initPlayer();
    }

    initPlayer() {
        const plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
            fullscreen: { enabled: true, fallback: true, iosNative: true },
        });

        plyr.on('enterfullscreen', () => {
            this.setupWaveform();
        });

        plyr.on('exitfullscreen', () => {
            this.stopWaveform();
        });

        plyr.on('playing', () => this.renderWaveform());
    }

    setupWaveform() {
        this.source = this.audioContext.createMediaElementSource(this.player);
        this.analyser = this.audioContext.createAnalyser();
        this.analyser.fftSize = 256;
        this.source.connect(this.analyser);
        this.analyser.connect(this.audioContext.destination);
        this.dataArray = new Uint8Array(this.analyser.frequencyBinCount);

        const waveformContainer = document.createElement('div');
        waveformContainer.className = 'waveform-container';
        document.body.appendChild(waveformContainer);
    }

    stopWaveform() {
        if (this.source) this.source.disconnect();
        if (this.analyser) this.analyser.disconnect();
        this.source = null;
        this.analyser = null;
        const waveform = document.querySelector('.waveform-container');
        if (waveform) waveform.remove();
    }

    renderWaveform() {
        if (!this.analyser) return;
        requestAnimationFrame(() => this.renderWaveform());
        this.analyser.getByteTimeDomainData(this.dataArray);

        const waveformContainer = document.querySelector('.waveform-container');
        waveformContainer.innerHTML = '';
        const barWidth = waveformContainer.offsetWidth / this.dataArray.length;
        this.dataArray.forEach((value, index) => {
            const bar = document.createElement('div');
            bar.className = 'waveform-bar';
            bar.style.width = `${barWidth}px`;
            bar.style.height = `${(value / 255) * 100}px`;
            bar.style.left = `${index * barWidth}px`;
            waveformContainer.appendChild(bar);
        });
    }
}

document.querySelectorAll('.brmedia-audio-fullscreen').forEach(element => {
    new BRMediaAudioFullscreen(element);
});