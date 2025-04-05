<?php
/**
 * BRMedia REST Media Controller
 *
 * Handles media-related REST API endpoints for brmusic and brvideo.
 *
 * @package BRMedia\API\Endpoints
 */

namespace BRMedia\API\Endpoints;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class BRMedia_REST_Media extends WP_REST_Controller {

    /**
     * Constructor
     */
    public function __construct() {
        $this->namespace = 'brmedia/v1';
        $this->rest_base = 'media';
    }

    /**
     * Register the routes
     */
    public function register_routes() {
        // GET /media - List all media
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_items' ],
            'permission_callback' => [ $this, 'get_items_permissions_check' ],
        ] );

        // POST /media - Create new media
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [ $this, 'create_item' ],
            'permission_callback' => [ $this, 'create_item_permissions_check' ],
            'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
        ] );

        // GET /media/{id} - Get specific media
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_item' ],
            'permission_callback' => [ $this, 'get_item_permissions_check' ],
            'args'                => [
                'id' => [
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    },
                ],
            ],
        ] );

        // PUT /media/{id} - Update media
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', [
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => [ $this, 'update_item' ],
            'permission_callback' => [ $this, 'update_item_permissions_check' ],
            'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
        ] );

        // DELETE /media/{id} - Delete media
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', [
            'methods'             => \WP_REST_Server::DELETABLE,
            'callback'            => [ $this, 'delete_item' ],
            'permission_callback' => [ $this, 'delete_item_permissions_check' ],
            'args'                => [
                'id' => [
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    },
                ],
            ],
        ] );
    }

    /**
     * Check permissions for reading media items
     */
    public function get_items_permissions_check( $request ) {
        return current_user_can( 'read' );
    }

    /**
     * Check permissions for creating media items
     */
    public function create_item_permissions_check( $request ) {
        return current_user_can( 'publish_posts' );
    }

    /**
     * Check permissions for reading a single media item
     */
    public function get_item_permissions_check( $request ) {
        return current_user_can( 'read' );
    }

    /**
     * Check permissions for updating media items
     */
    public function update_item_permissions_check( $request ) {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Check permissions for deleting media items
     */
    public function delete_item_permissions_check( $request ) {
        return current_user_can( 'delete_posts' );
    }

    /**
     * Get a collection of media items
     */
    public function get_items( $request ) {
        $args = [
            'post_type'      => [ 'brmusic', 'brvideo' ],
            'posts_per_page' => 10,
            'paged'          => $request['page'] ?? 1,
        ];

        $query = new \WP_Query( $args );
        $items = [];

        foreach ( $query->posts as $post ) {
            $items[] = $this->prepare_item_for_response( $post, $request );
        }

        $response = rest_ensure_response( $items );
        $response->header( 'X-WP-Total', $query->found_posts );
        $response->header( 'X-WP-TotalPages', $query->max_num_pages );

        return $response;
    }

    /**
     * Create a new media item
     */
    public function create_item( $request ) {
        $post_type = $request['type'] ?? 'brmusic';
        if ( ! in_array( $post_type, [ 'brmusic', 'brvideo' ] ) ) {
            return new WP_Error( 'rest_invalid_type', __( 'Invalid media type.', 'brmedia-player' ), [ 'status' => 400 ] );
        }

        $post_id = wp_insert_post( [
            'post_title'  => $request['title'],
            'post_type'   => $post_type,
            'post_status' => 'publish',
        ], true );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // Add metadata (e.g., artist, BPM, etc.)
        if ( isset( $request['meta'] ) ) {
            foreach ( $request['meta'] as $key => $value ) {
                update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
            }
        }

        $post = get_post( $post_id );
        return $this->prepare_item_for_response( $post, $request );
    }

    /**
     * Get a single media item
     */
    public function get_item( $request ) {
        $post = get_post( $request['id'] );
        if ( ! $post || ! in_array( $post->post_type, [ 'brmusic', 'brvideo' ] ) ) {
            return new WP_Error( 'rest_invalid_media', __( 'Invalid media ID.', 'brmedia-player' ), [ 'status' => 404 ] );
        }
        return $this->prepare_item_for_response( $post, $request );
    }

    /**
     * Update a media item
     */
    public function update_item( $request ) {
        $post_id = $request['id'];
        $post = get_post( $post_id );
        if ( ! $post || ! in_array( $post->post_type, [ 'brmusic', 'brvideo' ] ) ) {
            return new WP_Error( 'rest_invalid_media', __( 'Invalid media ID.', 'brmedia-player' ), [ 'status' => 404 ] );
        }

        wp_update_post( [
            'ID'         => $post_id,
            'post_title' => $request['title'] ?? $post->post_title,
        ] );

        // Update metadata
        if ( isset( $request['meta'] ) ) {
            foreach ( $request['meta'] as $key => $value ) {
                update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
            }
        }

        $post = get_post( $post_id );
        return $this->prepare_item_for_response( $post, $request );
    }

    /**
     * Delete a media item
     */
    public function delete_item( $request ) {
        $post_id = $request['id'];
        $post = get_post( $post_id );
        if ( ! $post || ! in_array( $post->post_type, [ 'brmusic', 'brvideo' ] ) ) {
            return new WP_Error( 'rest_invalid_media', __( 'Invalid media ID.', 'brmedia-player' ), [ 'status' => 404 ] );
        }

        wp_delete_post( $post_id, true );
        return rest_ensure_response( [ 'deleted' => true ] );
    }

    /**
     * Prepare a media item for response
     */
    public function prepare_item_for_response( $post, $request ) {
        $data = [
            'id'    => $post->ID,
            'title' => $post->post_title,
            'type'  => $post->post_type,
            'meta'  => get_post_meta( $post->ID ),
        ];
        return apply_filters( 'brmedia_media_item_response', $data, $post );
    }
}