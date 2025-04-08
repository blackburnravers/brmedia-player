// BRMedia Player Media Metadata JavaScript

// Function to extract ID3 tags using jsmediatags
function extractID3Tags(file, callback) {
    jsmediatags.read(file, {
        onSuccess: function(tag) {
            callback(tag.tags);
        },
        onError: function(error) {
            console.error('Error reading ID3 tags:', error);
            callback(null);
        }
    });
}

// Function to detect BPM using bpm-detective
function detectBPM(audioContext, audioBuffer, callback) {
    bpmDetective(audioBuffer, audioContext.sampleRate, function(err, bpm) {
        if (err) {
            console.error('BPM detection error:', err);
            callback(null);
        } else {
            callback(bpm);
        }
    });
}

// Function to detect musical key using KeyFinder.js
function detectKey(audioContext, audioBuffer, callback) {
    // Placeholder for KeyFinder.js integration
    // This would require a library or custom implementation
    callback('Unknown');
}

// Example usage in the admin area when uploading a file
document.getElementById('audio-file-input').addEventListener('change', function(event) {
    var file = event.target.files[0];
    var audioContext = new (window.AudioContext || window.webkitAudioContext)();

    // Extract ID3 tags
    extractID3Tags(file, function(tags) {
        if (tags) {
            document.getElementById('artist').value = tags.artist || '';
            document.getElementById('title').value = tags.title || '';
            document.getElementById('album').value = tags.album || '';
        }
    });

    // Detect BPM and key
    var reader = new FileReader();
    reader.onload = function(e) {
        audioContext.decodeAudioData(e.target.result, function(buffer) {
            detectBPM(audioContext, buffer, function(bpm) {
                document.getElementById('bpm').value = bpm || 'Unknown';
            });
            detectKey(audioContext, buffer, function(key) {
                document.getElementById('key').value = key;
            });
        });
    };
    reader.readAsArrayBuffer(file);
});