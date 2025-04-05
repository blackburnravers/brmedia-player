// assets/js/frontend.worker.js
self.addEventListener('message', function(e) {
    const { type, data } = e.data;
    if (type === 'generateWaveform') {
        const waveform = generateWaveform(data.audioData);
        self.postMessage({ type: 'waveform', data: waveform });
    } else if (type === 'processAnalytics') {
        const processedData = processAnalyticsData(data.analytics);
        self.postMessage({ type: 'analytics', data: processedData });
    }
});

function generateWaveform(audioData) {
    const waveform = [];
    for (let i = 0; i < audioData.length; i += 100) {
        waveform.push(Math.max(...audioData.slice(i, i + 100)));
    }
    return waveform;
}

function processAnalyticsData(analytics) {
    return analytics.map(item => ({
        ...item,
        normalizedPlays: item.plays / 100,
    }));
}