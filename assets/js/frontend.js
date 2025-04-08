// BRMedia Player Frontend JavaScript
(function($) {
    $(document).ready(function() {
        // Check for required dependencies
        if (typeof WaveSurfer === 'undefined') {
            console.error('WaveSurfer.js is not loaded.');
            return;
        }
        if (typeof videojs === 'undefined') {
            console.error('Video.js is not loaded.');
            return;
        }
        if (typeof brmedia_params === 'undefined') {
            console.error('brmedia_params is not defined.');
            return;
        }

        // Initialize WaveSurfer.js for audio players
        $('.wavesurfer-container').each(function() {
            var wavesurfer = WaveSurfer.create({
                container: this,
                waveColor: '#0073aa',
                progressColor: '#005a87',
                cursorColor: '#333',
                height: 100,
                responsive: true
            });
            var audioUrl = $(this).data('audio-url');
            wavesurfer.load(audioUrl);

            // Store the wavesurfer instance for later access
            $(this).data('wavesurfer', wavesurfer);

            // Play/Pause button
            $(this).next('.controls').find('.play-pause').on('click', function() {
                wavesurfer.playPause();
                $(this).find('i').toggleClass('fa-play fa-pause');
            });

            // Track play event
            wavesurfer.on('play', function() {
                $.post(brmedia_params.ajax_url, {
                    action: 'brmedia_track_play',
                    post_id: $(wavesurfer.container).data('post-id'),
                    nonce: brmedia_params.nonce
                });
            });

            // Update current time and duration
            var currentTimeElement = $(this).next('.controls').find('.current-time');
            var durationElement = $(this).next('.controls').find('.duration');
            if (currentTimeElement.length && durationElement.length) {
                wavesurfer.on('audioprocess', function() {
                    currentTimeElement.text(formatTime(wavesurfer.getCurrentTime()));
                });
                wavesurfer.on('ready', function() {
                    durationElement.text(formatTime(wavesurfer.getDuration()));
                });
            }
        });

        // Initialize Video.js for all video players
        $('video.video-js').each(function() {
            videojs(this, {
                controls: true,
                autoplay: false,
                preload: 'auto'
            });
        });

        // Handle video popup functionality
        $('.brmedia-video-popup-button').on('click', function() {
            var videoId = $(this).data('video-id');
            var popup = $('#brmedia-video-popup-' + videoId);
            if (popup.length) {
                popup.show();
                var videoElement = popup.find('video')[0];
                if (videoElement) {
                    videojs(videoElement).play();
                }
            }
        });

        // Close video popup
        $('.brmedia-popup .close-popup').on('click', function() {
            var popup = $(this).closest('.brmedia-popup');
            if (popup.length) {
                popup.hide();
                var videoElement = popup.find('video')[0];
                if (videoElement) {
                    videojs(videoElement).pause();
                }
            }
        });

        // Handle download button clicks
        $('.brmedia-download-button').on('click', function(e) {
            e.preventDefault();
            var downloadUrl = $(this).attr('href');
            window.location.href = downloadUrl;
        });

        // Handle comment timestamps for audio players
        $('.wavesurfer-container').on('click', function(e) {
            var wavesurfer = $(this).data('wavesurfer');
            if (wavesurfer) {
                var time = wavesurfer.getCurrentTime();
                var comment = prompt('Enter your comment:');
                if (comment) {
                    $.post(brmedia_params.ajax_url, {
                        action: 'brmedia_add_comment',
                        post_id: $(this).data('post-id'),
                        timestamp: time,
                        comment: comment,
                        nonce: brmedia_params.nonce
                    }, function(response) {
                        if (response.success) {
                            alert('Comment added successfully!');
                        } else {
                            alert('Failed to add comment: ' + (response.data || 'Unknown error'));
                        }
                    }).fail(function() {
                        alert('Network error. Please try again later.');
                    });
                }
            }
        });

        // Initialize footer player
        var footerPlayerContainer = document.querySelector('.brmedia-footer-player .wavesurfer-container');
        if (footerPlayerContainer) {
            var footerWavesurfer = WaveSurfer.create({
                container: footerPlayerContainer,
                waveColor: '#0073aa',
                progressColor: '#005a87',
                cursorColor: '#333',
                height: 50,
                responsive: true
            });
            var audioUrl = footerPlayerContainer.getAttribute('data-audio-url');
            footerWavesurfer.load(audioUrl);

            // Play/Pause button for footer player
            var footerPlayPauseButton = document.querySelector('.brmedia-footer-player .play-pause');
            if (footerPlayPauseButton) {
                footerPlayPauseButton.addEventListener('click', function() {
                    footerWavesurfer.playPause();
                    var icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-play');
                        icon.classList.toggle('fa-pause');
                    }
                });
            }

            // Update current time and duration for footer player
            var footerCurrentTimeElement = document.querySelector('.brmedia-footer-player .current-time');
            var footerDurationElement = document.querySelector('.brmedia-footer-player .duration');
            if (footerCurrentTimeElement && footerDurationElement) {
                footerWavesurfer.on('audioprocess', function() {
                    footerCurrentTimeElement.textContent = formatTime(footerWavesurfer.getCurrentTime());
                });
                footerWavesurfer.on('ready', function() {
                    footerDurationElement.textContent = formatTime(footerWavesurfer.getDuration());
                });
            }
        }

        // Hover Effects for All Buttons
        const buttons = document.querySelectorAll('.brmedia-download-button');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                if (!button.classList.contains('icon-only')) {
                    button.style.transform = 'scale(1.05)';
                }
            });
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'scale(1)';
            });
        });

        // Progress Bar Download Simulation
        const progressButtons = document.querySelectorAll('.brmedia-download-progress .progress-button');
        progressButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const progressBar = this.nextElementSibling.querySelector('.progress');
                if (progressBar) {
                    let width = 0;
                    const interval = setInterval(() => {
                        if (width >= 100) {
                            clearInterval(interval);
                            window.location.href = this.href;
                        } else {
                            width += 10;
                            progressBar.style.width = width + '%';
                        }
                    }, 200);
                }
            });
        });

        // Optional: Add smooth scroll for massive buttons
        const massiveButtons = document.querySelectorAll('.brmedia-download-button.massive');
        massiveButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                setTimeout(() => {
                    window.location.href = button.href;
                }, 500);
            });
        });

        // Gaming Template: Fetch Stream Data
        document.querySelectorAll('.brmedia-gaming-default').forEach(container => {
            const channel = container.dataset.channel;
            const clientId = brmedia_params.twitchClientId; // Must be passed via wp_localize_script
            const oauthToken = brmedia_params.twitchOAuthToken; // Must be passed via wp_localize_script

            function fetchStreamData() {
                fetch(`https://api.twitch.tv/helix/streams?user_login=${channel}`, {
                    headers: {
                        'Client-ID': clientId,
                        'Authorization': `Bearer ${oauthToken}`
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('API request failed');
                    return response.json();
                })
                .then(data => {
                    const stream = data.data[0];
                    const titleElement = container.querySelector('#stream-title');
                    const statusElement = container.querySelector('#stream-status span');
                    const viewersElement = container.querySelector('#viewer-count span');

                    if (stream) {
                        titleElement.textContent = stream.title;
                        statusElement.textContent = 'Live';
                        viewersElement.textContent = stream.viewer_count;
                    } else {
                        titleElement.textContent = 'Stream is offline';
                        statusElement.textContent = 'Offline';
                        viewersElement.textContent = '0';
                    }
                })
                .catch(error => {
                    console.error('Error fetching stream data:', error);
                    container.querySelector('#stream-title').textContent = 'Failed to load stream data';
                });
            }

            fetchStreamData();
            setInterval(fetchStreamData, 300000); // Update every 5 minutes
        });

        // Radio Template: Fetch Track Info
        document.querySelectorAll('.brmedia-radio-default').forEach(container => {
            const trackApiUrl = brmedia_params.trackApiUrl; // Must be passed via wp_localize_script

            function fetchTrackInfo() {
                if (!trackApiUrl) return;

                fetch(trackApiUrl)
                    .then(response => {
                        if (!response.ok) throw new Error('API request failed');
                        return response.json();
                    })
                    .then(data => {
                        container.querySelector('#current-track').textContent = data.current_track || 'Unknown';
                        container.querySelector('#next-show').textContent = data.next_show || 'Unknown';
                    })
                    .catch(error => {
                        console.error('Error fetching track info:', error);
                        container.querySelector('#current-track').textContent = 'Failed to load';
                    });
            }

            fetchTrackInfo();
            setInterval(fetchTrackInfo, 30000); // Update every 30 seconds
        });

        // Gaming Popup Functionality
        document.querySelectorAll('.brmedia-gaming-popup-button').forEach(button => {
            button.addEventListener('click', function() {
                const popup = document.getElementById('brmedia-gaming-popup');
                if (popup) {
                    popup.style.display = 'flex';
                    popup.setAttribute('aria-hidden', 'false');
                }
            });
        });

        // Radio Popup Functionality
        document.querySelectorAll('.brmedia-radio-popup-button').forEach(button => {
            button.addEventListener('click', function() {
                const popup = document.getElementById('brmedia-radio-popup');
                if (popup) {
                    popup.style.display = 'flex';
                    popup.setAttribute('aria-hidden', 'false');
                    const player = popup.querySelector('#radio-player-popup');
                    if (player) {
                        player.play().catch(error => console.error('Auto-play failed:', error));
                    }
                }
            });
        });

        // Close Popup Functionality
        document.querySelectorAll('.close-popup').forEach(button => {
            button.addEventListener('click', function() {
                const popup = this.closest('.brmedia-popup');
                if (popup) {
                    popup.style.display = 'none';
                    popup.setAttribute('aria-hidden', 'true');
                    const player = popup.querySelector('audio');
                    if (player) {
                        player.pause();
                    }
                }
            });
        });

        // Close Popup with Escape Key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.brmedia-popup').forEach(popup => {
                    if (popup.style.display === 'flex') {
                        popup.style.display = 'none';
                        popup.setAttribute('aria-hidden', 'true');
                        const player = popup.querySelector('audio');
                        if (player) {
                            player.pause();
                        }
                    }
                });
            }
        });

        // Helper function to format time
        function formatTime(time) {
            if (isNaN(time) || time < 0) return '00:00';
            var minutes = Math.floor(time / 60);
            var seconds = Math.floor(time % 60);
            return minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
        }
    });
})(jQuery);