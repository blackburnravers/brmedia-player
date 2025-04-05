<?php
/**
 * BRMedia SoundCloud Integration
 *
 * Handles SoundCloud API interactions.
 *
 * @package BRMedia\API\Integrations
 */

namespace BRMedia\API\Integrations;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BRMedia_SoundCloud {

    private $client_id;
    private $client;

    public function __construct() {
        $this->client_id = get_option( 'brmedia_soundcloud_client_id', '' );
        $this->client = new Client( [
            'base_uri' => 'https://api.soundcloud.com/',
        ] );
    }

    public function get_user_tracks( $username ) {
        try {
            $response = $this->client->get( "users/{$username}/tracks", [
                'query' => [
                    'client_id' => $this->client_id,
                ],
            ] );
            $tracks = json_decode( $response->getBody(), true );
            return apply_filters( 'brmedia_soundcloud_tracks', $tracks );
        } catch ( GuzzleException $e ) {
            return new \WP_Error( 'soundcloud_api_error', $e->getMessage() );
        }
    }

    public function get_track( $track_id ) {
        try {
            $response = $this->client->get( "tracks/{$track_id}", [
                'query' => [
                    'client_id' => $this->client_id,
                ],
            ] );
            $track = json_decode( $response->getBody(), true );
            return apply_filters( 'brmedia_soundcloud_track', $track );
        } catch ( GuzzleException $e ) {
            return new \WP_Error( 'soundcloud_api_error', $e->getMessage() );
        }
    }

    public function get_embed_code( $track_id ) {
        $embed_url = "https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{$track_id}&client_id={$this->client_id}";
        return "<iframe width='100%' height='166' scrolling='no' frameborder='no' src='{$embed_url}'></iframe>";
    }
}