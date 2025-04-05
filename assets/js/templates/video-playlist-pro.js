// assets/js/templates/video-playlist-pro.js
class BRMediaVideoPlaylistPro {
    constructor(playlistElement) {
        this.playlist = playlistElement.querySelector('.playlist-items');
        this.plyr = new Plyr('.plyr', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume'],
        });
        this.initPlaylist();
        this.initDragAndDrop();
    }

    initPlaylist() {
        const savedOrder = JSON.parse(localStorage.getItem('videoPlaylistOrder')) || [];
        const videos = Array.from(this.playlist.querySelectorAll('.video'));
        if (savedOrder.length) {
            savedOrder.forEach(id => {
                const video = videos.find(v => v.dataset.videoId === id);
                if (video) this.playlist.appendChild(video);
            });
        }

        videos.forEach(video => {
            const playBtn = video.querySelector('.play-btn');
            playBtn.addEventListener('click', () => this.playVideo(video.dataset.videoId));
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

    playVideo(videoId) {
        this.plyr.source = {
            type: 'video',
            sources: [{ src: `/videos/${videoId}.mp4`, type: 'video/mp4' }],
        };
        this.plyr.play();
    }

    reorderPlaylist(oldIndex, newIndex) {
        const videos = Array.from(this.playlist.children);
        const movedVideo = videos[oldIndex];
        this.playlist.insertBefore(movedVideo, videos[newIndex]);
        const newOrder = videos.map(video => video.dataset.videoId);
        localStorage.setItem('videoPlaylistOrder', JSON.stringify(newOrder));
    }
}

document.querySelectorAll('.brmedia-video-playlist-pro').forEach(element => {
    new BRMediaVideoPlaylistPro(element);
});