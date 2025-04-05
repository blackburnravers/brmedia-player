<?php
/**
 * BRMedia REST Settings Controller
 *
 * Handles settings-related REST API endpoints.
 *
 * @package BRMedia\API\Endpoints
 */

namespace BRMedia\API\Endpoints;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class BRMedia_REST_Settings extends WP_REST_Controller {

    /**
     * Constructor
     */
    public function __construct() {
        $this->namespace = 'brmedia/v1';
        $this->rest_base = 'settings';
    }

    /**
     * Register the routes
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_settings' ],
            'permission_callback' => [ $this, 'get_settings_permissions_check' ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => [ $this, 'update_settings' ],
            'permission_callback' => [ $this, 'update_settings_permissions_check' ],
            'args'                => [
                'settings' => [
                    'type'        => 'object',
                    'required'    => true,
                    'properties'  => [
                        // Define expected settings properties here
                        'default_audio_template' => [ 'type' => 'string' ],
                        'default_video_template' => [ 'type' => 'string' ],
                        'enable_analytics'       => [ 'type' => 'boolean' ],
                    ],
                ],
            ],
        ] );
    }

    /**
     * Check permissions for reading settings
     */
    public function get_settings_permissions_check( $request ) {
        return current_user_can( 'manage_options' );
    }

    /**
     * Check permissions for updating settings
     */
    public function update_settings_permissions_check( $request ) {
        return current_user_can( 'manage_options' );
    }

    /**
     * Get plugin settings
     */
    public function get_settings( $request ) {
        $settings = get_option( 'brmedia_settings', [] );
        return rest_ensure_response( $settings );
    }

    /**
     * Update plugin settings
     */
    public function update_settings( $request ) {
        $settings = $request['settings'];
        $current_settings = get_option( 'brmedia_settings', [] );

        // Merge and sanitize settings
        $updated_settings = wp_parse_args( $settings, $current_settings );
        $updated_settings = apply_filters( 'brmedia_sanitize_settings', $updated_settings );

        update_option( 'brmedia_settings', $updated_settings );

        return rest_ensure_response( $updated_settings );
    }
}