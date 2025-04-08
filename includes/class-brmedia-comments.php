<?php
/**
 * BRMedia Player Comment Timestamps Class
 *
 * This class manages comment timestamps for media playback.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BRMedia_Comments {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_brmedia_add_comment', array($this, 'add_comment'));
        add_action('wp_ajax_nopriv_brmedia_add_comment', array($this, 'add_comment'));
    }

    /**
     * Add a comment with a timestamp
     */
    public function add_comment() {
        check_ajax_referer('brmedia_nonce', 'nonce');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $timestamp = isset($_POST['timestamp']) ? floatval($_POST['timestamp']) : 0;
        $comment_content = isset($_POST['comment']) ? sanitize_text_field($_POST['comment']) : '';

        if (!$post_id || !$comment_content) {
            wp_send_json_error('Invalid data');
        }

        $comment_data = array(
            'comment_post_ID' => $post_id,
            'comment_content' => $comment_content,
            'comment_author' => is_user_logged_in() ? wp_get_current_user()->display_name : 'Anonymous',
            'comment_author_email' => is_user_logged_in() ? wp_get_current_user()->user_email : '',
            'comment_date' => current_time('mysql'),
            'comment_approved' => 1,
        );

        $comment_id = wp_insert_comment($comment_data);
        if ($comment_id) {
            update_comment_meta($comment_id, 'brmedia_timestamp', $timestamp);
            wp_send_json_success('Comment added');
        } else {
            wp_send_json_error('Failed to add comment');
        }
    }

    /**
     * Get comments with timestamps for a post
     *
     * @param int $post_id Post ID
     * @return array Array of comments with timestamps
     */
    public static function get_comments_with_timestamps($post_id) {
        $comments = get_comments(array('post_id' => $post_id));
        $result = array();

        foreach ($comments as $comment) {
            $timestamp = get_comment_meta($comment->comment_ID, 'brmedia_timestamp', true);
            $result[] = array(
                'content' => $comment->comment_content,
                'author' => $comment->comment_author,
                'timestamp' => floatval($timestamp),
            );
        }

        return $result;
    }
}