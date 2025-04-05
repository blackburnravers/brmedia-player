// assets/js/templates/audio-playlist.js
class BRMediaAudioPlaylist {
    constructor(playlistElement) {
        this.playlist = playlistElement.querySelector('.playlist-items');
        this.currentTrackIndex = 0;
        this.tracks = Array.from(this.playlist.querySelectorAll('.track'));
        this.plyr = new Plyr('.plyr', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
        });
        this.initPlaylist();
    }

    initPlaylist() {
        this.tracks.forEach((track, index) => {
            track.addEventListener('click', () => this.playTrack(index));
        });

        this.plyr.on('ended', () => this.playNextTrack());
        document.querySelector('.shuffle-btn')?.addEventListener('click', () => this.shufflePlaylist());
    }

    playTrack(index) {
        this.currentTrackIndex = index;
        const track = this.tracks[index];
        this.plyr.source = {
            type: 'audio',
            sources: [{ src: track.dataset.src, type: 'audio/mp3' }],
        };
        this.plyr.play();
    }

    playNextTrack() {
        if (this.currentTrackIndex < this.tracks.length - 1) {
            this.playTrack(this.currentTrackIndex + 1);
        }
    }

    shufflePlaylist() {
        for (let i = this.tracks.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [this.tracks[i], this.tracks[j]] = [this.tracks[j], this.tracks[i]];
            this.playlist.appendChild(this.tracks[i]);
            this.playlist.appendChild(this.tracks[j]);
        }
        this.currentTrackIndex = 0;
        this.playTrack(0);
    }
}

document.querySelectorAll('.brmedia-audio-playlist').forEach(element => {
    new BRMediaAudioPlaylist(element);
});