<?php
/**
 * BRMedia Mixcloud Integration
 *
 * Handles Mixcloud API interactions.
 *
 * @package BRMedia\API\Integrations
 */

namespace BRMedia\API\Integrations;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use WP_Error;

class BRMedia_Mixcloud {

    private $client_id;
    private $client_secret;
    private $access_token;
    private $client;

    public function __construct() {
        $this->client_id = get_option( 'brmedia_mixcloud_client_id', '' );
        $this->client_secret = get_option( 'brmedia_mixcloud_client_secret', '' );
        $this->access_token = get_option( 'brmedia_mixcloud_access_token', '' );

        $this->client = new Client( [
            'base_uri' => 'https://api.mixcloud.com/',
        ] );
    }

    /**
     * Refresh access token if needed
     */
    private function refresh_token() {
        // Placeholder for token refresh logic
        $this->access_token = 'new_access_token';
        update_option( 'brmedia_mixcloud_access_token', $this->access_token );
    }

    public function get_user_shows( $username ) {
        try {
            $response = $this->client->get( "{$username}/cloudcasts/", [
                'query' => [
                    'access_token' => $this->access_token,
                ],
            ] );
            $shows = json_decode( $response->getBody(), true );
            return apply_filters( 'brmedia_mixcloud_shows', $shows['data'] );
        } catch ( GuzzleException $e ) {
            return new WP_Error( 'mixcloud_api_error', $e->getMessage() );
        }
    }

    public function get_show( $show_key ) {
        try {
            $response = $this->client->get( $show_key, [
                'query' => [
                    'access_token' => $this->access_token,
                ],
            ] );
            $show = json_decode( $response->getBody(), true );
            return apply_filters( 'brmedia_mixcloud_show', $show );
        } catch ( GuzzleException $e ) {
            return new WP_Error( 'mixcloud_api_error', $e->getMessage() );
        }
    }

    public function get_embed_code( $show_key ) {
        $embed_url = "https://www.mixcloud.com/widget/iframe/?feed={$show_key}";
        return "<iframe width='100%' height='120' src='{$embed_url}' frameborder='0'></iframe>";
    }
}