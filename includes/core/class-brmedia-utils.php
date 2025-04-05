<?php
/**
 * BRMedia Utils Class
 *
 * Provides utility functions for the plugin.
 *
 * @package BRMedia\Includes\Core
 */

namespace BRMedia\Includes\Core;

class BRMedia_Utils {
    /**
     * Sanitizes an array of data
     *
     * @param array $data Input data
     * @return array Sanitized data
     */
    public static function sanitize_array($data) {
        return array_map('sanitize_text_field', $data);
    }

    /**
     * Logs debug information to error log
     *
     * @param mixed $data Data to log
     */
    public static function debug_log($data) {
        if (WP_DEBUG) {
            error_log(print_r($data, true));
        }
    }

    /**
     * Generates a unique ID
     *
     * @return string Unique ID
     */
    public static function generate_unique_id() {
        return wp_generate_uuid4();
    }

    /**
     * Checks if a user has a specific capability
     *
     * @param string $capability Capability to check
     * @param int $user_id User ID (optional)
     * @return bool Whether user has capability
     */
    public static function has_capability($capability, $user_id = null) {
        return $user_id ? user_can($user_id, $capability) : current_user_can($capability);
    }
}