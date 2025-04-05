// assets/js/templates/audio-popup-advanced.js
class BRMediaAudioPopupAdvanced {
    constructor(playerElement, triggerElement) {
        this.playerElement = playerElement;
        this.triggerElement = triggerElement;
        this.plyr = null;
        this.audioContext = null;
        this.analyser = null;
        this.canvas = null;
        this.ctx = null;
        this.isOpen = false;
        this.init();
    }

    init() {
        this.plyr = new Plyr(this.playerElement, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'settings'],
            settings: ['speed', 'quality'],
        });

        this.triggerElement.addEventListener('click', () => this.togglePopup());
        this.setupCanvas();
    }

    setupCanvas() {
        this.canvas = document.createElement('canvas');
        this.canvas.className = 'audio-visualization';
        this.playerElement.appendChild(this.canvas);
        this.ctx = this.canvas.getContext('2d');
        this.canvas.width = this.playerElement.offsetWidth;
        this.canvas.height = 100;
    }

    togglePopup() {
        if (this.isOpen) {
            this.closePopup();
        } else {
            this.openPopup();
        }
    }

    openPopup() {
        this.playerElement.classList.add('active');
        this.isOpen = true;
        this.setupAudioAnalysis();
        this.renderVisualization();
    }

    closePopup() {
        this.playerElement.classList.remove('active');
        this.isOpen = false;
        this.stopAudioAnalysis();
    }

    setupAudioAnalysis() {
        this.audioContext = new AudioContext();
        this.analyser = this.audioContext.createAnalyser();
        this.analyser.fftSize = 256;
        const source = this.audioContext.createMediaElementSource(this.plyr.media);
        source.connect(this.analyser);
        this.analyser.connect(this.audioContext.destination);
        this.dataArray = new Uint8Array(this.analyser.frequencyBinCount);
    }

    stopAudioAnalysis() {
        if (this.audioContext) {
            this.audioContext.close();
            this.audioContext = null;
        }
    }

    renderVisualization() {
        if (!this.isOpen) return;
        requestAnimationFrame(() => this.renderVisualization());
        this.analyser.getByteFrequencyData(this.dataArray);
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        const barWidth = (this.canvas.width / this.dataArray.length) * 2.5;
        let x = 0;
        this.dataArray.forEach(value => {
            const barHeight = (value / 255) * this.canvas.height;
            this.ctx.fillStyle = `rgb(${value}, 50, 50)`;
            this.ctx.fillRect(x, this.canvas.height - barHeight, barWidth, barHeight);
            x += barWidth + 1;
        });
    }
}

// Usage
document.querySelectorAll('.brmedia-audio-popup-advanced-trigger').forEach(trigger => {
    const player = document.querySelector(trigger.getAttribute('data-target'));
    new BRMediaAudioPopupAdvanced(player, trigger);
});