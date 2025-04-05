// assets/js/templates/video-playlist.js
class BRMediaVideoPlaylist {
    constructor(playlistElement) {
        this.playlist = playlistElement.querySelector('.playlist-items');
        this.currentVideoIndex = 0;
        this.videos = Array.from(this.playlist.querySelectorAll('.video'));
        this.plyr = new Plyr('.plyr', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
        });
        this.initPlaylist();
    }

    initPlaylist() {
        this.videos.forEach((video, index) => {
            video.addEventListener('click', () => this.playVideo(index));
        });

        this.plyr.on('ended', () => this.playNextVideo());
        document.querySelector('.shuffle-btn')?.addEventListener('click', () => this.shufflePlaylist());
    }

    playVideo(index) {
        this.currentVideoIndex = index;
        const video = this.videos[index];
        this.plyr.source = {
            type: 'video',
            sources: [{ src: video.dataset.src, type: 'video/mp4' }],
        };
        this.plyr.play();
    }

    playNextVideo() {
        if (this.currentVideoIndex < this.videos.length - 1) {
            this.playVideo(this.currentVideoIndex + 1);
        }
    }

    shufflePlaylist() {
        for (let i = this.videos.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [this.videos[i], this.videos[j]] = [this.videos[j], this.videos[i]];
            this.playlist.appendChild(this.videos[i]);
            this.playlist.appendChild(this.videos[j]);
        }
        this.currentVideoIndex = 0;
        this.playVideo(0);
    }
}

document.querySelectorAll('.brmedia-video-playlist').forEach(element => {
    new BRMediaVideoPlaylist(element);
});