document.addEventListener("DOMContentLoaded", function() {
    var players = document.querySelectorAll('.brmedia-music-player');

    players.forEach(function(player) {
        var postId = player.getAttribute('id').replace('waveform-', '');
        var wavesurfer = WaveSurfer.create({
            container: "#waveform-" + postId,
            waveColor: "violet",
            progressColor: "purple"
        });

        var audioSource = player.getAttribute('data-audio-source');
        wavesurfer.load(audioSource);

        document.getElementById("play-pause-" + postId).addEventListener("click", function() {
            if (wavesurfer.isPlaying()) {
                wavesurfer.pause();
                this.innerHTML = '<i class="fas fa-play"></i>';
            } else {
                wavesurfer.play();
                this.innerHTML = '<i class="fas fa-pause"></i>';
            }
        });
    });
});