// assets/js/templates/audio-ai-enhanced.js
class BRMediaAudioAIEnhanced {
    constructor(playerElement) {
        this.player = playerElement.querySelector('.plyr');
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.source = null;
        this.analyser = null;
        this.initPlayer();
    }

    initPlayer() {
        const plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
            settings: ['speed'],
        });

        plyr.on('ready', () => {
            this.setupAudioAnalysis();
        });

        plyr.on('playing', () => this.updateVisualization());
    }

    setupAudioAnalysis() {
        this.source = this.audioContext.createMediaElementSource(this.player);
        this.analyser = this.audioContext.createAnalyser();
        this.analyser.fftSize = 256;
        this.source.connect(this.analyser);
        this.analyser.connect(this.audioContext.destination);

        const background = document.querySelector('.brmedia-audio-ai-enhanced .ai-background');
        this.dataArray = new Uint8Array(this.analyser.frequencyBinCount);
    }

    updateVisualization() {
        requestAnimationFrame(() => this.updateVisualization());
        this.analyser.getByteFrequencyData(this.dataArray);

        // Simulated AI mood detection (e.g., energy level)
        const avgFrequency = this.dataArray.reduce((a, b) => a + b) / this.dataArray.length;
        const moodColor = avgFrequency > 100 ? '#ff6b6b' : avgFrequency > 50 ? '#4ecdc4' : '#6b7280';
        document.querySelector('.brmedia-audio-ai-enhanced .ai-background').style.background = `linear-gradient(45deg, ${moodColor}, #000)`;
    }
}

document.querySelectorAll('.brmedia-audio-ai-enhanced').forEach(element => {
    new BRMediaAudioAIEnhanced(element);
});