<?php
/**
 * BRMedia Player Chat Module
 *
 * Implements real-time chat functionality with a shortcode and AJAX handling.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Enqueue chat scripts and styles
 */
function brmedia_enqueue_chat_scripts() {
    wp_enqueue_script('brmedia-chat', plugin_dir_url(__FILE__) . 'chat.js', array('jquery'), '1.0.0', true);
    wp_enqueue_style('brmedia-chat-css', plugin_dir_url(__FILE__) . 'chat.css');
    wp_localize_script('brmedia-chat', 'brmediaChatAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('brmedia_chat_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'brmedia_enqueue_chat_scripts');

/**
 * Define the [brmedia_chat] shortcode
 *
 * @return string HTML output for the chat interface
 */
function brmedia_chat_shortcode() {
    ob_start();
    ?>
    <div id="brmedia-chat">
        <div id="chat-messages"></div>
        <form id="chat-form">
            <input type="text" id="chat-input" placeholder="Type a message..." required />
            <button type="submit" id="chat-send">Send</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('brmedia_chat', 'brmedia_chat_shortcode');

/**
 * Handle chat message submission via AJAX
 */
function brmedia_handle_chat_message() {
    check_ajax_referer('brmedia_chat_nonce', 'nonce');

    $message = sanitize_text_field($_POST['message']);
    $user_id = get_current_user_id();
    $username = $user_id ? get_userdata($user_id)->user_login : 'Guest';

    $chat_message = array(
        'username' => $username,
        'message'  => $message,
        'time'     => current_time('mysql'),
    );

    // Store message in transients for simplicity (could use a custom table for persistence)
    $messages = get_transient('brmedia_chat_messages') ?: array();
    $messages[] = $chat_message;
    set_transient('brmedia_chat_messages', $messages, HOUR_IN_SECONDS);

    wp_send_json_success($chat_message);
}
add_action('wp_ajax_brmedia_chat_message', 'brmedia_handle_chat_message');
add_action('wp_ajax_nopriv_brmedia_chat_message', 'brmedia_handle_chat_message');

/**
 * Fetch chat messages via AJAX
 */
function brmedia_fetch_chat_messages() {
    check_ajax_referer('brmedia_chat_nonce', 'nonce');
    $messages = get_transient('brmedia_chat_messages') ?: array();
    wp_send_json_success($messages);
}
add_action('wp_ajax_brmedia_fetch_chat', 'brmedia_fetch_chat_messages');
add_action('wp_ajax_nopriv_brmedia_fetch_chat', 'brmedia_fetch_chat_messages');