<?php
/**
 * BRMedia Hearthis.at Integration
 *
 * Handles Hearthis.at API interactions.
 *
 * @package BRMedia\API\Integrations
 */

namespace BRMedia\API\Integrations;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use WP_Error;

class BRMedia_Hearthis {

    private $client;

    public function __construct() {
        $this->client = new Client( [
            'base_uri' => 'https://api-v2.hearthis.at/',
        ] );
    }

    public function get_user_tracks( $username ) {
        try {
            $response = $this->client->get( "feed/{$username}/", [
                'query' => [
                    'type' => 'tracks',
                ],
            ] );
            $tracks = json_decode( $response->getBody(), true );
            return apply_filters( 'brmedia_hearthis_tracks', $tracks );
        } catch ( GuzzleException $e ) {
            return new WP_Error( 'hearthis_api_error', $e->getMessage() );
        }
    }

    public function get_track( $track_id ) {
        try {
            $response = $this->client->get( "tracks/{$track_id}/" );
            $track = json_decode( $response->getBody(), true );
            return apply_filters( 'brmedia_hearthis_track', $track );
        } catch ( GuzzleException $e ) {
            return new WP_Error( 'hearthis_api_error', $e->getMessage() );
        }
    }

    public function get_embed_code( $track_id ) {
        return "<iframe width='100%' height='120' scrolling='no' frameborder='no' src='https://hearthis.at/embed/{$track_id}/transparent_black/?hcolor=&color=&style=2&block_size=2&block_space=1&background=1&waveform=0&cover=0&autoplay=0&css='></iframe>";
    }
}