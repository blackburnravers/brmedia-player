// assets/js/templates/audio-compact.js
class BRMediaAudioCompact {
    constructor(playerElement) {
        this.player = playerElement.querySelector('.plyr');
        this.initPlayer();
        this.resizeControls();
        window.addEventListener('resize', () => this.resizeControls());
    }

    initPlayer() {
        const plyr = new Plyr(this.player, {
            controls: ['play', 'progress', 'current-time', 'mute'],
            clickToPlay: true,
        });

        plyr.on('ready', () => {
            console.log('Compact player ready');
        });
    }

    resizeControls() {
        const containerWidth = this.player.parentElement.offsetWidth;
        const controlSize = containerWidth < 300 ? '25px' : '30px';
        const controls = this.player.querySelectorAll('.plyr__control');
        controls.forEach(control => {
            control.style.width = controlSize;
            control.style.height = controlSize;
        });
    }
}

document.querySelectorAll('.brmedia-audio-compact').forEach(element => {
    new BRMediaAudioCompact(element);
});