<?php
/**
 * Utility: File Upload Handler
 * Description: Manages file uploads for BRMedia Player, including validation and integration with the WordPress media library.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles file uploads with validation and error handling.
 *
 * @param array $file The uploaded file data from $_FILES.
 * @param array $allowed_types Optional. Array of allowed MIME types. Default: audio and video types.
 * @param int $max_size Optional. Maximum file size in bytes. Default: 10MB.
 * @return array|WP_Error The attachment ID on success, or WP_Error on failure.
 */
function brmedia_handle_file_upload( $file, $allowed_types = array( 'audio/mpeg', 'audio/wav', 'video/mp4' ), $max_size = 10485760 ) {
    // Check if file is uploaded
    if ( empty( $file['name'] ) ) {
        return new WP_Error( 'no_file', __( 'No file uploaded.', 'brmedia-player' ) );
    }

    // Validate file type
    $file_type = wp_check_filetype( $file['name'] );
    if ( ! in_array( $file_type['type'], $allowed_types ) ) {
        return new WP_Error( 'invalid_type', __( 'Invalid file type. Allowed types: ' . implode( ', ', $allowed_types ), 'brmedia-player' ) );
    }

    // Validate file size
    if ( $file['size'] > $max_size ) {
        return new WP_Error( 'file_too_large', __( 'File size exceeds the maximum limit of 10MB.', 'brmedia-player' ) );
    }

    // Handle the upload using WordPress's media handler
    $upload = wp_handle_upload( $file, array( 'test_form' => false ) );
    if ( isset( $upload['error'] ) ) {
        return new WP_Error( 'upload_error', $upload['error'] );
    }

    // Insert the file into the media library
    $attachment_id = wp_insert_attachment( array(
        'guid'           => $upload['url'],
        'post_mime_type' => $upload['type'],
        'post_title'     => sanitize_file_name( $file['name'] ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ), $upload['file'] );

    if ( is_wp_error( $attachment_id ) ) {
        return $attachment_id;
    }

    // Generate metadata for the attachment
    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );

    return $attachment_id;
}