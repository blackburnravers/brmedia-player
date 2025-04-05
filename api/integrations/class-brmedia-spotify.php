<?php
/**
 * BRMedia Spotify Integration
 *
 * Handles Spotify API interactions.
 *
 * @package BRMedia\API\Integrations
 */

namespace BRMedia\API\Integrations;

use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\Session;
use WP_Error;

class BRMedia_Spotify {

    private $api;

    public function __construct() {
        $client_id = get_option( 'brmedia_spotify_client_id', '' );
        $client_secret = get_option( 'brmedia_spotify_client_secret', '' );

        if ( empty( $client_id ) || empty( $client_secret ) ) {
            return;
        }

        $session = new Session( $client_id, $client_secret );
        $session->requestCredentialsToken();
        $access_token = $session->getAccessToken();

        $this->api = new SpotifyWebAPI();
        $this->api->setAccessToken( $access_token );
    }

    public function get_user_playlists( $user_id ) {
        try {
            $playlists = $this->api->getUserPlaylists( $user_id );
            return apply_filters( 'brmedia_spotify_playlists', $playlists->items );
        } catch ( \Exception $e ) {
            return new WP_Error( 'spotify_api_error', $e->getMessage() );
        }
    }

    public function get_track( $track_id ) {
        try {
            $track = $this->api->getTrack( $track_id );
            return apply_filters( 'brmedia_spotify_track', $track );
        } catch ( \Exception $e ) {
            return new WP_Error( 'spotify_api_error', $e->getMessage() );
        }
    }

    public function get_embed_code( $track_id ) {
        return "<iframe src='https://open.spotify.com/embed/track/{$track_id}' width='300' height='380' frameborder='0' allowtransparency='true' allow='encrypted-media'></iframe>";
    }
}