// assets/js/templates/audio-custom.js
class BRMediaAudioCustom {
    constructor(playerElement) {
        this.player = playerElement.querySelector('.plyr');
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.source = null;
        this.distortion = null;
        this.panner = null;
        this.initPlayer();
    }

    initPlayer() {
        const plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
            settings: ['speed'],
        });

        plyr.on('ready', () => {
            this.setupAudioEffects();
            this.addEffectControls();
        });
    }

    setupAudioEffects() {
        this.source = this.audioContext.createMediaElementSource(this.player);
        this.distortion = this.audioContext.createWaveShaper();
        this.panner = this.audioContext.createStereoPanner();

        this.distortion.curve = this.makeDistortionCurve(100);
        this.distortion.oversample = '4x';

        this.source.connect(this.distortion);
        this.distortion.connect(this.panner);
        this.panner.connect(this.audioContext.destination);
    }

    makeDistortionCurve(amount) {
        const k = typeof amount === 'number' ? amount : 50;
        const n_samples = 44100;
        const curve = new Float32Array(n_samples);
        const deg = Math.PI / 180;
        for (let i = 0; i < n_samples; i++) {
            const x = (i * 2) / n_samples - 1;
            curve[i] = (3 + k) * x * 20 * deg / (Math.PI + k * Math.abs(x));
        }
        return curve;
    }

    addEffectControls() {
        const controls = document.createElement('div');
        controls.innerHTML = `
            <label>Distortion: <input type="range" min="0" max="200" value="100" class="distortion-slider"></label>
            <label>Panning: <input type="range" min="-1" max="1" value="0" step="0.1" class="panner-slider"></label>
        `;
        this.player.parentElement.appendChild(controls);

        controls.querySelector('.distortion-slider').addEventListener('input', (e) => {
            this.distortion.curve = this.makeDistortionCurve(parseInt(e.target.value));
        });

        controls.querySelector('.panner-slider').addEventListener('input', (e) => {
            this.panner.pan.value = parseFloat(e.target.value);
        });
    }
}

document.querySelectorAll('.brmedia-audio-custom').forEach(element => {
    new BRMediaAudioCustom(element);
});