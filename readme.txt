=== BRMedia Player ===
Contributors: rhyscole
Tags: media, player, audio, video, radio, gaming, podcast, WaveSurfer, Video.js, Twitch, multimedia
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful, modular WordPress plugin for managing and displaying multimedia content, including audio, video, radio streams, gaming streams, and podcasts.

== Description ==
BRMedia Player is an advanced, feature-rich WordPress plugin crafted for content creators, DJs, podcasters, gamers, and media enthusiasts. Inspired by platforms like SoundCloud, YouTube, and Twitch, it provides a seamless way to integrate and showcase multimedia content on your WordPress site. Whether you're streaming live radio, embedding Twitch gaming feeds, or offering downloadable podcasts, BRMedia Player combines performance, customization, and user engagement into one robust package.

### Core Features
- **Advanced Audio Player**: Powered by WaveSurfer.js, featuring waveform visualization, playback controls, and comment timestamps.
- **Dynamic Video Player**: Built with Video.js, supporting adaptive streaming, fullscreen mode, and custom overlays.
- **Live Radio Streaming**: Stream radio with real-time track metadata and schedule integration via custom APIs.
- **Gaming Streams**: Embed Twitch streams with live status indicators, viewer counts, and optional chat overlays.
- **Podcast Management**: Organize episodes with RSS feed generation and playback analytics.
- **Secure Downloads**: Protect files with expiring download tokens and user authentication.
- **Engagement Tools**: Timestamped comments, social sharing buttons, and listener analytics.
- **Modular Design**: Enable/disable modules (Audio, Video, Radio, Gaming, Podcasts) to tailor functionality.

### Technical Highlights
- **Performance Optimized**: Leverages CDN-hosted libraries (WaveSurfer.js, Video.js, Font Awesome) and client-side processing for speed.
- **Extensible**: Hooks and filters for developers to extend functionality.
- **Responsive**: Mobile-friendly players with touch controls and notification integration.
- **Security**: Nonces for AJAX requests, sanitized inputs, and secure file handling.

### Use Cases
- **DJs & Musicians**: Share mixes with waveform visuals and downloadable tracks.
- **Podcasters**: Host episodes with RSS feeds for iTunes/Spotify syndication.
- **Gamers**: Showcase Twitch streams with live interaction.
- **Radio Hosts**: Stream live broadcasts with track info and schedules.

== Installation ==
1. **Upload the Plugin**:
   - Download the `brmedia-player.zip` file.
   - Navigate to Plugins > Add New in your WordPress dashboard.
   - Click "Upload Plugin" and select the ZIP file.
   - Alternatively, extract and upload the `brmedia-player` folder to `/wp-content/plugins/` via FTP.
2. **Activate**:
   - Go to Plugins > Installed Plugins.
   - Locate "BRMedia Player" and click "Activate."
3. **Configure**:
   - Visit the "BRMedia" menu in the admin dashboard.
   - Set up API keys (e.g., Twitch Client ID/OAuth) and customize player settings.
4. **Embed Content**:
   - Use shortcodes like `[brmedia_audio id="123"]` or `[brmedia_video id="456"]` in posts, pages, or widgets.

== Advanced Usage ==
### Shortcodes
- `[brmedia_audio id="123" template="waveform" autoplay="true"]`: Embeds an audio player with a specific template and autoplay enabled.
- `[brmedia_video id="456" controls="true" poster="URL"]`: Displays a video with custom poster image.
- `[brmedia_twitch channel="username" chat="true"]`: Embeds a Twitch stream with live chat.

### Custom Post Types
- **brmusic**: Audio tracks with metadata (artist, genre, duration).
- **brvideo**: Video content with thumbnail and source options.
- **brradio**: Radio streams with API integration.
- **brgaming**: Gaming streams linked to Twitch.
- **brpodcasts**: Podcast episodes with RSS support.

### Developer Hooks
- **Filter: `brmedia_player_templates`**: Modify available player templates.
- **Action: `brmedia_after_player_load`**: Add custom scripts post-player initialization.
- **Filter: `brmedia_download_token_expiry`**: Adjust token expiration time.

== Frequently Asked Questions ==
### How do I configure Twitch integration?
1. Register an app at [Twitch Developer Portal](https://dev.twitch.tv/).
2. Obtain your Client ID and OAuth token.
3. Enter these in the BRMedia settings under "Gaming Module."
4. Create a `brgaming` post and input the Twitch channel name.

### Can I style the players?
Yes! Use the "Templates Panel" in the admin settings to adjust colors, fonts, and layouts. For advanced customization, override CSS via your theme or the `brmedia-frontend-css` handle.

### What file formats are supported?
- **Audio**: MP3, WAV, AAC (via WaveSurfer.js).
- **Video**: MP4, WebM, HLS (via Video.js).
- Check library docs for additional codec support.

### How do secure downloads work?
Downloads use temporary tokens generated via AJAX, valid for a configurable time (default: 24 hours). Only authenticated users with permission can access files.

### Is it compatible with multisite?
Yes, BRMedia Player supports WordPress Multisite. Configure settings per site or network-wide as needed.

== Screenshots ==
1. **Dashboard Overview**: Stats for plays, downloads, and streams.
2. **Audio Waveform**: Interactive player with comments.
3. **Video Player**: Cinematic fullscreen experience.
4. **Radio Interface**: Live stream with track display.
5. **Twitch Embed**: Stream with chat integration.

== Changelog ==
### 1.0.0
- Initial release with full audio, video, radio, gaming, and podcast support.

== Upgrade Notice ==
### 1.0.0
First version—no upgrades yet. Stay tuned for future enhancements!

== Troubleshooting ==
- **Player Not Loading**: Ensure API keys are set and assets (JS/CSS) aren’t blocked by ad blockers.
- **404 Errors**: Verify permalinks are flushed (Settings > Permalinks > Save).
- **Slow Performance**: Check hosting resources; consider a CDN for media files.

== Author ==
**Rhys Cole**
- **Website**: [www.blackburnravers.co.uk](https://www.blackburnravers.co.uk)
- **Email**: rhysc2101@gmail.com

== Support ==
- Email: rhysc2101@gmail.com
- Web: [www.blackburnravers.co.uk/contact](https://www.blackburnravers.co.uk/contact)