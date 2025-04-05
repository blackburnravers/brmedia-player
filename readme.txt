=== BRMedia Player ===
Contributors: rhyscole
Donate link: https://www.blackburnravers.co.uk/donate
Tags: media, audio, video, player, streaming, dj, podcast, analytics, api, ai
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An advanced, modular WordPress plugin for managing and showcasing music and video content with AI, streaming, and API integrations.

== Description ==

BRMedia Player is a cutting-edge WordPress plugin designed for DJs, content creators, podcasters, and media publishers. Inspired by platforms like SoundCloud, Mixcloud, YouTube, Twitch, Spotify, and Hearthis, it offers a robust suite of features:

- **Custom Audio/Video Players**: Powered by Plyr.js with HLS/DASH streaming, Chromecast/AirPlay support, and advanced templates (reactive, spatial, immersive).
- **API Integrations**: Seamlessly import and play content from SoundCloud, Spotify, YouTube, Mixcloud, Twitch, and Hearthis.
- **AI Enhancements**: AI-driven analytics, beat detection, and dynamic visuals (TensorFlow.js).
- **Waveforms & Visualizers**: Real-time waveforms (Wavesurfer.js) and 3D visuals (Three.js).
- **Admin Control Panel**: Comprehensive dashboard with live previews, template management, and predictive analytics.
- **Performance**: Lazy loading, Web Workers, and caching (Redis/Memcached) for enterprise-scale sites.

== Installation ==

1. Upload the `brmedia-player` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure settings under the "BRMedia" admin menu.
4. Use shortcodes like `[brmedia_audio]` or `[brmedia_video]` to embed players.

== Frequently Asked Questions ==

= Does it support third-party APIs? =
Yes, it integrates with SoundCloud, Spotify, YouTube, Mixcloud, Twitch, and Hearthis via their respective APIs.

= Is it multisite compatible? =
Absolutely, with full multisite support for settings and cleanup.

= What are the system requirements? =
WordPress 5.0+, PHP 7.4+, and a modern browser for advanced features.

== Screenshots ==

1. Admin Dashboard with AI-driven insights.
2. Reactive Audio Player with waveform visuals.
3. Immersive Fullscreen Player (SoundCloud-style).
4. Interactive Video Player with hotspots.

== Changelog ==

= 1.0.0 =
* Initial release with advanced media players, API integrations, and AI features.

== Upgrade Notice ==

= 1.0.0 =
First version - ensure PHP 7.4+ for full functionality.

== Developer Notes ==

- **Hooks**: Extend via `brmedia_register_services` and `brmedia_uninstall_cleanup`.
- **REST API**: Access endpoints at `/wp-json/brmedia/v1/`.
- **Dependencies**: Managed via Composer (see `composer.json`).

Developed by Rhys Cole at [Blackburn Ravers](https://www.blackburnravers.co.uk).