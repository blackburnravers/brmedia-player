<?php
/**
 * BRMedia YouTube Integration
 *
 * Handles YouTube API interactions.
 *
 * @package BRMedia\API\Integrations
 */

namespace BRMedia\API\Integrations;

use Google_Client;
use Google_Service_YouTube;
use WP_Error;

class BRMedia_YouTube {

    private $youtube;

    public function __construct() {
        $api_key = get_option( 'brmedia_youtube_api_key', '' );
        if ( empty( $api_key ) ) {
            return;
        }

        $client = new Google_Client();
        $client->setDeveloperKey( $api_key );
        $this->youtube = new Google_Service_YouTube( $client );
    }

    public function get_video( $video_id ) {
        try {
            $response = $this->youtube->videos->listVideos( 'snippet,contentDetails,statistics', [
                'id' => $video_id,
            ] );
            if ( empty( $response->items ) ) {
                return new WP_Error( 'youtube_api_error', 'Video not found.' );
            }
            $video = $response->items[0];
            return apply_filters( 'brmedia_youtube_video', $video );
        } catch ( \Exception $e ) {
            return new WP_Error( 'youtube_api_error', $e->getMessage() );
        }
    }

    public function get_channel_videos( $channel_id, $max_results = 10 ) {
        try {
            $response = $this->youtube->search->listSearch( 'snippet', [
                'channelId' => $channel_id,
                'maxResults' => $max_results,
                'order' => 'date',
            ] );
            return apply_filters( 'brmedia_youtube_channel_videos', $response->items );
        } catch ( \Exception $e ) {
            return new WP_Error( 'youtube_api_error', $e->getMessage() );
        }
    }

    public function get_embed_code( $video_id ) {
        return "<iframe width='560' height='315' src='https://www.youtube.com/embed/{$video_id}' frameborder='0' allowfullscreen></iframe>";
    }
}