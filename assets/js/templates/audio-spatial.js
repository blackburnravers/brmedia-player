// assets/js/templates/audio-spatial.js
class BRMediaAudioSpatial {
    constructor(playerElement) {
        this.player = playerElement;
        this.audioContext = new AudioContext();
        this.panner = this.audioContext.createStereoPanner();
        this.source = this.audioContext.createMediaElementSource(this.player.querySelector('audio'));
        this.source.connect(this.panner);
        this.panner.connect(this.audioContext.destination);
        this.initPlayer();
        this.addPanningControl();
    }

    initPlayer() {
        new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
        });
    }

    addPanningControl() {
        const panningControl = document.createElement('input');
        panningControl.type = 'range';
        panningControl.min = -1;
        panningControl.max = 1;
        panningControl.step = 0.1;
        panningControl.value = 0;
        panningControl.className = 'panning-slider';
        panningControl.addEventListener('input', (e) => {
            this.panner.pan.value = parseFloat(e.target.value);
        });
        this.player.appendChild(panningControl);
    }
}

// Usage
document.querySelectorAll('.brmedia-audio-spatial').forEach(element => {
    new BRMediaAudioSpatial(element);
});