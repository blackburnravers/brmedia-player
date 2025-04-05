<?php
/**
 * BRMedia REST Analytics Controller
 *
 * Handles analytics-related REST API endpoints.
 *
 * @package BRMedia\API\Endpoints
 */

namespace BRMedia\API\Endpoints;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class BRMedia_REST_Analytics extends WP_REST_Controller {

    /**
     * Constructor
     */
    public function __construct() {
        $this->namespace = 'brmedia/v1';
        $this->rest_base = 'analytics';
    }

    /**
     * Register the routes
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/summary', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_summary' ],
            'permission_callback' => [ $this, 'get_items_permissions_check' ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/media/(?P<id>\d+)', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_media_analytics' ],
            'permission_callback' => [ $this, 'get_items_permissions_check' ],
            'args'                => [
                'id' => [
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    },
                ],
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/realtime', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_realtime' ],
            'permission_callback' => [ $this, 'get_items_permissions_check' ],
        ] );
    }

    /**
     * Check permissions for reading analytics
     *
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function get_items_permissions_check( $request ) {
        if ( ! current_user_can( 'edit_others_posts' ) ) {
            return new WP_Error( 'rest_forbidden', __( 'You do not have permission to view analytics.', 'brmedia-player' ), [ 'status' => 403 ] );
        }
        return true;
    }

    /**
     * Get analytics summary
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_summary( $request ) {
        // Simulate fetching aggregated analytics data
        $summary = [
            'total_plays'     => 10500,
            'total_downloads' => 2300,
            'unique_locations'=> 45,
            'device_types'    => [
                'mobile'  => 70,
                'desktop' => 25,
                'tablet'  => 5,
            ],
        ];

        // Allow modification via filter
        $summary = apply_filters( 'brmedia_analytics_summary', $summary );

        return rest_ensure_response( $summary );
    }

    /**
     * Get analytics for a specific media item
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_media_analytics( $request ) {
        $id = $request['id'];
        $post = get_post( $id );

        if ( ! $post || ! in_array( $post->post_type, [ 'brmusic', 'brvideo' ] ) ) {
            return new WP_Error( 'rest_invalid_media', __( 'Invalid media ID.', 'brmedia-player' ), [ 'status' => 404 ] );
        }

        // Simulate fetching media-specific analytics
        $analytics = [
            'plays'      => get_post_meta( $id, '_brmedia_plays', true ) ?: 0,
            'downloads'  => get_post_meta( $id, '_brmedia_downloads', true ) ?: 0,
            'locations'  => get_post_meta( $id, '_brmedia_locations', true ) ?: [],
            'devices'    => get_post_meta( $id, '_brmedia_devices', true ) ?: [],
        ];

        // Allow modification via filter
        $analytics = apply_filters( 'brmedia_media_analytics', $analytics, $id );

        return rest_ensure_response( $analytics );
    }

    /**
     * Get real-time analytics (simulated)
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_realtime( $request ) {
        // Simulate real-time data (e.g., last 5 minutes)
        $realtime = [
            'current_plays'    => 15,
            'current_downloads'=> 3,
            'active_users'     => 42,
        ];

        // Allow modification via filter
        $realtime = apply_filters( 'brmedia_realtime_analytics', $realtime );

        return rest_ensure_response( $realtime );
    }
}