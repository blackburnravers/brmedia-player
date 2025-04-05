// assets/js/templates/video-popup-3d.js
class BRMediaVideoPopup3D {
    constructor(playerElement, triggerElement) {
        this.playerElement = playerElement;
        this.triggerElement = triggerElement;
        this.plyr = null;
        this.scene = null;
        this.camera = null;
        this.renderer = null;
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
        this.setup3DScene();
    }

    closePopup() {
        this.playerElement.style.display = 'none';
        this.isOpen = false;
        this.cleanup3DScene();
    }

    setup3DScene() {
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.renderer = new THREE.WebGLRenderer();
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(this.renderer.domElement);

        const geometry = new THREE.BoxGeometry();
        const material = new THREE.MeshBasicMaterial({ color: 0x00ff00 });
        const cube = new THREE.Mesh(geometry, material);
        this.scene.add(cube);

        this.camera.position.z = 5;

        const animate = () => {
            requestAnimationFrame(animate);
            cube.rotation.x += 0.01;
            cube.rotation.y += 0.01;
            this.renderer.render(this.scene, this.camera);
        };
        animate();
    }

    cleanup3DScene() {
        if (this.renderer) {
            document.body.removeChild(this.renderer.domElement);
            this.renderer = null;
        }
        this.scene = null;
        this.camera = null;
    }
}

document.querySelectorAll('.brmedia-video-popup-3d-trigger').forEach(trigger => {
    const player = document.querySelector(trigger.getAttribute('data-target'));
    new BRMediaVideoPopup3D(player, trigger);
});