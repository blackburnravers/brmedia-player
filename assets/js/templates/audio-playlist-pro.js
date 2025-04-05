// assets/js/templates/audio-playlist-pro.js
class BRMediaAudioPlaylistPro {
    constructor(playlistElement) {
        this.playlist = playlistElement.querySelector('.playlist-items');
        this.plyr = new Plyr('.plyr', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
        });
        this.initPlaylist();
        this.initDragAndDrop();
    }

    initPlaylist() {
        const savedOrder = JSON.parse(localStorage.getItem('playlistOrder')) || [];
        const tracks = Array.from(this.playlist.querySelectorAll('.track'));
        if (savedOrder.length) {
            savedOrder.forEach(id => {
                const track = tracks.find(t => t.dataset.trackId === id);
                if (track) this.playlist.appendChild(track);
            });
        }

        tracks.forEach(track => {
            const playBtn = track.querySelector('.play-btn');
            playBtn.addEventListener('click', () => this.playTrack(track.dataset.trackId));
        });
    }

    initDragAndDrop() {
        Sortable.create(this.playlist, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: (evt) => {
                this.reorderPlaylist(evt.oldIndex, evt.newIndex);
            },
        });
    }

    playTrack(trackId) {
        this.plyr.source = {
            type: 'audio',
            sources: [{ src: `/audio/${trackId}.mp3`, type: 'audio/mp3' }],
        };
        this.plyr.play();
    }

    reorderPlaylist(oldIndex, newIndex) {
        const tracks = Array.from(this.playlist.children);
        const movedTrack = tracks[oldIndex];
        this.playlist.insertBefore(movedTrack, tracks[newIndex]);
        const newOrder = tracks.map(track => track.dataset.trackId);
        localStorage.setItem('playlistOrder', JSON.stringify(newOrder));
    }
}

document.querySelectorAll('.brmedia-audio-playlist-pro').forEach(element => {
    new BRMediaAudioPlaylistPro(element);
});