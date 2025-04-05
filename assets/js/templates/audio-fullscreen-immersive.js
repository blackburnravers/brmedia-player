// assets/js/templates/audio-fullscreen-immersive.js
class BRMediaAudioFullscreenImmersive {
    constructor(playerElement) {
        this.player = playerElement.querySelector('.plyr');
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.analyser = null;
        this.source = null;
        this.particles = [];
        this.initPlayer();
        this.initParticles();
    }

    initPlayer() {
        const plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
            fullscreen: { enabled: true, fallback: true, iosNative: true },
        });

        plyr.on('enterfullscreen', () => {
            document.body.classList.add('fullscreen-active');
            this.setupAudioAnalysis();
        });

        plyr.on('exitfullscreen', () => {
            document.body.classList.remove('fullscreen-active');
            this.stopAudioAnalysis();
        });

        plyr.on('playing', () => this.updateParticles());
    }

    setupAudioAnalysis() {
        this.source = this.audioContext.createMediaElementSource(this.player);
        this.analyser = this.audioContext.createAnalyser();
        this.analyser.fftSize = 512;
        this.source.connect(this.analyser);
        this.analyser.connect(this.audioContext.destination);
        this.dataArray = new Uint8Array(this.analyser.frequencyBinCount);
    }

    stopAudioAnalysis() {
        if (this.source) this.source.disconnect();
        if (this.analyser) this.analyser.disconnect();
        this.source = null;
        this.analyser = null;
    }

    initParticles() {
        const particleContainer = document.createElement('div');
        particleContainer.className = 'particle-container';
        document.body.appendChild(particleContainer);

        for (let i = 0; i < 100; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = `${Math.random() * 100}vw`;
            particle.style.top = `${Math.random() * 100}vh`;
            particle.style.width = `${Math.random() * 5 + 2}px`;
            particle.style.height = particle.style.width;
            particleContainer.appendChild(particle);
            this.particles.push(particle);
        }
    }

    updateParticles() {
        if (!this.analyser) return;
        requestAnimationFrame(() => this.updateParticles());
        this.analyser.getByteFrequencyData(this.dataArray);

        const avgFrequency = this.dataArray.reduce((a, b) => a + b) / this.dataArray.length;
        const scaleFactor = 1 + (avgFrequency / 255) * 2;

        this.particles.forEach(particle => {
            particle.style.transform = `scale(${scaleFactor})`;
            particle.style.opacity = avgFrequency / 255;
        });
    }
}

document.querySelectorAll('.brmedia-audio-fullscreen-immersive').forEach(element => {
    new BRMediaAudioFullscreenImmersive(element);
});