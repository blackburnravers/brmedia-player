<?php
/**
 * BRMedia Twitch Integration
 *
 * Handles Twitch API interactions.
 *
 * @package BRMedia\API\Integrations
 */

namespace BRMedia\API\Integrations;

use TwitchApi\TwitchApi;
use WP_Error;

class BRMedia_Twitch {

    private $twitch_api;

    public function __construct() {
        $client_id = get_option( 'brmedia_twitch_client_id', '' );
        $client_secret = get_option( 'brmedia_twitch_client_secret', '' );

        if ( empty( $client_id ) || empty( $client_secret ) ) {
            return;
        }

        $this->twitch_api = new TwitchApi( [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
        ] );
    }

    public function get_live_streams( $channel_name ) {
        try {
            $streams = $this->twitch_api->getLiveStreams( [ $channel_name ] );
            return apply_filters( 'brmedia_twitch_streams', $streams );
        } catch ( \Exception $e ) {
            return new WP_Error( 'twitch_api_error', $e->getMessage() );
        }
    }

    public function get_video( $video_id ) {
        try {
            $video = $this->twitch_api->getVideo( $video_id );
            return apply_filters( 'brmedia_twitch_video', $video );
        } catch ( \Exception $e ) {
            return new WP_Error( 'twitch_api_error', $e->getMessage() );
        }
    }

    public function get_embed_code( $channel_name, $type = 'live' ) {
        if ( $type === 'live' ) {
            return "<iframe src='https://player.twitch.tv/?channel={$channel_name}&parent=" . parse_url( home_url(), PHP_URL_HOST ) . "' frameborder='0' allowfullscreen='true' scrolling='no' height='378' width='620'></iframe>";
        } elseif ( $type === 'video' ) {
            return "<iframe src='https://player.twitch.tv/?video={$channel_name}&parent=" . parse_url( home_url(), PHP_URL_HOST ) . "' frameborder='0' allowfullscreen='true' scrolling='no' height='378' width='620'></iframe>";
        }
        return '';
    }
}