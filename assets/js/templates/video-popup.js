// assets/js/templates/video-popup.js
class BRMediaVideoPopup {
    constructor(playerElement, triggerElement) {
        this.playerElement = playerElement;
        this.triggerElement = triggerElement;
        this.plyr = null;
        this.isOpen = false;
        this.init();
    }

    init() {
        this.plyr = new Plyr(this.playerElement, {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
        });

        this.triggerElement.addEventListener('click', () => this.togglePopup());
    }

    togglePopup() {
        if (this.isOpen) {
            this.closePopup();
        } else {
            this.openPopup();
        }
    }

    openPopup() {
        this.playerElement.style.display = 'block';
        this.isOpen = true;
    }

    closePopup() {
        this.playerElement.style.display = 'none';
        this.isOpen = false;
    }
}

document.querySelectorAll('.brmedia-video-popup-trigger').forEach(trigger => {
    const player = document.querySelector(trigger.getAttribute('data-target'));
    new BRMediaVideoPopup(player, trigger);
});